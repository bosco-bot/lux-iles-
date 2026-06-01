@extends('layouts.app')

@section('title', 'Connexion | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Login Section -->
    <section class="py-5" style="margin-top: 80px; min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border shadow-lg" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <div class="text-center mb-3">
                                    <img src="{{ asset('Social_Media_Profil_Bleu.png') }}" alt="LUX Îles" class="mb-3" style="height: 80px; width: auto; object-fit: contain;">
                                </div>
                                <h1 class="h3 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Connexion</h1>
                                <p class="text-muted small">Accédez à votre espace client</p>
                            </div>

                            @if(session('intended'))
                                <div class="alert alert-info small mb-3" role="alert">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    Veuillez vous connecter pour continuer votre réservation.
                                </div>
                            @endif
                            
                            <form id="login-form">
                                @csrf
                                @if(session('intended'))
                                    <input type="hidden" id="intended-url" value="{{ session('intended') }}">
                                @endif
                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Mot de passe</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember" name="remember" style="border-color: rgba(138, 150, 166, 0.3);">
                                        <label class="form-check-label small" for="remember" style="color: var(--lux-gray);">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="small text-lux-gold text-decoration-none">Mot de passe oublié ?</a>
                                </div>
                                <button type="submit" class="btn btn-lux-primary w-100 mb-3">Se connecter</button>
                                <div class="text-center">
                                    <p class="small text-muted mb-0">
                                        Pas encore de compte ? 
                                        <a href="{{ route('register') }}" class="text-lux-gold text-decoration-none">Créer un compte</a>
                                    </p>
                                </div>
                            </form>

                            <div class="mt-4 pt-4 border-top text-center" style="border-top-color: rgba(138, 150, 166, 0.1) !important;">
                                <p class="small text-muted mb-2">Accès administrateur</p>
                                <a href="{{ route('admin.login') }}" class="small text-lux-gold text-decoration-none">Connexion admin</a>
                            </div>
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


