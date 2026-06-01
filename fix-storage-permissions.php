<?php
/**
 * Script pour corriger les permissions du stockage et vérifier la configuration
 */

echo "=== CORRECTION DES PERMISSIONS DE STOCKAGE ===\n\n";

// Fonction pour changer les permissions récursivement
function setPermissions($path, $dirPerm = 0755, $filePerm = 0644) {
    if (!file_exists($path)) {
        echo "❌ Chemin inexistant: {$path}\n";
        return false;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::LEAVES_ONLY
    );

    foreach ($iterator as $file) {
        if ($file->isDir()) {
            chmod($file->getRealPath(), $dirPerm);
        } else {
            chmod($file->getRealPath(), $filePerm);
        }
    }

    // Permissions du dossier racine
    chmod($path, $dirPerm);

    return true;
}

// Vérifier et créer le lien symbolique storage
$storagePath = __DIR__ . '/storage/app/public';
$publicStorageLink = __DIR__ . '/public/storage';

echo "1. Vérification du dossier storage/app/public...\n";
if (file_exists($storagePath)) {
    echo "✅ Dossier existe\n";

    // Corriger les permissions
    echo "2. Correction des permissions...\n";
    if (setPermissions($storagePath)) {
        echo "✅ Permissions corrigées\n";
    }

} else {
    echo "❌ Dossier storage/app/public n'existe pas\n";
}

// Vérifier le lien symbolique
echo "3. Vérification du lien symbolique public/storage...\n";
if (is_link($publicStorageLink)) {
    echo "✅ Lien symbolique existe\n";

    $target = readlink($publicStorageLink);
    if ($target === '../storage/app/public') {
        echo "✅ Lien symbolique correct\n";
    } else {
        echo "⚠️ Lien symbolique pointe vers: {$target}\n";
    }

} elseif (file_exists($publicStorageLink)) {
    echo "⚠️ public/storage existe mais n'est pas un lien symbolique\n";

    // Sauvegarder et recréer
    $backupPath = __DIR__ . '/public/storage.backup.' . time();
    rename($publicStorageLink, $backupPath);
    echo "📁 Sauvegarde créée: {$backupPath}\n";

    if (symlink('../storage/app/public', $publicStorageLink)) {
        echo "✅ Lien symbolique recréé\n";
    } else {
        echo "❌ Échec de création du lien symbolique\n";
    }

} else {
    echo "❌ Lien symbolique manquant\n";

    if (symlink('../storage/app/public', $publicStorageLink)) {
        echo "✅ Lien symbolique créé\n";
    } else {
        echo "❌ Échec de création du lien symbolique\n";
    }
}

// Vérifier l'accès aux fichiers
echo "4. Test d'accès aux fichiers...\n";
$testFile = $storagePath . '/.gitkeep';
if (file_exists($testFile)) {
    echo "✅ Fichier test accessible\n";
} else {
    echo "⚠️ Aucun fichier test trouvé\n";
}

// Lister les documents existants
echo "5. Documents existants...\n";
$documentsPath = $storagePath . '/documents/reservations';
if (file_exists($documentsPath)) {
    $dirs = glob($documentsPath . '/*', GLOB_ONLYDIR);
    echo "📁 Dossiers de réservations: " . count($dirs) . "\n";

    foreach ($dirs as $dir) {
        $reservationId = basename($dir);
        $files = glob($dir . '/*.pdf');
        echo "  Réservation {$reservationId}: " . count($files) . " PDF(s)\n";
    }
} else {
    echo "❌ Dossier documents/reservations inexistant\n";
}

echo "\n=== CONFIGURATION APACHE/NGINX ===\n";
echo "Assurez-vous que votre serveur web permet l'accès aux fichiers dans /public/storage/\n";
echo "Exemple pour Apache (.htaccess):\n";
echo "<Directory \"/path/to/public/storage\">\n";
echo "    Require all granted\n";
echo "</Directory>\n\n";

echo "=== FIN DE LA CORRECTION ===\n";