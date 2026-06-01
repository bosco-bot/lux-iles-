<?php
/**
 * Script de diagnostic pour tester l'appel à la route admin/payments/{id}
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Payment;
use Illuminate\Support\Facades\Route;

echo "=== DIAGNOSTIC MODALE PAIEMENT ===\n\n";

try {
    // Tester si la route existe
    $route = Route::getRoutes()->getByName('admin.payments.show');
    if ($route) {
        echo "✅ Route 'admin.payments.show' existe\n";
        echo "   URI: " . $route->uri() . "\n";
        echo "   Méthodes: " . implode(', ', $route->methods()) . "\n";
        echo "   Middleware: " . implode(', ', $route->middleware()) . "\n\n";
    } else {
        echo "❌ Route 'admin.payments.show' n'existe pas\n\n";
        exit(1);
    }

    // Tester avec un paiement existant (celui de l'utilisateur)
    $payment = Payment::where('payment_number', 'PAY-MZL49GPZ-2026')->first();

    if (!$payment) {
        echo "❌ Paiement PAY-MZL49GPZ-2026 non trouvé\n";
        echo "Paiements disponibles :\n";
        $payments = Payment::take(5)->get();
        foreach ($payments as $p) {
            echo "  - {$p->payment_number} (ID: {$p->id})\n";
        }
        echo "\n";
        exit(1);
    }

    echo "✅ Paiement trouvé: {$payment->payment_number} (ID: {$payment->id})\n\n";

    // Tester l'appel à la méthode show du contrôleur
    echo "=== TEST APPEL CONTRÔLEUR ===\n";

    $controller = app(\App\Http\Controllers\Admin\PaymentController::class);
    $request = new \Illuminate\Http\Request();
    $request->setMethod('GET');

    // Simuler un utilisateur admin connecté
    $admin = \App\Models\User::where('is_admin', true)->first();
    if ($admin) {
        \Illuminate\Support\Facades\Auth::login($admin);
        echo "✅ Utilisateur admin simulé: {$admin->email}\n";
    } else {
        echo "⚠️ Aucun utilisateur admin trouvé\n";
    }

    try {
        $response = $controller->show($payment->id);
        echo "✅ Contrôleur répond\n";

        if (method_exists($response, 'getData')) {
            $data = $response->getData(true);
            echo "✅ Réponse JSON valide\n";
            echo "   Clés: " . implode(', ', array_keys($data)) . "\n";

            if (isset($data['payment'])) {
                echo "✅ Données paiement présentes\n";
                echo "   Numéro: {$data['payment']['payment_number']}\n";
                echo "   Statut: {$data['payment']['status']}\n";
            }

            if (isset($data['reservation'])) {
                echo "✅ Données réservation présentes\n";
                echo "   Numéro: {$data['reservation']['reservation_number']}\n";
            }
        } else {
            echo "❌ Réponse inattendue du contrôleur\n";
            echo "   Type: " . gettype($response) . "\n";
        }

    } catch (Exception $e) {
        echo "❌ Erreur contrôleur: " . $e->getMessage() . "\n";
        echo "   Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }

    echo "\n=== RECOMMANDATIONS ===\n";
    echo "1. Vérifier que l'utilisateur est connecté en admin\n";
    echo "2. Vérifier les permissions de la session\n";
    echo "3. Vérifier que le CSRF token est valide\n";
    echo "4. Vérifier les logs Laravel pour les erreurs 403/500\n";

} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
}

echo "\n=== FIN DIAGNOSTIC ===\n";