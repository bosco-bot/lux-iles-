#!/bin/bash

# Script pour corriger le problème d'accès aux images

echo "🔧 Correction du problème d'accès aux images..."

# Vérifier qu'on est dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo "❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel"
    exit 1
fi

echo "📁 Vérification du lien symbolique storage..."

# Supprimer l'ancien lien s'il existe
if [ -L "public/storage" ]; then
    echo "🗑️  Suppression de l'ancien lien..."
    rm public/storage
fi

# Créer le nouveau lien
echo "🔗 Création du lien symbolique..."
php artisan storage:link

# Vérifier que le lien existe
if [ -L "public/storage" ]; then
    echo "✅ Lien symbolique créé avec succès"
    ls -la public/storage
else
    echo "❌ Erreur: Le lien symbolique n'a pas pu être créé"
    echo "💡 Essayez manuellement: ln -s ../storage/app/public public/storage"
    exit 1
fi

# Vérifier les permissions
echo "🔐 Vérification des permissions..."
chmod -R 775 storage/app/public 2>/dev/null || echo "⚠️  Impossible de modifier les permissions (besoin de sudo?)"

# Vérifier la configuration
echo "⚙️  Vérification de la configuration..."
if [ -f ".env" ]; then
    echo "📝 APP_URL dans .env:"
    grep "APP_URL" .env || echo "⚠️  APP_URL non trouvé dans .env"
else
    echo "⚠️  Fichier .env non trouvé"
fi

echo ""
echo "✅ Correction terminée!"
echo ""
echo "📋 Prochaines étapes:"
echo "1. Vérifiez que APP_URL dans .env est correct"
echo "2. Exécutez: php artisan config:cache"
echo "3. Testez l'accès à une image via: http://votre-domaine.com/storage/..."




