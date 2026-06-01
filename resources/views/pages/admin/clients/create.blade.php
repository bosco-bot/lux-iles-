@extends('layouts.admin')

@section('title', 'Nouveau Client | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.clients') }}" class="text-white-50">Clients</a>
    <span class="text-white"> / Nouveau client</span>
@endsection

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Nouveau client
            </h1>
            <p class="small text-lux-greyBlue mb-0">Création manuelle d'un compte client — un email d'invitation sera envoyé pour définir le mot de passe.</p>
        </div>
        <a href="{{ route('admin.clients') }}" class="btn btn-outline-secondary btn-sm mt-3 mt-md-0">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour à la liste
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

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

    <div class="row">
        <div class="col-lg-8">
            <section class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(0,0,0,0.05) !important;">
                <form action="{{ route('admin.clients.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="first_name" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Prénom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" value="{{ old('first_name') }}" required maxlength="100">
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="last_name" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Nom <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" value="{{ old('last_name') }}" required maxlength="100">
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required maxlength="255">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Téléphone</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" maxlength="20">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="alert alert-info mt-4 mb-0 small" role="alert">
                        <i class="fa-solid fa-envelope me-2"></i>
                        Le client recevra un email avec un lien sécurisé (valable 24 h) pour définir son mot de passe et accéder à son espace client.
                    </div>

                    <div class="d-flex gap-2 mt-4 pt-3 border-top" style="border-color: rgba(0,0,0,0.05) !important;">
                        <button type="submit" class="btn text-white" style="background-color: var(--lux-dark-blue);">
                            <i class="fa-solid fa-user-plus me-2"></i>Créer et envoyer l'invitation
                        </button>
                        <a href="{{ route('admin.clients') }}" class="btn btn-outline-secondary">Annuler</a>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection
