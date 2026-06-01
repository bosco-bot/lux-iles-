<?php

namespace Tests\Feature;

use App\Models\Equipment;
use App\Models\Island;
use App\Models\User;
use App\Models\Villa;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class EquipmentSearchFilterTest extends TestCase
{
    protected Island $island;

    protected Equipment $filterEquipment;

    protected Equipment $nonFilterEquipment;

    protected Villa $villaWithPool;

    protected Villa $villaWithoutPool;

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
        Schema::dropIfExists('villa_photos');
        Schema::dropIfExists('villa_equipments');
        Schema::dropIfExists('equipments');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');

        parent::tearDown();
    }

    #[Test]
    public function villas_index_only_lists_equipments_marked_as_search_filters(): void
    {
        $response = $this->get(route('villas.index'));

        $response->assertOk()
            ->assertSee('Piscine')
            ->assertDontSee('Alarme');
    }

    #[Test]
    public function villas_index_ignores_equipment_filter_ids_not_marked_as_search_filters(): void
    {
        $response = $this->get(route('villas.index', [
            'equipments' => [$this->nonFilterEquipment->id],
        ]));

        $response->assertOk()
            ->assertSee('Villa Avec Piscine')
            ->assertSee('Villa Sans Piscine');
    }

    #[Test]
    public function villas_index_filters_by_allowed_search_filter_equipment(): void
    {
        $response = $this->get(route('villas.index', [
            'equipments' => [$this->filterEquipment->id],
        ]));

        $response->assertOk()
            ->assertSee('Villa Avec Piscine')
            ->assertDontSee('Villa Sans Piscine');
    }

    #[Test]
    public function admin_can_toggle_equipment_search_filter(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Lux',
            'email' => 'admin@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
            'is_active' => true,
        ]);

        $this->actingAs($admin)
            ->patch(route('admin.equipments.toggle-search-filter', $this->nonFilterEquipment))
            ->assertRedirect(route('admin.equipments.index'));

        $this->assertTrue($this->nonFilterEquipment->fresh()->is_search_filter);
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

        Schema::create('equipments', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('icon')->nullable();
            $table->string('category')->nullable();
            $table->boolean('is_search_filter')->default(false);
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
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('villa_equipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->foreignId('equipment_id')->constrained('equipments')->cascadeOnDelete();
            $table->unique(['villa_id', 'equipment_id']);
        });

        Schema::create('villa_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('villa_id')->constrained('villas')->cascadeOnDelete();
            $table->string('file_path');
            $table->string('file_name')->nullable();
            $table->string('alt_text')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    private function seedTestData(): void
    {
        $this->island = Island::create([
            'name' => 'Saint-Martin',
            'code' => 'SM',
            'country' => 'France',
        ]);

        $this->filterEquipment = Equipment::create([
            'name' => 'Piscine',
            'is_search_filter' => true,
        ]);

        $this->nonFilterEquipment = Equipment::create([
            'name' => 'Alarme',
            'is_search_filter' => false,
        ]);

        $this->villaWithPool = Villa::create([
            'name' => 'Villa Avec Piscine',
            'slug' => 'villa-avec-piscine',
            'island_id' => $this->island->id,
            'base_price_per_night' => 800,
            'is_active' => true,
        ]);
        $this->villaWithPool->equipments()->attach($this->filterEquipment->id);

        $this->villaWithoutPool = Villa::create([
            'name' => 'Villa Sans Piscine',
            'slug' => 'villa-sans-piscine',
            'island_id' => $this->island->id,
            'base_price_per_night' => 600,
            'is_active' => true,
        ]);
    }
}
