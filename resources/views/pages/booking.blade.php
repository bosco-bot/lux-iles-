@extends('layouts.app')

@section('title', 'Réserver - ' . ($villa->name ?? 'Villa') . ' | LUXÎLES')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: #F8F8F6; }
    ::-webkit-scrollbar-thumb { background: #CBAE82; border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: #A48C64; }
    
    /* Custom Checkbox */
    .custom-checkbox {
        position: absolute;
        opacity: 0;
        width: 1.25rem;
        height: 1.25rem;
        z-index: 1;
        cursor: pointer;
    }
    .custom-checkbox + div {
        width: 1.25rem;
        height: 1.25rem;
        border: 1px solid rgba(138, 150, 166, 0.4);
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s;
        background-color: transparent;
    }
    .custom-checkbox:checked + div {
        background-color: #CBAE82 !important;
        border-color: #CBAE82 !important;
    }
    .custom-checkbox:checked + div svg {
        display: block !important;
    }
    .custom-checkbox:hover + div {
        border-color: #CBAE82;
    }

    /* Flatpickr Customization for Luxury Feel */
    #inline-calendar {
        width: 100%;
    }
    #inline-calendar .flatpickr-calendar {
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        font-family: 'Montserrat', sans-serif;
        border-radius: 8px;
        padding: 15px;
        width: 100% !important;
        max-width: 100% !important;
        box-sizing: border-box;
    }
    /* Remplacer toutes les couleurs bleues par la couleur dorée des icônes */
    #inline-calendar .flatpickr-calendar * {
        --flatpickr-blue: #CBAE82 !important;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-current-month {
        color: var(--lux-dark-blue) !important;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-prev-month,
    #inline-calendar .flatpickr-calendar .flatpickr-next-month {
        color: #CBAE82 !important;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-prev-month:hover,
    #inline-calendar .flatpickr-calendar .flatpickr-next-month:hover {
        color: #A48C64 !important;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-months {
        width: 100%;
        box-sizing: border-box;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-days {
        width: 100%;
        box-sizing: border-box;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-day {
        width: calc(100% / 7) !important;
        max-width: calc(100% / 7) !important;
        box-sizing: border-box;
        flex: 0 0 calc(100% / 7);
    }
    #inline-calendar .flatpickr-calendar .flatpickr-weekdays {
        width: 100%;
        box-sizing: border-box;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-weekday {
        width: calc(100% / 7) !important;
        flex: 0 0 calc(100% / 7);
        box-sizing: border-box;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-dayContainer {
        width: 100%;
        box-sizing: border-box;
    }
    /* Styles pour les dates sélectionnées - Forcer les couleurs dorées */
    #inline-calendar .flatpickr-day.selected,
    #inline-calendar .flatpickr-day.startRange,
    #inline-calendar .flatpickr-day.endRange,
    #inline-calendar .flatpickr-day.selected.inRange,
    #inline-calendar .flatpickr-day.startRange.inRange,
    #inline-calendar .flatpickr-day.endRange.inRange,
    #inline-calendar .flatpickr-day.selected:focus,
    #inline-calendar .flatpickr-day.startRange:focus,
    #inline-calendar .flatpickr-day.endRange:focus,
    #inline-calendar .flatpickr-day.selected:hover,
    #inline-calendar .flatpickr-day.startRange:hover,
    #inline-calendar .flatpickr-day.endRange:hover,
    #inline-calendar .flatpickr-day.selected.prevMonthDay,
    #inline-calendar .flatpickr-day.startRange.prevMonthDay,
    #inline-calendar .flatpickr-day.endRange.prevMonthDay,
    #inline-calendar .flatpickr-day.selected.nextMonthDay,
    #inline-calendar .flatpickr-day.startRange.nextMonthDay,
    #inline-calendar .flatpickr-day.endRange.nextMonthDay {
        background: #CBAE82 !important;
        border-color: #CBAE82 !important;
        color: white !important;
    }
    #inline-calendar .flatpickr-day.inRange {
        box-shadow: -5px 0 0 #F8F8F6, 5px 0 0 #F8F8F6 !important;
        background: #e6dac6 !important;
        border-color: #e6dac6 !important;
        color: var(--lux-dark-blue) !important;
    }
    /* Empêcher toute couleur bleue sur le calendrier - Utiliser la couleur dorée des icônes */
    #inline-calendar .flatpickr-day:not(.flatpickr-disabled):not(.disabled):hover {
        background: rgba(203, 174, 130, 0.1) !important;
        border-color: #CBAE82 !important;
        color: var(--lux-dark-blue) !important;
    }
    #inline-calendar .flatpickr-day:focus {
        background: #CBAE82 !important;
        border-color: #CBAE82 !important;
        color: white !important;
        outline: none !important;
        box-shadow: 0 0 0 2px rgba(203, 174, 130, 0.2) !important;
    }
    /* Remplacer toutes les couleurs bleues par défaut de Flatpickr */
    #inline-calendar .flatpickr-calendar .flatpickr-day.today {
        border-color: #CBAE82 !important;
    }
    #inline-calendar .flatpickr-calendar .flatpickr-day.today:hover {
        background: rgba(203, 174, 130, 0.15) !important;
        border-color: #CBAE82 !important;
    }
    /* S'assurer qu'aucun bleu n'apparaît dans le calendrier */
    #inline-calendar .flatpickr-calendar * {
        --flatpickr-blue: #CBAE82 !important;
    }
    #inline-calendar .flatpickr-calendar [style*="blue"],
    #inline-calendar .flatpickr-calendar [style*="Blue"],
    #inline-calendar .flatpickr-calendar [style*="BLUE"] {
        background-color: #CBAE82 !important;
        border-color: #CBAE82 !important;
        color: white !important;
    }
    /* Style pour les dates désactivées (bloquées/réservées) */
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.disabled {
        background-color: #CBAE821A !important;
        color: #CBAE82 !important;
        cursor: not-allowed !important;
        opacity: 0.6;
    }
    .flatpickr-day.flatpickr-disabled:hover,
    .flatpickr-day.disabled:hover {
        background-color: #CBAE821A !important;
    }
