#!/bin/bash

# Script pour lister tous les fichiers à uploader sur le serveur
# Exclut les fichiers/dossiers listés dans .gitignore

echo "=========================================="
echo "📦 LISTE DES FICHIERS À UPLOADER"
echo "=========================================="
echo ""

# Créer un fichier temporaire avec la liste
OUTPUT_FILE="files-to-upload.txt"

echo "# Fichiers et dossiers à uploader sur le serveur" > $OUTPUT_FILE
echo "# Généré le $(date)" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

echo "📁 STRUCTURE DES DOSSIERS À UPLOADER:" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

# Lister les dossiers principaux
echo "✅ DOSSIERS PRINCIPAUX:" >> $OUTPUT_FILE
find . -maxdepth 1 -type d ! -name "." ! -name ".git" ! -name "vendor" ! -name "node_modules" ! -name ".idea" ! -name ".vscode" ! -name ".fleet" ! -name ".nova" ! -name ".zed" | sort >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

echo "✅ FICHIERS À LA RACINE:" >> $OUTPUT_FILE
find . -maxdepth 1 -type f ! -name ".env*" ! -name "*.log" ! -name ".DS_Store" ! -name "Thumbs.db" ! -name "Homestead.*" ! -name ".phpactor.json" ! -name ".phpunit.result.cache" ! -name "*.zip" ! -name "*.sh" ! -name "*.md" | sort >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

echo "📋 RÉCAPITULATIF:" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

# Compter les fichiers
TOTAL_FILES=$(find . -type f ! -path "./vendor/*" ! -path "./node_modules/*" ! -path "./.git/*" ! -path "./storage/logs/*.log" ! -name ".env*" ! -name ".DS_Store" ! -name "Thumbs.db" ! -name "*.zip" | wc -l)
TOTAL_DIRS=$(find . -type d ! -path "./vendor/*" ! -path "./node_modules/*" ! -path "./.git/*" ! -name ".git" ! -name "vendor" ! -name "node_modules" | wc -l)

echo "Total de fichiers: $TOTAL_FILES" >> $OUTPUT_FILE
echo "Total de dossiers: $TOTAL_DIRS" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

echo "❌ FICHIERS/DOSSIERS À NE PAS UPLOADER:" >> $OUTPUT_FILE
echo "  - .env" >> $OUTPUT_FILE
echo "  - vendor/" >> $OUTPUT_FILE
echo "  - node_modules/" >> $OUTPUT_FILE
echo "  - storage/logs/*.log" >> $OUTPUT_FILE
echo "  - .git/" >> $OUTPUT_FILE
echo "  - public/storage (lien symbolique à créer)" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

echo "📝 COMMANDES À EXÉCUTER SUR LE SERVEUR:" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE
echo "1. composer install --no-dev --optimize-autoloader" >> $OUTPUT_FILE
echo "2. cp .env.example .env" >> $OUTPUT_FILE
echo "3. php artisan key:generate" >> $OUTPUT_FILE
echo "4. php artisan storage:link" >> $OUTPUT_FILE
echo "5. php artisan migrate --force" >> $OUTPUT_FILE
echo "6. chmod -R 775 storage bootstrap/cache" >> $OUTPUT_FILE
echo "7. php artisan config:cache" >> $OUTPUT_FILE
echo "8. php artisan route:cache" >> $OUTPUT_FILE
echo "9. php artisan view:cache" >> $OUTPUT_FILE
echo "" >> $OUTPUT_FILE

# Afficher le résultat
cat $OUTPUT_FILE

echo ""
echo "=========================================="
echo "✅ Liste sauvegardée dans: $OUTPUT_FILE"
echo "=========================================="






