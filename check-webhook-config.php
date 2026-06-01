<?php
/**
 * Script pour vérifier la configuration actuelle du webhook Stripe
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

echo "=== VÉRIFICATION CONFIGURATION WEBHOOK STRIPE ===\n\n";

// Vérifier les clés Stripe
$publicKey = SettingsHelper::get('stripe_public_key');
$secretKey = SettingsHelper::get('stripe_secret_key');
$webhookSecret = SettingsHelper::get('stripe_webhook_secret');

echo "📋 CONFIGURATION ACTUELLE :\n\n";

// Clé publique
if ($publicKey) {
    $mode = str_starts_with($publicKey, 'pk_test_') ? 'TEST' : 'LIVE';
    echo "🔑 Clé publique : " . substr($publicKey, 0, 20) . "... (MODE: $mode)\n";
} else {
    echo "❌ Clé publique : NON CONFIGURÉE\n";
}

// Clé secrète
if ($secretKey) {
    $mode = str_starts_with($secretKey, 'sk_test_') ? 'TEST' : 'LIVE';
    echo "🔐 Clé secrète : " . substr($secretKey, 0, 20) . "... (MODE: $mode)\n";
} else {
    echo "❌ Clé secrète : NON CONFIGURÉE\n";
}

// Secret webhook
echo "\n🎣 WEBHOOK :\n";
if ($webhookSecret) {
    // Détecter le mode selon le secret
    if (str_starts_with($webhookSecret, 'whsec_6StLHCgXPmMQUG9QsZsH9WxHVxK1CWvy')) {
        $mode = 'TEST';
    } elseif (str_starts_with($webhookSecret, 'whsec_iBUdxfrz0zuM9SWWBKsfdvHYzxZMSUlv')) {
        $mode = 'PRODUCTION';
    } else {
        $mode = 'INCONNU';
    }
    
    echo "   Secret : " . substr($webhookSecret, 0, 20) . "...\n";
    echo "   Mode : $mode\n";
    echo "   Longueur : " . strlen($webhookSecret) . " caractères\n";
    
    // Vérifier la cohérence
    if ($publicKey && $secretKey) {
        $keysMode = str_starts_with($publicKey, 'pk_test_') ? 'TEST' : 'LIVE';
        if ($mode === 'TEST' && $keysMode === 'LIVE') {
            echo "\n⚠️  ATTENTION : Les clés sont en mode LIVE mais le webhook est en mode TEST !\n";
        } elseif ($mode === 'PRODUCTION' && $keysMode === 'TEST') {
            echo "\n⚠️  ATTENTION : Les clés sont en mode TEST mais le webhook est en mode PRODUCTION !\n";
        } else {
            echo "\n✅ Configuration cohérente : Clés et webhook en mode $mode\n";
        }
    }
} else {
    echo "   ❌ Secret webhook : NON CONFIGURÉ\n";
    echo "\n💡 Pour configurer :\n";
    echo "   • Mode TEST : php setup-webhook-test.php\n";
    echo "   • Mode PRODUCTION : php setup-webhook-production.php\n";
}

echo "\n=== FIN DE LA VÉRIFICATION ===\n";
