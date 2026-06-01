@extends('layouts.app')

@section('title', 'Réinitialiser le mot de passe | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Reset Password Section -->
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
                                <h1 class="h3 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Réinitialiser le mot de passe</h1>
                                <p class="text-muted small">Entrez votre nouveau mot de passe</p>
                            </div>

                            <form id="reset-password-form">
                                @csrf
                                <input type="hidden" id="token" name="token" value="{{ $token }}">
                                
                                <div class="mb-3">
                                    <label for="email" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="password" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Nouveau mot de passe</label>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="••••••••" required minlength="8" style="border-color: rgba(138, 150, 166, 0.2);">
                                    <small class="text-muted">Minimum 8 caractères</small>
                                </div>
                                
                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Confirmer le mot de passe</label>
                                    <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="••••••••" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                
                                <button type="submit" class="btn btn-lux-primary w-100 mb-3">Réinitialiser le mot de passe</button>
                                <div class="text-center">
                                    <p class="small text-muted mb-0">
                                        <a href="{{ route('login') }}" class="text-lux-gold text-decoration-none">Retour à la connexion</a>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('reset-password-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    
                    const formData = {
                        token: form.querySelector('#token').value,
                        email: form.querySelector('#email').value.trim(),
                        password: form.querySelector('#password').value,
                        password_confirmation: form.querySelector('#password_confirmation').value,
                    };
                    
                    if (!formData.email || !formData.password || !formData.password_confirmation) {
                        showAuthError('Veuillez remplir tous les champs.');
                        return;
                    }
                    
                    if (formData.password.length < 8) {
                        showAuthError('Le mot de passe doit contenir au moins 8 caractères.');
                        return;
                    }
                    
                    if (formData.password !== formData.password_confirmation) {
                        showAuthError('Les mots de passe ne correspondent pas.');
                        return;
                    }
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Réinitialisation...';
                    
                    hideAuthErrors();
                    
                    axios.post('/api/auth/reset-password', formData)
                        .then(response => {
                            if (response.data.success) {
                                showAuthSuccess(response.data.message || 'Votre mot de passe a été réinitialisé avec succès.');
                                
                                setTimeout(() => {
                                    window.location.href = response.data.redirect || '{{ route("login") }}';
                                }, 2000);
                            }
                        })
                        .catch(error => {
                            let errorMessage = 'Une erreur est survenue.';
                            
                            if (error.response && error.response.data) {
                                const data = error.response.data;
                                if (data.message) {
                                    errorMessage = data.message;
                                } else if (data.errors) {
                                    const errors = Object.values(data.errors).flat();
                                    errorMessage = errors.join('<br>');
                                }
                            }
                            
                            showAuthError(errorMessage);
                            submitBtn.disabled = false;
                            submitBtn.textContent = originalText;
                        });
                });
            }
            
            function showAuthError(message) {
                hideAuthErrors();
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger alert-dismissible fade show';
                errorDiv.setAttribute('role', 'alert');
                errorDiv.innerHTML = `
                    <strong>Erreur :</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                const form = document.querySelector('form');
                if (form) {
                    form.insertBefore(errorDiv, form.firstChild);
                }
            }
            
            function showAuthSuccess(message) {
                hideAuthErrors();
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success alert-dismissible fade show';
                successDiv.setAttribute('role', 'alert');
                successDiv.innerHTML = `
                    <strong>Succès :</strong> ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                const form = document.querySelector('form');
                if (form) {
                    form.insertBefore(successDiv, form.firstChild);
                }
            }
            
            function hideAuthErrors() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => alert.remove());
            }
        });
    </script>
@endpush

