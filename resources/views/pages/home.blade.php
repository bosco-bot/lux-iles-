@extends('layouts.app')

@section('title', 'Accueil | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Hero Section -->
    <section id="hero-section" class="hero-section" style="background-image: url('{{ asset('home-hero.png') }}');">
        <div class="container">
            <div class="text-center">
                <h1 class="hero-title font-serif text-white mb-4 fade-in-up">
                    L'Excellence des Caraïbes<br>
                    <span class="italic text-lux-gold fw-light">à votre portée</span>
                </h1>
                <p class="hero-description text-white-90 fs-5 fw-light mb-5 mx-auto" style="max-width: 700px; letter-spacing: 0.02em;">
                    Découvrez une collection exclusive de villas de prestige à Saint-Martin, en Guadeloupe et en Marie-Galante.
                </p>
                
                <!-- Search Engine Component -->
                <div class="hero-search">
                    <form method="GET" action="{{ route('villas.index') }}" class="row g-4 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--lux-gray);">
                                Destination
                            </label>
                            <div class="d-flex align-items-center border-bottom pb-2 position-relative" style="border-color: rgba(138, 150, 166, 0.3) !important;">
                                <i class="fas fa-location-dot text-lux-gold me-3"></i>
                                <select class="form-select border-0 bg-transparent p-0 pe-4" style="outline: none; appearance: none; -webkit-appearance: none; -moz-appearance: none; background-image: none;" name="island">
                                    <option value="">Toutes les destinations</option>
                                    @foreach($islands as $island)
                                        <option value="{{ $island->id }}">{{ $island->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down text-lux-gray position-absolute" style="font-size: 0.75rem; right: 0; pointer-events: none;"></i>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--lux-gray);">
                                Arrivée - Départ
                            </label>
                            <div class="d-flex align-items-center border-bottom pb-2 border-start ps-4" style="border-color: rgba(138, 150, 166, 0.3) !important;">
                                <i class="far fa-calendar text-lux-gold me-3"></i>
                                <input type="text" id="hero-date-range" placeholder="Ajouter des dates" class="form-control border-0 bg-transparent p-0" style="outline: none; cursor: pointer;" name="dates" readonly>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label d-block mb-1" style="font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--lux-gray);">
                                Voyageurs
                            </label>
                            <div class="d-flex align-items-center border-bottom pb-2 border-start ps-4" style="border-color: rgba(138, 150, 166, 0.3) !important;">
                                <i class="far fa-user text-lux-gold me-3"></i>
                                <input type="number" placeholder="2 Voyageurs" class="form-control border-0 bg-transparent p-0" style="outline: none;" name="guests" min="1" value="2">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-lux-primary w-100 py-3 d-flex align-items-center justify-content-center gap-2" style="height: 56px;">
                                <i class="fas fa-magnifying-glass"></i>
                                <span class="fw-medium">Rechercher</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Advantages Section -->
    <section id="advantages-section" class="py-5">
        <div class="container py-5">
            <div class="row g-5">
                <div class="col-md-4 text-center">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="far fa-gem fs-3 text-lux-gold"></i>
                    </div>
                    <h3 class="h5 font-serif mb-3" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">Sélection Exclusive</h3>
                    <p class="text-lux-gray" style="line-height: 1.8; max-width: 300px; margin: 0 auto;">
                        Chaque villa est visitée et validée par nos experts pour garantir un standard de luxe inégalé.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-bell-concierge fs-3 text-lux-gold"></i>
                    </div>
                    <h3 class="h5 font-serif mb-3" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">Conciergerie Dédiée</h3>
                    <p class="text-lux-gray" style="line-height: 1.8; max-width: 300px; margin: 0 auto;">
                        Un service personnalisé 24/7 pour organiser vos transferts, chefs privés et expériences uniques.
                    </p>
                </div>
                <div class="col-md-4 text-center">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 64px; height: 64px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fas fa-umbrella-beach fs-3 text-lux-gold"></i>
                    </div>
                    <h3 class="h5 font-serif mb-3" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">Emplacements de Rêve</h3>
                    <p class="text-lux-gray" style="line-height: 1.8; max-width: 300px; margin: 0 auto;">
                        Des propriétés situées sur les plus belles plages et les collines les plus prisées des Caraïbes.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Villas Section -->
    <section id="featured-villas" class="py-5 bg-white">
        <div class="container py-5">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-end mb-5">
                <div>
                    <span class="text-lux-gold text-uppercase mb-2 d-block" style="font-size: 0.875rem; letter-spacing: 0.2em; font-weight: 500;">Collection Prestige</span>
                    <h2 class="h2 font-serif mb-0" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">Nos Villas à la Une</h2>
                </div>
                <a href="{{ route('villas.index') }}" class="text-decoration-none d-flex align-items-center gap-2 mt-4 mt-md-0" style="color: var(--lux-dark-blue);">
                    <span class="fw-medium">Voir toute la collection</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="row g-4">
                @php
                    use Illuminate\Support\Facades\Storage;
                    use Illuminate\Support\Str;
                @endphp
                @forelse($featuredVillas as $villa)
                    @php
                        $primaryPhoto = $villa->photos->where('is_primary', true)->first() ?? $villa->photos->first();
                        $photoUrl = $primaryPhoto ? Storage::url($primaryPhoto->file_path) : 'https://via.placeholder.com/600x400?text=No+Photo';
                    @endphp
                    <div class="col-md-4 d-flex">
                        <article class="villa-card w-100">
                            <a href="{{ route('villas.show', $villa->id) }}" class="text-decoration-none" style="color: inherit;">
                                <div class="position-relative" style="height: 400px; overflow: hidden; border-radius: 8px 8px 0 0;">
                                    <img src="{{ $photoUrl }}" class="villa-card-img w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                                    <div class="position-absolute top-0 end-0 m-3 bg-white bg-opacity-90 px-3 py-1 rounded" style="backdrop-filter: blur(10px); z-index: 20;">
                                        <span class="text-lux-dark-blue fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.05em; color: var(--lux-dark-blue);">{{ $villa->island->name ?? 'N/A' }}</span>
                                    </div>
                                    <button class="position-absolute rounded-circle border-0 d-flex align-items-center justify-content-center toggle-favorite-btn" 
                                            data-villa-id="{{ $villa->id }}"
                                            data-is-favorite="{{ auth()->check() && auth()->user()->hasFavorite($villa->id) ? 'true' : 'false' }}"
                                            style="width: 32px; height: 32px; top: 0.75rem; left: 0.75rem; z-index: 21; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px);" 
                                            onclick="event.stopPropagation(); event.preventDefault(); toggleFavorite({{ $villa->id }}, this);">
                                        <i class="{{ auth()->check() && auth()->user()->hasFavorite($villa->id) ? 'fa-solid' : 'fa-regular' }} fa-heart" style="color: var(--lux-gold);"></i>
                                    </button>
                                </div>
                                <div style="padding: 1.5rem; display: flex; flex-direction: column; flex: 1;">
                                    <div class="d-flex justify-content-between align-items-start mb-2 gap-2">
                                        <h3 class="font-serif mb-0" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif; font-size: 1.1rem; line-height: 1.3; flex: 1; min-width: 0;">{{ $villa->name }}</h3>
                                        <span class="fw-medium flex-shrink-0" style="color: var(--lux-dark-blue); white-space: nowrap; font-size: 0.95rem;">dès {{ number_format($villa->base_price_per_night, 0, ',', ' ') }}€ <span class="text-lux-gray fw-normal small" style="color: var(--lux-gray);">/nuit</span></span>
                                    </div>
                                    <p class="text-lux-gray small mb-4" style="line-height: 1.6; flex: 1; color: var(--lux-gray);">{{ $villa->short_description ?? ($villa->description ? Str::limit($villa->description, 120) : 'Villa de prestige exceptionnelle.') }}</p>
                                    <div class="d-flex align-items-center gap-4 text-lux-gray small border-top pt-4" style="border-color: rgba(138, 150, 166, 0.1) !important; color: var(--lux-gray);">
                                        <span class="d-flex align-items-center gap-2" style="color: var(--lux-gray);"><i class="fas fa-bed text-lux-gold" style="color: var(--lux-gold);"></i> {{ $villa->bedrooms }} Ch.</span>
                                        <span class="d-flex align-items-center gap-2" style="color: var(--lux-gray);"><i class="fas fa-shower text-lux-gold" style="color: var(--lux-gold);"></i> {{ $villa->bathrooms }} Sdb.</span>
                                        <span class="d-flex align-items-center gap-2" style="color: var(--lux-gray);"><i class="fas fa-users text-lux-gold" style="color: var(--lux-gold);"></i> {{ $villa->max_capacity }} Pers.</span>
                                    </div>
                                </div>
                            </a>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-lux-gray mb-0">Aucune villa mise en avant pour le moment</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Destinations Section -->
    <section id="destinations" class="py-5 bg-lux-dark text-white" style="overflow-x: hidden;">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-lux-gold text-uppercase mb-3 d-block" style="font-size: 0.875rem; letter-spacing: 0.2em; font-weight: 500;">Évasion</span>
                <h2 class="h2 font-serif mb-4" style="font-family: 'Playfair Display', serif;">Nos Destinations d'Exception</h2>
                <div class="mx-auto" style="width: 96px; height: 2px; background-color: var(--lux-gold); opacity: 0.5;"></div>
            </div>
            <div class="row g-4">
                @php
                    // Images par défaut pour les îles non configurées en base (fallback uniquement)
                    $defaultImages = [
                        'Saint-Barthélemy' => 'https://images.unsplash.com/photo-1540541338287-41700207dee6?q=80&w=2070&auto=format&fit=crop',
                        'Saint-Martin' => 'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?q=80&w=2070&auto=format&fit=crop',
                    ];
                    $defaultDescriptions = [
                        'Saint-Barthélemy' => 'Le joyau chic des Caraïbes. Luxe, boutiques de créateurs et plages immaculées.',
                        'Saint-Martin' => 'L\'île franco-néerlandaise. Plages paradisiaques et ambiance cosmopolite.',
                    ];
                @endphp
                @forelse($destinations as $index => $destination)
                    @php
                        // Priorité : image_path (uploadée) > image_url (URL) > images par défaut > image générique
                        if ($destination->image_path) {
                            $imageUrl = Storage::url($destination->image_path);
                        } elseif ($destination->image_url) {
                            $imageUrl = $destination->image_url;
                        } else {
                            $imageUrl = $defaultImages[$destination->name] ?? 'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?q=80&w=2070&auto=format&fit=crop';
                        }
                        // Utiliser la description de la BDD en priorité, sinon fallback vers les descriptions par défaut, sinon description générique
                        $description = $destination->description ?? ($defaultDescriptions[$destination->name] ?? 'Découvrez cette destination exceptionnelle des Caraïbes.');
                    @endphp
                    <div class="col-lg-4 {{ $index === 2 ? 'mt-lg-5' : '' }}">
                        <a href="{{ route('villas.index') }}?island={{ $destination->id }}" class="destination-card d-block text-decoration-none">
                            <img src="{{ $imageUrl }}" class="w-100 h-100" style="object-fit: cover; opacity: 0.8;" alt="{{ $destination->name }}">
                            <div class="destination-content">
                                <h3 class="h3 font-serif text-white mb-2" style="font-family: 'Playfair Display', serif;">{{ $destination->name }}</h3>
                                <p class="text-white-50 small mb-3">{{ Str::limit($description, 100) }}</p>
                                <span class="text-lux-gold small fw-medium text-uppercase d-inline-flex align-items-center gap-2" style="letter-spacing: 0.1em;">
                                    Découvrir <i class="fas fa-arrow-right"></i>
                                </span>
                            </div>
                        </a>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-white-50 mb-0">Aucune destination disponible pour le moment</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials-section" class="py-5">
        <div class="container py-5">
            <div class="text-center mb-5">
                <span class="text-lux-gold text-uppercase mb-3 d-block" style="font-size: 0.875rem; letter-spacing: 0.2em; font-weight: 500;">Témoignages</span>
                <h2 class="h2 font-serif mb-4" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">L'Excellence Reconnue</h2>
                <p class="text-lux-gray mx-auto" style="max-width: 600px;">Découvrez les expériences de nos clients qui ont fait confiance à LUXÎLES pour leurs séjours d'exception.</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-1 mb-4 text-lux-gold">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-lux-gray fst-italic mb-4" style="line-height: 1.8;">"Une expérience inoubliable à Saint-Martin. La villa était absolument magnifique et le service de conciergerie exceptionnel. Nous reviendrons sans hésiter."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px;">
                                <img src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-5.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Sophie Martin">
                            </div>
                            <div>
                                <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">Sophie Martin</p>
                                <p class="text-lux-gray small mb-0">Paris, France</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-1 mb-4 text-lux-gold">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-lux-gray fst-italic mb-4" style="line-height: 1.8;">"Le professionnalisme de l'équipe LUXÎLES est remarquable. Chaque détail était parfait, de la réservation au départ. Un séjour de rêve en Guadeloupe."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px;">
                                <img src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-8.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Marc Dubois">
                            </div>
                            <div>
                                <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">Marc Dubois</p>
                                <p class="text-lux-gray small mb-0">Genève, Suisse</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="testimonial-card">
                        <div class="d-flex align-items-center gap-1 mb-4 text-lux-gold">
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                            <i class="fas fa-star"></i>
                        </div>
                        <p class="text-lux-gray fst-italic mb-4" style="line-height: 1.8;">"Un séjour enchanteur à Marie-Galante. L'île aux cent moulins nous a séduits : plages paradisiaques, authenticité et douceur de vivre. Une escapade parfaite."</p>
                        <div class="d-flex align-items-center gap-3">
                            <div class="rounded-circle overflow-hidden" style="width: 48px; height: 48px;">
                                <img src="https://storage.googleapis.com/uxpilot-auth.appspot.com/avatars/avatar-6.jpg" class="w-100 h-100" style="object-fit: cover;" alt="Isabelle Laurent">
                            </div>
                            <div>
                                <p class="fw-medium mb-0" style="color: var(--lux-dark-blue);">Isabelle Laurent</p>
                                <p class="text-lux-gray small mb-0">Bruxelles, Belgique</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/fr.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser Flatpickr pour le champ de dates dans le hero
            const dateInput = document.getElementById('hero-date-range');
            if (dateInput) {
                flatpickr(dateInput, {
                    mode: "range",
                    dateFormat: "d/m/Y",
                    locale: "fr",
                    minDate: "today",
                    placeholder: "Ajouter des dates",
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr, instance) {
                        // Mettre à jour le placeholder si des dates sont sélectionnées
                        if (selectedDates.length === 2) {
                            const startDate = selectedDates[0];
                            const endDate = selectedDates[1];
                            const startFormatted = startDate.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                            const endFormatted = endDate.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                            dateInput.value = startFormatted + ' - ' + endFormatted;
                        }
                    }
                });
            }
        });

        // Fonction globale pour toggle favori
        function toggleFavorite(villaId, button) {
            // Vérifier si l'utilisateur est connecté
            @auth
            const isAuthenticated = true;
            @else
            const isAuthenticated = false;
            @endauth
            
            if (!isAuthenticated) {
                // Rediriger vers la page de connexion si non connecté
                window.location.href = '{{ route("login") }}';
                return;
            }
            
            const icon = button.querySelector('i');
            const isCurrentlyFavorite = icon.classList.contains('fa-solid');
            
            // Désactiver le bouton pendant la requête
            button.disabled = true;
            const originalIcon = icon.className;
            icon.className = 'fa-solid fa-spinner fa-spin';
            
            fetch('{{ route("espace-client.favorites.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ villa_id: villaId })
            })
            .then(response => {
                // Si la réponse est 401 (non autorisé), rediriger vers la connexion
                if (response.status === 401) {
                    window.location.href = '{{ route("login") }}';
                    return;
                }
                return response.json();
            })
            .then(data => {
                if (!data) return; // Si on a été redirigé, data sera null
                
                if (data.success) {
                    if (data.is_favorite) {
                        icon.className = 'fa-solid fa-heart';
                        button.setAttribute('data-is-favorite', 'true');
                    } else {
                        icon.className = 'fa-regular fa-heart';
                        button.setAttribute('data-is-favorite', 'false');
                    }
                } else {
                    alert(data.message || 'Une erreur est survenue');
                    icon.className = originalIcon;
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Une erreur est survenue lors de la modification des favoris');
                icon.className = originalIcon;
            })
            .finally(() => {
                button.disabled = false;
            });
        }
    </script>
@endpush


