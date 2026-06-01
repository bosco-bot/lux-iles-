@extends('layouts.app')

@section('title', 'Inscription | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Register Section -->
    <section class="py-5" style="margin-top: 80px; min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-7">
                    <div class="card border shadow-lg" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('Social_Media_Profil_Bleu.png') }}" alt="LUX Îles" class="mb-3" style="height: 80px; width: auto; object-fit: contain;">
                                </div>
                                <h1 class="h3 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Créer un compte</h1>
                                <p class="text-muted small">Rejoignez LUXÎLES et accédez à nos villas de prestige</p>
                            </div>

                            <form id="register-form">
                                @csrf
                                <div class="row g-3">
                                    <!-- Prénom -->
                                    <div class="col-md-6">
                                        <label for="first_name" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" id="first_name" name="first_name" class="form-control" placeholder="Jean" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Nom -->
                                    <div class="col-md-6">
                                        <label for="last_name" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Nom <span class="text-danger">*</span></label>
                                        <input type="text" id="last_name" name="last_name" class="form-control" placeholder="Dupont" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label for="email" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Email <span class="text-danger">*</span></label>
                                        <input type="email" id="email" name="email" class="form-control" placeholder="jean.dupont@email.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Téléphone -->
                                    <div class="col-md-6">
                                        <label for="phone" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Téléphone</label>
                                        <input type="tel" id="phone" name="phone" class="form-control" placeholder="+33 6 12 34 56 78" style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Mot de passe -->
                                    <div class="col-md-6">
                                        <label for="password" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8" style="border-color: rgba(138, 150, 166, 0.2);">
                                        <small class="text-muted" style="font-size: 0.75rem;">Minimum 8 caractères</small>
                                    </div>
                                    
                                    <!-- Confirmation mot de passe -->
                                    <div class="col-md-6">
                                        <label for="password_confirmation" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                </div>

                                <!-- Conditions générales -->
                                <div class="form-check mt-4 mb-4">
                                    <input class="form-check-input" type="checkbox" id="acceptTerms" required style="border-color: rgba(138, 150, 166, 0.3);">
                                    <label class="form-check-label small" for="acceptTerms" style="color: var(--lux-gray);">
                                        J'accepte les <a href="#" class="text-lux-gold text-decoration-none">conditions générales</a> et la <a href="{{ route('politique-confidentialite') }}" class="text-lux-gold text-decoration-none">politique de confidentialité</a> <span class="text-danger">*</span>
                                    </label>
                                </div>

                                <button type="submit" class="btn btn-lux-primary w-100 mb-3">Créer mon compte</button>
                                
                                <div class="text-center">
                                    <p class="small text-muted mb-0">
                                        Déjà un compte ? 
                                        <a href="{{ route('login') }}" class="text-lux-gold text-decoration-none">Se connecter</a>
                                    </p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="{{ asset('js/auth.js') }}"></script>
@endpush


