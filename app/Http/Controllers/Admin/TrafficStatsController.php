<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PageViewTracker;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TrafficStatsController extends Controller
{
    public function index(Request $request, PageViewTracker $tracker)
    {
        $period = $request->query('period', '30d');

        [$from, $to, $periodLabel] = $this->resolvePeriod($period, $request);

        $stats = $tracker->statsForPeriod($from, $to);

        $sourceLabels = collect($stats['sources']->keys())->mapWithKeys(
            fn ($key) => [$key => $tracker->sourceLabel($key)]
        );

        return view('pages.admin.traffic.index', compact(
            'stats',
            'period',
            'periodLabel',
            'from',
            'to',
            'sourceLabels'
        ));
    }

    /**
     * @return array{0: Carbon, 1: Carbon, 2: string}
     */
    protected function resolvePeriod(string $period, Request $request): array
    {
        if ($period === 'custom' && $request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->query('from'))->startOfDay();
            $to = Carbon::parse($request->query('to'))->endOfDay();

            return [$from, $to, $from->format('d/m/Y').' — '.$to->format('d/m/Y')];
        }

        return match ($period) {
            '7d' => [now()->subDays(6)->startOfDay(), now()->endOfDay(), '7 derniers jours'],
            '90d' => [now()->subDays(89)->startOfDay(), now()->endOfDay(), '90 derniers jours'],
            'today' => [now()->startOfDay(), now()->endOfDay(), 'Aujourd\'hui'],
            default => [now()->subDays(29)->startOfDay(), now()->endOfDay(), '30 derniers jours'],
        };
    }
}
