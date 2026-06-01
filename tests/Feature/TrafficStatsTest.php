<?php

namespace Tests\Feature;

use App\Models\PageView;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TrafficStatsTest extends TestCase
{
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

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->boolean('is_admin')->default(false);
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

        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users');
            $table->foreignId('recipient_id')->constrained('users');
            $table->text('content');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        Schema::create('privilege_club_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('type', 40);
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('villa_reviews', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('pending');
            $table->timestamps();
        });

        Schema::create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('session_id', 64);
            $table->string('visitor_hash', 64);
            $table->foreignId('user_id')->nullable();
            $table->string('path', 500);
            $table->string('route_name', 100)->nullable();
            $table->string('page_type', 50)->nullable();
            $table->foreignId('villa_id')->nullable();
            $table->foreignId('island_id')->nullable();
            $table->string('referrer', 500)->nullable();
            $table->string('referrer_source', 30)->default('direct');
            $table->string('country_code', 2)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('viewed_at');
            $table->timestamps();
        });

        $this->admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Lux',
            'email' => 'admin@test.luxiles.fr',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('villa_reviews');
        Schema::dropIfExists('privilege_club_notifications');
        Schema::dropIfExists('messages');
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function admin_can_view_traffic_stats_page(): void
    {
        PageView::create([
            'session_id' => 's1',
            'visitor_hash' => 'v1',
            'path' => '/',
            'route_name' => 'home',
            'page_type' => 'home',
            'referrer_source' => 'direct',
            'viewed_at' => now(),
        ]);

        $this->actingAs($this->admin)
            ->get(route('admin.traffic'))
            ->assertOk()
            ->assertSee('Fréquentation du site')
            ->assertSee('Visiteurs uniques');
    }

    #[Test]
    public function guest_cannot_access_traffic_stats(): void
    {
        $this->get(route('admin.traffic'))
            ->assertRedirect(route('login'));
    }
}
