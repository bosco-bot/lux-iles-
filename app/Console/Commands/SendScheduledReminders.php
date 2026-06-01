<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Reservation;
use App\Models\Payment;
use App\Jobs\SendPaymentReminderJob;
use App\Jobs\SendArrivalReminderJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class SendScheduledReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer les rappels automatiques (paiements et arrivées)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Envoi des rappels automatiques...');
        
        $sentCount = 0;
        
        // Rappels de paiement (paiements en attente avec date d'échéance dans 3 jours ou dépassée)
        $this->info('Vérification des rappels de paiement...');
        $paymentsDue = Payment::where('status', 'pending')
            ->whereNotNull('due_date')
            ->where(function($query) {
                $query->where('due_date', '<=', Carbon::now()->addDays(3)->toDateString())
                      ->where('due_date', '>=', Carbon::now()->subDays(1)->toDateString());
            })
            ->with('reservation.user')
            ->get();
        
        foreach ($paymentsDue as $payment) {
            try {
                SendPaymentReminderJob::dispatch($payment);
                $sentCount++;
                $this->line("  ✓ Rappel de paiement envoyé pour {$payment->payment_number}");
            } catch (\Exception $e) {
                $this->error("  ✗ Erreur pour le paiement {$payment->payment_number}: " . $e->getMessage());
                Log::error("Erreur rappel paiement {$payment->id}: " . $e->getMessage());
            }
        }
        
        // Rappels d'arrivée (7 jours avant)
        $this->info('Vérification des rappels d\'arrivée (7 jours)...');
        $arrivals7Days = Reservation::where('status', 'confirmed')
            ->where('check_in_date', Carbon::now()->addDays(7)->toDateString())
            ->with('user', 'villa')
            ->get();
        
        foreach ($arrivals7Days as $reservation) {
            try {
                SendArrivalReminderJob::dispatch($reservation, 7);
                $sentCount++;
                $this->line("  ✓ Rappel d'arrivée (7j) envoyé pour {$reservation->reservation_number}");
            } catch (\Exception $e) {
                $this->error("  ✗ Erreur pour la réservation {$reservation->reservation_number}: " . $e->getMessage());
                Log::error("Erreur rappel arrivée {$reservation->id}: " . $e->getMessage());
            }
        }
        
        // Rappels d'arrivée (1 jour avant)
        $this->info('Vérification des rappels d\'arrivée (1 jour)...');
        $arrivals1Day = Reservation::where('status', 'confirmed')
            ->where('check_in_date', Carbon::now()->addDay()->toDateString())
            ->with('user', 'villa')
            ->get();
        
        foreach ($arrivals1Day as $reservation) {
            try {
                SendArrivalReminderJob::dispatch($reservation, 1);
                $sentCount++;
                $this->line("  ✓ Rappel d'arrivée (1j) envoyé pour {$reservation->reservation_number}");
            } catch (\Exception $e) {
                $this->error("  ✗ Erreur pour la réservation {$reservation->reservation_number}: " . $e->getMessage());
                Log::error("Erreur rappel arrivée {$reservation->id}: " . $e->getMessage());
            }
        }
        
        $this->newLine();
        $this->info("✅ {$sentCount} rappel(s) envoyé(s) avec succès.");
        
        return Command::SUCCESS;
    }
}
