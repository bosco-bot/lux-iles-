@extends('layouts.app')

@section('title', 'Paiement Sécurisé - ' . ($villa->name ?? 'Villa') . ' | LUXÎLES')

@push('styles')
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #F8F8F6; }
    ::-webkit-scrollbar-thumb { background: #CBAE82; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #A48C64; }
    
    .input-focus-ring:focus-within {
        box-shadow: 0 0 0 2px rgba(203, 174, 130, 0.2);
        border-color: #CBAE82;
    }
    
    /* Styles pour Stripe Elements - intégration transparente */
    #card-element {
        padding: 0 !important;
    }
    #card-element .StripeElement {
        background: transparent !important;
        padding: 0 !important;
        height: auto !important;
        border: none !important;
        box-shadow: none !important;
    }
    #card-element .StripeElement--focus {
        box-shadow: none !important;
        outline: none !important;
    }
    #card-element .StripeElement--invalid {
        color: #dc3545 !important;
    }
    #card-element input {
        color: #0A1A2F !important;
        font-family: 'Montserrat', sans-serif !important;
        font-size: 16px !important;
        letter-spacing: 0.1em !important;
        background: transparent !important;
        border: none !important;
        padding: 0 !important;
        outline: none !important;
    }
    #card-element input::placeholder {
        color: #8A96A6 !important;
        opacity: 1 !important;
    }
    
    /* Animation de pulsation pour le bouton de confirmation */
    @keyframes pulse {
        0% {
            box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2);
        }
        50% {
            box-shadow: 0 4px 20px rgba(203, 174, 130, 0.5), 0 0 0 4px rgba(203, 174, 130, 0.1);
        }
        100% {
            box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2);
        }
    }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main style="padding-top: 8rem; padding-bottom: 5rem;">
    
    <!-- Page Title -->
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="text-center mb-5">
            <span class="text-lux-gold text-uppercase small fw-medium mb-2 d-block" style="letter-spacing: 0.2em;">Finalisation</span>
            <h1 class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif;">Confirmez votre Séjour d'Exception</h1>
        </div>

        <div class="row g-4">
            
            <!-- Left Column: Payment & Form -->
            <div class="col-12 col-lg-8">
                
                <!-- Section: Coordonnées Client -->
                <section id="client-info" class="bg-white p-4 p-md-5 rounded mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h2 class="h4 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">Vos Coordonnées</h2>
                        <i class="fa-regular fa-user text-lux-gold" style="font-size: 1.25rem;"></i>
                    </div>
                    
                    <div class="row g-4">
                        <div class="col-12 col-md-6">
                            <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Prénom & Nom</label>
                            <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                <input type="text" placeholder="Jean Dupont" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" style="color: var(--lux-dark-blue);">
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Email</label>
                            <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                <input type="email" placeholder="jean.dupont@email.com" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" style="color: var(--lux-dark-blue);">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Téléphone</label>
                            <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                <input type="tel" placeholder="+33 6 12 34 56 78" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" style="color: var(--lux-dark-blue);">
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section: Paiement Sécurisé -->
                <section id="payment-secure" class="bg-white p-4 p-md-5 rounded mb-4 border position-relative overflow-hidden" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <div class="position-absolute top-0 end-0 p-3 opacity-5" style="opacity: 0.05;">
                        <i class="fa-brands fa-stripe" style="font-size: 4rem;"></i>
                    </div>
                    
                    <div class="d-flex align-items-center justify-content-between mb-4 position-relative" style="z-index: 10;">
                        <div class="d-flex align-items-center gap-3">
                            <h2 class="h4 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">Paiement Sécurisé</h2>
                            <span class="badge rounded px-2 py-1 fw-bold text-uppercase small d-flex align-items-center gap-1" style="background-color: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; font-size: 0.625rem; letter-spacing: 0.05em;">
                                <i class="fa-solid fa-lock" style="font-size: 0.5rem;"></i> SSL Encrypté
                            </span>
                        </div>
                        <div class="d-flex gap-2 text-lux-greyBlue" style="font-size: 1.25rem;">
                            <i class="fa-brands fa-cc-visa" style="transition: color 0.3s; cursor: pointer;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="this.style.color='var(--lux-greyBlue)'"></i>
                            <i class="fa-brands fa-cc-mastercard" style="transition: color 0.3s; cursor: pointer;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="this.style.color='var(--lux-greyBlue)'"></i>
                            <i class="fa-brands fa-cc-amex" style="transition: color 0.3s; cursor: pointer;" onmouseover="this.style.color='var(--lux-dark-blue)'" onmouseout="this.style.color='var(--lux-greyBlue)'"></i>
                        </div>
                    </div>

                    <!-- Card Element Mockup -->
                    <div class="position-relative" style="z-index: 10;">
                        <div class="mb-4">
                            <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Titulaire de la carte</label>
                            <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                <input type="text" id="cardholder-name" placeholder="Nom sur la carte" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" style="color: var(--lux-dark-blue);">
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Informations de la carte</label>
                            <div class="input-focus-ring border rounded px-3 py-2 d-flex align-items-center gap-3" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                <i class="fa-regular fa-credit-card text-lux-greyBlue"></i>
                                <div id="card-element" class="flex-grow" style="flex: 1; padding: 0;">
                                    <!-- Stripe Elements will be mounted here -->
                                </div>
                            </div>
                            <div id="card-errors" class="mt-2 small text-danger" role="alert" style="display: none;"></div>
                        </div>
                    </div>

                    <div class="mt-4 d-flex align-items-start gap-3 p-3 rounded small" style="background-color: rgba(10, 26, 47, 0.05); color: rgba(10, 26, 47, 0.8);">
                        <i class="fa-solid fa-shield-halved text-lux-gold mt-1"></i>
                        <p class="mb-0 small" style="line-height: 1.75;">
                            Vos informations de paiement sont chiffrées et traitées de manière sécurisée. Nous ne stockons jamais vos données bancaires complètes.
                        </p>
                    </div>
                </section>

                <!-- CTA Action -->
                <button type="button" class="btn btn-lux-primary w-100 py-3 mb-3 fw-medium" id="confirm-booking-btn" style="transition: all 0.3s; box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2); font-size: 1.125rem;">
                    <span>Confirmer la réservation</span>
                    <i class="fa-solid fa-check ms-2"></i>
                </button>
                <p class="text-center small text-lux-greyBlue mt-3 mb-0">
                    En confirmant, vous acceptez nos <a href="{{ route('cgv') }}" target="_blank" rel="noopener noreferrer" class="text-decoration-underline" style="color: var(--lux-greyBlue); transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-greyBlue)'">Conditions Générales de Vente</a>.
                </p>

            </div>

            <!-- Right Column: Recap & Summary -->
            <div class="col-12 col-lg-4">
                <div class="position-sticky" style="top: 8rem;">
                    
                    <article class="bg-white rounded shadow-lg border overflow-hidden" style="border-color: rgba(138, 150, 166, 0.1);">
                        <!-- Image Header -->
                        <div class="position-relative" style="height: 200px;">
                            @if($primaryPhoto)
                                <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                            @else
                                <div class="w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50"></i>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 end-0" style="background: linear-gradient(to top, rgba(10, 26, 47, 0.8), transparent); padding: 1rem;">
                                <h3 class="font-serif text-white mb-0" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">{{ $villa->name }}</h3>
                                <p class="small text-white mb-0 opacity-90"><i class="fa-solid fa-location-dot text-lux-gold me-1"></i> {{ $villa->island->name ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Details -->
                        <div class="p-4">
                            <!-- Dates -->
                            <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                                <div>
                                    <p class="text-uppercase small mb-1" style="letter-spacing: 0.05em; color: var(--lux-greyBlue); font-size: 0.7rem;">Arrivée</p>
                                    <p class="fw-medium text-lux-dark-blue mb-0">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</p>
                                </div>
                                <i class="fa-solid fa-arrow-right-long text-lux-gold opacity-50"></i>
                                <div class="text-end">
                                    <p class="text-uppercase small mb-1" style="letter-spacing: 0.05em; color: var(--lux-greyBlue); font-size: 0.7rem;">Départ</p>
                                    <p class="fw-medium text-lux-dark-blue mb-0">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</p>
                                </div>
                            </div>

                            <!-- Guests -->
                            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                                <span class="text-lux-dark-blue small">Voyageurs</span>
                                <span class="fw-medium text-lux-dark-blue">
                                    @if($adults > 0){{ $adults }} Adulte{{ $adults > 1 ? 's' : '' }}@endif
                                    @if($children > 0){{ $adults > 0 ? ', ' : '' }}{{ $children }} Enfant{{ $children > 1 ? 's' : '' }}@endif
                                    @if($infants > 0){{ ($adults > 0 || $children > 0) ? ', ' : '' }}{{ $infants }} Bébé{{ $infants > 1 ? 's' : '' }}@endif
                                </span>
                            </div>

                            <!-- Pricing Breakdown -->
                            <div class="pt-2">
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>{{ $nights }} nuit{{ $nights > 1 ? 's' : '' }} x {{ number_format($villa->base_price_per_night, 0, ',', ' ') }}€</span>
                                    <span>{{ number_format($basePrice, 0, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Frais de service ({{ $villa->service_fee_percentage ?? 5 }}%)</span>
                                    <span>{{ number_format($serviceFee, 0, ',', ' ') }} €</span>
                                </div>
                                @if($vatAmount > 0)
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>TVA ({{ number_format($globalTaxRate ?? 8.5, 1, ',', ' ') }}%)</span>
                                    <span>{{ number_format($vatAmount, 2, ',', ' ') }} €</span>
                                </div>
                                @endif
                                @if($touristTax > 0)
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Taxe de séjour ({{ $nights }} nuit{{ $nights > 1 ? 's' : '' }} × {{ $adults + $children + $infants }} pers.)</span>
                                    <span>{{ number_format($touristTax, 2, ',', ' ') }} €</span>
                                </div>
                                @endif
                                @if(!empty($discountAmount) && $discountAmount > 0 && !empty($promoCode))
                                <div class="d-flex justify-content-between small text-success fw-medium mb-2">
                                    <span>Réduction ({{ $promoCode }})</span>
                                    <span>-{{ number_format($discountAmount, 2, ',', ' ') }} €</span>
                                </div>
                                @endif
                            </div>

                            <!-- Totals -->
                            <div class="mt-4 pt-4 border-top" style="background-color: rgba(10, 26, 47, 0.05); margin-left: -1rem; margin-right: -1rem; padding-left: 1rem; padding-right: 1rem; border-color: rgba(10, 26, 47, 0.1);">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Total Séjour</span>
                                    <span class="font-serif text-lux-dark-blue fw-bold" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">{{ number_format($total, 0, ',', ' ') }} €</span>
                                </div>
                                
                                <!-- Deposit Logic -->
                                <div class="d-flex justify-content-between align-items-center small mt-4 pt-4 border-top" style="border-color: rgba(10, 26, 47, 0.1);">
                                    <span class="text-lux-gold fw-medium">Arrhes à régler (30%)</span>
                                    <span class="text-lux-gold fw-bold" style="font-size: 1.125rem;">{{ number_format($depositAmount, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center small text-lux-greyBlue mt-2">
                                    <span>Solde à payer sur place</span>
                                    <span>{{ number_format($balanceAmount, 2, ',', ' ') }} €</span>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Security Badges -->
                    <div class="mt-4 d-flex justify-content-center gap-4 opacity-50" style="filter: grayscale(100%); transition: all 0.5s;" onmouseover="this.style.filter='grayscale(0%)'; this.style.opacity='1'" onmouseout="this.style.filter='grayscale(100%)'; this.style.opacity='0.5'">
                        <i class="fa-brands fa-cc-visa" style="font-size: 1.5rem;"></i>
                        <i class="fa-brands fa-cc-mastercard" style="font-size: 1.5rem;"></i>
                        <i class="fa-solid fa-lock" style="font-size: 1.5rem;"></i>
                    </div>

                </div>
            </div>

        </div>
    </div>
</main>

@endsection

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let stripe = null;
    let elements = null;
    let cardElement = null;
    let clientSecret = null;
    let paymentIntentId = null;
    let paymentId = null;
    let reservationNumber = null;
    
    // Variables globales pour les données de réservation
        const formData = {
            villa_id: {{ $villa->id }},
            check_in: '{{ $checkIn }}',
            check_out: '{{ $checkOut }}',
            guests: {{ $adults + $children + $infants }},
            adults: {{ $adults }},
            children: {{ $children }},
            infants: {{ $infants }},
            @if(!empty($promoCode))
            promo_code: @json($promoCode),
            @endif
            _token: '{{ csrf_token() }}'
        };
        
    const btn = document.getElementById('confirm-booking-btn');
    const originalText = btn.innerHTML;
    const cardErrors = document.getElementById('card-errors');
    
    // Clé publique Stripe depuis le serveur
    const stripePublicKey = @json($stripePublicKey ?? null);
    
    // Initialiser Stripe Elements dès le chargement de la page
    if (stripePublicKey) {
        initializeStripeElements(stripePublicKey);
    } else {
        console.warn('Clé publique Stripe non configurée. Les paiements ne fonctionneront pas.');
        // Afficher un message d'erreur discret
        const cardElementContainer = document.getElementById('card-element');
        if (cardElementContainer) {
            cardElementContainer.innerHTML = '<p class="text-danger small mb-0">Stripe n\'est pas configuré. Veuillez contacter le support.</p>';
        }
    }
    
    // Fonction pour initialiser Stripe Elements avec le style personnalisé
    function initializeStripeElements(pubKey) {
        stripe = Stripe(pubKey);
        elements = stripe.elements();
        
        // Style personnalisé pour correspondre au design existant
        const style = {
            base: {
                color: '#0A1A2F',
                fontFamily: '"Montserrat", sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                '::placeholder': {
                    color: '#8A96A6',
                    opacity: 1,
                },
            },
            invalid: {
                color: '#dc3545',
                iconColor: '#dc3545',
            },
        };
        
        // Créer l'élément de carte Stripe
        cardElement = elements.create('card', {
            style: style,
            hidePostalCode: true,
        });
        
        // Monter Stripe Elements dans le conteneur
        cardElement.mount('#card-element');
        
        // Gérer les erreurs
        cardElement.on('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.style.display = 'block';
            } else {
                cardErrors.textContent = '';
                cardErrors.style.display = 'none';
            }
        });
    }
    
    // Fonction pour définir le client_secret après création de réservation
    function setClientSecret(secret) {
        clientSecret = secret;
    }
    
    // Gestion du bouton de confirmation
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Bouton cliqué, clientSecret:', clientSecret ? 'défini' : 'non défini');
        
        // Vérifier que Stripe est initialisé
        if (!stripe || !cardElement) {
            console.error('Stripe non initialisé');
            alert('Le système de paiement n\'est pas disponible. Veuillez recharger la page ou contacter le support.');
            return;
        }
        
        if (!clientSecret) {
            console.log('Pas de clientSecret, création de la réservation...');
            // Première étape : créer la réservation et obtenir le client_secret
            createReservationAndGetClientSecret();
        } else {
            console.log('ClientSecret présent, confirmation du paiement...');
            // Deuxième étape : confirmer le paiement avec Stripe
            confirmPayment();
        }
    });
    
    // Fonction pour créer la réservation et obtenir le client_secret
    function createReservationAndGetClientSecret() {
        console.log('Début de la création de réservation...');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Création de la réservation...';
        
        fetch('{{ route("bookings.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(formData),
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Réponse reçue:', response.status, response.statusText);
            if (response.status === 401 || response.status === 403) {
                return response.json().then(data => {
                    if (data.redirect) {
                        window.location.href = data.url;
                    } else {
                        window.location.href = '{{ route("login") }}';
                    }
                    return null;
                });
            }
            
            if (!response.ok) {
                console.error('Erreur HTTP:', response.status);
                throw new Error('Erreur HTTP: ' + response.status);
            }
            
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (!data) {
                console.warn('Aucune donnée reçue');
                return;
            }
            
            if (data && data.success) {
                console.log('Réservation créée avec succès');
                // Vérifier si un paiement est requis
                if (data.requires_payment && data.payment && data.payment.client_secret) {
                    console.log('Paiement requis, configuration du client_secret');
                    // Définir le client_secret pour le paiement
                    paymentId = data.payment.payment_id;
                    paymentIntentId = data.payment.payment_intent_id;
                    reservationNumber = data.reservation_number;
                    
                    setClientSecret(data.payment.client_secret);
                    
                    // Changer le texte du bouton avec un effet visuel plus marqué
                    btn.innerHTML = '<span>Confirmer le paiement</span><i class="fa-solid fa-credit-card ms-2"></i>';
                    btn.disabled = false;
                    // Ajouter un effet de pulsation pour attirer l'attention
                    btn.style.animation = 'pulse 2s infinite';
                    
                    // Scroll vers le bouton pour le rendre plus visible
                    setTimeout(() => {
                        btn.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 100);
                    
                    // Afficher un message d'information avec style personnalisé
                    const existingAlert = btn.parentElement.querySelector('.reservation-success-alert');
                    if (existingAlert) {
                        existingAlert.remove();
                    }
                    const alertDiv = document.createElement('div');
                    alertDiv.className = 'reservation-success-alert alert alert-success alert-dismissible fade show mt-3';
                    alertDiv.style.backgroundColor = '#d1e7dd';
                    alertDiv.style.borderColor = '#badbcc';
                    alertDiv.style.color = '#0f5132';
                    alertDiv.innerHTML = '<i class="fa-solid fa-check-circle me-2"></i><strong>Réservation créée avec succès !</strong> Veuillez maintenant confirmer votre paiement en cliquant sur le bouton ci-dessous.<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
                    btn.parentElement.insertBefore(alertDiv, btn);
                    
                    console.log('Bouton mis à jour, prêt pour le paiement');
                } else if (data.redirect_url) {
                    console.log('Redirection vers:', data.redirect_url);
                    // Pas de paiement requis, rediriger directement
                    window.location.href = data.redirect_url;
                } else {
                    console.error('Réponse inattendue du serveur:', data);
                    alert('Réponse inattendue du serveur. Veuillez contacter le support.');
                    btn.disabled = false;
                    btn.innerHTML = originalText;
                }
            } else {
                console.error('Erreur dans la réponse:', data);
                alert(data?.message || 'Une erreur est survenue. Veuillez réessayer.');
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        })
        .catch(error => {
            console.error('Erreur lors de la création de la réservation:', error);
            alert('Une erreur est survenue lors de la création de la réservation. Veuillez réessayer.\n\nErreur: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    // Fonction pour confirmer le paiement avec Stripe
    function confirmPayment() {
        const cardholderName = document.getElementById('cardholder-name').value.trim();
        
        if (!cardholderName) {
            alert('Veuillez entrer le nom du titulaire de la carte.');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Traitement du paiement...';
        
        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: cardholderName,
                },
            },
        })
        .then(function(result) {
            if (result.error) {
                // Afficher l'erreur
                cardErrors.textContent = result.error.message;
                cardErrors.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<span>Confirmer le paiement</span><i class="fa-solid fa-check ms-2"></i>';
            } else {
                // Paiement réussi, confirmer côté serveur
                confirmPaymentOnServer(result.paymentIntent.id);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la confirmation du paiement:', error);
            alert('Une erreur est survenue lors du traitement du paiement. Veuillez réessayer.');
            btn.disabled = false;
            btn.innerHTML = '<span>Confirmer le paiement</span><i class="fa-solid fa-check ms-2"></i>';
        });
    }
    
    // Fonction pour confirmer le paiement côté serveur
    function confirmPaymentOnServer(intentId) {
        fetch('{{ route("api.payments.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                payment_intent_id: intentId,
                payment_id: paymentId,
            }),
            credentials: 'same-origin'
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.success) {
                // Rediriger vers la page de confirmation
                const confirmUrl = reservationNumber 
                    ? '{{ route("bookings.confirmation") }}?reservation_number=' + reservationNumber
                    : (data.reservation?.reservation_number 
                        ? '{{ route("bookings.confirmation") }}?reservation_number=' + data.reservation.reservation_number
                        : '{{ route("bookings.confirmation") }}');
                window.location.href = confirmUrl;
            } else {
                alert(data?.message || 'Le paiement a été traité mais une erreur est survenue. Veuillez contacter le support.');
                if (reservationNumber) {
                    window.location.href = '{{ route("bookings.confirmation") }}?reservation_number=' + reservationNumber;
                }
            }
        })
        .catch(error => {
            console.error('Erreur lors de la confirmation côté serveur:', error);
            if (reservationNumber) {
                alert('Le paiement a été traité. Vous serez redirigé vers la page de confirmation.');
                window.location.href = '{{ route("bookings.confirmation") }}?reservation_number=' + reservationNumber;
            } else {
                alert('Une erreur est survenue. Veuillez contacter le support.');
            }
        });
    }
});
</script>
@endpush

