<?php
/**
 * Script pour nettoyer les documents dupliqués
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Document;
use Illuminate\Support\Facades\Storage;

echo "=== NETTOYAGE DES DOCUMENTS DUPLIQUÉS ===\n\n";

try {
    // Trouver les documents dupliqués pour la réservation 13
    $duplicates = Document::where('reservation_id', 13)
        ->where('type', 'deposit_receipt')
        ->orderBy('created_at', 'desc')
        ->get();

    echo "Documents trouvés pour la réservation 13 (reçu d'arrhes) : " . $duplicates->count() . "\n\n";

    if ($duplicates->count() > 1) {
        echo "📋 Documents dupliqués détectés :\n";

        $keep = $duplicates->first(); // Garder le plus récent
        $toDelete = $duplicates->skip(1); // Supprimer les autres

        echo "✅ À conserver : {$keep->file_name} (créé le {$keep->created_at})\n";

        foreach ($toDelete as $doc) {
            echo "🗑️ À supprimer : {$doc->file_name} (créé le {$doc->created_at})\n";

            // Supprimer le fichier physique
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
                echo "   📄 Fichier supprimé : {$doc->file_path}\n";
            }

            // Supprimer l'enregistrement en base
            $doc->delete();
            echo "   🗃️ Enregistrement supprimé\n";
        }

        echo "\n✅ Nettoyage terminé ! Document conservé : {$keep->file_name}\n";
        echo "🌐 URL d'accès : " . asset('storage/' . $keep->file_path) . "\n";

    } else {
        echo "✅ Aucun document dupliqué trouvé pour la réservation 13.\n";
    }

    // Vérifier tous les autres documents pour détecter d'autres doublons
    echo "\n=== VÉRIFICATION GLOBALE DES DOUBLONS ===\n";

    $allDuplicates = DB::select("
        SELECT reservation_id, type, COUNT(*) as count
        FROM documents
        GROUP BY reservation_id, type
        HAVING count > 1
        ORDER BY count DESC
    ");

    if (count($allDuplicates) > 0) {
        echo "📋 Autres doublons trouvés :\n";
        foreach ($allDuplicates as $dup) {
            echo "   Réservation {$dup->reservation_id} - Type {$dup->type} : {$dup->count} exemplaires\n";
        }
        echo "\n⚠️ Pour nettoyer tous les doublons, exécutez ce script avec le paramètre --all\n";
    } else {
        echo "✅ Aucun autre doublon détecté.\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";