@extends('layouts.admin')

@section('title', 'Dashboard Admin | LUXÎLES - Administration')

@push('styles')
<style>
    .glass-panel {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(203, 174, 130, 0.2);
        box-shadow: 0 4px 20px rgba(10, 26, 47, 0.05);
    }
</style>
@endpush

@section('content')
    <!-- Section 1: Main Stats (KPIs) -->
    <section id="kpi-section" class="row g-4 mb-4">
        <!-- KPI 1: Revenue -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="glass-panel p-4 rounded-xl position-relative overflow-hidden" style="height: 140px;">
                <div class="position-absolute end-0 top-0 p-3 opacity-10" style="transition: opacity 0.3s;">
                    <i class="fa-solid fa-euro-sign" style="font-size: 4rem; color: var(--lux-gold);"></i>
                </div>
                <div>
                    <p class="text-lux-gray small fw-medium text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.1em;">Revenus (Mois)</p>
                    <h3 class="h3 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ number_format($currentMonthRevenue ?? 0, 0, ',', ' ') }} €</h3>
                    <div class="d-flex align-items-center small fw-medium {{ ($revenueGrowth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fa-solid fa-arrow-trend-{{ ($revenueGrowth ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>
                        <span style="font-size: 0.75rem;">{{ ($revenueGrowth ?? 0) >= 0 ? '+' : '' }}{{ number_format($revenueGrowth ?? 0, 1) }}% vs M-1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 2: Occupancy -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="glass-panel p-4 rounded-xl position-relative overflow-hidden" style="height: 140px;">
                <div class="position-absolute end-0 top-0 p-3 opacity-10" style="transition: opacity 0.3s;">
                    <i class="fa-solid fa-bed" style="font-size: 4rem; color: var(--lux-gold);"></i>
                </div>
                <div>
                    <p class="text-lux-gray small fw-medium text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.1em;">Taux d'Occupation</p>
                    <h3 class="h3 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ number_format($occupancyRate ?? 0, 0) }}%</h3>
                    <div class="d-flex align-items-center small fw-medium {{ ($occupancyGrowth ?? 0) >= 0 ? 'text-success' : 'text-danger' }}">
                        <i class="fa-solid fa-arrow-trend-{{ ($occupancyGrowth ?? 0) >= 0 ? 'up' : 'down' }} me-1"></i>
                        <span style="font-size: 0.75rem;">{{ ($occupancyGrowth ?? 0) >= 0 ? '+' : '' }}{{ number_format($occupancyGrowth ?? 0, 1) }}% vs M-1</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 3: New Bookings -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="glass-panel p-4 rounded-xl position-relative overflow-hidden" style="height: 140px;">
                <div class="position-absolute end-0 top-0 p-3 opacity-10" style="transition: opacity 0.3s;">
                    <i class="fa-solid fa-clipboard-check" style="font-size: 4rem; color: var(--lux-gold);"></i>
                </div>
                <div>
                    <p class="text-lux-gray small fw-medium text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.1em;">Réservations</p>
                    <h3 class="h3 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ $currentMonthBookings ?? 0 }}</h3>
                    <div class="d-flex align-items-center small fw-medium text-lux-gray">
                        @php
                            $bookingDiff = ($currentMonthBookings ?? 0) - ($lastMonthBookings ?? 0);
                        @endphp
                        @if($bookingDiff > 0)
                            <i class="fa-solid fa-arrow-trend-up me-1 text-success"></i>
                            <span class="text-success" style="font-size: 0.75rem;">+{{ $bookingDiff }} vs M-1</span>
                        @elseif($bookingDiff < 0)
                            <i class="fa-solid fa-arrow-trend-down me-1 text-danger"></i>
                            <span class="text-danger" style="font-size: 0.75rem;">{{ $bookingDiff }} vs M-1</span>
                        @else
                            <i class="fa-solid fa-minus me-1"></i>
                            <span style="font-size: 0.75rem;">Stable vs M-1</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI 4: Pending Actions -->
        <div class="col-12 col-md-6 col-lg-3">
            <div class="glass-panel p-4 rounded-xl position-relative overflow-hidden border-start border-4 border-lux-gold" style="height: 140px;">
                <div class="position-absolute end-0 top-0 p-3 opacity-10" style="transition: opacity 0.3s;">
                    <i class="fa-regular fa-bell" style="font-size: 4rem; color: var(--lux-gold);"></i>
                </div>
                <div>
                    <p class="text-lux-gray small fw-medium text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.1em;">À Traiter</p>
                    <h3 class="h3 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ $pendingActions ?? 0 }}</h3>
                    <div class="d-flex align-items-center small fw-medium text-warning">
                        <span style="font-size: 0.75rem;">{{ ($pendingActions ?? 0) > 0 ? 'Demandes en attente' : 'Aucune demande' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 2: Charts Area -->
    <section id="charts-section" class="row g-4 mb-4">
        <!-- Main Chart: Revenue & Occupancy -->
        <div class="col-12 col-lg-8">
            <div class="glass-panel p-4 rounded-xl">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">Performance Financière</h2>
                    <div class="d-flex gap-2">
                        <button id="btn-chart-month" class="btn btn-sm px-3 py-1 text-xs fw-medium rounded chart-period-btn active" data-period="month" style="background-color: var(--lux-dark-blue); color: white; border: none;">Mois</button>
                        <button id="btn-chart-year" class="btn btn-sm px-3 py-1 text-xs fw-medium rounded chart-period-btn" data-period="year" style="background-color: white; color: var(--lux-gray); border: 1px solid rgba(138, 150, 166, 0.2);">Année</button>
                    </div>
                </div>
                <div id="revenueChart" style="height: 300px; width: 100%;"></div>
            </div>
        </div>

        <!-- Secondary Chart: Occupancy Donut -->
        <div class="col-12 col-lg-4">
            <div class="glass-panel p-4 rounded-xl d-flex flex-column h-100">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">Répartition</h2>
                    <button class="btn btn-link p-0 text-lux-gray" style="border: none;"><i class="fa-solid fa-ellipsis"></i></button>
                </div>
                <div id="occupancyPieChart" style="height: 250px; width: 100%; flex: 1;"></div>
                    <div class="row g-3 mt-3 text-center">
                    <div class="col-6">
                        <p class="small text-lux-gray mb-1">Direct</p>
                        <p class="fw-bold text-lux-dark-blue mb-0">{{ number_format($directPercentage ?? 0, 0) }}%</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-lux-gray mb-1">Partenaires</p>
                        <p class="fw-bold text-lux-dark-blue mb-0">{{ number_format($partnerPercentage ?? 0, 0) }}%</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section 3: Recent Bookings & Alerts -->
    <section id="bottom-section" class="row g-4">
        <!-- Recent Reservations Table -->
        <div class="col-12 col-lg-8">
            <div class="glass-panel rounded-xl overflow-hidden">
                <div class="p-4 border-bottom d-flex align-items-center justify-content-between" style="border-color: rgba(138, 150, 166, 0.1);">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">Dernières Réservations</h2>
                    <a href="#" class="small text-lux-gold fw-medium text-decoration-none">Tout voir</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="small text-lux-gray text-uppercase bg-white" style="font-size: 0.7rem; border-bottom: 1px solid rgba(138, 150, 166, 0.1);">
                            <tr>
                                <th class="px-4 py-3 fw-medium">Client</th>
                                <th class="px-4 py-3 fw-medium">Villa</th>
                                <th class="px-4 py-3 fw-medium">Dates</th>
                                <th class="px-4 py-3 fw-medium">Montant</th>
                                <th class="px-4 py-3 fw-medium">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y" style="border-color: rgba(138, 150, 166, 0.1);">
                            @forelse($recentReservations ?? [] as $reservation)
                                @php
                                    $initials = strtoupper(substr($reservation->guest_first_name, 0, 1) . substr($reservation->guest_last_name, 0, 1));
                                    $statusColors = [
                                        'confirmed' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Confirmé'],
                                        'deposit_paid' => ['bg' => '#dbeafe', 'text' => '#1e40af', 'label' => 'Arrhes payées'],
                                        'fully_paid' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Payé'],
                                        'pending' => ['bg' => '#fef3c7', 'text' => '#92400e', 'label' => 'En attente'],
                                        'cancelled' => ['bg' => '#fee2e2', 'text' => '#991b1b', 'label' => 'Annulé'],
                                        'completed' => ['bg' => '#d1fae5', 'text' => '#065f46', 'label' => 'Terminé'],
                                    ];
                                    $statusInfo = $statusColors[$reservation->status] ?? $statusColors['pending'];
                                @endphp
                                <tr class="hover:bg-white/50" style="transition: background-color 0.3s;">
                                    <td class="px-4 py-3 d-flex align-items-center gap-3">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center text-lux-dark-blue fw-bold" style="width: 32px; height: 32px; background-color: rgba(10, 26, 47, 0.1); font-size: 0.75rem;">{{ $initials }}</div>
                                        <span class="fw-medium text-lux-dark-blue">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-lux-gray">{{ $reservation->villa->name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 text-lux-gray">
                                        {{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M') }}
                                    </td>
                                    <td class="px-4 py-3 fw-medium text-lux-dark-blue">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</td>
                                    <td class="px-4 py-3">
                                        <span class="badge rounded-pill px-2 py-1 small fw-medium" style="background-color: {{ $statusInfo['bg'] }}; color: {{ $statusInfo['text'] }};">{{ $statusInfo['label'] }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-muted">
                                        <i class="fas fa-inbox d-block mb-2" style="font-size: 2rem; opacity: 0.3;"></i>
                                        <span class="small">Aucune réservation récente</span>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Synchronization Alerts -->
        <div class="col-12 col-lg-4">
            <div class="glass-panel p-4 rounded-xl d-flex flex-column h-100">
                <h2 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">Alertes Synchronisation</h2>
                
                <div class="d-flex flex-column gap-3 flex-grow-1">
                    @forelse($syncAlerts ?? [] as $alert)
                        @php
                            $villa = \App\Models\Villa::find($alert->villa_id);
                            $isError = $alert->status === 'error';
                            $bgColor = $isError ? '#fef2f2' : '#fffbeb';
                            $borderColor = $isError ? '#fee2e2' : '#fef3c7';
                            $textColor = $isError ? 'text-danger' : 'text-warning';
                            $icon = $isError ? 'fa-circle-exclamation' : 'fa-triangle-exclamation';
                            $title = $isError ? 'Erreur de synchronisation' : 'Conflit de synchronisation';
                        @endphp
                        <div class="d-flex gap-3 p-3 rounded" style="background-color: {{ $bgColor }}; border: 1px solid {{ $borderColor }};">
                            <div class="flex-shrink-0 mt-1">
                                <i class="fa-solid {{ $icon }} {{ $textColor }}"></i>
                            </div>
                            <div>
                                <h4 class="small fw-bold {{ $textColor }} mb-1">{{ $title }}</h4>
                                <p class="small text-muted mb-2" style="font-size: 0.75rem;">
                                    {{ $villa->name ?? 'Villa #' . $alert->villa_id }} : {{ $alert->error_message ?? 'Problème de synchronisation' }} ({{ ucfirst($alert->platform) }})
                                </p>
                                <button class="btn btn-link p-0 small fw-medium {{ $textColor }} text-decoration-underline" style="font-size: 0.75rem;">Résoudre</button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="fa-solid fa-check-circle text-success d-block mb-2" style="font-size: 2rem; opacity: 0.5;"></i>
                            <p class="small text-muted mb-0">Aucune alerte de synchronisation</p>
                        </div>
                    @endforelse

                    <!-- Sync Status -->
                    @if(isset($lastSync) && $lastSync)
                        @php
                            $lastSyncDate = \Carbon\Carbon::parse($lastSync->last_sync_at);
                            $syncAge = $lastSyncDate->diffForHumans();
                            $totalSyncs = DB::table('platform_syncs')->count();
                            $syncedCount = DB::table('platform_syncs')->where('status', 'synced')->count();
                            $syncPercentage = $totalSyncs > 0 ? ($syncedCount / $totalSyncs) * 100 : 0;
                        @endphp
                        <div class="mt-auto pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1);">
                            <div class="d-flex align-items-center justify-content-between small text-lux-gray mb-2">
                                <span>Dernière synchro :</span>
                                <span class="fw-medium text-lux-dark-blue">{{ $syncAge }}</span>
                            </div>
                            <div class="w-100 rounded-pill" style="background-color: rgba(138, 150, 166, 0.2); height: 6px;">
                                <div class="bg-success rounded-pill" style="height: 6px; width: {{ $syncPercentage }}%;"></div>
                            </div>
                            <p class="text-end small text-lux-gray mt-1 mb-0" style="font-size: 0.65rem;">{{ number_format($syncPercentage, 0) }}% synchronisé</p>
                        </div>
                    @else
                        <div class="mt-auto pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1);">
                            <div class="d-flex align-items-center justify-content-between small text-lux-gray mb-2">
                                <span>Dernière synchro :</span>
                                <span class="fw-medium text-lux-dark-blue">Jamais</span>
                            </div>
                            <div class="w-100 rounded-pill" style="background-color: rgba(138, 150, 166, 0.2); height: 6px;">
                                <div class="bg-secondary rounded-pill" style="height: 6px; width: 0%;"></div>
                            </div>
                            <p class="text-end small text-lux-gray mt-1 mb-0" style="font-size: 0.65rem;">0% synchronisé</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script src="https://cdn.plot.ly/plotly-2.35.2.min.js"></script>
<script>
    window.addEventListener('load', function() {
        try {
            // Colors from palette
            const luxBlue = '#0A1A2F';
            const luxGold = '#CBAE82';
            const luxGrey = '#8A96A6';
            const luxWhite = '#F8F8F6';

            // Revenue Chart (Bar & Line Combo) - Données dynamiques
            const revenueTrace = {
                x: @json($months ?? []),
                y: @json($revenueData ?? []),
                type: 'bar',
                name: 'Revenus',
                marker: { color: luxGold }
            };

            const occupancyTrace = {
                x: @json($months ?? []),
                y: @json($occupancyData ?? []),
                type: 'scatter',
                mode: 'lines+markers',
                name: 'Taux Occupation (%)',
                yaxis: 'y2',
                line: { color: luxBlue, width: 3 },
                marker: { size: 6 }
            };

            const revenueLayout = {
                margin: { t: 20, r: 60, b: 40, l: 60 },
                plot_bgcolor: luxWhite,
                paper_bgcolor: luxWhite,
                showlegend: true,
                legend: { x: 0, y: 1.15, orientation: 'h' },
                xaxis: { title: '', gridcolor: luxGrey + '20' },
                yaxis: { 
                    title: 'Revenus (€)', 
                    gridcolor: luxGrey + '20',
                    tickformat: ',.0f'
                },
                yaxis2: {
                    title: 'Occupation (%)',
                    overlaying: 'y',
                    side: 'right',
                    range: [0, 100]
                }
            };

            const revenueConfig = {
                responsive: true,
                displayModeBar: false,
                displaylogo: false
            };

            // Variables globales pour les données
            const monthRevenueData = @json($revenueData ?? []);
            const monthOccupancyData = @json($occupancyData ?? []);
            const monthLabels = @json($months ?? []);
            const yearRevenueData = @json($revenueDataYear ?? []);
            const yearOccupancyData = @json($occupancyDataYear ?? []);
            const yearLabels = @json($years ?? []);
            
            let currentPeriod = 'month';

            // Fonction pour mettre à jour le graphique
            function updateChart(period) {
                currentPeriod = period;
                const isMonth = period === 'month';
                
                const revenueData = isMonth ? monthRevenueData : yearRevenueData;
                const occupancyData = isMonth ? monthOccupancyData : yearOccupancyData;
                const labels = isMonth ? monthLabels : yearLabels;
                
                const revenueTrace = {
                    x: labels,
                    y: revenueData,
                    type: 'bar',
                    name: 'Revenus',
                    marker: { color: luxGold }
                };

                const occupancyTrace = {
                    x: labels,
                    y: occupancyData,
                    type: 'scatter',
                    mode: 'lines+markers',
                    name: 'Taux Occupation (%)',
                    yaxis: 'y2',
                    line: { color: luxBlue, width: 3 },
                    marker: { size: 6 }
                };

                Plotly.newPlot('revenueChart', [revenueTrace, occupancyTrace], revenueLayout, revenueConfig);
                
                // Mettre à jour les styles des boutons
                document.querySelectorAll('.chart-period-btn').forEach(btn => {
                    if (btn.dataset.period === period) {
                        btn.style.backgroundColor = 'var(--lux-dark-blue)';
                        btn.style.color = 'white';
                        btn.style.border = 'none';
                        btn.classList.add('active');
                    } else {
                        btn.style.backgroundColor = 'white';
                        btn.style.color = 'var(--lux-gray)';
                        btn.style.border = '1px solid rgba(138, 150, 166, 0.2)';
                        btn.classList.remove('active');
                    }
                });
            }

            // Initialiser le graphique avec les données mensuelles
            Plotly.newPlot('revenueChart', [revenueTrace, occupancyTrace], revenueLayout, revenueConfig);

            // Gérer les clics sur les boutons
            document.getElementById('btn-chart-month')?.addEventListener('click', function() {
                updateChart('month');
            });
            
            document.getElementById('btn-chart-year')?.addEventListener('click', function() {
                updateChart('year');
            });

            // Occupancy Pie Chart - Données dynamiques
            const pieData = [{
                values: [{{ $directPercentage ?? 0 }}, {{ $partnerPercentage ?? 0 }}],
                labels: ['Réservations Directes', 'Partenaires (Airbnb, Booking)'],
                type: 'pie',
                marker: {
                    colors: [luxGold, luxBlue]
                },
                textinfo: 'label+percent',
                textposition: 'inside',
                hovertemplate: '<b>%{label}</b><br>%{percent}<extra></extra>'
            }];

            const pieLayout = {
                margin: { t: 10, r: 10, b: 10, l: 10 },
                plot_bgcolor: luxWhite,
                paper_bgcolor: luxWhite,
                showlegend: false,
                font: { size: 11 }
            };

            const pieConfig = {
                responsive: true,
                displayModeBar: false,
                displaylogo: false
            };

            Plotly.newPlot('occupancyPieChart', pieData, pieLayout, pieConfig);

        } catch(e) {
            console.error('Chart error:', e);
            document.getElementById('revenueChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-lux-gray small">Erreur de chargement du graphique</div>';
            document.getElementById('occupancyPieChart').innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-lux-gray small">Erreur de chargement</div>';
        }
    });
</script>
@endpush
