@extends('layouts.app')

@section('title', 'Mot de passe oublié | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Forgot Password Section -->
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
                                <h1 class="h3 mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Mot de passe oublié</h1>
                                <p class="text-muted small">Entrez votre adresse email pour recevoir un lien de réinitialisation</p>
                            </div>

                            <form id="forgot-password-form">
                                @csrf
                                <div class="mb-4">
                                    <label for="email" class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Email</label>
                                    <input type="email" id="email" name="email" class="form-control" placeholder="votre@email.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                </div>
                                <button type="submit" class="btn btn-lux-primary w-100 mb-3">Envoyer le lien de réinitialisation</button>
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
            const form = document.getElementById('forgot-password-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    
                    const formData = {
                        email: form.querySelector('#email').value.trim(),
                    };
                    
                    if (!formData.email) {
                        showAuthError('Veuillez entrer votre adresse email.');
                        return;
                    }
                    
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Envoi en cours...';
                    
                    hideAuthErrors();
                    
                    axios.post('/api/auth/forgot-password', formData)
                        .then(response => {
                            if (response.data.success) {
                                showAuthSuccess(response.data.message || 'Un email de réinitialisation a été envoyé à votre adresse.');
                                
                                setTimeout(() => {
                                    window.location.href = '{{ route("login") }}';
                                }, 3000);
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










