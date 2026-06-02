<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\Villa;
use App\Models\VillaAvailabilityBlock;
use App\Services\VillaAvailabilityContext;
use App\Services\VillaAvailabilityService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class VillaAvailabilityServiceTest extends TestCase
{
    protected VillaAvailabilityService $service;

    protected int $villaId;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');

        DB::purge('sqlite');
        DB::reconnect('sqlite');

        $this->createSchema();
        $this->service = new VillaAvailabilityService;
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
        Schema::dropIfExists('villa_availability_blocks');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        parent::tearDown();
    }

    #[Test]
    public function it_detects_reservation_overlap(): void
    {
        Reservation::create([
            'reservation_number' => 'LX-TEST01-2026',
            'villa_id' => $this->villaId,
            'user_id' => 1,
            'guest_first_name' => 'A',
            'guest_last_name' => 'B',
            'guest_email' => 'a@test.fr',
            'check_in_date' => '2026-06-10',
            'check_out_date' => '2026-06-17',
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'base_price' => 1000,
            'total_price' => 1000,
            'status' => 'confirmed',
            'source' => 'manual',
        ]);

        $this->assertTrue($this->service->hasConflict(
            $this->villaId,
            Carbon::parse('2026-06-15'),
            Carbon::parse('2026-06-20')
        ));

        $this->assertFalse($this->service->hasConflict(
            $this->villaId,
            Carbon::parse('2026-06-18'),
            Carbon::parse('2026-06-24')
        ));
    }

    #[Test]
    public function it_excludes_cancelled_reservations(): void
    {
        Reservation::create([
            'reservation_number' => 'LX-TEST02-2026',
            'villa_id' => $this->villaId,
            'user_id' => 1,
            'guest_first_name' => 'A',
            'guest_last_name' => 'B',
            'guest_email' => 'a@test.fr',
            'check_in_date' => '2026-07-01',
            'check_out_date' => '2026-07-08',
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'base_price' => 1000,
            'total_price' => 1000,
            'status' => 'cancelled',
            'source' => 'manual',
        ]);

        $this->assertFalse($this->service->hasConflict(
            $this->villaId,
            Carbon::parse('2026-07-03'),
            Carbon::parse('2026-07-10')
        ));
    }

    #[Test]
    public function public_context_includes_pending_reservations_in_blocked_dates(): void
    {
        Reservation::create([
            'reservation_number' => 'LX-PENDING-2026',
            'villa_id' => $this->villaId,
            'user_id' => 1,
            'guest_first_name' => 'A',
            'guest_last_name' => 'B',
            'guest_email' => 'pending@test.fr',
            'check_in_date' => '2026-10-01',
            'check_out_date' => '2026-10-05',
            'number_of_nights' => 4,
            'number_of_guests' => 2,
            'base_price' => 500,
            'total_price' => 500,
            'status' => 'pending',
            'source' => 'direct',
        ]);

        $adminBlocked = $this->service->getBlockedDates($this->villaId, null, VillaAvailabilityContext::admin());
        $publicBlocked = $this->service->getBlockedDates($this->villaId, null, VillaAvailabilityContext::publicSite());

        $this->assertContains('2026-10-01', $adminBlocked);
        $this->assertContains('2026-10-01', $publicBlocked);
    }

    #[Test]
    public function it_returns_blocked_dates_for_calendar(): void
    {
        Reservation::create([
            'reservation_number' => 'LX-TEST03-2026',
            'villa_id' => $this->villaId,
            'user_id' => 1,
            'guest_first_name' => 'A',
            'guest_last_name' => 'B',
            'guest_email' => 'a@test.fr',
            'check_in_date' => '2026-08-01',
            'check_out_date' => '2026-08-03',
            'number_of_nights' => 2,
            'number_of_guests' => 2,
            'base_price' => 500,
            'total_price' => 500,
            'status' => 'confirmed',
            'source' => 'direct',
        ]);

        VillaAvailabilityBlock::create([
            'villa_id' => $this->villaId,
            'start_date' => '2026-09-10',
            'end_date' => '2026-09-12',
            'reason' => 'Maintenance',
        ]);

        $blocked = $this->service->getBlockedDates($this->villaId);

        $this->assertContains('2026-08-01', $blocked);
        $this->assertContains('2026-08-02', $blocked);
        $this->assertContains('2026-08-03', $blocked);
        $this->assertContains('2026-09-10', $blocked);
        $this->assertContains('2026-09-12', $blocked);
    }

    protected function createSchema(): void
    {
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
            $table->string('source')->nullable();
            $table->timestamps();
        });

        Schema::create('villa_availability_blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('villa_id');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }
}
