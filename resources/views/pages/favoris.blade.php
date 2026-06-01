@extends('layouts.dashboard')

@section('title', 'Mes Favoris | LUXÎLES - Dashboard')

@section('content')
    <!-- Page Header / Hero Minimal -->
    <section id="favoris-hero" class="position-relative" style="height: 250px; background-color: var(--lux-dark-blue); overflow: hidden; margin-top: -1rem; margin-left: -1rem; margin-right: -1rem; margin-bottom: 2rem;">
        <style>
            @media (min-width: 768px) {
                #favoris-hero {
                    margin-top: -2rem !important;
                    margin-left: -2rem !important;
                    margin-right: -2rem !important;
                }
            }
        </style>
        <div class="position-absolute top-0 start-0 w-100 h-100 opacity-40">
            <img src="https://images.unsplash.com/photo-1602343168117-bb8ffe3e2e9f?q=80&w=2070&auto=format&fit=crop" class="w-100 h-100" style="object-fit: cover;" alt="Luxury Villa">
            <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to bottom, rgba(10,26,47,0.7) 0%, rgba(10,26,47,0.4) 50%, rgba(10,26,47,0.8) 100%);"></div>
        </div>
        <div class="position-relative z-10 h-100 d-flex align-items-center justify-content-center text-center" style="padding-top: 3rem;">
            <div>
                <h1 class="h1 font-serif text-white mb-2" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Mes Favoris</h1>
                <p class="text-lux-gold text-uppercase small fw-medium mb-0" style="letter-spacing: 0.2em; font-size: 0.875rem;">{{ $favoriteVillas->count() }} Villa{{ $favoriteVillas->count() > 1 ? 's' : '' }} d'Exception</p>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <div class="container-fluid px-4">
        @if($favoriteVillas->count() > 0)
            <!-- Filters & Actions Bar -->
            <div class="card border mb-4 shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem;">
                <div class="card-body p-4">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-auto">
                            <div class="position-relative">
                                <i class="fa-solid fa-filter position-absolute top-50 start-0 translate-middle-y ms-3 text-lux-gold"></i>
                                <select id="filter-island" class="form-select form-select-sm ps-5" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s;" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                                    <option value="">Toutes les destinations</option>
                                    @foreach($favoriteVillas->pluck('island.name')->unique()->filter() as $islandName)
                                        <option value="{{ $islandName }}">{{ $islandName }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto">
                            <div class="position-relative">
                                <i class="fa-solid fa-sort position-absolute top-50 start-0 translate-middle-y ms-3 text-lux-gold"></i>
                                <select id="sort-favorites" class="form-select form-select-sm ps-5" style="border-color: transparent; background-color: rgba(248, 248, 246, 0.5); transition: border-color 0.3s;" onmouseover="this.style.borderColor='rgba(203, 174, 130, 0.3)'" onmouseout="this.style.borderColor='transparent'" onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='transparent'">
                                    <option value="date">Date d'ajout</option>
                                    <option value="price_asc">Prix croissant</option>
                                    <option value="price_desc">Prix décroissant</option>
                                    <option value="name_asc">Nom A-Z</option>
                                    <option value="name_desc">Nom Z-A</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto ms-md-auto">
                            <div class="d-flex gap-2">
                                <button onclick="showView('grid')" id="btn-grid" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 active" style="border-color: var(--lux-gold); color: var(--lux-gold); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="if(this.classList.contains('active')) { this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'; } else { this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='inherit'; }">
                                    <i class="fa-solid fa-th-large"></i> Grille
                                </button>
                                <button onclick="showView('list')" id="btn-list" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'" onmouseout="if(this.classList.contains('active')) { this.style.borderColor='var(--lux-gold)'; this.style.color='var(--lux-gold)'; } else { this.style.borderColor='rgba(138, 150, 166, 0.2)'; this.style.color='inherit'; }">
                                    <i class="fa-solid fa-list"></i> Liste
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Favorites Grid -->
            <section id="favorites-grid" class="mb-4">
                <div class="row g-4">
                    @foreach($favoriteVillas as $villa)
                        @php
                            $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
                                ?? $villa->photos->first();
                            $photoUrl = $primaryPhoto ? asset('storage/' . $primaryPhoto->file_path) : 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=2071&auto=format&fit=crop';
                        @endphp
                        <div class="col-12 col-md-6 col-lg-4 favorite-item" data-island="{{ $villa->island->name ?? '' }}" data-price="{{ $villa->base_price_per_night }}" data-name="{{ strtolower($villa->name) }}">
                            <article class="card border h-100 position-relative overflow-hidden" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.boxShadow='0 10px 25px rgba(0,0,0,0.15)'" onmouseout="this.style.boxShadow='none'">
                                <div class="position-relative" style="height: 300px; overflow: hidden;">
                                    @if($villa->is_featured)
                                        <span class="position-absolute bg-lux-gold text-white small fw-bold px-3 py-1 rounded text-uppercase" style="top: 1rem; left: 1rem; z-index: 20; letter-spacing: 0.05em;">Coup de cœur</span>
                                    @endif
                                    <span class="position-absolute bg-white bg-opacity-90 px-3 py-1 rounded small fw-bold text-lux-dark-blue text-uppercase" style="top: 1rem; right: {{ $villa->is_featured ? '3.5rem' : '3.5rem' }}; z-index: 20; letter-spacing: 0.05em; backdrop-filter: blur(10px);">{{ $villa->island->name ?? 'N/A' }}</span>
                                    <button class="position-absolute rounded-circle border-0 d-flex align-items-center justify-content-center toggle-favorite-btn" 
                                            data-villa-id="{{ $villa->id }}"
                                            style="width: 40px; height: 40px; top: 1rem; right: 1rem; z-index: 21; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); transition: all 0.3s;" 
                                            onclick="event.stopPropagation(); toggleFavorite({{ $villa->id }}, this);"
                                            onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.9)'; this.querySelector('i').style.color='white'" 
                                            onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.querySelector('i').style.color='var(--lux-gold)'">
                                        <i class="fa-solid fa-heart" style="color: var(--lux-gold);"></i>
                                    </button>
                                    <a href="{{ route('villas.show', $villa->id) }}">
                                        <img src="{{ $photoUrl }}" 
                                             class="w-100 h-100" 
                                             style="object-fit: cover; transition: transform 0.7s;" 
                                             onmouseover="this.style.transform='scale(1.1)'" 
                                             onmouseout="this.style.transform='scale(1)'" 
                                             alt="{{ $villa->name }}">
                                    </a>
                                    <div class="position-absolute bottom-0 start-0 w-100 p-3" style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent);">
                                        <p class="text-white small fw-medium mb-0 d-flex align-items-center gap-1">
                                            <i class="fas fa-location-dot text-lux-gold" style="font-size: 0.75rem;"></i> {{ $villa->island->name ?? 'N/A' }}
                                        </p>
                                    </div>
                                </div>
                                <div class="card-body p-4">
                                    <h3 class="font-serif h4 mb-2" style="color: var(--lux-dark-blue);">{{ $villa->name }}</h3>
                                    <p class="text-lux-gray small mb-3">{{ Str::limit($villa->short_description ?? $villa->description, 100) }}</p>
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-users text-lux-gold"></i>
                                            <span class="small text-lux-gray">Jusqu'à {{ $villa->max_capacity }} personne{{ $villa->max_capacity > 1 ? 's' : '' }}</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-bed text-lux-gold"></i>
                                            <span class="small text-lux-gray">{{ $villa->bedrooms }} chambre{{ $villa->bedrooms > 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                                        <div>
                                            <p class="h5 font-serif mb-0" style="color: var(--lux-dark-blue);">À partir de {{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €</p>
                                            <p class="small text-lux-gray mb-0">par nuit</p>
                                        </div>
                                        <a href="{{ route('villas.show', $villa->id) }}" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2">
                                            Voir détails <i class="fa-solid fa-arrow-right small"></i>
                                        </a>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>

            <!-- Favorites List View (Hidden by default) -->
            <section id="favorites-list" class="d-none mb-4">
                <div class="d-flex flex-column gap-3">
                    @foreach($favoriteVillas as $villa)
                        @php
                            $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
                                ?? $villa->photos->first();
                            $photoUrl = $primaryPhoto ? asset('storage/' . $primaryPhoto->file_path) : 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?q=80&w=2071&auto=format&fit=crop';
                        @endphp
                        <div class="card border shadow-sm favorite-item" 
                             data-island="{{ $villa->island->name ?? '' }}" 
                             data-price="{{ $villa->base_price_per_night }}" 
                             data-name="{{ strtolower($villa->name) }}"
                             style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.5rem; transition: all 0.3s; cursor: pointer;" 
                             onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,0.1)'" 
                             onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,0.1)'">
                            <div class="card-body p-4">
                                <div class="row g-4 align-items-center">
                                    <div class="col-12 col-md-4">
                                        <div class="position-relative" style="height: 200px; overflow: hidden; border-radius: 0.5rem;">
                                            @if($villa->is_featured)
                                                <span class="position-absolute bg-lux-gold text-white small fw-bold px-2 py-1 rounded text-uppercase" style="top: 0.5rem; left: 0.5rem; z-index: 20; letter-spacing: 0.05em; font-size: 0.625rem;">Coup de cœur</span>
                                            @endif
                                            <button class="position-absolute rounded-circle border-0 d-flex align-items-center justify-content-center toggle-favorite-btn" 
                                                    data-villa-id="{{ $villa->id }}"
                                                    style="width: 32px; height: 32px; top: 0.5rem; right: 0.5rem; z-index: 21; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); transition: all 0.3s;" 
                                                    onclick="event.stopPropagation(); toggleFavorite({{ $villa->id }}, this);"
                                                    onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.9)'; this.querySelector('i').style.color='white'" 
                                                    onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.querySelector('i').style.color='var(--lux-gold)'">
                                                <i class="fa-solid fa-heart small" style="color: var(--lux-gold);"></i>
                                            </button>
                                            <a href="{{ route('villas.show', $villa->id) }}">
                                                <img src="{{ $photoUrl }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-5">
                                        <h3 class="font-serif h4 mb-2" style="color: var(--lux-dark-blue);">{{ $villa->name }}</h3>
                                        <p class="text-lux-gray small mb-2 d-flex align-items-center gap-1">
                                            <i class="fas fa-location-dot text-lux-gold" style="font-size: 0.75rem;"></i> {{ $villa->island->name ?? 'N/A' }}
                                        </p>
                                        <p class="text-lux-gray small mb-3">{{ Str::limit($villa->short_description ?? $villa->description, 150) }}</p>
                                        <div class="d-flex align-items-center gap-4 small text-lux-gray">
                                            <span><i class="fa-solid fa-users text-lux-gold me-1"></i>Jusqu'à {{ $villa->max_capacity }} personne{{ $villa->max_capacity > 1 ? 's' : '' }}</span>
                                            <span><i class="fa-solid fa-bed text-lux-gold me-1"></i>{{ $villa->bedrooms }} chambre{{ $villa->bedrooms > 1 ? 's' : '' }}</span>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-3 text-end">
                                        <div class="mb-3">
                                            <p class="h5 font-serif mb-0" style="color: var(--lux-dark-blue);">À partir de {{ number_format($villa->base_price_per_night, 0, ',', ' ') }} €</p>
                                            <p class="small text-lux-gray mb-0">par nuit</p>
                                        </div>
                                        <a href="{{ route('villas.show', $villa->id) }}" class="btn btn-lux-primary btn-sm d-inline-flex align-items-center gap-2">
                                            Voir détails <i class="fa-solid fa-arrow-right small"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @else
            <!-- Empty State -->
            <div class="text-center py-5" id="empty-favorites">
                <i class="fa-regular fa-heart text-lux-gray mb-3" style="font-size: 4rem;"></i>
                <h3 class="h5 font-serif mb-2" style="color: var(--lux-dark-blue);">Aucun favori pour le moment</h3>
                <p class="text-lux-gray mb-4">Explorez nos villas et ajoutez vos préférées à vos favoris.</p>
                <a href="{{ route('villas.index') }}" class="btn btn-lux-primary">
                    <i class="fa-solid fa-search me-2"></i>
                    Découvrir nos villas
                </a>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    function showView(viewType) {
        const gridView = document.getElementById('favorites-grid');
        const listView = document.getElementById('favorites-list');
        const btnGrid = document.getElementById('btn-grid');
        const btnList = document.getElementById('btn-list');
        
        if (viewType === 'grid') {
            gridView.classList.remove('d-none');
            listView.classList.add('d-none');
            btnGrid.classList.add('active');
            btnGrid.style.borderColor = 'var(--lux-gold)';
            btnGrid.style.color = 'var(--lux-gold)';
            btnList.classList.remove('active');
            btnList.style.borderColor = 'rgba(138, 150, 166, 0.2)';
            btnList.style.color = 'inherit';
        } else if (viewType === 'list') {
            gridView.classList.add('d-none');
            listView.classList.remove('d-none');
            btnList.classList.add('active');
            btnList.style.borderColor = 'var(--lux-gold)';
            btnList.style.color = 'var(--lux-gold)';
            btnGrid.classList.remove('active');
            btnGrid.style.borderColor = 'rgba(138, 150, 166, 0.2)';
            btnGrid.style.color = 'inherit';
        }
    }

    // Toggle favorite functionality
    function toggleFavorite(villaId, button) {
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.is_favorite) {
                    icon.className = 'fa-solid fa-heart';
                } else {
                    icon.className = 'fa-regular fa-heart';
                    // Retirer la carte de la vue
                    const favoriteItem = button.closest('.favorite-item');
                    if (favoriteItem) {
                        favoriteItem.style.transition = 'opacity 0.3s, transform 0.3s';
                        favoriteItem.style.opacity = '0';
                        favoriteItem.style.transform = 'scale(0.9)';
                        setTimeout(() => {
                            favoriteItem.remove();
                            // Vérifier s'il reste des favoris
                            const remainingFavorites = document.querySelectorAll('.favorite-item');
                            if (remainingFavorites.length === 0) {
                                window.location.reload();
                            }
                        }, 300);
                    }
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

    // Filtres et tri
    document.addEventListener('DOMContentLoaded', function() {
        const filterIsland = document.getElementById('filter-island');
        const sortFavorites = document.getElementById('sort-favorites');
        
        if (filterIsland) {
            filterIsland.addEventListener('change', function() {
                const selectedIsland = this.value;
                const items = document.querySelectorAll('.favorite-item');
                
                items.forEach(item => {
                    const island = item.getAttribute('data-island');
                    if (!selectedIsland || island === selectedIsland) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            });
        }
        
        if (sortFavorites) {
            sortFavorites.addEventListener('change', function() {
                const sortValue = this.value;
                const container = document.querySelector('#favorites-grid .row') || document.querySelector('#favorites-list');
                const items = Array.from(document.querySelectorAll('.favorite-item'));
                
                items.sort((a, b) => {
                    switch(sortValue) {
                        case 'price_asc':
                            return parseFloat(a.getAttribute('data-price')) - parseFloat(b.getAttribute('data-price'));
                        case 'price_desc':
                            return parseFloat(b.getAttribute('data-price')) - parseFloat(a.getAttribute('data-price'));
                        case 'name_asc':
                            return a.getAttribute('data-name').localeCompare(b.getAttribute('data-name'));
                        case 'name_desc':
                            return b.getAttribute('data-name').localeCompare(a.getAttribute('data-name'));
                        default: // date
                            return 0; // Garder l'ordre d'origine (date d'ajout)
                    }
                });
                
                items.forEach(item => {
                    container.appendChild(item);
                });
            });
        }
    });
</script>
@endpush
