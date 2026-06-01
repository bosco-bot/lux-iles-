#!/bin/bash

# Script de déploiement pour LUXÎLES
# Usage: ./deploy.sh

set -e

echo "🚀 Déploiement de LUXÎLES..."

# Couleurs pour les messages
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Vérifier que nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: Ce script doit être exécuté depuis la racine du projet Laravel${NC}"
    exit 1
fi

echo -e "${YELLOW}📦 Mise à jour des dépendances...${NC}"
composer install --optimize-autoloader --no-dev

echo -e "${YELLOW}🔧 Optimisation de Laravel...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo -e "${YELLOW}🗄️  Exécution des migrations...${NC}"
php artisan migrate --force

echo -e "${YELLOW}🔗 Création du lien symbolique pour le stockage...${NC}"
php artisan storage:link || true

echo -e "${YELLOW}🧹 Nettoyage des caches...${NC}"
php artisan cache:clear
php artisan view:clear

echo -e "${GREEN}✅ Déploiement terminé avec succès!${NC}"
echo -e "${GREEN}💡 N'oubliez pas de redémarrer PHP-FPM si nécessaire: sudo systemctl restart php8.2-fpm${NC}"




