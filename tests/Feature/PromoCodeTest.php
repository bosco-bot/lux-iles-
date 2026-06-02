<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\PromoCode;
use App\Models\User;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PromoCodeTest extends TestCase
{
    protected User $user;

    protected Villa $villa;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        Config::set('session.driver', 'array');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->withoutMiddleware(ValidateCsrfToken::class);

        Queue::fake();
        Mail::fake();

        $this->createTestSchema();
        $this->seedTestData();
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('villa_availability_blocks');
        Schema::dropIfExists('promo_code_usages');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('payments');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villa_seasonal_prices');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[Test]
    public function check_promo_returns_invalid_for_unknown_code(): void
    {
        $response = $this->actingAs($this->user)->postJson(route('bookings.check-promo'), [
            'promo_code' => 'INVALID',
            'villa_id' => $this->villa->id,
            'check_in' => Carbon::tomorrow()->toDateString(),
            'check_out' => Carbon::tomorrow()->addDays(5)->toDateString(),
            'guests' => 2,
        ]);

        $response->assertOk()
            ->assertJsonPath('valid', false);
    }

    #[Test]
    public function check_promo_calculates_percent_discount_correctly(): void
    {
        PromoCode::create([
            'code' => 'SUMMER10',
            'type' => 'percent',
            'value' => 10,
            'is_active' => true,
            'uses_count' => 0,
        ]);

        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(5)->toDateString();

        $response = $this->actingAs($this->user)->postJson(route('bookings.check-promo'), [
            'promo_code' => 'summer10',
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
        ]);

        $response->assertOk()
            ->assertJsonPath('valid', true)
            ->assertJsonPath('promo_code', 'SUMMER10');

        $this->assertGreaterThan(0, (float) $response->json('discount_amount'));
        $this->assertGreaterThan(0, (float) $response->json('new_total'));
    }

    #[Test]
    public function confirm_stores_discount_amount_when_promo_applied(): void
    {
        PromoCode::create([
            'code' => 'FIXED100',
            'type' => 'fixed',
            'value' => 100,
            'is_active' => true,
            'uses_count' => 0,
        ]);

        $checkIn = Carbon::tomorrow()->toDateString();
        $checkOut = Carbon::tomorrow()->addDays(5)->toDateString();

        $response = $this->actingAs($this->user)->postJson(route('bookings.confirm'), [
            'villa_id' => $this->villa->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'guests' => 2,
            'adults' => 2,
            'children' => 0,
            'infants' => 0,
            'promo_code' => 'FIXED100',
        ]);

        $response->assertOk()->assertJsonPath('success', true);

        $this->assertEquals(100.0, (float) \App\Models\Reservation::where('user_id', $this->user->id)->value('discount_amount'));

        $this->assertDatabaseHas('promo_codes', [
            'code' => 'FIXED100',
            'uses_count' => 1,
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
            $table->unsignedTinyInteger('max_capacity')->default(8);
            $table->decimal('base_price_per_night', 10, 2);
            $table->decimal('cleaning_fee', 10, 2)->default(50);
            $table->decimal('service_fee_percentage', 5, 2)->nullable();
            $table->unsignedTinyInteger('minimum_stay_nights')->default(4);
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

        Schema::create('villa_availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });

        Schema::create('promo_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->enum('type', ['percent', 'fixed']);
            $table->decimal('value', 8, 2);
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->unsignedInteger('max_uses')->nullable();
            $table->unsignedInteger('uses_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('villa_id')->constrained('villas');
            $table->foreignId('user_id')->constrained('users');
            $table->string('guest_first_name');
            $table->string('guest_last_name');
            $table->string('guest_email');
            $table->string('guest_phone')->nullable();
            $table->string('guest_address')->nullable();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedInteger('number_of_nights');
            $table->unsignedInteger('number_of_guests');
            $table->unsignedInteger('adults')->default(1);
            $table->unsignedInteger('children')->default(0);
            $table->unsignedInteger('infants')->default(0);
            $table->decimal('base_price', 10, 2)->default(0);
            $table->decimal('cleaning_fee', 10, 2)->default(0);
            $table->decimal('service_fee', 10, 2)->default(0);
            $table->decimal('vat_amount', 10, 2)->default(0);
            $table->decimal('tourist_tax', 10, 2)->default(0);
            $table->decimal('total_price', 10, 2);
            $table->foreignId('promo_code_id')->nullable()->constrained('promo_codes')->nullOnDelete();
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->string('currency', 3)->default('EUR');
            $table->unsignedTinyInteger('deposit_percentage')->default(30);
            $table->decimal('deposit_amount', 10, 2)->default(0);
            $table->decimal('balance_amount', 10, 2)->default(0);
            $table->decimal('deposit_guarantee', 10, 2)->nullable();
            $table->string('status')->default('pending');
            $table->string('source')->default('direct');
            $table->text('special_requests')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('promo_code_usages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('promo_code_id')->constrained('promo_codes')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('reservation_id')->nullable()->constrained('reservations')->nullOnDelete();
            $table->timestamp('applied_at');
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reservation_id')->constrained('reservations')->cascadeOnDelete();
            $table->string('payment_number')->unique();
            $table->string('type');
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('EUR');
            $table->string('status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->json('metadata')->nullable();
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
            'cleaning_fee' => 50,
            'service_fee_percentage' => 5,
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
