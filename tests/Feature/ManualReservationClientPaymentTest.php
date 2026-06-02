<?php

namespace Tests\Feature;

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
use Tests\TestCase;

class ManualReservationClientPaymentTest extends TestCase
{
    protected User $client;

    protected Reservation $manualReservation;

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
    public function manual_reservation_model_disallows_client_online_payment(): void
    {
        $this->assertTrue($this->manualReservation->isManualOffline());
        $this->assertFalse($this->manualReservation->allowsClientOnlinePayment());
    }

    #[Test]
    public function client_cannot_open_stripe_deposit_page_for_manual_reservation(): void
    {
        $this->actingAs($this->client)
            ->get(route('espace-client.pay-deposit', $this->manualReservation))
            ->assertRedirect(route('espace-client.reservations'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function client_cannot_open_stripe_balance_page_for_manual_reservation(): void
    {
        $this->actingAs($this->client)
            ->get(route('espace-client.pay-balance', $this->manualReservation))
            ->assertRedirect(route('espace-client.reservations'))
            ->assertSessionHas('error');
    }

    #[Test]
    public function client_cannot_open_stripe_guarantee_page_for_manual_reservation(): void
    {
        $this->actingAs($this->client)
            ->get(route('espace-client.pay-deposit-guarantee', $this->manualReservation))
            ->assertRedirect(route('espace-client.reservations'))
            ->assertSessionHas('error');
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
            $table->string('source')->default('direct');
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
            $table->timestamps();
        });
    }

    private function seedData(): void
    {
        $this->client = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Test',
            'email' => 'marie-manual-pay@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $island = Island::create(['name' => 'Saint-Martin', 'code' => 'SM']);
        $villa = Villa::create([
            'name' => 'Villa Test',
            'slug' => 'villa-test',
            'island_id' => $island->id,
            'is_active' => true,
        ]);

        $this->manualReservation = Reservation::create([
            'reservation_number' => 'LX-MANUAL-001',
            'villa_id' => $villa->id,
            'user_id' => $this->client->id,
            'check_in_date' => now()->addDays(30),
            'check_out_date' => now()->addDays(37),
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'total_price' => 5000,
            'status' => 'confirmed',
            'source' => 'manual',
        ]);

        Payment::create([
            'reservation_id' => $this->manualReservation->id,
            'payment_number' => 'PAY-MAN-001',
            'type' => 'deposit',
            'amount' => 1500,
            'status' => 'pending',
            'payment_method' => 'bank_transfer',
        ]);
    }
}
