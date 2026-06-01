<?php

namespace Tests\Feature;

use App\Models\Island;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use App\Models\VillaReview;
use App\Services\VillaReviewService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VillaReviewTest extends TestCase
{
    protected User $user;

    protected User $admin;

    protected Villa $villa;

    protected Reservation $reservation;

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
        Schema::dropIfExists('villa_reviews');
        Schema::dropIfExists('villa_equipments');
        Schema::dropIfExists('equipments');
        Schema::dropIfExists('villa_availability_blocks');
        Schema::dropIfExists('villa_seasonal_prices');
        Schema::dropIfExists('seasons');
        Schema::dropIfExists('villa_photos');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[Test]
    public function traveler_can_submit_review_within_30_days_after_checkout(): void
    {
        $response = $this->actingAs($this->user)->post(route('espace-client.reviews.store', $this->reservation), [
            'rating' => 5,
            'comment' => 'Séjour magnifique, villa impeccable et accueil chaleureux.',
        ]);

        $response->assertRedirect(route('espace-client.reservations'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('villa_reviews', [
            'reservation_id' => $this->reservation->id,
            'status' => VillaReview::STATUS_PENDING,
            'rating' => 5,
        ]);

        $review = VillaReview::where('reservation_id', $this->reservation->id)->first();
        $this->assertNotNull($review->submitted_at);
    }

    #[Test]
    public function review_is_not_visible_on_villa_page_until_published(): void
    {
        VillaReview::create([
            'villa_id' => $this->villa->id,
            'reservation_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'comment' => 'Super séjour',
            'status' => VillaReview::STATUS_PENDING,
        ]);

        $response = $this->get(route('villas.show', $this->villa->id));

        $response->assertOk()
            ->assertDontSee('Super séjour')
            ->assertSee('Aucun avis publié');
    }

    #[Test]
    public function published_review_appears_on_villa_page_with_first_name_only(): void
    {
        VillaReview::create([
            'villa_id' => $this->villa->id,
            'reservation_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'rating' => 4,
            'comment' => 'Très belle villa avec vue exceptionnelle.',
            'status' => VillaReview::STATUS_APPROVED,
            'published_at' => now(),
        ]);

        $response = $this->get(route('villas.show', $this->villa->id));

        $response->assertOk()
            ->assertSee('Marie')
            ->assertSee('Très belle villa')
            ->assertDontSee('Dupont');
    }

    #[Test]
    public function admin_can_approve_pending_review(): void
    {
        $review = VillaReview::create([
            'villa_id' => $this->villa->id,
            'reservation_id' => $this->reservation->id,
            'user_id' => $this->user->id,
            'rating' => 5,
            'comment' => 'Excellent',
            'status' => VillaReview::STATUS_PENDING,
        ]);

        $this->actingAs($this->admin)
            ->post(route('admin.villa-reviews.approve', $review))
            ->assertRedirect();

        $this->assertSame(VillaReview::STATUS_APPROVED, $review->fresh()->status);
        $this->assertNotNull($review->fresh()->published_at);
    }

    #[Test]
    public function cannot_submit_review_after_30_day_window(): void
    {
        $this->reservation->update([
            'check_out_date' => Carbon::now()->subDays(31)->toDateString(),
        ]);

        $service = app(VillaReviewService::class);

        $this->assertFalse($service->canUserSubmitReview($this->user, $this->reservation->fresh()));
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
            $table->unsignedTinyInteger('bedrooms')->default(2);
            $table->unsignedTinyInteger('bathrooms')->default(2);
            $table->unsignedTinyInteger('max_capacity')->default(4);
            $table->unsignedTinyInteger('minimum_stay_nights')->default(3);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->decimal('base_price_per_night', 10, 2)->default(500);
            $table->timestamps();
        });

        Schema::create('villa_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->string('file_path');
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('seasons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('start_date');
            $table->date('end_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('villa_seasonal_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('season_id')->constrained('seasons')->cascadeOnDelete();
            $table->decimal('price_per_night', 10, 2);
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

        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->boolean('is_search_filter')->default(false);
            $table->timestamps();
        });

        Schema::create('villa_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->unique(['villa_id', 'equipment_id']);
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->unsignedTinyInteger('number_of_nights')->default(3);
            $table->unsignedTinyInteger('number_of_guests')->default(2);
            $table->decimal('total_price', 10, 2)->default(1000);
            $table->string('status')->default('confirmed');
            $table->timestamps();
        });

        Schema::create('villa_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('reservation_id')->unique()->constrained('reservations')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('rating');
            $table->text('comment');
            $table->string('status', 20)->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->text('admin_response')->nullable();
            $table->foreignId('moderated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('moderated_at')->nullable();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });
    }

    private function seedTestData(): void
    {
        $island = Island::create(['name' => 'Saint-Martin', 'code' => 'SM']);

        $this->villa = Villa::create([
            'name' => 'Villa Test',
            'slug' => 'villa-test',
            'island_id' => $island->id,
            'is_active' => true,
        ]);

        $this->user = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Dupont',
            'email' => 'marie@test.luxiles.fr',
            'password' => Hash::make('password'),
        ]);

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Lux',
            'email' => 'admin@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        $this->reservation = Reservation::create([
            'reservation_number' => 'LX-TEST-001',
            'villa_id' => $this->villa->id,
            'user_id' => $this->user->id,
            'check_in_date' => Carbon::now()->subDays(10)->toDateString(),
            'check_out_date' => Carbon::now()->subDays(3)->toDateString(),
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'total_price' => 3500,
            'status' => 'fully_paid',
        ]);
    }
}
