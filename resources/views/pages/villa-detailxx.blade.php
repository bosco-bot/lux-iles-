@extends('layouts.app')

@section('title', ($villa->name ?? 'Villa') . ' - ' . ($villa->island->name ?? '') . ' | LUXÎLES')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(to bottom, rgba(10,26,47,0.3) 0%, rgba(10,26,47,0.1) 50%, rgba(10,26,47,0.6) 100%);
    }
    .map-container iframe {
        filter: grayscale(1) contrast(1.2) opacity(0.8);
        transition: filter 0.3s;
    }
    .map-container:hover iframe {
        filter: grayscale(0) contrast(1) opacity(1);
    }
    .gallery-image {
        transition: transform 0.7s;
    }
    .gallery-group:hover .gallery-image {
        transform: scale(1.05);
    }
    /* Style des boutons du calendrier FullCalendar */
    .fc-button-primary {
        background-color: var(--lux-gold) !important;
        border-color: var(--lux-gold) !important;
        color: var(--lux-dark-blue) !important;
    }
    .fc-button-primary:hover {
        background-color: var(--lux-light-gold) !important;
        border-color: var(--lux-light-gold) !important;
        color: var(--lux-dark-blue) !important;
    }
    .fc-button-primary:not(:disabled):active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background-color: var(--lux-gold) !important;
        border-color: var(--lux-gold) !important;
        color: var(--lux-dark-blue) !important;
    }
    /* Style pour la sélection dans le calendrier */
    .fc-highlight {
        background-color: var(--lux-blue) !important;
        opacity: 1 !important;
    }
    
    /* Style pour les dates réservées/bloquées - Forcer l'application de la couleur */
    .fc-event.blocked-date,
    .fc-event.reserved-date {
        background-color: #CBAE821A !important;
        border-color: #CBAE821A !important;
        border: none !important;
    }
    
    /* Style pour les événements en arrière-plan */
    .fc-daygrid-event.fc-event {
        background-color: #CBAE821A !important;
    }
    
    /* Forcer la couleur sur les cellules de jour qui contiennent des événements bloqués */
    .fc-daygrid-day .fc-daygrid-day-bg {
        background-color: transparent !important;
    }
    
    /* Style pour les événements de type background */
    .fc-daygrid-event[data-event-display="background"] {
        background-color: #CBAE821A !important;
    }
    
    /* Responsivité pour la carte de réservation */
    @media (max-width: 991.98px) {
        .booking-card-container {
            margin-top: 0 !important;
        }
    }
</style>
@endpush

@section('content')

