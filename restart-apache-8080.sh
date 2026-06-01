#!/bin/bash

echo "=== Redémarrage d'Apache XAMPP sur le port 8080 ==="
echo ""

# Arrêter Apache s'il est en cours d'exécution
echo "🛑 Arrêt d'Apache..."
sudo pkill -f "/opt/lampp/bin/httpd" 2>/dev/null
sleep 2

# Vérifier que le port 8080 est libre
if netstat -tlnp 2>/dev/null | grep -q :8080 || ss -tlnp 2>/dev/null | grep -q :8080; then
    echo "⚠️  Le port 8080 est encore utilisé, attente..."
    sleep 2
fi

# Démarrer Apache directement
echo "🚀 Démarrage d'Apache sur le port 8080..."
sudo /opt/lampp/bin/httpd -f /opt/lampp/etc/httpd.conf -k start

if [ $? -eq 0 ]; then
    echo ""
    echo "✅ Apache redémarré avec succès !"
    echo ""
    echo "🌐 Vous pouvez maintenant accéder à phpMyAdmin :"
    echo "   http://localhost:8080/phpmyadmin"
    echo ""
    echo "📝 La configuration du socket MySQL a été appliquée."
else
    echo ""
    echo "❌ Erreur lors du redémarrage d'Apache"
    exit 1
fi










