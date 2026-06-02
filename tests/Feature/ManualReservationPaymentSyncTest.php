<?php

namespace Tests\Feature;

use App\Http\Controllers\Admin\ReservationController;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use App\Models\Island;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use ReflectionMethod;
use Tests\TestCase;

class ManualReservationPaymentSyncTest extends TestCase
{
    protected Reservation $reservation;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('session.driver', 'array');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->createSchema();
        $this->seedData();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function deposit_paid_status_marks_only_deposit_completed(): void
    {
        $this->invokeSync($this->reservation, 'deposit_paid');

        $this->assertSame('completed', $this->payment('deposit')->status);
        $this->assertNotNull($this->payment('deposit')->paid_at);
        $this->assertSame('pending', $this->payment('balance')->status);
        $this->assertSame('pending', $this->payment('deposit_guarantee')->status);
    }

    #[Test]
    public function fully_paid_status_marks_all_payments_completed(): void
    {
        $this->invokeSync($this->reservation, 'fully_paid');

        $this->assertSame('completed', $this->payment('deposit')->status);
        $this->assertSame('completed', $this->payment('balance')->status);
        $this->assertSame('completed', $this->payment('deposit_guarantee')->status);
    }

    #[Test]
    public function confirmed_status_resets_all_payments_to_pending(): void
    {
        $this->payment('deposit')->update(['status' => 'completed', 'paid_at' => now()]);
        $this->payment('balance')->update(['status' => 'completed', 'paid_at' => now()]);

        $this->invokeSync($this->reservation, 'confirmed');

        $this->assertSame('pending', $this->payment('deposit')->status);
        $this->assertNull($this->payment('deposit')->paid_at);
        $this->assertSame('pending', $this->payment('balance')->status);
        $this->assertNull($this->payment('balance')->paid_at);
    }

    #[Test]
    public function cancel_route_syncs_unpaid_payments_for_manual_reservation(): void
    {
        $this->payment('deposit')->update(['status' => 'completed', 'paid_at' => now()]);
        $this->payment('balance')->update(['status' => 'pending']);
        $this->reservation->update(['status' => 'fully_paid']);

        $this->actingAs($this->admin)
            ->post(route('admin.reservations.cancel', $this->reservation->id), [
                'cancellation_reason' => 'Test annulation',
            ])
            ->assertRedirect(route('admin.reservations'));

        $this->reservation->refresh();
        $this->assertSame('cancelled', $this->reservation->status);
        $this->assertSame('completed', $this->payment('deposit')->fresh()->status);
        $this->assertSame('cancelled', $this->payment('balance')->fresh()->status);
        $this->assertSame('cancelled', $this->payment('deposit_guarantee')->fresh()->status);
    }

    #[Test]
    public function cancelled_status_cancels_only_unpaid_payments(): void
    {
        $this->payment('deposit')->update(['status' => 'completed', 'paid_at' => now()]);
        $this->payment('balance')->update(['status' => 'pending']);
        $this->payment('deposit_guarantee')->update(['status' => 'processing']);

        $this->invokeSync($this->reservation, 'cancelled');

        $this->assertSame('completed', $this->payment('deposit')->status);
        $this->assertSame('cancelled', $this->payment('balance')->status);
        $this->assertSame('cancelled', $this->payment('deposit_guarantee')->status);
    }

    private function invokeSync(Reservation $reservation, string $status): void
    {
        $controller = app(ReservationController::class);
        $method = new ReflectionMethod(ReservationController::class, 'syncManualReservationPaymentsFromStatus');
        $method->setAccessible(true);
        $method->invoke($controller, $reservation->fresh(), $status);
    }

    private function payment(string $type): Payment
    {
        return Payment::where('reservation_id', $this->reservation->id)->where('type', $type)->firstOrFail();
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')->constrained('islands');
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('villa_id')->constrained('villas');
            $table->foreignId('user_id')->constrained('users');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedTinyInteger('number_of_nights')->default(1);
            $table->unsignedTinyInteger('number_of_guests')->default(2);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->string('status')->default('confirmed');
            $table->string('source')->default('manual');
            $table->text('cancellation_reason')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->foreignId('cancelled_by')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations');
            $table->string('payment_number')->unique();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->text('metadata')->nullable();
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Test',
            'email' => 'admin-sync@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $island = Island::create(['name' => 'Saint-Martin', 'code' => 'SM']);
        $villa = Villa::create([
            'name' => 'Villa Sync',
            'slug' => 'villa-sync',
            'island_id' => $island->id,
            'is_active' => true,
        ]);

        $this->reservation = Reservation::create([
            'reservation_number' => 'LX-SYNC-001',
            'villa_id' => $villa->id,
            'user_id' => $this->admin->id,
            'check_in_date' => now()->addDays(30),
            'check_out_date' => now()->addDays(35),
            'number_of_nights' => 5,
            'number_of_guests' => 2,
            'total_price' => 4000,
            'status' => 'confirmed',
            'source' => 'manual',
        ]);

        foreach (['deposit' => 1200, 'balance' => 2200, 'deposit_guarantee' => 600] as $type => $amount) {
            Payment::create([
                'reservation_id' => $this->reservation->id,
                'payment_number' => 'PAY-' . strtoupper($type) . '-001',
                'type' => $type,
                'amount' => $amount,
                'status' => 'pending',
                'payment_method' => 'bank_transfer',
            ]);
        }
    }
}
