<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Reservation;
use App\Models\Payment;
use App\Services\DocumentService;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    protected $documentService;

    public function __construct(DocumentService $documentService)
    {
        $this->documentService = $documentService;
    }

    /**
     * Générer un contrat pour une réservation
     */
    public function generateContract(Reservation $reservation)
    {
        $document = $this->documentService->generateDocument('contract', $reservation);
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Générer une facture pour une réservation
     */
    public function generateInvoice(Reservation $reservation)
    {
        $document = $this->documentService->generateDocument('invoice', $reservation);
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Générer un reçu d'arrhes
     */
    public function generateReceiptDeposit(Reservation $reservation, Payment $payment)
    {
        $document = $this->documentService->generateDocument('receipt-deposit', $reservation, $payment);
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Générer un reçu de solde
     */
    public function generateReceiptBalance(Reservation $reservation, Payment $payment)
    {
        $document = $this->documentService->generateDocument('receipt-balance', $reservation, $payment);
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Télécharger un document existant
     */
    public function download(Document $document)
    {
        // Vérifier que l'utilisateur connecté a le droit de télécharger ce document
        $user = auth()->user();
        
        // Si l'utilisateur n'est pas admin, vérifier que le document appartient à une de ses réservations
        if (!$user->is_admin) {
            if (!$document->reservation || $document->reservation->user_id !== $user->id) {
                abort(403, 'Vous n\'avez pas le droit de télécharger ce document.');
            }
        }
        
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Générer un document par type (route générique)
     */
    public function generate(Request $request, Reservation $reservation)
    {
        $request->validate([
            'type' => 'required|in:contract,invoice,receipt-deposit,receipt-balance,deposit_receipt,balance_receipt',
            'payment_id' => 'nullable|exists:payments,id',
        ]);

        $payment = $request->payment_id ? Payment::findOrFail($request->payment_id) : null;
        $document = $this->documentService->generateDocument($request->type, $reservation, $payment);
        
        return $this->documentService->downloadDocument($document);
    }

    /**
     * Marquer un document comme signé (Admin uniquement)
     */
    public function sign(Request $request, Document $document)
    {
        // Vérifier que l'utilisateur est admin
        if (!auth()->user()->is_admin) {
            abort(403, 'Accès réservé aux administrateurs.');
        }

        $document = $this->documentService->signDocument($document);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Document marqué comme signé avec succès.',
                'document' => [
                    'id' => $document->id,
                    'is_signed' => $document->is_signed,
                    'signed_at' => $document->signed_at?->format('d/m/Y H:i'),
                    'signed_by' => $document->signer?->first_name . ' ' . $document->signer?->last_name,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Document marqué comme signé avec succès.');
    }
}
