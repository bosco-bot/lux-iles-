#!/bin/bash

# Script pour trouver où sont les images

echo "🔍 Recherche des fichiers images..."
echo ""

# Chercher dans différents emplacements
echo "1️⃣  Recherche dans storage/app/public:"
find storage/app/public -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" 2>/dev/null | head -10

echo ""
echo "2️⃣  Recherche dans public/storage:"
find public/storage -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" 2>/dev/null | head -10

echo ""
echo "3️⃣  Recherche dans storage/app:"
find storage/app -name "*.jpg" -o -name "*.jpeg" -o -name "*.png" 2>/dev/null | head -10

echo ""
echo "4️⃣  Recherche dans tout le projet (villas):"
find . -path "*/villas/*" -name "*.jpg" -o -path "*/villas/*" -name "*.jpeg" -o -path "*/villas/*" -name "*.png" 2>/dev/null | head -10

echo ""
echo "5️⃣  Vérification de la base de données (chemins stockés):"
echo "   Exécutez cette commande SQL pour voir les chemins:"
echo "   SELECT id, villa_id, file_path FROM villa_photos LIMIT 5;"

echo ""
echo "✅ Recherche terminée!"




