@extends('layouts.admin')

@section('title', 'Calendrier Global | LUXÎLES Admin')

@section('admin-breadcrumbs')
    <span class="text-white">Calendrier Global</span>
@endsection

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2 font-serif mb-0" style="font-family: 'Playfair Display', serif; color: var(--lux-dark-blue);">
            <i class="fa-solid fa-globe text-lux-gold me-2"></i>Calendrier Global
        </h1>
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('admin.calendar') }}" class="btn bg-lux-blue text-white px-4 py-2 rounded small" style="transition: all 0.3s;" onmouseover="this.style.backgroundColor='rgba(10, 26, 47, 0.9)'" onmouseout="this.style.backgroundColor='var(--lux-dark-blue)'">
                <i class="fa-regular fa-calendar-days me-2"></i>Vue par Villa
            </a>
        </div>
    </div>

    <!-- Filtres -->
    <div class="bg-white rounded shadow-sm border mb-4 p-3" style="border-color: rgba(138, 150, 166, 0.1) !important;">
        <div class="row align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-uppercase fw-medium text-lux-greyBlue mb-2" style="font-size: 0.7rem; letter-spacing: 0.05em;">Filtrer par Île</label>
                <select id="island-select" class="form-select" style="border-color: rgba(138, 150, 166, 0.3);">
                    <option value="">Toutes les îles</option>
                    @foreach($islands as $island)
                        <option value="{{ $island->id }}" {{ $islandId == $island->id ? 'selected' : '' }}>
                            {{ $island->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-3 justify-content-end flex-wrap">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #3b82f6;"></div>
                        <span class="small text-lux-greyBlue">Confirmée</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #f59e0b;"></div>
                        <span class="small text-lux-greyBlue">Arrhes payées</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #94a3b8;"></div>
                        <span class="small text-lux-greyBlue">En attente</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #10b981;"></div>
                        <span class="small text-lux-greyBlue">Payée</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded" style="width: 16px; height: 16px; background-color: #6b7280;"></div>
                        <span class="small text-lux-greyBlue">Terminée</span>
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
        <div id="calendar-loading" class="text-center py-4" style="display: none;">
            <div class="spinner-border text-lux-gold" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="small text-lux-greyBlue mt-2">Chargement des événements...</p>
        </div>
        <div id="calendar-container"></div>
        <div id="calendar-empty" class="text-center py-5" style="display: none;">
            <i class="fa-regular fa-calendar-xmark text-lux-greyBlue mb-3" style="font-size: 3rem;"></i>
            <p class="text-lux-greyBlue mb-2">Aucun événement trouvé pour cette période</p>
            <p class="small text-lux-greyBlue">Essayez de naviguer vers un autre mois ou vérifiez les filtres</p>
        </div>
    </div>

    <!-- Modal Détails -->
    <div class="modal fade" id="eventModal" tabindex="-1" aria-labelledby="eventModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title font-serif text-lux-dark-blue" id="eventModalLabel" style="font-family: 'Playfair Display', serif;">Détails</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="eventModalBody">
                    <!-- Contenu chargé dynamiquement -->
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                    <a href="#" id="eventModalLink" class="btn bg-lux-blue text-white" style="display: none;">Voir les détails</a>
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
    const islandSelect = document.getElementById('island-select');

    // Vérifier que l'élément existe
    if (!calendarEl) {
        console.error('❌ Élément calendar-container non trouvé!');
        return;
    }

    console.log('✅ Initialisation du calendrier...');

    // Initialiser le calendrier
    calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'fr',
        initialView: 'dayGridMonth',
        initialDate: new Date(), // S'assurer qu'on commence sur le mois actuel
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
        firstDay: 1, // Lundi comme premier jour
        events: function(fetchInfo, successCallback, failureCallback) {
            const loadingEl = document.getElementById('calendar-loading');
            const emptyEl = document.getElementById('calendar-empty');
            
            if (loadingEl) loadingEl.style.display = 'block';
            if (emptyEl) emptyEl.style.display = 'none';
            
            const islandId = islandSelect.value || '';
            // Extraire seulement la date (sans l'heure) pour éviter l'erreur 422
            const startDate = fetchInfo.startStr.split('T')[0];
            const endDate = fetchInfo.endStr.split('T')[0];
            const url = `{{ route('admin.calendar.events.global') }}?start=${startDate}&end=${endDate}`;
            const finalUrl = islandId ? `${url}&island_id=${islandId}` : url;

            console.log('Chargement des événements pour la période:', fetchInfo.startStr, '→', fetchInfo.endStr);
            console.log('URL:', finalUrl);

            fetch(finalUrl, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
                .then(response => {
                    console.log('Réponse reçue, statut:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Événements reçus:', data.length, 'événement(s)');
                    if (data.length > 0) {
                        console.log('Premier événement:', data[0]);
                        console.log('Tous les événements:', data);
                    } else {
                        console.warn('⚠ Aucun événement trouvé pour cette période.');
                        if (emptyEl) emptyEl.style.display = 'block';
                    }
                    if (loadingEl) loadingEl.style.display = 'none';
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Erreur lors du chargement des événements:', error);
                    if (loadingEl) loadingEl.style.display = 'none';
                    if (emptyEl) {
                        emptyEl.innerHTML = `
                            <i class="fa-solid fa-exclamation-triangle text-warning mb-3" style="font-size: 3rem;"></i>
                            <p class="text-lux-greyBlue mb-2">Erreur lors du chargement</p>
                            <p class="small text-lux-greyBlue">${error.message}</p>
                        `;
                        emptyEl.style.display = 'block';
                    }
                    failureCallback(error);
                });
        },
        eventClick: function(info) {
            const props = info.event.extendedProps;
            const modal = new bootstrap.Modal(document.getElementById('eventModal'));
            const modalBody = document.getElementById('eventModalBody');
            const modalLink = document.getElementById('eventModalLink');
            
            if (props.type === 'reservation') {
                modalBody.innerHTML = `
                    <div class="mb-3">
                        <strong>Villa:</strong> ${props.villa_name}
                    </div>
                    <div class="mb-3">
                        <strong>Numéro:</strong> #${info.event.title.split(' - ')[1]?.replace('Réservation ', '') || 'N/A'}
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
                modalLink.style.display = 'inline-block';
            } else if (props.type === 'block') {
                modalBody.innerHTML = `
                    <div class="mb-3">
                        <strong>Villa:</strong> ${props.villa_name}
                    </div>
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
            }
            
            modal.show();
        },
        eventDidMount: function(info) {
            info.el.setAttribute('title', info.event.title);
        }
    });

    try {
        calendar.render();
        console.log('✅ Calendrier rendu avec succès');
        console.log('Élément calendrier:', calendarEl);
        
        // Vérifier que le calendrier est bien rendu
        setTimeout(() => {
            const calendarView = calendar.view;
            console.log('Vue actuelle:', calendarView.type, calendarView.title);
            console.log('Date de début:', calendarView.activeStart);
            console.log('Date de fin:', calendarView.activeEnd);
            
            // Vérifier les événements chargés
            const events = calendar.getEvents();
            console.log('Événements dans le calendrier:', events.length);
            if (events.length > 0) {
                console.log('Premier événement chargé:', events[0].title, events[0].start);
            } else {
                console.warn('⚠ Aucun événement affiché dans le calendrier');
            }
        }, 1000);
    } catch (error) {
        console.error('❌ Erreur lors du rendu du calendrier:', error);
    }

    // Changer d'île
    islandSelect.addEventListener('change', function() {
        calendar.refetchEvents();
    });

    // Fonctions utilitaires
    function getStatusColor(status) {
        const colors = {
            'confirmed': 'primary',
            'deposit_paid': 'warning',
            'pending': 'secondary', // ou une classe CSS spécifique si définie, ici 'secondary' pour gris par défaut bootstrap ou custom
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
            'pending': 'En attente',
            'fully_paid': 'Payée',
            'completed': 'Terminée',
            'cancelled': 'Annulée'
        };
        return labels[status] || status;
    }
});
</script>
@endpush

