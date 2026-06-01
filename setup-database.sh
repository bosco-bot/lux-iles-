#!/bin/bash

echo "=== Configuration de la base de données LUX ÎLES ==="
echo ""

# Vérifier si MySQL est démarré
if ! /opt/lampp/bin/mysql -u root -e "SELECT 1" &>/dev/null; then
    echo "⚠️  MySQL n'est pas démarré. Veuillez démarrer XAMPP avec :"
    echo "   sudo /opt/lampp/lampp startmysql"
    echo ""
    exit 1
fi

echo "✅ MySQL est démarré"
echo ""

# Créer la base de données
echo "📦 Création de la base de données 'lux_iles'..."
/opt/lampp/bin/mysql -u root -e "CREATE DATABASE IF NOT EXISTS lux_iles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>&1

if [ $? -eq 0 ]; then
    echo "✅ Base de données créée avec succès"
else
    echo "❌ Erreur lors de la création de la base de données"
    exit 1
fi

echo ""
echo "📋 Exécution des migrations Laravel..."
cd /home/babiel/lux-iles
php artisan migrate

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Configuration terminée avec succès !"
    echo ""
    echo "Vous pouvez maintenant accéder à votre application Laravel."
else
    echo ""
    echo "❌ Erreur lors de l'exécution des migrations"
    exit 1
fi


