@extends('layouts.app')

@section('title', 'Paiement du Solde - Réservation ' . $reservation->reservation_number . ' | LUXÎLES')

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
            <span class="text-lux-gold text-uppercase small fw-medium mb-2 d-block" style="letter-spacing: 0.2em;">Paiement</span>
            <h1 class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif;">Paiement du Solde</h1>
            <p class="text-lux-greyBlue mt-2">Réservation #{{ $reservation->reservation_number }}</p>
        </div>

        <div class="row g-4">
            
            <!-- Left Column: Payment & Form -->
            <div class="col-12 col-lg-8">
                
                <!-- Section: Paiement Sécurisé -->
                <section id="payment-section" class="bg-white p-4 p-md-5 rounded mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    
                    <div class="position-relative mb-4" style="background: linear-gradient(135deg, rgba(203, 174, 130, 0.05) 0%, rgba(10, 26, 47, 0.02) 100%); border-radius: 0.75rem; padding: 2rem; border: 1px solid rgba(203, 174, 130, 0.1);">
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

                        <!-- Card Element -->
                        <div class="position-relative" style="z-index: 10;">
                            <div class="mb-4">
                                <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Titulaire de la carte</label>
                                <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                    <input type="text" id="cardholder-name" placeholder="Nom sur la carte" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" style="color: var(--lux-dark-blue);" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}">
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
                    </div>

                    <!-- CTA Action -->
                    <button type="button" class="btn btn-lux-primary w-100 py-3 mb-3 fw-medium" id="confirm-payment-btn" style="transition: all 0.3s; box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2); font-size: 1.125rem;">
                        <span>Payer le solde</span>
                        <i class="fa-solid fa-check ms-2"></i>
                    </button>
                    <p class="text-center small text-lux-greyBlue mt-3 mb-0">
                        En confirmant, vous acceptez nos <a href="{{ route('cgv') }}" target="_blank" rel="noopener noreferrer" class="text-decoration-underline" style="color: var(--lux-greyBlue); transition: color 0.3s;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.color='var(--lux-greyBlue)'">Conditions Générales de Vente</a>.
                    </p>

                </section>

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

                            <!-- Résumé des Paiements -->
                            <div class="pt-2">
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Total de la réservation</span>
                                    <span>{{ number_format($total, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Déjà payé (arrhes)</span>
                                    <span class="text-success">{{ number_format($totalPaid, 2, ',', ' ') }} €</span>
                                </div>
                            </div>

                            <!-- Totals -->
                            <div class="mt-4 pt-4 border-top" style="background-color: rgba(10, 26, 47, 0.05); margin-left: -1rem; margin-right: -1rem; padding-left: 1rem; padding-right: 1rem; border-color: rgba(10, 26, 47, 0.1);">
                                <div class="d-flex justify-content-between align-items-end mb-2">
                                    <span class="font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Solde à régler</span>
                                    <span class="font-serif text-lux-gold fw-bold" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;">{{ number_format($balanceAmount, 2, ',', ' ') }} €</span>
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
    
    const btn = document.getElementById('confirm-payment-btn');
    const originalText = btn.innerHTML;
    const cardErrors = document.getElementById('card-errors');
    
    // Clé publique Stripe depuis le serveur
    const stripePublicKey = @json($stripePublicKey ?? null);
    const paymentId = {{ $balancePayment->id }};
    const reservationId = {{ $reservation->id }};
    
    // Initialiser Stripe Elements dès le chargement de la page
    if (stripePublicKey) {
        initializeStripeElements(stripePublicKey);
    } else {
        console.warn('Clé publique Stripe non configurée. Les paiements ne fonctionneront pas.');
        const cardElementContainer = document.getElementById('card-element');
        if (cardElementContainer) {
            cardElementContainer.innerHTML = '<p class="text-danger small mb-0">Stripe n\'est pas configuré. Veuillez contacter le support.</p>';
        }
    }
    
    // Fonction pour initialiser Stripe Elements
    function initializeStripeElements(pubKey) {
        stripe = Stripe(pubKey);
        elements = stripe.elements();
        
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
        
        cardElement = elements.create('card', {
            style: style,
            hidePostalCode: true,
        });
        
        cardElement.mount('#card-element');
        
        cardElement.on('change', function(event) {
            if (event.error) {
                cardErrors.textContent = event.error.message;
                cardErrors.style.display = 'block';
            } else {
                cardErrors.textContent = '';
                cardErrors.style.display = 'none';
            }
        });
        
        // Créer le PaymentIntent immédiatement
        createPaymentIntent();
    }
    
    // Créer le PaymentIntent
    function createPaymentIntent() {
        console.log('Création du PaymentIntent pour le solde...');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Initialisation...';
        
        fetch('{{ route("api.payments.create-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                reservation_id: reservationId,
                payment_type: 'balance',
            }),
            credentials: 'same-origin'
        })
        .then(response => {
            console.log('Réponse reçue:', response.status);
            if (!response.ok) {
                throw new Error('Erreur HTTP: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Données reçues:', data);
            if (data && data.success && data.client_secret) {
                clientSecret = data.client_secret;
                btn.disabled = false;
                btn.innerHTML = originalText;
                console.log('PaymentIntent créé, prêt pour le paiement');
            } else {
                throw new Error(data?.message || 'Erreur lors de la création du paiement');
            }
        })
        .catch(error => {
            console.error('Erreur lors de la création du PaymentIntent:', error);
            alert('Une erreur est survenue lors de l\'initialisation du paiement. Veuillez réessayer.\n\nErreur: ' + error.message);
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    // Gestion du bouton de paiement
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        console.log('Bouton cliqué, clientSecret:', clientSecret ? 'défini' : 'non défini');
        
        if (!stripe || !cardElement) {
            console.error('Stripe non initialisé');
            alert('Le système de paiement n\'est pas disponible. Veuillez recharger la page ou contacter le support.');
            return;
        }
        
        if (!clientSecret) {
            console.log('Pas de clientSecret, création...');
            createPaymentIntent();
            return;
        }
        
        confirmPayment();
    });
    
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
                cardErrors.textContent = result.error.message;
                cardErrors.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = originalText;
            } else {
                confirmPaymentOnServer(result.paymentIntent.id);
            }
        })
        .catch(error => {
            console.error('Erreur lors de la confirmation du paiement:', error);
            alert('Une erreur est survenue lors du traitement du paiement. Veuillez réessayer.');
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    }
    
    // Fonction pour confirmer le paiement côté serveur
    function confirmPaymentOnServer(intentId) {
        console.log('Confirmation du paiement côté serveur...');
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
            console.log('Réponse serveur:', data);
            if (data && data.success) {
                // Rediriger vers l'espace client avec un message de succès
                window.location.href = '{{ route("espace-client.reservations") }}?payment_success=1';
            } else {
                alert(data?.message || 'Le paiement a été traité mais une erreur est survenue. Veuillez contacter le support.');
                window.location.href = '{{ route("espace-client.reservations") }}';
            }
        })
        .catch(error => {
            console.error('Erreur lors de la confirmation côté serveur:', error);
            alert('Le paiement a été traité. Vous serez redirigé vers vos réservations.');
            window.location.href = '{{ route("espace-client.reservations") }}';
        });
    }
});
</script>
@endpush








