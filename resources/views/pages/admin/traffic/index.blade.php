@extends('layouts.admin')

@section('title', 'Statistiques de trafic | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Statistiques de trafic</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Fréquentation du site
            </h1>
            <p class="text-muted small mb-0">§3.8 CDC — {{ $periodLabel }} ({{ $from->format('d/m/Y') }} → {{ $to->format('d/m/Y') }})</p>
        </div>
        <form method="GET" class="d-flex flex-wrap gap-2 align-items-center mt-3 mt-md-0">
            <select name="period" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="today" {{ $period === 'today' ? 'selected' : '' }}>Aujourd'hui</option>
                <option value="7d" {{ $period === '7d' ? 'selected' : '' }}>7 jours</option>
                <option value="30d" {{ $period === '30d' ? 'selected' : '' }}>30 jours</option>
                <option value="90d" {{ $period === '90d' ? 'selected' : '' }}>90 jours</option>
            </select>
        </form>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-lux-greyBlue mb-1">Visiteurs uniques</p>
                    <p class="display-6 font-serif text-lux-dark-blue mb-0">{{ number_format($stats['unique_visitors'], 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-lux-greyBlue mb-1">Pages vues</p>
                    <p class="display-6 font-serif text-lux-gold mb-0">{{ number_format($stats['total_views'], 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <p class="small text-uppercase text-lux-greyBlue mb-1">Sessions</p>
                    <p class="display-6 font-serif text-lux-dark-blue mb-0">{{ number_format($stats['unique_sessions'], 0, ',', ' ') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card border shadow-sm mb-4" style="border-radius: 0.75rem;">
        <div class="card-header bg-white py-3">
            <h2 class="h6 mb-0 text-lux-dark-blue">Évolution des pages vues</h2>
        </div>
        <div class="card-body">
            <div id="trafficChart" style="height: 280px;"></div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border shadow-sm h-100" style="border-radius: 0.75rem;">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0 text-lux-dark-blue">Pages les plus consultées</h2>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr class="bg-light">
                                <th class="px-4 py-3 small text-uppercase">Page</th>
                                <th class="px-4 py-3 small text-uppercase text-end">Vues</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stats['top_pages'] as $page)
                                <tr>
                                    <td class="px-4 py-3">
                                        <span class="fw-medium text-lux-dark-blue">{{ $page['label'] }}</span>
                                        <span class="d-block small text-muted">{{ $page['path'] }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-end fw-semibold">{{ number_format($page['views'], 0, ',', ' ') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="2" class="text-center py-4 text-muted">Aucune donnée sur cette période</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card border shadow-sm mb-4" style="border-radius: 0.75rem;">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0 text-lux-dark-blue">Sources de trafic</h2>
                </div>
                <div class="card-body">
                    @php $sourceTotal = max(1, $stats['sources']->sum()); @endphp
                    @forelse($stats['sources'] as $source => $count)
                        @php $pct = round(($count / $sourceTotal) * 100, 1); @endphp
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $sourceLabels[$source] ?? $source }}</span>
                                <span class="fw-medium">{{ number_format($count, 0, ',', ' ') }} ({{ $pct }}%)</span>
                            </div>
                            <div class="progress" style="height: 6px;">
                                <div class="progress-bar bg-lux-gold" style="width: {{ $pct }}%; background-color: var(--lux-gold) !important;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Aucune source enregistrée.</p>
                    @endforelse
                </div>
            </div>

            <div class="card border shadow-sm" style="border-radius: 0.75rem;">
                <div class="card-header bg-white py-3">
                    <h2 class="h6 mb-0 text-lux-dark-blue">Origine géographique <span class="text-muted fw-normal">(optionnel)</span></h2>
                </div>
                <div class="card-body">
                    @forelse($stats['countries'] as $country)
                        <div class="d-flex justify-content-between small py-1 border-bottom">
                            <span>{{ $country->country_code }}</span>
                            <span class="fw-medium">{{ number_format($country->views, 0, ',', ' ') }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0">Pays non disponibles (en-tête CDN/proxy requis, ex. Cloudflare CF-IPCountry).</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    @if($stats['top_villas']->isNotEmpty())
        <div class="card border shadow-sm mt-4" style="border-radius: 0.75rem;">
            <div class="card-header bg-white py-3">
                <h2 class="h6 mb-0 text-lux-dark-blue">Fiches villas les plus vues</h2>
            </div>
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr class="bg-light">
                            <th class="px-4 py-3 small text-uppercase">Villa</th>
                            <th class="px-4 py-3 small text-uppercase text-end">Vues</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stats['top_villas'] as $villa)
                            <tr>
                                <td class="px-4 py-3 fw-medium text-lux-dark-blue">{{ $villa['name'] }}</td>
                                <td class="px-4 py-3 text-end">{{ number_format($villa['views'], 0, ',', ' ') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script src="https://cdn.plot.ly/plotly-2.27.0.min.js"></script>
<script>
    const chartLabels = @json($stats['chart']['labels']);
    const chartViews = @json($stats['chart']['views']);

    if (chartLabels.length > 0) {
        Plotly.newPlot('trafficChart', [{
            x: chartLabels,
            y: chartViews,
            type: 'scatter',
            mode: 'lines+markers',
            fill: 'tozeroy',
            line: { color: '#CBAE82', width: 2 },
            marker: { color: '#0A1A2F' },
            fillcolor: 'rgba(203, 174, 130, 0.15)',
        }], {
            margin: { t: 10, r: 20, b: 40, l: 50 },
            paper_bgcolor: 'transparent',
            plot_bgcolor: 'transparent',
            xaxis: { tickfont: { size: 11 } },
            yaxis: { tickfont: { size: 11 }, rangemode: 'tozero' },
        }, { responsive: true, displayModeBar: false });
    } else {
        document.getElementById('trafficChart').innerHTML = '<p class="text-center text-muted py-5 mb-0">Pas encore de données sur cette période.</p>';
    }
</script>
@endpush
