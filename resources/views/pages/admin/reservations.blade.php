@extends('layouts.admin')

@section('title', 'Gestion des Réservations | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Réservations</span>
@endsection

@section('content')
    <!-- Top Header Section -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Gestion des Réservations
            </h1>
        </div>
        <div class="d-flex align-items-center gap-3 mt-3 mt-md-0">
            <div class="position-relative d-none d-md-block">
                <i class="fa-solid fa-magnifying-glass position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-greyBlue);"></i>
                <input type="text" id="search-input" name="search" value="{{ request('search') }}" placeholder="Rechercher une réservation..." class="form-control ps-5" style="width: 300px; border-color: rgba(0,0,0,0.1);">
            </div>
            <button class="btn position-relative text-lux-dark-blue border-0 bg-transparent p-0 d-none" style="text-decoration: none; display: none !important;">
                <i class="fa-regular fa-bell fs-5"></i>
                <span class="position-absolute top-0 end-0 translate-middle badge rounded-pill bg-danger" style="width: 8px; height: 8px; padding: 0;"></span>
            </button>
            {{-- TODO: Développer le formulaire de création manuelle de réservation
            <a href="{{ route('admin.reservations.create') }}" class="btn btn-lux-primary d-flex align-items-center gap-2">
                <i class="fa-solid fa-plus"></i>
                <span>Nouvelle Réservation</span>
            </a>
            --}}

        </div>
    </div>

    <!-- Filters Section -->
    <section class="bg-white rounded shadow-sm p-4 mb-4 border" style="border-color: rgba(0,0,0,0.05) !important;">
        <form method="GET" action="{{ route('admin.reservations') }}" id="filters-form">
            <div class="d-flex flex-column flex-md-row align-items-end gap-3">
                <div class="row g-3 flex-fill">
                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Période</label>
                        <div class="position-relative">
                            <i class="fa-regular fa-calendar position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="period" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Toutes périodes</option>
                                <option value="this_month" {{ request('period') == 'this_month' ? 'selected' : '' }}>Ce mois-ci</option>
                                <option value="last_month" {{ request('period') == 'last_month' ? 'selected' : '' }}>Mois dernier</option>
                                <option value="this_year" {{ request('period') == 'this_year' ? 'selected' : '' }}>Cette année</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-filter position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="status" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Tous les statuts</option>
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                <option value="fully_paid" {{ request('status') == 'fully_paid' ? 'selected' : '' }}>Payée</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Source</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-globe position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="source" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Toutes sources</option>
                                <option value="direct" {{ request('source') == 'direct' ? 'selected' : '' }}>Site Web</option>
                                <option value="airbnb" {{ request('source') == 'airbnb' ? 'selected' : '' }}>Airbnb</option>
                                <option value="booking" {{ request('source') == 'booking' ? 'selected' : '' }}>Booking</option>
                                <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manuelle</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-1" style="font-size: 0.7rem; letter-spacing: 0.05em;">Villa</label>
                        <div class="position-relative">
                            <i class="fa-solid fa-house position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: var(--lux-gold); z-index: 10;"></i>
                            <select name="villa_id" class="form-select ps-5" style="background-color: #f8f9fa; border-color: rgba(0,0,0,0.1); cursor: pointer;">
                                <option value="">Toutes les villas</option>
                                @foreach($villas as $villa)
                                    <option value="{{ $villa->id }}" {{ request('villa_id') == $villa->id ? 'selected' : '' }}>{{ $villa->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="d-flex align-items-center gap-2 border-start border-gray-200 ps-4">
                    <button type="button" class="btn p-2 rounded view-toggle-btn active" data-view="table" title="Vue Tableau" style="background-color: var(--lux-dark-blue); color: white; border: none;">
                        <i class="fa-solid fa-list"></i>
                    </button>
                    <button type="button" class="btn p-2 rounded view-toggle-btn" data-view="cards" title="Vue Cartes" style="background-color: white; border: 1px solid rgba(0,0,0,0.1); color: var(--lux-greyBlue);">
                        <i class="fa-solid fa-grip"></i>
                    </button>
                </div>
            </div>
        </form>
    </section>

    <!-- Reservations Table View -->
    <section id="table-view" class="bg-white rounded shadow-sm border overflow-hidden mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="bg-light border-bottom" style="background-color: #f8f9fa !important;">
                    <tr>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">ID / Date</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Client</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Villa</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Séjour</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Montant</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem; letter-spacing: 0.05em;">Statut</th>
                        <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue text-end" style="font-size: 0.7rem; letter-spacing: 0.05em;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservations as $reservation)
                        <tr class="reservation-row" style="transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                            <td class="px-4 py-3">
                                <div class="fw-medium text-lux-dark-blue">#{{ $reservation->reservation_number }}</div>
                                <div class="small text-lux-greyBlue">{{ $reservation->created_at->format('d M Y') }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="rounded-circle overflow-hidden" style="width: 32px; height: 32px; background-color: var(--lux-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                        {{ strtoupper(substr($reservation->guest_first_name ?? '', 0, 1) . substr($reservation->guest_last_name ?? '', 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="small fw-medium text-lux-dark-blue">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</div>
                                        <div class="small text-lux-greyBlue">{{ $reservation->guest_email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="small fw-medium text-lux-dark-blue">{{ $reservation->villa->name ?? 'N/A' }}</div>
                                <div class="small text-lux-greyBlue">{{ $reservation->villa->island->name ?? 'N/A' }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="small text-lux-dark-blue">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M') }}</div>
                                <div class="small text-lux-greyBlue">{{ $reservation->number_of_nights }} nuits</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="fw-medium text-lux-dark-blue">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</div>
                                @php
                                    $paidAmount = $reservation->payments()->where('status', 'completed')->sum('amount');
                                    $paymentStatus = '';
                                    $paymentColor = '';
                                    if ($paidAmount >= $reservation->total_price) {
                                        $paymentStatus = 'Payé';
                                        $paymentColor = 'text-success';
                                    } elseif ($paidAmount >= $reservation->deposit_amount) {
                                        $paymentStatus = 'Acompte versé';
                                        $paymentColor = 'text-warning';
                                    } else {
                                        $paymentStatus = 'Non payé';
                                        $paymentColor = 'text-danger';
                                    }
                                @endphp
                                <div class="small {{ $paymentColor }}">{{ $paymentStatus }}</div>
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusConfig = [
                                        'confirmed' => ['label' => 'Confirmée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                        'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                        'cancelled' => ['label' => 'Annulée', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                        'fully_paid' => ['label' => 'Payée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                        'deposit_paid' => ['label' => 'Acompte payé', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                                    ];
                                    $status = $statusConfig[$reservation->status] ?? ['label' => ucfirst($reservation->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                                @endphp
                                <span class="badge rounded-pill px-3 py-1 small fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                            </td>
                            <td class="px-4 py-3 text-end">
                                <div class="d-flex align-items-center justify-content-end gap-2 reservation-actions" style="opacity: 0; transition: opacity 0.2s;">
                                    <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-sm btn-link text-lux-greyBlue p-2 border-0" style="text-decoration: none;" title="Modifier">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                    @if($reservation->status != 'cancelled')
                                        <button class="btn btn-sm btn-link text-lux-greyBlue p-2 border-0 cancel-reservation-btn" style="text-decoration: none;" title="Annuler" data-reservation-id="{{ $reservation->id }}" data-reservation-number="{{ $reservation->reservation_number }}">
                                            <i class="fa-solid fa-ban"></i>
                                        </button>
                                    @endif
                                    <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm btn-link text-lux-gold p-2 border-0" style="text-decoration: none;" title="Voir détails">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-lux-greyBlue">
                                <i class="fa-regular fa-calendar-xmark fs-1 mb-3 d-block"></i>
                                <p class="mb-0">Aucune réservation trouvée</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($reservations->hasPages())
            <div class="px-4 py-3 border-top bg-light d-flex align-items-center justify-content-between">
                <span class="small text-lux-greyBlue">
                    Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations
                </span>
                <div class="d-flex align-items-center gap-2">
                    {{ $reservations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </section>

    <!-- Reservations Cards View -->
    <section id="cards-view" class="d-none">
        <div class="row g-4">
            @forelse($reservations as $reservation)
                <div class="col-md-6 col-lg-4">
                    <div class="bg-white rounded shadow-sm border p-4 h-100" style="border-color: rgba(0,0,0,0.05) !important;">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="fw-medium text-lux-dark-blue mb-1">#{{ $reservation->reservation_number }}</h5>
                                <p class="small text-lux-greyBlue mb-0">{{ $reservation->created_at->format('d M Y') }}</p>
                            </div>
                            @php
                                $statusConfig = [
                                    'confirmed' => ['label' => 'Confirmée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                    'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                    'cancelled' => ['label' => 'Annulée', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                    'fully_paid' => ['label' => 'Payée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                    'deposit_paid' => ['label' => 'Acompte payé', 'class' => 'bg-info bg-opacity-10 text-info border border-info border-opacity-25'],
                                ];
                                $status = $statusConfig[$reservation->status] ?? ['label' => ucfirst($reservation->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-1 small fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </div>
                        
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div class="rounded-circle overflow-hidden" style="width: 40px; height: 40px; background-color: var(--lux-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                {{ strtoupper(substr($reservation->guest_first_name ?? '', 0, 1) . substr($reservation->guest_last_name ?? '', 0, 1)) }}
                            </div>
                            <div>
                                <div class="fw-medium text-lux-dark-blue">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</div>
                                <div class="small text-lux-greyBlue">{{ $reservation->guest_email }}</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-lux-greyBlue mb-1">Villa</div>
                            <div class="fw-medium text-lux-dark-blue">{{ $reservation->villa->name ?? 'N/A' }}</div>
                            <div class="small text-lux-greyBlue">{{ $reservation->villa->island->name ?? 'N/A' }}</div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-lux-greyBlue mb-1">Séjour</div>
                            <div class="fw-medium text-lux-dark-blue">{{ \Carbon\Carbon::parse($reservation->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($reservation->check_out_date)->format('d M') }}</div>
                            <div class="small text-lux-greyBlue">{{ $reservation->number_of_nights }} nuits</div>
                        </div>

                        <div class="mb-3 pb-3 border-bottom">
                            <div class="small text-lux-greyBlue mb-1">Montant</div>
                            <div class="fw-bold text-lux-dark-blue fs-5">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</div>
                        </div>

                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-sm p-2 flex-fill d-flex align-items-center justify-content-center" style="background-color: var(--lux-gold); color: white; text-decoration: none; border: none;" title="Voir détails">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.reservations.edit', $reservation->id) }}" class="btn btn-sm btn-outline-secondary">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            @if($reservation->status != 'cancelled')
                                <button class="btn btn-sm btn-outline-danger cancel-reservation-btn" data-reservation-id="{{ $reservation->id }}" data-reservation-number="{{ $reservation->reservation_number }}">
                                    <i class="fa-solid fa-ban"></i>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="bg-white rounded shadow-sm border p-5 text-center" style="border-color: rgba(0,0,0,0.05) !important;">
                        <i class="fa-regular fa-calendar-xmark fs-1 mb-3 d-block text-lux-greyBlue"></i>
                        <p class="mb-0 text-lux-greyBlue">Aucune réservation trouvée</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination for Cards View -->
        @if($reservations->hasPages())
            <div class="mt-4 d-flex align-items-center justify-content-between">
                <span class="small text-lux-greyBlue">
                    Affichage de {{ $reservations->firstItem() }} à {{ $reservations->lastItem() }} sur {{ $reservations->total() }} réservations
                </span>
                <div class="d-flex align-items-center gap-2">
                    {{ $reservations->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </section>

    <!-- Variant C: Mix Table + Detail Panel -->
    <section id="mix-view" class="d-none">
        <div class="row g-4">
            <!-- Left: Compact Table -->
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm border overflow-hidden" style="border-color: rgba(0,0,0,0.05) !important;">
                    <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between">
                        <h3 class="h6 fw-medium text-lux-dark-blue mb-0">Réservations récentes</h3>
                        <a href="{{ route('admin.reservations') }}" class="small text-lux-gold text-decoration-none">Voir tout</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="bg-light border-bottom" style="background-color: #f8f9fa !important;">
                                <tr>
                                    <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem;">ID</th>
                                    <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem;">Client</th>
                                    <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem;">Villa</th>
                                    <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem;">Montant</th>
                                    <th class="px-4 py-3 small text-uppercase fw-medium text-lux-greyBlue" style="font-size: 0.7rem;">Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($reservations->take(10) as $reservation)
                                    <tr class="reservation-row-mix cursor-pointer" style="transition: background-color 0.2s;" data-reservation-id="{{ $reservation->id }}" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.03)'" onmouseout="this.style.backgroundColor='transparent'">
                                        <td class="px-4 py-3 fw-medium text-lux-dark-blue">#{{ $reservation->reservation_number }}</td>
                                        <td class="px-4 py-3 text-lux-dark-blue">{{ $reservation->guest_first_name }} {{ $reservation->guest_last_name }}</td>
                                        <td class="px-4 py-3 text-lux-greyBlue">{{ $reservation->villa->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 fw-medium">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusConfig = [
                                                    'confirmed' => ['label' => 'Confirmée', 'class' => 'bg-success bg-opacity-10 text-success'],
                                                    'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning'],
                                                    'cancelled' => ['label' => 'Annulée', 'class' => 'bg-danger bg-opacity-10 text-danger'],
                                                    'fully_paid' => ['label' => 'Payée', 'class' => 'bg-success bg-opacity-10 text-success'],
                                                ];
                                                $status = $statusConfig[$reservation->status] ?? ['label' => ucfirst($reservation->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                                            @endphp
                                            <span class="badge rounded-pill px-2 py-1 small {{ $status['class'] }}">{{ $status['label'] }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-lux-greyBlue">
                                            <p class="mb-0">Aucune réservation trouvée</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right: Detail Card -->
            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm border p-4 sticky-top" style="border-color: rgba(0,0,0,0.05) !important; top: 20px;" id="detail-panel">
                    @php
                        $selectedReservation = $reservations->first();
                    @endphp
                    @if($selectedReservation)
                        <div class="d-flex align-items-start justify-content-between mb-4">
                            <div>
                                <h3 class="h6 fw-medium text-lux-dark-blue mb-1">Réservation #{{ $selectedReservation->reservation_number }}</h3>
                                <p class="small text-lux-greyBlue mb-0">Créée le {{ $selectedReservation->created_at->format('d M Y') }}</p>
                            </div>
                            @php
                                $statusConfig = [
                                    'confirmed' => ['label' => 'Confirmée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                    'pending' => ['label' => 'En attente', 'class' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25'],
                                    'cancelled' => ['label' => 'Annulée', 'class' => 'bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25'],
                                    'fully_paid' => ['label' => 'Payée', 'class' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-25'],
                                ];
                                $status = $statusConfig[$selectedReservation->status] ?? ['label' => ucfirst($selectedReservation->status), 'class' => 'bg-secondary bg-opacity-10 text-secondary'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-1 small fw-medium {{ $status['class'] }}">{{ $status['label'] }}</span>
                        </div>

                        <div class="mb-4">
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px; background-color: var(--lux-gold); display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                    {{ strtoupper(substr($selectedReservation->guest_first_name ?? '', 0, 1) . substr($selectedReservation->guest_last_name ?? '', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="fw-medium text-lux-dark-blue mb-0">{{ $selectedReservation->guest_first_name }} {{ $selectedReservation->guest_last_name }}</p>
                                    <p class="small text-lux-greyBlue mb-0">{{ $selectedReservation->guest_email }}</p>
                                    @if($selectedReservation->guest_phone)
                                        <p class="small text-lux-greyBlue mb-0">{{ $selectedReservation->guest_phone }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="pt-3 border-top">
                                <div class="d-flex align-items-center gap-3 mb-2 small">
                                    <i class="fa-solid fa-house text-lux-gold" style="width: 20px;"></i>
                                    <span class="text-lux-dark-blue fw-medium">{{ $selectedReservation->villa->name ?? 'N/A' }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 mb-2 small">
                                    <i class="fa-regular fa-calendar text-lux-gold" style="width: 20px;"></i>
                                    <span class="text-lux-greyBlue">{{ \Carbon\Carbon::parse($selectedReservation->check_in_date)->format('d M') }} - {{ \Carbon\Carbon::parse($selectedReservation->check_out_date)->format('d M Y') }}</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 mb-2 small">
                                    <i class="fa-regular fa-moon text-lux-gold" style="width: 20px;"></i>
                                    <span class="text-lux-greyBlue">{{ $selectedReservation->number_of_nights }} nuits</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 mb-2 small">
                                    <i class="fa-regular fa-user text-lux-gold" style="width: 20px;"></i>
                                    <span class="text-lux-greyBlue">{{ $selectedReservation->number_of_guests }} personnes</span>
                                </div>
                            </div>

                            <div class="pt-3 border-top mt-3">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-lux-greyBlue">Sous-total</span>
                                    <span class="small fw-medium">{{ number_format($selectedReservation->base_price, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-lux-greyBlue">Frais service</span>
                                    <span class="small fw-medium">{{ number_format($selectedReservation->service_fee, 2, ',', ' ') }} €</span>
                                </div>
                                @if($selectedReservation->vat_amount > 0)
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-lux-greyBlue">TVA</span>
                                    <span class="small fw-medium">{{ number_format($selectedReservation->vat_amount, 2, ',', ' ') }} €</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="small text-lux-greyBlue">Taxe séjour</span>
                                    <span class="small fw-medium">{{ number_format($selectedReservation->tourist_tax, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between pt-3 border-top">
                                    <span class="fw-medium text-lux-dark-blue">Total</span>
                                    <span class="fs-5 fw-semibold text-lux-gold">{{ number_format($selectedReservation->total_price, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.reservations.edit', $selectedReservation->id) }}" class="btn btn-lux-primary w-100">
                                <i class="fa-solid fa-pen me-2"></i>Modifier
                            </a>
                            @if($selectedReservation->status != 'cancelled')
                                <button class="btn btn-outline-danger w-100 cancel-reservation-btn" data-reservation-id="{{ $selectedReservation->id }}" data-reservation-number="{{ $selectedReservation->reservation_number }}">
                                    <i class="fa-solid fa-ban me-2"></i>Annuler réservation
                                </button>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-5 text-lux-greyBlue">
                            <p class="mb-0">Sélectionnez une réservation</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <style>
        .reservation-row:hover .reservation-actions {
            opacity: 1 !important;
        }
        .form-select:focus, .form-control:focus {
            border-color: var(--lux-gold) !important;
            box-shadow: 0 0 0 0.2rem rgba(203, 174, 130, 0.25) !important;
        }
    </style>

    <!-- Modal de confirmation d'annulation -->
    <div class="modal fade" id="cancelReservationModal" tabindex="-1" aria-labelledby="cancelReservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelReservationModalLabel">Annuler la réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="cancelReservationForm" method="POST">
                    @csrf
                    <div class="modal-body">
                        <p>Êtes-vous sûr de vouloir annuler la réservation <strong id="reservationNumber"></strong> ?</p>
                        <div class="mb-3">
                            <label for="cancellation_reason" class="form-label">Raison de l'annulation (optionnel)</label>
                            <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" placeholder="Ex: Demande du client"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button type="submit" class="btn btn-danger">Confirmer l'annulation</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Auto-submit form on filter change
            const filterSelects = document.querySelectorAll('#filters-form select');
            filterSelects.forEach(select => {
                select.addEventListener('change', function() {
                    document.getElementById('filters-form').submit();
                });
            });

            // Search input with debounce
            const searchInput = document.getElementById('search-input');
            let searchTimeout;
            if (searchInput) {
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(function() {
                        document.getElementById('filters-form').submit();
                    }, 500);
                });
            }

            // Gestion de l'annulation
            const cancelModalEl = document.getElementById('cancelReservationModal');
            let cancelModal = null;
            if (cancelModalEl) {
                cancelModal = new bootstrap.Modal(cancelModalEl);
            }
            const cancelForm = document.getElementById('cancelReservationForm');
            const reservationNumberSpan = document.getElementById('reservationNumber');

            // Utiliser la délégation d'événements pour les boutons créés dynamiquement ou présents
            document.addEventListener('click', function(e) {
                const button = e.target.closest('.cancel-reservation-btn');
                if (button) {
                    const reservationId = button.getAttribute('data-reservation-id');
                    const reservationNumber = button.getAttribute('data-reservation-number');
                    
                    if (reservationNumberSpan) reservationNumberSpan.textContent = reservationNumber;
                    
                    // Construction correcte de l'URL en utilisant la route de base js
                    // Note: Idéalement on passerait l'URL complète dans un attribut data-url
                    if (cancelForm) {
                        // On utilise une URL générique qu'on remplace
                        const baseUrl = "{{ route('admin.reservations.cancel', ['id' => ':id']) }}";
                        cancelForm.action = baseUrl.replace(':id', reservationId);
                    }
                    
                    if (cancelModal) cancelModal.show();
                }
            });

            // Gestion du basculement entre vue tableau et vue cartes
            const viewToggleButtons = document.querySelectorAll('.view-toggle-btn');
            const tableView = document.getElementById('table-view');
            const cardsView = document.getElementById('cards-view');
            const mixView = document.getElementById('mix-view');

            viewToggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const view = this.getAttribute('data-view');
                    
                    // Mettre à jour les boutons actifs
                    viewToggleButtons.forEach(btn => {
                        if (btn === this) {
                            btn.classList.add('active');
                            btn.style.backgroundColor = 'var(--lux-dark-blue)';
                            btn.style.color = 'white';
                            btn.style.border = 'none';
                        } else {
                            btn.classList.remove('active');
                            btn.style.backgroundColor = 'white';
                            btn.style.color = 'var(--lux-greyBlue)';
                            btn.style.border = '1px solid rgba(0,0,0,0.1)';
                        }
                    });

                    // Afficher/masquer les vues
                    tableView.classList.add('d-none');
                    cardsView.classList.add('d-none');
                    mixView.classList.add('d-none');
                    
                    if (view === 'table') {
                        tableView.classList.remove('d-none');
                    } else if (view === 'cards') {
                        cardsView.classList.remove('d-none');
                    } else {
                        mixView.classList.remove('d-none');
                    }
                });
            });

            // Gestion de la sélection dans la vue mixte
            const mixRows = document.querySelectorAll('.reservation-row-mix');
            mixRows.forEach(row => {
                row.addEventListener('click', function() {
                    const reservationId = this.getAttribute('data-reservation-id');
                    // Charger les détails de la réservation via AJAX ou redirection
                    window.location.href = '{{ route("admin.reservations") }}?selected=' + reservationId;
                });
            });
        });
    </script>
@endsection

