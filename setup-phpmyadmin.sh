#!/bin/bash

echo "=== Configuration d'Apache XAMPP pour phpMyAdmin sur le port 8080 ==="
echo ""

# Vérifier si le port 8080 est libre
if netstat -tlnp 2>/dev/null | grep -q :8080 || ss -tlnp 2>/dev/null | grep -q :8080; then
    echo "⚠️  Le port 8080 est déjà utilisé !"
    exit 1
fi

echo "✅ Port 8080 disponible"
echo ""

# Modifier la configuration Apache
echo "📝 Modification de la configuration Apache..."
sudo sed -i 's/^Listen 80$/Listen 8080/' /opt/lampp/etc/httpd.conf
sudo sed -i 's/^ServerName localhost$/ServerName localhost:8080/' /opt/lampp/etc/httpd.conf

if [ $? -eq 0 ]; then
    echo "✅ Configuration modifiée avec succès"
else
    echo "❌ Erreur lors de la modification de la configuration"
    exit 1
fi

echo ""
echo "🚀 Démarrage d'Apache XAMPP..."
sudo /opt/lampp/lampp startapache

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Apache XAMPP démarré avec succès !"
    echo ""
    echo "📋 Vérification du statut..."
    sudo /opt/lampp/lampp status
    echo ""
    echo "🌐 Vous pouvez maintenant accéder à phpMyAdmin via :"
    echo "   http://localhost:8080/phpmyadmin"
    echo ""
    echo "📝 Identifiants de connexion :"
    echo "   Utilisateur : root"
    echo "   Mot de passe : (vide)"
    echo "   Base de données : lux_iles"
else
    echo ""
    echo "❌ Erreur lors du démarrage d'Apache"
    exit 1
fi











