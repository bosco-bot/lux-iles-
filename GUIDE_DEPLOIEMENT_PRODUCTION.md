# 🚀 GUIDE DE DÉPLOIEMENT EN PRODUCTION - LUXÎLES

## 📋 Prérequis

- **Serveur** : Hébergement Hostinger (partagé ou VPS)
- **PHP** : Version 8.2 ou supérieure
- **Base de données** : MySQL 8.0+
- **Accès** : FTP/SFTP + cPanel/Plesk + phpMyAdmin

---

## 📁 1. PRÉPARATION LOCALE

### A. Nettoyer et optimiser le projet

```bash
cd /home/bosco/Bureau/lux-iles

# Nettoyer le cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear
php artisan route:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Compiler les assets pour la production
npm run build
```

### B. Créer l'archive de déploiement

```bash
# Créer une archive sans les fichiers inutiles
tar -czf lux-iles-production.tar.gz \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='storage/framework/cache/*' \
    --exclude='storage/framework/sessions/*' \
    --exclude='storage/framework/views/*' \
    --exclude='.env' \
    --exclude='*.log' \
    --exclude='.DS_Store' \
    .
```

---

## 🖥️ 2. DÉPLOIEMENT SUR LE SERVEUR

### A. Connexion au serveur

**Via FTP/SFTP :**
- Utilisez FileZilla ou votre client FTP préféré
- Serveur : Adresse fournie par Hostinger
- Port : 21 (FTP) ou 22 (SFTP)
- Identifiants : Fournis par Hostinger

### B. Structure des dossiers sur le serveur

```
📁 public_html/ (ou www/)
├── 📁 lux-iles/          # Votre application Laravel
│   ├── 📁 app/
│   ├── 📁 bootstrap/
│   ├── 📁 config/
│   ├── 📁 database/
│   ├── 📁 public/
│   ├── 📁 resources/
│   ├── 📁 routes/
│   ├── 📁 storage/
│   ├── 📁 vendor/
│   ├── artisan
│   ├── composer.json
│   └── ...
├── 📄 index.php          # Point d'entrée (redirige vers public/)
└── 📄 .htaccess          # Configuration Apache
```

### C. Transfert des fichiers

1. **Créer le dossier principal** sur le serveur :
   ```
   public_html/lux-iles/
   ```

2. **Transférer l'archive** :
   - Uploader `lux-iles-production.tar.gz`
   - Extraire l'archive dans `public_html/lux-iles/`

3. **Créer le point d'entrée** :
   Créer un fichier `public_html/index.php` :
   ```php
   <?php
   require __DIR__.'/lux-iles/public/index.php';
   ```

---

## ⚙️ 3. CONFIGURATION SERVEUR

### A. Variables d'environnement (.env)

Créer le fichier `.env` dans `public_html/lux-iles/` :

```env
APP_NAME=LUXÎLES
APP_ENV=production
APP_KEY=base64:YOUR_APP_KEY_HERE
APP_DEBUG=false
APP_URL=https://votre-domaine.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lux_iles_prod
DB_USERNAME=votre_user_db
DB_PASSWORD=votre_password_db

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

MEMCACHED_HOST=127.0.0.1

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=luxiles.smtp@gmail.com
MAIL_PASSWORD=votre_mot_de_passe_app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=contact.luxiles@gmail.com
MAIL_FROM_NAME="${APP_NAME}"

AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="${PUSHER_HOST}"
VITE_PUSHER_PORT="${PUSHER_PORT}"
VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"

STRIPE_PUBLIC_KEY=votre_cle_publique_stripe_production
STRIPE_SECRET_KEY=votre_cle_secrete_stripe_production
STRIPE_WEBHOOK_SECRET=votre_webhook_secret_production
```

### B. Générer la clé d'application

```bash
# Via SSH ou terminal Hostinger
cd public_html/lux-iles
php artisan key:generate
```

**OU** générer localement et copier :
```bash
# Local
php artisan key:generate --show
# Copier la clé générée dans le .env du serveur
```

---

## 🗄️ 4. BASE DE DONNÉES

### A. Créer la base de données

