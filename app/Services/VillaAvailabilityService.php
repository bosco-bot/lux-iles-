<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\VillaAvailabilityBlock;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class VillaAvailabilityService
{
    public const BLOCKING_STATUSES = [
        'pending',
        'confirmed',
        'deposit_paid',
        'fully_paid',
        'completed',
    ];

    public function hasConflict(
        int $villaId,
        CarbonInterface $checkIn,
        CarbonInterface $checkOut,
        ?int $excludeReservationId = null,
        ?VillaAvailabilityContext $context = null,
    ): bool {
        $context ??= VillaAvailabilityContext::admin();

        if ($this->reservationsQuery($villaId, $context, $excludeReservationId)
            ->where('check_in_date', '<', $checkOut->toDateString())
            ->where('check_out_date', '>', $checkIn->toDateString())
            ->exists()
        ) {
            return true;
        }

        return VillaAvailabilityBlock::where('villa_id', $villaId)
            ->where('start_date', '<', $checkOut->toDateString())
            ->where('end_date', '>', $checkIn->toDateString())
            ->exists();
    }

    /**
     * Jours indisponibles (réservations + blocages calendrier).
     *
     * @return list<string> Dates au format Y-m-d
     */
    public function getBlockedDates(
        int $villaId,
        ?int $excludeReservationId = null,
        ?VillaAvailabilityContext $context = null,
    ): array {
        $context ??= VillaAvailabilityContext::admin();
        $dates = [];

        foreach ($this->reservationsQuery($villaId, $context, $excludeReservationId)
            ->get(['check_in_date', 'check_out_date']) as $reservation) {
            $dates = array_merge(
                $dates,
                $this->expandOccupiedRange($reservation->check_in_date, $reservation->check_out_date)
            );
        }

        foreach (VillaAvailabilityBlock::where('villa_id', $villaId)->get(['start_date', 'end_date']) as $block) {
            $dates = array_merge(
                $dates,
                $this->expandOccupiedRange($block->start_date, $block->end_date)
            );
        }

        $dates = array_values(array_unique($dates));
        sort($dates);

        return $dates;
    }

    /**
     * Réservations affichées sur le calendrier FullCalendar (fiche villa).
     */
    public function getReservationsForCalendar(
        int $villaId,
        ?VillaAvailabilityContext $context = null,
    ): Collection {
        $context ??= VillaAvailabilityContext::publicSite();

        return $this->reservationsQuery($villaId, $context)
            ->orderBy('check_in_date')
            ->get(['id', 'check_in_date', 'check_out_date', 'status']);
    }

    private function reservationsQuery(
        int $villaId,
        VillaAvailabilityContext $context,
        ?int $excludeReservationId = null,
    ): Builder {
        $query = Reservation::where('villa_id', $villaId)
            ->whereIn('status', $context->blockingStatuses);

        if ($context->onlyFutureReservations) {
            $query->where('check_out_date', '>=', Carbon::today()->toDateString());
        }

        if ($excludeReservationId !== null) {
            $query->where('id', '!=', $excludeReservationId);
        }

        return $query;
    }

    /**
     * @param  CarbonInterface|string  $startDate
     * @param  CarbonInterface|string  $endDate
     * @return list<string>
     */
    private function expandOccupiedRange($startDate, $endDate): array
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->startOfDay()->addDay();
        $dates = [];

        while ($start->lt($end)) {
            $dates[] = $start->format('Y-m-d');
            $start->addDay();
        }

        return $dates;
    }
}
