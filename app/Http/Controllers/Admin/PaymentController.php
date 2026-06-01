<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Reservation;
use App\Services\ExportService;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;

class PaymentController extends Controller
{
    /**
     * Afficher la liste des paiements
     */
    public function index(Request $request)
    {
        $query = Payment::with(['reservation.villa.island', 'reservation.user']);

        // Filtre par période
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
                case 'last_30_days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30));
                    break;
            }
        }

        // Filtre par statut
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtre par type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filtre par méthode de paiement
        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        // Recherche par numéro de paiement ou réservation
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhereHas('reservation', function($q) use ($search) {
                      $q->where('reservation_number', 'like', "%{$search}%");
                  });
            });
        }

        // Tri
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Pagination
        $payments = $query->paginate(20)->withQueryString();

        // Statistiques
        $totalAmount = Payment::where('status', 'completed')->sum('amount');
        $pendingCount = Payment::where('status', 'pending')->count();
        $failedCount = Payment::where('status', 'failed')->count();

        return view('pages.admin.payments', compact('payments', 'totalAmount', 'pendingCount', 'failedCount'));
    }

    /**
     * Afficher les détails d'un paiement
     */
    public function show(Request $request, $id)
    {
        try {
            $payment = Payment::with(['reservation.villa.island', 'reservation.user'])->findOrFail($id);

            // Retourner la vue HTML pour les requêtes normales
            if (!$request->expectsJson() && !$request->ajax()) {
                return view('pages.admin.payment-show', compact('payment'));
            }

            // Retourner JSON uniquement pour les requêtes AJAX (modale)
            return response()->json([
                'success' => true,
                'payment' => $payment,
                'reservation' => $payment->reservation,
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Paiement non trouvé'
            ], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la récupération du paiement ' . $id . ': ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur interne du serveur'
            ], 500);
        }
    }

    /**
     * Initier un remboursement
     */
    public function refund(Request $request, $id)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'nullable|string|max:500',
        ]);

        $payment = Payment::with('reservation')->findOrFail($id);

        // Vérifier que le paiement peut être remboursé
        if ($payment->status !== 'completed') {
            return response()->json([
                'success' => false,
                'message' => 'Seuls les paiements complétés peuvent être remboursés.',
            ], 400);
        }

        // Créer un paiement de type refund
        $refund = Payment::create([
            'reservation_id' => $payment->reservation_id,
            'payment_number' => 'REF-' . strtoupper(uniqid()),
            'type' => 'refund',
            'amount' => $request->amount,
            'currency' => $payment->currency,
            'status' => 'pending',
            'payment_method' => $payment->payment_method,
            'metadata' => [
                'original_payment_id' => $payment->id,
                'original_payment_number' => $payment->payment_number,
                'reason' => $request->reason,
            ],
        ]);

        // Mettre à jour le statut du paiement original
        $payment->update(['status' => 'refunded']);

        return response()->json([
            'success' => true,
            'message' => 'Remboursement initié avec succès.',
            'refund' => $refund,
        ]);
    }

    /**
     * Exporter les paiements en CSV ou Excel
     */
    public function export(Request $request, ExportService $exportService)
    {
        $query = Payment::with(['reservation.villa.island', 'reservation.user']);

        // Appliquer les mêmes filtres que dans index()
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
                case 'last_30_days':
                    $query->where('created_at', '>=', $now->copy()->subDays(30));
                    break;
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('payment_number', 'like', "%{$search}%")
                  ->orWhereHas('reservation', function($q) use ($search) {
                      $q->where('reservation_number', 'like', "%{$search}%");
                  });
            });
        }

        // Pas de pagination pour l'export
        $payments = $query->orderBy('created_at', 'desc')->get();

        $format = $request->get('format', 'csv'); // csv ou excel

        if ($format === 'excel') {
            $csv = $exportService->exportPaymentsToExcel($payments, $request->all());
            $filename = 'paiements_' . Carbon::now()->format('Y-m-d_His') . '.xlsx';
            $mimeType = 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet';
        } else {
            $csv = $exportService->exportPaymentsToCsv($payments, $request->all());
            $filename = 'paiements_' . Carbon::now()->format('Y-m-d_His') . '.csv';
            $mimeType = 'text/csv; charset=UTF-8';
        }

        return Response::make($csv, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Encoding' => 'UTF-8',
        ]);
    }
}
