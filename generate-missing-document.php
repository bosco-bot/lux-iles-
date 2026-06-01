<?php
/**
 * Script pour générer le document manquant pour la réservation 13
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Reservation;
use App\Models\Payment;
use App\Services\DocumentService;

echo "=== GÉNÉRATION DU DOCUMENT MANQUANT POUR RÉSERVATION 13 ===\n\n";

try {
    // Trouver la réservation 13
    $reservation = Reservation::find(13);

    if (!$reservation) {
        echo "❌ Réservation 13 non trouvée\n";
        exit(1);
    }

    echo "Réservation trouvée: {$reservation->reservation_number}\n";
    echo "Statut: {$reservation->status}\n\n";

    // Trouver le paiement d'arrhes complété
    $depositPayment = $reservation->payments()
        ->where('type', 'deposit')
        ->where('status', 'completed')
        ->first();

    if (!$depositPayment) {
        echo "❌ Aucun paiement d'arrhes complété trouvé\n";
        exit(1);
    }

    echo "Paiement d'arrhes trouvé: {$depositPayment->payment_number} ({$depositPayment->amount}€)\n\n";

    // Générer le document
    $documentService = app(DocumentService::class);
    $document = $documentService->generateDocument('deposit_receipt', $reservation, $depositPayment);

    echo "✅ Document généré avec succès!\n";
    echo "Nom du fichier: {$document->file_name}\n";
    echo "Chemin: {$document->file_path}\n";
    echo "Taille: {$document->file_size} octets\n\n";

    // Vérifier que le fichier existe physiquement
    $filePath = storage_path('app/public/' . $document->file_path);
    if (file_exists($filePath)) {
        echo "✅ Fichier créé physiquement: {$filePath}\n";

        // Corriger les permissions
        chmod($filePath, 0644);
        echo "✅ Permissions du fichier corrigées\n";

        // URL d'accès
        $url = asset('storage/' . $document->file_path);
        echo "🌐 URL d'accès: {$url}\n";

    } else {
        echo "❌ Fichier non trouvé physiquement: {$filePath}\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIN ===\n";