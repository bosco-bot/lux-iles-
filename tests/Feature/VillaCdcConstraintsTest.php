<?php

namespace Tests\Feature;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VillaCdcConstraintsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::dropIfExists('villas');
        Schema::create('villas', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->unsignedTinyInteger('max_capacity')->default(2);
            $table->unsignedTinyInteger('minimum_stay_nights')->default(3);
            $table->timestamps();
        });

        DB::table('villas')->insert([
            [
                'name' => 'Villa Aurélio',
                'slug' => 'aurelio',
                'max_capacity' => 2,
                'minimum_stay_nights' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Villa Soléro',
                'slug' => 'solero',
                'max_capacity' => 6,
                'minimum_stay_nights' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Villa Élios',
                'slug' => 'elios',
                'max_capacity' => 8,
                'minimum_stay_nights' => 5,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('villas');
        parent::tearDown();
    }

    #[Test]
    public function fix_villa_cdc_constraints_migration_applies_cdc_values(): void
    {
        $migration = require database_path('migrations/2026_06_02_130000_fix_villa_cdc_constraints.php');
        $migration->up();

        $this->assertDatabaseHas('villas', [
            'slug' => 'aurelio',
            'minimum_stay_nights' => 4,
            'max_capacity' => 8,
        ]);
        $this->assertDatabaseHas('villas', [
            'slug' => 'solero',
            'minimum_stay_nights' => 4,
            'max_capacity' => 8,
        ]);
        $this->assertDatabaseHas('villas', [
            'slug' => 'elios',
            'minimum_stay_nights' => 2,
            'max_capacity' => 4,
        ]);
    }
}
