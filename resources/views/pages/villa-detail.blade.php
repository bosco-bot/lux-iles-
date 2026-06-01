@extends('layouts.app')

@section('title', ($villa->name ?? 'Villa') . ' - ' . ($villa->island->name ?? '') . ' | LUXÎLES')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
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

            <!-- Pricing Section -->
            @if($villa->seasonalPrices->count() > 0)
            <section id="pricing" class="mb-5">
                <h3 class="h3 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Tarification saisonnière</h3>
                <div class="bg-white p-4 rounded border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1);">
                    <div class="table-responsive">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr class="border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                                    <th class="small text-uppercase text-lux-greyBlue fw-semibold pb-3" style="letter-spacing: 0.1em;">Saison</th>
                                    <th class="small text-uppercase text-lux-greyBlue fw-semibold pb-3" style="letter-spacing: 0.1em;">Période</th>
                                    <th class="small text-uppercase text-lux-greyBlue fw-semibold pb-3 text-end" style="letter-spacing: 0.1em;">Prix / nuit</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($villa->seasonalPrices as $seasonalPrice)
                                <tr class="align-middle">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: rgba(203, 174, 130, 0.1);">
                                                <i class="fas fa-calendar-star text-lux-gold small"></i>
                                            </div>
                                            <span class="fw-medium text-lux-dark-blue">{{ $seasonalPrice->season->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-lux-greyBlue">
                                        {{ $seasonalPrice->season->period }}
                                    </td>
                                    <td class="py-3 text-end fw-bold text-lux-dark-blue">
                                        {{ number_format($seasonalPrice->price_per_night, 0, ',', ' ') }} {{ $seasonalPrice->currency ?? '€' }}
                                    </td>
                                </tr>
                                @endforeach
                                <tr class="align-middle border-top" style="border-color: rgba(138, 150, 166, 0.1);">
                                    <td class="py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; background-color: rgba(138, 150, 166, 0.05);">
                                                <i class="fas fa-tag text-lux-greyBlue small"></i>
                                            </div>
                                            <span class="fw-medium text-lux-greyBlue">Tarif de base</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-lux-greyBlue italic">Périodes non listées</td>
                                    <td class="py-3 text-end fw-bold text-lux-greyBlue">
                                        {{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
            @endif

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

            <!-- Reviews §3.4 CDC -->
            <section id="reviews" class="mb-5">
                <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
                    <h3 class="h3 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">Avis des voyageurs</h3>
                    @if($reviewsCount > 0)
                        <div class="d-flex align-items-center gap-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fa-solid fa-star {{ $i <= round($averageRating) ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                            @endfor
                            <span class="fw-semibold text-lux-dark-blue">{{ number_format($averageRating, 1, ',', ' ') }}</span>
                            <span class="text-lux-greyBlue small">({{ $reviewsCount }} avis)</span>
                        </div>
                    @endif
                </div>

                @forelse($publishedReviews as $review)
                    <div class="bg-white p-4 rounded border mb-3" style="border-color: rgba(138, 150, 166, 0.1);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="fw-medium text-lux-dark-blue">{{ $review->user->first_name }}</span>
                                @if($review->published_at)
                                    <span class="text-lux-greyBlue small ms-2">{{ $review->published_at->format('F Y') }}</span>
                                @endif
                            </div>
                            <div>
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fa-solid fa-star small {{ $i <= $review->rating ? 'text-warning' : 'text-muted opacity-25' }}"></i>
                                @endfor
                            </div>
                        </div>
                        <p class="text-lux-greyBlue mb-0" style="white-space: pre-line;">{{ $review->comment }}</p>
                        @if($review->admin_response)
                            <div class="mt-3 pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.15) !important;">
                                <p class="small fw-medium text-lux-dark-blue mb-1"><i class="fa-solid fa-reply me-1 text-lux-gold"></i> Réponse LUXÎLES</p>
                                <p class="small text-lux-greyBlue mb-0" style="white-space: pre-line;">{{ $review->admin_response }}</p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white p-4 rounded border mb-3" style="border-color: rgba(138, 150, 166, 0.1);">
                        <p class="text-lux-greyBlue mb-0">Aucun avis publié pour le moment.</p>
                    </div>
                @endforelse
            </section>
        </div>

        <!-- RIGHT COLUMN: Booking Card -->
        <div class="col-12 col-lg-4 booking-card-container" style="margin-top: -100px;">
            <div class="position-sticky" style="top: 8rem;">
                <div class="bg-white rounded shadow-lg border p-4" style="border-color: rgba(138, 150, 166, 0.1);">
                    <!-- Price -->
                    @php
                        $now = new \DateTime();
                        $currentPrice = $villa->getPriceForDate($now);
                        $currentSeason = null;
                        foreach($villa->seasonalPrices as $sp) {
                            if($villa->isDateInSeason($now, $sp->season)) {
                                $currentSeason = $sp->season;
                                break;
                            }
                        }
                    @endphp
                    <div class="mb-4 pb-4 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                        <div class="d-flex align-items-baseline gap-2 mb-1">
                            <span class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif; font-size: 2.25rem;">{{ number_format($currentPrice, 0, ',', ' ') }} €</span>
                            <span class="text-lux-greyBlue">/ nuit</span>
                        </div>
                        @if($currentSeason)
                            <p class="small text-lux-gold mb-0 fw-medium">
                                <i class="fas fa-sparkles me-1"></i> Tarif {{ $currentSeason->name }} ({{ $currentSeason->period }})
                            </p>
                        @elseif($villa->seasonalPrices->count() > 0)
                            <p class="small text-lux-greyBlue mb-0">Tarif standard (hors saison)</p>
                        @else
                            <p class="small text-lux-greyBlue mb-0">Tarif par nuit</p>
                        @endif
                    </div>

                    <!-- Booking Form -->
                    <form class="mb-4" id="booking-form">
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-lux-dark-blue mb-2 d-block">Arrivée</label>
                            <input type="text" id="check-in-date" class="form-control w-100 px-3 py-2 border rounded" style="border-color: rgba(138, 150, 166, 0.3);" autocomplete="off" placeholder="YYYY-MM-DD" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-medium text-lux-dark-blue mb-2 d-block">Départ</label>
                            <input type="text" id="check-out-date" class="form-control w-100 px-3 py-2 border rounded" style="border-color: rgba(138, 150, 166, 0.3);" autocomplete="off" placeholder="YYYY-MM-DD" required>
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
                        <button class="btn btn-outline-lux-primary w-100 mb-2 py-2 fw-medium" style="transition: all 0.3s;" onclick="openConciergeModal()">
                            <i class="fas fa-phone me-2"></i> Appeler le concierge
                        </button>
                        <button class="btn btn-outline-lux-primary w-100 py-2 fw-medium" style="transition: all 0.3s;" onclick="openQuoteModal()">
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Helper pour formater sans décalage de fuseau
    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        return `${year}-${month}-${day}`;
    }

    // Empêcher les boucles de synchronisation (Flatpickr <-> FullCalendar)
    let isSyncingFromCalendar = false;
    let isSyncingFromPicker = false;

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
        $blockedDates = array_unique($blockedDates);
        $blockedDates = array_values($blockedDates); // Réindexer le tableau
    @endphp
    
    // S'assurer que blockedDatesList est toujours un tableau
    const blockedDatesList = @json($blockedDates) || [];
    
    // Fonction pour vérifier si une date est bloquée
    function isDateBlocked(dateStr) {
        if (!Array.isArray(blockedDatesList) || blockedDatesList.length === 0) {
            return false;
        }
        return blockedDatesList.includes(dateStr);
    }
    
    // Fonction pour vérifier si une période chevauche des dates bloquées
    function isPeriodBlocked(startStr, endStr) {
        const startParts = startStr.split('-');
        const endParts = endStr.split('-');
        const start = new Date(parseInt(startParts[0]), parseInt(startParts[1]) - 1, parseInt(startParts[2]));
        const end = new Date(parseInt(endParts[0]), parseInt(endParts[1]) - 1, parseInt(endParts[2]));
        const current = new Date(start);

        while (current < end) {
            const year = current.getFullYear();
            const month = String(current.getMonth() + 1).padStart(2, '0');
            const day = String(current.getDate()).padStart(2, '0');
            const dateStr = `${year}-${month}-${day}`;
            if (blockedDatesList.includes(dateStr)) {
                return true;
            }
            current.setDate(current.getDate() + 1);
        }
        return false;
    }
    
    const calendarEl = document.getElementById('availability-calendar');
    if (calendarEl) {
        let checkInPicker = null;
        let checkOutPicker = null;

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
                    // Debug: afficher les valeurs reçues
                    console.log('FullCalendar select:', {
                        startStr: info.startStr,
                        endStr: info.endStr
                    });

                    // OPTION 1: Utiliser endStr directement (sans soustraction)
                    // Si la sélection visuelle du 23 au 30 donne endStr = '2026-01-31',
                    // alors la vraie date de fin devrait être endStr - 1 = '2026-01-30'
                    const endParts = info.endStr.split('-');
                    const endDate = new Date(parseInt(endParts[0]), parseInt(endParts[1]) - 1, parseInt(endParts[2]));
                    endDate.setDate(endDate.getDate() - 1);
                    const endDateStr = endDate.getFullYear() + '-' +
                        String(endDate.getMonth() + 1).padStart(2, '0') + '-' +
                        String(endDate.getDate()).padStart(2, '0');

                    // OPTION 2: Si vous voulez que la date affichée corresponde exactement à la sélection visuelle,
                    // commentez la ligne endDate.setDate(endDate.getDate() - 1); ci-dessus

                    console.log('Selection:', info.startStr, 'to', endDateStr, '(FullCalendar endStr:', info.endStr, ')');

                    console.log('Calculated end date:', endDateStr);

                    // Si la sélection vient du picker (formulaire), éviter la boucle
                    if (isSyncingFromPicker) {
                        return;
                    }

                    // Mettre à jour le formulaire via Flatpickr sans retrigger (plus fluide)
                    isSyncingFromCalendar = true;
                    if (checkInPicker) {
                        checkInPicker.setDate(info.startStr, false);
                    } else {
                        checkInInput.value = info.startStr;
                    }
                    if (checkOutPicker) {
                        checkOutPicker.setDate(endDateStr, false);
                    } else {
                        checkOutInput.value = endDateStr;
                    }
                    isSyncingFromCalendar = false;

                    // Une seule validation + recalcul prix
                    validateAndUpdateDates(info.startStr, endDateStr, 'check-out');
                }
            },
            dateClick: function(info) {
                console.log('FullCalendar dateClick:', info.dateStr);

                // Empêcher le clic sur les dates bloquées/réservées
                if (isDateBlocked(info.dateStr)) {
                    alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                    return; // Ne rien faire si la date est bloquée/réservée
                }

                const checkInInput = document.getElementById('check-in-date');
                const checkOutInput = document.getElementById('check-out-date');
                const checkIn = checkInInput.value;
                const checkOut = checkOutInput.value;

                console.log('Current field values:', { checkIn, checkOut });
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const dateParts = info.dateStr.split('-');
                const clickedDate = new Date(parseInt(dateParts[0]), parseInt(dateParts[1]) - 1, parseInt(dateParts[2]));
                
                // Empêcher la sélection de dates passées
                if (clickedDate < today) {
                    return;
                }
                
                // Si aucune date n'est sélectionnée OU les deux dates sont déjà sélectionnées
                if (!checkIn || (checkIn && checkOut)) {
                    console.log('Branch 1: Nouvelle sélection d\'arrivée');
                    isSyncingFromCalendar = true;
                    if (checkInPicker) {
                        checkInPicker.setDate(info.dateStr, false);
                    } else {
                        checkInInput.value = info.dateStr;
                    }
                    if (checkOutPicker) {
                        checkOutPicker.clear();
                    } else {
                        checkOutInput.value = '';
                    }
                    isSyncingFromCalendar = false;

                    validateAndUpdateDates(info.dateStr, '', 'check-in');
                }
                // Si seule la date d'arrivée est sélectionnée
                else if (checkIn && !checkOut) {
                    console.log('Branch 2: Sélection de départ');
                    const checkInParts = checkIn.split('-');
                    const startDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));

                    // Si on clique sur une date avant l'arrivée, réinitialiser et recommencer
                    if (clickedDate < startDate) {
                        console.log('Branch 2a: Date avant arrivée - réinitialisation');
                        isSyncingFromCalendar = true;
                        if (checkInPicker) {
                            checkInPicker.setDate(info.dateStr, false);
                        } else {
                            checkInInput.value = info.dateStr;
                        }
                        if (checkOutPicker) {
                            checkOutPicker.clear();
                        } else {
                            checkOutInput.value = '';
                        }
                        isSyncingFromCalendar = false;

                        validateAndUpdateDates(info.dateStr, '', 'check-in');
                    } 
                    // Si on clique sur la même date, réinitialiser
                    else if (clickedDate.getTime() === startDate.getTime()) {
                        console.log('Branch 2b: Même date - réinitialisation');
                        isSyncingFromCalendar = true;
                        if (checkInPicker) {
                            checkInPicker.clear();
                        } else {
                            checkInInput.value = '';
                        }
                        if (checkOutPicker) {
                            checkOutPicker.clear();
                        } else {
                            checkOutInput.value = '';
                        }
                        isSyncingFromCalendar = false;

                        validateAndUpdateDates('', '', 'check-in');
                    }
                    // Sinon, définir la date de départ
                    else {
                        console.log('Branch 2c: Définition date de départ:', info.dateStr);
                        // Vérifier que la période n'est pas bloquée
                        if (isPeriodBlocked(checkIn, info.dateStr)) {
                            alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                            return;
                        }
                        isSyncingFromCalendar = true;
                        if (checkOutPicker) {
                            checkOutPicker.setDate(info.dateStr, false);
                        } else {
                            checkOutInput.value = info.dateStr;
                        }
                        isSyncingFromCalendar = false;

                        validateAndUpdateDates(checkIn, info.dateStr, 'check-out');
                    }
                }
            },
            selectAllow: function(selectInfo) {
                // Empêcher la sélection de dates bloquées/réservées
                const selectStartStr = selectInfo.startStr;
                const selectEndStr = selectInfo.endStr;
                
                // Vérifier si la période sélectionnée chevauche des dates bloquées
                if (isPeriodBlocked(selectStartStr, selectEndStr)) {
                    return false; // Bloquer la sélection
                }
                
                // Vérifier que la date de début n'est pas dans le passé
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                const selectStartParts = selectStartStr.split('-');
                const selectStart = new Date(parseInt(selectStartParts[0]), parseInt(selectStartParts[1]) - 1, parseInt(selectStartParts[2]));
                selectStart.setHours(0, 0, 0, 0);
                if (selectStart < today) {
                    return false;
                }
                
                return true;
            },
        });
        calendar.render();

        // Initialiser Flatpickr sur les champs pour griser les dates bloquées
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        const todayStr = formatDate(today);

        checkInPicker = flatpickr('#check-in-date', {
            dateFormat: 'Y-m-d',
            minDate: todayStr,
            disable: blockedDatesList,
            disableMobile: true,
            onChange: function(selectedDates, dateStr) {
                if (isSyncingFromCalendar) return;
                isSyncingFromPicker = true;
                const checkOut = document.getElementById('check-out-date').value;
                validateAndUpdateDates(dateStr, checkOut, 'check-in');
                isSyncingFromPicker = false;
            }
        });

        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = formatDate(tomorrow);

        checkOutPicker = flatpickr('#check-out-date', {
            dateFormat: 'Y-m-d',
            minDate: tomorrowStr,
            disable: blockedDatesList,
            disableMobile: true,
            onChange: function(selectedDates, dateStr) {
                if (isSyncingFromCalendar) return;
                isSyncingFromPicker = true;
                const checkIn = document.getElementById('check-in-date').value;
                validateAndUpdateDates(checkIn, dateStr, 'check-out');
                isSyncingFromPicker = false;
            }
        });
        
        // Forcer l'application de la couleur de fond pour les dates réservées/bloquées
        setTimeout(function() {
            // Appliquer la couleur sur tous les événements bloqués/réservés
            document.querySelectorAll('.fc-event.blocked-date, .fc-event.reserved-date').forEach(function(el) {
                el.style.backgroundColor = '#CBAE821A';
                el.style.borderColor = '#CBAE821A';
            });
            
            // Appliquer la couleur sur les cellules de jour pour les dates bloquées
            if (Array.isArray(blockedDatesList) && blockedDatesList.length > 0) {
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
            }
        }, 200);
        
    // Passer les tarifs saisonniers au JavaScript
    const seasonalPrices = @json($villa->seasonalPrices()->with('season')->get());
    const basePricePerNightGlobal = {{ $villa->base_price_per_night }};

    function getPriceForDateJS(date) {
        const month = date.getMonth() + 1;
        const day = date.getDate();
        const dateValue = month * 100 + day;

        let maxPrice = null;

        for (const sp of seasonalPrices) {
            const s = sp.season;
            const startValue = s.start_month * 100 + s.start_day;
            const endValue = s.end_month * 100 + s.end_day;

            let inSeason = false;
            if (startValue <= endValue) {
                inSeason = (dateValue >= startValue && dateValue <= endValue);
            } else {
                inSeason = (dateValue >= startValue || dateValue <= endValue);
            }

            if (inSeason) {
                const price = parseFloat(sp.price_per_night);
                if (maxPrice === null || price > maxPrice) {
                    maxPrice = price;
                }
            }
        }

        return maxPrice !== null ? maxPrice : basePricePerNightGlobal;
    }

    // Fonction pour calculer le prix (même logique que booking.blade.php)
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

        const checkInParts = checkIn.split('-');
        const checkOutParts = checkOut.split('-');
        const startDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));
        const endDate = new Date(parseInt(checkOutParts[0]), parseInt(checkOutParts[1]) - 1, parseInt(checkOutParts[2]));
        const nights = Math.round((endDate - startDate) / (1000 * 60 * 60 * 24));

        if (nights <= 0) {
            return;
        }

        // Calcul du prix de base en itérant sur chaque nuit (gestion des saisons)
        let totalBasePrice = 0;
        let currentDate = new Date(startDate);
        for (let i = 0; i < nights; i++) {
            totalBasePrice += getPriceForDateJS(currentDate);
            currentDate.setDate(currentDate.getDate() + 1);
        }

        // Utiliser les mêmes paramètres que booking.blade.php
        const cleaningFee = {{ $villa->cleaning_fee ?? 0 }};
        const serviceFeePercentage = {{ $villa->service_fee_percentage ?? 5 }};
        const globalTaxRate = {{ $globalTaxRate }};
        const touristTaxPerNight = {{ $touristTaxPerNight }};
        const touristTaxEnabled = {{ $touristTaxEnabled ? 'true' : 'false' }};

        // Frais de service
        const serviceFee = totalBasePrice * (serviceFeePercentage / 100);

        // TVA sur les frais (ménage + service) - même logique que booking
        const vatAmount = (cleaningFee + serviceFee) * (globalTaxRate / 100);

        // Taxe touristique (estimée pour 2 adultes - valeur par défaut pour aperçu)
        let touristTax = 0;
        if (touristTaxEnabled === true) {
            const estimatedGuests = 2; // Estimation par défaut pour l'aperçu
            touristTax = touristTaxPerNight * estimatedGuests * nights;
        }

        // Total
        const total = totalBasePrice + cleaningFee + serviceFee + vatAmount + touristTax;
        
        // Formater les nombres
        function formatPrice(price) {
            return new Intl.NumberFormat('fr-FR', {
                style: 'decimal',
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }).format(price) + ' €';
        }

        // Calcul du total des taxes (TVA + taxe touristique)
        const totalTaxes = vatAmount + touristTax;

        const averagePrice = totalBasePrice / nights;
        document.getElementById('nights-text').textContent = formatPrice(averagePrice) + ' × ' + nights + ' nuit' + (nights > 1 ? 's' : '');
        document.getElementById('base-price').textContent = formatPrice(totalBasePrice);
        document.getElementById('service-fee').textContent = formatPrice(serviceFee);
        document.getElementById('local-taxes').textContent = formatPrice(totalTaxes);
        document.getElementById('total-price').textContent = formatPrice(total);
    }
        
        // Fonction réutilisable pour valider et mettre à jour les dates
        function validateAndUpdateDates(checkInValue, checkOutValue, fieldType) {
            console.log('validateAndUpdateDates called:', { checkInValue, checkOutValue, fieldType });

            const checkInInput = document.getElementById('check-in-date');
            const checkOutInput = document.getElementById('check-out-date');
            
            // Si on valide check-in
            if (fieldType === 'check-in' && checkInValue) {
                console.log('Validating check-in:', checkInValue);
                if (isDateBlocked(checkInValue)) {
                    console.log('Check-in date is blocked');
                    alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                    checkInInput.value = '';
                    calendar.unselect();
                    return false;
                }
                // Mettre à jour la date minimum pour le départ
                const checkInParts = checkInValue.split('-');
                const checkInDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));
                checkInDate.setDate(checkInDate.getDate() + 1);
                checkOutInput.min = checkInDate.getFullYear() + '-' +
                    String(checkInDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(checkInDate.getDate()).padStart(2, '0');
                console.log('Updated check-out min to:', checkOutInput.min);

                // Mettre à jour le minDate de flatpickr checkout
                if (typeof checkOutPicker !== 'undefined' && checkOutPicker) {
                    checkOutPicker.set('minDate', checkOutInput.min);
                }
            }
            
            // Si on valide check-out
            if (fieldType === 'check-out' && checkOutValue) {
                console.log('Validating check-out:', checkOutValue, 'with check-in:', checkInValue);
                if (isDateBlocked(checkOutValue)) {
                    console.log('Check-out date is blocked');
                    alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette période.');
                    checkOutInput.value = '';
                    calendar.unselect();
                    return false;
                }
                // Si pas d'arrivée mais un départ, réinitialiser
                if (!checkInValue) {
                    console.log('No check-in date set, resetting check-out');
                    checkOutInput.value = '';
                    return false;
                }
                console.log('Check-out validation passed');
            }
            
            // Si on a les deux dates, vérifier que la période n'est pas bloquée
            if (checkInValue && checkOutValue) {
                console.log('Validating period:', checkInValue, 'to', checkOutValue);
                if (isPeriodBlocked(checkInValue, checkOutValue)) {
                    console.log('Period is blocked');
                    alert('Cette période n\'est pas disponible. La villa est réservée ou bloquée pour certaines dates de cette période.');
                    checkInInput.value = '';
                    checkOutInput.value = '';
                    calendar.unselect();
                    return false;
                }
                console.log('Period validation passed');
            }
            
            // Mettre à jour la sélection du calendrier
            if (checkInValue && checkOutValue) {
                // Pour les champs du formulaire, on ajuste endDate +1 pour compenser le recalcul de FullCalendar
                const endParts = checkOutValue.split('-');
                const adjustedEndDate = new Date(parseInt(endParts[0]), parseInt(endParts[1]) - 1, parseInt(endParts[2]));
                adjustedEndDate.setDate(adjustedEndDate.getDate() + 1);
                const adjustedEndStr = adjustedEndDate.getFullYear() + '-' +
                    String(adjustedEndDate.getMonth() + 1).padStart(2, '0') + '-' +
                    String(adjustedEndDate.getDate()).padStart(2, '0');

                calendar.select(checkInValue, adjustedEndStr);
                calculatePrice();
            } else if (checkInValue) {
                calendar.select(checkInValue, checkInValue);
            } else {
                calendar.unselect();
            }
            
            return true;
        }
        
        // Écouter les changements sur les champs de date pour mettre à jour le calendrier
        document.getElementById('check-in-date').addEventListener('change', function() {
            if (isSyncingFromCalendar || isSyncingFromPicker) return;
            const checkIn = this.value;
            const checkOut = document.getElementById('check-out-date').value;
            validateAndUpdateDates(checkIn, checkOut, 'check-in');
        });
        
        document.getElementById('check-out-date').addEventListener('change', function() {
            if (isSyncingFromCalendar || isSyncingFromPicker) return;
            const checkIn = document.getElementById('check-in-date').value;
            const checkOut = this.value;
            validateAndUpdateDates(checkIn, checkOut, 'check-out');
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
            const checkInParts = checkIn.split('-');
            const checkOutParts = checkOut.split('-');
            const startDate = new Date(parseInt(checkInParts[0]), parseInt(checkInParts[1]) - 1, parseInt(checkInParts[2]));
            const endDate = new Date(parseInt(checkOutParts[0]), parseInt(checkOutParts[1]) - 1, parseInt(checkOutParts[2]));
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

// Fonctions pour les boutons concierge et devis
function openConciergeModal() {
    const modal = document.getElementById('conciergeModal');
    if (modal) {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
}

function openQuoteModal() {
    const modal = document.getElementById('quoteModal');
    if (modal) {
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    }
}

// Gestionnaire pour le formulaire concierge
document.addEventListener('DOMContentLoaded', function() {
    const conciergeForm = document.getElementById('concierge-form');
    if (conciergeForm) {
        conciergeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleConciergeSubmit();
        });
    }

    const quoteForm = document.getElementById('quote-form');
    if (quoteForm) {
        quoteForm.addEventListener('submit', function(e) {
            e.preventDefault();
            handleQuoteSubmit();
        });
    }
});

function handleConciergeSubmit() {
    const form = document.getElementById('concierge-form');
    const formData = new FormData(form);
    const alertDiv = document.getElementById('concierge-alert');

    // Masquer l'alerte précédente
    if (alertDiv) {
        alertDiv.style.display = 'none';
    }

    // Désactiver le bouton
    const submitBtn = document.getElementById('concierge-submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';

    fetch('{{ route("contact.send") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok && response.status === 422) {
            return response.json().then(data => {
                throw { type: 'validation', data: data };
            });
        }
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (alertDiv) {
            alertDiv.className = 'alert alert-success mt-3';
            alertDiv.textContent = 'Votre demande a été envoyée avec succès. Notre concierge vous contactera dans les plus brefs délais.';
            alertDiv.style.display = 'block';
        }
        form.reset();
    })
    .catch(error => {
        if (error && error.type === 'validation') {
            let errorMessages = [];
            if (error.data.errors) {
                Object.keys(error.data.errors).forEach(field => {
                    error.data.errors[field].forEach(msg => {
                        errorMessages.push(msg);
                    });
                });
            }

            if (alertDiv) {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.innerHTML = '<strong>Erreur de validation :</strong><ul class="mb-0 mt-2">' +
                    errorMessages.map(msg => '<li>' + msg + '</li>').join('') + '</ul>';
                alertDiv.style.display = 'block';
            }
        } else {
            console.error('Erreur:', error);
            if (alertDiv) {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.textContent = 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.';
                alertDiv.style.display = 'block';
            }
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function handleQuoteSubmit() {
    const form = document.getElementById('quote-form');
    const formData = new FormData(form);
    const alertDiv = document.getElementById('quote-alert');

    // Ajouter le sujet spécifique pour le devis
    formData.set('subject', 'Demande de devis - {{ $villa->name }}');

    // Masquer l'alerte précédente
    if (alertDiv) {
        alertDiv.style.display = 'none';
    }

    // Désactiver le bouton
    const submitBtn = document.getElementById('quote-submit-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';

    fetch('{{ route("contact.send") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
        }
    })
    .then(response => {
        if (!response.ok && response.status === 422) {
            return response.json().then(data => {
                data.errors.subject = []; // Supprimer l'erreur de sujet puisqu'on l'a défini
                if (Object.keys(data.errors).length === 0) {
                    return response.json();
                }
                throw { type: 'validation', data: data };
            });
        }
        if (!response.ok) {
            throw new Error('Erreur HTTP: ' + response.status);
        }
        return response.json();
    })
    .then(data => {
        if (alertDiv) {
            alertDiv.className = 'alert alert-success mt-3';
            alertDiv.textContent = 'Votre demande de devis a été envoyée avec succès. Notre équipe vous répondra dans les plus brefs délais.';
            alertDiv.style.display = 'block';
        }
        form.reset();
    })
    .catch(error => {
        if (error && error.type === 'validation') {
            let errorMessages = [];
            if (error.data.errors) {
                Object.keys(error.data.errors).forEach(field => {
                    error.data.errors[field].forEach(msg => {
                        errorMessages.push(msg);
                    });
                });
            }

            if (alertDiv) {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.innerHTML = '<strong>Erreur de validation :</strong><ul class="mb-0 mt-2">' +
                    errorMessages.map(msg => '<li>' + msg + '</li>').join('') + '</ul>';
                alertDiv.style.display = 'block';
            }
        } else {
            console.error('Erreur:', error);
            if (alertDiv) {
                alertDiv.className = 'alert alert-danger mt-3';
                alertDiv.textContent = 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.';
                alertDiv.style.display = 'block';
            }
        }
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}
</script>

<!-- Modal Concierge -->
<div class="modal fade" id="conciergeModal" tabindex="-1" aria-labelledby="conciergeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif text-lux-dark-blue" id="conciergeModalLabel">
                    <i class="fas fa-phone text-lux-gold me-2"></i>Contacter le concierge
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-4">
                    Notre concierge est à votre disposition pour répondre à toutes vos questions concernant la villa <strong>{{ $villa->name }}</strong>.
                </p>

                <div id="concierge-alert" style="display: none;"></div>

                <form id="concierge-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Téléphone</label>
                            <input type="tel" name="phone" class="form-control" placeholder="+33 6 12 34 56 78">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium text-lux-dark-blue">Votre demande <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Décrivez votre demande concernant {{ $villa->name }}..." required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="concierge-form" class="btn btn-lux-primary" id="concierge-submit-btn">
                    <i class="fas fa-paper-plane me-2"></i>Envoyer la demande
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Demande de devis -->
<div class="modal fade" id="quoteModal" tabindex="-1" aria-labelledby="quoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title font-serif text-lux-dark-blue" id="quoteModalLabel">
                    <i class="fas fa-envelope text-lux-gold me-2"></i>Demander un devis - {{ $villa->name }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        @if($villa->photos->count() > 0)
                            <img src="{{ asset('storage/' . $villa->photos->first()->file_path) }}"
                                 class="img-fluid rounded" alt="{{ $villa->name }}"
                                 style="height: 150px; width: 100%; object-fit: cover;">
                        @else
                            <div class="bg-lux-beige rounded d-flex align-items-center justify-content-center"
                                 style="height: 150px;">
                                <i class="fas fa-home fa-3x text-lux-gold"></i>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h6 class="text-lux-dark-blue mb-2">{{ $villa->name }}</h6>
                        <p class="text-muted small mb-2">{{ $villa->island->name ?? '' }}</p>
                        <p class="text-lux-gold fw-medium mb-0">
                            À partir de {{ number_format($villa->base_price_per_night ?? 0, 0, ',', ' ') }}€/nuit
                        </p>
                    </div>
                </div>

                <p class="text-muted mb-4">
                    Remplissez ce formulaire pour recevoir un devis personnalisé pour votre séjour dans cette villa.
                </p>

                <div id="quote-alert" style="display: none;"></div>

                <form id="quote-form">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Téléphone <span class="text-danger">*</span></label>
                            <input type="tel" name="phone" class="form-control" required placeholder="+33 6 12 34 56 78">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Nombre d'adultes</label>
                            <select name="adults" class="form-select">
                                <option value="1">1 adulte</option>
                                <option value="2" selected>2 adultes</option>
                                <option value="3">3 adultes</option>
                                <option value="4">4 adultes</option>
                                <option value="5">5+ adultes</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-medium text-lux-dark-blue">Nombre d'enfants</label>
                            <select name="children" class="form-select">
                                <option value="0" selected>Aucun</option>
                                <option value="1">1 enfant</option>
                                <option value="2">2 enfants</option>
                                <option value="3">3+ enfants</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-medium text-lux-dark-blue">Détails de votre demande <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Précisez vos dates souhaitées, vos besoins particuliers, etc." required></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" form="quote-form" class="btn btn-lux-primary" id="quote-submit-btn">
                    <i class="fas fa-paper-plane me-2"></i>Demander le devis
                </button>
            </div>
        </div>
    </div>
</div>
@endpush
