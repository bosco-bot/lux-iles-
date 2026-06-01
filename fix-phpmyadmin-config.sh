#!/bin/bash

echo "=== Correction de la configuration phpMyAdmin pour XAMPP ==="
echo ""

CONFIG_FILE="/opt/lampp/phpmyadmin/config.inc.php"

if [ ! -f "$CONFIG_FILE" ]; then
    echo "❌ Fichier de configuration introuvable : $CONFIG_FILE"
    exit 1
fi

echo "📝 Modification de la configuration..."

# Décommenter la ligne host et ajouter le socket
sudo sed -i 's|//$cfg\['\''Servers'\''\]\[$i\]\['\''host'\''\] = '\''localhost'\'';|$cfg['\''Servers'\''][$i]['\''host'\''] = '\''localhost'\'';|' "$CONFIG_FILE"

# Ajouter la ligne socket juste après la ligne host
if ! grep -q "socket.*mysql.sock" "$CONFIG_FILE"; then
    sudo sed -i '/$cfg\['\''Servers'\''\]\[$i\]\['\''host'\''\] = '\''localhost'\'';/a $cfg['\''Servers'\''][$i]['\''socket'\''] = '\''/opt/lampp/var/mysql/mysql.sock'\'';' "$CONFIG_FILE"
fi

if [ $? -eq 0 ]; then
    echo "✅ Configuration modifiée avec succès"
    echo ""
    echo "📋 Vérification de la configuration :"
    grep -A 2 "host.*localhost" "$CONFIG_FILE" | head -5
    echo ""
    echo "🔄 Redémarrage d'Apache pour appliquer les changements..."
    sudo /opt/lampp/lampp restartapache
    
    if [ $? -eq 0 ]; then
        echo ""
        echo "✅ Apache redémarré avec succès"
        echo ""
        echo "🌐 Vous pouvez maintenant accéder à phpMyAdmin :"
        echo "   http://localhost:8080/phpmyadmin"
    else
        echo ""
        echo "⚠️  Erreur lors du redémarrage d'Apache"
        echo "   Vous pouvez le redémarrer manuellement avec :"
        echo "   sudo /opt/lampp/lampp restartapache"
    fi
else
    echo "❌ Erreur lors de la modification de la configuration"
    exit 1
fi










