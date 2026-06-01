@extends('layouts.app')

@section('title', 'Villas | LUXÎLES - Nos Villas d\'Exception')

@section('content')

    <!-- Page Title Hero -->
    <section id="page-title" class="py-5" style="padding-top: 12rem; padding-bottom: 4rem; background-color: var(--lux-beige);">
        <div class="container">
            <div class="text-center">
                <br><br><br>
                <span class="text-lux-gold text-uppercase small fw-medium d-block mb-3" style="letter-spacing: 0.2em; font-size: 0.75rem;">Catalogue</span>
                <h1 class="display-4 font-serif mb-4" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;">Nos Villas d'Exception</h1>
                <div class="w-20 h-1 bg-lux-gold mx-auto opacity-50"></div>
            </div>
        </div>
    </section>

    <!-- Advanced Filter Bar -->
    <section id="filters" class="sticky-top lux-filter-bar" style="top: 80px; z-index: 40; background-color: white; border-top: 1px solid rgba(138, 150, 166, 0.1); border-bottom: 1px solid rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.1); overflow: visible;">
        <div class="container" style="overflow: visible;">
            <div class="py-3" style="overflow: visible;">
                <form class="d-flex flex-column flex-lg-row align-items-stretch align-items-lg-center justify-content-between gap-2 gap-lg-3" style="overflow: visible;">
                    <!-- Filter Groups -->
                    <div class="d-flex flex-column flex-md-row align-items-stretch align-items-md-center gap-2 gap-md-3 w-100" style="overflow: visible;">
                        <!-- Location Filter -->
                        <div class="dropdown position-relative" style="overflow: visible; width: 100%;">
                            <button type="button" class="btn btn-filter px-3 px-md-4 py-2 rounded border text-lux-dark-blue small d-flex align-items-center gap-2 dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" style="justify-content: space-between; border-color: rgba(138, 150, 166, 0.2); background-color: white; transition: border-color 0.3s;">
                                <span class="d-flex align-items-center gap-2 text-truncate"><i class="fas fa-location-dot flex-shrink-0" style="color: rgba(203, 174, 130, 0.8);"></i> <span id="destination-label" class="text-truncate">{{ $islandId ? $islands->where('id', $islandId)->first()->name ?? 'Destination' : 'Destination' }}</span></span>
                                <i class="fas fa-chevron-down small flex-shrink-0" style="color: var(--lux-greyBlue); font-size: 0.75rem;"></i>
                            </button>
                            <ul class="dropdown-menu w-100" style="z-index: 1050; position: absolute; max-width: 100%;">
                                @php
                                    $queryParams = request()->query();
                                    unset($queryParams['island']);
                                    unset($queryParams['page']);
                                @endphp
                                <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $queryParams) }}" style="overflow-wrap: break-word; word-wrap: break-word;">Toutes les destinations</a></li>
                                @foreach($islands as $island)
                                    @php
                                        $islandQueryParams = request()->query();
                                        $islandQueryParams['island'] = $island->id;
                                        unset($islandQueryParams['page']);
                                    @endphp
                                    <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $islandQueryParams) }}" style="overflow-wrap: break-word; word-wrap: break-word;">{{ $island->name }}</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Price Filter -->
                        <div class="dropdown position-relative" style="overflow: visible; width: 100%;">
                            <button type="button" class="btn btn-filter px-3 px-md-4 py-2 rounded border text-lux-dark-blue small d-flex align-items-center gap-2 dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" style="justify-content: space-between; border-color: rgba(138, 150, 166, 0.2); background-color: white; transition: border-color 0.3s;">
                                <span class="d-flex align-items-center gap-2 text-truncate"><i class="fas fa-tag flex-shrink-0" style="color: rgba(203, 174, 130, 0.8);"></i> <span id="price-label" class="text-truncate">{{ $priceMax ? 'Jusqu\'à ' . number_format($priceMax, 0, ',', ' ') . '€' : 'Prix' }}</span></span>
                                <i class="fas fa-chevron-down small flex-shrink-0" style="color: var(--lux-greyBlue); font-size: 0.75rem;"></i>
                            </button>
                            <ul class="dropdown-menu w-100" style="z-index: 1050; position: absolute; max-width: 100%;">
                                @php
                                    $priceQueryParams = request()->query();
                                    unset($priceQueryParams['price_max']);
                                    unset($priceQueryParams['page']);
                                @endphp
                                <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $priceQueryParams) }}" style="overflow-wrap: break-word; word-wrap: break-word;">Tous les prix</a></li>
                                @php
                                    $priceOptions = [500, 1000, 2000, 3000, 5000];
                                @endphp
                                @foreach($priceOptions as $price)
                                    @php
                                        $priceQueryParamsWithPrice = request()->query();
                                        $priceQueryParamsWithPrice['price_max'] = $price;
                                        unset($priceQueryParamsWithPrice['page']);
                                    @endphp
                                    <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $priceQueryParamsWithPrice) }}" style="overflow-wrap: break-word; word-wrap: break-word;">Jusqu'à {{ number_format($price, 0, ',', ' ') }}€</a></li>
                                @endforeach
                            </ul>
                        </div>

                        <!-- Rooms Filter -->
                        <div class="dropdown position-relative" style="overflow: visible; width: 100%;">
                            <button type="button" class="btn btn-filter px-3 px-md-4 py-2 rounded border text-lux-dark-blue small d-flex align-items-center gap-2 dropdown-toggle w-100" data-bs-toggle="dropdown" aria-expanded="false" style="justify-content: space-between; border-color: rgba(138, 150, 166, 0.2); background-color: white; transition: border-color 0.3s;">
                                <span class="d-flex align-items-center gap-2 text-truncate"><i class="fas fa-bed flex-shrink-0" style="color: rgba(203, 174, 130, 0.8);"></i> <span id="bedrooms-label" class="text-truncate">{{ $bedrooms ? $bedrooms . '+ chambres' : 'Chambres' }}</span></span>
                                <i class="fas fa-chevron-down small flex-shrink-0" style="color: var(--lux-greyBlue); font-size: 0.75rem;"></i>
                            </button>
                            <ul class="dropdown-menu w-100" style="z-index: 1050; position: absolute; max-width: 100%;">
                                @php
                                    $bedroomsQueryParams = request()->query();
                                    unset($bedroomsQueryParams['bedrooms']);
                                    unset($bedroomsQueryParams['page']);
                                @endphp
                                <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $bedroomsQueryParams) }}" style="overflow-wrap: break-word; word-wrap: break-word;">Toutes les chambres</a></li>
                                @for($i = 1; $i <= 6; $i++)
                                    @php
                                        $bedroomsQueryParamsWithValue = request()->query();
                                        $bedroomsQueryParamsWithValue['bedrooms'] = $i;
                                        unset($bedroomsQueryParamsWithValue['page']);
                                    @endphp
                                    <li><a class="dropdown-item text-truncate" href="{{ route('villas.index', $bedroomsQueryParamsWithValue) }}" style="overflow-wrap: break-word; word-wrap: break-word;">{{ $i }}+ chambres</a></li>
                                @endfor
                            </ul>
                        </div>

                        <!-- Amenities Filter -->
                        <div class="position-relative" style="width: 100%;">
                            <button type="button" class="btn btn-filter px-3 px-md-4 py-2 rounded border text-lux-dark-blue small d-flex align-items-center gap-2 w-100" data-bs-toggle="offcanvas" data-bs-target="#advancedFiltersOffcanvas" aria-controls="advancedFiltersOffcanvas" style="justify-content: center; border-color: rgba(138, 150, 166, 0.2); background-color: white; transition: border-color 0.3s; white-space: nowrap;">
                                <span class="d-flex align-items-center gap-2"><i class="fas fa-sliders flex-shrink-0" style="color: rgba(203, 174, 130, 0.8);"></i> <span class="d-none d-sm-inline">Plus de filtres</span><span class="d-sm-none">Filtres</span></span>
                            </button>
                        </div>
                    </div>

                    <!-- Sort & View Toggle -->
                    <div class="d-flex align-items-center gap-3 gap-md-4 justify-content-between justify-content-lg-end border-top border-0 lg-border-top-0 pt-2 lg-pt-0 w-100 lg-w-auto" style="border-top-color: rgba(138, 150, 166, 0.1);">
                        <span class="small text-lux-greyBlue">{{ $villas->total() }} résultat{{ $villas->total() > 1 ? 's' : '' }}</span>
                        <div class="h-6 w-px d-none d-sm-block" style="background-color: rgba(138, 150, 166, 0.2);"></div>
                        <form method="GET" action="{{ route('villas.index') }}" class="d-inline" id="sort-form">
                            @if($islandId)
                                <input type="hidden" name="island" value="{{ $islandId }}">
                            @endif
                            @if($priceMax)
                                <input type="hidden" name="price_max" value="{{ $priceMax }}">
                            @endif
                            @if($bedrooms)
                                <input type="hidden" name="bedrooms" value="{{ $bedrooms }}">
                            @endif
                            <select name="sort" class="bg-transparent small text-lux-dark-blue fw-medium border-0 outline-none cursor-pointer" onchange="document.getElementById('sort-form').submit();">
                                <option value="recommended" {{ $sortBy === 'recommended' ? 'selected' : '' }}>Trier par : Recommandé</option>
                                <option value="name_asc" {{ $sortBy === 'name_asc' ? 'selected' : '' }}>Nom (A-Z)</option>
                                <option value="name_desc" {{ $sortBy === 'name_desc' ? 'selected' : '' }}>Nom (Z-A)</option>
                                <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Plus récentes</option>
                                <option value="oldest" {{ $sortBy === 'oldest' ? 'selected' : '' }}>Plus anciennes</option>
                                <option value="capacity_desc" {{ $sortBy === 'capacity_desc' ? 'selected' : '' }}>Capacité (décroissante)</option>
                                <option value="capacity_asc" {{ $sortBy === 'capacity_asc' ? 'selected' : '' }}>Capacité (croissante)</option>
                            </select>
                        </form>
                    </div>
                </form>
            </div>
        </div>
    </section>

    <!-- Main Content Area -->
    <main id="villa-list" class="py-5" style="background-color: var(--lux-beige); padding-top: 3rem; padding-bottom: 3rem;">
        <div class="container">
            <!-- Grid 3 Columns (Desktop Default) -->
            <div class="row g-4">
                @php
                    use Illuminate\Support\Facades\Storage;
                    use Illuminate\Support\Str;
                @endphp
                @forelse($villas as $villa)
                    @php
                        $primaryPhoto = $villa->photos->where('is_primary', true)->first() ?? $villa->photos->first();
                        $photoUrl = $primaryPhoto ? Storage::url($primaryPhoto->file_path) : 'https://via.placeholder.com/600x400?text=No+Photo';
                    @endphp
                    <div class="col-12 col-md-6 col-lg-4">
                        <article class="villa-card-listing bg-white rounded-lg overflow-hidden position-relative" style="transition: all 0.5s; border: 1px solid rgba(138, 150, 166, 0.05); cursor: pointer;" onclick="window.location.href='{{ route('villas.show', $villa->id) }}'">
                            <div class="position-relative" style="height: 400px; overflow: hidden;">
                                @if($villa->is_featured)
                                    <span class="position-absolute bg-lux-gold text-white fw-bold px-2 py-1 rounded text-uppercase" style="top: 1rem; left: 1rem; z-index: 20; letter-spacing: 0.05em; font-size: 0.7rem;">Coup de cœur</span>
                                @endif
                                <span class="position-absolute bg-white bg-opacity-90 px-3 py-1 rounded small fw-bold text-lux-dark-blue text-uppercase" style="top: 1rem; right: {{ $villa->is_featured ? '3.5rem' : '3.5rem' }}; z-index: 20; letter-spacing: 0.05em; backdrop-filter: blur(10px);">{{ $villa->island->name ?? 'N/A' }}</span>
                                @auth
                                <button class="position-absolute rounded-circle border-0 d-flex align-items-center justify-content-center villa-card-heart-btn toggle-favorite-btn" 
                                        data-villa-id="{{ $villa->id }}"
                                        data-is-favorite="{{ auth()->user()->hasFavorite($villa->id) ? 'true' : 'false' }}"
                                        style="width: 32px; height: 32px; top: 1rem; right: 1rem; z-index: 21; background-color: rgba(255, 255, 255, 0.9); backdrop-filter: blur(10px); transition: all 0.3s;" 
                                        onclick="event.stopPropagation(); toggleFavorite({{ $villa->id }}, this);" 
                                        onmouseover="this.style.backgroundColor='rgba(203, 174, 130, 0.9)'; this.querySelector('i').style.color='white';" 
                                        onmouseout="this.style.backgroundColor='rgba(255,255,255,0.9)'; this.querySelector('i').style.color='var(--lux-gold)';">
                                    <i class="{{ auth()->user()->hasFavorite($villa->id) ? 'fa-solid' : 'fa-regular' }} fa-heart" style="color: var(--lux-gold);"></i>
                                </button>
                                @endauth
                                <img src="{{ $photoUrl }}" 
                                     class="w-100 h-100 villa-card-img" style="object-fit: cover; transition: transform 0.7s;" 
                                     alt="{{ $villa->name }}">
                                <div class="position-absolute top-0 start-0 w-100 h-100 villa-card-overlay" style="background: linear-gradient(to top, rgba(0,0,0,0.4), transparent); opacity: 0; transition: opacity 0.3s;"></div>
                                <div class="position-absolute bottom-0 start-0 text-white p-4">
                                    <p class="small fw-medium mb-0 d-flex align-items-center gap-1"><i class="fas fa-location-dot text-lux-gold" style="font-size: 0.75rem;"></i> {{ $villa->island->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                            <div class="p-4">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h3 class="h5 font-serif mb-0" style="color: var(--lux-dark-blue); font-weight: 500; transition: color 0.3s;">{{ $villa->name }}</h3>
                                    <span class="fw-medium" style="color: var(--lux-dark-blue);">dès {{ number_format($villa->base_price_per_night, 0, ',', ' ') }}€ <span class="small text-lux-greyBlue fw-normal">/nuit</span></span>
                                </div>
                                <p class="text-lux-greyBlue small mb-4" style="line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $villa->short_description ?? ($villa->description ? Str::limit($villa->description, 120) : 'Villa de prestige exceptionnelle.') }}</p>
                                
                                <!-- Amenities Icons -->
                                <div class="d-flex align-items-center gap-4 py-3 border-top villa-card-amenities" style="border-top-color: rgba(138, 150, 166, 0.1);">
                                    <span class="d-flex align-items-center gap-2 small text-lux-greyBlue"><i class="fas fa-bed text-lux-gold"></i> {{ $villa->bedrooms }} Ch.</span>
                                    <span class="d-flex align-items-center gap-2 small text-lux-greyBlue"><i class="fas fa-shower text-lux-gold"></i> {{ $villa->bathrooms }} Sdb.</span>
                                    <span class="d-flex align-items-center gap-2 small text-lux-greyBlue"><i class="fas fa-users text-lux-gold"></i> {{ $villa->max_capacity }} Pers.</span>
                                </div>
                            </div>
                        </article>
                    </div>
                @empty
                    <div class="col-12 text-center py-5">
                        <p class="text-lux-greyBlue mb-0">Aucune villa disponible pour le moment</p>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($villas->hasPages())
                <div id="pagination" class="d-flex align-items-center justify-content-center gap-2 mt-5 pt-4 border-top" style="border-top-color: rgba(138, 150, 166, 0.1);">
                    @php
                        $queryParams = request()->query();
                        unset($queryParams['page']);
                    @endphp
                    @if($villas->onFirstPage())
                        <button class="btn btn-pagination rounded border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-greyBlue); transition: all 0.3s; cursor: not-allowed; opacity: 0.5;" disabled>
                            <i class="fas fa-chevron-left small"></i>
                        </button>
                    @else
                        <a href="{{ $villas->appends($queryParams)->previousPageUrl() }}" class="btn btn-pagination rounded border d-flex align-items-center justify-content-center text-decoration-none" style="width: 40px; height: 40px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-greyBlue); transition: all 0.3s;">
                            <i class="fas fa-chevron-left small"></i>
                        </a>
                    @endif
                    
                    @foreach($villas->getUrlRange(1, $villas->lastPage()) as $page => $url)
                        @if($page == $villas->currentPage())
                            <button class="btn btn-pagination rounded border-0 d-flex align-items-center justify-content-center fw-medium" style="width: 40px; height: 40px; background-color: var(--lux-gold); color: white;">{{ $page }}</button>
                        @else
                            <a href="{{ $villas->appends($queryParams)->url($page) }}" class="btn btn-pagination rounded border d-flex align-items-center justify-content-center text-decoration-none" style="width: 40px; height: 40px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue); transition: all 0.3s;">{{ $page }}</a>
                        @endif
                    @endforeach
                    
                    @if($villas->hasMorePages())
                        <a href="{{ $villas->appends($queryParams)->nextPageUrl() }}" class="btn btn-pagination rounded border d-flex align-items-center justify-content-center text-decoration-none" style="width: 40px; height: 40px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-greyBlue); transition: all 0.3s;">
                            <i class="fas fa-chevron-right small"></i>
                        </a>
                    @else
                        <button class="btn btn-pagination rounded border d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-greyBlue); transition: all 0.3s; cursor: not-allowed; opacity: 0.5;" disabled>
                            <i class="fas fa-chevron-right small"></i>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </main>

    <!-- Offcanvas Panneau Latéral Filtres Avancés -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="advancedFiltersOffcanvas" aria-labelledby="advancedFiltersOffcanvasLabel" style="width: 100%; max-width: 500px;">
        <div class="offcanvas-header border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
            <h5 class="offcanvas-title font-serif" id="advancedFiltersOffcanvasLabel" style="color: var(--lux-dark-blue);">Filtres Avancés</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <form method="GET" action="{{ route('villas.index') }}" id="advanced-filters-form">
                @if($islandId)
                    <input type="hidden" name="island" value="{{ $islandId }}">
                @endif
                @if($priceMax)
                    <input type="hidden" name="price_max" value="{{ $priceMax }}">
                @endif
                @if($bedrooms)
                    <input type="hidden" name="bedrooms" value="{{ $bedrooms }}">
                @endif
                @if($sortBy && $sortBy !== 'recommended')
                    <input type="hidden" name="sort" value="{{ $sortBy }}">
                @endif
                
                <div class="d-flex flex-column gap-4">
                    <!-- Salles de bain -->
                    <div>
                        <label class="form-label fw-medium mb-2" style="color: var(--lux-dark-blue);">Salles de bain (minimum)</label>
                        <select name="bathrooms" class="form-select" style="border-color: rgba(138, 150, 166, 0.2);">
                            <option value="">Toutes</option>
                            @for($i = 1; $i <= 6; $i++)
                                <option value="{{ $i }}" {{ $bathrooms == $i ? 'selected' : '' }}>{{ $i }}+ salles de bain</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Capacité -->
                    <div>
                        <label class="form-label fw-medium mb-2" style="color: var(--lux-dark-blue);">Capacité (minimum)</label>
                        <select name="capacity" class="form-select" style="border-color: rgba(138, 150, 166, 0.2);">
                            <option value="">Toutes</option>
                            @for($i = 2; $i <= 20; $i += 2)
                                <option value="{{ $i }}" {{ $capacity == $i ? 'selected' : '' }}>{{ $i }}+ personnes</option>
                            @endfor
                        </select>
                    </div>

                    <!-- Surface -->
                    <div>
                        <label class="form-label fw-medium mb-2" style="color: var(--lux-dark-blue);">Surface (minimum)</label>
                        <select name="surface" class="form-select" style="border-color: rgba(138, 150, 166, 0.2);">
                            <option value="">Toutes</option>
                            <option value="50" {{ $surface == '50' ? 'selected' : '' }}>50+ m²</option>
                            <option value="100" {{ $surface == '100' ? 'selected' : '' }}>100+ m²</option>
                            <option value="150" {{ $surface == '150' ? 'selected' : '' }}>150+ m²</option>
                            <option value="200" {{ $surface == '200' ? 'selected' : '' }}>200+ m²</option>
                            <option value="300" {{ $surface == '300' ? 'selected' : '' }}>300+ m²</option>
                            <option value="500" {{ $surface == '500' ? 'selected' : '' }}>500+ m²</option>
                        </select>
                    </div>

                    <!-- Villa mise en avant -->
                    <div>
                        <label class="form-label fw-medium mb-2" style="color: var(--lux-dark-blue);">Type de villa</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="featured_only" value="1" id="featuredOnly" {{ $featuredOnly ? 'checked' : '' }}>
                            <label class="form-check-label" for="featuredOnly" style="color: var(--lux-gray);">
                                Uniquement les villas "Coup de cœur"
                            </label>
                        </div>
                    </div>

                    <!-- Équipements -->
                    <div>
                        <label class="form-label fw-medium mb-3" style="color: var(--lux-dark-blue);">Équipements</label>
                        <div class="row g-2">
                            @forelse($allEquipments as $equipment)
                                <div class="col-6 col-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="equipments[]" value="{{ $equipment->id }}" id="equipment-{{ $equipment->id }}" {{ in_array($equipment->id, (array)$selectedEquipments) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="equipment-{{ $equipment->id }}" style="color: var(--lux-gray); font-size: 0.9rem;">
                                            {{ $equipment->name }}
                                        </label>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12">
                                    <p class="text-muted small">Aucun équipement disponible</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="offcanvas-footer border-top p-3" style="border-color: rgba(138, 150, 166, 0.1); background-color: white;">
            <div class="d-flex flex-column flex-sm-row gap-2">
                <button type="button" class="btn btn-secondary w-100" data-bs-dismiss="offcanvas">Annuler</button>
                @php
                    $resetParams = request()->query();
                    unset($resetParams['bathrooms'], $resetParams['capacity'], $resetParams['surface'], $resetParams['equipments'], $resetParams['featured_only'], $resetParams['page']);
                @endphp
                <a href="{{ route('villas.index', $resetParams) }}" class="btn btn-link text-lux-gray w-100 text-center" style="text-decoration: none;">Réinitialiser</a>
                <button type="submit" form="advanced-filters-form" class="btn text-white w-100" style="background-color: var(--lux-gold);">Appliquer les filtres</button>
            </div>
        </div>
    </div>

    <style>
        /* Cacher le chevron Bootstrap par défaut sur les boutons de filtres */
        .btn-filter.dropdown-toggle::after {
            display: none !important;
        }
        
        /* Assurer que les éléments du dropdown ne débordent pas */
        .dropdown-menu {
            max-width: 100% !important;
            box-sizing: border-box !important;
        }
        
        .dropdown-item {
            white-space: normal !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            max-width: 100% !important;
        }
    </style>
@endsection

@push('scripts')
<script>
    // Fonction globale pour toggle favori
    function toggleFavorite(villaId, button) {
        @auth
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
        @else
        // Rediriger vers la page de connexion si non connecté
        window.location.href = '{{ route("login") }}';
        @endauth
    }
</script>
@endpush


