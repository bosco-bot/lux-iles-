@extends('layouts.admin')

@section('title', 'Synchronisation | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Synchronisation</span>
@endsection

@push('styles')
<style>
    .dashboard-card {
        background: white;
        border: 1px solid #E5E7EB;
        border-radius: 0.5rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
        transition: all 0.3s ease;
    }
    .dashboard-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    .card-bg-icon {
        position: absolute;
        top: 0;
        right: 0;
        padding: 1rem;
        opacity: 0.1;
        font-size: 3.75rem;
        color: var(--lux-dark-blue);
        transition: transform 0.3s;
    }
    .dashboard-card:hover .card-bg-icon {
        transform: scale(1.1);
    }
    .platform-icon-bg {
        width: 40px;
        height: 40px;
        border-radius: 0.5rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .timeline-item {
        position: relative;
        padding-bottom: 1.5rem;
    }
    .timeline-item::after {
        content: '';
        position: absolute;
        left: 19px;
        top: 40px;
        bottom: 0;
        width: 2px;
        background-color: #f3f4f6;
    }
    .timeline-item:last-child::after {
        display: none;
    }
    .timeline-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 2px solid white;
        position: relative;
        z-index: 10;
    }
    #syncChart {
        height: 350px;
        width: 100%;
    }
</style>
@endpush

