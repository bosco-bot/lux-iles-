<?php

namespace App\Observers;

use App\Models\Payment;
use App\Models\Document;
use Illuminate\Support\Facades\Log;

class PaymentObserver
{
    /**
     * Handle the Payment "updated" event.
     * Marque automatiquement le contrat comme signé après le paiement de l'acompte
     */
    public function updated(Payment $payment): void
    {
        // Si le paiement vient d'être complété et c'est un acompte
        if ($payment->status === 'completed' && $payment->type === 'deposit') {
            // Vérifier si le statut était différent avant (pour éviter les doublons)
            if ($payment->getOriginal('status') !== 'completed') {
                $this->signContractAfterDeposit($payment);
            }
        }
    }

    /**
     * Marquer le contrat comme signé après paiement de l'acompte
     */
    private function signContractAfterDeposit(Payment $payment): void
    {
        try {
            $reservation = $payment->reservation;
            
            if (!$reservation) {
                return;
            }

            // Trouver le contrat de la réservation
            $contract = Document::where('reservation_id', $reservation->id)
                ->where('type', 'contract')
                ->where('is_signed', false)
                ->first();

            if ($contract) {
                // Marquer le contrat comme signé
                $contract->update([
                    'is_signed' => true,
                    'signed_at' => now(),
                    'signed_by' => $reservation->user_id, // Le client qui a payé
                ]);

                Log::info("Contrat automatiquement signé après paiement de l'acompte", [
                    'reservation_id' => $reservation->id,
                    'document_id' => $contract->id,
                    'payment_id' => $payment->id,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la signature automatique du contrat", [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}



