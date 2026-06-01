<?php

namespace App\Services;

use App\Models\PageView;
use App\Models\Villa;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PageViewTracker
{
    public function shouldTrack(Request $request): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if ($request->cookie('cookie_consent') === 'rejected') {
            return false;
        }

        if ($request->routeIs('admin.*', 'espace-client.*', 'login', 'register', 'password.*')) {
            return false;
        }

        $path = $request->path();
        if (preg_match('#^(storage|build|vendor|up|api)#', $path)) {
            return false;
        }

        $ua = strtolower($request->userAgent() ?? '');
        if ($ua && preg_match('/bot|crawl|spider|slurp|mediapartners|headless|preview/', $ua)) {
            return false;
        }

        return $request->route() !== null;
    }

    public function record(Request $request): void
    {
        if (! $this->shouldTrack($request)) {
            return;
        }

        $route = $request->route();
        $routeName = $route?->getName();
        $meta = $this->resolvePageMeta($request, $routeName);

        if ($meta['villa_id'] && ! Villa::whereKey($meta['villa_id'])->exists()) {
            $meta['villa_id'] = null;
        }

        PageView::create([
            'session_id' => $request->session()->getId(),
            'visitor_hash' => $this->visitorHash($request),
            'user_id' => $request->user()?->id,
            'path' => '/'.ltrim($request->path(), '/'),
            'route_name' => $routeName,
            'page_type' => $meta['page_type'],
            'villa_id' => $meta['villa_id'],
            'island_id' => $meta['island_id'],
            'referrer' => $this->truncate($request->headers->get('referer'), 500),
            'referrer_source' => $this->classifyReferrer($request->headers->get('referer'), $request),
            'country_code' => $this->resolveCountryCode($request),
            'user_agent' => $this->truncate($request->userAgent(), 255),
            'viewed_at' => now(),
        ]);
    }

    /**
     * Statistiques agrégées pour le back-office (§3.8).
     *
     * @return array<string, mixed>
     */
    public function statsForPeriod(Carbon $from, Carbon $to): array
    {
        $base = PageView::query()->whereBetween('viewed_at', [$from, $to]);

        $totalViews = (clone $base)->count();
        $uniqueVisitors = (clone $base)->distinct('visitor_hash')->count('visitor_hash');
        $uniqueSessions = (clone $base)->distinct('session_id')->count('session_id');

        $topPages = (clone $base)
            ->select('page_type', 'route_name', 'path', 'villa_id', DB::raw('COUNT(*) as views'))
            ->groupBy('page_type', 'route_name', 'path', 'villa_id')
            ->orderByDesc('views')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                'label' => $this->labelForPageRow($row),
                'views' => (int) $row->views,
                'path' => $row->path,
            ]);

        $sources = (clone $base)
            ->select('referrer_source', DB::raw('COUNT(*) as views'))
            ->groupBy('referrer_source')
            ->orderByDesc('views')
            ->get()
            ->mapWithKeys(fn ($row) => [$row->referrer_source => (int) $row->views]);

        $countries = (clone $base)
            ->whereNotNull('country_code')
            ->select('country_code', DB::raw('COUNT(*) as views'))
            ->groupBy('country_code')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $viewsByDay = (clone $base)
            ->select(DB::raw('DATE(viewed_at) as day'), DB::raw('COUNT(*) as views'))
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $villaViews = (clone $base)
            ->whereNotNull('villa_id')
            ->select('villa_id', DB::raw('COUNT(*) as views'))
            ->groupBy('villa_id')
            ->orderByDesc('views')
            ->limit(10)
            ->get();

        $villaNames = Villa::whereIn('id', $villaViews->pluck('villa_id'))->pluck('name', 'id');

        return [
            'total_views' => $totalViews,
            'unique_visitors' => $uniqueVisitors,
            'unique_sessions' => $uniqueSessions,
            'top_pages' => $topPages,
            'sources' => $sources,
            'countries' => $countries,
            'views_by_day' => $viewsByDay,
            'top_villas' => $villaViews->map(fn ($row) => [
                'villa_id' => $row->villa_id,
                'name' => $villaNames[$row->villa_id] ?? 'Villa #'.$row->villa_id,
                'views' => (int) $row->views,
            ]),
            'chart' => [
                'labels' => $viewsByDay->pluck('day')->map(fn ($d) => Carbon::parse($d)->format('d/m'))->values()->all(),
                'views' => $viewsByDay->pluck('views')->map(fn ($v) => (int) $v)->values()->all(),
            ],
        ];
    }

    /**
     * @return array{page_type: string, villa_id: ?int, island_id: ?int}
     */
    protected function resolvePageMeta(Request $request, ?string $routeName): array
    {
        $pageType = 'other';
        $villaId = null;
        $islandId = null;

        if ($routeName === 'home') {
            $pageType = 'home';
        } elseif ($routeName === 'villas.index') {
            $pageType = 'villas_list';
            $islandId = $request->integer('island') ?: null;
        } elseif ($routeName === 'villas.show') {
            $pageType = 'villa_detail';
            $villaId = $request->route('id');
        } elseif (str_starts_with((string) $routeName, 'destination.')) {
            $pageType = 'destination';
            $islandId = $request->query('island');
        } elseif (str_starts_with((string) $routeName, 'bookings.')) {
            $pageType = 'booking';
        } elseif ($routeName === 'contact.index') {
            $pageType = 'contact';
        } elseif (in_array($routeName, ['cgv', 'mentions-legales', 'politique-cookies'], true)) {
            $pageType = 'legal';
        }

        return [
            'page_type' => $pageType,
            'villa_id' => $villaId ? (int) $villaId : null,
            'island_id' => $islandId ? (int) $islandId : null,
        ];
    }

    protected function classifyReferrer(?string $referrer, Request $request): string
    {
        if (empty($referrer)) {
            return PageView::SOURCE_DIRECT;
        }

        $host = strtolower(parse_url($referrer, PHP_URL_HOST) ?? '');
        $siteHost = strtolower($request->getHost());

        if ($host === '' || str_contains($host, $siteHost)) {
            return PageView::SOURCE_DIRECT;
        }

        if (preg_match('/google\.|bing\.|yahoo\.|duckduckgo\.|ecosia\.|qwant\.|baidu\./', $host)) {
            return PageView::SOURCE_SEARCH;
        }

        if (preg_match('/facebook\.|instagram\.|twitter\.|x\.com|linkedin\.|tiktok\.|pinterest\.|youtube\./', $host)) {
            return PageView::SOURCE_SOCIAL;
        }

        return PageView::SOURCE_REFERRAL;
    }

    protected function resolveCountryCode(Request $request): ?string
    {
        $code = $request->headers->get('CF-IPCountry')
            ?? $request->headers->get('X-Country-Code');

        if (! $code || strlen($code) !== 2 || strtoupper($code) === 'XX') {
            return null;
        }

        return strtoupper($code);
    }

    protected function visitorHash(Request $request): string
    {
        return hash('sha256', $request->session()->getId().'|'.$request->ip());
    }

    protected function truncate(?string $value, int $max): ?string
    {
        if ($value === null) {
            return null;
        }

        return mb_strlen($value) > $max ? mb_substr($value, 0, $max) : $value;
    }

    protected function labelForPageRow(object $row): string
    {
        if ($row->villa_id) {
            $name = Villa::find($row->villa_id)?->name;

            return $name ? "Fiche villa — {$name}" : "Fiche villa #{$row->villa_id}";
        }

        return match ($row->page_type) {
            'home' => 'Accueil',
            'villas_list' => 'Liste des villas',
            'villa_detail' => 'Fiche villa',
            'destination' => 'Destination',
            'booking' => 'Réservation',
            'contact' => 'Contact',
            'legal' => 'Page légale',
            default => $row->path ?: ($row->route_name ?? 'Page'),
        };
    }

    public function sourceLabel(string $source): string
    {
        return match ($source) {
            PageView::SOURCE_SEARCH => 'Moteurs de recherche',
            PageView::SOURCE_SOCIAL => 'Réseaux sociaux',
            PageView::SOURCE_REFERRAL => 'Sites référents',
            default => 'Accès direct',
        };
    }
}
