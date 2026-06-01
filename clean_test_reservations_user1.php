<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

require __DIR__ . '/vendor/autoload.php';

/** @var \Illuminate\Foundation\Application $app */
$app = require __DIR__ . '/bootstrap/app.php';

/** @var \Illuminate\Contracts\Console\Kernel $kernel */
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$userId = 1;

echo "=== Nettoyage des réservations pour user_id = {$userId} ===\n\n";

// 1) Récupérer les réservations concernées
$reservationIds = DB::table('reservations')
    ->where('user_id', $userId)
    ->pluck('id');

if ($reservationIds->isEmpty()) {
    echo "Aucune réservation trouvée pour user_id = {$userId}.\n";
    exit(0);
}

echo "✓ Réservations trouvées : " . implode(', ', $reservationIds->toArray()) . "\n";

// 2) Récupérer les IDs des paiements liés
$paymentIds = DB::table('payments')
    ->whereIn('reservation_id', $reservationIds)
    ->pluck('id');

echo "✓ Paiements trouvés : " . ($paymentIds->count() > 0 ? implode(', ', $paymentIds->toArray()) : 'aucun') . "\n";

// 3) Supprimer les PDFs liés (documents)
$documents = DB::table('documents')
    ->whereIn('reservation_id', $reservationIds)
    ->get();

echo "✓ Documents trouvés : " . $documents->count() . "\n";

$deletedFiles = 0;
foreach ($documents as $doc) {
    $path = $doc->file_path;

    if (!$path) {
        continue;
    }

    // Par défaut, les documents sont stockés sur le disque "local" (storage/app)
    if (Storage::disk('local')->exists($path)) {
        Storage::disk('local')->delete($path);
        $deletedFiles++;
        echo "  → Fichier supprimé : {$path}\n";
    } else {
        echo "  → Fichier introuvable (déjà supprimé ?) : {$path}\n";
    }
}

if ($deletedFiles > 0) {
    echo "✓ Fichiers PDF supprimés : {$deletedFiles}\n";
}

// 4) Supprimer les notifications liées aux réservations et paiements
// Les notifications sont stockées avec des données JSON contenant reservation_id ou payment_id
$deletedNotifications = 0;

// Supprimer les notifications de type "reservation_created"
foreach ($reservationIds as $reservationId) {
    $deleted = DB::table('notifications')
        ->where('type', 'App\Notifications\ReservationCreatedNotification')
        ->whereRaw("JSON_EXTRACT(data, '$.reservation_id') = ?", [$reservationId])
        ->delete();
    $deletedNotifications += $deleted;
}

// Supprimer les notifications de type "payment_received"
foreach ($paymentIds as $paymentId) {
    $deleted = DB::table('notifications')
        ->where('type', 'App\Notifications\PaymentReceivedNotification')
        ->whereRaw("JSON_EXTRACT(data, '$.payment_id') = ?", [$paymentId])
        ->delete();
    $deletedNotifications += $deleted;
}

// Supprimer aussi les notifications qui contiennent les IDs dans leur URL
// (méthode alternative pour être sûr de tout nettoyer)
$allNotifications = DB::table('notifications')->get();
foreach ($allNotifications as $notification) {
    $data = json_decode($notification->data, true);
    
    if (isset($data['reservation_id']) && in_array($data['reservation_id'], $reservationIds->toArray())) {
        DB::table('notifications')->where('id', $notification->id)->delete();
        $deletedNotifications++;
    } elseif (isset($data['payment_id']) && in_array($data['payment_id'], $paymentIds->toArray())) {
        DB::table('notifications')->where('id', $notification->id)->delete();
        $deletedNotifications++;
    }
}

if ($deletedNotifications > 0) {
    echo "✓ Notifications supprimées : {$deletedNotifications}\n";
} else {
    echo "✓ Aucune notification à supprimer\n";
}

// 5) Supprimer les paiements rattachés
$deletedPayments = DB::table('payments')
    ->whereIn('reservation_id', $reservationIds)
    ->delete();

echo "✓ Paiements supprimés en base : {$deletedPayments}\n";

// 6) Supprimer les documents (en base)
$deletedDocuments = DB::table('documents')
    ->whereIn('reservation_id', $reservationIds)
    ->delete();

echo "✓ Documents supprimés en base : {$deletedDocuments}\n";

// 7) Supprimer les réservations
// (les guests + messages liés seront supprimés via ON DELETE CASCADE)
$deletedReservations = DB::table('reservations')
    ->whereIn('id', $reservationIds)
    ->delete();

echo "✓ Réservations supprimées : {$deletedReservations}\n";

// 8) Vérifier les suppressions automatiques via CASCADE
$remainingGuests = DB::table('reservation_guests')
    ->whereIn('reservation_id', $reservationIds)
    ->count();

$remainingMessages = DB::table('messages')
    ->whereIn('reservation_id', $reservationIds)
    ->count();

if ($remainingGuests > 0 || $remainingMessages > 0) {
    echo "\n⚠ ATTENTION : Il reste des données liées :\n";
    if ($remainingGuests > 0) {
        echo "  → Invités supplémentaires : {$remainingGuests}\n";
    }
    if ($remainingMessages > 0) {
        echo "  → Messages : {$remainingMessages}\n";
    }
} else {
    echo "✓ Toutes les données liées ont été supprimées automatiquement (CASCADE)\n";
}

echo "\n=== Nettoyage terminé avec succès ===\n";
echo "Les villas concernées sont maintenant disponibles pour de nouvelles réservations.\n";
