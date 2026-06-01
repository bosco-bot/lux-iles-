# Instructions Concrètes - Mise à Jour Base de Données

## 📌 Ce que vous devez faire

Vous avez **2 options**. Choisissez **UNE SEULE** des deux :

---

## ✅ OPTION 1 : Script SQL Direct (RECOMMANDÉ - Plus Simple)

### Ce que fait le script :
Il ajoute 3 nouvelles colonnes à la table `reservations` :
- `adults` (nombre d'adultes)
- `children` (nombre d'enfants)  
- `infants` (nombre de bébés)

### Comment l'exécuter :

**Étape 1 :** Transférez le fichier sur le serveur
```bash
# Le fichier doit être présent sur le serveur à cet emplacement :
database/add_guests_details_to_reservations_table.sql
```

**Étape 2 :** Connectez-vous à votre serveur (en SSH)

**Étape 3 :** Allez dans le répertoire du projet
```bash
cd /chemin/vers/votre/projet/lux-iles
# Par exemple : cd /home/kats6173/public_html/lux-iles.embmission.com
```

**Étape 4 :** Exécutez la commande MySQL
```bash
mysql -u votre_utilisateur_mysql -p votre_nom_base_donnees < database/add_guests_details_to_reservations_table.sql
```

**Exemple concret :**
```bash
# Si votre utilisateur MySQL est "kats6173_luxiles" et votre base "kats6173_luxiles"
mysql -u kats6173_luxiles -p kats6173_luxiles < database/add_guests_details_to_reservations_table.sql
```

**Étape 5 :** Entrez votre mot de passe MySQL quand demandé

**Étape 6 :** Vérifiez que ça a fonctionné (optionnel)
```bash
mysql -u votre_utilisateur -p votre_base
```
Puis dans MySQL :
```sql
DESCRIBE reservations;
-- Vous devriez voir les colonnes : adults, children, infants
```

---

## ✅ OPTION 2 : Migration Laravel (Si vous utilisez les migrations)

### Ce que fait la migration :
Même chose que le script SQL, mais via le système de migrations Laravel.

### Comment l'exécuter :

**Étape 1 :** Transférez le fichier sur le serveur
```bash
# Le fichier doit être présent sur le serveur à cet emplacement :
database/migrations/2026_01_01_120000_add_guests_details_to_reservations_table.php
```

**Étape 2 :** Connectez-vous à votre serveur (en SSH)

**Étape 3 :** Allez dans le répertoire du projet
```bash
cd /chemin/vers/votre/projet/lux-iles
```

**Étape 4 :** Exécutez la migration
```bash
php artisan migrate
```

**Note :** Si vous avez des erreurs de colonnes existantes, vous devrez peut-être modifier la migration pour vérifier l'existence des colonnes d'abord.

---

## 🎯 RECOMMANDATION

**Utilisez l'OPTION 1 (Script SQL)** car :
- ✅ Plus simple et direct
- ✅ Fonctionne toujours, même si vous n'utilisez pas les migrations Laravel
- ✅ Pas de risque de conflit avec d'autres migrations
- ✅ Plus rapide

---

## ⚠️ IMPORTANT

**Vous n'avez besoin que d'UNE seule des deux options !**

- Si vous choisissez l'OPTION 1 : Vous n'avez pas besoin de transférer le fichier de migration
- Si vous choisissez l'OPTION 2 : Vous n'avez pas besoin d'exécuter le script SQL

---

## 🔍 Comment savoir quelle option choisir ?

- **Utilisez OPTION 1** si : Vous n'êtes pas sûr, vous préférez la simplicité, ou vous avez déjà exécuté d'autres scripts SQL directement
- **Utilisez OPTION 2** si : Vous gérez toutes vos modifications de base de données via `php artisan migrate` et vous voulez garder une traçabilité dans la table `migrations`

---

## 📝 Exemple Complet d'Exécution (OPTION 1)

```bash
# 1. Connectez-vous en SSH
ssh kats6173@oliviolet

# 2. Allez dans le projet
cd lux-iles.embmission.com

# 3. Vérifiez que le fichier existe
ls -la database/add_guests_details_to_reservations_table.sql

# 4. Exécutez le script SQL
mysql -u kats6173_luxiles -p kats6173_luxiles < database/add_guests_details_to_reservations_table.sql
# (Entrez le mot de passe quand demandé)

# 5. Vérifiez (optionnel)
mysql -u kats6173_luxiles -p kats6173_luxiles
# Puis dans MySQL :
DESCRIBE reservations;
EXIT;
```

Voilà ! C'est tout ce que vous devez faire. ✅








