@extends('layouts.app')

@section('title', 'Paiement de la Garantie - Réservation ' . $reservation->reservation_number . ' | LUXÎLES')

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
</style>
@endpush

@section('content')

<!-- Main Content -->
<main style="padding-top: 8rem; padding-bottom: 5rem;">
    
    <!-- Page Title -->
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="text-center mb-5">
            <span class="text-lux-gold text-uppercase small fw-medium mb-2 d-block" style="letter-spacing: 0.2em;">Garantie</span>
            <h1 class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif;">Dépôt de Garantie</h1>
            <p class="text-lux-greyBlue mt-2">Réservation #{{ $reservation->reservation_number }}</p>
        </div>

        <div class="row g-4">
            
            <!-- Left Column: Payment & Form -->
            <div class="col-12 col-lg-8">
                
                <!-- Alert: Caution Information -->
                <div class="alert alert-info border-0 shadow-sm mb-4 d-flex gap-3 p-4" style="background-color: rgba(138, 150, 166, 0.05); border-radius: 1rem;">
                    <i class="fa-solid fa-circle-info text-lux-gold fs-4"></i>
                    <div>
                        <h4 class="h6 fw-bold text-lux-dark-blue mb-2">À propos du dépôt de garantie</h4>
                        <p class="small text-lux-greyBlue mb-0" style="line-height: 1.6;">
                            Le dépôt de garantie est une provision destinée à couvrir d'éventuels dommages durant votre séjour. 
                            <strong>Ce montant sera intégralement remboursé</strong> après votre départ et vérification de la villa, sous un délai de 2 à 5 jours.
                        </p>
                    </div>
                </div>
                
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
                                <i class="fa-brands fa-cc-visa"></i>
                                <i class="fa-brands fa-cc-mastercard"></i>
                                <i class="fa-brands fa-cc-amex"></i>
                            </div>
                        </div>

                        <!-- Card Element -->
                        <div class="position-relative" style="z-index: 10;">
                            <div class="mb-4">
                                <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Titulaire de la carte</label>
                                <div class="input-focus-ring d-flex align-items-center border rounded px-3 py-2" style="border-color: rgba(138, 150, 166, 0.3); background-color: rgba(248, 248, 246, 0.5); transition: all 0.3s;">
                                    <input type="text" id="cardholder-name" placeholder="Nom sur la carte" class="w-100 border-0 bg-transparent outline-0 text-lux-dark-blue" value="{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}">
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
                    </div>

                    <!-- CTA Action -->
                    <button type="button" class="btn btn-lux-primary w-100 py-3 mb-3 fw-medium" id="confirm-payment-btn" style="transition: all 0.3s; box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2); font-size: 1.125rem;">
                        <span>Régler la caution de {{ number_format($guaranteeAmount, 2, ',', ' ') }} €</span>
                        <i class="fa-solid fa-check ms-2"></i>
                    </button>
                    <p class="text-center small text-lux-greyBlue mt-3 mb-0">
                        Paiement sécurisé via Stripe. Le montant sera remboursé après votre séjour.
                    </p>

                </section>

            </div>

            <!-- Right Column: recap -->
            <div class="col-12 col-lg-4">
                <div class="position-sticky" style="top: 8rem;">
                    
                    <article class="bg-white rounded shadow-lg border overflow-hidden" style="border-color: rgba(138, 150, 166, 0.1);">
                        <div class="position-relative" style="height: 180px;">
                            @if($primaryPhoto)
                                <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                            @endif
                            <div class="position-absolute bottom-0 start-0 end-0" style="background: linear-gradient(to top, rgba(10, 26, 47, 0.8), transparent); padding: 1rem;">
                                <h3 class="font-serif text-white mb-0" style="font-size: 1.25rem;">{{ $villa->name }}</h3>
                                <p class="small text-white mb-0 opacity-90">{{ $villa->island->name ?? '' }}</p>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                <span>Période</span>
                                <span class="fw-medium text-lux-dark-blue">{{ \Carbon\Carbon::parse($checkIn)->format('d/m') }} - {{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-lux-greyBlue mb-4">
                                <span>Durée</span>
                                <span class="fw-medium text-lux-dark-blue">{{ $nights }} nuit(s)</span>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                <div class="d-flex justify-content-between align-items-center mb-0">
                                    <span class="text-lux-dark-blue fw-medium">Montant à garantir</span>
                                    <span class="font-serif text-lux-gold fw-bold fs-3">{{ number_format($guaranteeAmount, 2, ',', ' ') }} €</span>
                                </div>
                                <p class="small text-lux-greyBlue mt-2 mb-0">
                                    <i class="fa-solid fa-clock me-1"></i> Échéance : {{ \Carbon\Carbon::parse($dueDate)->format('d/m/Y') }}
                                </p>
                            </div>
                        </div>
                    </article>
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
    
    const stripePublicKey = @json($stripePublicKey);
    const reservationId = {{ $reservation->id }};
    const paymentId = {{ $guaranteePayment->id }};
    
    if (stripePublicKey) {
        initializeStripeElements(stripePublicKey);
    }
    
    function initializeStripeElements(pubKey) {
        stripe = Stripe(pubKey);
        elements = stripe.elements();
        
        cardElement = elements.create('card', {
            style: {
                base: {
                    color: '#0A1A2F',
                    fontFamily: '"Montserrat", sans-serif',
                    fontSize: '16px',
                    '::placeholder': { color: '#8A96A6' },
                }
            },
            hidePostalCode: true,
        });
        
        cardElement.mount('#card-element');
        
        // Créer le PaymentIntent
        createPaymentIntent();
    }
    
    function createPaymentIntent() {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Initialisation...';
        
        fetch('{{ route("api.payments.create-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reservation_id: reservationId,
                payment_type: 'deposit_guarantee',
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.success) {
                clientSecret = data.client_secret;
                btn.disabled = false;
                btn.innerHTML = originalText;
            } else {
                throw new Error(data.message || 'Erreur d\'initialisation');
            }
        })
        .catch(error => {
            alert('Erreur : ' + error.message);
            btn.innerHTML = originalText;
        });
    }
    
    btn.addEventListener('click', function() {
        if (!clientSecret) return;
        
        const cardholderName = document.getElementById('cardholder-name').value.trim();
        if (!cardholderName) {
            alert('Veuillez entrer le nom sur la carte');
            return;
        }
        
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Traitement...';
        
        stripe.confirmCardPayment(clientSecret, {
            payment_method: {
                card: cardElement,
                billing_details: { name: cardholderName },
            },
        })
        .then(function(result) {
            if (result.error) {
                cardErrors.textContent = result.error.message;
                cardErrors.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = originalText;
            } else {
                confirmOnServer(result.paymentIntent.id);
            }
        });
    });
    
    function confirmOnServer(intentId) {
        fetch('{{ route("api.payments.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                payment_intent_id: intentId,
                payment_id: paymentId,
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data && data.success) {
                window.location.href = '{{ route("espace-client.reservations") }}?payment_success=1';
            } else {
                alert('Erreur confirmation serveur');
                window.location.href = '{{ route("espace-client.reservations") }}';
            }
        });
    }
});
</script>
@endpush
