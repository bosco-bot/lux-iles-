@extends('layouts.admin')

@php
    use Illuminate\Support\Facades\Storage;
    
    // Préparer les données pour le mode édition
    $existingPhotos = [];
    $existingEquipments = [];
    $existingBlockedPeriods = [];
    
    if (isset($villa)) {
        // Photos existantes
        $existingPhotos = $villa->photos->map(function($photo) {
            return [
                'id' => $photo->id,
                'url' => Storage::url($photo->file_path),
                'name' => $photo->file_name,
                'is_primary' => $photo->is_primary ?? false,
            ];
        })->toArray();
        
        // Équipements existants (mapping des noms d'équipements vers les valeurs du formulaire)
        $equipmentMap = [
            'Piscine' => 'piscine-debordement',
            'Jacuzzi' => 'jacuzzi-prive',
            'Plage privée' => 'acces-plage',
            'Climatisation' => 'climatisation',
            'WiFi' => 'wifi',
            'Salle de sport' => 'salle-sport',
        ];
        
        foreach ($villa->equipments as $equipment) {
            foreach ($equipmentMap as $dbName => $formValue) {
                if (stripos($equipment->name, $dbName) !== false) {
                    $existingEquipments[] = $formValue;
                    break;
                }
            }
        }
        
        // Périodes bloquées existantes
        $existingBlockedPeriods = $villa->availabilityBlocks->map(function($block) {
            return [
                'id' => 'block-' . $block->id,
                'start' => $block->start_date,
                'end' => $block->end_date,
                'reason' => $block->reason ?? 'Bloqué manuellement',
            ];
        })->toArray();
    }
@endphp

@section('title', 'Ajouter une Villa | LUXÎLES - Back-office')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.villas') }}" class="text-white-50 text-decoration-none hover-lux-gold">Villas</a>
    <span class="mx-2">/</span>
    <span class="text-white">Création</span>
@endsection

@section('content')
<style>
    .tab-active { 
        border-bottom: 2px solid #CBAE82 !important; 
        color: #0A1A2F !important; 
        font-weight: 600; 
    }
    .tab-inactive { 
        border-bottom: 2px solid transparent !important; 
        color: #8A96A6 !important; 
    }
    .tab-inactive:hover { 
        color: #CBAE82 !important; 
        border-bottom-color: rgba(138, 150, 166, 0.3) !important;
    }
    input[type="checkbox"].peer:checked {
        background-color: #CBAE82 !important;
        border-color: #CBAE82 !important;
    }
    input[type="checkbox"].peer:checked + i {
        opacity: 1 !important;
    }
    input[type="checkbox"].peer:not(:checked) + i {
        opacity: 0 !important;
    }
    /* Styles pour FullCalendar */
    #calendar-container {
        width: 100% !important;
    }
    #calendar-container .fc {
        width: 100% !important;
    }
    #calendar-container .fc-theme-standard td,
    #calendar-container .fc-theme-standard th {
        border-color: rgba(138, 150, 166, 0.2) !important;
    }
    #calendar-container .fc-daygrid-day {
        background-color: #fff !important;
    }
    #calendar-container .fc-daygrid-day:hover {
        background-color: rgba(203, 174, 130, 0.05) !important;
    }
    /* Style pour les jours du calendrier */
    #calendar-container .fc-col-header-cell-cushion {
        color: rgb(138 150 166 / var(--tw-text-opacity, 1)) !important;
    }
    #calendar-container .fc-daygrid-day-number {
        color: rgb(138 150 166 / var(--tw-text-opacity, 1)) !important;
    }
    /* Style pour les boutons du calendrier (prev, next, today) */
    #calendar-container .fc-button {
        background-color: var(--lux-gold) !important;
        border-color: var(--lux-gold) !important;
        color: var(--lux-white) !important;
        font-weight: 500 !important;
        transition: all 0.3s !important;
    }
    #calendar-container .fc-button:hover {
        background-color: var(--lux-light-gold) !important;
        border-color: var(--lux-light-gold) !important;
        color: var(--lux-white) !important;
    }
    #calendar-container .fc-button:focus {
        box-shadow: 0 0 0 0.2rem rgba(203, 174, 130, 0.25) !important;
    }
    #calendar-container .fc-button:disabled {
        background-color: rgba(203, 174, 130, 0.5) !important;
        border-color: rgba(203, 174, 130, 0.5) !important;
        color: var(--lux-white) !important;
        opacity: 0.6 !important;
    }
    /* Style pour les périodes bloquées */
    #calendar-container .fc-event.blocked-period {
        background-color: #fee !important;
        border-color: #fcc !important;
        color: #c33 !important;
    }
    /* Style pour la sélection */
    #calendar-container .fc-daygrid-day.selected {
        background-color: rgba(203, 174, 130, 0.1) !important;
    }
