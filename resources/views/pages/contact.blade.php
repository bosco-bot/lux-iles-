@extends('layouts.app')

@section('title', 'Contact | LUXÎLES - Location de Villas de Luxe')

@section('content')
    <!-- Contact Section -->
    <section class="py-5" style="margin-top: 80px; min-height: calc(100vh - 200px);">
        <div class="container">
            <div class="row g-5">
                <!-- Contact Info Column -->
                <div class="col-lg-5">
                    <div class="mb-4">
                        <h1 class="h2 font-serif mb-3" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Contactez-nous</h1>
                        <p class="text-muted">Notre équipe est à votre disposition pour répondre à toutes vos questions et vous accompagner dans votre projet de séjour dans les Caraïbes.</p>
                    </div>

                    <!-- Contact Cards -->
                    <div class="d-flex flex-column gap-4 mb-5">
                        <!-- Téléphone -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-gold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-phone text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="color: var(--lux-dark-blue); font-weight: 600;">Téléphone</h5>
                                @php
                                    $companyPhone = \App\Helpers\SettingsHelper::get('company_phone', '+33 7 66 33 41 98');
                                @endphp
                                <p class="text-muted small mb-0">{{ $companyPhone }}</p>
                                <p class="text-muted" style="font-size: 0.75rem;">Lun-Dim 8h-20h</p>
                            </div>
                        </div>

                        <!-- Email -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-gold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-envelope text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="color: var(--lux-dark-blue); font-weight: 600;">Email</h5>
                                @php
                                    $companyEmail = \App\Helpers\SettingsHelper::get('company_email', 'contact.luxiles@gmail.com');
                                @endphp
                                <a href="mailto:{{ $companyEmail }}" class="text-lux-gold text-decoration-none small">{{ $companyEmail }}</a>
                                <p class="text-muted" style="font-size: 0.75rem;">Réponse sous 24h</p>
                            </div>
                        </div>

                        <!-- Adresse -->
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-gold d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; flex-shrink: 0;">
                                <i class="fas fa-location-dot text-white"></i>
                            </div>
                            <div>
                                <h5 class="mb-1" style="color: var(--lux-dark-blue); font-weight: 600;">Adresse</h5>
                                @php
                                    $companyAddress = \App\Helpers\SettingsHelper::get('company_address', '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE');
                                @endphp
                                <p class="text-muted small mb-0">{!! nl2br(e($companyAddress)) !!}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media -->
                    <div class="mb-4">
                        <h5 class="mb-3" style="color: var(--lux-dark-blue); font-weight: 600;">Suivez-nous</h5>
                        <div class="d-flex align-items-center gap-3">
                            <a href="https://www.instagram.com/luxilesvillas/" target="_blank" rel="noopener noreferrer" class="rounded-circle border border-lux-gold d-flex align-items-center justify-content-center text-lux-gold text-decoration-none" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)';">
                                <i class="fab fa-instagram"></i>
                            </a>
                            <a href="https://www.facebook.com/profile.php?id=61579747463876" target="_blank" rel="noopener noreferrer" class="rounded-circle border border-lux-gold d-flex align-items-center justify-content-center text-lux-gold text-decoration-none" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)';">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <!-- <a href="#" class="rounded-circle border border-lux-gold d-flex align-items-center justify-content-center text-lux-gold text-decoration-none" style="width: 40px; height: 40px; transition: all 0.3s;" onmouseover="this.style.backgroundColor='var(--lux-gold)'; this.style.color='white';" onmouseout="this.style.backgroundColor='transparent'; this.style.color='var(--lux-gold)';">
                                <i class="fab fa-linkedin-in"></i>
                            </a> -->
                        </div>
                    </div>
                </div>

                <!-- Contact Form Column -->
                <div class="col-lg-7">
                    <div class="card border shadow-lg" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <div class="card-body p-4 p-lg-5">
                            <h2 class="h4 font-serif mb-4" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">Envoyez-nous un message</h2>
                            
                            <!-- Messages d'alerte -->
                            <div id="contact-alert" style="display: none;" class="alert mb-4"></div>
                            
                            <form id="contact-form" action="{{ route('contact.send') }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <!-- Prénom -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Prénom <span class="text-danger">*</span></label>
                                        <input type="text" name="first_name" class="form-control" placeholder="Jean" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Nom -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Nom <span class="text-danger">*</span></label>
                                        <input type="text" name="last_name" class="form-control" placeholder="Dupont" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Email -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Email <span class="text-danger">*</span></label>
                                        <input type="email" name="email" class="form-control" placeholder="jean.dupont@email.com" required style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Téléphone -->
                                    <div class="col-md-6">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Téléphone</label>
                                        <input type="tel" name="phone" class="form-control" placeholder="+33 6 12 34 56 78" style="border-color: rgba(138, 150, 166, 0.2);">
                                    </div>
                                    
                                    <!-- Sujet -->
                                    <div class="col-12">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Sujet <span class="text-danger">*</span></label>
                                        @php
                                            $preselectedSubject = request()->query('subject', '');
                                        @endphp
                                        <select name="subject" id="contact-subject" class="form-select" required style="border-color: rgba(138, 150, 166, 0.2);">
                                            <option value="">Sélectionnez un sujet</option>
                                            <option value="Demande de renseignements" {{ $preselectedSubject === 'Demande de renseignements' ? 'selected' : '' }}>Demande de renseignements</option>
                                            <option value="Réservation" {{ $preselectedSubject === 'Réservation' ? 'selected' : '' }}>Réservation</option>
                                            <option value="Conciergerie" {{ $preselectedSubject === 'Conciergerie' ? 'selected' : '' }}>Conciergerie</option>
                                            <option value="Conciergerie 24/7" {{ $preselectedSubject === 'Conciergerie 24/7' ? 'selected' : '' }}>Conciergerie 24/7</option>
                                            <option value="Chef à domicile" {{ $preselectedSubject === 'Chef à domicile' ? 'selected' : '' }}>Chef à domicile</option>
                                            <option value="Transferts privés" {{ $preselectedSubject === 'Transferts privés' ? 'selected' : '' }}>Transferts privés</option>
                                            <option value="Activités exclusives" {{ $preselectedSubject === 'Activités exclusives' ? 'selected' : '' }}>Activités exclusives</option>
                                            <option value="Partenariat" {{ $preselectedSubject === 'Partenariat' ? 'selected' : '' }}>Partenariat</option>
                                            <option value="Autre" {{ $preselectedSubject === 'Autre' ? 'selected' : '' }}>Autre</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Message -->
                                    <div class="col-12">
                                        <label class="form-label small fw-medium" style="color: var(--lux-dark-blue);">Message <span class="text-danger">*</span></label>
                                        <textarea name="message" class="form-control" rows="6" placeholder="Votre message..." required style="border-color: rgba(138, 150, 166, 0.2);"></textarea>
                                    </div>
                                </div>

                                <button type="submit" id="contact-submit-btn" class="btn btn-lux-primary w-100 mt-4">
                                    <span id="submit-text">Envoyer le message</span>
                                    <span id="submit-loading" style="display: none;">
                                        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                                        Envoi en cours...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('contact-form');
            const alertDiv = document.getElementById('contact-alert');
            const submitBtn = document.getElementById('contact-submit-btn');
            const submitText = document.getElementById('submit-text');
            const submitLoading = document.getElementById('submit-loading');

            form.addEventListener('submit', function(e) {
                e.preventDefault();

                // Masquer l'alerte précédente
                alertDiv.style.display = 'none';
                alertDiv.className = 'alert mb-4';

                // Désactiver le bouton et afficher le loading
                submitBtn.disabled = true;
                submitText.style.display = 'none';
                submitLoading.style.display = 'inline';

                // Récupérer les données du formulaire
                const formData = new FormData(form);

                // Envoyer la requête AJAX
                fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    }
                })
                .then(response => {
                    // Vérifier si la réponse est OK (200) ou erreur de validation (422)
                    if (!response.ok && response.status === 422) {
                        return response.json().then(data => {
                            throw { type: 'validation', data: data };
                        });
                    }
                    if (!response.ok) {
                        throw new Error('Erreur HTTP: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Afficher le message de succès
                        alertDiv.className = 'alert alert-success mb-4';
                        alertDiv.textContent = data.message;
                        alertDiv.style.display = 'block';

                        // Réinitialiser le formulaire
                        form.reset();

                        // Faire défiler vers le haut du formulaire
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    } else {
                        // Afficher le message d'erreur
                        alertDiv.className = 'alert alert-danger mb-4';
                        alertDiv.textContent = data.message || 'Une erreur est survenue. Veuillez réessayer.';
                        alertDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    if (error && error.type === 'validation') {
                        // Gérer les erreurs de validation
                        let errorMessages = [];
                        if (error.data.errors) {
                            // Collecter toutes les erreurs de validation
                            Object.keys(error.data.errors).forEach(field => {
                                error.data.errors[field].forEach(msg => {
                                    errorMessages.push(msg);
                                });
                            });
                        }
                        
                        alertDiv.className = 'alert alert-danger mb-4';
                        alertDiv.innerHTML = '<strong>Erreur de validation :</strong><ul class="mb-0 mt-2">' + 
                            errorMessages.map(msg => '<li>' + msg + '</li>').join('') + 
                            '</ul>';
                        alertDiv.style.display = 'block';
                    } else {
                        console.error('Erreur:', error);
                        alertDiv.className = 'alert alert-danger mb-4';
                        alertDiv.textContent = 'Une erreur est survenue lors de l\'envoi. Veuillez réessayer plus tard.';
                        alertDiv.style.display = 'block';
                    }
                })
                .finally(() => {
                    // Réactiver le bouton
                    submitBtn.disabled = false;
                    submitText.style.display = 'inline';
                    submitLoading.style.display = 'none';
                });
            });
        });
    </script>
    @endpush
@endsection