<!-- Gallery Section -->
<section id="gallery-hero" class="pt-5 pb-4" style="padding-top: 8rem; background-color: var(--lux-beige);">
    <div class="container-fluid px-0" style="max-width: 1920px;">
        <div class="row g-2 mx-0">
            <!-- Main Large Image -->
            <div class="col-12 col-lg-8 px-0 position-relative" style="height: 800px; overflow: hidden; cursor: pointer;" onmouseover="this.querySelector('.gallery-image').style.transform='scale(1.05)'" onmouseout="this.querySelector('.gallery-image').style.transform='scale(1)'">
                @php
                    $mainPhoto = $primaryPhoto ?? $villa->photos->first();
                @endphp
                @if($mainPhoto)
                    <img src="{{ asset('storage/' . $mainPhoto->file_path) }}" 
                         class="w-100 h-100 gallery-image" 
                         style="object-fit: cover; transition: transform 1s;" 
                         alt="{{ $mainPhoto->alt_text ?? $villa->name }}"
                         onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F8F8F6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'18\' fill=\'%238A96A6\'%3EImage non disponible%3C/text%3E%3C/svg%3E';">
                @else
                    <div class="w-100 h-100 d-flex align-items-center justify-content-center position-absolute top-0 start-0" style="background-color: var(--lux-beige);">
                        <div class="text-center">
                            <i class="fas fa-image fa-5x text-lux-greyBlue opacity-50 mb-3"></i>
                            <p class="text-lux-greyBlue opacity-75 small mb-0">Aucune photo disponible</p>
                        </div>
                    </div>
                @endif
                <div class="position-absolute inset-0 hero-gradient" style="opacity: 0.6;"></div>
                <div class="position-absolute bottom-0 start-0 p-4 text-white" style="z-index: 10;">
                    @if($villa->is_featured)
                        <span class="bg-lux-gold bg-opacity-90 text-white px-3 py-1 small fw-bold text-uppercase mb-3 d-inline-block rounded" style="letter-spacing: 0.1em;">Exclusivité</span>
                    @endif
                    <h1 class="text-white mb-2 font-serif" style="font-size: 2.5rem; font-family: 'Playfair Display', serif;">{{ $villa->name }}</h1>
                    <p class="text-white mb-0 opacity-90">
                        <i class="fas fa-location-dot text-lux-gold me-2"></i>
                        {{ $villa->address ?? ($villa->island->name ?? '') }}
                    </p>
                </div>
            </div>

            <!-- Side Grid Images -->
            <div class="col-12 col-lg-4 px-0">
                <div class="d-flex flex-column gap-2" style="height: 800px;">
                    @php
                        $allPhotos = $villa->photos;
                        $sidePhotos = $allPhotos->skip($mainPhoto ? 1 : 0)->take(2);
                    @endphp
                    @foreach($sidePhotos as $index => $photo)
                        <div class="position-relative flex-grow-1" style="overflow: hidden; cursor: pointer; height: {{ $index === 0 ? '50%' : '50%' }};" onmouseover="this.querySelector('.gallery-image').style.transform='scale(1.1)'; this.querySelector('.gallery-overlay').style.backgroundColor='transparent'" onmouseout="this.querySelector('.gallery-image').style.transform='scale(1)'; this.querySelector('.gallery-overlay').style.backgroundColor='rgba(0,0,0,0.1)'">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                 class="w-100 h-100 gallery-image" 
                                 style="object-fit: cover; transition: transform 0.7s;" 
                                 alt="{{ $photo->alt_text ?? $villa->name }}"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F8F8F6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'18\' fill=\'%238A96A6\'%3EImage non disponible%3C/text%3E%3C/svg%3E';">
                            <div class="position-absolute inset-0 gallery-overlay" style="background-color: rgba(0,0,0,0.1); transition: background-color 0.3s; pointer-events: none;"></div>
                            
                            @if($loop->last && $allPhotos->count() > 0)
                                <button class="position-absolute" style="bottom: 0.5rem; right: 1.5rem; z-index: 1000; background-color: rgba(255,255,255,0.95); color: var(--lux-dark-blue); padding: 0.625rem 1.25rem; border-radius: 0.375rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.2); font-size: 0.875rem; font-weight: 500; border: none; transition: all 0.3s; display: flex !important; align-items: center; gap: 0.5rem; pointer-events: auto; visibility: visible !important; opacity: 1 !important;" 
                                        onmouseover="this.style.backgroundColor='white';" 
                                        onmouseout="this.style.backgroundColor='rgba(255,255,255,0.95)';"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#photoGalleryModal"
                                        onclick="event.stopPropagation();">
                                    <i class="fas fa-images"></i> Voir les {{ $allPhotos->count() }} photos
                                </button>
                            @endif
                        </div>
                    @endforeach
                    @if($sidePhotos->isEmpty())
                        @for($i = 0; $i < 2; $i++)
                            <div class="position-relative flex-grow-1 d-flex align-items-center justify-content-center" style="background-color: var(--lux-beige); height: 50%;">
                                <div class="text-center">
                                    <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50 mb-2"></i>
                                    <p class="text-lux-greyBlue opacity-75 small mb-0" style="font-size: 0.75rem;">Aucune photo</p>
                                </div>
                                @if($i === 1 && $allPhotos->count() > 0)
                                    <button class="position-absolute" style="bottom: 0; right: 1.5rem; z-index: 10; background-color: rgba(255,255,255,0.9); color: var(--lux-dark-blue); padding: 0.625rem 1.25rem; border-radius: 0.375rem; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1); font-size: 0.875rem; font-weight: 500; border: none; transition: all 0.3s; display: flex; align-items: center; gap: 0.5rem;" 
                                            onmouseover="this.style.backgroundColor='white';" 
                                            onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)';"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#photoGalleryModal"
                                            onclick="event.stopPropagation();">
                                        <i class="fas fa-images"></i> Voir les {{ $allPhotos->count() }} photos
                                    </button>
                                @endif
                            </div>
                        @endfor
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Grid -->
<main class="container py-5">
    <div class="row g-5">
        <!-- LEFT COLUMN: Details -->
        <div class="col-12 col-lg-8">
            <!-- Key Stats Bar -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-4 py-4 border-top border-bottom mb-5" style="border-color: rgba(138, 150, 166, 0.2);">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-users text-lux-gold"></i>
                    </div>
                    <div>
                        <span class="d-block small text-lux-greyBlue text-uppercase" style="letter-spacing: 0.1em;">Capacité</span>
                        <span class="text-lux-dark-blue fw-medium">{{ $villa->max_capacity }} Personnes</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-bed text-lux-gold"></i>
                    </div>
                    <div>
                        <span class="d-block small text-lux-greyBlue text-uppercase" style="letter-spacing: 0.1em;">Chambres</span>
                        <span class="text-lux-dark-blue fw-medium">{{ $villa->bedrooms }} {{ $villa->bedrooms > 1 ? 'Suites' : 'Suite' }}</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-shower text-lux-gold"></i>
                    </div>
                    <div>
                        <span class="d-block small text-lux-greyBlue text-uppercase" style="letter-spacing: 0.1em;">Salles de bain</span>
                        <span class="text-lux-dark-blue fw-medium">{{ $villa->bathrooms }} {{ $villa->bathrooms > 1 ? 'Privatives' : 'Privative' }}</span>
                    </div>
                </div>
                @if($villa->surface_area)
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-ruler-combined text-lux-gold"></i>
                    </div>
                    <div>
                        <span class="d-block small text-lux-greyBlue text-uppercase" style="letter-spacing: 0.1em;">Surface</span>
                        <span class="text-lux-dark-blue fw-medium">{{ number_format($villa->surface_area, 0, ',', ' ') }} m²</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Description -->
            <section id="description" class="mb-5">
                <h2 class="h2 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.875rem;">{{ $villa->short_description ?? 'Un havre de paix face à l\'océan' }}</h2>
                <div class="text-lux-greyBlue lh-lg" style="font-weight: 300; line-height: 1.75;">
                    {!! nl2br(e($villa->description ?? 'Découvrez cette villa exceptionnelle dans les Caraïbes.')) !!}
                </div>
            </section>

            <!-- Amenities -->
            <section id="amenities" class="mb-5">
                <h3 class="h3 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Équipements & Services</h3>
                <div class="row g-4">
                    @forelse($villa->equipments as $equipment)
                        @php
                            // Mapping des icônes de la base de données vers FontAwesome
                            $iconMap = [
                                'pool' => 'fa-person-swimming',
                                'jacuzzi' => 'fa-hot-tub',
                                'wifi' => 'fa-wifi',
                                'ac' => 'fa-snowflake',
                                'parking' => 'fa-square-parking',
                                'washing-machine' => 'fa-soap',
                                'dishwasher' => 'fa-spray-can',
                                'tv' => 'fa-tv',
                                'kitchen' => 'fa-utensils',
                                'beach' => 'fa-umbrella-beach',
                                'sea-view' => 'fa-water',
                                'alarm' => 'fa-shield-halved',
                                'safe' => 'fa-vault',
                            ];
                            
                            // Récupérer l'icône depuis la base de données ou utiliser le mapping
                            $iconClass = 'fa-check'; // Par défaut
                            if ($equipment->icon) {
                                // Si l'icône commence par 'fa-', utiliser directement
                                if (strpos($equipment->icon, 'fa-') === 0) {
                                    $iconClass = $equipment->icon;
                                } else {
                                    // Sinon, utiliser le mapping
                                    $iconClass = $iconMap[$equipment->icon] ?? 'fa-check';
                                }
                            }
                        @endphp
                        <div class="col-6 col-md-4 d-flex align-items-center gap-3">
                            <i class="fas {{ $iconClass }} text-lux-gold" style="width: 24px; text-align: center;"></i>
                            <span class="text-lux-dark-blue">{{ $equipment->name }}</span>
                        </div>
                    @empty
                        <div class="col-12">
                            <p class="text-lux-greyBlue">Aucun équipement renseigné pour le moment.</p>
                        </div>
                    @endforelse
                </div>
            </section>

            <!-- Location Map -->
            @if($villa->latitude && $villa->longitude)
            <section id="location" class="mb-5">
                <h3 class="h3 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Localisation</h3>
                <div class="w-100 rounded overflow-hidden border map-container position-relative" style="height: 400px; border-color: rgba(138, 150, 166, 0.2);">
                    <iframe src="https://maps.google.com/maps?q={{ $villa->latitude }},{{ $villa->longitude }}&hl=fr&z=15&output=embed" 
                            width="100%" 
                            height="100%" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="position-absolute bottom-0 start-0 m-3 bg-white px-3 py-2 rounded shadow small">
                        <p class="fw-medium text-lux-dark-blue mb-0">{{ $villa->name }}</p>
                        <p class="small text-lux-greyBlue mb-0">{{ $villa->island->name ?? '' }}</p>
                        @if($villa->address)
                        <p class="small text-lux-greyBlue mb-0 mt-1"><i class="fas fa-map-marker-alt text-lux-gold me-1"></i>{{ $villa->address }}</p>
                        @endif
                    </div>
                </div>
                <div class="mt-4 d-flex flex-wrap gap-4 small text-lux-greyBlue">
                    <span><i class="fas fa-plane-up text-lux-gold me-2"></i> 10 min de l'aéroport</span>
                    <span><i class="fas fa-umbrella-beach text-lux-gold me-2"></i> 5 min de la plage</span>
                    <span><i class="fas fa-bag-shopping text-lux-gold me-2"></i> 2 min des boutiques</span>
                </div>
            </section>
            @endif

            <!-- Availability Calendar -->
            <section id="availability" class="mb-5">
                <h3 class="h3 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Disponibilités</h3>
                <div class="bg-white p-4 rounded border" style="border-color: rgba(138, 150, 166, 0.1);">
                    <div id="availability-calendar"></div>
                    <div class="mt-4 d-flex flex-wrap gap-4 small">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px; background-color: var(--lux-blue); display: block;"></span>
                            <span class="text-lux-greyBlue">Sélectionné</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px; background-color: rgba(203, 174, 130, 0.1); display: block;"></span>
                            <span class="text-lux-greyBlue">Réservé</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-inline-block rounded" style="width: 12px; height: 12px; min-width: 12px; min-height: 12px; background-color: rgba(44, 62, 80, 0.1); display: block;"></span>
                            <span class="text-lux-greyBlue">Disponible</span>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reviews -->
            <section id="reviews" class="mb-5">
                <h3 class="h3 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Avis des voyageurs</h3>
                <div class="bg-white p-4 rounded border mb-3" style="border-color: rgba(138, 150, 166, 0.1);">
                    <p class="text-lux-greyBlue mb-0">Aucun avis pour le moment.</p>
                </div>
            </section>
        </div>

        <!-- RIGHT COLUMN: Booking Card -->
        <div class="col-12 col-lg-4 booking-card-container" style="margin-top: -100px;">
            <div class="position-sticky" style="top: 8rem;">
                <div class="bg-white rounded shadow-lg border p-4" style="border-color: rgba(138, 150, 166, 0.1);">
                    <!-- Price -->
                    <div class="mb-4 pb-4 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                        <div class="d-flex align-items-baseline gap-2 mb-2">
                            <span class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif; font-size: 2.25rem;">{{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €</span>
                            <span class="text-lux-greyBlue">/ nuit</span>
                        </div>
                        <p class="small text-lux-greyBlue mb-0">Tarif haute saison (Déc - Avr)</p>
                    </div>

                    <!-- Booking Form -->
                    <form class="mb-4" id="booking-form">
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-lux-dark-blue mb-2 d-block">Arrivée</label>
                            <input type="date" id="check-in-date" class="form-control w-100 px-3 py-2 border rounded" style="border-color: rgba(138, 150, 166, 0.3);" min="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-lux-dark-blue mb-2 d-block">Départ</label>
                            <input type="date" id="check-out-date" class="form-control w-100 px-3 py-2 border rounded" style="border-color: rgba(138, 150, 166, 0.3);" min="{{ date('Y-m-d', strtotime('+1 day')) }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-medium text-lux-dark-blue mb-2 d-block">Voyageurs</label>
                            <select id="number-of-guests" class="form-select w-100 px-3 py-2 border rounded" style="border-color: rgba(138, 150, 166, 0.3);">
                                @for($i = 1; $i <= $villa->max_capacity; $i++)
                                    <option value="{{ $i }}" {{ $i == 2 ? 'selected' : '' }}>{{ $i }} {{ $i > 1 ? 'personnes' : 'personne' }}</option>
                                @endfor
                            </select>
                        </div>
                    </form>

                    <!-- Price Details -->
                    <div class="mb-4 pb-4 border-bottom small" style="border-color: rgba(138, 150, 166, 0.1);" id="price-details">
                        <div class="d-flex justify-content-between text-lux-dark-blue mb-2">
                            <span id="nights-text">-</span>
                            <span id="base-price">-</span>
                        </div>
                        <div class="d-flex justify-content-between text-lux-dark-blue mb-2">
                            <span>Frais de service</span>
                            <span id="service-fee">-</span>
                        </div>
                        <div class="d-flex justify-content-between text-lux-dark-blue">
                            <span>Taxes locales</span>
                            <span id="local-taxes">-</span>
                        </div>
                    </div>

                    <!-- Total -->
                    <div class="d-flex justify-content-between align-items-center mb-4 fw-medium" style="font-size: 1.125rem;">
                        <span class="text-lux-dark-blue">Total</span>
                        <span class="text-lux-dark-blue" id="total-price">-</span>
                    </div>

                    <!-- CTA Button -->
                    <button class="btn btn-lux-primary w-100 py-3 mb-3 fw-medium" id="book-villa-btn" style="transition: background-color 0.3s;">
                        Réserver cette villa
                    </button>

                    <p class="small text-center text-lux-greyBlue mb-4">Annulation gratuite jusqu'à 30 jours avant l'arrivée</p>

                    <!-- Contact Options -->
                    <div class="border-top pt-4" style="border-color: rgba(138, 150, 166, 0.1);">
                        <button class="btn btn-outline-lux-primary w-100 mb-2 py-2 fw-medium" style="transition: all 0.3s;">
                            <i class="fas fa-phone me-2"></i> Appeler le concierge
                        </button>
                        <button class="btn btn-outline-lux-primary w-100 py-2 fw-medium" style="transition: all 0.3s;">
                            <i class="fas fa-envelope me-2"></i> Demander un devis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Photo Gallery Modal -->
