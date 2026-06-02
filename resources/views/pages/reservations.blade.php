@extends('layouts.dashboard')

@section('title', 'Mes Réservations | LUXÎLES - Dashboard')

@section('content')
    <!-- Page Header / Hero Minimal -->
    <section id="reservations-hero" class="position-relative" style="height: 300px; background-color: var(--lux-dark-blue); overflow: hidden; margin-top: -1rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 768px) {
                #reservations-hero {
                    margin-top: -2rem !important;
                    margin-left: -2rem !important;
                    margin-right: -2rem !important;
                }
            }
        </style>
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-40">
            <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop" class="w-100 h-100" style="object-fit: cover;" alt="Luxury Pool">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(10,26,47,0.7) 0%, rgba(10,26,47,0.4) 50%, rgba(10,26,47,0.8) 100%);"></div>
        </div>
        <div class="position-relative z-10 h-100 d-flex align-items-center justify-content-center text-center" style="padding-top: 3rem;">
            <div>
                <span class="text-lux-gold text-uppercase small fw-medium mb-2 d-block" style="letter-spacing: 0.2em; font-size: 0.875rem;">Espace Client</span>
                <h1 class="h1 font-serif text-white mb-3" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Mes Séjours d'Exception</h1>
                <p class="text-white-50 fw-light mx-auto" style="max-width: 600px;">Retrouvez l'historique de vos voyages et préparez vos prochaines évasions.</p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container-fluid px-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
        @endif

        <!-- Filter & View Toggle Bar -->
        <div class="card border mb-4 shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
            <div class="card-body p-4">
                <div class="row g-3 align-items-center">
                    <!-- Filter Tabs -->
                    <div class="col-12 col-md-auto">
                        <div class="d-flex align-items-center gap-1 bg-light p-1 rounded overflow-x-auto" style="background-color: rgba(248, 248, 246, 0.5) !important;">
                            <button class="btn btn-sm px-3 py-2 rounded border-0 bg-white text-lux-dark-blue shadow-sm fw-medium active" data-filter="all" style="white-space: nowrap; font-size: 0.875rem;">Tout voir</button>
                            <button class="btn btn-sm px-3 py-2 rounded border-0 text-lux-gray fw-medium" data-filter="upcoming" style="white-space: nowrap; font-size: 0.875rem; background: transparent;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.5)'" onmouseout="this.style.backgroundColor='transparent'">À venir</button>
                            <button class="btn btn-sm px-3 py-2 rounded border-0 text-lux-gray fw-medium" data-filter="past" style="white-space: nowrap; font-size: 0.875rem; background: transparent;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.5)'" onmouseout="this.style.backgroundColor='transparent'">Passées</button>
                            <button class="btn btn-sm px-3 py-2 rounded border-0 text-lux-gray fw-medium" data-filter="cancelled" style="white-space: nowrap; font-size: 0.875rem; background: transparent;" onmouseover="this.style.backgroundColor='rgba(255,255,255,0.5)'" onmouseout="this.style.backgroundColor='transparent'">Annulées</button>
                        </div>
                    </div>
                    <!-- View Toggles -->
                    <div class="col-12 col-md-auto ms-md-auto">
                        <div class="d-flex align-items-center gap-3">
                            <span class="small text-lux-gray d-none d-md-inline">Affichage :</span>
                            <div class="d-flex bg-light p-1 rounded border" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.1) !important;">
                                <button onclick="showView('cards')" class="btn btn-sm p-2 rounded border-0" style="background: transparent;" onmouseover="this.style.backgroundColor='white'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'" title="Vue Cartes">
                                    <i class="fa-solid fa-th-large"></i>
                                </button>
                                <button onclick="showView('table')" class="btn btn-sm p-2 rounded border-0" style="background: transparent;" onmouseover="this.style.backgroundColor='white'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'" title="Vue Tableau">
                                    <i class="fa-solid fa-table-list"></i>
                                </button>
                                <button onclick="showView('list')" class="btn btn-sm p-2 rounded border-0" style="background: transparent;" onmouseover="this.style.backgroundColor='white'; this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.backgroundColor='transparent'; this.style.color='inherit'" title="Vue Liste Simple">
                                    <i class="fa-solid fa-list"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Variant A: Cards View (Default) -->
        <section id="view-cards" class="row g-4 mb-4">
            @forelse($reservations as $reservation)
                @php
                    // Déterminer la période
                    $isUpcoming = $reservation->check_out_date >= now()->toDateString() 
                        && !in_array($reservation->status, ['cancelled', 'completed']);
                    $isPast = $reservation->check_out_date < now()->toDateString() 
                        || $reservation->status === 'completed';
                    $period = $isUpcoming ? 'upcoming' : 'past';
                    
                    // Mapper les statuts aux badges
                    $statusBadges = [
                        'confirmed' => ['bg' => 'success', 'text' => 'Confirmée', 'textColor' => 'white'],
                        'deposit_paid' => ['bg' => 'info', 'text' => 'Arrhes payées', 'textColor' => 'white'],
                        'fully_paid' => ['bg' => 'success', 'text' => 'Payée', 'textColor' => 'white'],
                        'pending' => ['bg' => 'warning', 'text' => 'En attente', 'textColor' => 'dark'],
                        'completed' => ['bg' => 'secondary', 'text' => 'Terminée', 'textColor' => 'dark'],
                        'cancelled' => ['bg' => 'danger', 'text' => 'Annulée', 'textColor' => 'white'],
                        'refunded' => ['bg' => 'secondary', 'text' => 'Remboursée', 'textColor' => 'dark'],
                    ];
                    $badge = $statusBadges[$reservation->status] ?? ['bg' => 'secondary', 'text' => ucfirst($reservation->status), 'textColor' => 'dark'];
                    
                    // Récupérer la photo principale
                    $primaryPhoto = $reservation->villa->photos->where('is_primary', true)->first() 
                        ?? $reservation->villa->photos->first();
                    
                    // Styles conditionnels
                    $cardOpacity = $reservation->status === 'cancelled' ? '0.75' : ($isPast ? '0.9' : '1');
                    $cardFilter = $isPast && $reservation->status !== 'cancelled' ? 'filter: grayscale(1); transition: filter 0.5s;' : '';
                    $imageFilter = $reservation->status === 'cancelled' ? 'filter: sepia(0.3);' : '';
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <article class="card border h-100 shadow-sm reservation-item" 
                             data-status="{{ $reservation->status }}" 
                             data-period="{{ $period }}" 
                             style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; overflow: hidden; opacity: {{ $cardOpacity }}; transition: box-shadow 0.3s{{ $isPast && $reservation->status !== 'cancelled' ? ', filter 0.5s' : '' }};" 
                             onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'; @if($isPast && $reservation->status !== 'cancelled') this.style.filter='grayscale(0)'; @endif" 
                             onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; @if($isPast && $reservation->status !== 'cancelled') this.style.filter='grayscale(1)'; @endif">
                        <div class="position-relative" style="height: 192px; overflow: hidden; {{ $cardFilter }}">
                            <div class="position-absolute top-0 end-0 m-3" style="z-index: 10;">
                                <span class="badge bg-{{ $badge['bg'] }} bg-opacity-90 text-{{ $badge['textColor'] }} px-2 py-1 small border border-{{ $badge['bg'] }} border-opacity-25">{{ $badge['text'] }}</span>
                            </div>
                            @if($primaryPhoto)
                                <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" 
                                     class="w-100 h-100" 
                                     style="object-fit: cover; transition: transform 0.7s; {{ $imageFilter }}" 
                                     onmouseover="this.style.transform='scale(1.05)'" 
                                     onmouseout="this.style.transform='scale(1)'" 
                                     alt="{{ $reservation->villa->name }}">
                            @else
                                <div class="w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50"></i>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                                <p class="text-white small fw-medium text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">{{ $reservation->villa->island->name ?? '' }}</p>
                                <h3 class="text-white font-serif h5 mb-0">{{ $reservation->villa->name }}</h3>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                                <div class="text-center">
                                    <span class="d-block small text-lux-gray text-uppercase mb-1" style="font-size: 0.75rem;">Arrivée</span>
                                    <span class="{{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }} fw-medium small">{{ $reservation->check_in_date->format('d M') }}</span>
                                </div>
                                <i class="fa-solid fa-arrow-right-long text-lux-gold {{ $reservation->status === 'cancelled' ? 'opacity-30' : 'opacity-50' }}"></i>
                                <div class="text-center">
                                    <span class="d-block small text-lux-gray text-uppercase mb-1" style="font-size: 0.75rem;">Départ</span>
                                    <span class="{{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }} fw-medium small">{{ $reservation->check_out_date->format('d M') }}</span>
                                </div>
                                <div class="text-center ps-3 border-start" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                                    <span class="d-block small text-lux-gray text-uppercase mb-1" style="font-size: 0.75rem;">Nuits</span>
                                    <span class="{{ $reservation->status === 'cancelled' ? 'text-muted' : 'text-lux-dark-blue' }} fw-medium small">{{ $reservation->number_of_nights }}</span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center justify-content-between mb-4 small">
                                <span class="text-lux-gray"><i class="fa-solid fa-user-group text-lux-gold me-2"></i>{{ $reservation->number_of_guests }} Voyageur{{ $reservation->number_of_guests > 1 ? 's' : '' }}</span>
                                <span class="{{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }} fw-semibold">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</span>
                            </div>
                            @if($reservation->status === 'cancelled')
                                <a href="{{ route('villas.show', $reservation->villa_id) }}" class="btn btn-link w-100 text-lux-gold py-2 rounded small fw-medium text-decoration-none" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">
                                    <i class="fa-solid fa-rotate-right me-2"></i> Réserver à nouveau
                                </a>
                            @elseif($isPast)
                                <div class="d-flex flex-column gap-2">
                                    <x-reservation-review-action :reservation="$reservation" :review-service="$reviewService" />
                                    <a href="{{ route('espace-client.documents') }}" class="btn btn-outline-secondary w-100 border-lux-gray text-lux-gray py-2 rounded small fw-medium text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='var(--lux-gray)'; this.style.color='var(--lux-gray)'">
                                        Revoir la facture
                                    </a>
                                </div>
                            @else
                                @if($reservation->allowsClientOnlinePayment())
                                    @if($reservation->status === 'deposit_paid')
                                        @php
                                            $pendingBalance = $reservation->payments()->where('type', 'balance')->whereIn('status', ['pending', 'processing'])->first();
                                            $pendingGuarantee = $reservation->payments()->where('type', 'deposit_guarantee')->whereIn('status', ['pending', 'processing'])->first();
                                        @endphp

                                        <div class="d-flex flex-column gap-2">
                                            @if($pendingBalance)
                                                <a href="{{ route('espace-client.pay-balance', $reservation) }}" class="btn btn-lux-primary w-100 py-2 rounded small fw-medium text-decoration-none">
                                                    Régler le solde
                                                </a>
                                            @endif

                                            @if($pendingGuarantee)
                                                <a href="{{ route('espace-client.pay-deposit-guarantee', $reservation) }}" class="btn btn-lux-secondary w-100 py-2 rounded small fw-medium text-decoration-none">
                                                    Régler ma caution
                                                </a>
                                            @endif

                                            <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-link w-100 text-lux-gray py-1 small text-decoration-none">
                                                Voir détails
                                            </a>
                                        </div>
                                    @else
                                        <div class="d-flex flex-column gap-2">
                                            <a href="{{ route('espace-client.pay-deposit', $reservation) }}" class="btn btn-lux-secondary w-100 py-2 rounded small fw-medium text-decoration-none">
                                                Régler l'acompte
                                            </a>
                                            <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-link w-100 text-lux-gray py-1 small text-decoration-none">
                                                Voir détails
                                            </a>
                                        </div>
                                    @endif
                                @else
                                    <div class="d-flex flex-column gap-2">
                                        @if($reservation->hasPendingClientPayments())
                                            <x-reservation-offline-payment-notice class="text-center py-2" />
                                        @endif
                                        <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-link w-100 text-lux-gray py-1 small text-decoration-none">
                                            Voir détails
                                        </a>
                                    </div>
                                @endif
                            @endif
                        </div>
                    </article>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border text-center p-5" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <i class="fa-regular fa-calendar-xmark fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                        <h3 class="h4 font-serif text-lux-dark-blue mb-2">Aucune réservation</h3>
                        <p class="text-lux-gray mb-4">Vous n'avez pas encore de réservation.</p>
                        <a href="{{ route('villas.index') }}" class="btn btn-lux-primary">Réserver une villa</a>
                    </div>
                </div>
            @endforelse
        </section>

        <!-- Variant B: Premium Table View (Hidden by default) -->
        <section id="view-table" class="d-none mb-4">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; overflow: hidden;">
                @if($reservations->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr class="bg-light border-bottom" style="background-color: rgba(248, 248, 246, 0.5) !important; border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <th class="p-4 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Villa</th>
                                    <th class="p-4 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Dates</th>
                                    <th class="p-4 small fw-bold text-lux-gray text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em;">Statut</th>
                                    <th class="p-4 small fw-bold text-lux-gray text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Montant</th>
                                    <th class="p-4 small fw-bold text-lux-gray text-uppercase text-end" style="font-size: 0.75rem; letter-spacing: 0.05em;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($reservations as $reservation)
                                    @php
                                        $isUpcoming = $reservation->check_out_date >= now()->toDateString() 
                                            && !in_array($reservation->status, ['cancelled', 'completed']);
                                        $isPast = $reservation->check_out_date < now()->toDateString() 
                                            || $reservation->status === 'completed';
                                        $period = $isUpcoming ? 'upcoming' : 'past';
                                        
                                        $statusBadges = [
                                            'confirmed' => ['bg' => 'success', 'text' => 'Confirmée'],
                                            'deposit_paid' => ['bg' => 'info', 'text' => 'Arrhes payées'],
                                            'fully_paid' => ['bg' => 'success', 'text' => 'Payée'],
                                            'pending' => ['bg' => 'warning', 'text' => 'En attente'],
                                            'completed' => ['bg' => 'secondary', 'text' => 'Terminée'],
                                            'cancelled' => ['bg' => 'danger', 'text' => 'Annulée'],
                                            'refunded' => ['bg' => 'secondary', 'text' => 'Remboursée'],
                                        ];
                                        $badge = $statusBadges[$reservation->status] ?? ['bg' => 'secondary', 'text' => ucfirst($reservation->status)];
                                        
                                        $primaryPhoto = $reservation->villa->photos->where('is_primary', true)->first() 
                                            ?? $reservation->villa->photos->first();
                                        $imageFilter = $isPast && $reservation->status !== 'cancelled' ? 'filter: grayscale(1); transition: filter 0.5s;' : ($reservation->status === 'cancelled' ? 'filter: sepia(0.3);' : '');
                                    @endphp
                                    <tr class="reservation-item" 
                                        data-status="{{ $reservation->status }}" 
                                        data-period="{{ $period }}" 
                                        style="opacity: {{ $reservation->status === 'cancelled' ? '0.75' : ($isPast ? '0.9' : '1') }}; transition: background-color 0.3s{{ $isPast && $reservation->status !== 'cancelled' ? ', filter 0.5s' : '' }};" 
                                        onmouseover="this.style.backgroundColor='rgba(248, 248, 246, 0.3)'; @if($isPast && $reservation->status !== 'cancelled') this.querySelector('img').style.filter='grayscale(0)'; @endif" 
                                        onmouseout="this.style.backgroundColor='transparent'; @if($isPast && $reservation->status !== 'cancelled') this.querySelector('img').style.filter='grayscale(1)'; @endif">
                                        <td class="p-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="rounded overflow-hidden flex-shrink-0" style="width: 96px; height: 64px; {{ $imageFilter }}">
                                                    @if($primaryPhoto)
                                                        <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $reservation->villa->name }}">
                                                    @else
                                                        <div class="w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                                            <i class="fas fa-image fa-lg text-lux-greyBlue opacity-50"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h4 class="font-serif text-lux-dark-blue h5 mb-1">{{ $reservation->villa->name }}</h4>
                                                    <span class="small text-lux-gray">{{ $reservation->villa->island->name ?? '' }} • #{{ $reservation->reservation_number }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4">
                                            <div class="small {{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }}">{{ $reservation->check_in_date->format('d M') }} - {{ $reservation->check_out_date->format('d M Y') }}</div>
                                            <span class="small text-lux-gray">{{ $reservation->number_of_nights }} nuits</span>
                                        </td>
                                        <td class="p-4">
                                            <span class="badge bg-{{ $badge['bg'] }} bg-opacity-10 text-{{ $badge['bg'] }} px-3 py-1 rounded-pill small border border-{{ $badge['bg'] }} border-opacity-25">{{ $badge['text'] }}</span>
                                        </td>
                                        <td class="p-4 text-end fw-medium {{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }}">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</td>
                                        <td class="p-4 text-end">
                                            @if($reservation->status === 'cancelled')
                                                <a href="{{ route('villas.show', $reservation->villa_id) }}" class="btn btn-link text-lux-gold p-0 border-0 text-decoration-none small fw-medium" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'"><i class="fa-solid fa-rotate-right me-1"></i> Réserver</a>
                                            @elseif($isPast)
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    @if($reviewService->canUserSubmitReview(auth()->user(), $reservation))
                                                        <a href="{{ route('espace-client.reviews.create', $reservation) }}" class="text-lux-gold text-decoration-none small fw-medium">Déposer un avis</a>
                                                    @endif
                                                    <a href="{{ route('espace-client.documents') }}" class="btn btn-link text-lux-gray p-0 border-0 text-decoration-none small fw-medium" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="this.style.color='var(--lux-gray)'">Facture <i class="fa-solid fa-download ms-1"></i></a>
                                                </div>
                                            @else
                                                @if($reservation->allowsClientOnlinePayment())
                                                    @if($reservation->status === 'deposit_paid')
                                                        @php
                                                            $pendingBalance = $reservation->payments()->where('type', 'balance')->whereIn('status', ['pending', 'processing'])->first();
                                                            $pendingGuarantee = $reservation->payments()->where('type', 'deposit_guarantee')->whereIn('status', ['pending', 'processing'])->first();
                                                        @endphp
                                                        <div class="d-flex flex-column align-items-end gap-1">
                                                            @if($pendingBalance)
                                                                <a href="{{ route('espace-client.pay-balance', $reservation) }}" class="text-lux-gold text-decoration-none small fw-medium">Régler Solde</a>
                                                            @endif
                                                            @if($pendingGuarantee)
                                                                <a href="{{ route('espace-client.pay-deposit-guarantee', $reservation) }}" class="text-lux-gold text-decoration-none small fw-medium">Régler Caution</a>
                                                            @endif
                                                        </div>
                                                    @elseif($reservation->status === 'pending')
                                                        <div class="d-flex flex-column align-items-end gap-1">
                                                            <a href="{{ route('espace-client.pay-deposit', $reservation) }}" class="btn btn-lux-secondary btn-sm px-3 py-1 rounded small fw-medium text-decoration-none">Régler l'acompte</a>
                                                            <a href="{{ route('espace-client.index', $reservation) }}" class="text-lux-gray text-decoration-none small opacity-75">Détails</a>
                                                        </div>
                                                    @else
                                                        <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-link text-lux-gold p-0 border-0 text-decoration-none small fw-medium" style="transition: color 0.3s; border-bottom: 1px solid transparent !important;" onmouseover="this.style.color='var(--lux-light-gold)'; this.style.borderBottomColor='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'; this.style.borderBottomColor='transparent'">Voir détails</a>
                                                    @endif
                                                @elseif($reservation->hasPendingClientPayments())
                                                    <x-reservation-offline-payment-notice class="text-end" />
                                                @else
                                                    <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-link text-lux-gold p-0 border-0 text-decoration-none small fw-medium">Voir détails</a>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="p-5 text-center">
                        <i class="fa-regular fa-calendar-xmark fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                        <h3 class="h4 font-serif text-lux-dark-blue mb-2">Aucune réservation</h3>
                        <p class="text-lux-gray mb-4">Vous n'avez pas encore de réservation.</p>
                        <a href="{{ route('villas.index') }}" class="btn btn-lux-primary">Réserver une villa</a>
                    </div>
                @endif
            </div>
        </section>

        <!-- Variant C: Simple List View (Hidden by default) -->
        <section id="view-list" class="d-none mb-4">
            @if($reservations->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($reservations as $reservation)
                        @php
                            $isUpcoming = $reservation->check_out_date >= now()->toDateString() 
                                && !in_array($reservation->status, ['cancelled', 'completed']);
                            $isPast = $reservation->check_out_date < now()->toDateString() 
                                || $reservation->status === 'completed';
                            $period = $isUpcoming ? 'upcoming' : 'past';
                            
                            $statusBadges = [
                                'confirmed' => ['bg' => 'success', 'text' => 'Confirmée'],
                                'deposit_paid' => ['bg' => 'info', 'text' => 'Arrhes payées'],
                                'fully_paid' => ['bg' => 'success', 'text' => 'Payée'],
                                'pending' => ['bg' => 'warning', 'text' => 'En attente'],
                                'completed' => ['bg' => 'secondary', 'text' => 'Terminée'],
                                'cancelled' => ['bg' => 'danger', 'text' => 'Annulée'],
                                'refunded' => ['bg' => 'secondary', 'text' => 'Remboursée'],
                            ];
                            $badge = $statusBadges[$reservation->status] ?? ['bg' => 'secondary', 'text' => ucfirst($reservation->status)];
                            
                            $primaryPhoto = $reservation->villa->photos->where('is_primary', true)->first() 
                                ?? $reservation->villa->photos->first();
                            $imageFilter = $isPast && $reservation->status !== 'cancelled' ? 'filter: grayscale(1); transition: filter 0.5s;' : ($reservation->status === 'cancelled' ? 'filter: sepia(0.3);' : '');
                        @endphp
                        <div class="card border shadow-sm reservation-item" 
                             data-status="{{ $reservation->status }}" 
                             data-period="{{ $period }}" 
                             style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; opacity: {{ $reservation->status === 'cancelled' ? '0.75' : ($isPast ? '0.9' : '1') }}; transition: box-shadow 0.3s{{ $isPast && $reservation->status !== 'cancelled' ? ', filter 0.5s' : '' }};" 
                             onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'; @if($isPast && $reservation->status !== 'cancelled') this.querySelector('img').style.filter='grayscale(0)'; @endif" 
                             onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'; @if($isPast && $reservation->status !== 'cancelled') this.querySelector('img').style.filter='grayscale(1)'; @endif">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-center justify-content-between gap-4">
                                    <div class="d-flex align-items-center gap-4 flex-grow-1">
                                        <div class="rounded overflow-hidden flex-shrink-0" style="width: 128px; height: 80px; {{ $imageFilter }}">
                                            @if($primaryPhoto)
                                                <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $reservation->villa->name }}">
                                            @else
                                                <div class="w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                                    <i class="fas fa-image fa-lg text-lux-greyBlue opacity-50"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-2">
                                                <h3 class="font-serif h4 text-lux-dark-blue mb-0">{{ $reservation->villa->name }}</h3>
                                                <span class="badge bg-{{ $badge['bg'] }} bg-opacity-10 text-{{ $badge['bg'] }} px-2 py-1 rounded small border border-{{ $badge['bg'] }} border-opacity-25">{{ $badge['text'] }}</span>
                                            </div>
                                            <p class="small text-lux-gray mb-1">{{ $reservation->villa->island->name ?? '' }} • Réf. #{{ $reservation->reservation_number }}</p>
                                            <p class="small {{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }} mb-0">
                                                {{ $reservation->check_in_date->format('d M') }} - {{ $reservation->check_out_date->format('d M Y') }} • {{ $reservation->number_of_nights }} nuits • {{ $reservation->number_of_guests }} voyageur{{ $reservation->number_of_guests > 1 ? 's' : '' }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end flex-shrink-0">
                                        @if($reservation->discount_amount > 0 && $reservation->promoCode)
                                            <p class="small text-success mb-1">
                                                Réduction ({{ $reservation->promoCode->code }}) : -{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €
                                            </p>
                                        @endif
                                        <p class="h4 fw-semibold {{ $reservation->status === 'cancelled' ? 'text-muted text-decoration-line-through' : 'text-lux-dark-blue' }} mb-2">{{ number_format($reservation->total_price, 0, ',', ' ') }} €</p>
                                        @if($reservation->status === 'cancelled')
                                            <a href="{{ route('villas.show', $reservation->villa_id) }}" class="btn btn-link btn-sm px-4 py-2 rounded small fw-medium text-lux-gold text-decoration-none" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-light-gold)'" onmouseout="this.style.color='var(--lux-gold)'">
                                                <i class="fa-solid fa-rotate-right me-2"></i> Réserver à nouveau
                                            </a>
                                        @elseif($isPast)
                                            <div class="d-flex flex-column align-items-end gap-2">
                                                <x-reservation-review-action :reservation="$reservation" :review-service="$reviewService" btn-class="btn btn-lux-primary btn-sm px-3 py-2 rounded small fw-medium text-decoration-none" />
                                                <a href="{{ route('espace-client.documents') }}" class="btn btn-outline-secondary btn-sm px-4 py-2 rounded small fw-medium border-lux-gray text-lux-gray text-decoration-none" style="transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="this.style.borderColor='var(--lux-gray)'; this.style.color='var(--lux-gray)'">Facture</a>
                                            </div>
                                        @else
                                            @if($reservation->allowsClientOnlinePayment())
                                                @if($reservation->status === 'deposit_paid')
                                                    @php
                                                        $pendingBalance = $reservation->payments()->where('type', 'balance')->whereIn('status', ['pending', 'processing'])->first();
                                                        $pendingGuarantee = $reservation->payments()->where('type', 'deposit_guarantee')->whereIn('status', ['pending', 'processing'])->first();
                                                    @endphp
                                                    <div class="d-flex flex-column gap-2 align-items-end">
                                                        @if($pendingBalance)
                                                            <a href="{{ route('espace-client.pay-balance', $reservation) }}" class="btn btn-lux-primary btn-sm px-4 py-1 rounded small fw-medium text-decoration-none">Régler Solde</a>
                                                        @endif
                                                        @if($pendingGuarantee)
                                                            <a href="{{ route('espace-client.pay-deposit-guarantee', $reservation) }}" class="btn btn-lux-secondary btn-sm px-4 py-1 rounded small fw-medium text-decoration-none">Régler Caution</a>
                                                        @endif
                                                    </div>
                                                @elseif($reservation->status === 'pending')
                                                    <div class="d-flex flex-column gap-2 w-100">
                                                        <a href="{{ route('espace-client.pay-deposit', $reservation) }}" class="btn btn-lux-secondary btn-sm w-100 py-2 rounded small fw-medium text-decoration-none">Régler l'acompte</a>
                                                        <a href="{{ route('espace-client.index', $reservation) }}" class="text-lux-gray small text-center text-decoration-none">Voir détails</a>
                                                    </div>
                                                @else
                                                    <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-lux-primary btn-sm px-4 py-2 rounded small fw-medium text-decoration-none">Voir détails</a>
                                                @endif
                                            @elseif($reservation->hasPendingClientPayments())
                                                <x-reservation-offline-payment-notice class="text-end mb-2" />
                                                <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-lux-primary btn-sm px-4 py-2 rounded small fw-medium text-decoration-none">Voir détails</a>
                                            @else
                                                <a href="{{ route('espace-client.index', $reservation) }}" class="btn btn-lux-primary btn-sm px-4 py-2 rounded small fw-medium text-decoration-none">Voir détails</a>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="card border text-center p-5" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <i class="fa-regular fa-calendar-xmark fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                    <h3 class="h4 font-serif text-lux-dark-blue mb-2">Aucune réservation</h3>
                    <p class="text-lux-gray mb-4">Vous n'avez pas encore de réservation.</p>
                    <a href="{{ route('villas.index') }}" class="btn btn-lux-primary">Réserver une villa</a>
                </div>
            @endif
        </section>
    </div>
@endsection

@push('scripts')
<script>
    function showView(viewType) {
        // Hide all views
        document.getElementById('view-cards').classList.add('d-none');
        document.getElementById('view-table').classList.add('d-none');
        document.getElementById('view-list').classList.add('d-none');
        
        // Show selected view
        document.getElementById('view-' + viewType).classList.remove('d-none');
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Filter tabs functionality
        const filterButtons = document.querySelectorAll('[data-filter]');
        filterButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons
                filterButtons.forEach(b => {
                    b.classList.remove('active', 'bg-white', 'shadow-sm', 'text-lux-dark-blue');
                    b.classList.add('text-lux-gray');
                    b.style.backgroundColor = 'transparent';
                });
                
                // Add active class to clicked button
                this.classList.add('active', 'bg-white', 'shadow-sm', 'text-lux-dark-blue');
                this.classList.remove('text-lux-gray');
                
                const filter = this.getAttribute('data-filter');
                const items = document.querySelectorAll('.reservation-item');
                
                items.forEach(item => {
                    const status = item.getAttribute('data-status');
                    const period = item.getAttribute('data-period');
                    
                    if (filter === 'all') {
                        item.style.display = '';
                    } else if (filter === 'upcoming' && period === 'upcoming') {
                        item.style.display = '';
                    } else if (filter === 'past' && period === 'past') {
                        item.style.display = '';
                    } else if (filter === 'cancelled' && status === 'cancelled') {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endpush
