#!/bin/bash

# Script pour diagnostiquer le problème de storage

echo "🔍 Diagnostic du problème de storage..."
echo ""

# Vérifier le lien symbolique
echo "1️⃣  Vérification du lien symbolique:"
if [ -L "public/storage" ]; then
    echo "   ✅ Le lien existe"
    echo "   📍 Pointe vers: $(readlink -f public/storage)"
    ls -la public/storage
else
    echo "   ❌ Le lien n'existe pas ou est cassé"
fi

echo ""
echo "2️⃣  Vérification du dossier source:"
if [ -d "storage/app/public" ]; then
    echo "   ✅ Le dossier storage/app/public existe"
    echo "   📁 Contenu:"
    ls -la storage/app/public/ | head -10
else
    echo "   ❌ Le dossier storage/app/public n'existe pas"
fi

echo ""
echo "3️⃣  Vérification des permissions:"
echo "   Permissions de storage/app/public:"
ls -ld storage/app/public 2>/dev/null || echo "   ⚠️  Impossible de lire les permissions"

echo ""
echo "4️⃣  Vérification de la configuration .env:"
if [ -f ".env" ]; then
    echo "   APP_URL:"
    grep "APP_URL" .env || echo "   ⚠️  APP_URL non trouvé"
else
    echo "   ⚠️  Fichier .env non trouvé"
fi

echo ""
echo "5️⃣  Test d'accès à un fichier:"
if [ -d "storage/app/public/villas" ]; then
    echo "   ✅ Le dossier villas existe"
    echo "   Fichiers dans villas/:"
    find storage/app/public/villas -type f | head -5
else
    echo "   ⚠️  Le dossier villas n'existe pas dans storage/app/public"
fi

echo ""
echo "6️⃣  Vérification du lien symbolique (détaillé):"
if [ -L "public/storage" ]; then
    TARGET=$(readlink public/storage)
    ABSOLUTE_TARGET=$(readlink -f public/storage)
    echo "   Lien relatif: $TARGET"
    echo "   Chemin absolu: $ABSOLUTE_TARGET"
    
    if [ -d "$ABSOLUTE_TARGET" ]; then
        echo "   ✅ Le lien pointe vers un dossier valide"
    else
        echo "   ❌ Le lien est cassé (pointe vers un dossier inexistant)"
        echo "   💡 Solution: Supprimez le lien et recréez-le"
        echo "      rm public/storage"
        echo "      php artisan storage:link"
    fi
fi

echo ""
echo "✅ Diagnostic terminé!"




