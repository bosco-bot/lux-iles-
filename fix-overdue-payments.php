<?php
/**
 * Script pour corriger les paiements dont les échéances sont dépassées
 * Définit des nouvelles échéances raisonnables pour les réservations last-minute
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use App\Models\Reservation;
use Carbon\Carbon;

echo "=== CORRECTION DES ÉCHÉANCES DÉPASSÉES ===\n\n";

try {
    // Trouver tous les paiements en attente avec échéance dépassée
    $overduePayments = Payment::where('status', '!=', 'completed')
        ->where('due_date', '<', now()->toDateString())
        ->whereNotNull('due_date')
        ->with('reservation')
        ->get();

    echo "Paiements en attente avec échéance dépassée trouvés : " . $overduePayments->count() . "\n\n";

    $corrected = 0;

    foreach ($overduePayments as $payment) {
        $oldDueDate = $payment->due_date;
        $reservation = $payment->reservation;

        if ($reservation) {
            // Calculer une nouvelle échéance raisonnable
            $checkInDate = Carbon::parse($reservation->check_in_date);
            $daysSinceReservation = Carbon::parse($reservation->created_at)->diffInDays(now());

            // Nouvelle logique : minimum 7 jours après la date de réservation
            $newDueDate = Carbon::parse($reservation->created_at)->addDays(7);

            // Si la réservation a été faite il y a plus de 7 jours, ajouter encore 7 jours
            if ($daysSinceReservation > 7) {
                $newDueDate = now()->addDays(7);
            }

            // Mettre à jour la date d'échéance
            $payment->update(['due_date' => $newDueDate->toDateString()]);

            echo "✅ Paiement {$payment->payment_number} ({$payment->type})\n";
            echo "   Ancienne échéance: {$oldDueDate}\n";
            echo "   Nouvelle échéance: {$newDueDate->format('d M Y')}\n";
            echo "   Réservation: {$reservation->reservation_number}\n\n";

            $corrected++;
        }
    }

    echo "=== RÉSULTATS ===\n";
    echo "Paiements corrigés: {$corrected}\n";

    if ($corrected > 0) {
        echo "\n🎉 Les échéances dépassées ont été corrigées !\n";
        echo "Les clients auront maintenant des délais raisonnables pour payer.\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";