<?php

namespace Tests\Feature;

use App\Jobs\SendPrivilegeClubTierChangeJob;
use App\Models\PrivilegeClubNotification;
use App\Models\User;
use App\Services\PrivilegeClubService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PrivilegeClubManualTierTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Config::set('database.default', 'sqlite');
        Config::set('database.connections.sqlite.database', ':memory:');
        DB::purge('sqlite');
        DB::reconnect('sqlite');

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
            $table->string('privilege_tier', 20)->nullable();
            $table->boolean('privilege_tier_manual_override')->default(false);
            $table->timestamp('privilege_tier_updated_at')->nullable();
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
            $table->timestamp('whatsapp_sent_at')->nullable();
            $table->timestamps();
        });
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('privilege_club_notifications');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function email_is_dispatched_when_admin_manually_changes_tier(): void
    {
        Queue::fake();

        $user = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Client',
            'email' => 'marie-manual-tier@test.luxiles.fr',
            'password' => Hash::make('password'),
            'privilege_tier' => null,
        ]);

        $service = app(PrivilegeClubService::class);
        $service->setManualTier($user, PrivilegeClubService::TIER_INSIDER);

        Queue::assertPushed(SendPrivilegeClubTierChangeJob::class, function (SendPrivilegeClubTierChangeJob $job) use ($user) {
            return $job->user->id === $user->id
                && $job->oldTier === null
                && $job->newTier === PrivilegeClubService::TIER_INSIDER;
        });
    }

    #[Test]
    public function no_email_dispatched_when_tier_does_not_change(): void
    {
        Queue::fake();

        $user = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Client',
            'email' => 'marie-same-tier@test.luxiles.fr',
            'password' => Hash::make('password'),
            'privilege_tier' => PrivilegeClubService::TIER_INSIDER,
        ]);

        $service = app(PrivilegeClubService::class);
        $service->setManualTier($user, PrivilegeClubService::TIER_INSIDER);

        Queue::assertNotPushed(SendPrivilegeClubTierChangeJob::class);
    }

    #[Test]
    public function in_app_notification_is_still_created_on_manual_change(): void
    {
        Queue::fake();

        $user = User::create([
            'first_name' => 'Marie',
            'last_name' => 'Client',
            'email' => 'marie-notif@test.luxiles.fr',
            'password' => Hash::make('password'),
            'privilege_tier' => PrivilegeClubService::TIER_INSIDER,
        ]);

        $service = app(PrivilegeClubService::class);
        $service->setManualTier($user, PrivilegeClubService::TIER_SIGNATURE);

        $this->assertDatabaseHas('privilege_club_notifications', [
            'user_id' => $user->id,
            'old_tier' => PrivilegeClubService::TIER_INSIDER,
            'new_tier' => PrivilegeClubService::TIER_SIGNATURE,
            'type' => PrivilegeClubNotification::TYPE_TIER_UP,
        ]);
    }
}
