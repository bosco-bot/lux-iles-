<?php

namespace App\Jobs;

use App\Models\Reservation;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendReservationConfirmationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        try {
            $emailService->sendReservationConfirmation($this->reservation);
            Log::info("Email de confirmation de réservation envoyé pour la réservation {$this->reservation->reservation_number}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email de confirmation de réservation: " . $e->getMessage());
            throw $e;
        }
    }
}
