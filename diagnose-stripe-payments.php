<?php
/**
 * Diagnostic complet des paiements Stripe
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;
use App\Models\Payment;

echo "=== DIAGNOSTIC COMPLET PAIEMENTS STRIPE ===\n\n";

try {
    // 1. Vérification des clés Stripe
    echo "1️⃣ CLÉS STRIPE CONFIGURÉES :\n";
    $publicKey = SettingsHelper::get('stripe_public_key');
    $secretKey = SettingsHelper::get('stripe_secret_key');
    $webhookSecret = SettingsHelper::get('stripe_webhook_secret');

    if ($publicKey) {
        $mode = str_starts_with($publicKey, 'pk_test_') ? 'TEST' : 'LIVE';
        echo "   🔑 Publique : " . substr($publicKey, 0, 20) . "... (MODE: $mode)\n";
    } else {
        echo "   ❌ Clé publique NON CONFIGURÉE\n";
    }

    if ($secretKey) {
        $mode = str_starts_with($secretKey, 'sk_test_') ? 'TEST' : 'LIVE';
        echo "   🔐 Secrète : " . substr($secretKey, 0, 20) . "... (MODE: $mode)\n";
    } else {
        echo "   ❌ Clé secrète NON CONFIGURÉE\n";
    }

    if ($webhookSecret) {
        echo "   🎣 Webhook : CONFIGURÉ\n";
    } else {
        echo "   ⚠️ Webhook : NON CONFIGURÉ (recommandé)\n";
    }

    echo "\n";

    // 2. Vérification des paiements en base
    echo "2️⃣ PAIEMENTS EN BASE DE DONNÉES :\n";
    $totalPayments = Payment::count();
    $pendingPayments = Payment::where('status', 'pending')->count();
    $completedPayments = Payment::where('status', 'completed')->count();
    $failedPayments = Payment::where('status', 'failed')->count();

    echo "   📊 Total : $totalPayments paiements\n";
    echo "   ⏳ En attente : $pendingPayments\n";
    echo "   ✅ Complétés : $completedPayments\n";
    echo "   ❌ Échoués : $failedPayments\n";

    if ($totalPayments > 0) {
        echo "\n   📋 DERNIERS PAIEMENTS :\n";
        $recentPayments = Payment::orderBy('created_at', 'desc')->take(3)->get();

        foreach ($recentPayments as $payment) {
            $intentId = $payment->stripe_payment_intent_id ? substr($payment->stripe_payment_intent_id, -10) : 'N/A';
            echo "      • {$payment->payment_number} ({$payment->status}) - Intent: ...{$intentId}\n";
        }
    }

    echo "\n";

    // 3. Analyse du problème
    echo "3️⃣ ANALYSE DU PROBLÈME :\n";

    if (str_starts_with($publicKey ?? '', 'pk_test_')) {
        echo "   🎯 CAUSE PRINCIPALE : CLÉS EN MODE TEST\n";
        echo "   📝 Les paiements avec clés TEST n'apparaissent PAS dans le dashboard Stripe LIVE\n";
        echo "   🔍 Vérifiez que vous consultez le bon dashboard Stripe (test ou live)\n\n";

        echo "   ✅ SOLUTION : Consulter https://dashboard.stripe.com/test\n";
        echo "   ⚠️ OU passer aux clés LIVE pour la production\n\n";
    } elseif (!$webhookSecret) {
        echo "   🎣 CAUSE POSSIBLE : WEBHOOK NON CONFIGURÉ\n";
        echo "   📝 Sans webhook, les paiements peuvent être créés mais pas confirmés\n";
        echo "   🔄 Le processus peut rester 'en attente'\n\n";
    } elseif ($pendingPayments > 0) {
        echo "   ⏳ PAIEMENTS EN ATTENTE DÉTECTÉS\n";
        echo "   📝 Les paiements sont créés côté application mais pas confirmés\n";
        echo "   🔍 Vérifier les logs Stripe et les webhooks\n\n";
    }

    // 4. Recommandations
    echo "4️⃣ RECOMMANDATIONS :\n";

    if (str_starts_with($publicKey ?? '', 'pk_test_')) {
        echo "   🔍 1. Vérifiez le DASHBOARD STRIPE :\n";
        echo "      • Mode TEST : https://dashboard.stripe.com/test/payments\n";
        echo "      • Mode LIVE : https://dashboard.stripe.com/payments\n\n";

        echo "   ⚙️ 2. POUR LA PRODUCTION :\n";
        echo "      • Récupérer les clés LIVE sur https://dashboard.stripe.com/apikeys\n";
        echo "      • Les remplacer dans Admin > Paramètres > Paiement\n\n";
    }

    echo "   🎣 3. CONFIGURER LE WEBHOOK (recommandé) :\n";
    echo "      • URL : https://votre-domaine.com/api/stripe/webhook\n";
    echo "      • Événements : payment_intent.succeeded, payment_intent.payment_failed\n";
    echo "      • Copier le 'Signing secret' dans les paramètres\n\n";

    echo "   🧪 4. TEST DE PAIEMENT :\n";
    echo "      • Utiliser une carte test Stripe : 4242 4242 4242 4242\n";
    echo "      • Vérifier que le paiement apparaît dans le bon dashboard\n\n";

    // 5. Test de connexion Stripe
    echo "5️⃣ TEST DE CONNEXNION STRIPE :\n";
    if ($secretKey) {
        try {
            \Stripe\Stripe::setApiKey($secretKey);
            $balance = \Stripe\Balance::retrieve();
            echo "   ✅ Connexion Stripe : RÉUSSIE\n";
            echo "   💰 Solde disponible : " . ($balance->available[0]->amount / 100) . " " . strtoupper($balance->available[0]->currency) . "\n";
        } catch (\Exception $e) {
            echo "   ❌ Connexion Stripe : ÉCHEC\n";
            echo "   📝 Erreur : " . $e->getMessage() . "\n";
        }
    } else {
        echo "   ❌ Impossible de tester : clé secrète manquante\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur lors du diagnostic : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DU DIAGNOSTIC ===\n";