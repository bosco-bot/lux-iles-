#!/bin/bash

# Script complet pour corriger le problème de storage

echo "🔧 Correction complète du problème de storage..."
echo ""

# 1. Supprimer le mauvais lien
echo "1️⃣  Suppression du lien incorrect..."
if [ -L "public/storage" ] || [ -d "public/storage" ]; then
    rm -rf public/storage
    echo "   ✅ Ancien lien supprimé"
else
    echo "   ℹ️  Pas de lien à supprimer"
fi

# 2. Vérifier/créer le dossier storage/app/public
echo ""
echo "2️⃣  Vérification du dossier storage/app/public..."
mkdir -p storage/app/public
echo "   ✅ Dossier créé/vérifié"

# 3. Déplacer les fichiers de public/storage vers storage/app/public (si ils existent)
echo ""
echo "3️⃣  Déplacement des fichiers existants..."
if [ -d "public/storage" ] && [ ! -L "public/storage" ]; then
    echo "   📦 Déplacement des fichiers..."
    cp -r public/storage/* storage/app/public/ 2>/dev/null || true
    echo "   ✅ Fichiers déplacés"
else
    echo "   ℹ️  Pas de fichiers à déplacer"
fi

# 4. Créer le bon lien symbolique
echo ""
echo "4️⃣  Création du lien symbolique correct..."
php artisan storage:link
if [ -L "public/storage" ]; then
    echo "   ✅ Lien créé"
    echo "   📍 Pointe vers: $(readlink -f public/storage)"
else
    echo "   ❌ Erreur lors de la création du lien"
    echo "   💡 Création manuelle..."
    cd public && ln -s ../storage/app/public storage && cd ..
    echo "   ✅ Lien créé manuellement"
fi

# 5. Vérifier les permissions
echo ""
echo "5️⃣  Configuration des permissions..."
chmod -R 775 storage/app/public 2>/dev/null || echo "   ⚠️  Impossible de modifier les permissions"
chmod -R 755 public 2>/dev/null || echo "   ⚠️  Impossible de modifier les permissions"

echo ""
echo "✅ Correction terminée!"
echo ""
echo "📋 Prochaines étapes:"
echo "1. Modifiez APP_URL dans .env : APP_URL=http://lux-iles.embmission.com/lux-iles"
echo "2. Exécutez: php artisan config:cache"
echo "3. Vérifiez que les images sont accessibles"




