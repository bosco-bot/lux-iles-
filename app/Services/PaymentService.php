<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Reservation;
use App\Helpers\SettingsHelper;
use Illuminate\Support\Facades\Log;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;
use Stripe\Refund;

class PaymentService
{
    protected $emailService;

    public function __construct(EmailService $emailService)
    {
        $this->emailService = $emailService;

        // Initialiser Stripe avec la clé secrète
        $secretKey = SettingsHelper::get('stripe_secret_key');
        if ($secretKey) {
            Stripe::setApiKey($secretKey);
        }
    }

    /**
     * Créer un PaymentIntent Stripe pour un paiement
     *
     * @param Payment $payment
     * @param array $metadata Données supplémentaires à inclure dans le PaymentIntent
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function createPaymentIntent(Payment $payment, array $metadata = []): PaymentIntent
    {
        $reservation = $payment->reservation;
        $villa = $reservation->villa;

        // Préparer les métadonnées
        $paymentMetadata = array_merge([
            'payment_id' => $payment->id,
            'payment_number' => $payment->payment_number,
            'reservation_id' => $reservation->id,
            'reservation_number' => $reservation->reservation_number,
            'villa_id' => $villa->id,
            'payment_type' => $payment->type,
        ], $metadata);

        // Description du paiement
        $description = sprintf(
            'Paiement %s - Réservation %s - %s',
            $payment->type_label,
            $reservation->reservation_number,
            $villa->name
        );

        try {
            // Vérifier si un PaymentIntent existe déjà et est réutilisable
            if ($payment->stripe_payment_intent_id) {
                try {
                    $existingIntent = PaymentIntent::retrieve($payment->stripe_payment_intent_id);
                    
                    // On peut réutiliser si l'intent attend toujours un moyen de paiement
                    // et que le montant n'a pas changé
                    if ($existingIntent->status === 'requires_payment_method' && 
                        $existingIntent->amount === (int)($payment->amount * 100)) {
                        
                        Log::info('Réutilisation du PaymentIntent existant', [
                            'payment_id' => $payment->id,
                            'payment_intent_id' => $existingIntent->id
                        ]);
                        
                        return $existingIntent;
                    }
                } catch (\Exception $e) {
                    Log::warning('Impossible de récupérer le PaymentIntent existant, création d\'un nouveau', [
                        'payment_id' => $payment->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            $paymentIntent = PaymentIntent::create([
                'amount' => (int)($payment->amount * 100), // Convertir en centimes
                'currency' => strtolower($payment->currency ?? 'eur'),
                'description' => $description,
                'metadata' => $paymentMetadata,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            // Mettre à jour le paiement avec l'ID du PaymentIntent
            $payment->update([
                'stripe_payment_intent_id' => $paymentIntent->id,
                'status' => 'processing',
            ]);

            Log::info('PaymentIntent créé', [
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntent->id,
                'amount' => $payment->amount,
            ]);

            return $paymentIntent;

        } catch (ApiErrorException $e) {
            Log::error('Erreur lors de la création du PaymentIntent', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            // Mettre à jour le statut du paiement
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Confirmer un paiement après validation côté client
     *
     * @param Payment $payment
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function confirmPayment(Payment $payment, string $paymentIntentId): PaymentIntent
    {
        try {
            $paymentIntent = PaymentIntent::retrieve($paymentIntentId);

            // Vérifier que le PaymentIntent correspond au paiement
            if ($payment->stripe_payment_intent_id !== $paymentIntentId) {
                throw new \Exception('Le PaymentIntent ne correspond pas au paiement');
            }

            // Si le paiement est déjà confirmé
            if ($paymentIntent->status === 'succeeded') {
                $this->handleSuccessfulPayment($payment, $paymentIntent);
                return $paymentIntent;
            }

            // Si le paiement est en attente de confirmation
            if ($paymentIntent->status === 'requires_confirmation') {
                $paymentIntent->confirm();
            }

            // Traiter selon le statut final
            if ($paymentIntent->status === 'succeeded') {
                $this->handleSuccessfulPayment($payment, $paymentIntent);
            } elseif ($paymentIntent->status === 'requires_action') {
                // Le client doit compléter l'authentification (3D Secure, etc.)
                $payment->update(['status' => 'processing']);
            } else {
                // Échec du paiement
                $payment->update([
                    'status' => 'failed',
                    'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Paiement échoué',
                ]);
            }

            return $paymentIntent;

        } catch (ApiErrorException $e) {
            Log::error('Erreur lors de la confirmation du paiement', [
                'payment_id' => $payment->id,
                'payment_intent_id' => $paymentIntentId,
                'error' => $e->getMessage(),
            ]);

            $payment->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Gérer un paiement réussi
     *
     * @param Payment $payment
     * @param PaymentIntent $paymentIntent
     * @return void
     */
    protected function handleSuccessfulPayment(Payment $payment, PaymentIntent $paymentIntent): void
    {
        // Vérifier si le paiement est déjà marqué comme complété pour éviter les doublons d'actions (emails, etc.)
        if ($payment->status === 'completed') {
            return;
        }

        // Mettre à jour le paiement
        $payment->update([
            'status' => 'completed',
            'stripe_charge_id' => $paymentIntent->latest_charge,
            'transaction_id' => $paymentIntent->id,
            'paid_at' => now(),
        ]);

        // Mettre à jour le statut de la réservation selon le type de paiement
        $reservation = $payment->reservation;
        
        if ($payment->type === 'deposit') {
            // Si c'est l'acompte, passer la réservation à "deposit_paid"
            // Accepter 'pending' (statut initial) ou 'confirmed' (si confirmée manuellement)
            if (in_array($reservation->status, ['pending', 'confirmed'])) {
                $reservation->update([
                    'status' => 'deposit_paid',
                    'payment_expires_at' => null,
                ]);
            }

            // Générer le reçu d'acompte
            try {
                $documentService = app(\App\Services\DocumentService::class);
                $documentService->generateDocument('receipt-deposit', $reservation, $payment);
                Log::info('Reçu d\'acompte généré automatiquement', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la génération automatique du reçu d\'acompte: ' . $e->getMessage());
            }

        } elseif ($payment->type === 'balance') {
            // Si c'est le solde, vérifier si tous les paiements sont complétés
            $allPaymentsCompleted = $reservation->payments()
                ->whereIn('type', ['deposit', 'balance'])
                ->where('status', 'completed')
                ->count() >= 2;
            
            if ($allPaymentsCompleted) {
                $reservation->update(['status' => 'fully_paid']);
            }

            // Générer le reçu de solde
            try {
                $documentService = app(\App\Services\DocumentService::class);
                $documentService->generateDocument('receipt-balance', $reservation, $payment);
                Log::info('Reçu de solde généré automatiquement', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la génération automatique du reçu de solde: ' . $e->getMessage());
            }

        } elseif ($payment->type === 'deposit_guarantee') {
            // Générer le reçu de caution
            try {
                $documentService = app(\App\Services\DocumentService::class);
                $documentService->generateDocument('receipt-guarantee', $reservation, $payment);
                Log::info('Reçu de caution généré automatiquement', ['payment_id' => $payment->id]);
            } catch (\Exception $e) {
                Log::error('Erreur lors de la génération automatique du reçu de caution: ' . $e->getMessage());
            }
        }

        Log::info('Paiement confirmé avec succès', [
            'payment_id' => $payment->id,
            'reservation_id' => $reservation->id,
            'amount' => $payment->amount,
        ]);

        // Envoyer l'email de confirmation de paiement (CENTRALISÉ)
        try {
            $this->emailService->sendPaymentConfirmation($payment->fresh());
            Log::info('Email de confirmation de paiement envoyé', ['payment_id' => $payment->id]);
        } catch (\Exception $e) {
            // Log l'erreur mais ne bloque pas le processus de paiement
            Log::error('Erreur lors de l\'envoi de l\'email de confirmation de paiement: ' . $e->getMessage());
        }
    }

    /**
     * Gérer les webhooks Stripe
     *
     * @param string $payload
     * @param string $signature
     * @param string $secret
     * @return object|null
     */
    public function handleWebhook(string $payload, string $signature, string $secret): ?object
    {
        try {
            $event = Webhook::constructEvent($payload, $signature, $secret);
            
            Log::info('Webhook Stripe reçu', [
                'event_type' => $event->type,
                'event_id' => $event->id,
            ]);

            // Traiter selon le type d'événement
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $this->handlePaymentIntentSucceeded($event->data->object);
                    break;

                case 'payment_intent.payment_failed':
                    $this->handlePaymentIntentFailed($event->data->object);
                    break;

                case 'payment_intent.canceled':
                    $this->handlePaymentIntentCanceled($event->data->object);
                    break;

                case 'charge.refunded':
                    $this->handleChargeRefunded($event->data->object);
                    break;
            }

            return $event;

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du webhook Stripe', [
                'error' => $e->getMessage(),
            ]);
            