**Via cPanel/phpMyAdmin :**
1. Aller dans **Bases de données > MySQL**
2. Créer une nouvelle base : `lux_iles_prod`
3. Créer un utilisateur avec tous les droits

### B. Importer le schéma

**Via phpMyAdmin :**
1. Sélectionner la base `lux_iles_prod`
2. Importer le fichier `database/schema.sql`

### C. Configuration des paramètres globaux

**Via phpMyAdmin :**
1. Insérer dans la table `global_settings` :

```sql
INSERT INTO `global_settings` (`key`, `value`, `created_at`, `updated_at`) VALUES
-- Configuration société
('company_name', 'BLUE SECRET', NOW(), NOW()),
('company_address', '4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE', NOW(), NOW()),
('company_phone', '+33 7 66 33 41 98', NOW(), NOW()),
('company_email', 'contact.luxiles@gmail.com', NOW(), NOW()),

-- Configuration Stripe (PRODUCTION)
('stripe_public_key', 'pk_live_...', NOW(), NOW()),
('stripe_secret_key', 'sk_live_...', NOW(), NOW()),
('stripe_webhook_secret', 'whsec_...', NOW(), NOW()),

-- Configuration email (Gmail SMTP)
('email_smtp_host', 'smtp.gmail.com', NOW(), NOW()),
('email_smtp_port', '587', NOW(), NOW()),
('email_smtp_username', 'luxiles.smtp@gmail.com', NOW(), NOW()),
('email_smtp_password', 'votre_mot_de_passe_app_gmail', NOW(), NOW()),
('email_smtp_encryption', 'tls', NOW(), NOW()),
('email_from_address', 'contact.luxiles@gmail.com', NOW(), NOW()),
('email_from_name', 'LUXÎLES', NOW(), NOW()),

-- Configuration tarifaire
('deposit_percentage_min', '30', NOW(), NOW()),
('deposit_percentage_max', '50', NOW(), NOW()),
('service_fee_percentage', '5', NOW(), NOW()),
('global_tax_rate', '8.5', NOW(), NOW()),
('tourist_tax_per_night', '2.50', NOW(), NOW()),
('tourist_tax_enabled', '1', NOW(), NOW()),
('balance_due_days_before_checkin', '30', NOW(), NOW()),
('deposit_guarantee_days_before_checkin', '7', NOW(), NOW()),

-- Configuration Pusher (optionnel pour temps réel)
('pusher_app_id', '', NOW(), NOW()),
('pusher_app_key', '', NOW(), NOW()),
('pusher_app_secret', '', NOW(), NOW()),
('pusher_app_cluster', 'mt1', NOW(), NOW());
```

---

## 📦 5. INSTALLATION DES DÉPENDANCES

### A. Installation de Composer (si nécessaire)

```bash
# Via SSH sur le serveur
cd public_html/lux-iles
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

### B. Installation des dépendances PHP

```bash
cd public_html/lux-iles
composer install --no-dev --optimize-autoloader
```

---

## 🔧 6. CONFIGURATION TECHNIQUE

### A. Permissions des fichiers

```bash
cd public_html/lux-iles

# Permissions pour les dossiers
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Permissions pour les fichiers
find . -type f -exec chmod 644 {} \;
chmod 775 artisan
```

### B. Création des liens symboliques

```bash
php artisan storage:link
```

### C. Cache de production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache
```

### D. Migration de la base (si nécessaire)

```bash
php artisan migrate --force
```

---

## 🌐 7. CONFIGURATION WEB

### A. Configuration Apache (.htaccess)

Le fichier `.htaccess` dans `public_html/lux-iles/public/` devrait contenir :

```apache
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP_AUTHORIZATION}]

    # Handle X-XSRF-Token Header
    RewriteCond %{HTTP:x-xsrf-token} .
    RewriteRule .* - [E=HTTP_X_XSRF_TOKEN:%{HTTP:X-XSRF-Token}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
```

### B. Configuration HTTPS (recommandé)

1. **Via cPanel** : Aller dans **Sécurité > Let's Encrypt**
2. **Activer SSL** pour votre domaine
3. **Mettre à jour** `APP_URL=https://votre-domaine.com` dans `.env`

---

