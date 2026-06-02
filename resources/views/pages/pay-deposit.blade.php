@extends('layouts.app')

@section('title', 'Règlement de l\'acompte - Réservation ' . $reservation->reservation_number . ' | LUXÎLES')

@push('styles')
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #F8F8F6; }
    ::-webkit-scrollbar-thumb { background: #CBAE82; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #A48C64; }
    
    .input-focus-ring:focus-within {
        box-shadow: 0 0 0 2px rgba(10, 26, 47, 0.1);
        border-color: var(--lux-dark-blue);
    }
    
    /* Styles pour Stripe Elements */
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
    #card-element input {
        color: #0A1A2F !important;
        font-family: 'Montserrat', sans-serif !important;
        font-size: 16px !important;
        letter-spacing: 0.1em !important;
    }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main style="padding-top: 8rem; padding-bottom: 5rem;">
    
    <!-- Page Title -->
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="text-center mb-5">
            <span class="text-lux-gold text-uppercase small fw-medium mb-2 d-block" style="letter-spacing: 0.2em;">Engagement</span>
            <h1 class="h2 font-serif text-lux-dark-blue" style="font-family: 'Playfair Display', serif;">Règlement de l'acompte</h1>
            <p class="text-lux-greyBlue mt-2">Réservation #{{ $reservation->reservation_number }}</p>
        </div>

        <div class="row g-4">
            
            <!-- Left Column: Payment & Form -->
            <div class="col-12 col-lg-8">
                
                <!-- Documents Alert -->
                <div class="alert alert-info border-0 shadow-sm mb-4 d-flex gap-3 p-4" style="background-color: rgba(138, 150, 166, 0.05); border-radius: 1rem;">
                    <i class="fa-solid fa-file-signature text-lux-gold fs-4"></i>
                    <div class="flex-grow-1">
                        <h4 class="h6 fw-bold text-lux-dark-blue mb-2">Documents de votre réservation</h4>
                        <p class="small text-lux-greyBlue mb-3">
                            Votre contrat et votre facture sont déjà disponibles. Vous pouvez les consulter avant de procéder au règlement.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $contract = $reservation->documents->where('type', 'contract')->first();
                                $invoice = $reservation->documents->where('type', 'invoice')->first();
                            @endphp
                            
                            @if($contract)
                                <a href="{{ route('espace-client.documents.download', $contract) }}" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-file-contract"></i> Consulter le contrat
                                </a>
                            @endif
                            
                            @if($invoice)
                                <a href="{{ route('espace-client.documents.download', $invoice) }}" class="btn btn-sm btn-outline-dark d-inline-flex align-items-center gap-2 small">
                                    <i class="fa-solid fa-file-invoice"></i> Voir la facture
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Section: Paiement Sécurisé -->
                <section id="payment-section" class="bg-white p-4 p-md-5 rounded mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    
                    <div class="position-relative mb-4" style="background: linear-gradient(135deg, rgba(10, 26, 47, 0.02) 0%, rgba(203, 174, 130, 0.02) 100%); border-radius: 0.75rem; padding: 2rem; border: 1px solid rgba(138, 150, 166, 0.1);">
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
                                        <!-- Stripe Elements mounted here -->
                                    </div>
                                </div>
                                <div id="card-errors" class="mt-2 small text-danger" role="alert" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- CTA Action -->
                    <button type="button" class="btn btn-lux-secondary w-100 py-3 mb-3 fw-medium" id="confirm-payment-btn" style="transition: all 0.3s; box-shadow: 0 4px 6px rgba(10, 26, 47, 0.1); font-size: 1.125rem;">
                        <span>Régler l'acompte de {{ number_format($depositAmount, 2, ',', ' ') }} €</span>
                        <i class="fa-solid fa-chevron-right ms-2 small"></i>
                    </button>
                    <p class="text-center small text-lux-greyBlue mt-3 mb-0">
                        Paiement sécurisé via Stripe. Le statut de votre réservation sera mis à jour instantanément.
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
                                <span>Séjour</span>
                                <span class="fw-medium text-lux-dark-blue">{{ \Carbon\Carbon::parse($checkIn)->format('d/m') }} - {{ \Carbon\Carbon::parse($checkOut)->format('d/m/Y') }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-lux-greyBlue mb-4">
                                <span>Durée</span>
                                <span class="fw-medium text-lux-dark-blue">{{ $nights }} nuit(s)</span>
                            </div>

                            <div class="mt-4 pt-4 border-top">
                                @if($reservation->discount_amount > 0 && $reservation->promoCode)
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-success small fw-medium">Réduction ({{ $reservation->promoCode->code }})</span>
                                    <span class="text-success small fw-medium">-{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</span>
                                </div>
                                @endif
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="text-lux-greyBlue small">Total du séjour</span>
                                    <span class="text-lux-greyBlue small">{{ number_format($total, 2, ',', ' ') }} €</span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <span class="text-lux-dark-blue fw-bold">Acompte à régler</span>
                                    <span class="font-serif text-lux-gold fw-bold fs-3">{{ number_format($depositAmount, 2, ',', ' ') }} €</span>
                                </div>
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
    const paymentId = {{ $depositPayment->id }};
    
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
        createPaymentIntent();
    }
    
    function createPaymentIntent() {
        btn.disabled = true;
        btn.innerHTML = '<span class="status-loading spinner-border spinner-border-sm me-2"></span>Initialisation...';
        
        fetch('{{ route("api.payments.create-intent") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                reservation_id: reservationId,
                payment_type: 'deposit',
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
        btn.innerHTML = '<span class="status-loading spinner-border spinner-border-sm me-2"></span>Traitement...';
        
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
                alert('Erreur de confirmation sur le serveur. Veuillez contacter le support.');
                window.location.href = '{{ route("espace-client.reservations") }}';
            }
        });
    }
});
</script>
@endpush
