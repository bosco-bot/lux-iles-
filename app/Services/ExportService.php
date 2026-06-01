<?php

namespace App\Services;

use App\Models\Payment;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ExportService
{
    /**
     * Exporter les paiements en CSV
     */
    public function exportPaymentsToCsv(Collection $payments, array $filters = []): string
    {
        $filename = 'paiements_' . Carbon::now()->format('Y-m-d_His') . '.csv';
        
        $headers = [
            'Date',
            'Numéro de paiement',
            'Numéro de réservation',
            'Client',
            'Villa',
            'Type',
            'Montant',
            'Devise',
            'Statut',
            'Méthode de paiement',
            'Date de paiement',
            'Transaction ID',
        ];

        $rows = [];
        $rows[] = $headers;

        foreach ($payments as $payment) {
            $reservation = $payment->reservation;
            $clientName = 'N/A';
            $villaName = 'N/A';
            $reservationNumber = 'N/A';
            
            if ($reservation) {
                $reservationNumber = $reservation->reservation_number ?? 'N/A';
                $clientName = trim(($reservation->guest_first_name ?? '') . ' ' . ($reservation->guest_last_name ?? '')) ?: 'N/A';
                $villaName = $reservation->villa->name ?? 'N/A';
            }
            
            $rows[] = [
                $payment->created_at->format('d/m/Y H:i'),
                $payment->payment_number,
                $reservationNumber,
                $clientName,
                $villaName,
                $payment->type_label,
                number_format($payment->amount, 2, ',', ' '),
                $payment->currency,
                $payment->status_label,
                $payment->payment_method_label,
                $payment->paid_at ? $payment->paid_at->format('d/m/Y H:i') : 'N/A',
                $payment->transaction_id ?? 'N/A',
            ];
        }

        // Ajouter une ligne de statistiques
        $rows[] = [];
        $rows[] = ['STATISTIQUES', '', '', '', '', '', '', '', '', '', '', ''];
        $rows[] = ['Total collecté', '', '', '', '', '', number_format($payments->where('status', 'completed')->sum('amount'), 2, ',', ' '), 'EUR', '', '', '', ''];
        $rows[] = ['Nombre de paiements', '', '', '', '', '', (string)$payments->count(), '', '', '', '', ''];
        $rows[] = ['Paiements complétés', '', '', '', '', '', (string)$payments->where('status', 'completed')->count(), '', '', '', '', ''];
        $rows[] = ['Paiements en attente', '', '', '', '', '', (string)$payments->where('status', 'pending')->count(), '', '', '', '', ''];

        return $this->arrayToCsv($rows, $filename);
    }

    /**
     * Convertir un tableau en CSV
     */
    private function arrayToCsv(array $data, string $filename): string
    {
        $output = fopen('php://temp', 'r+');
        
        // Ajouter le BOM UTF-8 pour Excel
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        foreach ($data as $row) {
            fputcsv($output, $row, ';');
        }
        
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);
        
        return $csv;
    }

    /**
     * Exporter les paiements en Excel (utilise PhpSpreadsheet si disponible)
     */
    public function exportPaymentsToExcel(Collection $payments, array $filters = []): string
    {
        // Pour l'instant, on utilise CSV avec extension .xlsx
        // TODO: Implémenter avec PhpSpreadsheet si nécessaire
        return $this->exportPaymentsToCsv($payments, $filters);
    }

    /**
     * Obtenir les statistiques d'export
     */
    public function getExportStats(Collection $payments): array
    {
        return [
            'total_amount' => $payments->where('status', 'completed')->sum('amount'),
            'total_count' => $payments->count(),
            'completed_count' => $payments->where('status', 'completed')->count(),
            'pending_count' => $payments->where('status', 'pending')->count(),
            'failed_count' => $payments->where('status', 'failed')->count(),
            'refunded_count' => $payments->where('status', 'refunded')->count(),
        ];
    }
}

