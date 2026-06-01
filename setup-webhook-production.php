<?php
/**
 * Script pour configurer le secret webhook Stripe en mode PRODUCTION
 * 
 * ⚠️ ATTENTION : Utilisez ce script uniquement quand vous êtes prêt pour la production !
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

echo "=== CONFIGURATION WEBHOOK STRIPE - MODE PRODUCTION ===\n\n";

// ⚠️ Vérification avant de continuer
echo "⚠️  ATTENTION : Vous êtes sur le point de configurer le webhook PRODUCTION.\n";
echo "   Assurez-vous que :\n";
echo "   • Les clés Stripe sont en mode LIVE (pk_live_... et sk_live_...)\n";
echo "   • L'endpoint webhook PRODUCTION est créé dans Stripe Dashboard\n";
echo "   • Vous êtes prêt à recevoir de vrais paiements\n\n";

// Demander confirmation
echo "Voulez-vous continuer ? (oui/non) : ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
$confirmation = trim(strtolower($line));
fclose($handle);

if ($confirmation !== 'oui' && $confirmation !== 'o' && $confirmation !== 'yes' && $confirmation !== 'y') {
    echo "\n❌ Configuration annulée.\n";
    exit(0);
}

echo "\n";

// Secret webhook PRODUCTION
$webhookSecret = 'whsec_iBUdxfrz0zuM9SWWBKsfdvHYzxZMSUlv';

try {
    // Vérifier l'ancienne valeur
    $oldSecret = SettingsHelper::get('stripe_webhook_secret');
    
    if ($oldSecret) {
        $oldMode = str_starts_with($oldSecret, 'whsec_6StL') ? 'TEST' : 'PRODUCTION';
        echo "⚠️  Ancien secret trouvé (Mode: $oldMode) : " . substr($oldSecret, 0, 20) . "...\n";
        echo "   Il sera remplacé par le secret PRODUCTION.\n\n";
    }
    
    // Vérifier les clés Stripe actuelles
    $publicKey = SettingsHelper::get('stripe_public_key');
    $secretKey = SettingsHelper::get('stripe_secret_key');
    
    if ($publicKey && str_starts_with($publicKey, 'pk_test_')) {
        echo "⚠️  ATTENTION : Les clés Stripe sont encore en mode TEST !\n";
        echo "   Clé publique : " . substr($publicKey, 0, 20) . "...\n";
        echo "   Vous devez d'abord changer les clés Stripe pour le mode LIVE.\n\n";
        
        echo "Voulez-vous quand même continuer ? (oui/non) : ";
        $handle = fopen("php://stdin", "r");
        $line = fgets($handle);
        $confirmation2 = trim(strtolower($line));
        fclose($handle);
        
        if ($confirmation2 !== 'oui' && $confirmation2 !== 'o' && $confirmation2 !== 'yes' && $confirmation2 !== 'y') {
            echo "\n❌ Configuration annulée.\n";
            exit(0);
        }
        echo "\n";
    }
    
    // Configurer le nouveau secret
    SettingsHelper::set('stripe_webhook_secret', $webhookSecret, 'string');
    SettingsHelper::clearCache();
    
    // Vérifier la configuration
    $newSecret = SettingsHelper::get('stripe_webhook_secret');
    
    if ($newSecret === $webhookSecret) {
        echo "✅ Secret webhook PRODUCTION configuré avec succès !\n\n";
        echo "📋 Détails :\n";
        echo "   Mode : PRODUCTION\n";
        echo "   Secret : " . substr($webhookSecret, 0, 20) . "...\n";
        echo "   Longueur : " . strlen($webhookSecret) . " caractères\n\n";
        
        echo "🎯 Prochaines étapes :\n";
        echo "   1. Vérifier que les clés Stripe sont en mode LIVE\n";
        echo "   2. Tester un paiement avec une carte test Stripe\n";
        echo "   3. Vérifier les logs : storage/logs/laravel.log\n";
        echo "   4. Envoyer un test webhook depuis Stripe Dashboard (mode LIVE)\n\n";
        
        echo "✅ Votre application est maintenant configurée pour la PRODUCTION !\n\n";
        
    } else {
        echo "❌ Erreur : Le secret n'a pas été correctement configuré.\n";
        exit(1);
    }
    
} catch (Exception $e) {
    echo "❌ Erreur lors de la configuration : " . $e->getMessage() . "\n";
    exit(1);
}

echo "=== CONFIGURATION TERMINÉE ===\n";
