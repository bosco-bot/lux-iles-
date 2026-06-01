@extends('layouts.admin')

@section('title', 'Calendrier | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Calendrier</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
            <i class="fa-regular fa-calendar-days text-lux-gold me-2"></i>Calendrier des Réservations
        </h1>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.calendar.global') }}" class="btn bg-lux-blue text-white px-4 py-2 rounded small" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">
                <i class="fa-solid fa-globe me-2"></i>Vue Globale
            </a>
        </div>
    </div>

    <!-- Filtre Villa -->
    <div class="bg-white rounded shadow-sm border mb-4 p-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Sélectionner une Villa</label>
                <select id="villa-select" class="form-select" style="border-color: rgba(138, 150, 166, 0.3);">
                    <option value="">Toutes les villas</option>
                    @foreach($villas as $villa)
                        <option value="{{ $villa->id }}" {{ $selectedVillaId == $villa->id ? 'selected' : '' }}>
                            {{ $villa->name }} - {{ $villa->island->name ?? 'N/A' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8 text-end">
                <div class="d-flex align-items-center gap-3 justify-content-end">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #3b82f6;"></div>
                        <span class="small text-lux-greyBlue">Confirmée</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #f59e0b;"></div>
                        <span class="small text-lux-greyBlue">Arrhes payées</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #10b981;"></div>
                        <span class="small text-lux-greyBlue">Payée</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #94a3b8;"></div>
                        <span class="small text-lux-greyBlue">En attente</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #ef4444;"></div>
                        <span class="small text-lux-greyBlue">Bloquée</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendrier -->
    <div class="bg-white rounded shadow-sm border p-4" style="border-color: rgba(138, 150, 166, 0.1) !important;">
        <div id="calendar-container"></div>
    </div>

    <!-- Modal Détails Réservation -->
    <div class="modal fade" id="reservationModal" tabindex="-1" aria-labelledby="reservationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="reservationModalLabel" style="font-family: 'Playfair Display', serif;">Détails de la Réservation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="reservationModalBody">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <a href="#" id="reservationModalLink" class="btn bg-lux-blue text-white">Voir les détails</a>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' crossorigin="anonymous" />
<style>
    #calendar-container {
        min-height: 600px;
    }
    #calendar-container .fc {
        font-family: 'Montserrat', sans-serif;
    }
    #calendar-container .fc-theme-standard td,
    #calendar-container .fc-theme-standard th {
        border-color: rgba(138, 150, 166, 0.2);
    }
    #calendar-container .fc-daygrid-day {
        background-color: #ffffff;
    }
    #calendar-container .fc-daygrid-day:hover {
        background-color: rgba(203, 174, 130, 0.05);
    }
    #calendar-container .fc-col-header-cell-cushion {
        color: var(--lux-dark-blue);
        font-weight: 600;
    }
    #calendar-container .fc-daygrid-day-number {
        color: var(--lux-dark-blue);
        padding: 8px;
    }
    #calendar-container .fc-button {
        background-color: var(--lux-dark-blue);
        border-color: var(--lux-dark-blue);
        color: white;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
    }
    #calendar-container .fc-button:hover {
        background-color: rgba(10, 26, 47, 0.9);
        border-color: rgba(10, 26, 47, 0.9);
    }
    #calendar-container .fc-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(10, 26, 47, 0.25);
    }
    #calendar-container .fc-button:disabled {
        opacity: 0.5;
    }
    #calendar-container .fc-event {
        cursor: pointer;
        border-radius: 4px;
        padding: 2px 6px;
        font-size: 0.75rem;
    }
    #calendar-container .fc-event:hover {
        opacity: 0.9;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js' crossorigin="anonymous"></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/locales/fr.global.min.js' crossorigin="anonymous"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    let calendar;
    const calendarEl = document.getElementById('calendar-container');
    const villaSelect = document.getElementById('villa-select');

    // Initialiser le calendrier
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listWeek'
        },
        buttonText: {
            today: 'Aujourd\'hui',
            month: 'Mois',
            week: 'Semaine',
            day: 'Jour',
            list: 'Liste'
        },
        height: 'auto',
        events: function(fetchInfo, successCallback, failureCallback) {
            const villaId = villaSelect.value;
            if (!villaId) {
                successCallback([]);
                return;
            }

            // Extraire seulement la date (sans l'heure) pour éviter l'erreur 422
            const startDate = fetchInfo.startStr.split('T')[0];
            const endDate = fetchInfo.endStr.split('T')[0];
            fetch(`{{ route('admin.calendar.events') }}?villa_id=${villaId}&start=${startDate}&end=${endDate}`)
                .then(response => response.json())
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des événements:', error);
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            
            if (props.type === 'reservation') {
                // Charger les détails de la réservation
                const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
                const modalBody = document.getElementById('reservationModalBody');
                const modalLink = document.getElementById('reservationModalLink');
                
                modalBody.innerHTML = `
                    <div class="mb-3">
                        <strong>Numéro:</strong> #${info.event.title.replace('Réservation ', '')}
                    </div>
                    <div class="mb-3">
                        <strong>Client:</strong> ${props.guest_name}
                    </div>
                    <div class="mb-3">
                        <strong>Période:</strong> ${info.event.start.toLocaleDateString('fr-FR')} - ${new Date(info.event.end.getTime() - 86400000).toLocaleDateString('fr-FR')}
                    </div>
                    <div class="mb-3">
                        <strong>Voyageurs:</strong> ${props.number_of_guests} personne(s)
                    </div>
                    <div class="mb-3">
                        <strong>Statut:</strong> <span class="badge bg-${getStatusColor(props.status)}">${getStatusLabel(props.status)}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Montant total:</strong> ${parseFloat(props.total_price).toLocaleString('fr-FR', {style: 'currency', currency: 'EUR'})}
                    </div>
                `;
                
                modalLink.href = `/admin/reservations/${props.reservation_id}`;
                modal.show();
            } else if (props.type === 'block') {
                // Afficher les détails du blocage
                const modal = new bootstrap.Modal(document.getElementById('reservationModal'));
                const modalBody = document.getElementById('reservationModalBody');
                const modalLink = document.getElementById('reservationModalLink');
                
                modalBody.innerHTML = `
                    <div class="mb-3">
                        <strong>Type:</strong> Période bloquée
                    </div>
                    <div class="mb-3">
                        <strong>Période:</strong> ${info.event.start.toLocaleDateString('fr-FR')} - ${new Date(info.event.end.getTime() - 86400000).toLocaleDateString('fr-FR')}
                    </div>
                    <div class="mb-3">
                        <strong>Raison:</strong> ${props.reason || 'Non spécifiée'}
                    </div>
                `;
                
                modalLink.style.display = 'none';
                modal.show();
            }
        },
        eventDidMount: function(info) {
            // Ajouter un tooltip
            info.el.setAttribute('title', info.event.title);
        }
    });

    calendar.render();

    // Changer de villa
    villaSelect.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Fonctions utilitaires
    function getStatusColor(status) {
        const colors = {
            'confirmed': 'primary',
            'deposit_paid': 'warning',
            'fully_paid': 'success',
            'completed': 'secondary',
            'cancelled': 'danger'
        };
        return colors[status] || 'secondary';
    }

    function getStatusLabel(status) {
        const labels = {
            'confirmed': 'Confirmée',
            'deposit_paid': 'Arrhes payées',
            'fully_paid': 'Payée',
            'completed': 'Terminée',
            'cancelled': 'Annulée'
        };
        return labels[status] || status;
    }
});
</script>
@endpush

