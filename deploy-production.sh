#!/bin/bash

# Script de déploiement en production pour LUXÎLES
# Usage: ./deploy-production.sh

set -e  # Arrêter en cas d'erreur

echo "🚀 Déploiement en production - LUXÎLES"
echo "======================================"
echo ""

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Fonction pour afficher les messages
info() {
    echo -e "${GREEN}✓${NC} $1"
}

warn() {
    echo -e "${YELLOW}⚠${NC} $1"
}

error() {
    echo -e "${RED}✗${NC} $1"
}

# 1. Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    error "Ce script doit être exécuté à la racine du projet Laravel"
    exit 1
fi

info "Répertoire de travail : $(pwd)"
echo ""

# 2. Vérifier que .env existe
if [ ! -f ".env" ]; then
    warn "Le fichier .env n'existe pas. Créez-le à partir de .env.example"
    exit 1
fi

info "Fichier .env trouvé"
echo ""

# 3. Vérifier que APP_KEY est défini
if ! grep -q "APP_KEY=base64:" .env; then
    warn "APP_KEY n'est pas défini. Génération..."
    php artisan key:generate
    info "APP_KEY généré"
else
    info "APP_KEY déjà défini"
fi
echo ""

# 4. Vider tous les caches
info "Vidage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
info "Caches vidés"
echo ""

# 5. Optimiser pour la production
info "Optimisation pour la production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache 2>/dev/null || true
info "Optimisation terminée"
echo ""

# 6. Vérifier et créer le lien symbolique storage
if [ ! -L "public/storage" ]; then
    info "Création du lien symbolique storage..."
    php artisan storage:link
    info "Lien symbolique créé"
else
    info "Lien symbolique storage déjà existant"
fi
echo ""

# 7. Optimiser l'autoloader Composer
info "Optimisation de l'autoloader Composer..."
composer dump-autoload --optimize --no-dev --quiet
info "Autoloader optimisé"
echo ""

# 8. Vérifier et ajuster les permissions
info "Vérification des permissions..."
if [ -d "storage" ]; then
    chmod -R 775 storage
    info "Permissions de storage ajustées (775)"
fi

if [ -d "bootstrap/cache" ]; then
    chmod -R 775 bootstrap/cache
    info "Permissions de bootstrap/cache ajustées (775)"
fi
echo ""

# 9. Vérifier la base de données
info "Vérification de la connexion à la base de données..."
if php artisan db:show 2>/dev/null; then
    info "Connexion à la base de données OK"
else
    warn "Impossible de vérifier la connexion à la base de données"
fi
echo ""

# 10. Afficher les routes disponibles
info "Routes disponibles :"
php artisan route:list --columns=method,uri,name | head -20
echo ""

# 11. Résumé
echo "======================================"
echo -e "${GREEN}✅ Déploiement terminé avec succès !${NC}"
echo ""
echo "Prochaines étapes :"
echo "1. Vérifiez que votre serveur web pointe vers le bon répertoire"
echo "2. Testez l'accès à votre site : http://votre-domaine.com"
echo "3. Vérifiez les logs en cas de problème : tail -f storage/logs/laravel.log"
echo ""
echo "Commandes utiles :"
echo "- Voir les routes : php artisan route:list"
echo "- Voir la config : php artisan config:show"
echo "- Voir les logs : tail -f storage/logs/laravel.log"
echo ""




