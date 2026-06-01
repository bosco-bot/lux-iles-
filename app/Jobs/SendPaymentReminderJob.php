<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Services\EmailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendPaymentReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $payment;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * Execute the job.
     */
    public function handle(EmailService $emailService): void
    {
        try {
            $emailService->sendPaymentReminder($this->payment);
            Log::info("Email de rappel de paiement envoyé pour le paiement {$this->payment->payment_number}");
        } catch (\Exception $e) {
            Log::error("Erreur lors de l'envoi de l'email de rappel de paiement: " . $e->getMessage());
            throw $e;
        }
    }
}