</style>
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 small text-lux-gray mb-2">
                <a href="{{ route('admin.villas') }}" class="text-lux-gray text-decoration-none hover-lux-gold">Villas</a>
                <i class="fa-solid fa-chevron-right" style="font-size: 0.6rem;"></i>
                <span>{{ isset($villa) ? 'Édition' : 'Création' }}</span>
            </div>
            <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                {{ isset($villa) ? 'Édition : ' . $villa->name : 'Nouvelle Villa' }}
            </h1>
        </div>
        <div class="d-flex gap-3 mt-3 mt-md-0">
            <button type="button" class="px-4 py-2 border rounded transition-colors text-sm font-medium d-flex align-items-center gap-2" 
                    data-bs-toggle="modal" data-bs-target="#previewModal" onclick="generatePreview()" 
                    style="border-color: var(--lux-blue); color: var(--lux-blue); background-color: transparent;"
                    onmouseover="this.style.backgroundColor='var(--lux-white)'; this.style.borderColor='var(--lux-blue)'"
                    onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='var(--lux-blue)'">
                <i class="fa-regular fa-eye"></i> Aperçu
            </button>
            <button type="submit" form="villa-form" class="btn btn-lux-primary btn-sm d-flex align-items-center gap-2 shadow-sm text-white">
                <i class="fa-solid fa-floppy-disk text-white"></i> Enregistrer
            </button>
        </div>
    </div>

    <!-- Messages d'erreur/succès -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <strong><i class="fa-solid fa-exclamation-triangle me-2"></i>Erreurs de validation :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- VARIANT A: HORIZONTAL TABS (Main View) -->
    <form action="{{ isset($villa) ? route('admin.villas.update', $villa->id) : route('admin.villas.store') }}" method="POST" enctype="multipart/form-data" id="villa-form" novalidate>
        @csrf
        @if(isset($villa))
            @method('PUT')
        @endif
    <section id="variant-a-horizontal" class="card border shadow-sm mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
        <!-- Tab Headers -->
        <div class="card-header bg-transparent border-bottom px-4" style="border-color: rgba(138, 150, 166, 0.2) !important;">
            <ul class="nav nav-tabs border-0" id="horizontalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-active active border-0 bg-transparent text-uppercase fw-medium d-flex align-items-center gap-2" 
                            id="general-info-tab" data-bs-toggle="tab" data-bs-target="#general-info-content" 
                            type="button" role="tab" aria-selected="true" style="padding-bottom: 1rem; font-size: 0.875rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-info-circle"></i> Informations générales
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-inactive border-0 bg-transparent text-uppercase fw-medium d-flex align-items-center gap-2" 
                            id="photos-tab" data-bs-toggle="tab" data-bs-target="#photos-content" 
                            type="button" role="tab" aria-selected="false" style="padding-bottom: 1rem; font-size: 0.875rem; letter-spacing: 0.05em;">
                        <i class="fa-regular fa-image"></i> Photos & Média
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-inactive border-0 bg-transparent text-uppercase fw-medium d-flex align-items-center gap-2" 
                            id="equipments-tab" data-bs-toggle="tab" data-bs-target="#equipments-content" 
                            type="button" role="tab" aria-selected="false" style="padding-bottom: 1rem; font-size: 0.875rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-list-check"></i> Équipements
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-inactive border-0 bg-transparent text-uppercase fw-medium d-flex align-items-center gap-2" 
                            id="prices-tab" data-bs-toggle="tab" data-bs-target="#prices-content" 
                            type="button" role="tab" aria-selected="false" style="padding-bottom: 1rem; font-size: 0.875rem; letter-spacing: 0.05em;">
                        <i class="fa-solid fa-tag"></i> Tarifs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-inactive border-0 bg-transparent text-uppercase fw-medium d-flex align-items-center gap-2" 
                            id="calendar-tab" data-bs-toggle="tab" data-bs-target="#calendar-content" 
                            type="button" role="tab" aria-selected="false" style="padding-bottom: 1rem; font-size: 0.875rem; letter-spacing: 0.05em;">
                        <i class="fa-regular fa-calendar"></i> Calendrier
                    </button>
                </li>
            </ul>
        </div>

        <!-- Tab Content -->
        <div class="tab-content p-4" id="horizontalTabsContent">
            <!-- General Info Tab -->
            <div class="tab-pane fade show active" id="general-info-content" role="tabpanel" aria-labelledby="general-info-tab">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h2 class="h4 font-serif mb-0 d-flex align-items-center gap-3" style="color: var(--lux-dark-blue);">
                        <i class="fa-solid fa-info-circle" style="color: var(--lux-gold);"></i> Informations générales
                    </h2>
                </div>

                <div class="row g-4">
                    <!-- Nom de la villa -->
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Nom de la villa <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Villa Azure - Saint Barth" required
                               value="{{ old('name', $villa->name ?? '') }}"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Île -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Île <span class="text-danger">*</span>
                        </label>
                        <select name="island_id" class="form-select" required
                                style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                                onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                            <option value="">Sélectionnez une île</option>
                            @foreach($islands ?? [] as $island)
                                <option value="{{ $island->id }}" {{ old('island_id', $villa->island_id ?? '') == $island->id ? 'selected' : '' }}>
                                    {{ $island->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Surface -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Surface (m²)
                        </label>
                        <div class="input-group">
                            <input type="number" name="surface_area" min="0" class="form-control" placeholder="0"
                                   value="{{ old('surface_area', $villa->surface_area ?? '') }}"
                                   style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                                   onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                            <span class="input-group-text" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray);">m²</span>
                        </div>
                    </div>

                    <!-- Chambres -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Chambres <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="bedrooms" min="1" value="{{ old('bedrooms', $villa->bedrooms ?? 1) }}" class="form-control" required
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Salles de bain -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Salles de bain <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="bathrooms" min="1" value="{{ old('bathrooms', $villa->bathrooms ?? 1) }}" class="form-control" required
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Capacité maximale -->
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Capacité max. (personnes) <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="max_capacity" min="1" value="{{ old('max_capacity', $villa->max_capacity ?? 2) }}" class="form-control" required
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Description courte -->
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Description courte (max. 500 caractères)
                        </label>
                        <textarea name="short_description" rows="3" maxlength="500" class="form-control" placeholder="Une description courte et accrocheuse..."
                                  style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue); resize: vertical;"
                                  onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">{{ old('short_description', $villa->short_description ?? '') }}</textarea>
                        <small class="text-lux-gray" style="font-size: 0.75rem;">Utilisée pour les aperçus et les listes</small>
                    </div>

                    <!-- Description complète -->
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Description complète
                        </label>
                        <textarea name="description" rows="6" class="form-control" placeholder="Description détaillée de la villa, de ses équipements, de son emplacement..."
                                  style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue); resize: vertical;"
                                  onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">{{ old('description', $villa->description ?? '') }}</textarea>
                    </div>

                    <!-- Adresse -->
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Adresse complète
                        </label>
                        <textarea name="address" rows="2" class="form-control" placeholder="Adresse complète de la villa..."
                                  style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue); resize: vertical;"
                                  onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">{{ old('address', $villa->address ?? '') }}</textarea>
                    </div>

                    <!-- Coordonnées GPS -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Latitude
                        </label>
                        <input type="number" name="latitude" step="0.00000001" class="form-control" placeholder="14.6161"
                               value="{{ old('latitude', $villa->latitude ?? '') }}"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Longitude
                        </label>
                        <input type="number" name="longitude" step="0.00000001" class="form-control" placeholder="-61.0589"
                               value="{{ old('longitude', $villa->longitude ?? '') }}"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Heures d'arrivée et départ -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Heure d'arrivée
                        </label>
                        <input type="time" name="check_in_time" value="{{ old('check_in_time', isset($villa) && $villa->check_in_time ? \Carbon\Carbon::parse($villa->check_in_time)->format('H:i') : '16:00') }}" class="form-control"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Heure de départ
                        </label>
                        <input type="time" name="check_out_time" value="{{ old('check_out_time', isset($villa) && $villa->check_out_time ? \Carbon\Carbon::parse($villa->check_out_time)->format('H:i') : '10:00') }}" class="form-control"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Séjour minimum -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Séjour minimum (nuits)
                        </label>
                        <input type="number" name="minimum_stay_nights" min="1" value="{{ old('minimum_stay_nights', $villa->minimum_stay_nights ?? 3) }}" class="form-control"
                               style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                               onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                    </div>

                    <!-- Pourcentage de frais de service -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Frais de service (%)
                        </label>
                        <div class="input-group">
                            <input type="number" name="service_fee_percentage" step="0.01" min="0" max="100" value="{{ old('service_fee_percentage', $villa->service_fee_percentage ?? 0.00) }}" class="form-control"
                                   style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                                   onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                            <span class="input-group-text" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-gray);">%</span>
                        </div>
                    </div>

                    <!-- Statut de la villa -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Statut
                        </label>
                        <select name="status" class="form-select" 
                                style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);"
                                onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                            @php
                                $currentStatus = old('status', isset($villa) && $villa->is_active ? 'active' : 'maintenance');
                            @endphp
                            <option value="active" {{ $currentStatus === 'active' ? 'selected' : '' }}>Active</option>
                            <option value="maintenance" {{ $currentStatus === 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                        </select>
                    </div>

                    <!-- Options -->
                    <div class="col-12">
                        <label class="form-label small text-uppercase fw-semibold mb-3" style="color: var(--lux-gray); letter-spacing: 0.1em;">
                            Options
                        </label>
                        <div class="d-flex flex-column gap-3">
                            <label class="d-flex align-items-center gap-3 cursor-pointer">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', isset($villa) && $villa->is_featured ? true : false) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ old('is_featured', isset($villa) && $villa->is_featured ? 'var(--lux-gold)' : 'transparent') }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ old('is_featured', isset($villa) && $villa->is_featured ? '1' : '0') }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Villa mise en avant</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Photos Tab -->
            <div class="tab-pane fade" id="photos-content" role="tabpanel" aria-labelledby="photos-tab">
                <div class="mb-4">
                    <h2 class="h4 font-serif mb-2" style="color: var(--lux-dark-blue);">Galerie Photos</h2>
                    <p class="text-lux-gray small mb-0">Ajoutez des images haute résolution. Survolez une image pour voir les actions disponibles. Cliquez sur l'étoile <i class="fa-solid fa-star" style="color: var(--lux-gold);"></i> pour définir la photo de couverture.</p>
                </div>
                
                <style>
                    .photo-hover-container {
                        position: relative;
                        cursor: pointer;
                    }
                    
                    .photo-hover-container:hover .photo-actions {
                        opacity: 1 !important;
                    }
                    
                    .photo-hover-container:hover::before {
                        content: '';
                        position: absolute;
                        top: 0;
                        left: 0;
                        right: 0;
                        bottom: 0;
                        background-color: rgba(0, 0, 0, 0.4);
                        z-index: 9;
                        border-radius: inherit;
                    }
                    
                    .photo-actions {
                        pointer-events: auto;
                    }
                    
                    .photo-actions button {
                        pointer-events: auto;
                    }
                </style>

                <!-- Upload Area -->
                <input type="file" id="photo-upload" name="photos[]" multiple accept="image/jpeg,image/png,image/jpg" style="display: none;">
                <div class="border border-2 border-dashed rounded p-5 text-center mb-4" 
                     id="upload-area"
                     style="border-color: rgba(138, 150, 166, 0.3) !important; cursor: pointer; transition: all 0.3s;"
                     onmouseover="this.style.backgroundColor='var(--lux-white)'; this.style.borderColor='var(--lux-gold)'"
                     onmouseout="this.style.backgroundColor='transparent'; this.style.borderColor='rgba(138, 150, 166, 0.3)'"
                     onclick="document.getElementById('photo-upload').click()">
                    <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 64px; height: 64px; background-color: rgba(10, 26, 47, 0.05);">
                        <i class="fa-solid fa-cloud-arrow-up fs-4 d-block text-center" style="color: var(--lux-gold);"></i>
                    </div>
                    <h3 class="h6 mb-1" style="color: var(--lux-dark-blue); font-weight: 500;">Cliquez pour uploader ou glissez vos fichiers</h3>
                    <p class="small text-lux-gray mb-0">JPG, PNG jusqu'à 10MB</p>
                </div>

                <!-- Preview Grid -->
                <div class="row g-3" id="photos-preview-grid">
                    @if(isset($villa) && count($existingPhotos) > 0)
                        @foreach($existingPhotos as $photo)
                            <div class="col-6 col-md-4 col-lg-3" data-photo-id="{{ $photo['id'] }}">
                                <div class="position-relative rounded overflow-hidden photo-container photo-hover-container" 
                                     style="aspect-ratio: 4/3; background-color: #f8f9fa; {{ $photo['is_primary'] ? 'border: 3px solid var(--lux-gold) !important;' : 'border: 2px solid transparent;' }}">
                                    @if($photo['is_primary'])
                                        <div class="position-absolute top-0 start-0 m-2 bg-lux-gold text-white px-2 py-1 rounded small fw-bold" style="z-index: 11; font-size: 0.7rem;">
                                            <i class="fa-solid fa-star me-1"></i> Couverture
                                        </div>
                                    @endif
                                    <img src="{{ $photo['url'] }}" alt="{{ $photo['name'] }}" class="w-100 h-100" style="object-fit: cover;">
                                    <div class="position-absolute top-50 start-50 translate-middle d-flex gap-2 photo-actions" style="z-index: 10; opacity: 0; transition: opacity 0.3s ease;">
                                        <button type="button" class="btn btn-sm btn-lux-primary rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                                style="width: 36px; height: 36px;"
                                                onclick="setPrimaryPhoto({{ $photo['id'] }}, true)" 
                                                title="Mettre en avant">
                                            <i class="fa-solid fa-star" style="font-size: 0.85rem;"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                                style="width: 36px; height: 36px;"
                                                onclick="removeExistingPhoto({{ $photo['id'] }}, this)"
                                                title="Supprimer">
                                            <i class="fa-solid fa-times" style="font-size: 0.85rem;"></i>
                                        </button>
                                    </div>
                                    <input type="hidden" name="existing_photos[]" value="{{ $photo['id'] }}">
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="col-12 text-center py-4">
                            <p class="text-lux-gray small mb-0">Aucune photo ajoutée. Utilisez la zone ci-dessus pour uploader des images.</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Equipments Tab -->
            <div class="tab-pane fade" id="equipments-content" role="tabpanel" aria-labelledby="equipments-tab">
                <div class="mb-4 border-bottom pb-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h2 class="h4 font-serif mb-2" style="color: var(--lux-dark-blue);">Équipements & Services</h2>
                    <p class="text-lux-gray small mb-0">Sélectionnez les équipements disponibles pour cette villa.</p>
                </div>

                <div class="row g-4">
                    <!-- Category 1 -->
                    <div class="col-12 col-md-6">
                        <h3 class="small text-uppercase fw-semibold mb-3" style="color: var(--lux-dark-blue); letter-spacing: 0.1em;">Extérieur</h3>
                        <div class="d-flex flex-column gap-3">
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="piscine-debordement" {{ in_array('piscine-debordement', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('piscine-debordement', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('piscine-debordement', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Piscine à débordement</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="jacuzzi-prive" {{ in_array('jacuzzi-prive', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('jacuzzi-prive', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('jacuzzi-prive', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Jacuzzi privé</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="acces-plage" {{ in_array('acces-plage', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('acces-plage', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('acces-plage', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Accès plage direct</span>
                            </label>
                        </div>
                    </div>

                    <!-- Category 2 -->
                    <div class="col-12 col-md-6">
                        <h3 class="small text-uppercase fw-semibold mb-3" style="color: var(--lux-dark-blue); letter-spacing: 0.1em;">Intérieur</h3>
                        <div class="d-flex flex-column gap-3">
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="climatisation" {{ in_array('climatisation', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('climatisation', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('climatisation', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Climatisation</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="wifi" {{ in_array('wifi', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('wifi', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('wifi', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Wi-Fi Haut débit</span>
                            </label>
                            <label class="d-flex align-items-center gap-3 cursor-pointer group">
                                <div class="position-relative d-flex align-items-center">
                                    <input type="checkbox" name="equipments[]" value="salle-sport" {{ in_array('salle-sport', $existingEquipments) ? 'checked' : '' }} class="peer" style="width: 20px; height: 20px; border: 2px solid rgba(138, 150, 166, 0.5); border-radius: 4px; appearance: none; background-color: {{ in_array('salle-sport', $existingEquipments) ? 'var(--lux-gold)' : 'transparent' }}; transition: all 0.3s;" 
                                           onchange="this.style.backgroundColor=this.checked?'var(--lux-gold)':'transparent'; this.style.borderColor=this.checked?'var(--lux-gold)':'rgba(138, 150, 166, 0.5)'; const icon=this.nextElementSibling; icon.style.opacity=this.checked?'1':'0';">
                                    <i class="fa-solid fa-check text-white position-absolute" style="font-size: 0.75rem; left: 4px; opacity: {{ in_array('salle-sport', $existingEquipments) ? '1' : '0' }}; transition: opacity 0.3s; pointer-events: none;"></i>
                                </div>
                                <span class="text-lux-gray" style="transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-gray)'">Salle de sport</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prices Tab -->
            <div class="tab-pane fade" id="prices-content" role="tabpanel" aria-labelledby="prices-tab">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h2 class="h4 font-serif mb-0 d-flex align-items-center gap-3" style="color: var(--lux-dark-blue);">
                        <i class="fa-solid fa-tag" style="color: var(--lux-gold);"></i> Tarification
                    </h2>
                </div>

                <div class="row g-4">
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">Prix par nuit (Base)</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="color: var(--lux-dark-blue); font-weight: 500;">€</span>
                            <input type="number" name="base_price_per_night" step="0.01" min="0" value="{{ old('base_price_per_night', $villa->base_price_per_night ?? '') }}" class="form-control ps-5" placeholder="0.00" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);" 
                                   onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">Caution</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="color: var(--lux-dark-blue); font-weight: 500;">€</span>
                            <input type="number" name="deposit_amount" step="0.01" min="0" value="{{ old('deposit_amount', $villa->deposit_amount ?? '') }}" class="form-control ps-5" placeholder="0.00" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);" 
                                   onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                        </div>
                    </div>
                    <div class="col-12 col-md-4">
                        <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">Frais de ménage</label>
                        <div class="position-relative">
                            <span class="position-absolute start-0 top-50 translate-middle-y ms-3" style="color: var(--lux-dark-blue); font-weight: 500;">€</span>
                            <input type="number" name="cleaning_fee" step="0.01" min="0" value="{{ old('cleaning_fee', $villa->cleaning_fee ?? '') }}" class="form-control ps-5" placeholder="0.00" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue);" 
                                   onfocus="this.style.borderColor='var(--lux-gold)'" onblur="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                        </div>
                    </div>
                </div>

                <!-- Section Tarifs Saisonniers -->
                <div class="mt-5 pt-4 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h5 font-serif mb-0 d-flex align-items-center gap-2" style="color: var(--lux-dark-blue);">
                            <i class="fa-solid fa-calendar-days" style="color: var(--lux-gold);"></i> Tarifs Saisonniers
                        </h3>
                        <button type="button" class="btn btn-sm btn-lux-primary text-white d-flex align-items-center gap-2" onclick="addSeasonalPrice()">
                            <i class="fa-solid fa-plus text-white"></i> Ajouter un tarif
                        </button>
                    </div>

                    <p class="small text-lux-gray mb-4">Définissez des prix spécifiques pour chaque saison. Si aucun tarif saisonnier n'est défini, le prix de base sera utilisé.</p>

                    <!-- Liste des tarifs saisonniers existants -->
                    <div id="seasonal-prices-list" class="d-flex flex-column gap-3">
                        @if(isset($villa) && $villa->seasonalPrices->count() > 0)
                            @foreach($villa->seasonalPrices as $seasonalPrice)
                                <div class="card border seasonal-price-item" data-id="{{ $seasonalPrice->id }}" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <div class="card-body p-3">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-12 col-md-4">
                                                <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Saison</label>
                                                <select name="seasonal_prices[{{ $seasonalPrice->id }}][season_id]" class="form-select form-select-sm" style="border-color: rgba(138, 150, 166, 0.2);" required>
                                                    <option value="">Sélectionner une saison</option>
                                                    @foreach($seasons as $season)
                                                        <option value="{{ $season->id }}" {{ $seasonalPrice->season_id == $season->id ? 'selected' : '' }}>
                                                            {{ $season->name }} ({{ $season->period }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-4">
                                                <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Prix par nuit</label>
                                                <div class="position-relative">
                                                    <span class="position-absolute start-0 top-50 translate-middle-y ms-2" style="color: var(--lux-dark-blue); font-weight: 500; font-size: 0.875rem;">€</span>
                                                    <input type="number" name="seasonal_prices[{{ $seasonalPrice->id }}][price_per_night]" step="0.01" min="0" value="{{ $seasonalPrice->price_per_night }}" class="form-control form-control-sm ps-4" placeholder="0.00" style="border-color: rgba(138, 150, 166, 0.2);" required>
                                                </div>
                                            </div>
                                            <div class="col-12 col-md-3">
                                                <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Devise</label>
                                                <select name="seasonal_prices[{{ $seasonalPrice->id }}][currency]" class="form-select form-select-sm" style="border-color: rgba(138, 150, 166, 0.2);">
                                                    <option value="EUR" {{ $seasonalPrice->currency == 'EUR' ? 'selected' : '' }}>EUR</option>
                                                    <option value="USD" {{ $seasonalPrice->currency == 'USD' ? 'selected' : '' }}>USD</option>
                                                </select>
                                            </div>
                                            <div class="col-12 col-md-1 d-flex flex-column justify-content-end">
                                                <label class="form-label small fw-semibold mb-1" style="color: transparent; visibility: hidden;">Action</label>
                                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeSeasonalPrice(this)" title="Supprimer" style="height: 31px;">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <input type="hidden" name="seasonal_prices[{{ $seasonalPrice->id }}][id]" value="{{ $seasonalPrice->id }}">
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center py-4 border rounded" style="border-color: rgba(138, 150, 166, 0.2) !important; background-color: rgba(248, 248, 246, 0.5);">
                                <i class="fa-solid fa-calendar-days fs-4 text-lux-gray opacity-50 mb-2"></i>
                                <p class="small text-lux-gray mb-0">Aucun tarif saisonnier défini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Calendar Tab -->
            <div class="tab-pane fade" id="calendar-content" role="tabpanel" aria-labelledby="calendar-tab">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <h2 class="h4 font-serif mb-0 d-flex align-items-center gap-3" style="color: var(--lux-dark-blue);">
                        <i class="fa-regular fa-calendar" style="color: var(--lux-gold);"></i> Disponibilités
                    </h2>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" style="font-size: 0.75rem;" onclick="importDates()">Importer dates</button>
                        <button type="button" class="btn btn-sm btn-lux-primary text-white" style="font-size: 0.75rem;" onclick="blockPeriod()">Bloquer période</button>
                    </div>
                </div>

                <!-- Calendrier interactif -->
                <div class="row">
                    <div class="col-12 col-lg-8">
                        <div class="mb-3">
                            <label class="form-label small text-uppercase fw-semibold mb-2" style="color: var(--lux-gray); letter-spacing: 0.1em;">Sélectionner une période à bloquer</label>
                            <div class="mb-3">
                                <input type="text" id="selected-period" class="form-control" placeholder="Sélectionnez une période sur le calendrier" style="background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2); color: var(--lux-dark-blue); cursor: pointer;" readonly>
                            </div>
                        </div>
                        <div id="calendar-container" class="mb-4" style="width: 100%;"></div>
                    </div>
                    <div class="col-12 col-lg-4">
                        <div class="card border" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                            <div class="card-header bg-transparent border-bottom" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                                <h5 class="mb-0 small fw-semibold" style="color: var(--lux-dark-blue);">Périodes bloquées</h5>
                            </div>
                            <div class="card-body">
                                <div id="blocked-periods-list" class="d-flex flex-column gap-2">
                                    <p class="text-lux-gray small mb-0 text-center">Aucune période bloquée</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-4 mt-4 pt-3 border-top small" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #fee; border: 1px solid #fcc;"></div>
                        <span class="text-lux-gray">Réservé/Bloqué</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded border" style="width: 16px; height: 16px; background-color: rgba(203, 174, 130, 0.1); border-color: var(--lux-gold) !important;"></div>
                        <span class="text-lux-gray">Sélection</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded border" style="width: 16px; height: 16px; background-color: #f8f9fa; border-color: rgba(138, 150, 166, 0.2) !important;"></div>
                        <span class="text-lux-gray">Disponible</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Champ caché pour les périodes bloquées -->
    <input type="hidden" name="blocked_periods" id="blocked-periods-input" value="{{ json_encode($existingBlockedPeriods ?? []) }}">
    </form>

    <!-- FullCalendar CSS -->
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
    
    <!-- JavaScript pour gérer les tabs et le calendrier -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/fr.global.min.js'></script>
    <script>
        // Initialiser les périodes bloquées avec les données existantes
        let blockedPeriods = @json($existingBlockedPeriods ?? []); // Stocker les périodes bloquées
        let calendarInstance = null;
        let selectedStartDate = null;
        let selectedEndDate = null;

        document.addEventListener('DOMContentLoaded', function() {
            // Gérer les styles des tabs horizontaux
            const horizontalTabs = document.querySelectorAll('#horizontalTabs button[data-bs-toggle="tab"]');
            
            // Fonction pour mettre à jour les styles des onglets
            function updateTabStyles(activeTabId) {
                horizontalTabs.forEach(tab => {
                    if (tab.id === activeTabId) {
                        tab.classList.add('active', 'tab-active');
                        tab.classList.remove('tab-inactive');
                        tab.setAttribute('aria-selected', 'true');
                    } else {
                        tab.classList.remove('active', 'tab-active');
                        tab.classList.add('tab-inactive');
                        tab.setAttribute('aria-selected', 'false');
                    }
                });
            }
            
            // Initialiser les styles pour l'onglet actif au chargement
            const initialActiveTab = document.querySelector('#horizontalTabs button.tab-active, #horizontalTabs button.active');
            if (initialActiveTab) {
                updateTabStyles(initialActiveTab.id);
            }
            
            horizontalTabs.forEach(tab => {
                // Écouter le changement d'onglet APRÈS qu'il soit affiché
                tab.addEventListener('shown.bs.tab', function(e) {
                    const activeTab = e.target;
                    const activeTabId = activeTab.id;
                    
                    // Mettre à jour les styles
                    updateTabStyles(activeTabId);
                    
                    // Détruire le calendrier seulement si on quitte l'onglet calendrier
                    if (calendarInstance && activeTabId !== 'calendar-tab') {
                        requestAnimationFrame(() => {
                            if (calendarInstance && activeTabId !== 'calendar-tab') {
                                calendarInstance.destroy();
                                calendarInstance = null;
                            }
                        });
                    }
                    
                    // Initialiser le calendrier uniquement si on arrive sur l'onglet Calendrier
                    if (activeTabId === 'calendar-tab' && !calendarInstance) {
                        requestAnimationFrame(() => {
                            if (activeTabId === 'calendar-tab' && !calendarInstance) {
                                initCalendar();
                            }
                        });
                    }
                });
            });
            
            // Initialiser le calendrier si l'onglet Calendrier est actif au chargement
            const calendarTab = document.getElementById('calendar-tab');
            if (calendarTab && (calendarTab.classList.contains('active') || calendarTab.classList.contains('tab-active'))) {
                requestAnimationFrame(() => initCalendar());
            }
            
            // Initialiser les périodes bloquées dans le calendrier si elles existent
            if (blockedPeriods.length > 0) {
                updateBlockedPeriodsList();
            }
        });
        
        // Fonction pour supprimer une photo existante
        function removeExistingPhoto(photoId, button) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cette photo ?')) {
                const photoContainer = button.closest('[data-photo-id]');
                if (photoContainer) {
                    photoContainer.remove();
                }
                
                // Créer un nouveau champ masqué avec l'ID pour chaque photo supprimée
                const deletedInput = document.createElement('input');
                deletedInput.type = 'hidden';
                deletedInput.name = 'deleted_photos[]';
                deletedInput.value = photoId;
                document.getElementById('villa-form').appendChild(deletedInput);
            }
        }

        function initCalendar() {
            const calendarContainer = document.getElementById('calendar-container');
            // Vérifier que le conteneur existe et n'est pas déjà initialisé
            if (!calendarContainer || calendarInstance) {
                return;
            }
            
            // Vérifier que le conteneur est visible (évite les initialisations inutiles)
            if (calendarContainer.offsetParent === null) {
                return;
            }

            // Créer les événements pour les périodes bloquées
            const blockedEvents = blockedPeriods.map(period => ({
                id: period.id || 'block-' + Date.now(),
                title: 'Bloqué',
                start: period.start,
                end: new Date(new Date(period.end).setDate(new Date(period.end).getDate() + 1)).toISOString().split('T')[0],
                classNames: ['blocked-period'],
                display: 'background',
                color: '#fee',
                textColor: '#c33',
                extendedProps: {
                    reason: period.reason || 'Bloqué manuellement'
                }
            }));

            calendarInstance = new FullCalendar.Calendar(calendarContainer, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                firstDay: 1, // Lundi
                height: 'auto',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: ''
                },
                validRange: {
                    start: new Date().toISOString().split('T')[0] // À partir d'aujourd'hui
                },
                events: blockedEvents,
                selectable: true,
                selectMirror: true,
                select: function(info) {
                    // Stocker la sélection
                    selectedStartDate = info.startStr;
                    const endDate = new Date(info.end);
                    endDate.setDate(endDate.getDate() - 1); // FullCalendar inclut le jour suivant
                    selectedEndDate = endDate.toISOString().split('T')[0];
                    
                    // Mettre à jour le champ de texte avec la période sélectionnée
                    const startFormatted = new Date(selectedStartDate).toLocaleDateString('fr-FR');
                    const endFormatted = new Date(selectedEndDate).toLocaleDateString('fr-FR');
                    const periodField = document.getElementById('selected-period');
                    if (periodField) {
                        periodField.value = `${startFormatted} - ${endFormatted}`;
                    }
                    
                    console.log('Période sélectionnée:', selectedStartDate, 'à', selectedEndDate);
                },
                unselect: function() {
                    // Ne pas réinitialiser automatiquement - laisser l'utilisateur cliquer sur "Bloquer"
                    // La sélection sera réinitialisée après le blocage
                    console.log('Sélection désélectionnée');
                },
                eventClick: function(info) {
                    // Permettre de supprimer une période bloquée en cliquant dessus
                    if (confirm('Voulez-vous supprimer cette période bloquée ?')) {
                        const event = info.event;
                        const start = event.startStr;
                        const end = new Date(event.end);
                        end.setDate(end.getDate() - 1);
                        const endStr = end.toISOString().split('T')[0];
                        
                        // Retirer de la liste
                        blockedPeriods = blockedPeriods.filter(p => 
                            !(p.start === start && p.end === endStr)
                        );
                        
                        updateBlockedPeriodsList();
                        refreshCalendar();
                    }
                },
            });

            calendarInstance.render();
        }
        
        function refreshCalendar() {
            if (calendarInstance) {
                // Utiliser requestAnimationFrame pour une meilleure performance
                requestAnimationFrame(() => {
                    if (calendarInstance) {
                        calendarInstance.destroy();
                        calendarInstance = null;
                        // Réinitialiser seulement si le conteneur est visible
                        const calendarContainer = document.getElementById('calendar-container');
                        if (calendarContainer && calendarContainer.offsetParent !== null) {
                            initCalendar();
                        }
                    }
                });
            }
        }

        function blockPeriod() {
            console.log('blockPeriod appelé');
            console.log('selectedStartDate:', selectedStartDate);
            console.log('selectedEndDate:', selectedEndDate);
            
            // Utiliser les dates sélectionnées sur le calendrier
            if (!selectedStartDate || !selectedEndDate) {
                alert('Veuillez sélectionner une période sur le calendrier en cliquant-glissant sur les dates');
                return;
            }

            const startDate = selectedStartDate;
            const endDate = selectedEndDate;
            
            console.log('Tentative de bloquer:', startDate, 'à', endDate);
            
            // Vérifier que la date de fin est après la date de début
            if (new Date(endDate) < new Date(startDate)) {
                alert('La date de fin doit être après la date de début');
                return;
            }
            
            // Vérifier que la date de début est dans le futur
            if (new Date(startDate) < new Date().setHours(0, 0, 0, 0)) {
                alert('La date de début doit être aujourd\'hui ou dans le futur');
                return;
            }
            
            // Vérifier qu'il n'y a pas de chevauchement
            const hasOverlap = blockedPeriods.some(period => {
                const periodStart = new Date(period.start);
                const periodEnd = new Date(period.end);
                const newStart = new Date(startDate);
                const newEnd = new Date(endDate);
                
                return (newStart <= periodEnd && newEnd >= periodStart);
            });
            
            if (hasOverlap) {
                alert('Cette période chevauche une période déjà bloquée');
                return;
            }
            
            // Ajouter la période à la liste des périodes bloquées
            const period = {
                start: startDate,
                end: endDate,
                id: Date.now()
            };
            
            blockedPeriods.push(period);
            updateBlockedPeriodsList();
            updateHiddenInputs();
            
            // Réinitialiser la sélection
            selectedStartDate = null;
            selectedEndDate = null;
            document.getElementById('selected-period').value = '';
            
            // Désélectionner sur le calendrier
            if (calendarInstance) {
                calendarInstance.unselect();
            }
            
            // Rafraîchir le calendrier
            refreshCalendar();
        }

        function updateBlockedPeriodsList() {
            const listContainer = document.getElementById('blocked-periods-list');
            if (blockedPeriods.length === 0) {
                listContainer.innerHTML = '<p class="text-lux-gray small mb-0 text-center">Aucune période bloquée</p>';
                return;
            }

            listContainer.innerHTML = blockedPeriods.map((period, index) => {
                const start = new Date(period.start).toLocaleDateString('fr-FR');
                const end = new Date(period.end).toLocaleDateString('fr-FR');
                return `
                    <div class="d-flex justify-content-between align-items-center p-2 border rounded" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                        <div>
                            <span class="small fw-medium" style="color: var(--lux-dark-blue);">${start} - ${end}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeBlockedPeriod(${period.id})" style="font-size: 0.75rem;">
                            <i class="fa-solid fa-times"></i>
                        </button>
                    </div>
                `;
            }).join('');
        }

        function removeBlockedPeriod(id) {
            blockedPeriods = blockedPeriods.filter(p => p.id !== id);
            updateBlockedPeriodsList();
            updateHiddenInputs();
            
            // Rafraîchir le calendrier
            refreshCalendar();
        }

        function updateHiddenInputs() {
            const hiddenInput = document.getElementById('blocked-periods-input');
            if (hiddenInput) {
                hiddenInput.value = JSON.stringify(blockedPeriods);
            }
        }

        // Gestion de l'upload de photos
        let uploadedPhotos = [];
        const photoUpload = document.getElementById('photo-upload');
        const uploadArea = document.getElementById('upload-area');
        const photosGrid = document.getElementById('photos-preview-grid');

        if (photoUpload) {
            photoUpload.addEventListener('change', function(e) {
                const files = Array.from(e.target.files);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const photoId = Date.now() + Math.random();
                            uploadedPhotos.push({
                                id: photoId,
                                file: file,
                                preview: event.target.result
                            });
                            displayPhotoPreview(photoId, event.target.result, file.name);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }

        // Drag and drop
        if (uploadArea) {
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = 'var(--lux-gold)';
                uploadArea.style.backgroundColor = 'var(--lux-white)';
            });

            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = 'rgba(138, 150, 166, 0.3)';
                uploadArea.style.backgroundColor = 'transparent';
            });

            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.style.borderColor = 'rgba(138, 150, 166, 0.3)';
                uploadArea.style.backgroundColor = 'transparent';
                
                const files = Array.from(e.dataTransfer.files);
                files.forEach(file => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const photoId = Date.now() + Math.random();
                            uploadedPhotos.push({
                                id: photoId,
                                file: file,
                                preview: event.target.result
                            });
                            displayPhotoPreview(photoId, event.target.result, file.name);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
        }

        let primaryPhotoId = null; // Pour les nouvelles photos
        
        function displayPhotoPreview(id, previewUrl, fileName, isPrimary = false) {
            if (photosGrid.querySelector('.col-12.text-center')) {
                photosGrid.innerHTML = '';
            }

            const col = document.createElement('div');
            col.className = 'col-12 col-md-4 col-lg-3';
            col.setAttribute('data-photo-id', id);
            col.innerHTML = `
                <div class="position-relative border rounded overflow-hidden photo-container photo-hover-container" 
                     style="aspect-ratio: 4/3; border-color: ${isPrimary ? 'var(--lux-gold)' : 'rgba(138, 150, 166, 0.2)'} !important; ${isPrimary ? 'border-width: 3px;' : 'border-width: 2px;'}">
                    ${isPrimary ? '<div class="position-absolute top-0 start-0 m-2 bg-lux-gold text-white px-2 py-1 rounded small fw-bold" style="z-index: 11; font-size: 0.7rem;"><i class="fa-solid fa-star me-1"></i> Couverture</div>' : ''}
                    <img src="${previewUrl}" alt="${fileName}" class="w-100 h-100" style="object-fit: cover;">
                    <div class="position-absolute top-50 start-50 translate-middle d-flex gap-2 photo-actions" style="z-index: 10; opacity: 0; transition: opacity 0.3s ease;">
                        <button type="button" class="btn btn-sm btn-lux-primary rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                style="width: 36px; height: 36px;"
                                onclick="setPrimaryPhoto(${id}, false)" 
                                title="Mettre en avant">
                            <i class="fa-solid fa-star" style="font-size: 0.85rem;"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-danger rounded-circle p-1 d-flex align-items-center justify-content-center shadow" 
                                style="width: 36px; height: 36px;"
                                onclick="removePhoto(${id})"
                                title="Supprimer">
                            <i class="fa-solid fa-times" style="font-size: 0.85rem;"></i>
                        </button>
                    </div>
                    <input type="hidden" name="primary_photo_id" value="${isPrimary ? id : ''}">
                </div>
            `;
            photosGrid.appendChild(col);
            
            // Mettre à jour uploadedPhotos avec isPrimary
            const photoIndex = uploadedPhotos.findIndex(p => p.id === id);
            if (photoIndex !== -1) {
                uploadedPhotos[photoIndex].isPrimary = isPrimary;
            }
        }
        
        function setPrimaryPhoto(photoId, isExisting) {
            // Si c'est une photo existante, appeler l'API
            if (isExisting) {
                const villaId = {{ isset($villa) ? $villa->id : 0 }};
                
                // Sauvegarder l'onglet actif avant la requête
                const activeTab = document.querySelector('#horizontalTabs button.active');
                const activeTabId = activeTab ? activeTab.id : null;
                
                fetch(`/admin/villas/${villaId}/photos/${photoId}/set-primary`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour l'affichage dynamiquement sans recharger
                        updatePrimaryPhotoDisplay(photoId);
                    } else {
                        alert('Erreur : ' + (data.message || 'Impossible de définir la photo de couverture'));
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Erreur lors de la mise à jour de la photo de couverture');
                });
            } else {
                // Pour les nouvelles photos, mettre à jour visuellement
                primaryPhotoId = photoId;
                
                // Retirer le style "couverture" de toutes les photos
                document.querySelectorAll('.photo-container').forEach(container => {
                    container.style.borderColor = 'rgba(138, 150, 166, 0.2)';
                    container.style.borderWidth = '2px';
                    const badge = container.querySelector('.position-absolute.top-0.start-0');
                    if (badge) badge.remove();
                });
                
                // Appliquer le style à la photo sélectionnée
                const selectedPhoto = document.querySelector(`[data-photo-id="${photoId}"] .photo-container`);
                if (selectedPhoto) {
                    selectedPhoto.style.borderColor = 'var(--lux-gold)';
                    selectedPhoto.style.borderWidth = '3px';
                    
                    // Ajouter le badge
                    const badge = document.createElement('div');
                    badge.className = 'position-absolute top-0 start-0 m-2 bg-lux-gold text-white px-2 py-1 rounded small fw-bold';
                    badge.style.cssText = 'z-index: 11; font-size: 0.7rem;';
                    badge.innerHTML = '<i class="fa-solid fa-star me-1"></i> Couverture';
                    selectedPhoto.insertBefore(badge, selectedPhoto.firstChild);
                    
                    // Mettre à jour le champ caché
                    document.querySelectorAll('input[name="primary_photo_id"]').forEach(input => {
                        input.value = '';
                    });
                    const hiddenInput = selectedPhoto.querySelector('input[name="primary_photo_id"]');
                    if (hiddenInput) {
                        hiddenInput.value = photoId;
                    }
                }
                
                // Mettre à jour uploadedPhotos
                uploadedPhotos.forEach(photo => {
                    photo.isPrimary = (photo.id === photoId);
                });
            }
        }
        
        // Fonction pour mettre à jour l'affichage de la photo de couverture (pour les photos existantes)
        function updatePrimaryPhotoDisplay(selectedPhotoId) {
            // Retirer le style "couverture" de toutes les photos existantes
            document.querySelectorAll('[data-photo-id]').forEach(photoElement => {
                const photoId = parseInt(photoElement.getAttribute('data-photo-id'));
                const container = photoElement.querySelector('.photo-container');
                
                if (container) {
                    // Retirer le badge "Couverture" s'il existe
                    const existingBadges = container.querySelectorAll('.position-absolute.top-0.start-0');
                    existingBadges.forEach(badge => {
                        if (badge.textContent.includes('Couverture')) {
                            badge.remove();
                        }
                    });
                    
                    // Retirer le style de bordure dorée de toutes les photos
                    container.style.border = '2px solid transparent';
                    container.style.borderWidth = '2px';
                    
                    // Appliquer le style à la photo sélectionnée
                    if (photoId == selectedPhotoId) {
                        container.style.border = '3px solid var(--lux-gold)';
                        container.style.borderWidth = '3px';
                        
                        // Ajouter le badge "Couverture"
                        const badge = document.createElement('div');
                        badge.className = 'position-absolute top-0 start-0 m-2 bg-lux-gold text-white px-2 py-1 rounded small fw-bold';
                        badge.style.cssText = 'z-index: 11; font-size: 0.7rem;';
                        badge.innerHTML = '<i class="fa-solid fa-star me-1"></i> Couverture';
                        container.insertBefore(badge, container.firstChild);
                    }
                }
            });
        }

        function removePhoto(id) {
            uploadedPhotos = uploadedPhotos.filter(p => p.id !== id);
            const photoElement = event.target.closest('.col-12');
            if (photoElement) {
                photoElement.remove();
            }
            if (uploadedPhotos.length === 0) {
                photosGrid.innerHTML = `
                    <div class="col-12 text-center py-4">
                        <p class="text-lux-gray small mb-0">Aucune photo ajoutée. Utilisez la zone ci-dessus pour uploader des images.</p>
                    </div>
                `;
            }
        }

        function importDates() {
            @if(!isset($villa))
                alert('Veuillez d\'abord enregistrer la villa avant d\'importer des dates. Vous pourrez importer les dates après la création.');
                return;
            @endif
            // Afficher la modal d'import
            const modal = new bootstrap.Modal(document.getElementById('importDatesModal'));
            modal.show();
        }

        // Gestion de l'import depuis URL
        function importFromUrl() {
            const urlInput = document.getElementById('ical-url-input');
            const url = urlInput.value.trim();
            
            if (!url) {
                alert('Veuillez saisir une URL iCal');
                return;
            }
            
            // Valider que c'est une URL valide
            try {
                new URL(url);
            } catch (e) {
                alert('URL invalide');
                return;
            }
            
            const submitBtn = document.getElementById('import-url-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Import en cours...';
            
            // Appeler l'API pour importer
            fetch('{{ route("admin.villas.import-ical") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    villa_id: {{ isset($villa) ? $villa->id : 'null' }},
                    ical_url: url,
                    import_type: 'url'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Ajouter les périodes importées aux périodes bloquées
                    if (data.events && data.events.length > 0) {
                        data.events.forEach(event => {
                            const period = {
                                start: event.start,
                                end: event.end,
                                id: Date.now() + Math.random(),
                                reason: event.summary || 'Importé depuis iCal'
                            };
                            blockedPeriods.push(period);
                        });
                        
                        updateBlockedPeriodsList();
                        updateHiddenInputs();
                        refreshCalendar();
                        
                        alert(`${data.events.length} période(s) importée(s) avec succès`);
                    } else {
                        alert('Aucun événement trouvé dans le fichier iCal');
                    }
                    
                    // Fermer la modal
                    bootstrap.Modal.getInstance(document.getElementById('importDatesModal')).hide();
                    urlInput.value = '';
                } else {
                    alert(data.message || 'Erreur lors de l\'import');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'import : ' + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        // Gestion de l'import depuis fichier
        function importFromFile() {
            const fileInput = document.getElementById('ical-file-input');
            const file = fileInput.files[0];
            
            if (!file) {
                alert('Veuillez sélectionner un fichier iCal');
                return;
            }
            
            if (!file.name.endsWith('.ics')) {
                alert('Le fichier doit être au format .ics');
                return;
            }
            
            const submitBtn = document.getElementById('import-file-btn');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Import en cours...';
            
            const formData = new FormData();
            formData.append('ical_file', file);
            formData.append('villa_id', {{ isset($villa) ? $villa->id : 'null' }});
            formData.append('import_type', 'file');
            
            fetch('{{ route("admin.villas.import-ical") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.events && data.events.length > 0) {
                        data.events.forEach(event => {
                            const period = {
                                start: event.start,
                                end: event.end,
                                id: Date.now() + Math.random(),
                                reason: event.summary || 'Importé depuis iCal'
                            };
                            blockedPeriods.push(period);
                        });
                        
                        updateBlockedPeriodsList();
                        updateHiddenInputs();
                        refreshCalendar();
                        
                        alert(`${data.events.length} période(s) importée(s) avec succès`);
                    } else {
                        alert('Aucun événement trouvé dans le fichier iCal');
                    }
                    
                    bootstrap.Modal.getInstance(document.getElementById('importDatesModal')).hide();
                    fileInput.value = '';
                } else {
                    alert(data.message || 'Erreur lors de l\'import');
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert('Erreur lors de l\'import : ' + error.message);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        }

        // Gestion des tarifs saisonniers
        let seasonalPriceCounter = {{ isset($villa) && $villa->seasonalPrices->count() > 0 ? $villa->seasonalPrices->max('id') + 1 : 1000 }};

        function addSeasonalPrice() {
            const listContainer = document.getElementById('seasonal-prices-list');
            const emptyMessage = listContainer.querySelector('.text-center');
            if (emptyMessage) {
                emptyMessage.remove();
            }

            const newId = seasonalPriceCounter++;
            const seasons = @json($seasons ?? []);
            
            let seasonOptions = '<option value="">Sélectionner une saison</option>';
            seasons.forEach(season => {
                const months = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];
                const period = months[season.start_month - 1] + ' ' + season.start_day + ' - ' + months[season.end_month - 1] + ' ' + season.end_day;
                seasonOptions += `<option value="${season.id}">${season.name} (${period})</option>`;
            });

            const newItem = document.createElement('div');
            newItem.className = 'card border seasonal-price-item';
            newItem.setAttribute('data-id', 'new-' + newId);
            newItem.innerHTML = `
                <div class="card-body p-3">
                    <div class="row g-3 align-items-center">
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Saison</label>
                            <select name="seasonal_prices[new-${newId}][season_id]" class="form-select form-select-sm" style="border-color: rgba(138, 150, 166, 0.2);" required>
                                ${seasonOptions}
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Prix par nuit</label>
                            <div class="position-relative">
                                <span class="position-absolute start-0 top-50 translate-middle-y ms-2" style="color: var(--lux-dark-blue); font-weight: 500; font-size: 0.875rem;">€</span>
                                <input type="number" name="seasonal_prices[new-${newId}][price_per_night]" step="0.01" min="0" class="form-control form-control-sm ps-4" placeholder="0.00" style="border-color: rgba(138, 150, 166, 0.2);" required>
                            </div>
                        </div>
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold mb-1" style="color: var(--lux-dark-blue);">Devise</label>
                            <select name="seasonal_prices[new-${newId}][currency]" class="form-select form-select-sm" style="border-color: rgba(138, 150, 166, 0.2);">
                                <option value="EUR" selected>EUR</option>
                                <option value="USD">USD</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-1 d-flex flex-column justify-content-end">
                            <label class="form-label small fw-semibold mb-1" style="color: transparent; visibility: hidden;">Action</label>
                            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeSeasonalPrice(this)" title="Supprimer" style="height: 31px;">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            listContainer.appendChild(newItem);
        }

        function removeSeasonalPrice(button) {
            const item = button.closest('.seasonal-price-item');
            if (confirm('Êtes-vous sûr de vouloir supprimer ce tarif saisonnier ?')) {
                item.remove();
                
                // Si plus aucun tarif, afficher le message vide
                const listContainer = document.getElementById('seasonal-prices-list');
                if (listContainer.querySelectorAll('.seasonal-price-item').length === 0) {
                    listContainer.innerHTML = `
                        <div class="text-center py-4 border rounded" style="border-color: rgba(138, 150, 166, 0.2) !important; background-color: rgba(248, 248, 246, 0.5);">
                            <i class="fa-solid fa-calendar-days fs-4 text-lux-gray opacity-50 mb-2"></i>
                            <p class="small text-lux-gray mb-0">Aucun tarif saisonnier défini</p>
                        </div>
                    `;
                }
            }
        }

        // Gestionnaire de soumission du formulaire
        const villaForm = document.getElementById('villa-form');
        if (villaForm) {
            villaForm.addEventListener('submit', function(e) {
                // Ajouter les photos au formulaire
                const photoInput = document.getElementById('photo-upload');
                if (photoInput && uploadedPhotos.length > 0) {
                    const dataTransfer = new DataTransfer();
                    uploadedPhotos.forEach(photo => {
                        dataTransfer.items.add(photo.file);
                    });
                    photoInput.files = dataTransfer.files;
                }

                // Validation personnalisée : vérifier les champs obligatoires
                const requiredFields = villaForm.querySelectorAll('[required]');
                let firstInvalidField = null;
                let firstInvalidTab = null;

                requiredFields.forEach(field => {
                    if (!field.value.trim() && !field.validity.valid) {
                        if (!firstInvalidField) {
                            firstInvalidField = field;
                            // Trouver l'onglet parent
                            const tabPane = field.closest('.tab-pane');
                            if (tabPane) {
                                const tabId = tabPane.getAttribute('id');
                                // Trouver le bouton d'onglet correspondant
                                const tabButton = document.querySelector(`button[data-bs-target="#${tabId}"]`);
                                if (tabButton) {
                                    firstInvalidTab = tabButton;
                                }
                            }
                        }
                        // Marquer le champ comme invalide
                        field.classList.add('is-invalid');
                        field.style.borderColor = '#dc3545';
                    } else {
                        field.classList.remove('is-invalid');
                        field.style.borderColor = '';
                    }
                });

                // Si des champs sont invalides, basculer vers l'onglet concerné
                if (firstInvalidField) {
                    e.preventDefault();
                    
                    // Afficher un message d'erreur
                    let errorMessage = document.getElementById('form-validation-error');
                    if (!errorMessage) {
                        errorMessage = document.createElement('div');
                        errorMessage.id = 'form-validation-error';
                        errorMessage.className = 'alert alert-danger alert-dismissible fade show mb-4';
                        errorMessage.setAttribute('role', 'alert');
                        errorMessage.innerHTML = `
                            <i class="fa-solid fa-exclamation-triangle me-2"></i>
                            <strong>Veuillez remplir tous les champs obligatoires</strong>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        `;
                        villaForm.parentNode.insertBefore(errorMessage, villaForm);
                    }
                    
                    // Basculer vers l'onglet contenant le premier champ invalide
                    if (firstInvalidTab) {
                        const tab = new bootstrap.Tab(firstInvalidTab);
                        tab.show();
                        
                        // Faire défiler vers le champ invalide après un court délai
                        setTimeout(() => {
                            firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            firstInvalidField.focus();
                        }, 300);
                    } else {
                        // Si on ne trouve pas l'onglet, faire défiler vers le champ
                        firstInvalidField.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        firstInvalidField.focus();
                    }
                    
                    return false;
                }

                // Si tout est valide, permettre la soumission
                return true;
            });
        }

        // Fonction pour générer l'aperçu
        function generatePreview() {
            const form = document.getElementById('villa-form');
            if (!form) return;

            // Collecter les données du formulaire
            const formData = new FormData(form);
            const previewData = {
                name: formData.get('name') || 'Nom de la villa',
                island: getSelectedIslandName(),
                surface: formData.get('surface_area') || '-',
                bedrooms: formData.get('bedrooms') || '1',
                bathrooms: formData.get('bathrooms') || '1',
                maxCapacity: formData.get('max_capacity') || '2',
                shortDescription: formData.get('short_description') || 'Aucune description courte',
                description: formData.get('description') || 'Aucune description',
                address: formData.get('address') || 'Adresse non renseignée',
                basePrice: formData.get('base_price_per_night') || '0.00',
                cleaningFee: formData.get('cleaning_fee') || '0.00',
                depositAmount: formData.get('deposit_amount') || '0.00',
                serviceFee: formData.get('service_fee_percentage') || '0.00',
                checkIn: formData.get('check_in_time') || '16:00',
                checkOut: formData.get('check_out_time') || '10:00',
                minStay: formData.get('minimum_stay_nights') || '3',
                status: formData.get('status') || 'active',
                isActive: formData.get('status') === 'active',
                isFeatured: form.querySelector('[name="is_featured"]')?.checked || false,
                equipments: getSelectedEquipments(),
                photos: collectAllPhotos(),
                blockedPeriods: blockedPeriods
            };

            // Remplir la modale avec les données
            populatePreviewModal(previewData);
        }

        // Fonction pour collecter toutes les photos (existantes + nouvelles)
        function collectAllPhotos() {
            const allPhotos = [];
            
            // Collecter les photos existantes (en mode édition)
            const existingPhotoImages = document.querySelectorAll('#photos-preview-grid [data-photo-id] img');
            existingPhotoImages.forEach(img => {
                if (img.src && !img.src.includes('placeholder')) {
                    allPhotos.push(img.src);
                }
            });
            
            // Collecter les nouvelles photos uploadées
            uploadedPhotos.forEach(photo => {
                if (photo.preview) {
                    allPhotos.push(photo.preview);
                }
            });
            
            return allPhotos;
        }

        function getSelectedIslandName() {
            const select = document.querySelector('[name="island_id"]');
            if (select && select.value) {
                return select.options[select.selectedIndex].text;
            }
            return 'Île non sélectionnée';
        }

        function getSelectedEquipments() {
            const checkboxes = document.querySelectorAll('[name="equipments[]"]:checked');
            return Array.from(checkboxes).map(cb => {
                const label = cb.closest('label');
                return label ? label.querySelector('span').textContent : cb.value;
            });
        }

        function populatePreviewModal(data) {
            // Nom et île
            document.getElementById('preview-name').textContent = data.name;
            document.getElementById('preview-island').textContent = data.island;

            // Informations principales
            document.getElementById('preview-bedrooms').textContent = data.bedrooms;
            document.getElementById('preview-bathrooms').textContent = data.bathrooms;
            document.getElementById('preview-capacity').textContent = data.maxCapacity;
            document.getElementById('preview-surface').textContent = data.surface + ' m²';

            // Descriptions
            document.getElementById('preview-short-desc').textContent = data.shortDescription;
            document.getElementById('preview-full-desc').textContent = data.description || 'Aucune description complète';

            // Adresse
            document.getElementById('preview-address').textContent = data.address;

            // Tarifs
            document.getElementById('preview-base-price').textContent = parseFloat(data.basePrice).toFixed(2) + ' €';
            document.getElementById('preview-cleaning-fee').textContent = parseFloat(data.cleaningFee).toFixed(2) + ' €';
            document.getElementById('preview-deposit').textContent = parseFloat(data.depositAmount).toFixed(2) + ' €';
            document.getElementById('preview-service-fee').textContent = parseFloat(data.serviceFee).toFixed(2) + ' %';

            // Horaires
            document.getElementById('preview-checkin').textContent = data.checkIn;
            document.getElementById('preview-checkout').textContent = data.checkOut;
            document.getElementById('preview-min-stay').textContent = data.minStay + ' nuit(s)';

            // Statut
            const statusBadge = document.getElementById('preview-status');
            if (data.status === 'active') {
                statusBadge.className = 'badge bg-success';
                statusBadge.textContent = 'Active';
            } else if (data.status === 'maintenance') {
                statusBadge.className = 'badge bg-warning text-dark';
                statusBadge.textContent = 'Maintenance';
            } else {
                statusBadge.className = 'badge bg-secondary';
                statusBadge.textContent = 'Inactive';
            }

            if (data.isFeatured) {
                const featuredBadge = document.getElementById('preview-featured');
                if (featuredBadge) {
                    featuredBadge.style.display = 'inline-block';
                }
            }

            // Équipements
            const equipmentsList = document.getElementById('preview-equipments');
            if (data.equipments.length > 0) {
                equipmentsList.innerHTML = data.equipments.map(eq => 
                    `<span class="badge bg-light text-dark me-2 mb-2">${eq}</span>`
                ).join('');
            } else {
                equipmentsList.innerHTML = '<span class="text-muted">Aucun équipement sélectionné</span>';
            }

            // Photos
            const photosContainer = document.getElementById('preview-photos');
            if (data.photos && data.photos.length > 0) {
                photosContainer.innerHTML = data.photos.map((photo, index) => 
                    `<div class="col-6 col-md-4 col-lg-3">
                        <div class="rounded overflow-hidden position-relative" style="aspect-ratio: 4/3; background-color: #f8f9fa;">
                            <img src="${photo}" alt="Photo ${index + 1}" class="w-100 h-100" style="object-fit: cover;" onerror="this.src='https://via.placeholder.com/400x300?text=Photo+${index + 1}'">
                        </div>
                    </div>`
                ).join('');
            } else {
                photosContainer.innerHTML = '<div class="col-12 text-center py-4"><p class="text-muted mb-0">Aucune photo ajoutée</p></div>';
            }

            // Périodes bloquées
            const blockedPeriodsList = document.getElementById('preview-blocked-periods');
            if (data.blockedPeriods.length > 0) {
                blockedPeriodsList.innerHTML = data.blockedPeriods.map(period => {
                    const start = new Date(period.start).toLocaleDateString('fr-FR');
                    const end = new Date(period.end).toLocaleDateString('fr-FR');
                    return `<div class="small mb-1"><i class="fa-solid fa-calendar-xmark text-danger me-2"></i>${start} - ${end}</div>`;
                }).join('');
            } else {
                blockedPeriodsList.innerHTML = '<span class="text-muted small">Aucune période bloquée</span>';
            }
        }
    </script>

    <!-- Modal d'aperçu -->
    <div class="modal fade" id="previewModal" tabindex="-1" aria-labelledby="previewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <div class="modal-header border-bottom" style="border-color: rgba(138, 150, 166, 0.2) !important; background-color: var(--lux-dark-blue);">
                    <h5 class="modal-title text-white" id="previewModalLabel">
                        <i class="fa-regular fa-eye me-2"></i> Aperçu de la villa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4" style="background-color: #f8f9fa;">
                    <!-- En-tête -->
                    <div class="mb-4">
                        <h2 class="h3 font-serif mb-2" style="color: var(--lux-dark-blue); font-family: 'Playfair Display', serif;" id="preview-name">Nom de la villa</h2>
                        <p class="text-muted mb-0">
                            <i class="fa-solid fa-location-dot me-2" style="color: var(--lux-gold);"></i>
                            <span id="preview-island">Île</span>
                        </p>
                        <div class="mt-2">
                            <span class="badge bg-secondary me-2" id="preview-status">Statut</span>
                            <span class="badge bg-warning text-dark" id="preview-featured" style="display: none;">
                                <i class="fa-solid fa-star me-1"></i> Mise en avant
                            </span>
                        </div>
                    </div>

                    <!-- Photos -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Photos</h4>
                        <div class="row g-2" id="preview-photos">
                            <!-- Photos seront ajoutées ici -->
                        </div>
                    </div>

                    <!-- Informations principales -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Informations principales</h4>
                        <div class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-white rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <i class="fa-solid fa-bed fs-4 mb-2" style="color: var(--lux-gold);"></i>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-bedrooms">-</div>
                                    <small class="text-muted">Chambres</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-white rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <i class="fa-solid fa-bath fs-4 mb-2" style="color: var(--lux-gold);"></i>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-bathrooms">-</div>
                                    <small class="text-muted">Salles de bain</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-white rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <i class="fa-solid fa-users fs-4 mb-2" style="color: var(--lux-gold);"></i>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-capacity">-</div>
                                    <small class="text-muted">Capacité max.</small>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="text-center p-3 bg-white rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <i class="fa-solid fa-ruler-combined fs-4 mb-2" style="color: var(--lux-gold);"></i>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-surface">-</div>
                                    <small class="text-muted">Surface</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Description</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                            <p class="mb-2 fw-medium" style="color: var(--lux-dark-blue);" id="preview-short-desc">Description courte</p>
                            <p class="text-muted small mb-0" id="preview-full-desc">Description complète</p>
                        </div>
                    </div>

                    <!-- Adresse -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Adresse</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                            <p class="mb-0 text-muted" id="preview-address">Adresse</p>
                        </div>
                    </div>

                    <!-- Équipements -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Équipements</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;" id="preview-equipments">
                            <!-- Équipements seront ajoutés ici -->
                        </div>
                    </div>

                    <!-- Tarifs -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Tarification</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Prix par nuit</small>
                                    <div class="fw-bold" style="color: var(--lux-gold);" id="preview-base-price">0.00 €</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Frais de ménage</small>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-cleaning-fee">0.00 €</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Caution</small>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-deposit">0.00 €</div>
                                </div>
                                <div class="col-6 col-md-3">
                                    <small class="text-muted d-block">Frais de service</small>
                                    <div class="fw-bold" style="color: var(--lux-dark-blue);" id="preview-service-fee">0.00 %</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Horaires et conditions -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Horaires et conditions</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                            <div class="row g-3">
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Arrivée</small>
                                    <div class="fw-medium" style="color: var(--lux-dark-blue);" id="preview-checkin">16:00</div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Départ</small>
                                    <div class="fw-medium" style="color: var(--lux-dark-blue);" id="preview-checkout">10:00</div>
                                </div>
                                <div class="col-6 col-md-4">
                                    <small class="text-muted d-block">Séjour minimum</small>
                                    <div class="fw-medium" style="color: var(--lux-dark-blue);" id="preview-min-stay">3 nuit(s)</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Périodes bloquées -->
                    <div class="mb-4">
                        <h4 class="h5 mb-3" style="color: var(--lux-dark-blue);">Périodes bloquées</h4>
                        <div class="bg-white p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2) !important;" id="preview-blocked-periods">
                            <!-- Périodes seront ajoutées ici -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal d'import de dates -->
    <div class="modal fade" id="importDatesModal" tabindex="-1" aria-labelledby="importDatesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content" style="border-radius: 0.75rem;">
                <div class="modal-header border-bottom" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                    <h5 class="modal-title font-serif" id="importDatesModalLabel" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                        <i class="fa-solid fa-calendar-arrow-down me-2" style="color: var(--lux-gold);"></i> Importer des dates
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <p class="small text-lux-gray mb-4">Importez des dates depuis un fichier iCal ou une URL iCal (Airbnb, Booking.com, etc.)</p>
                    
                    <!-- Onglets pour URL ou Fichier -->
                    <ul class="nav nav-tabs mb-4 border-0" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active border-0 bg-transparent text-lux-dark-blue fw-medium" id="url-tab" data-bs-toggle="tab" data-bs-target="#url-import" type="button" role="tab" style="border-bottom: 2px solid var(--lux-gold) !important;">
                                <i class="fa-solid fa-link me-2"></i> Depuis une URL
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link border-0 bg-transparent text-lux-gray fw-medium" id="file-tab" data-bs-toggle="tab" data-bs-target="#file-import" type="button" role="tab" style="border-bottom: 2px solid transparent !important;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="if(!this.classList.contains('active')) this.style.color='var(--lux-gray)'">
                                <i class="fa-solid fa-file-upload me-2"></i> Depuis un fichier
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content">
                        <!-- Import depuis URL -->
                        <div class="tab-pane fade show active" id="url-import" role="tabpanel" aria-labelledby="url-tab">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-2" style="color: var(--lux-dark-blue);">URL iCal</label>
                                <input type="url" id="ical-url-input" class="form-control" placeholder="https://calendar.airbnb.com/ical/..." style="border-color: rgba(138, 150, 166, 0.2);">
                                <small class="text-lux-gray mt-1 d-block">Collez l'URL iCal de votre plateforme (Airbnb, Booking.com, etc.)</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-lux-primary text-white flex-grow-1" id="import-url-btn" onclick="importFromUrl()">
                                    <i class="fa-solid fa-download me-2"></i> Importer
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            </div>
                        </div>
                        
                        <!-- Import depuis fichier -->
                        <div class="tab-pane fade" id="file-import" role="tabpanel" aria-labelledby="file-tab">
                            <div class="mb-3">
                                <label class="form-label small fw-semibold mb-2" style="color: var(--lux-dark-blue);">Fichier iCal (.ics)</label>
                                <input type="file" id="ical-file-input" class="form-control" accept=".ics" style="border-color: rgba(138, 150, 166, 0.2);">
                                <small class="text-lux-gray mt-1 d-block">Sélectionnez un fichier iCal (.ics) à importer</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-lux-primary text-white flex-grow-1" id="import-file-btn" onclick="importFromFile()">
                                    <i class="fa-solid fa-upload me-2"></i> Importer
                                </button>
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuler</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 p-3 rounded" style="background-color: rgba(203, 174, 130, 0.1); border-left: 3px solid var(--lux-gold);">
                        <p class="small fw-medium mb-1" style="color: var(--lux-dark-blue);">
                            <i class="fa-solid fa-info-circle me-2" style="color: var(--lux-gold);"></i> Comment trouver l'URL iCal ?
                        </p>
                        <ul class="small text-lux-gray mb-0 ps-3">
                            <li><strong>Airbnb:</strong> Paramètres → Calendrier → Synchroniser les calendriers</li>
                            <li><strong>Booking.com:</strong> Extranet → Calendrier → Synchronisation</li>
                            <li><strong>VRBO:</strong> Calendrier → Paramètres → URL iCal</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

