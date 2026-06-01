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

class SendArrivalReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reservation;
    protected $daysBefore;

    /**
     * Create a new job instance.
     */
    public function __construct(Reservation $reservation, int $daysBefore = 7)
    {
        $this->reservation = $reservation;
        $this->daysBefore = $daysBefore;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        try {
            $emailService->sendArrivalReminder($this->reservation, $this->daysBefore);
            Log::info("Email de rappel d'arrivée envoyé pour la réservation {$this->reservation->reservation_number} ({$this->daysBefore} jours avant)");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email de rappel d'arrivée: " . $e->getMessage());
            throw $e;
        }
    }
}
