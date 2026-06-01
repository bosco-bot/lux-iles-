@extends('layouts.admin')

@section('title', 'Ajouter une Destination | LUXÎLES - Back-office')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.islands') }}" class="text-white hover-lux-gold" style="text-decoration: none;">Destinations</a>
    <span class="text-white mx-2">/</span>
    <span class="text-white">Ajouter</span>
@endsection

@section('content')
    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Ajouter une Destination
            </h1>
            <p class="text-muted small mb-0">Créez une nouvelle destination pour votre plateforme.</p>
        </div>
        <a href="{{ route('admin.islands') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2 mt-3 mt-md-0">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Retour à la liste</span>
        </a>
    </div>

    <!-- Messages d'erreur/succès -->
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Formulaire de création -->
    <div class="row">
        <div class="col-lg-8">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <form action="{{ route('admin.islands.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Nom -->
                        <div class="mb-4">
                            <label for="name" class="form-label fw-medium" style="color: var(--lux-dark-blue);">
                                Nom de l'île <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Code -->
                        <div class="mb-4">
                            <label for="code" class="form-label fw-medium" style="color: var(--lux-dark-blue);">
                                Code <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control @error('code') is-invalid @enderror" id="code" name="code" value="{{ old('code') }}" required maxlength="10">
                            @error('code')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Code ISO ou abréviation (ex: MQ, GP, BL)</small>
                        </div>

                        <!-- Pays -->
                        <div class="mb-4">
                            <label for="country" class="form-label fw-medium" style="color: var(--lux-dark-blue);">
                                Pays
                            </label>
                            <input type="text" class="form-control @error('country') is-invalid @enderror" id="country" name="country" value="{{ old('country', 'France') }}">
                            @error('country')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-4">
                            <label for="description" class="form-label fw-medium" style="color: var(--lux-dark-blue);">
                                Description
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Description de la destination qui apparaîtra sur la page d'accueil</small>
                        </div>

                        <!-- Upload d'image -->
                        <div class="mb-4">
                            <label for="image" class="form-label fw-medium" style="color: var(--lux-dark-blue);">
                                Image de la destination <span class="text-danger">*</span>
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" id="image" name="image" accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImage(this)" required>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Formats acceptés : JPEG, PNG, WebP (max 10MB)</small>
                            
                            <!-- Aperçu de l'image -->
                            <div id="image-preview-container" class="mt-3" style="display: none;">
                                <label class="form-label fw-medium" style="color: var(--lux-dark-blue);">Aperçu</label>
                                <div class="border rounded p-3" style="border-color: rgba(138, 150, 166, 0.2) !important;">
                                    <img id="image-preview" src="" alt="Aperçu" style="max-width: 100%; max-height: 300px; border-radius: 0.5rem; object-fit: cover;">
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-flex align-items-center gap-3 pt-3 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                            <button type="submit" class="btn btn-lux-primary text-white d-flex align-items-center gap-2">
                                <i class="fa-solid fa-save"></i>
                                <span>Créer la destination</span>
                            </button>
                            <a href="{{ route('admin.islands') }}" class="btn btn-outline-secondary">
                                Annuler
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Sidebar avec informations -->
        <div class="col-lg-4 mt-4 mt-lg-0">
            <div class="card border shadow-sm" style="border-color: rgba(138, 150, 166, 0.1) !important; border-radius: 0.75rem;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3" style="color: var(--lux-dark-blue);">Informations</h5>
                    <div class="alert alert-info mb-0" style="background-color: rgba(10, 26, 47, 0.05); border-color: rgba(10, 26, 47, 0.1);">
                        <i class="fa-solid fa-info-circle me-2"></i>
                        <small>La destination créée apparaîtra immédiatement sur la page d'accueil dans la section "Nos Destinations d'Exception" si elle fait partie des 3 premières îles par ordre alphabétique.</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Aperçu de l'image en temps réel
        function previewImage(input) {
            const previewContainer = document.getElementById('image-preview-container');
            const preview = document.getElementById('image-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.style.display = 'none';
            }
        }
    </script>
@endsection