@if($villa->photos->count() > 0)
<div class="modal fade" id="photoGalleryModal" tabindex="-1" aria-labelledby="photoGalleryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header border-0">
                <h5 class="modal-title text-white" id="photoGalleryModalLabel">Galerie Photos - {{ $villa->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-2">
                    @foreach($villa->photos as $photo)
                        <div class="col-6 col-md-4 col-lg-3">
                            <img src="{{ asset('storage/' . $photo->file_path) }}" 
                                 class="w-100 rounded" 
                                 style="height: 200px; object-fit: cover; cursor: pointer;" 
                                 alt="{{ $photo->alt_text ?? $villa->name }}"
                                 onclick="window.open('{{ asset('storage/' . $photo->file_path) }}', '_blank')"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'800\' height=\'600\'%3E%3Crect width=\'800\' height=\'600\' fill=\'%23F8F8F6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' dominant-baseline=\'middle\' text-anchor=\'middle\' font-family=\'Arial\' font-size=\'18\' fill=\'%238A96A6\'%3EImage non disponible%3C/text%3E%3C/svg%3E';">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Générer la liste des dates bloquées
    @php
        $blockedDates = [];
        foreach($villa->availabilityBlocks as $block) {
            $start = new \DateTime($block->start_date);
            $end = new \DateTime($block->end_date);
            $end->modify('+1 day');
            $current = clone $start;
            while ($current < $end) {
                $blockedDates[] = $current->format('Y-m-d');
                $current->modify('+1 day');
            }
        }
        if(isset($reservations)) {
            foreach($reservations as $reservation) {
                $start = new \DateTime($reservation->check_in_date);
                $end = new \DateTime($reservation->check_out_date);
                $end->modify('+1 day');
                $current = clone $start;
                while ($current < $end) {
                    $blockedDates[] = $current->format('Y-m-d');
                    $current->modify('+1 day');
                }
            }
        }
    @endphp
    
    const blockedDatesList = @json(array_unique($blockedDates));
    
    // Fonction pour vérifier si une date est bloquée
    function isDateBlocked(dateStr) {
        return blockedDatesList.includes(dateStr);
    }
    
    // Fonction pour vérifier si une période chevauche des dates bloquées
    function isPeriodBlocked(startStr, endStr) {
        const start = new Date(startStr);
        const end = new Date(endStr);
        const current = new Date(start);
        
        while (current < end) {
            const dateStr = current.toISOString().split('T')[0];
            if (blockedDatesList.includes(dateStr)) {
                return true;
            }
            current.setDate(current.getDate() + 1);
        }
        return false;
    }
    
    const calendarEl = document.getElementById('availability-calendar');
    if (calendarEl) {
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            locale: 'fr',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: ''
            },
            buttonText: {
                today: 'Aujourd\'hui'
            },
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true,
            events: [
                @foreach($villa->availabilityBlocks as $block)
                {
                    title: 'Réservé',
                    start: '{{ $block->start_date }}',
                    end: '{{ date('Y-m-d', strtotime($block->end_date . ' +1 day')) }}',
                    display: 'background',
                    backgroundColor: '#CBAE821A',
                    borderColor: '#CBAE821A',
                    textColor: '#CBAE82',
                    classNames: ['blocked-date'],
                    editable: false,
                    selectable: false,
                    rendering: 'background'
                },
                @endforeach
                @if(isset($reservations))
                    @foreach($reservations as $reservation)
                    {
                        title: 'Réservé',
                        start: '{{ $reservation->check_in_date }}',
                        end: '{{ date('Y-m-d', strtotime($reservation->check_out_date . ' +1 day')) }}',
                        display: 'background',
                        backgroundColor: '#CBAE821A',
                        borderColor: '#CBAE821A',
                        textColor: '#CBAE82',
                        classNames: ['reserved-date'],
                        editable: false,
                        selectable: false,
                        rendering: 'background'
                    },
                    @endforeach
                @endif
            ],
            select: function(info) {
                // Mettre à jour les champs de date quand une période est sélectionnée
                const checkInInput = document.getElementById('check-in-date');
                const checkOutInput = document.getElementById('check-out-date');
                
                if (info.startStr && info.endStr) {
                    // Calculer la date de fin (exclure le dernier jour car FullCalendar inclut le jour suivant)
                    const endDate = new Date(info.endStr);
                    endDate.setDate(endDate.getDate() - 1);
                    const endDateStr = endDate.toISOString().split('T')[0];
                    
                    checkInInput.value = info.startStr;
                    checkOutInput.value = endDateStr;
                    calculatePrice();
                }
            },
            dateClick: function(info) {
                // Empêcher le clic sur les dates bloquées/réservées
                if (isDateBlocked(info.dateStr)) {
                    alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                    return; // Ne rien faire si la date est bloquée/réservée
                }
                
                const checkInInput = document.getElementById('check-in-date');
                const checkOutInput = document.getElementById('check-out-date');
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const clickedDate = new Date(info.dateStr);
                clickedDate.setHours(0, 0, 0, 0);
                
                // Empêcher la sélection de dates passées
                if (clickedDate < today) {
                    return;
                }
                
                // Si aucune date n'est sélectionnée OU les deux dates sont déjà sélectionnées
                if (!checkIn || (checkIn && checkOut)) {
                    // Nouvelle sélection : définir la date d'arrivée
                    checkInInput.value = info.dateStr;
                    checkOutInput.value = '';
                    calendar.select(info.dateStr, info.dateStr);
                } 
                // Si seule la date d'arrivée est sélectionnée
                else if (checkIn && !checkOut) {
                    const startDate = new Date(checkIn);
                    
                    // Si on clique sur une date avant l'arrivée, réinitialiser et recommencer
                    if (clickedDate < startDate) {
                        checkInInput.value = info.dateStr;
                        checkOutInput.value = '';
                        calendar.select(info.dateStr, info.dateStr);
                    } 
                    // Si on clique sur la même date, réinitialiser
                    else if (clickedDate.getTime() === startDate.getTime()) {
                        checkInInput.value = '';
                        checkOutInput.value = '';
                        calendar.unselect();
                    }
                    // Sinon, définir la date de départ
                    else {
                        // Vérifier que la période n'est pas bloquée
                        if (isPeriodBlocked(checkIn, info.dateStr)) {
                            alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                            return;
                        }
                        checkOutInput.value = info.dateStr;
                        calendar.select(checkIn, info.dateStr);
                        calculatePrice();
                    }
                }
            },
            selectAllow: function(selectInfo) {
                // Empêcher la sélection de dates bloquées/réservées
                const selectStartStr = selectInfo.start.toISOString().split('T')[0];
                const selectEndStr = selectInfo.end.toISOString().split('T')[0];
                
                // Vérifier si la période sélectionnée chevauche des dates bloquées
                if (isPeriodBlocked(selectStartStr, selectEndStr)) {
                    return false; // Bloquer la sélection
                }
                
                // Vérifier que la date de début n'est pas dans le passé
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const selectStart = new Date(selectInfo.start);
                selectStart.setHours(0, 0, 0, 0);
                if (selectStart < today) {
                    return false;
                }
                
                return true;
            },
        });
        calendar.render();
        
        // Forcer l'application de la couleur de fond pour les dates réservées/bloquées
        setTimeout(function() {
            // Appliquer la couleur sur tous les événements bloqués/réservés
            document.querySelectorAll('.fc-event.blocked-date, .fc-event.reserved-date').forEach(function(el) {
                el.style.backgroundColor = '#CBAE821A';
                el.style.borderColor = '#CBAE821A';
            });
            
            // Appliquer la couleur sur les cellules de jour pour les dates bloquées
            blockedDatesList.forEach(function(dateStr) {
                const dayEl = document.querySelector('.fc-daygrid-day[data-date="' + dateStr + '"]');
                if (dayEl) {
                    // Appliquer sur le fond de la cellule
                    const bgEl = dayEl.querySelector('.fc-daygrid-day-bg');
                    if (bgEl) {
                        bgEl.style.backgroundColor = '#CBAE821A';
                    }
                    // Appliquer aussi directement sur la cellule
                    dayEl.style.backgroundColor = '#CBAE821A';
                    // Désactiver le clic sur les dates bloquées
                    dayEl.style.pointerEvents = 'none';
                    dayEl.style.cursor = 'not-allowed';
                }
            });
        }, 200);
        
        // Fonction pour calculer le prix
        function calculatePrice() {
            const checkIn = document.getElementById('check-in-date').value;
            const checkOut = document.getElementById('check-out-date').value;
            
            if (!checkIn || !checkOut) {
                document.getElementById('nights-text').textContent = '-';
                document.getElementById('base-price').textContent = '-';
                document.getElementById('service-fee').textContent = '-';
                document.getElementById('local-taxes').textContent = '-';
                document.getElementById('total-price').textContent = '-';
                return;
            }
            
            const startDate = new Date(checkIn);
            const endDate = new Date(checkOut);
            const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            
            if (nights <= 0) {
                return;
            }
            
            const basePricePerNight = {{ $villa->base_price_per_night }};
            const serviceFeePercentage = {{ $villa->service_fee_percentage ?? 5 }};
            const localTaxRate = 0.03; // 3%
            
            const basePrice = basePricePerNight * nights;
            const serviceFee = basePrice * (serviceFeePercentage / 100);
            const localTaxes = basePrice * localTaxRate;
            const total = basePrice + serviceFee + localTaxes;
            
            // Formater les nombres
            function formatPrice(price) {
                return new Intl.NumberFormat('fr-FR', { 
                    style: 'decimal', 
                    minimumFractionDigits: 0,
                    maximumFractionDigits: 0 
                }).format(price) + ' €';
            }
            
            document.getElementById('nights-text').textContent = formatPrice(basePricePerNight) + ' × ' + nights + ' nuit' + (nights > 1 ? 's' : '');
            document.getElementById('base-price').textContent = formatPrice(basePrice);
            document.getElementById('service-fee').textContent = formatPrice(serviceFee);
            document.getElementById('local-taxes').textContent = formatPrice(localTaxes);
            document.getElementById('total-price').textContent = formatPrice(total);
        }
        
        // Écouter les changements sur les champs de date pour mettre à jour le calendrier
        document.getElementById('check-in-date').addEventListener('change', function() {
            const checkIn = this.value;
            const checkOut = document.getElementById('check-out-date').value;
            
            // Vérifier si la date d'arrivée est bloquée
            if (checkIn && isDateBlocked(checkIn)) {
                alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                this.value = '';
                calendar.unselect();
                return;
            }
            
            // Si on a une date de départ, vérifier que la période n'est pas bloquée
            if (checkIn && checkOut) {
                if (isPeriodBlocked(checkIn, checkOut)) {
                    alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                    this.value = '';
                    document.getElementById('check-out-date').value = '';
                    calendar.unselect();
                    return;
                }
            }
            
            // Mettre à jour la sélection du calendrier
            if (checkIn && checkOut) {
                calendar.select(checkIn, checkOut);
                calculatePrice();
            } else if (checkIn) {
                calendar.select(checkIn, checkIn);
            } else {
                calendar.unselect();
            }
            
            // Mettre à jour la date minimum pour le départ
            if (checkIn) {
                const minDate = new Date(checkIn);
                minDate.setDate(minDate.getDate() + 1);
                document.getElementById('check-out-date').min = minDate.toISOString().split('T')[0];
            }
        });
        
        document.getElementById('check-out-date').addEventListener('change', function() {
            const checkIn = document.getElementById('check-in-date').value;
            const checkOut = this.value;
            
            // Vérifier si la date de départ est bloquée
            if (checkOut && isDateBlocked(checkOut)) {
                alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                this.value = '';
                calendar.unselect();
                return;
            }
            
            // Si on a une date d'arrivée, vérifier que la période n'est pas bloquée
            if (checkIn && checkOut) {
                if (isPeriodBlocked(checkIn, checkOut)) {
                    alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                    document.getElementById('check-in-date').value = '';
                    this.value = '';
                    calendar.unselect();
                    return;
                }
            }
            
            // Mettre à jour la sélection du calendrier
            if (checkIn && checkOut) {
                calendar.select(checkIn, checkOut);
                calculatePrice();
            } else if (!checkIn && checkOut) {
                // Si pas d'arrivée mais un départ, réinitialiser
                this.value = '';
            }
        });
        
        // Gestionnaire pour le bouton "Réserver cette villa"
        document.getElementById('book-villa-btn').addEventListener('click', function() {
            const checkIn = document.getElementById('check-in-date').value;
            const checkOut = document.getElementById('check-out-date').value;
            const numberOfGuests = document.getElementById('number-of-guests').value;
            
            // Vérifier que les dates sont sélectionnées
            if (!checkIn || !checkOut) {
                alert('Veuillez sélectionner vos dates d\'arrivée et de départ.');
                return;
            }
            
            // Vérifier que la période n'est pas bloquée
            if (isPeriodBlocked(checkIn, checkOut)) {
                alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                return;
            }
            
            // Vérifier le séjour minimum
            const startDate = new Date(checkIn);
            const endDate = new Date(checkOut);
            const nights = Math.ceil((endDate - startDate) / (1000 * 60 * 60 * 24));
            const minStay = {{ $villa->minimum_stay_nights ?? 3 }};
            
            if (nights < minStay) {
                alert('Le séjour minimum est de ' + minStay + ' nuit' + (minStay > 1 ? 's' : '') + '.');
                return;
            }
            
            // Rediriger vers la page de réservation avec les paramètres
            const villaId = {{ $villa->id }};
            const params = new URLSearchParams({
                villa_id: villaId,
                check_in: checkIn,
                check_out: checkOut,
                guests: numberOfGuests
            });
            
            window.location.href = '{{ route("bookings.create") }}?' + params.toString();
        });
    }
});
</script>
@endpush
