<?php

namespace Tests\Feature;

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

class SecurityAccessTest extends TestCase
{
    protected User $client;

    protected User $otherClient;

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
        $this->seedUsers();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('villa_equipments');
        Schema::dropIfExists('equipments');
        Schema::dropIfExists('villa_reviews');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function guest_is_redirected_from_admin_area_to_login(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function client_cannot_access_admin_dashboard(): void
    {
        $this->actingAs($this->client)
            ->get(route('admin.dashboard'))
            ->assertRedirect(route('espace-client.index'));
    }

    #[Test]
    public function admin_can_access_protected_admin_route(): void
    {
        $this->actingAs($this->admin)
            ->get(route('admin.equipments.index'))
            ->assertOk();
    }

    #[Test]
    public function guest_cannot_access_espace_client(): void
    {
        $this->get(route('espace-client.reservations'))
            ->assertRedirect(route('login'));
    }

    #[Test]
    public function client_cannot_access_another_clients_reservation_payment(): void
    {
        $island = Island::create(['name' => 'SM', 'code' => 'SM']);
        $villa = Villa::create(['name' => 'Villa', 'slug' => 'villa', 'island_id' => $island->id, 'is_active' => true]);

        $reservation = Reservation::create([
            'reservation_number' => 'LX-OTHER-001',
            'villa_id' => $villa->id,
            'user_id' => $this->otherClient->id,
            'check_in_date' => now()->addDays(10),
            'check_out_date' => now()->addDays(15),
            'number_of_nights' => 5,
            'number_of_guests' => 2,
            'total_price' => 1000,
            'status' => 'confirmed',
        ]);

        $this->actingAs($this->client)
            ->get(route('espace-client.pay-deposit', $reservation))
            ->assertRedirect(route('espace-client.index'));
    }

    #[Test]
    public function removed_utility_routes_are_not_registered(): void
    {
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.clear-cache'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.link-storage'));
        $this->assertFalse(\Illuminate\Support\Facades\Route::has('admin.update-seasons-db'));
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
            $table->decimal('base_price_per_night', 10, 2)->default(100);
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
            $table->string('status')->default('pending');
            $table->timestamps();
        });

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('villa_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });

        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_search_filter')->default(false);
            $table->timestamps();
        });

        Schema::create('villa_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->unique(['villa_id', 'equipment_id']);
        });
    }

    private function seedUsers(): void
    {
        $this->client = User::create([
            'first_name' => 'Client',
            'last_name' => 'A',
            'email' => 'client-a@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $this->otherClient = User::create([
            'first_name' => 'Client',
            'last_name' => 'B',
            'email' => 'client-b@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => false,
        ]);

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Lux',
            'email' => 'admin@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }
}
