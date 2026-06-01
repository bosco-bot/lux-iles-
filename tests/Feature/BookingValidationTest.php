<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\User;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookingValidationTest extends TestCase
{
    protected Villa $villa;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('session.driver', 'array');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->withoutMiddleware(ValidateCsrfToken::class);

        $this->createTestSchema();
        $this->seedTestData();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('villa_seasonal_prices');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[Test]
    public function confirm_rejects_stay_shorter_than_minimum_nights(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(2)->toDateString();

        $response = $this->actingAs($this->user)->postJson(route('bookings.confirm'), [
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
            'adults' => 2,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'message' => 'La durée minimale pour cette villa est de 4 nuits. Pour toute demande spécifique, contactez directement l\'équipe LUXÎLES.',
            ]);
    }

    #[Test]
    public function confirm_rejects_guest_count_above_max_capacity(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(5)->toDateString();

        $response = $this->actingAs($this->user)->postJson(route('bookings.confirm'), [
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 10,
            'adults' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'message' => 'La capacité maximale de cette villa est de 8 personnes.',
            ]);
    }

    #[Test]
    public function calculate_price_rejects_stay_shorter_than_minimum_nights(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(2)->toDateString();

        $response = $this->postJson(route('bookings.calculate-price'), [
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'message' => 'La durée minimale pour cette villa est de 4 nuits. Pour toute demande spécifique, contactez directement l\'équipe LUXÎLES.',
            ]);
    }

    #[Test]
    public function calculate_price_rejects_guest_count_above_max_capacity(): void
    {
        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(5)->toDateString();

        $response = $this->postJson(route('bookings.calculate-price'), [
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 10,
        ]);

        $response->assertStatus(422)
            ->assertJsonPath('success', false)
            ->assertJsonFragment([
                'message' => 'La capacité maximale de cette villa est de 8 personnes.',
            ]);
    }

    private function createTestSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('islands', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->string('country')->default('France');
            $table->timestamps();
        });

        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('island_id')->constrained('islands')->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('bedrooms')->default(1);
            $table->unsignedTinyInteger('bathrooms')->default(1);
            $table->unsignedTinyInteger('max_capacity')->default(2);
            $table->decimal('base_price_per_night', 10, 2);
            $table->unsignedTinyInteger('minimum_stay_nights')->default(3);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->decimal('multiplier', 5, 2)->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('villa_seasonal_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->decimal('price_per_night', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->timestamps();
        });
    }

    private function seedTestData(): void
    {
        $island = Island::create([
            'name' => 'Saint-Martin',
            'code' => 'SM',
            'country' => 'France',
        ]);

        $this->villa = Villa::create([
            'name' => 'Villa Test',
            'slug' => 'villa-test',
            'island_id' => $island->id,
            'bedrooms' => 4,
            'bathrooms' => 3,
            'max_capacity' => 8,
            'base_price_per_night' => 500,
            'minimum_stay_nights' => 4,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'first_name' => 'Jean',
            'last_name' => 'Voyageur',
            'email' => 'voyageur@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => false,
            'is_active' => true,
        ]);
    }
}