## ✅ 8. TESTS ET VALIDATION

### A. Tests de base

```bash
# Test du fichier index.php
curl https://votre-domaine.com/

# Test des pages légales
curl https://votre-domaine.com/mentions-legales
curl https://votre-domaine.com/politique-confidentialite
curl https://votre-domaine.com/politique-cookies
curl https://votre-domaine.com/cgv
```

### B. Tests fonctionnels

1. **Page d'accueil** : `https://votre-domaine.com`
2. **Formulaire de contact** : Envoi d'email
3. **Inscription** : Création de compte utilisateur
4. **Réservation** : Processus complet
5. **Paiements** : Test avec Stripe (montants faibles)

### C. Tests de performance

- **GTmetrix** ou **Google PageSpeed Insights**
- **Temps de réponse** des pages
- **Taille des assets** optimisés

---

## 🔄 9. MISE À JOUR FUTURES

### A. Processus de mise à jour

```bash
# Sur votre machine locale
cd /home/bosco/Bureau/lux-iles

# Modifications du code
git add .
git commit -m "Mise à jour production"

# Créer une nouvelle archive
tar -czf lux-iles-update.tar.gz \
    --exclude='node_modules' \
    --exclude='.git' \
    --exclude='storage/logs/*.log' \
    --exclude='.env' \
    .

# Sur le serveur
cd public_html/lux-iles

# Sauvegarde (optionnel)
cp -r . ../lux-iles-backup-$(date +%Y%m%d_%H%M%S)

# Extraire la mise à jour
tar -xzf lux-iles-update.tar.gz

# Mettre à jour les dépendances
composer install --no-dev --optimize-autoloader

# Nettoyer et recacher
php artisan cache:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
chmod -R 775 storage bootstrap/cache
```

### B. Rollback en cas de problème

```bash
cd public_html

# Restaurer la sauvegarde
rm -rf lux-iles
cp -r lux-iles-backup-DATE lux-iles

# Vérifier les permissions
cd lux-iles
chmod -R 775 storage bootstrap/cache
```

---

## ⚠️ 10. POINTS IMPORTANTS

### A. Sécurité

- ✅ **Désactiver le debug** : `APP_DEBUG=false`
- ✅ **Utiliser HTTPS** en production
- ✅ **Mots de passe forts** pour la base de données
- ✅ **Permissions restrictives** sur les fichiers sensibles

### B. Performance

- ✅ **Assets compilés** pour la production (`npm run build`)
- ✅ **Cache activé** (config, routes, vues)
- ✅ **Optimisation Composer** (`--optimize-autoloader`)

### C. Monitoring

- ✅ **Logs Laravel** : `storage/logs/laravel.log`
- ✅ **Logs serveur** : Vérifier les logs Apache/PHP
- ✅ **Base de données** : Monitorer les connexions

---

## 📞 SUPPORT ET DÉPANNAGE

### A. Problèmes courants

**Erreur 500 :**
```bash
# Vérifier les logs
tail storage/logs/laravel.log

# Vérifier les permissions
ls -la storage/ bootstrap/cache/
```

**Page blanche :**
```bash
# Vérifier la configuration
php artisan config:show

# Vérifier la base de données
php artisan tinker
# Puis : DB::connection()->getPdo();
```

**Assets non chargés :**
```bash
# Vérifier le lien symbolique
php artisan storage:link

# Compiler les assets
npm run build
```

### B. Contact support

- **Hostinger** : Support technique via cPanel
- **Documentation Laravel** : https://laravel.com/docs
- **Communauté** : Laracasts, Stack Overflow

---

## 🎯 RÉCAPITULATIF DES COMMANDES CLÉS

```bash
# Préparation locale
composer install && npm install && npm run build
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Sur le serveur
cd public_html/lux-iles
composer install --no-dev --optimize-autoloader
php artisan storage:link
php artisan migrate --force
php artisan config:cache && php artisan route:cache && php artisan view:cache

# Permissions
chmod -R 775 storage bootstrap/cache
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
```

---

**🚀 Votre plateforme LUXÎLES est maintenant prête pour la production !**

*Document créé le : $(date)*
*Version : 1.0*