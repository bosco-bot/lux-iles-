#!/bin/bash

echo "=== Démarrage d'Apache XAMPP sur le port 8080 (sans arrêter nginx) ==="
echo ""

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
else
    echo "✅ Configuration déjà correcte (port 8080)"
fi

echo ""
echo "🚀 Démarrage d'Apache XAMPP directement (en contournant la vérification XAMPP)..."
echo ""

# Vérifier si Apache XAMPP est déjà en cours d'exécution
if pgrep -f "/opt/lampp/bin/httpd" > /dev/null; then
    echo "⚠️  Apache XAMPP est déjà en cours d'exécution"
    echo "   Arrêt des processus existants..."
    sudo pkill -f "/opt/lampp/bin/httpd"
    sleep 2
fi

# Démarrer Apache directement avec le binaire httpd
echo "🔄 Démarrage d'Apache..."
sudo /opt/lampp/bin/httpd -f /opt/lampp/etc/httpd.conf -k start

if [ $? -eq 0 ]; then
    sleep 2
    
    # Vérifier si Apache est bien démarré
    if pgrep -f "/opt/lampp/bin/httpd" > /dev/null; then
        echo ""
        echo "✅ Apache XAMPP démarré avec succès sur le port 8080 !"
        echo ""
        echo "🌐 Vous pouvez maintenant accéder à phpMyAdmin via :"
        echo "   http://localhost:8080/phpmyadmin"
        echo ""
        echo "📝 Identifiants de connexion :"
        echo "   Utilisateur : root"
        echo "   Mot de passe : (vide)"
        echo "   Base de données : lux_iles"
        echo ""
        echo "ℹ️  Nginx continue de fonctionner sur le port 80"
    else
        echo ""
        echo "❌ Apache n'a pas démarré correctement"
        echo "   Vérifiez les logs : tail -f /opt/lampp/logs/error_log"
        exit 1
    fi
else
    echo ""
    echo "❌ Erreur lors du démarrage d'Apache"
    echo "   Vérifiez les logs : tail -f /opt/lampp/logs/error_log"
    exit 1
fi











