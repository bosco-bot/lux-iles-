@extends('layouts.app')

@section('title', 'Connexion Admin | LUXÎLES - Administration')

@section('content')
    <!-- Admin Login Section -->
    <section class="py-5" style="margin-top: 80px; min-height: calc(100vh - 200px); background: linear-gradient(135deg, rgba(10, 26, 47, 0.05) 0%, rgba(203, 174, 130, 0.05) 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card border shadow-lg" style="border-color: rgba(138, 150, 166, 0.1) !important; border-top: 3px solid var(--lux-gold);">
                        <div class="card-body p-4 p-lg-5">
                            <div class="text-center mb-4">
                                <div class="d-inline-flex align-items-center justify-content-center mb-3 rounded-circle" style="width: 64px; height: 64px; background: linear-gradient(135deg, var(--lux-dark-blue) 0%, rgba(10, 26, 47, 0.8) 100%); border: 2px solid var(--lux-gold);">
                                    <i class="fas fa-shield-alt text-lux-gold" style="font-size: 1.5rem;"></i>
                                </div>
                                <h1 class="h3 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Connexion Administrateur</h1>
                                <p class="text-muted small">Accès sécurisé au panneau d'administration</p>
                            </div>

                            <form id="admin-login-form">
                                @csrf
                                <div class="mb-3">
                                    <label for="admin-email" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                        <i class="fas fa-envelope text-lux-gold me-2"></i>Email administrateur
                                    </label>
                                    <input type="email" id="admin-email" name="email" class="form-control" placeholder="admin@luxiles.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                <div class="mb-3">
                                    <label for="admin-password" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">
                                        <i class="fas fa-lock text-lux-gold me-2"></i>Mot de passe
                                    </label>
                                    <input type="password" id="admin-password" name="password" class="form-control" placeholder="••••••••" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="admin-remember" name="remember" style="border-color: rgba(138, 150, 166, 0.3);">
                                        <label class="form-check-label small" for="admin-remember" style="color: var(--lux-gray);">
                                            Se souvenir de moi
                                        </label>
                                    </div>
                                    <a href="{{ route('password.request') }}" class="small text-lux-gold text-decoration-none">Mot de passe oublié ?</a>
                                </div>
                                <button type="submit" class="btn btn-lux-primary w-100 mb-3 d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-sign-in-alt"></i>
                                    <span>Se connecter</span>
                                </button>
                                <div class="alert alert-danger d-none" id="admin-login-error" role="alert">
                                    <i class="fas fa-exclamation-circle me-2"></i>
                                    <span id="admin-error-message"></span>
                                </div>
                            </form>

                            <div class="mt-4 pt-4 border-top text-center" style="border-top-color: rgba(138, 150, 166, 0.1) !important;">
                                <p class="small text-muted mb-2">Accès client</p>
                                <a href="{{ route('login') }}" class="small text-lux-gold text-decoration-none">
                                    <i class="fas fa-arrow-left me-1"></i> Retour à la connexion client
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const adminLoginForm = document.getElementById('admin-login-form');
        const errorAlert = document.getElementById('admin-login-error');
        const errorMessage = document.getElementById('admin-error-message');

        if (adminLoginForm) {
            adminLoginForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                errorAlert.classList.add('d-none');
                
                const email = document.getElementById('admin-email').value;
                const password = document.getElementById('admin-password').value;
                const remember = document.getElementById('admin-remember').checked;

                const submitBtn = adminLoginForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Connexion...';

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
                    const formData = new FormData();
                    formData.append('email', email);
                    formData.append('password', password);
                    formData.append('remember', remember ? '1' : '0');
                    formData.append('is_admin_attempt', '1');
                    formData.append('_token', csrfToken);
                    
                    const response = await fetch('{{ route("api.auth.login") }}', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    // Vérifier si la réponse est OK avant de parser le JSON
                    if (!response.ok && response.status === 419) {
                        errorMessage.textContent = 'La session a expiré. Veuillez recharger la page et réessayer.';
                        errorAlert.classList.remove('d-none');
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                        return;
                    }

                    const data = await response.json();

                    if (data.success) {
                        // Vérifier si l'utilisateur est admin
                        if (data.user && data.user.is_admin) {
                            // Rediriger vers le dashboard admin
                            window.location.href = '{{ route("admin.dashboard") }}';
                        } else {
                            // L'utilisateur n'est pas admin
                            errorMessage.textContent = 'Accès refusé. Vous devez être administrateur pour accéder à cette page.';
                            errorAlert.classList.remove('d-none');
                            submitBtn.disabled = false;
                            submitBtn.innerHTML = originalText;
                        }
                    } else {
                        errorMessage.textContent = data.message || 'Erreur de connexion. Veuillez vérifier vos identifiants.';
                        errorAlert.classList.remove('d-none');
                        submitBtn.disabled = false;
                        submitBtn.innerHTML = originalText;
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    
                    let errorText = 'Une erreur est survenue. Veuillez réessayer.';
                    
                    // Gérer les erreurs CSRF (419)
                    if (error.response && error.response.status === 419) {
                        errorText = 'La session a expiré. Veuillez recharger la page et réessayer.';
                        // Recharger la page pour obtenir un nouveau token CSRF
                        setTimeout(() => {
                            window.location.reload();
                        }, 2000);
                    } else if (error.response && error.response.data) {
                        const errorData = error.response.data;
                        if (errorData.message) {
                            errorText = errorData.message;
                        } else if (errorData.errors) {
                            const errors = Array.isArray(errorData.errors) 
                                ? errorData.errors 
                                : Object.values(errorData.errors).flat();
                            errorText = 'Erreur de validation : ' + errors.join(', ');
                        }
                    }
                    
                    errorMessage.textContent = errorText;
                    errorAlert.classList.remove('d-none');
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            });
        }
    });
</script>
@endpush

