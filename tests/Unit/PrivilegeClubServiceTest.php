<?php

namespace Tests\Unit;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use App\Models\Island;
use App\Services\PrivilegeClubService;
use Carbon\Carbon;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrivilegeClubServiceTest extends TestCase
{
    protected PrivilegeClubService $service;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Queue::fake();

        $this->createSchema();
        $this->service = app(PrivilegeClubService::class);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('privilege_club_notifications');
        Schema::dropIfExists('reservations');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function tier_from_stay_count_follows_cdc_thresholds(): void
    {
        $this->assertNull($this->service->tierFromStayCount(0));
        $this->assertSame(PrivilegeClubService::TIER_INSIDER, $this->service->tierFromStayCount(1));
        $this->assertSame(PrivilegeClubService::TIER_SIGNATURE, $this->service->tierFromStayCount(3));
        $this->assertSame(PrivilegeClubService::TIER_LEGEND, $this->service->tierFromStayCount(7));
    }

    #[Test]
    public function sync_user_tier_upgrades_to_signature_after_third_stay(): void
    {
        $user = $this->createUser();
        $villa = $this->createVilla();

        for ($i = 0; $i < 3; $i++) {
            $this->createCompletedStay($user, $villa, Carbon::now()->subMonths(6 + $i));
        }

        $this->service->updateTierIfChanged($user, notify: false);

        $this->assertSame(PrivilegeClubService::TIER_SIGNATURE, $user->fresh()->privilege_tier);
    }

    #[Test]
    public function locked_tier_is_not_overwritten_by_sync(): void
    {
        $user = $this->createUser();
        $user->update([
            'privilege_tier' => PrivilegeClubService::TIER_INSIDER,
            'privilege_tier_manual_override' => true,
        ]);

        $villa = $this->createVilla();
        for ($i = 0; $i < 5; $i++) {
            $this->createCompletedStay($user, $villa, Carbon::now()->subMonths($i + 1));
        }

        $this->service->updateTierIfChanged($user, notify: false);

        $this->assertSame(PrivilegeClubService::TIER_INSIDER, $user->fresh()->privilege_tier);
    }

    #[Test]
    public function annual_maintenance_downgrades_when_no_booking_previous_year(): void
    {
        $user = $this->createUser();
        $user->update(['privilege_tier' => PrivilegeClubService::TIER_SIGNATURE]);

        $this->service->runAnnualMaintenance(Carbon::now()->year - 1);

        $this->assertSame(PrivilegeClubService::TIER_INSIDER, $user->fresh()->privilege_tier);
    }

    private function createSchema(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->string('privilege_tier', 20)->nullable();
            $table->boolean('privilege_tier_manual_override')->default(false);
            $table->timestamp('privilege_tier_updated_at')->nullable();
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
            $table->timestamps();
        });

        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reservation_number')->unique();
            $table->foreignId('villa_id')->constrained('villas');
            $table->foreignId('user_id')->constrained('users');
            $table->date('check_in_date');
            $table->date('check_out_date');
            $table->string('status');
            $table->decimal('total_price', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('privilege_club_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 40);
            $table->string('old_tier', 20)->nullable();
            $table->string('new_tier', 20)->nullable();
            $table->text('message');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    private function createUser(): User
    {
        return User::create([
            'first_name' => 'Test',
            'last_name' => 'Voyageur',
            'email' => 'voyager'.uniqid().'@test.fr',
            'password' => bcrypt('secret'),
        ]);
    }

    private function createVilla(): Villa
    {
        $island = Island::create(['name' => 'SM', 'code' => 'SM'.uniqid()]);

        return Villa::create([
            'name' => 'Villa',
            'slug' => 'villa-'.uniqid(),
            'island_id' => $island->id,
        ]);
    }

    private function createCompletedStay(User $user, Villa $villa, Carbon $checkout): void
    {
        Reservation::create([
            'reservation_number' => 'LX-'.uniqid(),
            'villa_id' => $villa->id,
            'user_id' => $user->id,
            'check_in_date' => $checkout->copy()->subDays(5),
            'check_out_date' => $checkout,
            'status' => 'fully_paid',
            'total_price' => 1000,
        ]);
    }
}
