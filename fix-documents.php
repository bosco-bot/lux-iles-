<?php
/**
 * Script pour générer les documents manquants pour les réservations existantes
 * À exécuter sur le serveur de production
 */

require_once __DIR__ . '/vendor/autoload.php';

// Configuration Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Reservation;
use App\Services\DocumentService;

echo "=== GÉNÉRATION DES DOCUMENTS MANQUANTS ===\n\n";

$documentService = app(DocumentService::class);
$totalGenerated = 0;
$errors = 0;

try {
    // Récupérer toutes les réservations non annulées
    $reservations = Reservation::where('status', '!=', 'cancelled')
        ->with(['payments', 'villa'])
        ->get();

    echo "Nombre de réservations trouvées: " . $reservations->count() . "\n\n";

    foreach ($reservations as $reservation) {
        echo "Traitement réservation #{$reservation->id} ({$reservation->reservation_number})...\n";

        try {
            // Générer les documents
            $generated = $documentService->generateReservationDocuments($reservation);

            if (count($generated) > 0) {
                echo "  ✅ Documents générés: " . count($generated) . "\n";
                foreach ($generated as $type => $document) {
                    echo "    - {$type}: {$document->file_name}\n";
                }
                $totalGenerated += count($generated);
            } else {
                echo "  ⚠️ Aucun document généré\n";
            }

        } catch (Exception $e) {
            echo "  ❌ Erreur: " . $e->getMessage() . "\n";
            $errors++;
        }

        echo "\n";
    }

    echo "=== RÉSULTATS ===\n";
    echo "Documents générés: {$totalGenerated}\n";
    echo "Erreurs: {$errors}\n";

    // Vérifier les permissions
    echo "\n=== VÉRIFICATION DES PERMISSIONS ===\n";

    $storagePath = storage_path('app/public');
    if (file_exists($storagePath)) {
        echo "✅ Dossier storage/app/public existe\n";

        // Vérifier les permissions
        $perms = substr(sprintf('%o', fileperms($storagePath)), -4);
        echo "Permissions storage/app/public: {$perms}\n";

        if ($perms >= '0755') {
            echo "✅ Permissions correctes\n";
        } else {
            echo "⚠️ Permissions insuffisantes, correction...\n";
            chmod($storagePath, 0755);
            echo "✅ Permissions corrigées\n";
        }

    } else {
        echo "❌ Dossier storage/app/public n'existe pas\n";
    }

    // Vérifier le lien symbolique
    $publicStorageLink = public_path('storage');
    if (is_link($publicStorageLink)) {
        echo "✅ Lien symbolique public/storage existe\n";
    } else {
        echo "⚠️ Lien symbolique public/storage manquant\n";
    }

} catch (Exception $e) {
    echo "❌ ERREUR GLOBALE: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU SCRIPT ===\n";