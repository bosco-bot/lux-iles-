<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\SettingsHelper;
use App\Http\Controllers\Controller;
use App\Jobs\SendReservationConfirmationJob;
use App\Models\Payment;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Villa;
use App\Services\EmailService;
use App\Services\VillaAvailabilityContext;
use App\Services\VillaAvailabilityService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ReservationController extends Controller
{
  public function __construct(
    protected EmailService $emailService,
    protected VillaAvailabilityService $availabilityService,
  ) {}

  /**
   * Afficher la liste des réservations
   */
  public function index(Request $request)
  {
    try {
      $query = Reservation::with(['villa.island', 'user', 'payments']);

      if ($request->filled('status')) {
        $query->where('status', $request->status);
      }

      if ($request->filled('source')) {
        $query->where('source', $request->source);
      }

      if ($request->filled('villa_id')) {
        $query->where('villa_id', $request->villa_id);
      }

      if ($request->filled('period')) {
        $now = Carbon::now();
        switch ($request->period) {
          case 'this_month':
            $query->whereBetween('created_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()]);
            break;
          case 'last_month':
            $query->whereBetween('created_at', [$now->copy()->subMonth()->startOfMonth(), $now->copy()->subMonth()->endOfMonth()]);
            break;
          case 'this_year':
            $query->whereBetween('created_at', [$now->copy()->startOfYear(), $now->copy()->endOfYear()]);
            break;
        }
      }

      if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
          $q->where('reservation_number', 'like', "%{$search}%")
            ->orWhere('guest_first_name', 'like', "%{$search}%")
            ->orWhere('guest_last_name', 'like', "%{$search}%")
            ->orWhere('guest_email', 'like', "%{$search}%");
        });
      }

      $sortBy = $request->get('sort_by', 'created_at');
      $sortOrder = $request->get('sort_order', 'desc');
      $query->orderBy($sortBy, $sortOrder);

      $perPage = $request->get('per_page', 10);
      $reservations = $query->paginate($perPage);

      $villas = Villa::where('is_active', true)->orderBy('name')->get();

      $totalReservations = Reservation::count();
      $confirmedReservations = Reservation::where('status', 'confirmed')->count();
      $pendingReservations = Reservation::where('status', 'pending')->count();
      $cancelledReservations = Reservation::where('status', 'cancelled')->count();

      return view('pages.admin.reservations', compact(
        'reservations',
        'villas',
        'totalReservations',
        'confirmedReservations',
        'pendingReservations',
        'cancelledReservations'
      ));
    } catch (\Exception $e) {
      return view('pages.admin.reservations', [
        'reservations' => collect([]),
        'villas' => collect([]),
        'totalReservations' => 0,
        'confirmedReservations' => 0,
        'pendingReservations' => 0,
        'cancelledReservations' => 0,
      ]);
    }
  }

  /**
   * Formulaire de création manuelle (§3.11 CDC).
   */
  public function create(Request $request)
  {
    $clients = User::where('is_admin', false)
      ->where('is_active', true)
      ->orderBy('last_name')
      ->orderBy('first_name')
      ->get(['id', 'first_name', 'last_name', 'email', 'phone']);

    $villas = Villa::with('island')
      ->where('is_active', true)
      ->orderBy('name')
      ->get();

    $depositPercentageDefault = SettingsHelper::get('deposit_percentage_min', 30);

    return view('pages.admin.reservation-create', compact(
      'clients',
      'villas',
      'depositPercentageDefault'
    ));
  }

  /**
   * Aperçu du calcul tarifaire (AJAX admin).
   */
  public function calculatePrice(Request $request)
  {
    try {
      $validated = $request->validate([
        'villa_id' => ['required', 'exists:villas,id'],
        'check_in' => ['required', 'date'],
        'check_out' => ['required', 'date', 'after:check_in'],
        'guests' => ['required', 'integer', 'min:1'],
        'exclude_reservation_id' => ['nullable', 'integer', 'exists:reservations,id'],
      ]);
    } catch (ValidationException $e) {
      return response()->json([
        'success' => false,
        'message' => 'Erreur de validation.',
        'errors' => $e->errors(),
      ], 422);
    }

    $villa = Villa::with('seasonalPrices.season')
      ->where('is_active', true)
      ->findOrFail($validated['villa_id']);

    $checkIn = Carbon::parse($validated['check_in']);
    $checkOut = Carbon::parse($validated['check_out']);
    $nights = $checkIn->diffInDays($checkOut);

    if ($error = $this->validateVillaBookingRules($villa, $nights, (int) $validated['guests'])) {
      return response()->json([
        'success' => false,
        'message' => $error,
      ], 422);
    }

    $excludeReservationId = isset($validated['exclude_reservation_id'])
      ? (int) $validated['exclude_reservation_id']
      : null;

    if ($this->availabilityService->hasConflict(
      $villa->id,
      $checkIn,
      $checkOut,
      $excludeReservationId,
      VillaAvailabilityContext::admin()
    )) {
      return response()->json([
        'success' => false,
        'message' => 'Ces dates ne sont pas disponibles pour cette villa (réservation ou blocage existant).',
      ], 422);
    }

    $breakdown = $this->buildPriceBreakdown(
      $villa,
      $validated['check_in'],
      $validated['check_out'],
      (int) $validated['guests']
    );

    return response()->json([
      'success' => true,
      ...$breakdown,
      'nights' => $nights,
    ]);
  }

  /**
   * Enregistrer une réservation manuelle (§3.11 CDC).
   */
  public function store(Request $request)
  {
    $validated = $request->validate([
      'user_id' => ['required', 'exists:users,id'],
      'villa_id' => ['required', 'exists:villas,id'],
      'check_in_date' => ['required', 'date'],
      'check_out_date' => ['required', 'date', 'after:check_in_date'],
      'number_of_guests' => ['required', 'integer', 'min:1'],
      'payment_method' => ['required', Rule::in(['bank_transfer', 'check', 'cash', 'other'])],
      'manual_payment_status' => ['required', Rule::in(['pending', 'deposit_paid', 'fully_paid'])],
      'deposit_percentage' => ['nullable', 'integer', 'min:1', 'max:100'],
      'use_custom_total' => ['nullable', 'boolean'],
      'total_price' => ['nullable', 'numeric', 'min:0'],
      'admin_notes' => ['nullable', 'string', 'max:2000'],
      'special_requests' => ['nullable', 'string', 'max:1000'],
    ]);

    $client = User::where('is_admin', false)->findOrFail($validated['user_id']);

    $villa = Villa::with('seasonalPrices.season')
      ->where('is_active', true)
      ->findOrFail($validated['villa_id']);

    $checkInDate = Carbon::parse($validated['check_in_date']);
    $checkOutDate = Carbon::parse($validated['check_out_date']);
    $nights = $checkInDate->diffInDays($checkOutDate);
    $guests = (int) $validated['number_of_guests'];

    if ($message = $this->validateVillaBookingRules($villa, $nights, $guests)) {
      return redirect()->back()->withInput()->with('error', $message);
    }

    if ($this->availabilityService->hasConflict(
      $villa->id,
      $checkInDate,
      $checkOutDate,
      null,
      VillaAvailabilityContext::admin()
    )) {
      return redirect()->back()->withInput()->with(
        'error',
        'Ces dates ne sont pas disponibles pour cette villa (réservation ou blocage existant).'
      );
    }

    $breakdown = $this->buildPriceBreakdown(
      $villa,
      $checkInDate->toDateString(),
      $checkOutDate->toDateString(),
      $guests
    );

    $useCustomTotal = $request->boolean('use_custom_total') && isset($validated['total_price']);
    $total = $useCustomTotal
      ? round((float) $validated['total_price'], 2)
      : $breakdown['total'];

    if ($total <= 0) {
      return redirect()->back()->withInput()->with('error', 'Le montant total doit être supérieur à 0.');
    }

    $depositPercentage = (int) ($validated['deposit_percentage']
      ?? SettingsHelper::get('deposit_percentage_min', 30));
    $depositAmount = round($total * ($depositPercentage / 100), 2);
    $balanceAmount = round($total - $depositAmount, 2);

    $reservationStatus = match ($validated['manual_payment_status']) {
      'fully_paid' => 'fully_paid',
      'deposit_paid' => 'deposit_paid',
      default => 'confirmed',
    };

    try {
      DB::beginTransaction();

      $reservationNumber = $this->generateReservationNumber();

      $reservation = Reservation::create([
        'reservation_number' => $reservationNumber,
        'villa_id' => $villa->id,
        'user_id' => $client->id,
        'guest_first_name' => $client->first_name ?? '',
        'guest_last_name' => $client->last_name ?? '',
        'guest_email' => $client->email,
        'guest_phone' => $client->phone,
        'guest_address' => $client->address,
        'check_in_date' => $checkInDate->toDateString(),
        'check_out_date' => $checkOutDate->toDateString(),
        'number_of_nights' => $nights,
        'number_of_guests' => $guests,
        'adults' => $guests,
        'children' => 0,
        'infants' => 0,
        'base_price' => $breakdown['base_price'],
        'cleaning_fee' => $breakdown['cleaning_fee'],
        'service_fee' => $breakdown['service_fee'],
        'vat_amount' => $breakdown['vat_amount'],
        'tourist_tax' => $breakdown['tourist_tax'],
        'total_price' => $total,
        'currency' => 'EUR',
        'deposit_percentage' => $depositPercentage,
        'deposit_amount' => $depositAmount,
        'balance_amount' => $balanceAmount,
        'deposit_guarantee' => $villa->deposit_amount ?? 0,
        'status' => $reservationStatus,
        'source' => 'manual',
        'special_requests' => $validated['special_requests'] ?? null,
        'admin_notes' => $validated['admin_notes'] ?? null,
        'created_by' => auth()->id(),
      ]);

      $paymentMethod = $validated['payment_method'];
      $now = now();

      $depositPaid = in_array($validated['manual_payment_status'], ['deposit_paid', 'fully_paid'], true);
      $balancePaid = $validated['manual_payment_status'] === 'fully_paid';

      $this->createManualPayment(
        $reservation,
        'deposit',
        $depositAmount,
        $paymentMethod,
        $depositPaid,
        $depositPaid ? $now : null
      );

      if ($balanceAmount > 0) {
        $balanceDueDate = $this->calculateSmartDueDate(
          $checkInDate,
          (int) SettingsHelper::get('balance_due_days_before_checkin', 30)
        );

        $this->createManualPayment(
          $reservation,
          'balance',
          $balanceAmount,
          $paymentMethod,
          $balancePaid,
          $balancePaid ? $now : null,
          $balanceDueDate->toDateString()
        );
      }

      $guaranteeAmount = (float) ($villa->deposit_amount ?? 0);
      if ($guaranteeAmount > 0) {
        $guaranteeDueDate = $this->calculateSmartDueDate(
          $checkInDate,
          (int) SettingsHelper::get('deposit_guarantee_days_before_checkin', 7)
        );

        $guaranteePaid = $validated['manual_payment_status'] === 'fully_paid';

        $this->createManualPayment(
          $reservation,
          'deposit_guarantee',
          $guaranteeAmount,
          $paymentMethod,
          $guaranteePaid,
          $guaranteePaid ? $now : null,
          $guaranteeDueDate->toDateString()
        );
      }

      DB::commit();

      $reservation->load('villa.island');

      try {
        SendReservationConfirmationJob::dispatch($reservation);
      } catch (\Exception $e) {
        \Log::error('Erreur envoi confirmation réservation manuelle: ' . $e->getMessage());
      }

      return redirect()
        ->route('admin.reservations.show', $reservation->id)
        ->with('success', 'Réservation manuelle créée avec succès. Un email de confirmation a été envoyé au client.');
    } catch (\Exception $e) {
      DB::rollBack();
      \Log::error('Erreur création réservation manuelle: ' . $e->getMessage());

      return redirect()
        ->back()
        ->withInput()
        ->with('error', 'Une erreur est survenue lors de la création de la réservation.');
    }
  }

  /**
   * Afficher les détails d'une réservation
   */
  public function show($id)
  {
    $reservation = Reservation::with(['villa.island', 'user', 'payments', 'guests', 'documents'])
      ->findOrFail($id);

    return view('pages.admin.reservation-show', compact('reservation'));
  }

  /**
   * Afficher le formulaire d'édition
   */
  public function edit($id)
  {
    $reservation = Reservation::with(['villa.island', 'user', 'payments', 'guests'])
      ->findOrFail($id);

    $villas = Villa::where('is_active', true)->orderBy('name')->get();

    return view('pages.admin.reservation-edit', compact('reservation', 'villas'));
  }

  /**
   * Mettre à jour une réservation
   */
  public function update(Request $request, $id)
  {
    $reservation = Reservation::with('villa')->findOrFail($id);

    $validated = $request->validate([
      'status' => 'required|in:pending,confirmed,deposit_paid,fully_paid,cancelled,completed',
      'guest_first_name' => 'required|string|max:100',
      'guest_last_name' => 'required|string|max:100',
      'guest_email' => 'required|email|max:255',
      'guest_phone' => 'nullable|string|max:20',
      'check_in_date' => 'required|date',
      'check_out_date' => 'required|date|after:check_in_date',
      'number_of_guests' => 'required|integer|min:1',
      'total_price' => 'required|numeric|min:0',
      'admin_notes' => 'nullable|string',
    ]);

    $checkInDate = Carbon::parse($validated['check_in_date']);
    $checkOutDate = Carbon::parse($validated['check_out_date']);
    $nights = $checkInDate->diffInDays($checkOutDate);
    $guests = (int) $validated['number_of_guests'];

    if ($reservation->villa && ($message = $this->validateVillaBookingRules($reservation->villa, $nights, $guests))) {
      return redirect()->back()->withInput()->with('error', $message);
    }

    if ($this->availabilityService->hasConflict(
      $reservation->villa_id,
      $checkInDate,
      $checkOutDate,
      $reservation->id,
      VillaAvailabilityContext::admin()
    )) {
      return redirect()->back()->withInput()->with(
        'error',
        'Ces dates ne sont pas disponibles pour cette villa (réservation ou blocage existant).'
      );
    }

    $validated['number_of_nights'] = $nights;

    DB::transaction(function () use ($reservation, $validated) {
      $reservation->update($validated);
      $this->syncManualReservationPaymentsFromStatus($reservation, $validated['status']);
    });

    return redirect()->route('admin.reservations.show', $reservation->id)
      ->with('success', 'Réservation mise à jour avec succès');
  }

  /**
   * Annuler une réservation
   */
  public function cancel(Request $request, $id)
  {
    $reservation = Reservation::findOrFail($id);

    $request->validate([
      'cancellation_reason' => 'nullable|string|max:500',
    ]);

    DB::transaction(function () use ($reservation, $request) {
      $reservation->update([
        'status' => 'cancelled',
        'cancellation_reason' => $request->cancellation_reason,
        'cancelled_at' => now(),
        'cancelled_by' => auth()->id(),
      ]);

      $this->syncManualReservationPaymentsFromStatus($reservation->fresh(), 'cancelled');
    });

    try {
      $reservation->loadMissing(['user', 'villa']);
      $this->emailService->sendCancellationEmail($reservation);
    } catch (\Exception $e) {
      \Log::error('Erreur envoi email annulation réservation: ' . $e->getMessage());
    }

    return redirect()->route('admin.reservations')
      ->with('success', 'Réservation annulée avec succès');
  }

  private function buildPriceBreakdown(Villa $villa, string $checkIn, string $checkOut, int $guests): array
  {
    $checkInDate = Carbon::parse($checkIn);
    $checkOutDate = Carbon::parse($checkOut);
    $nights = $checkInDate->diffInDays($checkOutDate);

    $globalTaxRate = SettingsHelper::get('global_tax_rate', 8.5);
    $touristTaxPerNight = SettingsHelper::get('tourist_tax_per_night', 2.50);
    $touristTaxEnabled = SettingsHelper::get('tourist_tax_enabled', true);

    $basePrice = $villa->calculatePriceForPeriod($checkIn, $checkOut);
    $cleaningFee = (float) ($villa->cleaning_fee ?? 0);
    $serviceFeePercentage = $villa->service_fee_percentage ?? SettingsHelper::get('service_fee_percentage', 5);
    $serviceFee = $basePrice * ((float) $serviceFeePercentage / 100);
    $vatAmount = ($cleaningFee + $serviceFee) * ((float) $globalTaxRate / 100);

    $touristTax = 0;
    if ($touristTaxEnabled) {
      $touristTax = (float) $touristTaxPerNight * $guests * $nights;
    }

    $total = round($basePrice + $cleaningFee + $serviceFee + $vatAmount + $touristTax, 2);

    return [
      'base_price' => round($basePrice, 2),
      'cleaning_fee' => round($cleaningFee, 2),
      'service_fee' => round($serviceFee, 2),
      'vat_amount' => round($vatAmount, 2),
      'tourist_tax' => round($touristTax, 2),
      'total' => $total,
    ];
  }

  private function validateVillaBookingRules(Villa $villa, int $nights, int $guests): ?string
  {
    $minStay = (int) ($villa->minimum_stay_nights ?? 3);

    if ($nights < $minStay) {
      return "La durée minimale pour cette villa est de {$minStay} nuit"
        . ($minStay > 1 ? 's' : '')
        . '.';
    }

    if ($guests > (int) $villa->max_capacity) {
      $maxCapacity = (int) $villa->max_capacity;

      return "La capacité maximale de cette villa est de {$maxCapacity} personne"
        . ($maxCapacity > 1 ? 's' : '')
        . '.';
    }

    return null;
  }

  private function generateReservationNumber(): string
  {
    do {
      $number = 'LX-' . strtoupper(Str::random(6)) . '-' . Carbon::now()->format('Y');
    } while (Reservation::where('reservation_number', $number)->exists());

    return $number;
  }

  private function generatePaymentNumber(): string
  {
    do {
      $number = 'PAY-' . strtoupper(Str::random(8)) . '-' . Carbon::now()->format('Y');
    } while (Payment::where('payment_number', $number)->exists());

    return $number;
  }

  private function createManualPayment(
    Reservation $reservation,
    string $type,
    float $amount,
    string $paymentMethod,
    bool $isPaid,
    ?Carbon $paidAt = null,
    ?string $dueDate = null
  ): Payment {
    if ($amount <= 0) {
      return new Payment;
    }

    return Payment::create([
      'reservation_id' => $reservation->id,
      'payment_number' => $this->generatePaymentNumber(),
      'type' => $type,
      'amount' => $amount,
      'currency' => 'EUR',
      'status' => $isPaid ? 'completed' : 'pending',
      'payment_method' => $paymentMethod,
      'due_date' => $dueDate,
      'paid_at' => $paidAt,
      'metadata' => ['source' => 'manual_admin'],
    ]);
  }

  private function calculateSmartDueDate(Carbon $checkInDate, int $daysBefore): Carbon
  {
    $calculatedDate = $checkInDate->copy()->subDays($daysBefore);

    if ($calculatedDate->isPast() || $calculatedDate->diffInDays(now()) < 7) {
      return now()->addDays(7);
    }

    return $calculatedDate;
  }

  /**
   * Maintient la cohérence comptable des paiements manuels lors d'un changement de statut admin.
   */
  private function syncManualReservationPaymentsFromStatus(Reservation $reservation, string $status): void
  {
    if ($reservation->source !== 'manual') {
      return;
    }

    $payments = $reservation->payments()
      ->whereIn('type', ['deposit', 'balance', 'deposit_guarantee'])
      ->get()
      ->keyBy('type');

    if ($payments->isEmpty()) {
      return;
    }

    $syncPayment = function (string $type, string $targetStatus, ?Carbon $paidAt = null) use ($payments): void {
      /** @var Payment|null $payment */
      $payment = $payments->get($type);
      if (! $payment) {
        return;
      }

      $payload = ['status' => $targetStatus];
      $payload['paid_at'] = $targetStatus === 'completed' ? ($payment->paid_at ?? $paidAt ?? now()) : null;
      $payment->update($payload);
    };

    if ($status === 'cancelled') {
      foreach (['deposit', 'balance', 'deposit_guarantee'] as $type) {
        /** @var Payment|null $payment */
        $payment = $payments->get($type);
        if (! $payment || $payment->status === 'completed') {
          continue;
        }

        $payment->update([
          'status' => 'cancelled',
          'paid_at' => null,
        ]);
      }

      return;
    }

    if (in_array($status, ['fully_paid', 'completed'], true)) {
      $syncPayment('deposit', 'completed');
      $syncPayment('balance', 'completed');
      $syncPayment('deposit_guarantee', 'completed');

      return;
    }

    if ($status === 'deposit_paid') {
      $syncPayment('deposit', 'completed');
      $syncPayment('balance', 'pending');
      $syncPayment('deposit_guarantee', 'pending');

      return;
    }

    // pending / confirmed: aucun encaissement validé côté paiements.
    $syncPayment('deposit', 'pending');
    $syncPayment('balance', 'pending');
    $syncPayment('deposit_guarantee', 'pending');
  }
}
