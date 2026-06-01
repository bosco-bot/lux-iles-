<?php
/**
 * Script de test pour vérifier la génération de contrats
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Reservation;
use App\Services\DocumentService;

echo "=== TEST GÉNÉRATION DE CONTRAT ===\n\n";

try {
    // Trouver une réservation récente
    $reservation = Reservation::where('status', 'deposit_paid')
        ->orderBy('created_at', 'desc')
        ->first();

    if (!$reservation) {
        echo "❌ Aucune réservation avec paiement d'arrhes trouvée\n";
        echo "Créons une réservation de test...\n\n";

        // Créer une réservation de test si nécessaire
        $reservation = Reservation::create([
            'reservation_number' => 'TEST-' . strtoupper(uniqid()),
            'villa_id' => 7,
            'user_id' => 1,
            'guest_first_name' => 'Test',
            'guest_last_name' => 'User',
            'guest_email' => 'test@example.com',
            'check_in_date' => now()->addDays(30)->toDateString(),
            'check_out_date' => now()->addDays(37)->toDateString(),
            'number_of_nights' => 7,
            'number_of_guests' => 2,
            'adults' => 2,
            'children' => 0,
            'infants' => 0,
            'base_price' => 22400.00,
            'cleaning_fee' => 500.00,
            'service_fee' => 1120.00,
            'vat_amount' => 162.00,
            'tourist_tax' => 42.00,
            'total_price' => 24224.00,
            'currency' => 'EUR',
            'deposit_percentage' => 30,
            'deposit_amount' => 7267.20,
            'balance_amount' => 16956.80,
            'status' => 'deposit_paid',
            'source' => 'direct',
            'created_by' => 1,
        ]);

        echo "✅ Réservation de test créée: {$reservation->reservation_number}\n\n";
    }

    echo "Réservation trouvée: {$reservation->reservation_number}\n";
    echo "Statut: {$reservation->status}\n";
    echo "Villa: {$reservation->villa->name ?? 'N/A'}\n\n";

    // Tester la génération de contrat
    echo "=== TEST GÉNÉRATION CONTRAT ===\n";

    $documentService = app(DocumentService::class);

    try {
        $contract = $documentService->generateDocument('contract', $reservation);

        echo "✅ Contrat généré avec succès!\n";
        echo "Nom du fichier: {$contract->file_name}\n";
        echo "Chemin: {$contract->file_path}\n";
        echo "Taille: {$contract->file_size} octets\n\n";

        // Vérifier que le fichier existe
        $filePath = storage_path('app/public/' . $contract->file_path);
        if (file_exists($filePath)) {
            echo "✅ Fichier existe physiquement: $filePath\n";

            // URL d'accès
            $url = asset('storage/' . $contract->file_path);
            echo "🌐 URL d'accès: $url\n\n";

            echo "=== TEST RÉUSSI ===\n";
            echo "Le bouton 'Télécharger le contrat' devrait fonctionner !\n";

        } else {
            echo "❌ Fichier introuvable: $filePath\n";
        }

    } catch (Exception $e) {
        echo "❌ Erreur génération contrat: {$e->getMessage()}\n";
        echo "Stack trace: {$e->getTraceAsString()}\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur générale: {$e->getMessage()}\n";
}

echo "\n=== FIN TEST ===\n";