<?php

namespace App\Console\Commands;

use App\Models\Payment;
use App\Models\Reservation;
use App\Services\EmailService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpireUnpaidPendingReservations extends Command
{
    protected $signature = 'reservations:expire-unpaid-pending';

    protected $description = 'Annule les réservations en ligne sans acompte payé après le délai configuré (24 h par défaut)';

    public function __construct(
        private readonly EmailService $emailService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $now = Carbon::now();
        $cancelledCount = 0;

        Reservation::query()
            ->where('status', 'pending')
            ->where('source', 'direct')
            ->whereNotNull('payment_expires_at')
            ->where('payment_expires_at', '<=', $now)
            ->with(['user', 'villa', 'payments'])
            ->orderBy('id')
            ->each(function (Reservation $reservation) use (&$cancelledCount): void {
                try {
                    DB::transaction(function () use ($reservation): void {
                        $reservation->update([
                            'status' => 'cancelled',
                            'cancellation_reason' => 'Annulation automatique : acompte non payé dans le délai de 24 h.',
                            'cancelled_at' => now(),
                            'cancelled_by' => null,
                        ]);

                        $reservation->payments()
                            ->whereIn('type', ['deposit', 'balance', 'deposit_guarantee'])
                            ->whereIn('status', ['pending', 'processing'])
                            ->each(function (Payment $payment): void {
                                $payment->update([
                                    'status' => 'cancelled',
                                    'paid_at' => null,
                                ]);
                            });
                    });

                    $reservation->loadMissing(['user', 'villa']);

                    if ($reservation->user) {
                        $this->emailService->sendCancellationEmail($reservation);
                    }

                    $cancelledCount++;
                    $this->line("  ✓ Réservation {$reservation->reservation_number} annulée (délai acompte dépassé)");
                } catch (\Throwable $e) {
                    $this->error("  ✗ Erreur réservation {$reservation->id}: {$e->getMessage()}");
                    Log::error('Erreur expiration réservation pending', [
                        'reservation_id' => $reservation->id,
                        'message' => $e->getMessage(),
                    ]);
                }
            });

        $this->info("{$cancelledCount} réservation(s) expirée(s).");

        return self::SUCCESS;
    }
}
