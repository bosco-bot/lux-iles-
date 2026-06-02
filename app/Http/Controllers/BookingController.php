<?php

namespace App\Http\Controllers;

use App\Models\Villa;
use App\Models\Reservation;
use App\Models\Payment;
use App\Helpers\SettingsHelper;
use App\Services\DocumentService;
use App\Services\PaymentService;
use App\Services\PromoCodeService;
use App\Models\PromoCode;
use App\Jobs\SendReservationConfirmationJob;
use App\Services\VillaAvailabilityContext;
use App\Services\VillaAvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct(
        protected VillaAvailabilityService $availabilityService,
    ) {}

    /**
     * Afficher la page de réservation
     */
    public function create(Request $request)
    {
        $villaId = $request->query('villa_id');
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $guests = $request->query('guests', 2);
        
        if (!$villaId) {
            return redirect()->route('villas.index')->with('error', 'Veuillez sélectionner une villa.');
        }
        
        $villa = Villa::with(['island', 'photos', 'equipments', 'availabilityBlocks', 'seasonalPrices.season'])
            ->where('is_active', true)
            ->findOrFail($villaId);

        $publicAvailability = VillaAvailabilityContext::publicSite();
        $blockedDates = $this->availabilityService->getBlockedDates($villa->id, null, $publicAvailability);
        $reservations = $this->availabilityService->getReservationsForCalendar($villa->id, $publicAvailability);
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        // Récupérer les paramètres globaux pour le JavaScript
        $globalTaxRate = SettingsHelper::get('global_tax_rate', 8.5);
        $touristTaxPerNight = SettingsHelper::get('tourist_tax_per_night', 2.50);
        $touristTaxEnabled = SettingsHelper::get('tourist_tax_enabled', true);
        $depositPercentage = SettingsHelper::get('deposit_percentage_min', 30);
        $depositPercentageMax = SettingsHelper::get('deposit_percentage_max', 50);
        $serviceFeePercentage = SettingsHelper::get('service_fee_percentage', 5);
        
        return view('pages.booking', compact(
            'villa', 
            'primaryPhoto', 
            'checkIn', 
            'checkOut', 
            'guests', 
            'reservations',
            'blockedDates',
            'globalTaxRate',
            'touristTaxPerNight',
            'touristTaxEnabled',
            'depositPercentage',
            'depositPercentageMax',
            'serviceFeePercentage'
        ));
    }
    
    /**
     * Afficher la page de paiement
     */
    public function payment(Request $request)
    {
        // Si l'utilisateur n'est pas connecté, le rediriger AVANT d'afficher la page de paiement
        if (!Auth::check()) {
            // Conserver tous les paramètres de la requête pour reconstruire l'URL de paiement
            $queryParams = $request->query();

            $returnUrl = route('bookings.payment', [
                'villa_id' => $queryParams['villa_id'] ?? null,
                'check_in' => $queryParams['check_in'] ?? null,
                'check_out' => $queryParams['check_out'] ?? null,
                'guests' => $queryParams['guests'] ?? null,
                'adults' => $queryParams['adults'] ?? null,
                'children' => $queryParams['children'] ?? null,
                'infants' => $queryParams['infants'] ?? null,
            ]);

            // Sauvegarder l'URL dans la session (utilisée par Api\AuthController après login)
            session(['intended_url' => $returnUrl]);

            return redirect()->route('login')->with('info', 'Veuillez vous connecter pour poursuivre votre paiement.');
        }

        $villaId = $request->query('villa_id');
        $checkIn = $request->query('check_in');
        $checkOut = $request->query('check_out');
        $guests = $request->query('guests', 2);
        $adults = $request->query('adults', $guests);
        $children = $request->query('children', 0);
        $infants = $request->query('infants', 0);
        
        if (!$villaId || !$checkIn || !$checkOut) {
            return redirect()->route('villas.index')->with('error', 'Informations de réservation incomplètes.');
        }
        
        $villa = Villa::with(['island', 'photos', 'seasonalPrices.season'])
            ->where('is_active', true)
            ->findOrFail($villaId);
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        $depositPercentage = SettingsHelper::get('deposit_percentage_min', 30);
        $totals = $this->buildBookingTotals($villa, $checkIn, $checkOut, (int) $guests, $depositPercentage);
        $nights = $totals['nights'];

        $promoCodeInput = $request->query('promo_code');
        $appliedPromo = null;
        $discountAmount = 0.0;

        if ($promoCodeInput && Auth::check()) {
            $promoService = app(PromoCodeService::class);
            $validation = $promoService->validate($promoCodeInput, Auth::user());

            if ($validation['valid']) {
                $appliedPromo = PromoCode::find($validation['promo_code_id']);
                if ($appliedPromo) {
                    $discountAmount = $promoService->calculateDiscount($appliedPromo, $totals['subtotal_before_discount']);
                    $totals = $this->applyDiscountToTotals($totals, $discountAmount, $depositPercentage);
                }
            }
        }

        extract($totals);

        $stripePublicKey = SettingsHelper::get('stripe_public_key');
        $promoCode = $appliedPromo?->code;
        $promoCodeId = $appliedPromo?->id;

        return view('pages.payment', compact(
            'villa',
            'primaryPhoto',
            'checkIn',
            'checkOut',
            'nights',
            'adults',
            'children',
            'infants',
            'basePrice',
            'cleaningFee',
            'serviceFee',
            'vatAmount',
            'touristTax',
            'total',
            'depositAmount',
            'balanceAmount',
            'discountAmount',
            'promoCode',
            'promoCodeId',
            'stripePublicKey'
        ));
    }
    
    /**
     * Traiter la confirmation de réservation (vérifie l'authentification)
     */
    public function confirm(Request $request)
    {
        // Si l'utilisateur n'est pas connecté
        if (!Auth::check()) {
            // Récupérer les paramètres depuis le body JSON ou les query params
            $params = $request->all();
            if (empty($params['villa_id']) && $request->has('villa_id')) {
                $params = $request->query();
            }
            
            // Construire l'URL de retour
            $returnUrl = route('bookings.payment', [
                'villa_id' => $params['villa_id'] ?? null,
                'check_in' => $params['check_in'] ?? null,
                'check_out' => $params['check_out'] ?? null,
                'guests' => $params['guests'] ?? null,
                'adults' => $params['adults'] ?? null,
                'children' => $params['children'] ?? null,
                'infants' => $params['infants'] ?? null,
            ]);
            
            // Sauvegarder l'URL dans la session
            session(['intended_url' => $returnUrl]);
            
            // Si c'est une requête AJAX, retourner une réponse JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'redirect' => true,
                    'url' => route('login'),
                    'message' => 'Veuillez vous connecter pour confirmer votre réservation.'
                ], 401);
            }
            
            // Sinon, rediriger normalement
            return redirect()->route('login')->with('info', 'Veuillez vous connecter pour confirmer votre réservation.');
        }
        
        // Debug: Log des données reçues
        \Log::info('Données reçues pour confirmation réservation:', $request->all());

        // Validation des données
        try {
            $validated = $request->validate([
                'villa_id' => 'required|exists:villas,id',
                'check_in' => 'required|date|after_or_equal:today',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'required|integer|min:1',
                'adults' => 'required|integer|min:1',
                'children' => 'nullable|integer|min:0',
                'infants' => 'nullable|integer|min:0',
                'special_requests' => 'nullable|string|max:1000',
                'promo_code' => 'nullable|string|max:50',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Erreur de validation:', $e->errors());
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors' => $e->errors(),
            ], 422);
        }

        \Log::info('Données validées:', $validated);

        $villa = Villa::with(['island', 'seasonalPrices.season'])
            ->where('is_active', true)
            ->findOrFail($validated['villa_id']);

        $checkInDate = Carbon::parse($validated['check_in']);
        $checkOutDate = Carbon::parse($validated['check_out']);
        $nights = $checkInDate->diffInDays($checkOutDate);

        if ($bookingRulesError = $this->validateVillaBookingRules($villa, $nights, (int) $validated['guests'])) {
            return $bookingRulesError;
        }

        if ($this->availabilityService->hasConflict(
            $villa->id,
            $checkInDate,
            $checkOutDate,
            null,
            VillaAvailabilityContext::publicSite()
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Ces dates ne sont pas disponibles pour cette villa (réservation ou blocage existant).',
            ], 422);
        }
        
        try {
            DB::beginTransaction();
            
            // Récupérer l'utilisateur connecté
            $user = Auth::user();
            
            // Récupérer les paramètres globaux
            $globalTaxRate = SettingsHelper::get('global_tax_rate', 8.5);
            $touristTaxPerNight = SettingsHelper::get('tourist_tax_per_night', 2.50);
            $touristTaxEnabled = SettingsHelper::get('tourist_tax_enabled', true);
            $depositPercentageMin = SettingsHelper::get('deposit_percentage_min', 30);
            $depositPercentageMax = SettingsHelper::get('deposit_percentage_max', 50);
            $balanceDueDays = SettingsHelper::get('balance_due_days_before_checkin', 30);
            $depositGuaranteeDays = SettingsHelper::get('deposit_guarantee_days_before_checkin', 7);
            
            // Récupérer le pourcentage d'acompte depuis la requête ou utiliser le minimum
            $depositPercentage = $request->input('deposit_percentage', $depositPercentageMin);
            
            // Valider que le pourcentage d'acompte est dans la plage autorisée
            if ($depositPercentage < $depositPercentageMin || $depositPercentage > $depositPercentageMax) {
                return response()->json([
                    'success' => false,
                    'message' => "Le pourcentage d'acompte doit être entre {$depositPercentageMin}% et {$depositPercentageMax}%.",
                ], 422);
            }
            
            $totals = $this->buildBookingTotals(
                $villa,
                $validated['check_in'],
                $validated['check_out'],
                (int) $validated['guests'],
                $depositPercentage
            );

            $discountAmount = 0.0;
            $promoCodeId = null;
            $promoCodeModel = null;

            if (! empty($validated['promo_code'])) {
                $promoService = app(PromoCodeService::class);
                $promoValidation = $promoService->validate($validated['promo_code'], $user);

                if (! $promoValidation['valid']) {
                    return response()->json([
                        'success' => false,
                        'message' => $promoValidation['message'],
                    ], 422);
                }

                $promoCodeModel = PromoCode::findOrFail($promoValidation['promo_code_id']);
                $discountAmount = $promoService->calculateDiscount($promoCodeModel, $totals['subtotal_before_discount']);
                $totals = $this->applyDiscountToTotals($totals, $discountAmount, $depositPercentage);
                $promoCodeId = $promoCodeModel->id;
            }

            $basePrice = $totals['basePrice'];
            $cleaningFee = $totals['cleaningFee'];
            $serviceFee = $totals['serviceFee'];
            $vatAmount = $totals['vatAmount'];
            $touristTax = $totals['touristTax'];
            $total = $totals['total'];
            $depositAmount = $totals['depositAmount'];
            $balanceAmount = $totals['balanceAmount'];
            
            // Calculer les dates d'échéance de manière intelligente
            $balanceDueDate = $this->calculateSmartDueDate($checkInDate, $balanceDueDays);
            $depositGuaranteeDueDate = $this->calculateSmartDueDate($checkInDate, $depositGuaranteeDays);
            
            // Générer un numéro de réservation unique
            $reservationNumber = 'LX-' . strtoupper(Str::random(6)) . '-' . Carbon::now()->format('Y');
            while (Reservation::where('reservation_number', $reservationNumber)->exists()) {
                $reservationNumber = 'LX-' . strtoupper(Str::random(6)) . '-' . Carbon::now()->format('Y');
            }
            
            // Créer la réservation
            $reservation = Reservation::create([
                'reservation_number' => $reservationNumber,
                'villa_id' => $villa->id,
                'user_id' => $user->id,
                'guest_first_name' => $user->first_name ?? '',
                'guest_last_name' => $user->last_name ?? '',
                'guest_email' => $user->email,
                'guest_phone' => $user->phone ?? null,
                'guest_address' => $user->address ?? null,
                'check_in_date' => $checkInDate->toDateString(),
                'check_out_date' => $checkOutDate->toDateString(),
                'number_of_nights' => $nights,
                'number_of_guests' => $validated['guests'],
                'adults' => $validated['adults'] ?? $validated['guests'],
                'children' => $validated['children'] ?? 0,
                'infants' => $validated['infants'] ?? 0,
                'base_price' => $basePrice,
                'cleaning_fee' => $cleaningFee,
                'service_fee' => $serviceFee,
                'vat_amount' => $vatAmount,
                'tourist_tax' => $touristTax,
                'total_price' => $total,
                'promo_code_id' => $promoCodeId,
                'discount_amount' => $discountAmount > 0 ? $discountAmount : null,
                'currency' => 'EUR',
                'deposit_percentage' => $depositPercentage,
                'deposit_amount' => $depositAmount,
                'balance_amount' => $balanceAmount,
                'status' => 'pending', // Statut initial : en attente de paiement
                'source' => 'direct',
                'special_requests' => $validated['special_requests'] ?? null,
                'created_by' => $user->id,
            ]);
            
            // Créer les paiements avec les dates d'échéance
            // Paiement de l'acompte (pas de date d'échéance, payable immédiatement)
            $depositPaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
            while (Payment::where('payment_number', $depositPaymentNumber)->exists()) {
                $depositPaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
            }
            
            $depositPayment = Payment::create([
                'reservation_id' => $reservation->id,
                'payment_number' => $depositPaymentNumber,
                'type' => 'deposit',
                'amount' => $depositAmount,
                'currency' => 'EUR',
                'status' => 'pending',
                'payment_method' => 'stripe',
            ]);
            
            // Paiement du solde avec date d'échéance
            $balancePaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
            while (Payment::where('payment_number', $balancePaymentNumber)->exists()) {
                $balancePaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
            }
            
            Payment::create([
                'reservation_id' => $reservation->id,
                'payment_number' => $balancePaymentNumber,
                'type' => 'balance',
                'amount' => $balanceAmount,
                'currency' => 'EUR',
                'status' => 'pending',
                'payment_method' => 'stripe',
                'due_date' => $balanceDueDate->toDateString(),
            ]);
            
            // Dépôt de garantie avec date d'échéance (si montant > 0)
            $depositGuaranteeAmount = $villa->deposit_amount ?? 0;
            if ($depositGuaranteeAmount > 0) {
                $guaranteePaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
                while (Payment::where('payment_number', $guaranteePaymentNumber)->exists()) {
                    $guaranteePaymentNumber = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
                }
                
                Payment::create([
                    'reservation_id' => $reservation->id,
                    'payment_number' => $guaranteePaymentNumber,
                    'type' => 'deposit_guarantee',
                    'amount' => $depositGuaranteeAmount,
                    'currency' => 'EUR',
                    'status' => 'pending',
                    'payment_method' => 'stripe',
                    'due_date' => $depositGuaranteeDueDate->toDateString(),
                ]);
                
                // Mettre à jour le montant du dépôt de garantie dans la réservation
                $reservation->update(['deposit_guarantee' => $depositGuaranteeAmount]);
            }
            
            // Créer le PaymentIntent Stripe pour l'acompte
            $paymentService = app(PaymentService::class);
            $clientSecret = null;
            $paymentIntentId = null;
            
            try {
                if ($depositAmount > 0) {
                    $paymentIntent = $paymentService->createPaymentIntent($depositPayment);
                    $clientSecret = $paymentIntent->client_secret;
                    $paymentIntentId = $paymentIntent->id;
                }
            } catch (\Exception $e) {
                // Log l'erreur mais continue (le paiement pourra être créé plus tard)
                \Log::error('Erreur lors de la création du PaymentIntent: ' . $e->getMessage());
                // Ne pas générer les documents si le paiement n'a pas pu être initialisé
                // Les documents seront générés après le paiement réussi via webhook
            }
            
            if ($promoCodeModel) {
                app(PromoCodeService::class)->recordUsage($promoCodeModel, $user, $reservation);
            }

            DB::commit();
            
            // Charger les relations nécessaires pour les notifications
            $reservation->load('villa');
            
            // Envoyer l'email de confirmation de réservation (en arrière-plan)
            try {
                SendReservationConfirmationJob::dispatch($reservation);
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                \Log::error('Erreur lors de l\'envoi de l\'email de confirmation: ' . $e->getMessage());
            }
            
            // Générer automatiquement les documents pour la réservation
            try {
                $documentService = app(\App\Services\DocumentService::class);
                $generatedDocuments = $documentService->generateReservationDocuments($reservation);
                \Log::info('Documents générés pour la réservation ' . $reservation->id . ': ' . count($generatedDocuments));
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                \Log::error('Erreur lors de la génération des documents: ' . $e->getMessage());
            }

            // Envoyer une notification à tous les administrateurs
            try {
                $admins = \App\Models\User::where('is_admin', true)->where('is_active', true)->get();
                foreach ($admins as $admin) {
                    $admin->notify(new \App\Notifications\ReservationCreatedNotification($reservation));
                }
            } catch (\Exception $e) {
                // Log l'erreur mais ne bloque pas le processus
                \Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
            }
            
            // Si le PaymentIntent a été créé avec succès, retourner les infos de paiement
            if ($clientSecret) {
                $publicKey = SettingsHelper::get('stripe_public_key');
                
                return response()->json([
                    'success' => true,
                    'requires_payment' => true,
                    'message' => 'Réservation créée. Veuillez compléter le paiement.',
                    'payment' => [
                        'client_secret' => $clientSecret,
                        'payment_intent_id' => $paymentIntentId,
                        'public_key' => $publicKey,
                        'payment_id' => $depositPayment->id,
                        'amount' => $depositAmount,
                    ],
                    'reservation_id' => $reservation->id,
                    'reservation_number' => $reservationNumber,
                ]);
            }
            
            // Fallback : si le PaymentIntent n'a pas pu être créé, retourner l'URL de confirmation
            // (pour ne pas casser le flux actuel, mais idéalement il faudrait gérer l'erreur)
            $confirmationUrl = route('bookings.confirmation', [
                'reservation_number' => $reservationNumber,
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Réservation créée avec succès.',
                'redirect_url' => $confirmationUrl,
                'reservation_number' => $reservationNumber,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::error('Erreur lors de la création de la réservation: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la confirmation de votre réservation. Veuillez réessayer.',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }
    
    /**
     * Afficher la page de confirmation de réservation
     */
    public function showConfirmation(Request $request)
    {
        $reservationNumber = $request->query('reservation_number');
        
        if (!$reservationNumber) {
            return redirect()->route('villas.index')->with('error', 'Numéro de réservation manquant.');
        }
        
        // Récupérer la réservation avec toutes ses relations
        $reservation = Reservation::where('reservation_number', $reservationNumber)
            ->with(['villa.island', 'villa.photos', 'user', 'payments', 'documents', 'promoCode'])
            ->first();
        
        if (!$reservation) {
            return redirect()->route('villas.index')->with('error', 'Réservation non trouvée.');
        }
        
        // Vérifier que l'utilisateur connecté est le propriétaire de la réservation
        if (Auth::check() && $reservation->user_id !== Auth::id()) {
            return redirect()->route('villas.index')->with('error', 'Vous n\'avez pas accès à cette réservation.');
        }
        
        $villa = $reservation->villa;
        
        // Récupérer la photo principale
        $primaryPhoto = $villa->photos->where('is_primary', true)->first() 
            ?? $villa->photos->first();
        
        // Calculer les dates
        $checkIn = $reservation->check_in_date->format('Y-m-d');
        $checkOut = $reservation->check_out_date->format('Y-m-d');
        $nights = $reservation->number_of_nights;
        
        // Récupérer les informations depuis la réservation
        $basePrice = $reservation->base_price;
        $cleaningFee = $reservation->cleaning_fee;
        $serviceFee = $reservation->service_fee;
        $vatAmount = $reservation->vat_amount ?? 0;
        $touristTax = $reservation->tourist_tax;
        $total = $reservation->total_price;
        $depositAmount = $reservation->deposit_amount;
        $balanceAmount = $reservation->balance_amount;
        
        $guestName = $reservation->guest_first_name . ' ' . $reservation->guest_last_name;
        
        // Récupérer la décomposition des voyageurs depuis la réservation
        $adults = $reservation->adults ?? $reservation->number_of_guests;
        $children = $reservation->children ?? 0;
        $infants = $reservation->infants ?? 0;
        
        return view('pages.booking-confirmation', compact(
            'villa', 
            'primaryPhoto', 
            'checkIn', 
            'checkOut', 
            'nights',
            'adults',
            'children',
            'infants',
            'basePrice',
            'cleaningFee',
            'serviceFee',
            'vatAmount',
            'touristTax',
            'total',
            'depositAmount',
            'balanceAmount',
            'reservationNumber',
            'guestName',
            'reservation'
        ));
    }

    /**
     * Calculer une date d'échéance de manière intelligente
     * Évite les dates dans le passé pour les réservations last-minute
     */
    private function calculateSmartDueDate(Carbon $checkInDate, int $daysBefore): Carbon
    {
        $calculatedDate = $checkInDate->copy()->subDays($daysBefore);

        // Si la date calculée est dans le passé ou dans moins de 7 jours
        if ($calculatedDate->isPast() || $calculatedDate->diffInDays(now()) < 7) {
            // Pour les réservations last-minute :
            // Utiliser minimum 7 jours après la date de réservation
            return now()->addDays(7);
        }

        return $calculatedDate;
    }

    /**
     * §3.2 CDC — Valider un code promo saisi manuellement (AJAX).
     */
    public function checkPromo(Request $request)
    {
        if (! Auth::check()) {
            return response()->json([
                'valid' => false,
                'message' => 'Veuillez vous connecter pour utiliser un code promotionnel.',
            ], 401);
        }

        try {
            $validated = $request->validate([
                'promo_code' => 'required|string|max:50',
                'villa_id' => 'required|exists:villas,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'nullable|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Données invalides.',
                'errors' => $e->errors(),
            ], 422);
        }

        $villa = Villa::with('seasonalPrices.season')
            ->where('is_active', true)
            ->findOrFail($validated['villa_id']);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);
        $nights = $checkIn->diffInDays($checkOut);
        $guests = (int) ($validated['guests'] ?? 1);

        if ($bookingRulesError = $this->validateVillaBookingRules($villa, $nights, $guests)) {
            $payload = json_decode($bookingRulesError->getContent(), true);

            return response()->json([
                'valid' => false,
                'message' => $payload['message'] ?? 'Règles de réservation non respectées.',
            ], 422);
        }

        $totals = $this->buildBookingTotals($villa, $validated['check_in'], $validated['check_out'], $guests);
        $promoService = app(PromoCodeService::class);
        $validation = $promoService->validate($validated['promo_code'], Auth::user());

        if (! $validation['valid']) {
            return response()->json([
                'valid' => false,
                'message' => $validation['message'],
            ]);
        }

        $promo = PromoCode::findOrFail($validation['promo_code_id']);
        $discountAmount = $promoService->calculateDiscount($promo, $totals['subtotal_before_discount']);
        $newTotal = round($totals['subtotal_before_discount'] - $discountAmount, 2);

        return response()->json([
            'valid' => true,
            'message' => $validation['message'],
            'discount_amount' => $discountAmount,
            'new_total' => $newTotal,
            'promo_code' => $promo->code,
            'deposit_amount' => round($newTotal * (SettingsHelper::get('deposit_percentage_min', 30) / 100), 2),
            'balance_amount' => round($newTotal - round($newTotal * (SettingsHelper::get('deposit_percentage_min', 30) / 100), 2), 2),
        ]);
    }

    /**
     * API: Calculer le prix pour une période donnée (prend en compte les tarifs saisonniers)
     */
    public function calculatePrice(Request $request)
    {
        try {
            $request->validate([
                'villa_id' => 'required|exists:villas,id',
                'check_in' => 'required|date',
                'check_out' => 'required|date|after:check_in',
                'guests' => 'nullable|integer|min:1',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur de validation des données.',
                'errors' => $e->errors(),
            ], 422);
        }

        $villa = Villa::with('seasonalPrices.season')
            ->where('is_active', true)
            ->findOrFail($request->villa_id);

        $checkIn = Carbon::parse($request->check_in);
        $checkOut = Carbon::parse($request->check_out);
        $nights = $checkIn->diffInDays($checkOut);

        $guests = $request->filled('guests') ? (int) $request->input('guests') : null;

        if ($bookingRulesError = $this->validateVillaBookingRules($villa, $nights, $guests)) {
            return $bookingRulesError;
        }

        if ($this->availabilityService->hasConflict(
            $villa->id,
            $checkIn,
            $checkOut,
            null,
            VillaAvailabilityContext::publicSite()
        )) {
            return response()->json([
                'success' => false,
                'message' => 'Ces dates ne sont pas disponibles pour cette villa (réservation ou blocage existant).',
            ], 422);
        }

        // Calculer le prix en tenant compte des tarifs saisonniers
        $basePrice = $villa->calculatePriceForPeriod($request->check_in, $request->check_out);
        
        // Calculer le prix moyen par nuit pour l'affichage
        $averagePricePerNight = $nights > 0 ? $basePrice / $nights : $villa->base_price_per_night;

        return response()->json([
            'success' => true,
            'base_price' => round($basePrice, 2),
            'average_price_per_night' => round($averagePricePerNight, 2),
            'nights' => $nights,
            'base_price_per_night' => round($villa->base_price_per_night, 2), // Prix de base pour référence
        ]);
    }

    /**
     * Valide la durée minimale de séjour et la capacité maximale (CDC §3.6, §3.7).
     *
     * @return \Illuminate\Http\JsonResponse|null
     */
    private function validateVillaBookingRules(Villa $villa, int $nights, ?int $guests = null): ?\Illuminate\Http\JsonResponse
    {
        $minStay = (int) ($villa->minimum_stay_nights ?? 3);

        if ($nights < $minStay) {
            $message = "La durée minimale pour cette villa est de {$minStay} nuit"
                . ($minStay > 1 ? 's' : '')
                . '. Pour toute demande spécifique, contactez directement l\'équipe LUXÎLES.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [
                    'check_out' => [$message],
                ],
            ], 422);
        }

        if ($guests !== null && $guests > (int) $villa->max_capacity) {
            $maxCapacity = (int) $villa->max_capacity;
            $message = "La capacité maximale de cette villa est de {$maxCapacity} personne"
                . ($maxCapacity > 1 ? 's' : '')
                . '.';

            return response()->json([
                'success' => false,
                'message' => $message,
                'errors' => [
                    'guests' => [$message],
                ],
            ], 422);
        }

        return null;
    }

    /**
     * Calcule le détail tarifaire d'une réservation (hors code promo).
     */
    private function buildBookingTotals(
        Villa $villa,
        string $checkIn,
        string $checkOut,
        int $guests,
        ?int $depositPercentage = null
    ): array {
        $globalTaxRate = SettingsHelper::get('global_tax_rate', 8.5);
        $touristTaxPerNight = SettingsHelper::get('tourist_tax_per_night', 2.50);
        $touristTaxEnabled = SettingsHelper::get('tourist_tax_enabled', true);
        $depositPercentage = $depositPercentage ?? SettingsHelper::get('deposit_percentage_min', 30);

        $checkInDate = Carbon::parse($checkIn);
        $checkOutDate = Carbon::parse($checkOut);
        $nights = $checkInDate->diffInDays($checkOutDate);

        $basePrice = $villa->calculatePriceForPeriod($checkIn, $checkOut);
        $cleaningFee = (float) ($villa->cleaning_fee ?? 0);
        $serviceFeePercentage = $villa->service_fee_percentage ?? SettingsHelper::get('service_fee_percentage', 5);
        $serviceFee = $basePrice * ((float) $serviceFeePercentage / 100);
        $vatAmount = ($cleaningFee + $serviceFee) * ((float) $globalTaxRate / 100);

        $touristTax = 0;
        if ($touristTaxEnabled) {
            $touristTax = (float) $touristTaxPerNight * $guests * $nights;
        }

        $subtotalBeforeDiscount = round($basePrice + $cleaningFee + $serviceFee + $vatAmount + $touristTax, 2);
        $total = $subtotalBeforeDiscount;
        $depositAmount = round($total * ($depositPercentage / 100), 2);
        $balanceAmount = round($total - $depositAmount, 2);

        return [
            'nights' => $nights,
            'basePrice' => round($basePrice, 2),
            'cleaningFee' => round($cleaningFee, 2),
            'serviceFee' => round($serviceFee, 2),
            'vatAmount' => round($vatAmount, 2),
            'touristTax' => round($touristTax, 2),
            'subtotal_before_discount' => $subtotalBeforeDiscount,
            'total' => $total,
            'depositAmount' => $depositAmount,
            'balanceAmount' => $balanceAmount,
        ];
    }

    /**
     * Applique une réduction promo sur les totaux calculés.
     */
    private function applyDiscountToTotals(array $totals, float $discountAmount, int $depositPercentage): array
    {
        $discountAmount = round(min($discountAmount, $totals['subtotal_before_discount']), 2);
        $total = round($totals['subtotal_before_discount'] - $discountAmount, 2);
        $depositAmount = round($total * ($depositPercentage / 100), 2);
        $balanceAmount = round($total - $depositAmount, 2);

        $totals['discount_amount'] = $discountAmount;
        $totals['total'] = $total;
        $totals['depositAmount'] = $depositAmount;
        $totals['balanceAmount'] = $balanceAmount;

        return $totals;
    }
}

