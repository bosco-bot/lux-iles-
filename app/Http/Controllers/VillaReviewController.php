<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\VillaReview;
use App\Services\VillaReviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VillaReviewController extends Controller
{
    public function __construct(
        protected VillaReviewService $reviewService
    ) {}

    public function create(Reservation $reservation)
    {
        $user = Auth::user();
        $this->authorizeReservation($user->id, $reservation);

        $blockReason = $this->reviewService->reviewBlockReason($user, $reservation);
        if ($blockReason && ! $this->reviewService->canUserSubmitReview($user, $reservation)) {
            return redirect()
                ->route('espace-client.reservations')
                ->with('error', $blockReason);
        }

        $reservation->load(['villa.photos', 'villa.island']);

        return view('pages.review-create', [
            'reservation' => $reservation,
            'daysLeft' => $this->reviewService->daysLeftToReview($reservation),
        ]);
    }

    public function store(Request $request, Reservation $reservation)
    {
        $user = Auth::user();
        $this->authorizeReservation($user->id, $reservation);

        if (! $this->reviewService->canUserSubmitReview($user, $reservation)) {
            $reason = $this->reviewService->reviewBlockReason($user, $reservation);

            return redirect()
                ->route('espace-client.reservations')
                ->with('error', $reason ?? 'Vous ne pouvez pas déposer d\'avis pour ce séjour.');
        }

        $validated = $request->validate([
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'comment' => ['required', 'string', 'min:10', 'max:5000'],
        ]);

        $this->reviewService->createFromReservation(
            $user,
            $reservation,
            (int) $validated['rating'],
            $validated['comment']
        );

        return redirect()
            ->route('espace-client.reservations')
            ->with('success', 'Merci ! Votre avis a été envoyé et sera publié après validation par notre équipe.');
    }

    protected function authorizeReservation(int $userId, Reservation $reservation): void
    {
        abort_unless($reservation->user_id === $userId, 403);
    }
}
