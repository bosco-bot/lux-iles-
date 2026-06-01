<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\User;
use App\Models\VillaReview;
use Carbon\Carbon;

class VillaReviewService
{
    public const REVIEW_WINDOW_DAYS = 30;

    /** @var list<string> */
    public const ELIGIBLE_RESERVATION_STATUSES = [
        'confirmed',
        'deposit_paid',
        'fully_paid',
        'completed',
    ];

    public function canUserSubmitReview(User $user, Reservation $reservation): bool
    {
        if ($reservation->user_id !== $user->id) {
            return false;
        }

        if (! in_array($reservation->status, self::ELIGIBLE_RESERVATION_STATUSES, true)) {
            return false;
        }

        if ($reservation->check_out_date->isFuture()) {
            return false;
        }

        if (now()->gt($this->reviewDeadline($reservation))) {
            return false;
        }

        return ! $reservation->review()->exists();
    }

    public function reviewDeadline(Reservation $reservation): Carbon
    {
        return $reservation->check_out_date->copy()->endOfDay()->addDays(self::REVIEW_WINDOW_DAYS);
    }

    public function daysLeftToReview(Reservation $reservation): int
    {
        return max(0, (int) now()->diffInDays($this->reviewDeadline($reservation), false));
    }

    public function reviewBlockReason(User $user, Reservation $reservation): ?string
    {
        if ($reservation->user_id !== $user->id) {
            return 'Cette réservation ne vous appartient pas.';
        }

        if (! in_array($reservation->status, self::ELIGIBLE_RESERVATION_STATUSES, true)) {
            return 'Seules les réservations confirmées et terminées donnent droit à un avis.';
        }

        if ($reservation->check_out_date->isFuture()) {
            return 'Vous pourrez déposer un avis après votre départ.';
        }

        $review = $reservation->review;

        if ($review) {
            return match ($review->status) {
                VillaReview::STATUS_PENDING => 'Votre avis est en cours de modération par notre équipe.',
                VillaReview::STATUS_PUBLISHED => 'Vous avez déjà déposé un avis pour ce séjour.',
                VillaReview::STATUS_REJECTED => 'Votre avis précédent n\'a pas été publié.',
                default => 'Un avis existe déjà pour ce séjour.',
            };
        }

        if (now()->gt($this->reviewDeadline($reservation))) {
            return 'Le délai de 30 jours après votre départ est expiré.';
        }

        return null;
    }
}
