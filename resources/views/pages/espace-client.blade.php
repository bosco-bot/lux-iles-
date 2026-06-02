@extends('layouts.dashboard')

@section('title', 'Espace Client | LUXÎLES - Dashboard')

@section('content')
    <!-- Welcome Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-5">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Bonjour, {{ auth()->user()->first_name ?? 'Utilisateur' }}
            </h1>
            <p class="text-lux-gray small mb-0">Voici un aperçu de vos séjours à venir.</p>
        </div>
        <div class="mt-3 mt-md-0">
            <a href="{{ route('villas.index') }}" class="btn btn-lux-primary d-inline-flex align-items-center gap-2">
                <i class="fa-solid fa-plus small"></i>
                <span class="small fw-medium">Nouvelle recherche</span>
            </a>
        </div>
    </div>

    <!-- Dashboard Grid -->
    <div class="row g-4">
        <!-- Left Column (Main Stats & Upcoming) -->
        <div class="col-lg-8">
            <!-- Upcoming Reservation Card -->
            @if($nextReservation)
                @php
                    $primaryPhoto = $nextReservation->villa->photos->where('is_primary', true)->first() 
                        ?? $nextReservation->villa->photos->first();
                @endphp
                <section class="reservation-card mb-4">
                    <div class="border-bottom bg-light p-4 d-flex justify-content-between align-items-center">
                        <h2 class="h5 font-serif mb-0 d-flex align-items-center gap-2" style="color: var(--lux-dark-blue);">
                            <i class="fa-solid fa-plane-departure text-lux-gold"></i>
                            Prochain Séjour
                        </h2>
                        @php
                            $statusBadges = [
                                'confirmed' => ['bg' => 'success', 'text' => 'Confirmé'],
                                'deposit_paid' => ['bg' => 'info', 'text' => 'Arrhes payées'],
                                'fully_paid' => ['bg' => 'success', 'text' => 'Payé'],
                                'pending' => ['bg' => 'warning', 'text' => 'En attente'],
                            ];
                            $badge = $statusBadges[$nextReservation->status] ?? ['bg' => 'secondary', 'text' => ucfirst($nextReservation->status)];
                        @endphp
                        <span class="badge bg-{{ $badge['bg'] }} bg-opacity-10 text-{{ $badge['bg'] }} border border-{{ $badge['bg'] }} border-opacity-25 px-3 py-1 small">{{ $badge['text'] }}</span>
                    </div>
                    <div class="row g-0">
                        <!-- Image -->
                        <div class="col-md-5">
                            <div class="position-relative" style="height: 256px;">
                                @if($primaryPhoto)
                                    <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" 
                                         class="reservation-image position-absolute top-0 start-0 w-100 h-100" 
                                         style="object-fit: cover;"
                                         alt="{{ $nextReservation->villa->name }}">
                                @else
                                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                        <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50"></i>
                                    </div>
                                @endif
                                <div class="position-absolute bottom-0 start-0 end-0 p-3 d-md-none" style="background: linear-gradient(to top, rgba(0,0,0,0.5), transparent);">
                                    <p class="font-serif text-white mb-1 h5">{{ $nextReservation->villa->name }}</p>
                                    <p class="text-white-50 small mb-0">{{ $nextReservation->villa->island->name ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <!-- Details -->
                        <div class="col-md-7 p-4 p-md-5 d-flex flex-column justify-content-between">
                            <div>
                                <div class="d-none d-md-block mb-3">
                                    <h3 class="h4 font-serif mb-2" style="color: var(--lux-dark-blue);">{{ $nextReservation->villa->name }}</h3>
                                    <p class="text-lux-gray small d-flex align-items-center gap-1 mb-0">
                                        <i class="fa-solid fa-location-dot text-lux-gold"></i>
                                        {{ $nextReservation->villa->address ?? '' }}, {{ $nextReservation->villa->island->name ?? '' }}
                                    </p>
                                </div>
                                
                                <div class="row g-4 mb-4">
                                    <div class="col-6">
                                        <p class="small text-lux-gray text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Arrivée</p>
                                        <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">{{ $nextReservation->check_in_date->format('d M Y') }}</p>
                                        <p class="small text-muted mb-0" style="font-size: 0.75rem;">{{ $nextReservation->villa->check_in_time ? \Carbon\Carbon::parse($nextReservation->villa->check_in_time)->format('H:i') : '15:00' }}</p>
                                    </div>
                                    <div class="col-6">
                                        <p class="small text-lux-gray text-uppercase mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Départ</p>
                                        <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">{{ $nextReservation->check_out_date->format('d M Y') }}</p>
                                        <p class="small text-muted mb-0" style="font-size: 0.75rem;">{{ $nextReservation->villa->check_out_time ? \Carbon\Carbon::parse($nextReservation->villa->check_out_time)->format('H:i') : '11:00' }}</p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center gap-3 py-3 border-top">
                                    <div class="d-flex align-items-center justify-content-center rounded-circle bg-lux-gold text-lux-dark-blue fw-bold" style="width: 32px; height: 32px; font-size: 0.75rem;">
                                        {{ strtoupper(substr($nextReservation->guest_first_name ?? '', 0, 1) . substr($nextReservation->guest_last_name ?? '', 0, 1)) }}
                                    </div>
                                    <span class="small text-lux-gray">{{ $nextReservation->number_of_guests }} Voyageur{{ $nextReservation->number_of_guests > 1 ? 's' : '' }}</span>
                                </div>
                            </div>
                            
                            <div class="d-flex gap-2 mt-3">
                                <a href="{{ route('espace-client.reservations') }}" class="btn btn-lux-primary flex-fill text-decoration-none">Gérer</a>
                                @if($nextReservation->status === 'pending' && $nextReservation->allowsClientOnlinePayment())
                                    <a href="{{ route('espace-client.pay-deposit', $nextReservation) }}" class="btn btn-lux-secondary flex-fill text-decoration-none">Régler l'acompte</a>
                                @else
                                    @php
                                        // Construire le lien Google Maps
                                        if ($nextReservation->villa->latitude && $nextReservation->villa->longitude) {
                                            // Utiliser les coordonnées GPS si disponibles (plus précis)
                                            $mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $nextReservation->villa->latitude . ',' . $nextReservation->villa->longitude;
                                        } else {
                                            // Sinon utiliser l'adresse comme fallback
                                            $destination = urlencode(($nextReservation->villa->address ?? '') . ', ' . ($nextReservation->villa->island->name ?? ''));
                                            $mapsUrl = 'https://www.google.com/maps/dir/?api=1&destination=' . $destination;
                                        }
                                    @endphp
                                    <a href="{{ $mapsUrl }}" target="_blank" rel="noopener noreferrer" class="btn btn-outline-secondary flex-fill" style="border-color: rgba(10, 26, 47, 0.2); color: var(--lux-dark-blue);">Itinéraire</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </section>
            @else
                <section class="reservation-card mb-4">
                    <div class="border-bottom bg-light p-4 d-flex justify-content-between align-items-center">
                        <h2 class="h5 font-serif mb-0 d-flex align-items-center gap-2" style="color: var(--lux-dark-blue);">
                            <i class="fa-solid fa-plane-departure text-lux-gold"></i>
                            Prochain Séjour
                        </h2>
                    </div>
                    <div class="p-5 text-center">
                        <i class="fa-regular fa-calendar-xmark fa-3x text-lux-greyBlue opacity-50 mb-3"></i>
                        <p class="text-lux-gray mb-0">Aucune réservation à venir</p>
                        <a href="{{ route('villas.index') }}" class="btn btn-lux-primary mt-3">Réserver une villa</a>
                    </div>
                </section>
            @endif

            <!-- Quick Actions Grid -->
            <div class="row">
                <!-- Documents Block -->
                <div class="col-md-6 mb-4 mb-md-0 pe-md-3">
                    <section class="widget-card h-100">
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div class="widget-icon bg-blue bg-opacity-10 text-primary">
                                <i class="fa-regular fa-file-lines"></i>
                            </div>
                            @if($newDocumentsCount > 0)
                                <span class="badge px-2 py-1 small fw-medium rounded" style="background-color: rgba(203, 174, 130, 0.1); color: var(--lux-gold);">{{ $newDocumentsCount }} Nouveau{{ $newDocumentsCount > 1 ? 'x' : '' }}</span>
                            @endif
                        </div>
                        <h3 class="h5 font-serif mb-2" style="color: var(--lux-dark-blue);">Documents de voyage</h3>
                        <p class="small text-lux-gray mb-3">Contrats, factures et reçus.</p>
                        <div>
                            @foreach($recentClientDocuments ?? [] as $clientDoc)
                                <a href="{{ route('espace-client.documents.client-documents.download', $clientDoc) }}" class="d-flex align-items-center justify-content-between small p-2 bg-light rounded mb-2 text-decoration-none" style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'" onmouseout="this.style.backgroundColor='#f8f9fa'">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="fa-solid fa-folder-open text-lux-gold"></i>
                                        <span class="text-dark">{{ $clientDoc->title }}</span>
                                    </div>
                                    <i class="fa-solid fa-download text-muted"></i>
                                </a>
                            @endforeach
                            @forelse($recentDocuments as $document)
                                @php
                                    $typeIcons = [
                                        'contract' => 'fa-file-contract',
                                        'invoice' => 'fa-file-invoice',
                                        'receipt' => 'fa-file-lines',
                                        'deposit_receipt' => 'fa-file-lines',
                                        'balance_receipt' => 'fa-file-lines',
                                        'cancellation' => 'fa-file-times',
                                    ];
                                    $icon = $typeIcons[$document->type] ?? 'fa-file-pdf';
                                    
                                    // Certaines icônes nécessitent fa-solid au lieu de fa-regular
                                    $solidIcons = ['fa-file-contract', 'fa-file-invoice', 'fa-file-times', 'fa-file-pdf'];
                                    $iconClass = in_array($icon, $solidIcons) ? 'fa-solid' : 'fa-regular';
                                @endphp
                                <a href="{{ route('espace-client.documents.download', $document) }}" class="d-flex align-items-center justify-content-between small p-2 bg-light rounded mb-2 text-decoration-none" style="transition: background-color 0.3s;" onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.1)'" onmouseout="this.style.backgroundColor='#f8f9fa'">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="{{ $iconClass }} {{ $icon }} text-danger"></i>
                                        <span class="text-dark">{{ $document->file_name }}</span>
                                    </div>
                                    <i class="fa-solid fa-download text-muted"></i>
                                </a>
                            @empty
                                @if(empty($recentClientDocuments) || $recentClientDocuments->count() === 0)
                                    <p class="small text-lux-gray text-center py-3 mb-0">Aucun document disponible</p>
                                @endif
                            @endforelse
                            @if(($recentDocuments->count() > 0) || (isset($recentClientDocuments) && $recentClientDocuments->count() > 0))
                                <a href="{{ route('espace-client.documents') }}" class="small text-lux-gold text-decoration-none d-block text-center mt-2">Voir tous les documents</a>
                            @endif
                        </div>
                    </section>
                </div>

                <!-- Concierge Block -->
                <div class="col-md-6 ps-md-3">
                    <section class="widget-card h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="widget-icon bg-warning bg-opacity-10 text-lux-gold">
                                <i class="fa-solid fa-bell-concierge"></i>
                            </div>
                        </div>
                        <h3 class="h5 font-serif mb-2" style="color: var(--lux-dark-blue);">Services Conciergerie</h3>
                        <p class="small text-lux-gray mb-3">Réservez un chef, un massage ou une excursion.</p>
                        <a href="{{ route('services.conciergerie') }}" class="d-flex align-items-center small fw-medium text-decoration-none" style="color: var(--lux-dark-blue);">
                            Explorer les services <i class="fa-solid fa-arrow-right ms-2 small"></i>
                        </a>
                    </section>
                </div>
            </div>
        </div>

        <!-- Right Column (Sidebar Widgets) -->
        <div class="col-lg-4">
            <!-- Payment Status -->
            @if($paymentStatus && $paymentStatus['remaining'] > 0)
                <section class="widget-card mb-4">
                    <h3 class="h5 font-serif mb-4" style="color: var(--lux-dark-blue);">État des Paiements</h3>
                    
                    <div class="mb-4">
                        <div class="d-flex mb-2 align-items-center justify-content-between">
                            <span class="badge bg-primary bg-opacity-10 text-primary small px-2 py-1">Payé</span>
                            <span class="small fw-semibold" style="color: var(--lux-dark-blue);">{{ number_format($paymentStatus['percentage'], 0) }}%</span>
                        </div>
                        <div class="progress mb-3" style="height: 8px;">
                            <div class="progress-bar progress-bar-custom" role="progressbar" style="width: {{ $paymentStatus['percentage'] }}%"></div>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="text-lux-gray">Reste à payer</span>
                            <span class="fw-medium" style="color: var(--lux-dark-blue);">{{ number_format($paymentStatus['remaining'], 2, ',', ' ') }} €</span>
                        </div>
                        @if($paymentStatus['balance_due_date'] && $paymentStatus['balance_due_date']->isFuture())
                            <p class="small text-lux-gray mb-0">Échéance : {{ $paymentStatus['balance_due_date']->format('d M Y') }}</p>
                        @elseif($paymentStatus['balance_due_date'] && $paymentStatus['balance_due_date']->isPast())
                            <p class="small text-danger mb-0">Échéance dépassée</p>
                        @endif
                    </div>
                    
                    @if($nextReservation->allowsClientOnlinePayment())
                        <a href="{{ route('espace-client.pay-balance', $nextReservation) }}" class="btn btn-lux-primary w-100">
                            Régler le solde
                        </a>
                    @else
                        <x-reservation-offline-payment-notice />
                    @endif
                </section>
            @endif

            @if($nextReservation && isset($paymentStatus['guarantee_payment']) && $paymentStatus['guarantee_payment'])
                <section class="widget-card mb-4">
                    <h3 class="h5 font-serif mb-4" style="color: var(--lux-dark-blue);">Dépôt de Garantie</h3>
                    
                    @if($paymentStatus['remaining'] <= 0)
                        <div class="text-center py-2 mb-3">
                            <i class="fa-solid fa-check-circle text-success me-2"></i>
                            <span class="small text-lux-gray">Séjour entièrement payé</span>
                        </div>
                    @endif

                    <div class="p-3 rounded mb-3" style="background-color: rgba(138, 150, 166, 0.05);">
                        <div class="d-flex justify-content-between small mb-2">
                            <span class="text-lux-gray">Montant de la caution</span>
                            <span class="fw-medium" style="color: var(--lux-dark-blue);">{{ number_format($paymentStatus['guarantee_payment']->amount, 2, ',', ' ') }} €</span>
                        </div>
                        @php
                            $dueDate = \Carbon\Carbon::parse($paymentStatus['guarantee_payment']->due_date);
                            $isPast = $dueDate->isPast();
                            $isSoon = !$isPast && $dueDate->diffInDays(now()) <= 2;
                        @endphp
                        <p class="small mb-0 {{ $isPast ? 'text-danger' : ($isSoon ? 'text-warning' : 'text-lux-gray') }}">
                            <i class="fa-solid fa-clock me-1"></i>
                            @if($isPast)
                                En retard depuis le {{ $dueDate->format('d/m/Y') }}
                            @else
                                À régler pour le {{ $dueDate->format('d/m/Y') }}
                            @endif
                        </p>
                    </div>

                    @if($nextReservation->allowsClientOnlinePayment())
                        <a href="{{ route('espace-client.pay-deposit-guarantee', $nextReservation) }}" class="btn btn-lux-secondary w-100">
                            Régler ma caution
                        </a>
                    @else
                        <x-reservation-offline-payment-notice />
                    @endif
                </section>
            @elseif($nextReservation && $paymentStatus && $paymentStatus['remaining'] <= 0)
                <section class="widget-card mb-4">
                    <h3 class="h5 font-serif mb-4" style="color: var(--lux-dark-blue);">État des Paiements</h3>
                    <div class="text-center py-3">
                        <i class="fa-solid fa-check-circle fa-2x text-success mb-2"></i>
                        <p class="small text-lux-gray mb-0">Séjour entièrement payé</p>
                    </div>
                </section>
            @endif

            <!-- Messages Widget -->
            <section class="widget-card mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h3 class="h5 font-serif mb-0" style="color: var(--lux-dark-blue);">
                        Messagerie
                        @if($unreadMessagesCount > 0)
                            <span class="badge bg-danger ms-2" style="font-size: 0.6rem;">{{ $unreadMessagesCount }}</span>
                        @endif
                    </h3>
                    <a href="{{ route('espace-client.messages') }}" class="small text-lux-gold text-decoration-none">Tout voir</a>
                </div>
                
                <div>
                    @forelse($recentMessages as $message)
                        @php
                            $isFromUser = $message->sender_id === auth()->id();
                            $otherUser = $isFromUser ? $message->recipient : $message->sender;
                        @endphp
                        <div class="d-flex align-items-start gap-3 {{ !$loop->last ? 'pb-3 mb-3 border-bottom' : '' }}">
                            <div class="position-relative">
                                @if($otherUser && $otherUser->photo_url)
                                    <img src="{{ asset('storage/' . $otherUser->photo_url) }}" 
                                         class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" alt="{{ $otherUser->first_name }}">
                                @else
                                    <div class="rounded-circle bg-lux-dark-blue text-white d-flex align-items-center justify-content-center" 
                                         style="width: 40px; height: 40px; font-size: 0.75rem;">
                                        {{ $otherUser ? strtoupper(substr($otherUser->first_name ?? '', 0, 1) . substr($otherUser->last_name ?? '', 0, 1)) : 'SYS' }}
                                    </div>
                                @endif
                                @if(!$isFromUser && !$message->is_read)
                                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" 
                                          style="width: 10px; height: 10px;"></span>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-baseline mb-1">
                                    <h4 class="small fw-medium mb-0" style="color: var(--lux-dark-blue);">
                                        {{ $otherUser ? ($otherUser->first_name . ($otherUser->is_admin ? ' (Concierge)' : '')) : 'Support LUXÎLES' }}
                                    </h4>
                                    <span class="small text-muted" style="font-size: 0.625rem;">{{ $message->short_time_ago }}</span>
                                </div>
                                <p class="small text-lux-gray mb-0" style="font-size: 0.75rem;">
                                    {{ \Illuminate\Support\Str::limit($message->subject ?? $message->body, 60) }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <p class="small text-lux-gray text-center py-3 mb-0">Aucun message</p>
                    @endforelse
                </div>
            </section>

            <!-- Inspiration / Upsell -->
            <a href="{{ route('villas.index') }}" class="text-decoration-none d-block">
                <section class="inspiration-card position-relative">
                    <img src="https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop" 
                         alt="Villas aux Caraïbes">
                    <div class="inspiration-content">
                        <span class="text-lux-gold small fw-bold text-uppercase d-block mb-1" style="font-size: 0.75rem; letter-spacing: 0.05em;">Découverte</span>
                        <h3 class="text-white font-serif h5 mb-0">Envie d'une escapade aux Caraïbes ?</h3>
                    </div>
                </section>
            </a>
        </div>
    </div>
@endsection
