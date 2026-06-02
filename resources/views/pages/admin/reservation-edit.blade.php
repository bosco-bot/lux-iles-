@extends('layouts.admin')

@section('title', 'Modifier Réservation | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <a href="{{ route('admin.reservations') }}" class="text-white-50 text-decoration-none hover-lux-gold">Réservations</a>
    <span class="mx-2">/</span>
    <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="text-white-50 text-decoration-none hover-lux-gold">Détails</a>
    <span class="mx-2">/</span>
    <span class="text-white">Modifier</span>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
@endpush

@section('content')
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h2 font-serif mb-2" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
                Modifier la réservation #{{ $reservation->reservation_number }}
            </h1>
        </div>
        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    <form action="{{ route('admin.reservations.update', $reservation->id) }}" method="POST" id="reservation-edit-form">
        @csrf
        @method('PUT')
        
        <div class="row g-4">
            <div class="col-lg-8">
                <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations de la réservation</h3>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="status" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="status" id="status" class="form-select" required>
                                <option value="pending" {{ $reservation->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                <option value="confirmed" {{ $reservation->status == 'confirmed' ? 'selected' : '' }}>Confirmée</option>
                                <option value="deposit_paid" {{ $reservation->status == 'deposit_paid' ? 'selected' : '' }}>Acompte payé</option>
                                <option value="fully_paid" {{ $reservation->status == 'fully_paid' ? 'selected' : '' }}>Payée</option>
                                <option value="completed" {{ $reservation->status == 'completed' ? 'selected' : '' }}>Terminée</option>
                                <option value="cancelled" {{ $reservation->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="check_in_date" class="form-label">Date d'arrivée <span class="text-danger">*</span></label>
                            <input type="text" name="check_in_date" id="check_in_date" class="form-control" value="{{ old('check_in_date', $reservation->check_in_date->format('Y-m-d')) }}" placeholder="Sélectionner…" autocomplete="off" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="check_out_date" class="form-label">Date de départ <span class="text-danger">*</span></label>
                            <input type="text" name="check_out_date" id="check_out_date" class="form-control" value="{{ old('check_out_date', $reservation->check_out_date->format('Y-m-d')) }}" placeholder="Sélectionner…" autocomplete="off" required>
                        </div>
                        <div class="col-md-6">
                            <label for="number_of_guests" class="form-label">Nombre de personnes <span class="text-danger">*</span></label>
                            <input type="number" name="number_of_guests" id="number_of_guests" class="form-control" value="{{ old('number_of_guests', $reservation->number_of_guests) }}" min="1" required>
                        </div>
                        <div class="col-12">
                            <p class="small text-lux-greyBlue mb-0" id="availability-hint">Chargement du calendrier…</p>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="total_price" class="form-label">Montant total (€) <span class="text-danger">*</span></label>
                            <input type="number" name="total_price" id="total_price" class="form-control" value="{{ $reservation->total_price }}" step="0.01" min="0" required>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow-sm p-4 mb-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations client</h3>
                    
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="guest_first_name" class="form-label">Prénom <span class="text-danger">*</span></label>
                            <input type="text" name="guest_first_name" id="guest_first_name" class="form-control" value="{{ $reservation->guest_first_name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guest_last_name" class="form-label">Nom <span class="text-danger">*</span></label>
                            <input type="text" name="guest_last_name" id="guest_last_name" class="form-control" value="{{ $reservation->guest_last_name }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label for="guest_email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="guest_email" id="guest_email" class="form-control" value="{{ $reservation->guest_email }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="guest_phone" class="form-label">Téléphone</label>
                            <input type="text" name="guest_phone" id="guest_phone" class="form-control" value="{{ $reservation->guest_phone }}">
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded shadow-sm p-4 border">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Notes</h3>
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Notes administrateur</label>
                        <textarea name="admin_notes" id="admin_notes" class="form-control" rows="4">{{ $reservation->admin_notes }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="bg-white rounded shadow-sm p-4 border sticky-top" style="top: 20px;">
                    <h3 class="h5 mb-4 text-lux-dark-blue">Informations</h3>
                    <div class="mb-3">
                        <label class="small text-lux-greyBlue">Villa</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->villa->name ?? 'N/A' }}</div>
                        @if($reservation->villa)
                            <p class="small text-lux-greyBlue mb-0 mt-1">
                                Min. {{ $reservation->villa->minimum_stay_nights ?? 3 }} nuit(s) ·
                                Max. {{ $reservation->villa->max_capacity }} personne(s)
                            </p>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="small text-lux-greyBlue">Numéro de réservation</label>
                        <div class="fw-medium text-lux-dark-blue">#{{ $reservation->reservation_number }}</div>
                    </div>
                    <div class="mb-4">
                        <label class="small text-lux-greyBlue">Créée le</label>
                        <div class="fw-medium text-lux-dark-blue">{{ $reservation->created_at->format('d/m/Y à H:i') }}</div>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-lux-primary">
                            <i class="fa-solid fa-save me-2"></i>Enregistrer les modifications
                        </button>
                        <a href="{{ route('admin.reservations.show', $reservation->id) }}" class="btn btn-outline-secondary">
                            Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('reservation-edit-form');
    const checkIn = document.getElementById('check_in_date');
    const checkOut = document.getElementById('check_out_date');
    const availabilityHint = document.getElementById('availability-hint');
    const villaId = @json($reservation->villa_id);
    const excludeReservationId = @json($reservation->id);
    const blockedDatesBaseUrl = @json(route('admin.villas.blocked-dates', ['id' => 0])).replace(/\/0\/blocked-dates$/, '/');

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
    let minStayNights = @json((int) ($reservation->villa->minimum_stay_nights ?? 3));
    let checkInPicker = null;
    let checkOutPicker = null;

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

    function updateAvailabilityHint() {
        const count = blockedDatesSet.size;
        if (count === 0) {
            availabilityHint.textContent = 'Aucune autre réservation ou blocage sur cette villa (hors séjour actuel).';
        } else {
            availabilityHint.textContent = count + ' jour' + (count > 1 ? 's' : '') + ' indisponible' + (count > 1 ? 's' : '') + ' pour les autres séjours. Les dates grisées ne sont pas sélectionnables.';
        }
        availabilityHint.classList.remove('text-danger');
    }

    function validateSelectedPeriod() {
        const inDate = checkIn.value;
        const outDate = checkOut.value;
        if (inDate && blockedDatesSet.has(inDate)) {
            availabilityHint.textContent = 'La date d\'arrivée sélectionnée n\'est pas disponible.';
            availabilityHint.classList.add('text-danger');
            return false;
        }
        if (outDate && blockedDatesSet.has(outDate)) {
            availabilityHint.textContent = 'La date de départ sélectionnée n\'est pas disponible.';
            availabilityHint.classList.add('text-danger');
            return false;
        }
        if (inDate && outDate && isPeriodBlocked(inDate, outDate)) {
            availabilityHint.textContent = 'Cette période chevauche des dates déjà réservées ou bloquées.';
            availabilityHint.classList.add('text-danger');
            return false;
        }
        updateAvailabilityHint();
        return true;
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
                if (selectedDates.length > 0 && checkOutPicker) {
                    const minOut = new Date(selectedDates[0].getTime() + minStayNights * 24 * 60 * 60 * 1000);
                    checkOutPicker.set('minDate', minOut);
                }
                validateSelectedPeriod();
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
            },
        });
    }

    function loadBlockedDates() {
        const url = blockedDatesBaseUrl + '/' + villaId + '/blocked-dates?exclude_reservation_id=' + excludeReservationId;
        return fetch(url, { headers: { Accept: 'application/json' } })
            .then(function (res) { return res.json(); })
            .then(function (data) {
                blockedDatesSet = new Set(data.blocked_dates || []);
                minStayNights = data.min_stay || minStayNights;
                initDatePickers();
                validateSelectedPeriod();
            })
            .catch(function () {
                availabilityHint.textContent = 'Impossible de charger les dates indisponibles.';
                availabilityHint.classList.add('text-danger');
                initDatePickers();
            });
    }

    form.addEventListener('submit', function (e) {
        if (!validateSelectedPeriod()) {
            e.preventDefault();
        }
    });

    loadBlockedDates();
});
</script>
@endpush

