<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Document;
use App\Services\DocumentService;

class SignExistingContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'documents:sign-existing-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Signer rétroactivement les contrats des réservations ayant déjà un acompte payé';

    /**
     * Execute the console command.
     */
    public function handle(DocumentService $documentService)
    {
        $this->info('Recherche des contrats à signer...');

        // Trouver tous les paiements d'acompte complétés
        $completedDeposits = Payment::where('type', 'deposit')
            ->where('status', 'completed')
            ->with(['reservation'])
            ->get();

        $signedCount = 0;
        $skippedCount = 0;

        foreach ($completedDeposits as $payment) {
            if (!$payment->reservation) {
                continue;
            }

            // Trouver le contrat non signé de la réservation
            $contract = Document::where('reservation_id', $payment->reservation->id)
                ->where('type', 'contract')
                ->where('is_signed', false)
                ->first();

            if ($contract) {
                // Signer le contrat
                $documentService->signDocument($contract, $payment->reservation->user_id);
                $signedCount++;
                $this->info("✓ Contrat #{$contract->id} signé pour la réservation #{$payment->reservation->id}");
            } else {
                $skippedCount++;
            }
        }

        $this->info("\nRésumé:");
        $this->info("  - Contrats signés: {$signedCount}");
        $this->info("  - Contrats déjà signés ou non trouvés: {$skippedCount}");
        $this->info("  - Total de paiements d'acompte complétés: " . $completedDeposits->count());
    }
}
