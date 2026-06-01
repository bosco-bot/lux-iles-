<?php

namespace Tests\Unit;

use App\Models\PageView;
use App\Services\PageViewTracker;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PageViewTrackerTest extends TestCase
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

        Route::get('/test-home', fn () => 'ok')->name('home');
    }

    protected function tearDown(): void
    {
        Schema::dropIfExists('page_views');
        Schema::dropIfExists('villas');
        Schema::dropIfExists('islands');
        Schema::dropIfExists('users');
        parent::tearDown();
    }

    #[Test]
    public function it_skips_tracking_when_cookies_rejected(): void
    {
        $tracker = app(PageViewTracker::class);
        $request = Request::create('/test-home', 'GET');
        $request->cookies->set('cookie_consent', 'rejected');
        $request->setRouteResolver(fn () => Route::getRoutes()->getByName('home'));

        $this->assertFalse($tracker->shouldTrack($request));
    }

    #[Test]
    public function stats_for_period_counts_unique_visitors_and_views(): void
    {
        $tracker = app(PageViewTracker::class);
        $now = now();

        PageView::create([
            'session_id' => 'sess-a',
            'visitor_hash' => 'visitor-a',
            'path' => '/',
            'route_name' => 'home',
            'page_type' => 'home',
            'referrer_source' => PageView::SOURCE_DIRECT,
            'viewed_at' => $now,
        ]);

        PageView::create([
            'session_id' => 'sess-a',
            'visitor_hash' => 'visitor-a',
            'path' => '/villas',
            'route_name' => 'villas.index',
            'page_type' => 'villas_list',
            'referrer_source' => PageView::SOURCE_SEARCH,
            'viewed_at' => $now,
        ]);

        PageView::create([
            'session_id' => 'sess-b',
            'visitor_hash' => 'visitor-b',
            'path' => '/',
            'route_name' => 'home',
            'page_type' => 'home',
            'referrer_source' => PageView::SOURCE_SOCIAL,
            'viewed_at' => $now,
        ]);

        $stats = $tracker->statsForPeriod($now->copy()->startOfDay(), $now->copy()->endOfDay());

        $this->assertSame(3, $stats['total_views']);
        $this->assertSame(2, $stats['unique_visitors']);
        $this->assertSame(2, $stats['unique_sessions']);
    }
}