            throw $e;
        }
    }

    /**
     * Gérer un PaymentIntent réussi (webhook)
     *
     * @param object $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentSucceeded(object $paymentIntent): void
    {
        $paymentId = $paymentIntent->metadata->payment_id ?? null;
        
        if (!$paymentId) {
            Log::warning('PaymentIntent réussi mais payment_id manquant dans les métadonnées', [
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $payment = Payment::find($paymentId);
        if (!$payment) {
            Log::warning('Paiement non trouvé pour le PaymentIntent', [
                'payment_id' => $paymentId,
                'payment_intent_id' => $paymentIntent->id,
            ]);
            return;
        }

        $this->handleSuccessfulPayment($payment, $paymentIntent);
    }

    /**
     * Gérer un PaymentIntent échoué (webhook)
     *
     * @param object $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentFailed(object $paymentIntent): void
    {
        $paymentId = $paymentIntent->metadata->payment_id ?? null;
        
        if (!$paymentId) {
            return;
        }

        $payment = Payment::find($paymentId);
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'failure_reason' => $paymentIntent->last_payment_error->message ?? 'Paiement échoué',
            ]);
        }
    }

    /**
     * Gérer un PaymentIntent annulé (webhook)
     *
     * @param object $paymentIntent
     * @return void
     */
    protected function handlePaymentIntentCanceled(object $paymentIntent): void
    {
        $paymentId = $paymentIntent->metadata->payment_id ?? null;
        
        if (!$paymentId) {
            return;
        }

        $payment = Payment::find($paymentId);
        if ($payment) {
            $payment->update([
                'status' => 'cancelled',
            ]);
        }
    }

    /**
     * Gérer un remboursement (webhook)
     *
     * @param object $charge
     * @return void
     */
    protected function handleChargeRefunded(object $charge): void
    {
        // Trouver le paiement correspondant au charge_id
        $payment = Payment::where('stripe_charge_id', $charge->id)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'refunded',
            ]);

            // Créer un paiement de type "refund" si nécessaire
            Payment::create([
                'reservation_id' => $payment->reservation_id,
                'payment_number' => 'REF-' . strtoupper(\Illuminate\Support\Str::random(8)) . '-' . \Carbon\Carbon::now()->format('Y'),
                'type' => 'refund',
                'amount' => $charge->amount_refunded / 100, // Convertir de centimes
                'currency' => strtoupper($charge->currency),
                'status' => 'completed',
                'payment_method' => 'stripe',
                'stripe_charge_id' => $charge->id,
                'paid_at' => now(),
            ]);
        }
    }

    /**
     * Rembourser un paiement
     *
     * @param Payment $payment
     * @param float|null $amount Montant à rembourser (null = remboursement total)
     * @param string|null $reason Raison du remboursement
     * @return Refund
     * @throws ApiErrorException
     */
    public function refundPayment(Payment $payment, ?float $amount = null, ?string $reason = null): Refund
    {
        if (!$payment->stripe_charge_id) {
            throw new \Exception('Le paiement n\'a pas de charge Stripe associée');
        }

        if ($payment->status !== 'completed') {
            throw new \Exception('Seuls les paiements complétés peuvent être remboursés');
        }

        try {
            $refundData = [
                'charge' => $payment->stripe_charge_id,
                'reason' => $reason ?? 'requested_by_customer',
            ];

            if ($amount !== null && $amount < $payment->amount) {
                $refundData['amount'] = (int)($amount * 100); // Convertir en centimes
            }

            $refund = Refund::create($refundData);

            // Mettre à jour le statut du paiement
            $payment->update([
                'status' => 'refunded',
            ]);

            Log::info('Remboursement effectué', [
                'payment_id' => $payment->id,
                'refund_id' => $refund->id,
                'amount' => $amount ?? $payment->amount,
            ]);

            return $refund;

        } catch (ApiErrorException $e) {
            Log::error('Erreur lors du remboursement', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Récupérer un PaymentIntent depuis Stripe
     *
     * @param string $paymentIntentId
     * @return PaymentIntent
     * @throws ApiErrorException
     */
    public function retrievePaymentIntent(string $paymentIntentId): PaymentIntent
    {
        return PaymentIntent::retrieve($paymentIntentId);
    }
}

