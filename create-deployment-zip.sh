#!/bin/bash

# Script pour créer un zip de déploiement (sans vendor, node_modules, etc.)

echo "📦 Création du zip de déploiement..."

# Nom du fichier zip avec date
ZIP_NAME="lux-iles-deployment-$(date +%Y%m%d-%H%M%S).zip"

# Créer un dossier temporaire
TEMP_DIR=$(mktemp -d)
echo "📁 Dossier temporaire: $TEMP_DIR"

# Copier les fichiers nécessaires (exclure vendor, node_modules, etc.)
echo "📋 Copie des fichiers..."
rsync -av \
  --exclude='vendor' \
  --exclude='node_modules' \
  --exclude='.git' \
  --exclude='.env' \
  --exclude='.env.backup' \
  --exclude='storage/*' \
  --exclude='storage/app/*' \
  --exclude='storage/framework/cache/*' \
  --exclude='storage/framework/sessions/*' \
  --exclude='storage/framework/views/*' \
  --exclude='storage/logs/*' \
  --exclude='bootstrap/cache/*' \
  --exclude='.idea' \
  --exclude='.vscode' \
  --exclude='*.zip' \
  --exclude='*.log' \
  --exclude='.DS_Store' \
  --exclude='Thumbs.db' \
  ./ "$TEMP_DIR/lux-iles/"

# Créer les dossiers nécessaires (vides) pour le stockage
mkdir -p "$TEMP_DIR/lux-iles/storage/app/public"
mkdir -p "$TEMP_DIR/lux-iles/storage/framework/cache"
mkdir -p "$TEMP_DIR/lux-iles/storage/framework/sessions"
mkdir -p "$TEMP_DIR/lux-iles/storage/framework/views"
mkdir -p "$TEMP_DIR/lux-iles/storage/logs"
mkdir -p "$TEMP_DIR/lux-iles/bootstrap/cache"

# Créer un fichier .gitkeep dans storage pour garder la structure
touch "$TEMP_DIR/lux-iles/storage/app/public/.gitkeep"
touch "$TEMP_DIR/lux-iles/storage/logs/.gitkeep"

# Créer le zip
echo "🗜️  Compression..."
cd "$TEMP_DIR"
zip -r "$ZIP_NAME" lux-iles/ > /dev/null

# Déplacer le zip dans le répertoire courant
mv "$ZIP_NAME" "$OLDPWD/"

# Nettoyer
rm -rf "$TEMP_DIR"

echo "✅ Zip créé: $ZIP_NAME"
echo "📦 Taille: $(du -h "$OLDPWD/$ZIP_NAME" | cut -f1)"
echo ""
echo "📝 Instructions:"
echo "1. Transférez ce fichier sur votre serveur"
echo "2. Extrayez-le dans /var/www/lux-iles (ou votre répertoire web)"
echo "3. Suivez les instructions dans DEPLOYMENT.md"




