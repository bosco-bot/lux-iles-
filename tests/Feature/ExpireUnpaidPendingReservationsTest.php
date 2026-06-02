<?php

namespace Tests\Feature;

use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ExpireUnpaidPendingReservationsTest extends TestCase
{
    protected int $villaId;

    protected int $userId;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('booking.unpaid_deposit_grace_hours', 24);

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();

        $this->userId = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Dupont',
            'email' => 'jean@test.fr',
            'password' => bcrypt('secret'),
        ])->id;

        $this->villaId = Villa::create([
            'name' => 'Villa Test',
            'slug' => 'villa-test',
            'is_active' => true,
            'base_price_per_night' => 500,
            'max_capacity' => 6,
            'minimum_stay_nights' => 3,
        ])->id;
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function it_cancels_expired_online_pending_reservations_and_their_payments(): void
    {
        $reservation = $this->createPendingDirectReservation(
            paymentExpiresAt: Carbon::now()->subHour(),
        );

        $deposit = Payment::create([
            'reservation_id' => $reservation->id,
            'payment_number' => 'PAY-DEP-001',
            'type' => 'deposit',
            'amount' => 500,
            'currency' => 'EUR',
            'status' => 'pending',
            'payment_method' => 'stripe',
        ]);

        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldReceive('sendCancellationEmail')->once();
        $this->app->instance(EmailService::class, $emailService);

        $this->artisan('reservations:expire-unpaid-pending')
            ->assertSuccessful();

        $reservation->refresh();
        $deposit->refresh();

        $this->assertSame('cancelled', $reservation->status);
        $this->assertNotNull($reservation->cancelled_at);
        $this->assertStringContainsString('24 h', (string) $reservation->cancellation_reason);
        $this->assertSame('cancelled', $deposit->status);
    }

    #[Test]
    public function it_does_not_cancel_pending_reservations_still_within_grace_period(): void
    {
        $reservation = $this->createPendingDirectReservation(
            paymentExpiresAt: Carbon::now()->addHours(2),
        );

        Payment::create([
            'reservation_id' => $reservation->id,
            'payment_number' => 'PAY-DEP-002',
            'type' => 'deposit',
            'amount' => 500,
            'currency' => 'EUR',
            'status' => 'pending',
            'payment_method' => 'stripe',
        ]);

        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldNotReceive('sendCancellationEmail');
        $this->app->instance(EmailService::class, $emailService);

        $this->artisan('reservations:expire-unpaid-pending')
            ->assertSuccessful();

        $this->assertSame('pending', $reservation->fresh()->status);
    }

    #[Test]
    public function it_does_not_cancel_manual_pending_reservations(): void
    {
        Reservation::create([
            'reservation_number' => 'LX-MANUAL-2026',
            'villa_id' => $this->villaId,
            'user_id' => $this->userId,
            'guest_first_name' => 'Admin',
            'guest_last_name' => 'Test',
            'guest_email' => 'admin@test.fr',
            'check_in_date' => '2026-11-01',
            'check_out_date' => '2026-11-08',
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'base_price' => 1000,
            'total_price' => 1000,
            'status' => 'pending',
            'source' => 'manual',
            'payment_expires_at' => Carbon::now()->subDay(),
        ]);

        $emailService = Mockery::mock(EmailService::class);
        $emailService->shouldNotReceive('sendCancellationEmail');
        $this->app->instance(EmailService::class, $emailService);

        $this->artisan('reservations:expire-unpaid-pending')
            ->assertSuccessful();

        $this->assertSame(
            'pending',
            Reservation::where('reservation_number', 'LX-MANUAL-2026')->value('status')
        );
    }

    protected function createPendingDirectReservation(Carbon $paymentExpiresAt): Reservation
    {
        return Reservation::create([
            'reservation_number' => 'LX-ONLINE-2026',
            'villa_id' => $this->villaId,
            'user_id' => $this->userId,
            'guest_first_name' => 'Jean',
            'guest_last_name' => 'Dupont',
            'guest_email' => 'jean@test.fr',
            'check_in_date' => '2026-12-01',
            'check_out_date' => '2026-12-08',
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'base_price' => 1000,
            'total_price' => 1000,
            'status' => 'pending',
            'source' => 'direct',
            'payment_expires_at' => $paymentExpiresAt,
        ]);
    }

    protected function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->decimal('base_price_per_night', 10, 2)->default(0);
            $table->unsignedInteger('max_capacity')->default(4);
            $table->unsignedInteger('minimum_stay_nights')->default(3);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->unsignedBigInteger('villa_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_email');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('number_of_nights');
            $table->unsignedInteger('number_of_guests');
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('status');
            $table->timestamp('payment_expires_at')->nullable();
            $table->string('source')->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->unsignedBigInteger('cancelled_by')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id');
            $table->string('payment_number')->unique();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('EUR');
            $table->string('status');
            $table->string('payment_method')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }
}
