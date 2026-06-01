<?php
/**
 * Script pour configurer le secret webhook Stripe en mode TEST
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

echo "=== CONFIGURATION WEBHOOK STRIPE - MODE TEST ===\n\n";

// Secret webhook TEST
$webhookSecret = 'whsec_6StLHCgXPmMQUG9QsZsH9WxHVxK1CWvy';

try {
    // Vérifier l'ancienne valeur
    $oldSecret = SettingsHelper::get('stripe_webhook_secret');
    
    if ($oldSecret) {
        echo "⚠️  Ancien secret trouvé : " . substr($oldSecret, 0, 20) . "...\n";
        echo "   Il sera remplacé par le secret TEST.\n\n";
    }
    
    // Configurer le nouveau secret
    SettingsHelper::set('stripe_webhook_secret', $webhookSecret, 'string');
    SettingsHelper::clearCache();
    
    // Vérifier la configuration
    $newSecret = SettingsHelper::get('stripe_webhook_secret');
    
    if ($newSecret === $webhookSecret) {
        echo "✅ Secret webhook TEST configuré avec succès !\n\n";
        echo "📋 Détails :\n";
        echo "   Mode : TEST\n";
        echo "   Secret : " . substr($webhookSecret, 0, 20) . "...\n";
        echo "   Longueur : " . strlen($webhookSecret) . " caractères\n\n";
        
        echo "🎯 Prochaines étapes :\n";
        echo "   1. Tester un paiement avec les clés TEST\n";
        echo "   2. Vérifier les logs : storage/logs/laravel.log\n";
        echo "   3. Envoyer un test webhook depuis Stripe Dashboard\n\n";
        
        echo "💡 Pour passer en PRODUCTION plus tard :\n";
        echo "   Exécutez : php setup-webhook-production.php\n\n";
        
    } else {
        echo "❌ Erreur : Le secret n'a pas été correctement configuré.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la configuration : " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== CONFIGURATION TERMINÉE ===\n";
