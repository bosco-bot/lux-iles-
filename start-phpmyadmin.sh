#!/bin/bash

echo "=== Démarrage de phpMyAdmin (Apache XAMPP) ==="
echo ""

# Vérifier si nginx est en cours d'exécution
if pgrep -x nginx > /dev/null; then
    echo "⚠️  Nginx est en cours d'exécution sur le port 80"
    echo ""
    read -p "Voulez-vous arrêter nginx temporairement ? (o/n) " -n 1 -r
    echo
    if [[ $REPLY =~ ^[OoYy]$ ]]; then
        echo "🛑 Arrêt de nginx..."
        sudo pkill nginx
        sleep 2
        echo "✅ Nginx arrêté"
    else
        echo "❌ Impossible de démarrer Apache XAMPP tant que nginx est actif"
        echo "   Vous pouvez arrêter nginx manuellement avec : sudo pkill nginx"
        exit 1
    fi
fi

# Vérifier si le port 8080 est libre
if netstat -tlnp 2>/dev/null | grep -q :8080 || ss -tlnp 2>/dev/null | grep -q :8080; then
    echo "⚠️  Le port 8080 est déjà utilisé !"
    exit 1
fi

echo "✅ Port 8080 disponible"
echo ""

# Vérifier si la configuration est correcte
if ! grep -q "^Listen 8080" /opt/lampp/etc/httpd.conf; then
    echo "📝 Configuration du port 8080..."
    sudo sed -i 's/^Listen 80$/Listen 8080/' /opt/lampp/etc/httpd.conf
    sudo sed -i 's/^ServerName localhost$/ServerName localhost:8080/' /opt/lampp/etc/httpd.conf
    echo "✅ Configuration mise à jour"
fi

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
    echo ""
    echo "💡 Solution alternative : Utilisez MySQL en ligne de commande :"
    echo "   /opt/lampp/bin/mysql -u root lux_iles"
    exit 1
fi











