<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Villa;
use App\Models\Reservation;
use App\Models\VillaAvailabilityBlock;
use App\Models\Island;
use Illuminate\Http\Request;
use Carbon\Carbon;

class CalendarController extends Controller
{
    /**
     * Afficher la page calendrier
     */
    public function index(Request $request)
    {
        // Récupérer toutes les villas actives
        $villas = Villa::where('is_active', true)
            ->with('island')
            ->orderBy('name')
            ->get();

        // Villa sélectionnée (par défaut, la première)
        $selectedVillaId = $request->get('villa_id', $villas->first()?->id);

        // Récupérer les réservations et blocages pour la villa sélectionnée
        $events = $this->getEventsForVilla($selectedVillaId);

        return view('pages.admin.calendar', compact('villas', 'selectedVillaId', 'events'));
    }

    /**
     * API: Récupérer les événements (réservations + blocages) pour une villa
     */
    public function getEvents(Request $request)
    {
        // Extraire seulement la date (sans l'heure) des paramètres
        $start = $request->get('start');
        $end = $request->get('end');
        
        // Si les dates contiennent un 'T', extraire seulement la partie date
        if ($start && strpos($start, 'T') !== false) {
            $start = substr($start, 0, strpos($start, 'T'));
        }
        if ($end && strpos($end, 'T') !== false) {
            $end = substr($end, 0, strpos($end, 'T'));
        }
        
        $request->merge(['start' => $start, 'end' => $end]);
        
        $request->validate([
            'villa_id' => 'nullable|exists:villas,id',
            'start' => 'required|date',
            'end' => 'required|date',
        ]);

        $villaId = $request->get('villa_id');
        $events = $this->getEventsForVilla($villaId, $start, $end);

        return response()->json($events);
    }

    /**
     * Récupérer les événements pour une villa (réservations + blocages)
     */
    private function getEventsForVilla(?int $villaId, ?string $start = null, ?string $end = null): array
    {
        $events = [];

        if (!$villaId) {
            return $events;
        }

        // Récupérer les réservations
        $reservationsQuery = Reservation::where('villa_id', $villaId)
            ->whereIn('status', ['confirmed', 'deposit_paid', 'fully_paid', 'completed', 'pending']);

        if ($start && $end) {
            $reservationsQuery->where(function($q) use ($start, $end) {
                $q->whereBetween('check_in_date', [$start, $end])
                    ->orWhereBetween('check_out_date', [$start, $end])
                    ->orWhere(function($q2) use ($start, $end) {
                        $q2->where('check_in_date', '<=', $start)
                            ->where('check_out_date', '>=', $end);
                    });
            });
        }

        $reservations = $reservationsQuery->get();

        foreach ($reservations as $reservation) {
            $color = match($reservation->status) {
                'confirmed' => '#3b82f6', // Bleu
                'deposit_paid' => '#f59e0b', // Orange
                'fully_paid' => '#10b981', // Vert
                'completed' => '#6b7280', // Gris
                'pending' => '#94a3b8', // Gris-bleu ardoise pour "En attente"
                default => '#ef4444', // Rouge
            };

            $events[] = [
                'id' => 'reservation-' . $reservation->id,
                'title' => 'Réservation #' . $reservation->reservation_number,
                'start' => $reservation->check_in_date->format('Y-m-d'),
                'end' => $reservation->check_out_date->copy()->addDay()->format('Y-m-d'), // FullCalendar exclut le dernier jour
                'color' => $color,
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'reservation',
                    'reservation_id' => $reservation->id,
                    'guest_name' => $reservation->guest_first_name . ' ' . $reservation->guest_last_name,
                    'status' => $reservation->status,
                    'number_of_guests' => $reservation->number_of_guests,
                    'total_price' => $reservation->total_price,
                ],
            ];
        }

        // Récupérer les périodes bloquées
        $blocksQuery = VillaAvailabilityBlock::where('villa_id', $villaId);

        if ($start && $end) {
            $blocksQuery->where(function($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function($q2) use ($start, $end) {
                        $q2->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            });
        }

        $blocks = $blocksQuery->get();

        foreach ($blocks as $block) {
            $events[] = [
                'id' => 'block-' . $block->id,
                'title' => $block->reason ?? 'Période bloquée',
                'start' => $block->start_date->format('Y-m-d'),
                'end' => $block->end_date->copy()->addDay()->format('Y-m-d'),
                'color' => '#ef4444', // Rouge
                'textColor' => '#ffffff',
                'extendedProps' => [
                    'type' => 'block',
                    'block_id' => $block->id,
                    'reason' => $block->reason,
                ],
            ];
        }

        return $events;
    }

    /**
     * Vue calendrier globale (toutes les villas)
     */
    public function global(Request $request)
    {
        // Récupérer les îles pour le filtre
        $islands = Island::orderBy('name')->get();

        // Récupérer toutes les villas actives
        $villasQuery = Villa::where('is_active', true)
            ->with('island');

        // Filtrer par île si demandé
        $islandId = $request->get('island_id');
        if ($islandId) {
            $villasQuery->where('island_id', $islandId);
        }

        $villas = $villasQuery->orderBy('name')->get();

        return view('pages.admin.calendar-global', compact('villas', 'islands', 'islandId'));
    }

    /**
     * API: Récupérer les événements pour toutes les villas (vue globale)
     */
    public function getGlobalEvents(Request $request)
    {
        // Extraire seulement la date (sans l'heure) des paramètres
        $start = $request->get('start');
        $end = $request->get('end');
        
        // Si les dates contiennent un 'T', extraire seulement la partie date
        if ($start && strpos($start, 'T') !== false) {
            $start = substr($start, 0, strpos($start, 'T'));
        }
        if ($end && strpos($end, 'T') !== false) {
            $end = substr($end, 0, strpos($end, 'T'));
        }
        
        $request->merge(['start' => $start, 'end' => $end]);
        
        $request->validate([
            'start' => 'required|date',
            'end' => 'required|date',
            'island_id' => 'nullable|exists:islands,id',
        ]);

        $events = [];
        $villas = Villa::where('is_active', true);

        if ($request->island_id) {
            $villas->where('island_id', $request->island_id);
        }

        $villas = $villas->with('island')->get();

        foreach ($villas as $villa) {
            $villaEvents = $this->getEventsForVilla($villa->id, $request->start, $request->end);
            
            // Ajouter le nom de la villa aux événements
            foreach ($villaEvents as &$event) {
                $event['extendedProps']['villa_name'] = $villa->name;
                $event['extendedProps']['villa_id'] = $villa->id;
                $event['title'] = $villa->name . ' - ' . $event['title'];
            }

            $events = array_merge($events, $villaEvents);
        }

        return response()->json($events);
    }
}
