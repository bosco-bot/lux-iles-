<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\PaymentService;
use App\Services\EmailService;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Stripe\Exception\ApiErrorException;

class PaymentController extends Controller
{
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * Créer un PaymentIntent pour un paiement (arrhes)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createIntent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reservation_id' => 'required|exists:reservations,id',
            'payment_type' => 'required|in:deposit,balance,deposit_guarantee',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $reservation = Reservation::findOrFail($request->reservation_id);
            
            // Vérifier que la réservation appartient à l'utilisateur connecté
            if ($reservation->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à payer cette réservation.',
                ], 403);
            }

            $payment = $reservation->payments()
                ->where('type', $request->payment_type)
                ->whereIn('status', ['pending', 'processing'])
                ->first();

            if (!$payment) {
                return response()->json([
                    'success' => false,
                    'message' => 'Paiement non trouvé ou déjà traité.',
                ], 404);
            }

            // Créer le PaymentIntent
            $paymentIntent = $this->paymentService->createPaymentIntent($payment);

            // Récupérer la clé publique Stripe
            $publicKey = SettingsHelper::get('stripe_public_key');

            return response()->json([
                'success' => true,
                'client_secret' => $paymentIntent->client_secret,
                'payment_intent_id' => $paymentIntent->id,
                'public_key' => $publicKey,
                'payment_id' => $payment->id,
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Erreur Stripe lors de la création du PaymentIntent', [
                'error' => $e->getMessage(),
                'reservation_id' => $request->reservation_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création du paiement : ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la création du PaymentIntent', [
                'error' => $e->getMessage(),
                'reservation_id' => $request->reservation_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Confirmer un paiement après validation côté client
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function confirm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
            'payment_id' => 'required|exists:payments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $payment = Payment::findOrFail($request->payment_id);
            $reservation = $payment->reservation;

            // Vérifier que le paiement appartient à l'utilisateur connecté
            if ($reservation->user_id !== auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Vous n\'êtes pas autorisé à confirmer ce paiement.',
                ], 403);
            }

            // Confirmer le paiement
            $paymentIntent = $this->paymentService->confirmPayment($payment, $request->payment_intent_id);

            // Si le paiement nécessite une action (3D Secure, etc.)
            if ($paymentIntent->status === 'requires_action') {
                return response()->json([
                    'success' => true,
                    'requires_action' => true,
                    'client_secret' => $paymentIntent->client_secret,
                    'payment_intent_id' => $paymentIntent->id,
                ]);
            }

            // Si le paiement a échoué
            if ($paymentIntent->status !== 'succeeded') {
                return response()->json([
                    'success' => false,
                    'message' => 'Le paiement a échoué. Veuillez réessayer.',
                    'status' => $paymentIntent->status,
                ], 400);
            }

            // Paiement réussi
            // L'email de confirmation est envoyé automatiquement par le PaymentService (handleSuccessfulPayment)
            
            // Envoyer une notification à tous les administrateurs
            try {
                $payment->load('reservation.villa');
                $admins = \App\Models\User::where('is_admin', true)->where('is_active', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\PaymentReceivedNotification($payment));
                }
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                Log::error('Erreur lors de l\'envoi de la notification de paiement: ' . $e->getMessage());
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Paiement confirmé avec succès.',
                'payment' => [
                    'id' => $payment->id,
                    'status' => $payment->fresh()->status,
                    'amount' => $payment->amount,
                ],
                'reservation' => [
                    'id' => $reservation->id,
                    'status' => $reservation->fresh()->status,
                ],
            ]);

        } catch (ApiErrorException $e) {
            Log::error('Erreur Stripe lors de la confirmation du paiement', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la confirmation du paiement : ' . $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            Log::error('Erreur lors de la confirmation du paiement', [
                'error' => $e->getMessage(),
                'payment_id' => $request->payment_id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
            ], 500);
        }
    }

    /**
     * Webhook Stripe
     * 
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $signature = $request->header('Stripe-Signature');
        $secret = SettingsHelper::get('stripe_webhook_secret');

        if (!$secret) {
            Log::warning('Secret webhook Stripe non configuré');
            return response('Webhook secret non configuré', 500);
        }

        try {
            $event = $this->paymentService->handleWebhook($payload, $signature, $secret);
            
            return response()->json([
                'received' => true,
                'event_id' => $event->id,
                'event_type' => $event->type,
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors du traitement du webhook Stripe', [
                'error' => $e->getMessage(),
            ]);

            return response('Erreur lors du traitement du webhook', 400);
        }
    }

    /**
     * Récupérer le statut d'un PaymentIntent
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_intent_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $paymentIntent = $this->paymentService->retrievePaymentIntent($request->payment_intent_id);

            return response()->json([
                'success' => true,
                'status' => $paymentIntent->status,
                'client_secret' => $paymentIntent->client_secret,
            ]);

        } catch (ApiErrorException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur : ' . $e->getMessage(),
            ], 500);
        }
    }
}

