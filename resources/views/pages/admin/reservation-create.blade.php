@extends('layouts.admin')

@section('title', 'Nouvelle Réservation | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.reservations') }}" class="text-white-50 text-decoration-none hover-lux-gold">Réservations</a>
    <span class="mx-2">/</span>
    <span class="text-white">Nouvelle</span>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Réservation manuelle
            </h1>
            <p class="small text-lux-greyBlue mb-0">Saisie hors ligne (téléphone, email, agence) — §3.11 CDC</p>
        </div>
        <a href="{{ route('admin.reservations') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.reservations.store') }}" method="POST" id="manual-reservation-form">
        @csrf

        <div class="row g-4">
            <div class="col-lg-8">
                <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                    <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-regular fa-user me-2 text-lux-gold"></i>Client et villa
                    </h3>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="user_id" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Client <span class="text-danger">*</span></label>
                            <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror" required>
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ (string) old('user_id', request('client_id')) === (string) $client->id ? 'selected' : '' }}>
                                        {{ $client->last_name }} {{ $client->first_name }} — {{ $client->email }}
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="mt-1">
                                <a href="{{ route('admin.clients.create') }}" class="small text-lux-greyBlue">Créer un nouveau client</a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="villa_id" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Villa <span class="text-danger">*</span></label>
                            <select name="villa_id" id="villa_id" class="form-select @error('villa_id') is-invalid @enderror" required>
                                <option value="">Sélectionner une villa</option>
                                @foreach($villas as $villa)
                                    <option value="{{ $villa->id }}"
                                        data-min-stay="{{ $villa->minimum_stay_nights ?? 3 }}"
                                        data-max-capacity="{{ $villa->max_capacity }}"
                                        {{ (string) old('villa_id') === (string) $villa->id ? 'selected' : '' }}>
                                        {{ $villa->name }}@if($villa->island) ({{ $villa->island->name }})@endif
                                    </option>
                                @endforeach
                            </select>
                            @error('villa_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <p class="small text-lux-greyBlue mb-0 mt-1" id="villa-rules-hint"></p>
                        </div>
                        <div class="col-md-4">
                            <label for="check_in_date" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Arrivée <span class="text-danger">*</span></label>
                            <input type="text" name="check_in_date" id="check_in_date" class="form-control js-date-field @error('check_in_date') is-invalid @enderror" value="{{ old('check_in_date') }}" placeholder="Choisir une villa d'abord" autocomplete="off" required readonly>
                            @error('check_in_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="check_out_date" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Départ <span class="text-danger">*</span></label>
                            <input type="text" name="check_out_date" id="check_out_date" class="form-control js-date-field @error('check_out_date') is-invalid @enderror" value="{{ old('check_out_date') }}" placeholder="Choisir une villa d'abord" autocomplete="off" required readonly>
                            @error('check_out_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-4">
                            <label for="number_of_guests" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Voyageurs <span class="text-danger">*</span></label>
                            <input type="number" name="number_of_guests" id="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" value="{{ old('number_of_guests', 2) }}" min="1" required>
                            @error('number_of_guests')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <p class="small text-lux-greyBlue mb-0" id="availability-hint">
                                Sélectionnez une villa pour afficher les dates disponibles.
                            </p>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                    <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-solid fa-euro-sign me-2 text-lux-gold"></i>Montant
                    </h3>

                    <div id="price-calc-alert" class="alert alert-warning d-none small" role="alert"></div>

                    <div id="price-breakdown" class="mb-3 d-none">
                        <div class="d-flex justify-content-between small mb-1"><span class="text-lux-greyBlue">Prix séjour</span><span id="bd-base">—</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span class="text-lux-greyBlue">Ménage</span><span id="bd-cleaning">—</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span class="text-lux-greyBlue">Frais de service</span><span id="bd-service">—</span></div>
                        <div class="d-flex justify-content-between small mb-1"><span class="text-lux-greyBlue">TVA</span><span id="bd-vat">—</span></div>
                        <div class="d-flex justify-content-between small mb-2"><span class="text-lux-greyBlue">Taxe de séjour</span><span id="bd-tourist">—</span></div>
                        <div class="d-flex justify-content-between fw-medium border-top pt-2">
                            <span class="text-lux-dark-blue">Total calculé</span>
                            <span id="bd-total" class="text-lux-gold">—</span>
                        </div>
                        <p class="small text-lux-greyBlue mb-0 mt-1" id="bd-nights"></p>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="use_custom_total" id="use_custom_total" value="1" {{ old('use_custom_total') ? 'checked' : '' }}>
                        <label class="form-check-label" for="use_custom_total">Montant convenu (tarif négocié)</label>
                    </div>

                    <div id="custom-total-wrap" class="{{ old('use_custom_total') ? '' : 'd-none' }}">
                        <label for="total_price" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Montant total (€)</label>
                        <input type="number" name="total_price" id="total_price" class="form-control @error('total_price') is-invalid @enderror" value="{{ old('total_price') }}" step="0.01" min="0">
                        @error('total_price')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mt-3">
                        <label for="deposit_percentage" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Pourcentage d'acompte</label>
                        <div class="input-group" style="max-width: 200px;">
                            <input type="number" name="deposit_percentage" id="deposit_percentage" class="form-control" value="{{ old('deposit_percentage', $depositPercentageDefault) }}" min="1" max="100">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </section>

                <section class="bg-white rounded shadow-sm border p-4 mb-4" style="border-color: rgba(0,0,0,0.05) !important;">
                    <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-regular fa-note-sticky me-2 text-lux-gold"></i>Notes
                    </h3>
                    <div class="mb-3">
                        <label for="special_requests" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Demandes du client</label>
                        <textarea name="special_requests" id="special_requests" class="form-control" rows="2">{{ old('special_requests') }}</textarea>
                    </div>
                    <div>
                        <label for="admin_notes" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Notes internes</label>
                        <textarea name="admin_notes" id="admin_notes" class="form-control" rows="2" placeholder="Ex. réservation téléphonique, tarif négocié…">{{ old('admin_notes') }}</textarea>
                    </div>
                </section>
            </div>

            <div class="col-lg-4">
                <section class="bg-white rounded shadow-sm border p-4 mb-4 sticky-top" style="border-color: rgba(0,0,0,0.05) !important; top: 1rem;">
                    <h3 class="h5 font-serif text-lux-dark-blue mb-4" style="font-family: 'Playfair Display', serif;">
                        <i class="fa-solid fa-credit-card me-2 text-lux-gold"></i>Paiement
                    </h3>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Mode de paiement <span class="text-danger">*</span></label>
                        <select name="payment_method" id="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                            <option value="bank_transfer" {{ old('payment_method') === 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="check" {{ old('payment_method') === 'check' ? 'selected' : '' }}>Chèque</option>
                            <option value="cash" {{ old('payment_method') === 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="other" {{ old('payment_method', 'other') === 'other' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('payment_method')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-4">
                        <label for="manual_payment_status" class="form-label small text-uppercase fw-medium text-lux-greyBlue">Statut de paiement <span class="text-danger">*</span></label>
                        <select name="manual_payment_status" id="manual_payment_status" class="form-select @error('manual_payment_status') is-invalid @enderror" required>
                            <option value="pending" {{ old('manual_payment_status') === 'pending' ? 'selected' : '' }}>En attente</option>
                            <option value="deposit_paid" {{ old('manual_payment_status') === 'deposit_paid' ? 'selected' : '' }}>Acompte versé</option>
                            <option value="fully_paid" {{ old('manual_payment_status') === 'fully_paid' ? 'selected' : '' }}>Réglé</option>
                        </select>
                        @error('manual_payment_status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="alert alert-info small mb-4" role="alert">
                        <i class="fa-solid fa-envelope me-1"></i>
                        Un email de confirmation sera envoyé automatiquement au client à la création.
                    </div>

                    <div id="submit-block-alert" class="alert alert-danger small d-none mb-3" role="alert"></div>

                    <button type="submit" class="btn w-100 text-white" style="background-color: var(--lux-dark-blue);">
                        <i class="fa-solid fa-check me-2"></i>Créer la réservation
                    </button>
                </section>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('manual-reservation-form');
    const villaSelect = document.getElementById('villa_id');
    const checkIn = document.getElementById('check_in_date');
    const checkOut = document.getElementById('check_out_date');
    const guests = document.getElementById('number_of_guests');
    const useCustomTotal = document.getElementById('use_custom_total');
    const customWrap = document.getElementById('custom-total-wrap');
    const totalPriceInput = document.getElementById('total_price');
    const priceBreakdown = document.getElementById('price-breakdown');
    const priceAlert = document.getElementById('price-calc-alert');
    const submitBlockAlert = document.getElementById('submit-block-alert');
    const villaRulesHint = document.getElementById('villa-rules-hint');
    const availabilityHint = document.getElementById('availability-hint');
    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    const blockedDatesUrlFor = function (villaId) {
        return @json(url('/admin/villas')) + '/' + villaId + '/blocked-dates';
    };
    const hasPreselectedClient = @json((bool) request('client_id'));

    const flatpickrLocale = {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: ['Dim', 'Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam'],
            longhand: ['Dimanche', 'Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi'],
        },
        months: {
            shorthand: ['Jan', 'Fév', 'Mar', 'Avr', 'Mai', 'Juin', 'Juil', 'Aoû', 'Sep', 'Oct', 'Nov', 'Déc'],
            longhand: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
        },
    };

    let blockedDatesSet = new Set();
    let minStayNights = 3;
    let checkInPicker = null;
    let checkOutPicker = null;

    function formatEuro(amount) {
        return new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'EUR' }).format(amount);
    }

    function localYmd(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return y + '-' + m + '-' + d;
    }

    function isPeriodBlocked(checkInYmd, checkOutYmd) {
        if (!checkInYmd || !checkOutYmd) {
            return false;
        }
        const start = new Date(checkInYmd + 'T12:00:00');
        const end = new Date(checkOutYmd + 'T12:00:00');
        const cur = new Date(start);
        while (cur < end) {
            if (blockedDatesSet.has(localYmd(cur))) {
                return true;
            }
            cur.setDate(cur.getDate() + 1);
        }
        return false;
    }

    function setDateFieldsLocked(locked) {
        [checkIn, checkOut].forEach(function (el) {
            el.readOnly = locked;
            el.classList.toggle('bg-light', locked);
            el.placeholder = locked ? 'Choisir une villa d\'abord' : 'Sélectionner…';
        });
    }

    function updateAvailabilityHint() {
        if (!villaSelect.value) {
            availabilityHint.textContent = hasPreselectedClient
                ? 'Client présélectionné — choisissez une villa pour activer les dates d\'arrivée et de départ.'
                : 'Sélectionnez une villa pour afficher les dates disponibles.';
            availabilityHint.classList.remove('text-danger');
            return;
        }
        const count = blockedDatesSet.size;
        if (count === 0) {
            availabilityHint.textContent = 'Aucune date bloquée sur le calendrier de cette villa.';
        } else {
            availabilityHint.textContent = count + ' jour' + (count > 1 ? 's' : '') + ' indisponible' + (count > 1 ? 's' : '') + ' (réservations ou blocages). Les dates grisées ne sont pas sélectionnables.';
        }
        availabilityHint.classList.remove('text-danger');
    }

    function hideSubmitBlockMessage() {
        if (submitBlockAlert) {
            submitBlockAlert.textContent = '';
            submitBlockAlert.classList.add('d-none');
        }
    }

    function showSubmitBlockMessage(message) {
        hideSubmitBlockMessage();
        if (submitBlockAlert) {
            submitBlockAlert.textContent = message;
            submitBlockAlert.classList.remove('d-none');
            submitBlockAlert.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function validateSelectedPeriod(updateUi) {
        if (updateUi === undefined) {
            updateUi = true;
        }
        const inDate = checkIn.value;
        const outDate = checkOut.value;
        if (inDate && isDateBlockedString(inDate)) {
            if (updateUi) {
                availabilityHint.textContent = 'La date d\'arrivée sélectionnée n\'est pas disponible.';
                availabilityHint.classList.add('text-danger');
            }
            return false;
        }
        if (outDate && isDateBlockedString(outDate)) {
            if (updateUi) {
                availabilityHint.textContent = 'La date de départ sélectionnée n\'est pas disponible.';
                availabilityHint.classList.add('text-danger');
            }
            return false;
        }
        if (inDate && outDate && isPeriodBlocked(inDate, outDate)) {
            if (updateUi) {
                availabilityHint.textContent = 'Cette période chevauche des dates déjà réservées ou bloquées.';
                availabilityHint.classList.add('text-danger');
            }
            return false;
        }
        if (updateUi) {
            updateAvailabilityHint();
            hideSubmitBlockMessage();
        }
        return true;
    }

    function isDateBlockedString(ymd) {
        return blockedDatesSet.has(ymd);
    }

    function destroyDatePickers() {
        if (checkInPicker) {
            checkInPicker.destroy();
            checkInPicker = null;
        }
        if (checkOutPicker) {
            checkOutPicker.destroy();
            checkOutPicker = null;
        }
    }

    function initDatePickers() {
        destroyDatePickers();

        const blockedList = Array.from(blockedDatesSet);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        checkInPicker = flatpickr(checkIn, {
            dateFormat: 'Y-m-d',
            minDate: today,
            disable: blockedList,
            locale: flatpickrLocale,
            defaultDate: checkIn.value || null,
            onChange: function (selectedDates) {
                if (selectedDates.length > 0) {
                    const minOut = new Date(selectedDates[0].getTime() + minStayNights * 24 * 60 * 60 * 1000);
                    if (checkOutPicker) {
                        checkOutPicker.set('minDate', minOut);
                    }
                }
                validateSelectedPeriod();
                recalculatePrice();
            },
        });

        let checkOutMin = today;
        if (checkIn.value) {
            const checkInDate = new Date(checkIn.value + 'T12:00:00');
            checkOutMin = new Date(checkInDate.getTime() + minStayNights * 24 * 60 * 60 * 1000);
        }

        checkOutPicker = flatpickr(checkOut, {
            dateFormat: 'Y-m-d',
            minDate: checkOutMin,
            disable: blockedList,
            locale: flatpickrLocale,
            defaultDate: checkOut.value || null,
            onChange: function () {
                validateSelectedPeriod();
                recalculatePrice();
            },
        });
    }

    function loadBlockedDates(villaId) {
        if (!villaId) {
            blockedDatesSet = new Set();
            destroyDatePickers();
            checkIn.value = '';
            checkOut.value = '';
            setDateFieldsLocked(true);
            updateAvailabilityHint();
            return Promise.resolve();
        }

        setDateFieldsLocked(true);
        availabilityHint.textContent = 'Chargement du calendrier…';

        return fetch(blockedDatesUrlFor(villaId), {
            headers: { Accept: 'application/json' },
            credentials: 'same-origin',
        })
        .then(function (res) {
            if (!res.ok) {
                throw new Error('blocked_dates_http_' + res.status);
            }
            return res.json();
        })
        .then(function (data) {
            blockedDatesSet = new Set(data.blocked_dates || []);
            minStayNights = data.min_stay || 3;
            setDateFieldsLocked(false);
            initDatePickers();
            updateAvailabilityHint();
            validateSelectedPeriod();
        })
        .catch(function () {
            blockedDatesSet = new Set();
            setDateFieldsLocked(false);
            availabilityHint.textContent = 'Impossible de charger les dates indisponibles. Vous pouvez tout de même saisir les dates manuellement.';
            availabilityHint.classList.add('text-danger');
            initDatePickers();
        });
    }

    function updateVillaHint() {
        const opt = villaSelect.selectedOptions[0];
        if (!opt || !opt.value) {
            villaRulesHint.textContent = '';
            return;
        }
        villaRulesHint.textContent = 'Min. ' + opt.dataset.minStay + ' nuit(s) · Max. ' + opt.dataset.maxCapacity + ' personne(s)';
    }

    function recalculatePrice() {
        const villaId = villaSelect.value;
        const inDate = checkIn.value;
        const outDate = checkOut.value;
        const guestCount = guests.value;

        priceAlert.classList.add('d-none');
        priceBreakdown.classList.add('d-none');

        if (!villaId || !inDate || !outDate || !guestCount) {
            return;
        }

        if (!validateSelectedPeriod()) {
            return;
        }

        fetch('{{ route('admin.reservations.calculate-price') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrf,
            },
            body: JSON.stringify({
                villa_id: villaId,
                check_in: inDate,
                check_out: outDate,
                guests: parseInt(guestCount, 10),
            }),
        })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
        .then(function ({ ok, data }) {
            if (!ok || !data.success) {
                priceAlert.textContent = data.message || 'Impossible de calculer le tarif.';
                priceAlert.classList.remove('d-none');
                return;
            }

            document.getElementById('bd-base').textContent = formatEuro(data.base_price);
            document.getElementById('bd-cleaning').textContent = formatEuro(data.cleaning_fee);
            document.getElementById('bd-service').textContent = formatEuro(data.service_fee);
            document.getElementById('bd-vat').textContent = formatEuro(data.vat_amount);
            document.getElementById('bd-tourist').textContent = formatEuro(data.tourist_tax);
            document.getElementById('bd-total').textContent = formatEuro(data.total);
            document.getElementById('bd-nights').textContent = data.nights + ' nuit' + (data.nights > 1 ? 's' : '');

            priceBreakdown.classList.remove('d-none');

            if (!useCustomTotal.checked) {
                totalPriceInput.value = data.total;
            }
        })
        .catch(function () {
            priceAlert.textContent = 'Erreur lors du calcul du tarif.';
            priceAlert.classList.remove('d-none');
        });
    }

    useCustomTotal.addEventListener('change', function () {
        customWrap.classList.toggle('d-none', !this.checked);
        if (!this.checked) {
            recalculatePrice();
        }
    });

    villaSelect.addEventListener('change', function () {
        checkIn.value = '';
        checkOut.value = '';
        updateVillaHint();
        loadBlockedDates(villaSelect.value).then(function () {
            recalculatePrice();
        });
    });

    guests.addEventListener('change', recalculatePrice);

    form.addEventListener('submit', function (e) {
        if (!villaSelect.value) {
            e.preventDefault();
            showSubmitBlockMessage('Veuillez sélectionner une villa avant de créer la réservation.');
            villaSelect.focus();
            return;
        }
        if (!checkIn.value || !checkOut.value) {
            e.preventDefault();
            showSubmitBlockMessage('Veuillez renseigner les dates d\'arrivée et de départ.');
            return;
        }
        if (!validateSelectedPeriod(false)) {
            e.preventDefault();
            showSubmitBlockMessage('Impossible de créer la réservation : cette période chevauche des dates déjà réservées ou bloquées. Choisissez d\'autres dates.');
            return;
        }
        hideSubmitBlockMessage();
    });

    [checkIn, checkOut].forEach(function (el) {
        el.addEventListener('click', function () {
            if (!villaSelect.value) {
                availabilityHint.textContent = 'Choisissez d\'abord une villa pour activer le calendrier des dates.';
                availabilityHint.classList.add('text-danger');
                villaSelect.focus();
            }
        });
    });

    setDateFieldsLocked(true);
    updateVillaHint();
    updateAvailabilityHint();

    if (villaSelect.value) {
        loadBlockedDates(villaSelect.value).then(function () {
            recalculatePrice();
        });
    }
});
</script>
@endpush
