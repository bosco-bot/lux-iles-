<?php
/**
 * Script pour récupérer les clés Stripe actuelles
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

echo "=== CLÉS STRIPE ACTUELLES ===\n\n";

try {
    // Récupérer les clés depuis les paramètres
    $publicKey = SettingsHelper::get('stripe_public_key');
    $secretKey = SettingsHelper::get('stripe_secret_key');
    $webhookSecret = SettingsHelper::get('stripe_webhook_secret');

    echo "📋 CLÉS STRIPE CONFIGURÉES :\n\n";

    if ($publicKey) {
        echo "🔑 Clé publique (Publishable Key) :\n";
        echo "   {$publicKey}\n\n";

        // Déterminer le mode (test ou live)
        if (str_starts_with($publicKey, 'pk_test_')) {
            echo "   🎯 MODE : TEST\n";
            echo "   ⚠️  À CHANGER pour la production\n\n";
        } elseif (str_starts_with($publicKey, 'pk_live_')) {
            echo "   🎯 MODE : PRODUCTION\n";
            echo "   ✅ Prêt pour la production\n\n";
        } else {
            echo "   ❓ MODE : INCONNU\n\n";
        }
    } else {
        echo "❌ Clé publique NON CONFIGURÉE\n\n";
    }

    if ($secretKey) {
        echo "🔐 Clé secrète (Secret Key) :\n";
        // Masquer partiellement la clé pour la sécurité
        $maskedSecret = substr($secretKey, 0, 10) . str_repeat('*', strlen($secretKey) - 20) . substr($secretKey, -10);
        echo "   {$maskedSecret}\n\n";

        // Déterminer le mode (test ou live)
        if (str_starts_with($secretKey, 'sk_test_')) {
            echo "   🎯 MODE : TEST\n";
            echo "   ⚠️  À CHANGER pour la production\n\n";
        } elseif (str_starts_with($secretKey, 'sk_live_')) {
            echo "   🎯 MODE : PRODUCTION\n";
            echo "   ✅ Prêt pour la production\n\n";
        } else {
            echo "   ❓ MODE : INCONNU\n\n";
        }
    } else {
        echo "❌ Clé secrète NON CONFIGURÉE\n\n";
    }

    if ($webhookSecret) {
        echo "🎣 Secret Webhook :\n";
        $maskedWebhook = substr($webhookSecret, 0, 10) . str_repeat('*', strlen($webhookSecret) - 20) . substr($webhookSecret, -10);
        echo "   {$maskedWebhook}\n\n";
    } else {
        echo "⚠️ Secret Webhook NON CONFIGURÉ\n";
        echo "   (Optionnel, mais recommandé pour la confirmation automatique)\n\n";
    }

    echo "=== INSTRUCTIONS POUR CHANGER LES CLÉS ===\n\n";

    echo "Pour passer en mode PRODUCTION :\n\n";

    echo "1. 📝 Aller sur https://dashboard.stripe.com/apikeys\n\n";

    echo "2. 🔑 Récupérer les clés LIVE :\n";
    echo "   - Publishable key : pk_live_...\n";
    echo "   - Secret key : sk_live_...\n\n";

    echo "3. ⚙️ Configurer dans l'admin :\n";
    echo "   - Aller dans Admin > Paramètres\n";
    echo "   - Section 'Paiement'\n";
    echo "   - Mettre à jour les clés Stripe\n\n";

    echo "4. 🎣 Configurer le webhook (optionnel) :\n";
    echo "   - URL : https://lux-iles.embmission.com/api/stripe/webhook\n";
    echo "   - Événements : payment_intent.succeeded, payment_intent.payment_failed\n\n";

    echo "=== HISTORIQUE DES CLÉS (d'après la documentation) ===\n\n";

    echo "📚 Selon STATUT_INFORMATIONS_CLIENT.md :\n";
    echo "   • Clé publique : pk_test_51Sii3l...\n";
    echo "   • Clé secrète : sk_test_51Sii3l...\n";
    echo "   • Mode : TEST\n";
    echo "   • Note : À passer en PRODUCTION avant mise en ligne\n\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN ===\n";