</style>
@endpush

@section('content')

<!-- Main Content -->
<main style="padding-top: 8rem; padding-bottom: 5rem;">
    
    <!-- Booking Layout Container -->
    <div class="container-fluid" style="max-width: 1200px;">
        <div class="row g-4">
            
            <!-- LEFT COLUMN: Booking Details -->
            <div class="col-12 col-lg-8">
                
                <!-- Header of the booking flow -->
                <div class="mb-4">
                    <a href="{{ route('villas.show', $villa->id) }}" class="text-sm mb-4 d-inline-flex align-items-center text-decoration-none" style="--tw-text-opacity: 1; color: rgb(138 150 166 / var(--tw-text-opacity, 1)); transition: color 0.3s; gap: 0.5rem;" onmouseover="this.style.color='var(--lux-gold)'" onmouseout="this.style.setProperty('color', 'rgb(138 150 166 / var(--tw-text-opacity, 1))')">
                        <i class="fa-solid fa-arrow-left"></i> Retour à la villa
                    </a>
                    <h1 class="h2 font-serif text-lux-dark-blue mt-2" style="font-family: 'Playfair Display', serif; font-size: 2.5rem;">Finalisez votre séjour</h1>
                    <p class="text-lux-greyBlue mt-2">{{ $villa->name }} • {{ $villa->island->name ?? '' }}</p>
                </div>

                <!-- Section 1: Dates Selection -->
                <section id="dates-selection" class="bg-white rounded p-4 mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-4 d-flex align-items-center" style="font-family: 'Playfair Display', serif; gap: 0.75rem;">
                        <i class="fa-regular fa-calendar text-lux-gold"></i>
                        Vos dates de séjour
                    </h2>
                    
                    <div class="row g-4">
                        <!-- Custom Date Input -->
                        <div class="col-12 col-md-5">
                            <div class="mb-3">
                                <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Arrivée</label>
                                <div class="position-relative">
                                    <input id="check-in" type="text" class="form-control ps-5" style="border-color: rgba(138, 150, 166, 0.2); cursor: default; background-color: #f8f9fa;" placeholder="Sélectionnez une date" readonly value="{{ $checkIn ? \Carbon\Carbon::parse($checkIn)->format('d M Y') : '' }}">
                                    <i class="fa-solid fa-plane-arrival position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: var(--lux-gold);"></i>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="d-block text-uppercase small mb-2" style="letter-spacing: 0.05em; color: var(--lux-greyBlue);">Départ</label>
                                <div class="position-relative">
                                    <input id="check-out" type="text" class="form-control ps-5" style="border-color: rgba(138, 150, 166, 0.2); cursor: default; background-color: #f8f9fa;" placeholder="Sélectionnez une date" readonly value="{{ $checkOut ? \Carbon\Carbon::parse($checkOut)->format('d M Y') : '' }}">
                                    <i class="fa-solid fa-plane-departure position-absolute" style="left: 1rem; top: 50%; transform: translateY(-50%); color: var(--lux-gold);"></i>
                                </div>
                            </div>
                            <div class="pt-2">
                                <p class="small text-lux-greyBlue mb-0"><i class="fa-solid fa-circle-info me-1"></i> Séjour minimum de {{ $villa->minimum_stay_nights ?? 3 }} nuit{{ ($villa->minimum_stay_nights ?? 3) > 1 ? 's' : '' }}.</p>
                            </div>
                        </div>

                        <!-- Calendar Visual Placeholder -->
                        <div class="col-12 col-md-7 d-none d-md-block">
                            <div class="bg-lux-beige rounded p-3 border" style="border-color: rgba(138, 150, 166, 0.1); min-height: 200px; width: 100%;">
                                <div id="inline-calendar" style="width: 100%; max-width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 2: Guests -->
                <section id="guests-selection" class="bg-white rounded p-4 mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-4 d-flex align-items-center" style="font-family: 'Playfair Display', serif; gap: 0.75rem;">
                        <i class="fa-regular fa-user text-lux-gold"></i>
                        Voyageurs
                    </h2>
                    
                    <!-- Champs cachés pour stocker les valeurs -->
                    <input type="hidden" id="number-of-adults" name="adults" value="{{ $guests ?? 2 }}">
                    <input type="hidden" id="number-of-children" name="children" value="0">
                    <input type="hidden" id="number-of-infants" name="infants" value="0">
                    <input type="hidden" id="number-of-guests" name="guests" value="{{ $guests ?? 2 }}">
                    
                    <div>
                        <!-- Adults -->
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3" style="border-color: rgba(138, 150, 166, 0.1);">
                            <div>
                                <h3 class="fw-medium text-lux-dark-blue mb-0">Adultes</h3>
                                <p class="small text-lux-greyBlue mb-0">13 ans et plus</p>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                <button type="button" class="btn-guests-decrease rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="adults">
                                    <i class="fa-solid fa-minus small"></i>
                                </button>
                                <span class="guests-count-adults text-center fw-medium" style="min-width: 2rem;">{{ $guests ?? 2 }}</span>
                                <button type="button" class="btn-guests-increase rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="adults" data-max="{{ $villa->max_capacity }}">
                                    <i class="fa-solid fa-plus small"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Children -->
                        <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-3" style="border-color: rgba(138, 150, 166, 0.1);">
                            <div>
                                <h3 class="fw-medium text-lux-dark-blue mb-0">Enfants</h3>
                                <p class="small text-lux-greyBlue mb-0">De 2 à 12 ans</p>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                <button type="button" class="btn-guests-decrease rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="children">
                                    <i class="fa-solid fa-minus small"></i>
                                </button>
                                <span class="guests-count-children text-center fw-medium" style="min-width: 2rem;">0</span>
                                <button type="button" class="btn-guests-increase rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="children">
                                    <i class="fa-solid fa-plus small"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Infants -->
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h3 class="fw-medium text-lux-dark-blue mb-0">Bébés</h3>
                                <p class="small text-lux-greyBlue mb-0">Moins de 2 ans</p>
                            </div>
                            <div class="d-flex align-items-center" style="gap: 0.75rem;">
                                <button type="button" class="btn-guests-decrease rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="infants">
                                    <i class="fa-solid fa-minus small"></i>
                                </button>
                                <span class="guests-count-infants text-center fw-medium" style="min-width: 2rem;">0</span>
                                <button type="button" class="btn-guests-increase rounded-circle border d-flex align-items-center justify-content-center text-lux-greyBlue" style="width: 2rem; height: 2rem; border-color: rgba(138, 150, 166, 0.3); background: transparent; transition: all 0.3s;" data-type="infants">
                                    <i class="fa-solid fa-plus small"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Section 3: Extra Services -->
                <section id="extras-selection" class="bg-white rounded p-4 mb-4 border" style="border-color: rgba(138, 150, 166, 0.1); box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                    <h2 class="h5 font-serif text-lux-dark-blue mb-4 d-flex align-items-center" style="font-family: 'Playfair Display', serif; gap: 0.75rem;">
                        <i class="fa-solid fa-bell-concierge text-lux-gold"></i>
                        Services additionnels
                    </h2>
                    
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="d-flex align-items-start p-3 border rounded" style="border-color: rgba(138, 150, 166, 0.2); cursor: pointer; transition: border-color 0.3s; gap: 0.75rem;">
                                <div class="position-relative d-flex align-items-center flex-shrink-0">
                                    <input type="checkbox" class="custom-checkbox position-absolute opacity-0" name="extra_transfer" value="included" style="width: 1.25rem; height: 1.25rem; z-index: 1; cursor: pointer;">
                                    <div class="border rounded d-flex align-items-center justify-content-center" style="width: 1.25rem; height: 1.25rem; border-color: rgba(138, 150, 166, 0.4); transition: all 0.3s; background-color: transparent;">
                                        <svg class="text-white d-none" style="width: 0.75rem; height: 0.75rem; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <span class="d-block fw-medium text-lux-dark-blue">Transfert aéroport VIP</span>
                                    <span class="small text-lux-greyBlue">Chauffeur privé à l'arrivée et au départ</span>
                                    <span class="d-block mt-1 small fw-medium text-lux-dark-blue">Inclus</span>
                                </div>
                            </label>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="d-flex align-items-start p-3 border rounded" style="border-color: rgba(138, 150, 166, 0.2); cursor: pointer; transition: border-color 0.3s; gap: 0.75rem;">
                                <div class="position-relative d-flex align-items-center flex-shrink-0">
                                    <input type="checkbox" class="custom-checkbox position-absolute opacity-0" name="extra_chef" value="450" style="width: 1.25rem; height: 1.25rem; z-index: 1; cursor: pointer;">
                                    <div class="border rounded d-flex align-items-center justify-content-center" style="width: 1.25rem; height: 1.25rem; border-color: rgba(138, 150, 166, 0.4); transition: all 0.3s; background-color: transparent;">
                                        <svg class="text-white d-none" style="width: 0.75rem; height: 0.75rem; pointer-events: none;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                </div>
                                <div>
                                    <span class="d-block fw-medium text-lux-dark-blue">Chef à domicile (1er soir)</span>
                                    <span class="small text-lux-greyBlue">Menu dégustation local</span>
                                    <span class="d-block mt-1 small fw-medium text-lux-dark-blue">+ 450€</span>
                                </div>
                            </label>
                        </div>
                    </div>
                </section>

            </div>

            <!-- RIGHT COLUMN: Price Summary (Sticky) -->
            <div class="col-12 col-lg-4">
                <div class="position-sticky" style="top: 8rem;">
                    
                    <!-- Card Recap -->
                    <div class="bg-white rounded shadow-lg border overflow-hidden mb-4" style="border-color: rgba(138, 150, 166, 0.1);">
                        <!-- Villa Mini Header -->
                        <div class="position-relative" style="height: 200px;">
                            @if($primaryPhoto)
                                <img src="{{ asset('storage/' . $primaryPhoto->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $villa->name }}">
                            @else
                                <div class="w-100 h-100 bg-lux-beige d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-3x text-lux-greyBlue opacity-50"></i>
                                </div>
                            @endif
                            <div class="position-absolute bottom-0 start-0 end-0" style="background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); padding: 1rem;">
                                <h3 class="font-serif text-white mb-0" style="font-family: 'Playfair Display', serif; font-size: 1.25rem;">{{ $villa->name }}</h3>
                                <p class="small text-white mb-0 opacity-90"><i class="fa-solid fa-location-dot text-lux-gold me-1"></i> {{ $villa->island->name ?? '' }}</p>
                            </div>
                        </div>

                        <div class="p-4">
                            <!-- Selected Dates Summary -->
                            <div class="d-flex justify-content-between align-items-center small mb-4 pb-3 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                                <div>
                                    <p class="text-lux-greyBlue text-uppercase small mb-1" style="letter-spacing: 0.05em; font-size: 0.7rem;">Dates</p>
                                    <p class="fw-medium text-lux-dark-blue mb-0" id="dates-summary">-</p>
                                </div>
                                <div class="text-end">
                                    <p class="text-lux-greyBlue text-uppercase small mb-1" style="letter-spacing: 0.05em; font-size: 0.7rem;">Durée</p>
                                    <p class="fw-medium text-lux-dark-blue mb-0" id="nights-summary">-</p>
                                </div>
                            </div>

                            <!-- Price Breakdown -->
                            <div class="mb-4" id="price-breakdown">
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span id="base-price-text">-</span>
                                    <span id="base-price-value">-</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Frais de ménage</span>
                                    <span id="cleaning-fee-value">{{ number_format($villa->cleaning_fee ?? 0, 0, ',', ' ') }}€</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-greyBlue mb-2">
                                    <span>Taxes et frais</span>
                                    <span id="taxes-value">-</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-gold fw-medium pt-2" id="discount-row" style="display: none;">
                                    <span id="discount-label">Réduction</span>
                                    <span id="discount-value">-</span>
                                </div>
                            </div>

                            <!-- Code promo §3.2 CDC -->
                            <div class="mb-4 pb-3 border-bottom" style="border-color: rgba(138, 150, 166, 0.1);">
                                <label class="small text-uppercase text-lux-greyBlue mb-2 d-block" style="letter-spacing: 0.05em; font-size: 0.7rem;">Code promotionnel</label>
                                <div class="d-flex gap-2 mb-2">
                                    <input type="text" id="promo-code-input" class="form-control form-control-sm" placeholder="Ex. SUMMER25" autocomplete="off" style="text-transform: uppercase;">
                                    <button type="button" class="btn btn-sm btn-outline-lux-gold text-nowrap" id="apply-promo-btn">Appliquer</button>
                                </div>
                                <div id="promo-feedback" class="small"></div>
                                @guest
                                    <p class="small text-lux-greyBlue mb-0 mt-1"><i class="fa-solid fa-info-circle me-1"></i>Connectez-vous pour utiliser un code promo.</p>
                                @endguest
                            </div>

                            <!-- Total -->
                            <div class="border-top pt-3 mb-4" style="border-color: rgba(138, 150, 166, 0.1);">
                                <div class="d-flex justify-content-between align-items-end mb-1">
                                    <span class="font-serif text-lux-dark-blue fw-bold" style="font-family: 'Playfair Display', serif; font-size: 1.125rem;">Total</span>
                                    <span class="font-serif text-lux-dark-blue fw-bold" style="font-family: 'Playfair Display', serif; font-size: 1.5rem;" id="total-price">-</span>
                                </div>
                                <p class="text-end small text-lux-greyBlue mb-0">Taxes et frais inclus</p>
                            </div>

                            <!-- Payment Schedule -->
                            <div class="bg-lux-beige p-3 rounded mb-4 border" style="border-color: rgba(138, 150, 166, 0.1);">
                                <h4 class="small fw-bold text-lux-dark-blue text-uppercase mb-3" style="font-size: 0.7rem;">Échéancier de paiement</h4>
                                <div class="d-flex justify-content-between small mb-2">
                                    <span class="text-lux-dark-blue fw-medium">À régler aujourd'hui (30%)</span>
                                    <span class="fw-bold text-lux-dark-blue" id="deposit-amount">-</span>
                                </div>
                                <div class="d-flex justify-content-between small text-lux-greyBlue">
                                    <span id="balance-due-date">Solde avant le -</span>
                                    <span id="balance-amount">-</span>
                                </div>
                            </div>

                            <!-- CTA -->
                            <button class="btn btn-lux-primary w-100 py-3 mb-3 fw-medium" id="proceed-payment-btn" style="transition: all 0.3s; box-shadow: 0 4px 6px rgba(203, 174, 130, 0.2);">
                                Passer au paiement sécurisé
                            </button>
                            
                            <p class="text-center small text-lux-greyBlue d-flex align-items-center justify-content-center gap-1 mb-0">
                                <i class="fa-solid fa-lock"></i> Transaction 100% sécurisée
                            </p>
                        </div>
                    </div>

                    <!-- Cancellation Policy -->
                    <div class="bg-white rounded shadow-sm p-4 mb-4 border" style="border-color: rgba(138, 150, 166, 0.1);">
                        <h3 class="fw-medium text-lux-dark-blue mb-3 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-shield-halved text-lux-gold"></i> Politique d'annulation
                        </h3>
                        <p class="small text-lux-greyBlue mb-3" style="line-height: 1.75;">
                            <span class="fw-medium text-lux-dark-blue">Flexible :</span> Remboursement intégral jusqu'à 30 jours avant l'arrivée. 50% remboursé jusqu'à 7 jours avant.
                        </p>
                        <a href="{{ route('cancellation-policies.index') }}" class="small text-lux-gold text-decoration-none" target="_blank">Voir les conditions complètes</a>
                    </div>

                    <!-- Contact Helper -->
                    <div class="bg-lux-dark-blue p-3 rounded text-white">
                        <div class="d-flex align-items-center" style="gap: 0.75rem;">
                            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 2.5rem; height: 2.5rem; background-color: rgba(255, 255, 255, 0.1);">
                                <i class="fa-solid fa-headset text-lux-gold"></i>
                            </div>
                            <div>
                                <p class="small fw-medium mb-0" style="font-size: 0.875rem;">Besoin d'aide ?</p>
                                <p class="mb-0" style="color: rgba(255, 255, 255, 0.7); font-size: 0.75rem;">Notre conciergerie est disponible 24/7</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</main>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dates bloquées (VillaAvailabilityService — étape C)
    const blockedDatesList = @json($blockedDates ?? []) || [];
    
    // Fonction pour convertir les dates bloquées au format attendu par Flatpickr
    function getDisabledDates(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');
        const dateStr = `${year}-${month}-${day}`;
        return blockedDatesList.includes(dateStr);
    }
    
    const villaId = {{ $villa->id }};
    let basePricePerNight = {{ $villa->base_price_per_night }}; // Prix par défaut, sera mis à jour selon les dates
    const cleaningFee = {{ $villa->cleaning_fee ?? 0 }};
    const serviceFeePercentage = {{ $villa->service_fee_percentage ?? $serviceFeePercentage }};
    const globalTaxRate = {{ $globalTaxRate }};
    const touristTaxPerNight = {{ $touristTaxPerNight }};
    const touristTaxEnabled = {{ $touristTaxEnabled ? 'true' : 'false' }};
    const depositPercentage = {{ $depositPercentage }};
    const depositPercentageMax = {{ $depositPercentageMax ?? 50 }};
    const minStay = {{ $villa->minimum_stay_nights ?? 3 }};
    const maxCapacity = {{ $villa->max_capacity }};
    const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};
    const checkPromoUrl = @json(route('bookings.check-promo'));

    let appliedPromo = { code: null, discount: 0 };
    
    // Fonction pour calculer le prix avec les tarifs saisonniers
    function calculatePriceWithSeasons(checkIn, checkOut, nights, adults, children, infants) {
        const checkInStr = checkIn.toISOString().split('T')[0];
        const checkOutStr = checkOut.toISOString().split('T')[0];
        
        // Appeler l'API pour obtenir le prix avec les tarifs saisonniers
        fetch('{{ route("bookings.calculate-price") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                villa_id: villaId,
                check_in: checkInStr,
                check_out: checkOutStr
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Utiliser le prix calculé avec les tarifs saisonniers
                const basePrice = data.base_price;
                basePricePerNight = data.average_price_per_night; // Prix moyen par nuit pour l'affichage
                
                const serviceFee = basePrice * (serviceFeePercentage / 100);
                const vatAmount = (cleaningFee + serviceFee) * (globalTaxRate / 100);
                
                let touristTax = 0;
                if (touristTaxEnabled) {
                    const totalGuests = adults + children + infants;
                    touristTax = touristTaxPerNight * totalGuests * nights;
                }
                
                let extrasTotal = 0;
                document.querySelectorAll('.custom-checkbox:checked').forEach(cb => {
                    if (cb.value !== 'included' && !isNaN(parseFloat(cb.value))) {
                        extrasTotal += parseFloat(cb.value);
                    }
                });
                
                const subtotal = basePrice + cleaningFee + serviceFee + vatAmount + touristTax + extrasTotal;
                const discount = appliedPromo.discount || 0;
                const total = Math.max(0, subtotal - discount);
                const deposit = total * (depositPercentage / 100);
                const balance = total - deposit;
                
                // Mise à jour de l'affichage
                const checkInStr = checkIn.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
                const checkOutStr = checkOut.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
                
                document.getElementById('dates-summary').textContent = checkInStr + ' - ' + checkOutStr;
                document.getElementById('nights-summary').textContent = nights + ' Nuit' + (nights > 1 ? 's' : '');
                
                // Afficher le prix moyen par nuit (peut varier selon les saisons)
                const priceDisplay = nights > 1 
                    ? new Intl.NumberFormat('fr-FR').format(basePricePerNight) + '€/nuit × ' + nights + ' nuits'
                    : new Intl.NumberFormat('fr-FR').format(basePricePerNight) + '€ × ' + nights + ' nuit';
                document.getElementById('base-price-text').textContent = priceDisplay;
                document.getElementById('base-price-value').textContent = new Intl.NumberFormat('fr-FR').format(basePrice) + '€';
                
                updatePriceDisplay(vatAmount, touristTax, total, deposit, balance, discount, appliedPromo.code);
            } else {
                // En cas d'erreur, utiliser le prix de base
                calculatePriceFallback(checkIn, checkOut, nights, adults, children, infants);
            }
        })
        .catch(error => {
            console.error('Erreur lors du calcul du prix:', error);
            // En cas d'erreur, utiliser le prix de base
            calculatePriceFallback(checkIn, checkOut, nights, adults, children, infants);
        });
    }
    
    // Fonction de secours si l'API échoue
    function calculatePriceFallback(checkIn, checkOut, nights, adults, children, infants) {
        const basePrice = basePricePerNight * nights;
        const serviceFee = basePrice * (serviceFeePercentage / 100);
        const vatAmount = (cleaningFee + serviceFee) * (globalTaxRate / 100);
        let touristTax = 0;
        if (touristTaxEnabled) {
            const totalGuests = adults + children + infants;
            touristTax = touristTaxPerNight * totalGuests * nights;
        }
        let extrasTotal = 0;
        document.querySelectorAll('.custom-checkbox:checked').forEach(cb => {
            if (cb.value !== 'included' && !isNaN(parseFloat(cb.value))) {
                extrasTotal += parseFloat(cb.value);
            }
        });
        const subtotal = basePrice + cleaningFee + serviceFee + vatAmount + touristTax + extrasTotal;
        const discount = appliedPromo.discount || 0;
        const total = Math.max(0, subtotal - discount);
        const deposit = total * (depositPercentage / 100);
        const balance = total - deposit;
        
        const checkInStr = checkIn.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        const checkOutStr = checkOut.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        
        document.getElementById('dates-summary').textContent = checkInStr + ' - ' + checkOutStr;
        document.getElementById('nights-summary').textContent = nights + ' Nuit' + (nights > 1 ? 's' : '');
        document.getElementById('base-price-text').textContent = new Intl.NumberFormat('fr-FR').format(basePricePerNight) + '€ × ' + nights + ' nuits';
        document.getElementById('base-price-value').textContent = new Intl.NumberFormat('fr-FR').format(basePrice) + '€';
        
        updatePriceDisplay(vatAmount, touristTax, total, deposit, balance, discount, appliedPromo.code);
    }
    
    // Fonction pour mettre à jour l'affichage des prix
    function updatePriceDisplay(vatAmount, touristTax, total, deposit, balance, discount = 0, promoCode = null) {
        const totalTaxes = vatAmount + touristTax;
        const discountRow = document.getElementById('discount-row');
        if (discount > 0 && promoCode) {
            discountRow.style.display = 'flex';
            document.getElementById('discount-label').textContent = 'Réduction (' + promoCode + ')';
            document.getElementById('discount-value').textContent = '-' + new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(discount) + '€';
        } else {
            discountRow.style.display = 'none';
        }
        document.getElementById('taxes-value').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(totalTaxes) + '€';
        document.getElementById('total-price').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(total) + '€';
        document.getElementById('deposit-amount').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(deposit) + '€';
        document.getElementById('balance-amount').textContent = new Intl.NumberFormat('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(balance) + '€';
    }

    document.getElementById('apply-promo-btn').addEventListener('click', function() {
        const promoInput = document.getElementById('promo-code-input');
        const feedback = document.getElementById('promo-feedback');
        const code = promoInput.value.trim();

        if (!code) {
            feedback.innerHTML = '<span class="text-danger">Veuillez saisir un code.</span>';
            return;
        }

        if (!isAuthenticated) {
            feedback.innerHTML = '<span class="text-danger">Connectez-vous pour appliquer un code promo.</span>';
            return;
        }

        const checkIn = checkInPicker.selectedDates[0];
        const checkOut = checkOutPicker.selectedDates[0];
        if (!checkIn || !checkOut) {
            feedback.innerHTML = '<span class="text-danger">Sélectionnez vos dates avant d\'appliquer un code.</span>';
            return;
        }

        const formatDate = (date) => {
            const y = date.getFullYear();
            const m = String(date.getMonth() + 1).padStart(2, '0');
            const d = String(date.getDate()).padStart(2, '0');
            return `${y}-${m}-${d}`;
        };

        feedback.innerHTML = '<span class="text-lux-greyBlue">Vérification…</span>';

        fetch(checkPromoUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            },
            body: JSON.stringify({
                promo_code: code,
                villa_id: villaId,
                check_in: formatDate(checkIn),
                check_out: formatDate(checkOut),
                guests: adults + children + infants,
            }),
        })
        .then(res => res.json().then(data => ({ ok: res.ok, data })))
        .then(({ ok, data }) => {
            if (data.valid) {
                appliedPromo = { code: data.promo_code, discount: parseFloat(data.discount_amount) };
                promoInput.value = data.promo_code;
                feedback.innerHTML = '<span class="text-success"><i class="fa-solid fa-check me-1"></i>' + data.message + '</span>';
                updatePrice();
            } else {
                appliedPromo = { code: null, discount: 0 };
                feedback.innerHTML = '<span class="text-danger">' + (data.message || 'Code invalide.') + '</span>';
                updatePrice();
            }
        })
        .catch(() => {
            feedback.innerHTML = '<span class="text-danger">Erreur lors de la vérification du code.</span>';
        });
    });
    
    // Dates initiales depuis les paramètres URL
    const initialCheckIn = @json($checkIn ?? null);
    const initialCheckOut = @json($checkOut ?? null);
    
    // Convertir les dates en objets Date si elles existent
    let checkInDate = null;
    let checkOutDate = null;
    
    if (initialCheckIn) {
        const parts = initialCheckIn.split('-');
        checkInDate = new Date(parts[0], parts[1] - 1, parts[2]);
    }
    if (initialCheckOut) {
        const parts = initialCheckOut.split('-');
        checkOutDate = new Date(parts[0], parts[1] - 1, parts[2]);
    }
    
    // Initialiser Flatpickr
    const today = new Date();
    today.setHours(0, 0, 0, 0); // Remettre à minuit pour comparaison de dates

    // La date minimale devrait être aujourd'hui si la date demandée est aujourd'hui ou dans le futur
    let minDate = new Date(today.getTime() + (1 * 24 * 60 * 60 * 1000)); // Par défaut : demain

    // Si une date d'arrivée est demandée et qu'elle est aujourd'hui ou dans le futur, ajuster minDate
    if (checkInDate) {
        const checkInDateOnly = new Date(checkInDate.getFullYear(), checkInDate.getMonth(), checkInDate.getDate());
        const todayOnly = new Date(today.getFullYear(), today.getMonth(), today.getDate());

        if (checkInDateOnly >= todayOnly) {
            minDate = today; // Permettre aujourd'hui et les dates futures
        }
    }
    
    const checkInPicker = flatpickr("#check-in", {
        minDate: minDate,
        dateFormat: "d M Y",
        defaultDate: checkInDate || null,
        clickOpens: false,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length > 0) {
                const checkOutMin = new Date(selectedDates[0].getTime() + (minStay * 24 * 60 * 60 * 1000));
                checkOutPicker.set('minDate', checkOutMin);
                updatePrice();
            }
        }
    });

    const checkOutPicker = flatpickr("#check-out", {
        minDate: checkInDate ? new Date(checkInDate.getTime() + (minStay * 24 * 60 * 60 * 1000)) : new Date(minDate.getTime() + (minStay * 24 * 60 * 60 * 1000)),
        dateFormat: "d M Y",
        defaultDate: checkOutDate || null,
        clickOpens: false,
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function() {
            updatePrice();
        }
    });
    
    // Inline calendar - Permet de modifier les dates sélectionnées depuis la page détail villa
    const inlineCalendar = flatpickr("#inline-calendar", {
        inline: true,
        mode: "range",
        minDate: minDate,
        dateFormat: "d M Y",
        defaultDate: checkInDate && checkOutDate ? [checkInDate, checkOutDate] : null,
        allowInput: false,
        disable: [
            function(date) {
                // Désactiver les dates bloquées/réservées
                return getDisabledDates(date);
            }
        ],
        locale: {
            firstDayOfWeek: 1,
            weekdays: {
                shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
                longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']
            },
            months: {
                shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
                longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre']
            }
        },
        onChange: function(selectedDates, dateStr, instance) {
            if (selectedDates.length === 2) {
                // Deux dates sélectionnées : arrivée et départ
                const startDate = selectedDates[0];
                const endDate = selectedDates[1];
                
                // Vérifier que la période ne chevauche pas de dates bloquées
                const startStr = startDate.toISOString().split('T')[0];
                const endStr = endDate.toISOString().split('T')[0];
                
                // Vérifier chaque date de la période
                let hasBlockedDate = false;
                const checkDate = new Date(startDate);
                while (checkDate < endDate) {
                    const dateStr = checkDate.toISOString().split('T')[0];
                    if (blockedDatesList.includes(dateStr)) {
                        hasBlockedDate = true;
                        break;
                    }
                    checkDate.setDate(checkDate.getDate() + 1);
                }
                
                if (hasBlockedDate) {
                    alert('Cette période contient des dates non disponibles. La villa est réservée ou bloquée pour certaines dates de cette période.');
                    instance.clear();
                    checkInPicker.clear();
                    checkOutPicker.clear();
                    return;
                }
                
                // Mettre à jour les champs d'arrivée et de départ
                checkInPicker.setDate(startDate, false);
                checkOutPicker.setDate(endDate, false);
                
                // Mettre à jour la date minimale pour le départ
                const checkOutMin = new Date(startDate.getTime() + (minStay * 24 * 60 * 60 * 1000));
                checkOutPicker.set('minDate', checkOutMin);
                
                // Calculer et mettre à jour le prix
                updatePrice();
            } else if (selectedDates.length === 1) {
                // Vérifier que la date d'arrivée n'est pas bloquée
                const selectedDateStr = selectedDates[0].toISOString().split('T')[0];
                if (blockedDatesList.includes(selectedDateStr)) {
                    alert('Cette date n\'est pas disponible. La villa est réservée ou bloquée pour cette date.');
                    instance.clear();
                    checkInPicker.clear();
                    checkOutPicker.clear();
                    return;
                }
                // Une seule date sélectionnée : c'est la nouvelle date d'arrivée
                const newStartDate = selectedDates[0];
                
                // Mettre à jour la date d'arrivée
                checkInPicker.setDate(newStartDate, false);
                
                // Réinitialiser la date de départ
                checkOutPicker.clear();
                
                // Mettre à jour la date minimale pour le départ
                const checkOutMin = new Date(newStartDate.getTime() + (minStay * 24 * 60 * 60 * 1000));
                checkOutPicker.set('minDate', checkOutMin);
                
                // Réinitialiser le prix car on n'a pas de date de départ
                document.getElementById('dates-summary').textContent = '-';
                document.getElementById('nights-summary').textContent = '-';
                document.getElementById('base-price-text').textContent = '-';
                document.getElementById('base-price-value').textContent = '-';
                document.getElementById('taxes-value').textContent = '-';
                document.getElementById('total-price').textContent = '-';
                document.getElementById('deposit-amount').textContent = '-';
                document.getElementById('balance-amount').textContent = '-';
            } else {
                // Aucune date sélectionnée : réinitialiser tout
                checkInPicker.clear();
                checkOutPicker.clear();
                
                // Réinitialiser l'affichage du prix
                document.getElementById('dates-summary').textContent = '-';
                document.getElementById('nights-summary').textContent = '-';
                document.getElementById('base-price-text').textContent = '-';
                document.getElementById('base-price-value').textContent = '-';
                document.getElementById('taxes-value').textContent = '-';
                document.getElementById('total-price').textContent = '-';
                document.getElementById('deposit-amount').textContent = '-';
                document.getElementById('balance-amount').textContent = '-';
            }
        }
    });
    
    // Forcer l'affichage des dates initiales et initialiser le prix si les dates sont déjà définies
    if (checkInDate && checkOutDate) {
        setTimeout(function() {
            // On définit les dates directement dans les instances
            checkInPicker.setDate(checkInDate, true);
            checkOutPicker.setDate(checkOutDate, true);

            if (inlineCalendar) {
                inlineCalendar.setDate([checkInDate, checkOutDate], true);
            }

            // Forcer la mise à jour du prix
            updatePrice();
        }, 300);
    }

    // Gestion des voyageurs
    let adults = parseInt(document.querySelector('.guests-count-adults').textContent) || 2;
    let children = parseInt(document.querySelector('.guests-count-children').textContent) || 0;
    let infants = parseInt(document.querySelector('.guests-count-infants').textContent) || 0;

    // Fonction pour mettre à jour l'affichage et les champs cachés
    function updateGuestsDisplay() {
        document.querySelector('.guests-count-adults').textContent = adults;
        document.querySelector('.guests-count-children').textContent = children;
        document.querySelector('.guests-count-infants').textContent = infants;
        
        // Mettre à jour les champs cachés si ils existent
        const adultsInput = document.getElementById('number-of-adults');
        const childrenInput = document.getElementById('number-of-children');
        const infantsInput = document.getElementById('number-of-infants');
        const totalGuestsInput = document.getElementById('number-of-guests');
        
        if (adultsInput) adultsInput.value = adults;
        if (childrenInput) childrenInput.value = children;
        if (infantsInput) infantsInput.value = infants;
        if (totalGuestsInput) totalGuestsInput.value = adults + children + infants;
    }

    document.querySelectorAll('.btn-guests-increase').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            const max = parseInt(this.dataset.max) || 10;
            const currentTotal = adults + children + infants;
            
            // Vérifier la capacité maximale avant d'augmenter
            if (currentTotal >= max) {
                alert('La capacité maximale de cette villa est de ' + max + ' personne' + (max > 1 ? 's' : '') + '.');
                return;
            }
            
            if (type === 'adults') {
                adults++;
            } else if (type === 'children') {
                children++;
            } else if (type === 'infants') {
                infants++;
            }
            
            updateGuestsDisplay();
        });
    });

    document.querySelectorAll('.btn-guests-decrease').forEach(btn => {
        btn.addEventListener('click', function() {
            const type = this.dataset.type;
            
            if (type === 'adults' && adults > 1) {
                adults--;
                updateGuestsDisplay();
            } else if (type === 'children' && children > 0) {
                children--;
                updateGuestsDisplay();
            } else if (type === 'infants' && infants > 0) {
                infants--;
                updateGuestsDisplay();
            }
        });
    });
    
    // Initialiser l'affichage
    updateGuestsDisplay();

    // Gestion des checkboxes
    document.querySelectorAll('.custom-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const div = this.nextElementSibling;
            if (this.checked) {
                div.style.backgroundColor = '#CBAE82';
                div.style.borderColor = '#CBAE82';
                div.querySelector('svg').classList.remove('d-none');
            } else {
                div.style.backgroundColor = 'transparent';
                div.style.borderColor = 'rgba(138, 150, 166, 0.4)';
                div.querySelector('svg').classList.add('d-none');
            }
            updatePrice();
        });
    });

    // Fonction de mise à jour du prix
    function updatePrice() {
        const checkIn = checkInPicker.selectedDates[0];
        const checkOut = checkOutPicker.selectedDates[0];
        
        if (!checkIn || !checkOut) {
            return;
        }
        
        const nights = Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        if (nights < minStay) {
            return;
        }
        
        // Calculer le prix avec les tarifs saisonniers via API
        calculatePriceWithSeasons(checkIn, checkOut, nights, adults, children, infants);
        
        // Date d'échéance du solde (30 jours avant l'arrivée)
        const balanceDueDays = 30; // Jours avant l'arrivée pour le paiement du solde
        const balanceDueDate = new Date(checkIn);
        balanceDueDate.setDate(balanceDueDate.getDate() - balanceDueDays);
        
        // Si la date d'échéance est dans le passé (réservation de dernière minute),
        // le solde est dû immédiatement (aujourd'hui)
        const today = new Date();
        today.setHours(0, 0, 0, 0);
        balanceDueDate.setHours(0, 0, 0, 0);
        
        if (balanceDueDate < today) {
            balanceDueDate.setTime(today.getTime());
            document.getElementById('balance-due-date').textContent = 'Solde à régler immédiatement';
        } else {
            document.getElementById('balance-due-date').textContent = 'Solde avant le ' + balanceDueDate.toLocaleDateString('fr-FR', { day: 'numeric', month: 'short', year: 'numeric' });
        }
    }
    
    
    // Bouton de paiement
    document.getElementById('proceed-payment-btn').addEventListener('click', function() {
        const checkIn = checkInPicker.selectedDates[0];
        const checkOut = checkOutPicker.selectedDates[0];
        
        if (!checkIn || !checkOut) {
            alert('Veuillez sélectionner vos dates d\'arrivée et de départ.');
            return;
        }
        
        const nights = Math.round((checkOut - checkIn) / (1000 * 60 * 60 * 24));
        if (nights < minStay) {
            alert('Le séjour minimum est de ' + minStay + ' nuit' + (minStay > 1 ? 's' : '') + '.');
            return;
        }
        
        // Vérifier qu'au moins un adulte est sélectionné
        const totalGuests = adults + children + infants;
        if (totalGuests < 1 || adults < 1) {
            alert('Veuillez sélectionner au moins un adulte.');
            return;
        }
        
        // Vérifier que le nombre total de voyageurs ne dépasse pas la capacité maximale
        if (totalGuests > maxCapacity) {
            alert('Le nombre total de voyageurs (' + totalGuests + ') dépasse la capacité maximale de cette villa (' + maxCapacity + ' personne' + (maxCapacity > 1 ? 's' : '') + ').');
            return;
        }
        
        // Rediriger vers la page de paiement avec les données
        // Formater les dates manuellement pour éviter les problèmes de timezone
        const formatDate = (date) => {
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        };
        
        const checkInStr = formatDate(checkIn);
        const checkOutStr = formatDate(checkOut);
        
        const params = new URLSearchParams({
            villa_id: villaId,
            check_in: checkInStr,
            check_out: checkOutStr,
            guests: adults + children + infants,
            adults: adults,
            children: children,
            infants: infants
        });

        if (appliedPromo.code) {
            params.set('promo_code', appliedPromo.code);
        }
        
        window.location.href = '{{ route("bookings.payment") }}?' + params.toString();
    });
});
</script>
@endpush

