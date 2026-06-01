<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Document;
use App\Models\Payment;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DocumentService
{
    /**
     * Générer un document PDF
     * 
     * @param string $type Type de document (contract, invoice, deposit_receipt, balance_receipt, receipt)
     * @param Reservation $reservation
     * @param Payment|null $payment Pour les reçus
     * @return Document
     */
    public function generateDocument(string $type, Reservation $reservation, ?Payment $payment = null): Document
    {
        // Normaliser le type pour correspondre à l'ENUM de la base de données
        $normalizedType = match($type) {
            'receipt-deposit' => 'deposit_receipt',
            'receipt-balance' => 'balance_receipt',
            'receipt-guarantee' => 'guarantee_receipt',
            default => $type,
        };

        // Vérifier si le document existe déjà
        $existingDocument = Document::where('reservation_id', $reservation->id)
            ->where('type', $normalizedType)
            ->first();

        if ($existingDocument && !in_array($normalizedType, ['deposit_receipt', 'balance_receipt', 'guarantee_receipt'])) {
            return $existingDocument;
        }

        // Charger les données nécessaires
        $reservation->load(['villa.island', 'user', 'payments']);

        // Générer le numéro de document
        $documentNumber = $this->generateDocumentNumber($type, $reservation);

        // Générer le HTML depuis le template Blade
        $html = $this->renderTemplate($type, $reservation, $payment, $documentNumber);

        // Générer le PDF
        $pdf = $this->generatePdf($html);

        // Sauvegarder le fichier
        $fileName = $this->generateFileName($type, $reservation, $documentNumber);
        $filePath = $this->savePdf($pdf, $fileName, $reservation->id);

        // Créer l'enregistrement dans la base de données
        $document = Document::create([
            'reservation_id' => $reservation->id,
            'type' => $normalizedType,
            'document_number' => $documentNumber,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => Storage::disk('public')->size($filePath),
            'mime_type' => 'application/pdf',
            'generated_at' => now(),
        ]);

        return $document;
    }

    /**
     * Générer le numéro de document
     */
    private function generateDocumentNumber(string $type, Reservation $reservation): string
    {
        // Normaliser le type pour la recherche
        $normalizedType = match($type) {
            'receipt-deposit' => 'deposit_receipt',
            'receipt-balance' => 'balance_receipt',
            'receipt-guarantee' => 'guarantee_receipt',
            default => $type,
        };

        $prefix = match($normalizedType) {
            'contract' => 'CONTRACT',
            'invoice' => 'INV',
            'deposit_receipt' => 'REC-DEP',
            'balance_receipt' => 'REC-BAL',
            'guarantee_receipt' => 'REC-GUA',
            'receipt' => 'REC',
            default => 'DOC',
        };

        $year = now()->format('Y');
        $sequence = Document::where('type', $normalizedType)
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('%s-%s-%04d', $prefix, $year, $sequence);
    }

    /**
     * Générer le nom de fichier
     */
    private function generateFileName(string $type, Reservation $reservation, string $documentNumber): string
    {
        $typeLabels = [
            'contract' => 'contrat',
            'invoice' => 'facture',
            'receipt-deposit' => 'recu-arrhes',
            'receipt-balance' => 'recu-solde',
            'receipt-guarantee' => 'recu-caution',
            'receipt' => 'recu',
        ];

        $typeLabel = $typeLabels[$type] ?? 'document';
        $reservationNumber = Str::slug($reservation->reservation_number);

        return sprintf('%s-%s-%s.pdf', $typeLabel, $reservationNumber, $documentNumber);
    }

    /**
     * Rendre le template Blade
     */
    private function renderTemplate(string $type, Reservation $reservation, ?Payment $payment, string $documentNumber): string
    {
        $viewName = match($type) {
            'contract' => 'pdf.contract',
            'invoice' => 'pdf.invoice',
            'receipt-deposit', 'deposit_receipt' => 'pdf.receipt-deposit',
            'receipt-balance', 'balance_receipt' => 'pdf.receipt-balance',
            'receipt-guarantee', 'guarantee_receipt' => 'pdf.receipt-guarantee',
            'receipt' => 'pdf.receipt',
            default => 'pdf.document',
        };

        return view($viewName, [
            'reservation' => $reservation,
            'payment' => $payment,
            'documentNumber' => $documentNumber,
        ])->render();
    }

    /**
     * Générer le PDF depuis le HTML
     */
    private function generatePdf(string $html): Dompdf
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);
        $options->set('defaultFont', 'DejaVu Sans');

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf;
    }

    /**
     * Sauvegarder le PDF
     */
    private function savePdf(Dompdf $pdf, string $fileName, int $reservationId): string
    {
        $directory = "documents/reservations/{$reservationId}";
        $filePath = "{$directory}/{$fileName}";

        Storage::disk('public')->put($filePath, $pdf->output());

        return $filePath;
    }

    /**
     * Télécharger un document
     */
    public function downloadDocument(Document $document)
    {
        $filePath = Storage::disk('public')->path($document->file_path);

        // Si le fichier n'existe pas, essayer de le régénérer
        if (!file_exists($filePath)) {
            try {
                $this->regenerateDocumentFile($document);
                // Re-vérifier après régénération
                if (!file_exists($filePath)) {
                    throw new \Exception('Échec de la régénération du document physique.');
                }
            } catch (\Exception $e) {
                \Log::error("Impossible de régénérer le document {$document->id} : " . $e->getMessage());
                abort(404, 'Document non trouvé et impossible de le régénérer.');
            }
        }

        return response()->download($filePath, $document->file_name, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    /**
     * Régénère le fichier physique d'un document existant
     */
    private function regenerateDocumentFile(Document $document)
    {
        $reservation = $document->reservation;
        $reservation->load(['villa.island', 'user', 'payments']);
        
        $payment = null;
        $type = $document->type;

        // Si c'est un reçu, trouver le paiement correspondant
        if ($type === 'deposit_receipt') {
            $payment = $reservation->payments()
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->first();
        } elseif ($type === 'balance_receipt') {
            $payment = $reservation->payments()
                ->where('type', 'balance')
                ->where('status', 'completed')
                ->first();
        } elseif ($type === 'guarantee_receipt') {
            $payment = $reservation->payments()
                ->where('type', 'deposit_guarantee')
                ->where('status', 'completed')
                ->first();
        }

        // Faire correspondre les types reçus par generateDocument
        $renderType = match($type) {
            'deposit_receipt' => 'receipt-deposit',
            'balance_receipt' => 'receipt-balance',
            'guarantee_receipt' => 'receipt-guarantee',
            default => $type,
        };

        // Générer le HTML depuis le template
        $html = $this->renderTemplate($renderType, $reservation, $payment, $document->document_number);

        // Générer le PDF
        $pdf = $this->generatePdf($html);

        // Sauvegarder le fichier (écrase l'ancien chemin s'il existait mais était vide)
        $this->savePdf($pdf, $document->file_name, $reservation->id);

        // Mettre à jour les métadonnées de l'enregistrement
        $document->update([
            'file_size' => Storage::disk('public')->size($document->file_path),
            'generated_at' => now(),
        ]);
    }

    /**
     * Signer un document
     * 
     * @param Document $document
     * @param int|null $signedBy ID de l'utilisateur qui signe (null = utilisateur connecté)
     * @return Document
     */
    public function signDocument(Document $document, ?int $signedBy = null): Document
    {
        $document->update([
            'is_signed' => true,
            'signed_at' => now(),
            'signed_by' => $signedBy ?? auth()->id(),
        ]);

        return $document->fresh();
    }

    /**
     * Générer automatiquement tous les documents pour une réservation
     * Appelé après la création d'une réservation
     */
    public function generateReservationDocuments(Reservation $reservation): array
    {
        $generated = [];

        try {
            // Générer le contrat
            $contract = $this->generateDocument('contract', $reservation);
            $generated['contract'] = $contract;

            // Générer la facture
            $invoice = $this->generateDocument('invoice', $reservation);
            $generated['invoice'] = $invoice;

            // Générer les reçus si des paiements existent
            $depositPayment = $reservation->payments()
                ->where('type', 'deposit')
                ->where('status', 'completed')
                ->first();

            if ($depositPayment) {
                $receiptDeposit = $this->generateDocument('deposit_receipt', $reservation, $depositPayment);
                $generated['receipt_deposit'] = $receiptDeposit;
            }

            $balancePayment = $reservation->payments()
                ->where('type', 'balance')
                ->where('status', 'completed')
                ->first();

            if ($balancePayment) {
                $receiptBalance = $this->generateDocument('balance_receipt', $reservation, $balancePayment);
                $generated['receipt_balance'] = $receiptBalance;
            }

        } catch (\Exception $e) {
            \Log::error('Erreur lors de la génération des documents pour la réservation ' . $reservation->id . ': ' . $e->getMessage());
        }

        return $generated;
    }
}

