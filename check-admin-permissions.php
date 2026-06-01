<?php
/**
 * Script pour vérifier les permissions admin de l'utilisateur actuel
 */

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;

echo "=== VÉRIFICATION PERMISSIONS ADMIN ===\n\n";

try {
    // Récupérer l'utilisateur connecté (simulé pour test)
    echo "Vérification des utilisateurs admin :\n\n";

    $admins = User::where('is_admin', true)->where('is_active', true)->get();

    if ($admins->count() === 0) {
        echo "❌ AUCUN UTILISATEUR ADMIN ACTIF TROUVÉ\n\n";
        echo "=== CRÉATION D'UN COMPTE ADMIN ===\n\n";

        // Créer un compte admin si aucun n'existe
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'Principal',
            'email' => 'admin@lux-iles.com',
            'password' => bcrypt('password123'),
            'phone' => '+33123456789',
            'is_admin' => true,
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        echo "✅ Compte admin créé :\n";
        echo "   Email: {$admin->email}\n";
        echo "   Mot de passe: password123\n";
        echo "   Prénom: {$admin->first_name}\n";
        echo "   Nom: {$admin->last_name}\n\n";

    } else {
        echo "✅ Utilisateurs admin actifs trouvés :\n\n";
        foreach ($admins as $admin) {
            echo "👤 {$admin->first_name} {$admin->last_name}\n";
            echo "   Email: {$admin->email}\n";
            echo "   Admin: " . ($admin->is_admin ? '✅' : '❌') . "\n";
            echo "   Actif: " . ($admin->is_active ? '✅' : '❌') . "\n";
            echo "   Dernière connexion: " . ($admin->last_login_at ? $admin->last_login_at->format('d/m/Y H:i') : 'Jamais') . "\n\n";
        }
    }

    echo "=== INSTRUCTIONS DE CONNEXION ===\n\n";

    if ($admins->count() > 0) {
        $admin = $admins->first();
        echo "1. Allez sur : https://lux-iles.embmission.com/admin/login\n";
        echo "2. Connectez-vous avec :\n";
        echo "   Email: {$admin->email}\n";
        echo "   Mot de passe: [celui que vous utilisez]\n\n";
    }

    echo "3. Une fois connecté, allez sur : https://lux-iles.embmission.com/admin/payments\n";
    echo "4. Testez la modale en cliquant sur l'icône œil\n\n";

    echo "=== SI LE PROBLÈME PERSISTE ===\n\n";
    echo "1. Videz le cache navigateur (Ctrl+F5)\n";
    echo "2. Supprimez les cookies du domaine lux-iles.embmission.com\n";
    echo "3. Reconnectez-vous\n\n";

    echo "=== VÉRIFICATION TECHNIQUE ===\n\n";
    echo "Si vous êtes déjà connecté en admin :\n";
    echo "1. Ouvrez les outils développeur (F12)\n";
    echo "2. Onglet Application > Cookies\n";
    echo "3. Vérifiez qu'il y a un cookie 'laravel_session'\n";
    echo "4. Vérifiez qu'il y a un cookie 'XSRF-TOKEN'\n\n";

} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\n=== FIN DIAGNOSTIC ===\n";