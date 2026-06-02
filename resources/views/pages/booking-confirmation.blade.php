@extends('layouts.app')

@section('title', 'Confirmation de Réservation | LUXÎLES')

@push('styles')
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #F8F8F6; }
    ::-webkit-scrollbar-thumb { background: #CBAE82; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #A48C64; }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animate-fade-in {
        animation: fadeIn 0.8s ease-out forwards;
    }
    
    .animate-delay-100 { animation-delay: 0.1s; }
    .animate-delay-200 { animation-delay: 0.2s; }
    .animate-delay-300 { animation-delay: 0.3s; }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main style="padding-top: 8rem; padding-bottom: 5rem;">
    
    <!-- Success Message Section -->
    <section id="success-message" class="container-fluid text-center mb-5 animate-fade-in" style="max-width: 1200px;">
        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-4" style="width: 6rem; height: 6rem; background-color: rgba(203, 174, 130, 0.1); border: 1px solid rgba(203, 174, 130, 0.3);">
            <i class="fa-solid fa-check text-lux-gold" style="font-size: 2.5rem;"></i>
        </div>
        <h1 class="h1 font-serif text-lux-dark-blue mb-3" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Félicitations !</h1>
        <p class="fs-5 text-lux-greyBlue fw-light mb-2">Votre réservation est confirmée. Préparez-vous à vivre l'exceptionnel.</p>
        <p class="small text-lux-greyBlue mt-2">Référence de réservation : <span class="fw-medium text-lux-dark-blue">#{{ $reservationNumber }}</span></p>
    </section>

    <!-- Confirmation Details Card -->
    <section id="confirmation-details" class="container-fluid mb-5 animate-fade-in animate-delay-100" style="max-width: 1200px;">
        <div class="bg-white rounded shadow-lg overflow-hidden border d-flex flex-column flex-lg-row" style="border-color: rgba(138, 150, 166, 0.1);">
            
            <!-- Left: Villa Image & Key Info -->
            <div class="col-lg-5 position-relative" style="min-height: 400px;">
                @if($primaryPhoto)
                    <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="position-absolute top-0 start-0 w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                @else
                    <div class="position-absolute top-0 start-0 w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                        <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50"></i>
                    </div>
                @endif
                <div class="position-absolute top-0 start-0 w-100 h-100" style="background: linear-gradient(to top, rgba(10, 26, 47, 0.9), transparent);"></div>
                <div class="position-absolute bottom-0 start-0 w-100 p-4 text-white">
                    <span class="bg-lux-gold text-lux-dark-blue small fw-bold px-3 py-1 rounded mb-3 d-inline-block text-uppercase" style="letter-spacing: 0.1em;">{{ $villa->island->name ?? 'Antilles' }}</span>
                    <h2 class="h3 font-serif mb-2" style="font-family: 'Playfair Display', serif;">{{ $villa->name }}</h2>
                    <div class="d-flex align-items-center gap-4 small text-white" style="opacity: 0.9;">
                        <span><i class="fa-solid fa-bed me-2 text-lux-gold"></i>{{ $villa->bedrooms ?? 4 }} Ch.</span>
                        <span><i class="fa-solid fa-users me-2 text-lux-gold"></i>{{ $villa->max_capacity ?? 8 }} Pers.</span>
                    </div>
                </div>
            </div>

            <!-- Right: Summary & Actions -->
            <div class="col-lg-7 p-4 p-lg-5 d-flex flex-column justify-content-between">
                <div>
                    <h3 class="h4 font-serif text-lux-dark-blue mb-4 pb-3 border-bottom" style="font-family: 'Playfair Display', serif; border-color: rgba(138, 150, 166, 0.1) !important;">Détails du séjour</h3>
                    
                    <div class="row g-4 mb-4">
                        <!-- Dates -->
                        <div class="col-md-6 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-beige d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                                <i class="fa-regular fa-calendar text-lux-gold"></i>
                            </div>
                            <div>
                                <p class="small text-lux-greyBlue text-uppercase mb-1" style="letter-spacing: 0.1em; font-size: 0.7rem;">Dates</p>
                                <p class="text-lux-dark-blue fw-medium mb-0">Du {{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</p>
                                <p class="text-lux-dark-blue fw-medium mb-0">Au {{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</p>
                                <p class="small text-lux-greyBlue mt-1 mb-0">{{ $nights }} Nuit{{ $nights > 1 ? 's' : '' }}</p>
                            </div>
                        </div>

                        <!-- Guests -->
                        <div class="col-md-6 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-beige d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                                <i class="fa-regular fa-user text-lux-gold"></i>
                            </div>
                            <div>
                                <p class="small text-lux-greyBlue text-uppercase mb-1" style="letter-spacing: 0.1em; font-size: 0.7rem;">Voyageurs</p>
                                @if($adults > 0)
                                    <p class="text-lux-dark-blue fw-medium mb-0">{{ $adults }} Adulte{{ $adults > 1 ? 's' : '' }}</p>
                                @endif
                                @if($children > 0)
                                    <p class="text-lux-dark-blue fw-medium mb-0">{{ $children }} Enfant{{ $children > 1 ? 's' : '' }}</p>
                                @endif
                                @if($infants > 0)
                                    <p class="text-lux-dark-blue fw-medium mb-0">{{ $infants }} Bébé{{ $infants > 1 ? 's' : '' }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Price -->
                        <div class="col-md-6 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-beige d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                                <i class="fa-solid fa-tag text-lux-gold"></i>
                            </div>
                            <div>
                                <p class="small text-lux-greyBlue text-uppercase mb-1" style="letter-spacing: 0.1em; font-size: 0.7rem;">Total payé</p>
                                <p class="h3 font-serif text-lux-dark-blue mb-0" style="font-family: 'Playfair Display', serif;">{{ number_format($total, 0, ',', ' ') }} €</p>
                                <p class="small text-success mt-1 mb-0"><i class="fa-solid fa-check-circle me-1"></i>Paiement validé</p>
                            </div>
                        </div>

                        <!-- Address -->
                        <div class="col-md-6 d-flex align-items-start gap-3">
                            <div class="rounded-circle bg-lux-beige d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem;">
                                <i class="fa-solid fa-map-pin text-lux-gold"></i>
                            </div>
                            <div>
                                <p class="small text-lux-greyBlue text-uppercase mb-1" style="letter-spacing: 0.1em; font-size: 0.7rem;">Adresse</p>
                                <p class="text-lux-dark-blue fw-medium mb-0">{{ $villa->address ?? 'Adresse à confirmer' }}</p>
                                <p class="text-lux-dark-blue fw-medium mb-0">{{ $villa->island->name ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-lux-beige p-4 rounded border mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                        <h4 class="font-serif text-lux-dark-blue mb-3 d-flex align-items-center gap-2" style="font-family: 'Playfair Display', serif;">
                            <i class="fa-regular fa-bell text-lux-gold"></i> Prochaines étapes
                        </h4>
                        <ul class="small text-lux-greyBlue mb-0 ps-4" style="list-style-type: disc;">
                            <li class="mb-2">Vous recevrez un email de confirmation détaillé dans les 5 minutes.</li>
                            <li class="mb-2">Notre conciergerie vous contactera 72h avant votre arrivée pour organiser votre transfert.</li>
                            <li>L'accès à la villa sera disponible à partir de {{ $villa->check_in_time ? \Carbon\Carbon::parse($villa->check_in_time)->format('H:i') : '15h00' }} le jour de votre arrivée.</li>
                        </ul>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="d-flex flex-column flex-sm-row gap-3 pt-4 border-top" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    @if(isset($reservation) && $reservation)
                        <a href="{{ route('espace-client.documents.contract', $reservation) }}" target="_blank" class="btn btn-lux-primary flex-fill py-3 px-4 rounded small fw-medium d-flex align-items-center justify-content-center gap-2 text-decoration-none" style="box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2); transition: all 0.3s;" onmouseover="this.style.boxShadow='0 6px 12px rgba(203, 174, 130, 0.3)'; this.style.transform='translateY(-1px)';" onmouseout="this.style.boxShadow='0 4px 6px rgba(203, 174, 130, 0.2)'; this.style.transform='translateY(0)';">
                            <i class="fa-regular fa-file-pdf"></i> Télécharger le contrat
                        </a>
                    @else
                        <button class="btn btn-lux-primary flex-fill py-3 px-4 rounded small fw-medium d-flex align-items-center justify-content-center gap-2" disabled style="box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2);">
                            <i class="fa-regular fa-file-pdf"></i> Contrat en préparation...
                        </button>
                    @endif
                    <a href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}" class="btn btn-outline-dark flex-fill py-3 px-4 rounded small fw-medium d-flex align-items-center justify-content-center gap-2" style="border-color: rgba(10, 26, 47, 0.2); color: var(--lux-dark-blue); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-dark-blue)'; this.style.color='var(--lux-dark-blue)'; this.style.backgroundColor='rgba(10, 26, 47, 0.05)';" onmouseout="this.style.borderColor='rgba(10, 26, 47, 0.2)'; this.style.color='var(--lux-dark-blue)'; this.style.backgroundColor='transparent';">
                        <i class="fa-solid fa-arrow-right"></i> Accéder à mon espace
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Divider -->
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="w-100" style="height: 1px; background-color: rgba(138, 150, 166, 0.1); margin: 3rem 0;"></div>
    </div>

    <!-- Additional Info Section -->
    <section id="additional-info" class="container-fluid mb-5 animate-fade-in animate-delay-200" style="max-width: 1200px;">
        <div class="row g-4 bg-white p-4 p-md-5 rounded border" style="border-color: rgba(138, 150, 166, 0.1) !important;">
            <!-- Summary List -->
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <div>
                        <h3 class="h4 font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">{{ $villa->name }}, {{ $villa->island->name ?? 'St-Barth' }}</h3>
                        <p class="text-lux-greyBlue fw-light mb-0">Réservation #{{ $reservationNumber }}</p>
                    </div>
                    <div class="text-end">
                        <p class="small text-lux-greyBlue text-uppercase mb-1" style="letter-spacing: 0.1em;">Statut</p>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1 small">
                            <span class="rounded-circle bg-success d-inline-block me-1" style="width: 0.5rem; height: 0.5rem;"></span> Confirmé
                        </span>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-6">
                        <p class="small text-lux-greyBlue text-uppercase mb-2" style="letter-spacing: 0.1em;">Arrivée</p>
                        <p class="h5 text-lux-dark-blue fw-medium mb-0">{{ \Carbon\Carbon::parse($checkIn)->format('d M Y') }}</p>
                        <p class="small text-lux-greyBlue mb-0">à partir de {{ $villa->check_in_time ? \Carbon\Carbon::parse($villa->check_in_time)->format('H:i') : '15:00' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-lux-greyBlue text-uppercase mb-2" style="letter-spacing: 0.1em;">Départ</p>
                        <p class="h5 text-lux-dark-blue fw-medium mb-0">{{ \Carbon\Carbon::parse($checkOut)->format('d M Y') }}</p>
                        <p class="small text-lux-greyBlue mb-0">avant {{ $villa->check_out_time ? \Carbon\Carbon::parse($villa->check_out_time)->format('H:i') : '11:00' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-lux-greyBlue text-uppercase mb-2" style="letter-spacing: 0.1em;">Invités</p>
                        <p class="h5 text-lux-dark-blue fw-medium mb-0">{{ $adults + $children + $infants }} Personne{{ ($adults + $children + $infants) > 1 ? 's' : '' }}</p>
                    </div>
                    <div class="col-6">
                        <p class="small text-lux-greyBlue text-uppercase mb-2" style="letter-spacing: 0.1em;">Total</p>
                        @if($reservation->discount_amount > 0 && $reservation->promoCode)
                            <p class="small text-success mb-1">Réduction ({{ $reservation->promoCode->code }}) : -{{ number_format($reservation->discount_amount, 2, ',', ' ') }} €</p>
                        @endif
                        <p class="h5 text-lux-dark-blue fw-medium mb-0">{{ number_format($total, 0, ',', ' ') }} €</p>
                    </div>
                </div>

                <div class="pt-3">
                    <h4 class="font-serif text-lux-dark-blue mb-3" style="font-family: 'Playfair Display', serif;">Documents de voyage</h4>
                    <div class="d-flex flex-wrap gap-3">
                        @if(isset($reservation) && $reservation)
                            <a href="{{ route('espace-client.documents.invoice', $reservation) }}" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                                <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div class="text-start">
                                    <p class="small fw-medium text-lux-dark-blue mb-0">Facture</p>
                                    <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Télécharger PDF</p>
                                </div>
                            </a>
                            <a href="{{ route('espace-client.documents.contract', $reservation) }}" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                                <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                    <i class="fa-solid fa-file-contract"></i>
                                </div>
                                <div class="text-start">
                                    <p class="small fw-medium text-lux-dark-blue mb-0">Contrat</p>
                                    <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Télécharger PDF</p>
                                </div>
                            </a>
                            @php
                                $depositPayment = $reservation->payments->where('type', 'deposit')->where('status', 'completed')->first();
                                $balancePayment = $reservation->payments->where('type', 'balance')->where('status', 'completed')->first();
                            @endphp
                            @if($depositPayment)
                                <a href="{{ route('espace-client.documents.receipt-deposit', ['reservation' => $reservation, 'payment' => $depositPayment]) }}" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                                    <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </div>
                                    <div class="text-start">
                                        <p class="small fw-medium text-lux-dark-blue mb-0">Reçu d'arrhes</p>
                                        <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Télécharger PDF</p>
                                    </div>
                                </a>
                            @endif
                            @if($balancePayment)
                                <a href="{{ route('espace-client.documents.receipt-balance', ['reservation' => $reservation, 'payment' => $balancePayment]) }}" target="_blank" class="d-flex align-items-center gap-3 p-3 rounded border text-decoration-none" style="border-color: rgba(138, 150, 166, 0.2); transition: all 0.3s;" onmouseover="this.style.borderColor='var(--lux-gold)'" onmouseout="this.style.borderColor='rgba(138, 150, 166, 0.2)'">
                                    <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                        <i class="fa-regular fa-file-lines"></i>
                                    </div>
                                    <div class="text-start">
                                        <p class="small fw-medium text-lux-dark-blue mb-0">Reçu de solde</p>
                                        <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Télécharger PDF</p>
                                    </div>
                                </a>
                            @endif
                        @else
                            <div class="d-flex align-items-center gap-3 p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2);">
                                <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </div>
                                <div class="text-start">
                                    <p class="small fw-medium text-lux-dark-blue mb-0">Facture</p>
                                    <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Disponible après confirmation</p>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-3 p-3 rounded border" style="border-color: rgba(138, 150, 166, 0.2);">
                                <div class="rounded bg-lux-dark-blue bg-opacity-5 text-lux-dark-blue d-flex align-items-center justify-content-center" style="width: 2rem; height: 2rem;">
                                    <i class="fa-solid fa-file-contract"></i>
                                </div>
                                <div class="text-start">
                                    <p class="small fw-medium text-lux-dark-blue mb-0">Contrat</p>
                                    <p class="small text-lux-greyBlue mb-0" style="font-size: 0.7rem;">Disponible après confirmation</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Side Card -->
            <div class="col-md-4">
                <div class="bg-lux-beige rounded p-4 h-100 d-flex flex-column justify-content-center align-items-center text-center border" style="border-color: rgba(138, 150, 166, 0.1) !important;">
                    <div class="rounded-circle bg-white shadow d-flex align-items-center justify-content-center mb-3 text-lux-gold" style="width: 4rem; height: 4rem; font-size: 1.5rem;">
                        <i class="fa-solid fa-user-shield"></i>
                    </div>
                    <h4 class="font-serif text-lux-dark-blue mb-2" style="font-family: 'Playfair Display', serif;">Espace Client</h4>
                    <p class="small text-lux-greyBlue mb-4">Gérez votre séjour, contactez la conciergerie et retrouvez vos documents.</p>
                    <a href="{{ auth()->check() && auth()->user()->is_admin ? route('admin.dashboard') : route('espace-client.index') }}" class="btn w-100 py-2 rounded small text-white" style="background-color: var(--lux-dark-blue); color: white; border: none; transition: all 0.3s; font-weight: 500;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'; this.style.color='white';" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'; this.style.color='white';">
                        Accéder à mon espace
                    </a>
                </div>
            </div>
        </div>
    </section>

</main>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation au chargement
    const elements = document.querySelectorAll('.animate-fade-in');
    elements.forEach((el, index) => {
        setTimeout(() => {
            el.style.opacity = '1';
        }, index * 100);
    });
});
</script>
@endpush