@section('content')
    <!-- Top Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Synchronisation Multi-Plateformes
            </h1>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <div class="d-flex align-items-center gap-2 px-3 py-1.5 rounded-pill small fw-medium" style="background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0;">
                <span class="rounded-circle d-inline-block" style="width: 8px; height: 8px; background-color: #10b981; animation: pulse 2s infinite;"></span>
                Système opérationnel
            </div>
            <a href="{{ route('admin.synchronization.config') }}" class="btn btn-sm px-3 py-2 rounded small text-lux-greyBlue border me-2">
                <i class="fa-solid fa-link me-1"></i>Gérer les plateformes
            </a>
            <button id="force-sync-btn" class="btn px-4 py-2 rounded small fw-medium text-white d-flex align-items-center gap-2" style="background-color: var(--lux-gold); transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-goldHover)'" onmouseout="this.style.backgroundColor='var(--lux-gold)'">
                <i class="fa-solid fa-bolt"></i>
                Forcer la synchro
            </button>
        </div>
    </div>

    <!-- Section 1: Platform Status Cards -->
    <section class="mb-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 font-serif fw-semibold" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">État des Canaux</h2>
            <span class="small text-lux-greyBlue">
                @if($lastGlobalUpdate)
                    Dernière maj: {{ $lastGlobalUpdate->last_sync_at->diffForHumans() }}
                @else
                    Aucune synchronisation
                @endif
            </span>
        </div>
        
        <div class="d-flex flex-column flex-md-row gap-4">
            <!-- Airbnb Card -->
            @php
                $airbnb = $platformStats['airbnb'] ?? ['listings' => 0, 'success_rate' => 0, 'status' => 'not_configured', 'status_text' => 'Non configuré', 'status_class' => 'text-secondary', 'status_icon' => 'fa-circle', 'last_sync' => null];
                $airbnbBorder = $airbnb['status'] === 'latency' || $airbnb['status'] === 'error' ? 'border-start border-warning border-4' : '';
            @endphp
            <div class="flex-fill" style="min-width: 0;">
                <div class="dashboard-card p-4 position-relative overflow-hidden h-100 {{ $airbnbBorder }}">
                    <div class="card-bg-icon">
                        <i class="fa-brands fa-airbnb"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="platform-icon-bg" style="background-color: rgba(255, 90, 95, 0.1); color: #FF5A5F;">
                                <i class="fa-brands fa-airbnb" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0" style="color: var(--lux-dark-blue);">Airbnb</h3>
                                <span class="small {{ $airbnb['status_class'] }} d-flex align-items-center gap-1">
                                    <i class="fa-solid {{ $airbnb['status_icon'] }}"></i> {{ $airbnb['status_text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fs-3 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $airbnb['listings'] }}</span>
                            <span class="small text-lux-greyBlue">Listings</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-lux-greyBlue">Taux de succès</span>
                            <span class="fw-medium {{ $airbnb['success_rate'] < 95 ? 'text-warning' : '' }}" style="color: {{ $airbnb['success_rate'] >= 95 ? 'var(--lux-dark-blue)' : '' }};">{{ $airbnb['success_rate'] }}%</span>
                        </div>
                        <div class="w-100 mb-0" style="height: 6px; background-color: #f3f4f6; border-radius: 9999px;">
                            <div style="height: 6px; background-color: #FF5A5F; border-radius: 9999px; width: {{ $airbnb['success_rate'] }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-lux-greyBlue mt-2 pt-2 border-top">
                            <span>Dernier sync</span>
                            <span class="{{ $airbnb['last_sync'] ? '' : 'text-warning fw-medium' }}">
                                @if($airbnb['last_sync'])
                                    {{ $airbnb['last_sync']->format('H:i') }}
                                @else
                                    Jamais
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Booking.com Card -->
            @php
                $booking = $platformStats['booking'] ?? ['listings' => 0, 'success_rate' => 0, 'status' => 'not_configured', 'status_text' => 'Non configuré', 'status_class' => 'text-secondary', 'status_icon' => 'fa-circle', 'last_sync' => null];
                $bookingBorder = $booking['status'] === 'latency' || $booking['status'] === 'error' ? 'border-start border-warning border-4' : '';
            @endphp
            <div class="flex-fill" style="min-width: 0;">
                <div class="dashboard-card p-4 position-relative overflow-hidden h-100 {{ $bookingBorder }}">
                    <div class="card-bg-icon">
                        <i class="fa-solid fa-b"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="platform-icon-bg" style="background-color: rgba(0, 53, 128, 0.1); color: #003580;">
                                <span class="fw-bold" style="font-size: 1.25rem;">B.</span>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0" style="color: var(--lux-dark-blue);">Booking.com</h3>
                                <span class="small {{ $booking['status_class'] }} d-flex align-items-center gap-1">
                                    <i class="fa-solid {{ $booking['status_icon'] }}"></i> {{ $booking['status_text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fs-3 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $booking['listings'] }}</span>
                            <span class="small text-lux-greyBlue">Listings</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-lux-greyBlue">Taux de succès</span>
                            <span class="fw-medium {{ $booking['success_rate'] < 95 ? 'text-warning' : '' }}" style="color: {{ $booking['success_rate'] >= 95 ? 'var(--lux-dark-blue)' : '' }};">{{ $booking['success_rate'] }}%</span>
                        </div>
                        <div class="w-100 mb-0" style="height: 6px; background-color: #f3f4f6; border-radius: 9999px;">
                            <div style="height: 6px; background-color: #003580; border-radius: 9999px; width: {{ $booking['success_rate'] }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-lux-greyBlue mt-2 pt-2 border-top">
                            <span>Dernier sync</span>
                            <span class="{{ $booking['last_sync'] ? '' : 'text-warning fw-medium' }}">
                                @if($booking['last_sync'])
                                    {{ $booking['last_sync']->format('H:i') }}
                                @else
                                    Jamais
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- VRBO Card -->
            @php
                $vrbo = $platformStats['vrbo'] ?? ['listings' => 0, 'success_rate' => 0, 'status' => 'not_configured', 'status_text' => 'Non configuré', 'status_class' => 'text-secondary', 'status_icon' => 'fa-circle', 'last_sync' => null];
                $vrboBorder = $vrbo['status'] === 'latency' || $vrbo['status'] === 'error' ? 'border-start border-warning border-4' : '';
            @endphp
            <div class="flex-fill" style="min-width: 0;">
                <div class="dashboard-card p-4 position-relative overflow-hidden h-100 {{ $vrboBorder }}">
                    <div class="card-bg-icon">
                        <i class="fa-solid fa-house-laptop"></i>
                    </div>
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="platform-icon-bg" style="background-color: rgba(42, 110, 190, 0.1); color: #2a6ebe;">
                                <i class="fa-solid fa-house-laptop" style="font-size: 1.25rem;"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold mb-0" style="color: var(--lux-dark-blue);">VRBO</h3>
                                <span class="small {{ $vrbo['status_class'] }} d-flex align-items-center gap-1">
                                    <i class="fa-solid {{ $vrbo['status_icon'] }}"></i> {{ $vrbo['status_text'] }}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="d-block fs-3 font-serif mb-0" style="color: var(--lux-dark-blue);">{{ $vrbo['listings'] }}</span>
                            <span class="small text-lux-greyBlue">Listings</span>
                        </div>
                    </div>
                    <div>
                        <div class="d-flex justify-content-between small mb-3">
                            <span class="text-lux-greyBlue">Taux de succès</span>
                            <span class="fw-medium {{ $vrbo['success_rate'] < 95 ? 'text-warning' : '' }}" style="color: {{ $vrbo['success_rate'] >= 95 ? 'var(--lux-dark-blue)' : '' }};">{{ $vrbo['success_rate'] }}%</span>
                        </div>
                        <div class="w-100 mb-0" style="height: 6px; background-color: #f3f4f6; border-radius: 9999px;">
                            <div class="bg-warning" style="height: 6px; border-radius: 9999px; width: {{ $vrbo['success_rate'] }}%;"></div>
                        </div>
                        <div class="d-flex justify-content-between small text-lux-greyBlue mt-2 pt-2 border-top">
                            <span>Dernier sync</span>
                            <span class="{{ $vrbo['last_sync'] ? '' : 'text-warning fw-medium' }}">
                                @if($vrbo['last_sync'])
                                    {{ $vrbo['last_sync']->diffForHumans() }}
                                @else
                                    Jamais
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Chart & Timeline -->
    <div class="row g-4 mb-5">
        <!-- Chart Area -->
        <div class="col-lg-8">
            <div class="dashboard-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 font-serif fw-semibold" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Volume de Synchronisations & Erreurs</h3>
                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-sm px-3 py-1 rounded-pill small fw-medium chart-period-btn" data-period="24h" style="background-color: rgba(203, 174, 130, 0.1); color: var(--lux-gold); border: 1px solid var(--lux-gold);">24h</button>
                        <button class="btn btn-sm px-3 py-1 rounded-pill small text-lux-greyBlue chart-period-btn" data-period="7j">7j</button>
                        <button class="btn btn-sm px-3 py-1 rounded-pill small text-lux-greyBlue chart-period-btn" data-period="30j">30j</button>
                    </div>
                </div>
                <div id="syncChart"></div>
            </div>
        </div>

        <!-- Timeline Logs -->
        <div class="col-lg-4">
            <div class="dashboard-card p-4 d-flex flex-column" style="height: 450px;">
                <h3 class="h5 font-serif fw-semibold mb-4" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Activités Récentes</h3>
                <div class="overflow-y-auto pe-2 flex-grow-1">
                    @forelse($recentLogs as $log)
                        @php
                            $logStatus = $log->status; // 'success', 'error', 'pending'
                            $logError = $log->error_message;
                            $platformName = ucfirst($log->platform);
                            
                            $iconBg = match($logStatus) {
                                'success' => '#d1fae5',
                                'error' => '#fee2e2',
                                default => '#dbeafe'
                            };
                            $iconColor = match($logStatus) {
                                'success' => '#059669',
                                'error' => '#dc2626',
                                default => '#2563eb'
                            };
                            $icon = match($logStatus) {
                                'success' => 'fa-check',
                                'error' => 'fa-triangle-exclamation',
                                default => 'fa-calendar-plus'
                            };
                            $title = match($logStatus) {
                                'success' => 'Sync réussie',
                                'error' => 'Échec Sync (' . $platformName . ')',
                                default => 'Sync en attente'
                            };
                            $description = $logStatus === 'error' 
                                ? ($logError ?? 'Erreur inconnue')
                                : ($log->villa ? $log->villa->name . ' - ' . $platformName : 'Synchronisation');
                        @endphp
                        <div class="timeline-item d-flex gap-3">
                            <div class="timeline-icon flex-shrink-0" style="background-color: {{ $iconBg }}; color: {{ $iconColor }};">
                                <i class="fa-solid {{ $icon }} small"></i>
                            </div>
                            <div class="pt-1">
                                <p class="small fw-medium mb-1" style="color: var(--lux-dark-blue);">{{ $title }}</p>
                                <p class="small text-lux-greyBlue mb-1">{{ $description }}</p>
                                <span class="small text-lux-greyBlue" style="font-size: 0.7rem; opacity: 0.7;">
                                    @if($log->last_sync_at)
                                        {{ $log->last_sync_at->diffForHumans() }}
                                    @else
                                        Jamais
                                    @endif
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-lux-greyBlue py-4">
                            <i class="fa-solid fa-inbox fa-2x mb-2 opacity-50"></i>
                            <p class="small mb-0">Aucune activité récente</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Section 3: Detailed Logs Table -->
    <section class="dashboard-card overflow-hidden">
        <div class="p-4 border-bottom d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <h2 class="h5 font-serif fw-semibold mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Journal des Opérations</h2>
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 small text-lux-greyBlue"></i>
                    <input type="text" placeholder="Rechercher..." class="form-control ps-5" style="width: 250px; border-color: #e5e7eb;">
                </div>
                <button class="btn p-2 text-lux-greyBlue border" style="border-color: #e5e7eb;">
                    <i class="fa-solid fa-filter"></i>
                </button>
                <button class="btn p-2 text-lux-greyBlue border" style="border-color: #e5e7eb;">
                    <i class="fa-solid fa-download"></i>
                </button>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">ID Log</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Date & Heure</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Plateforme</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Action</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</th>
                        <th class="py-3 px-4 small text-uppercase fw-semibold text-lux-greyBlue text-end" style="font-size: 0.7rem; letter-spacing: 0.05em;">Détails</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLogs as $log)
                        @php
                            $logStatus = $log->status;
                            $logError = $log->error_message;
                            $platformName = ucfirst($log->platform);

                            $platformIcon = match($log->platform) {
                                'airbnb' => '<i class="fa-brands fa-airbnb" style="color: #FF5A5F;"></i>',
                                'booking' => '<span class="fw-bold" style="color: #003580;">B.</span>',
                                'vrbo' => '<i class="fa-solid fa-house-laptop" style="color: #2a6ebe;"></i>',
                                default => '<i class="fa-solid fa-globe"></i>'
                            };
                            $statusBadge = match($logStatus) {
                                'success' => ['bg' => '#d1fae5', 'color' => '#065f46', 'icon' => 'fa-check-circle', 'text' => 'Succès'],
                                'error' => ['bg' => '#fee2e2', 'color' => '#991b1b', 'icon' => 'fa-xmark-circle', 'text' => 'Échec'],
                                default => ['bg' => '#dbeafe', 'color' => '#1e40af', 'icon' => 'fa-clock', 'text' => 'En attente']
                            };
                            $rowBg = $logStatus === 'error' ? 'style="background-color: rgba(254, 226, 226, 0.3);"' : '';
                        @endphp
                        <tr {!! $rowBg !!}>
                            <td class="py-3 px-4 small text-lux-greyBlue font-monospace">#LOG-{{ str_pad($log->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-3 px-4 small" style="color: var(--lux-dark-blue);">
                                @if($log->last_sync_at)
                                    {{ $log->last_sync_at->format('d M, H:i:s') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <div class="d-flex align-items-center gap-2">
                                    {!! $platformIcon !!}
                                    <span class="small" style="color: var(--lux-dark-blue);">{{ $platformName }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 small" style="color: var(--lux-dark-blue);">
                                @if($log->villa)
                                    {{ $log->villa->name }}
                                @else
                                    Sync Calendrier
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                <span class="badge d-inline-flex align-items-center gap-1 px-2 py-1 rounded-pill small fw-medium" style="background-color: {{ $statusBadge['bg'] }}; color: {{ $statusBadge['color'] }};">
                                    <i class="fa-solid {{ $statusBadge['icon'] }}" style="font-size: 0.6rem;"></i> {{ $statusBadge['text'] }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-end">
                                @if($logError)
                                    <button class="btn btn-link p-0 text-lux-gold" style="text-decoration: none;" onclick="alert('{{ addslashes($logError) }}')" onmouseover="this.style.color='var(--lux-goldHover)'" onmouseout="this.style.color='var(--lux-gold)'" title="Voir l'erreur">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="py-3 px-4 text-center text-lux-greyBlue" colspan="6">Aucun log disponible</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="small text-lux-greyBlue">
                @if($recentLogs->count() > 0)
                    Affichage de 1 à {{ $recentLogs->count() }} sur {{ $recentLogs->count() }} entrées
                @else
                    Aucune entrée
                @endif
            </span>
        </div>
    </section>

@endsection

@push('scripts')
<script src="https://cdn.plot.ly/plotly-2.35.2.min.js"></script>
<script>
    let currentPeriod = '24h';
    let chartInstance = null;

    function loadChart(period) {
        currentPeriod = period;
        
        fetch(`{{ route('admin.synchronization.chart-data') }}?period=${period}`, {
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateChart(data.data);
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        });
    }

    function updateChart(chartData) {
        try {
            var trace1 = {
                type: 'scatter',
                mode: 'lines',
                name: 'Synchronisations',
                x: chartData.labels,
                y: chartData.syncs,
                line: { color: '#CBAE82', width: 3 },
                fill: 'tozeroy',
                fillcolor: 'rgba(203, 174, 130, 0.1)'
            };
            
            var trace2 = {
                type: 'scatter',
                mode: 'lines+markers',
                name: 'Erreurs',
                x: chartData.labels,
                y: chartData.errors,
                line: { color: '#EF4444', width: 2 },
                marker: { size: 6, color: '#EF4444' }
            };
            
            var layout = {
                title: { text: '', font: { size: 0 } },
                xaxis: { title: '', gridcolor: '#E5E7EB' },
                yaxis: { title: 'Volume', gridcolor: '#E5E7EB' },
                margin: { t: 20, r: 20, b: 40, l: 60 },
                plot_bgcolor: '#FFFFFF',
                paper_bgcolor: '#FFFFFF',
                showlegend: true,
                legend: { x: 0, y: 1.1, orientation: 'h' }
            };
            
            var config = { responsive: true, displayModeBar: false, displaylogo: false };
            
            if (chartInstance) {
                Plotly.update('syncChart', [trace1, trace2], layout, config);
            } else {
                chartInstance = Plotly.newPlot('syncChart', [trace1, trace2], layout, config);
            }
        } catch(e) {
            console.error('Error updating chart:', e);
            document.getElementById('syncChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-lux-greyBlue"><i class="fa-solid fa-chart-line me-2"></i>Graphique indisponible</div>';
        }
    }

    window.addEventListener('load', function() {
        // Charger le graphique avec les données initiales
        const initialData = @json($chartData);
        updateChart(initialData);

        // Gestion des boutons de période
        document.querySelectorAll('.chart-period-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                
                // Mettre à jour l'apparence des boutons
                document.querySelectorAll('.chart-period-btn').forEach(b => {
                    b.style.backgroundColor = '';
                    b.style.color = '';
                    b.style.border = '';
                    b.classList.remove('fw-medium');
                    b.classList.add('text-lux-greyBlue');
                });
                
                this.style.backgroundColor = 'rgba(203, 174, 130, 0.1)';
                this.style.color = 'var(--lux-gold)';
                this.style.border = '1px solid var(--lux-gold)';
                this.classList.add('fw-medium');
                this.classList.remove('text-lux-greyBlue');
                
                // Charger les nouvelles données
                loadChart(period);
            });
        });
    });

    // Bouton Forcer la synchro
    document.getElementById('force-sync-btn').addEventListener('click', function() {
        if (!confirm('Êtes-vous sûr de vouloir forcer la synchronisation ?')) {
            return;
        }

        const btn = this;
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Synchronisation...';

        fetch('{{ route("admin.synchronization.force-sync") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            btn.disabled = false;
            btn.innerHTML = originalText;
            
            if (data.success) {
                alert('Synchronisation terminée: ' + data.results.success + ' succès, ' + data.results.errors + ' erreurs');
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            btn.disabled = false;
            btn.innerHTML = originalText;
            alert('Une erreur est survenue lors de la synchronisation');
        });
    });

</script>
@endpush
