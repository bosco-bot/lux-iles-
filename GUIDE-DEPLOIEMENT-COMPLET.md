# Guide de Déploiement Complet - LUXÎLES

Ce guide vous permet de déployer le projet en production exactement comme il fonctionne en local.

## 📋 Checklist de Déploiement

### 1. Préparation du Serveur

#### A. Configuration PHP
Vérifiez que PHP 8.2+ est installé :
```bash
php -v
```

#### B. Extensions PHP Requises
```bash
php -m | grep -E "pdo|mbstring|xml|curl|zip|gd|fileinfo"
```

Extensions nécessaires :
- `pdo_mysql` ou `pdo_pgsql`
- `mbstring`
- `xml`
- `curl`
- `zip`
- `gd` ou `imagick`
- `fileinfo`
- `openssl`

#### C. Configuration PHP pour Uploads
Créez ou modifiez `php.ini` ou `.user.ini` dans `public/` :
```ini
upload_max_filesize = 20M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
```

### 2. Transfert des Fichiers

#### A. Exclure les Fichiers Inutiles
Ne transférez PAS :
- `node_modules/`
- `.git/`
- `.env` (créez-en un nouveau sur le serveur)
- `storage/logs/*` (gardez le dossier, videz les fichiers)
- `storage/framework/cache/*`
- `storage/framework/sessions/*`
- `storage/framework/views/*`

#### B. Transférer les Fichiers
```bash
# Sur votre machine locale
rsync -avz --exclude 'node_modules' --exclude '.git' --exclude '.env' \
  --exclude 'storage/logs/*' --exclude 'storage/framework/cache/*' \
  --exclude 'storage/framework/sessions/*' --exclude 'storage/framework/views/*' \
  /chemin/local/lux-iles/ user@serveur:/chemin/serveur/lux-iles/
```

Ou utilisez un client FTP/SFTP.

### 3. Configuration sur le Serveur

#### A. Créer le Fichier `.env`
```bash
cd /chemin/serveur/lux-iles
cp .env.example .env
```

Modifiez `.env` avec vos paramètres de production :
```env
APP_NAME="LUXÎLES"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=http://lux-iles.embmission.com/lux-iles

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=votre_base_de_donnees
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

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
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

**⚠️ IMPORTANT :**
- `APP_URL` doit être l'URL complète avec le sous-dossier si nécessaire
- `APP_DEBUG=false` en production
- `APP_KEY` sera généré à l'étape suivante

#### B. Générer la Clé d'Application
```bash
php artisan key:generate
```

#### C. Installer les Dépendances
```bash
composer install --optimize-autoloader --no-dev
```

#### D. Créer le Lien Symbolique Storage
```bash
php artisan storage:link
```

Vérifiez que le lien existe :
```bash
ls -la public/storage
# Doit afficher : public/storage -> ../storage/app/public
```

### 4. Configuration de la Base de Données

#### A. Créer la Base de Données
```sql
CREATE DATABASE votre_base_de_donnees CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

#### B. Importer le Schéma
```bash
# Option 1 : Utiliser les migrations
php artisan migrate --force

# Option 2 : Importer le fichier SQL
mysql -u utilisateur -p votre_base_de_donnees < database/schema.sql
```

#### C. Vérifier la Connexion
```bash
php artisan tinker
>>> DB::connection()->getPdo();
# Doit retourner un objet PDO sans erreur
```

### 5. Configuration du Serveur Web

#### A. Apache (avec sous-dossier `/lux-iles/`)

**Option 1 : DocumentRoot vers `public/` (Recommandé)**
```apache
<VirtualHost *:80>
    ServerName lux-iles.embmission.com
    DocumentRoot /chemin/serveur/lux-iles/public
    
    <Directory /chemin/serveur/lux-iles/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

**Option 2 : DocumentRoot vers racine + `.htaccess`**
Si vous ne pouvez pas modifier le DocumentRoot, utilisez le `.htaccess` à la racine :
```apache
<VirtualHost *:80>
    ServerName lux-iles.embmission.com
    DocumentRoot /chemin/serveur/lux-iles
    
    <Directory /chemin/serveur/lux-iles>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

Et le `.htaccess` à la racine doit contenir :
```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /lux-iles/
    RewriteCond %{REQUEST_URI} !^/lux-iles/public/
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ public/$1 [L,QSA]
</IfModule>
```

#### B. Nginx
```nginx
server {
    listen 80;
    server_name lux-iles.embmission.com;
    root /chemin/serveur/lux-iles/public;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 6. Permissions des Fichiers

```bash
# Propriétaire (remplacez 'www-data' par l'utilisateur du serveur web)
sudo chown -R www-data:www-data /chemin/serveur/lux-iles

# Permissions des dossiers
find /chemin/serveur/lux-iles -type d -exec chmod 755 {} \;

# Permissions des fichiers
find /chemin/serveur/lux-iles -type f -exec chmod 644 {} \;

# Permissions spéciales pour storage et bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 7. Optimisations Laravel

```bash
# Vider tous les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimiser pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Optimiser l'autoloader Composer
composer dump-autoload --optimize
```

### 8. Vérifications Finales

#### A. Tester les Routes
```bash
php artisan route:list
```

#### B. Tester l'Accès Web
- Accueil : `http://lux-iles.embmission.com/lux-iles/`
- Villas : `http://lux-iles.embmission.com/lux-iles/villas`
- Admin : `http://lux-iles.embmission.com/lux-iles/admin/dashboard`

#### C. Vérifier les Images
```bash
# Vérifier que le lien symbolique fonctionne
ls -la public/storage
# Doit pointer vers storage/app/public

# Vérifier qu'une image est accessible
curl -I http://lux-iles.embmission.com/lux-iles/storage/villas/1/image.jpg
# Doit retourner HTTP 200 ou 404 (si l'image n'existe pas)
```

#### D. Vérifier les Logs
```bash
tail -f storage/logs/laravel.log
```

### 9. Script de Déploiement Automatique

Créez un script `deploy-production.sh` :
```bash
#!/bin/bash

echo "🚀 Déploiement en production..."

# 1. Vider les caches
echo "📦 Vidage des caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# 2. Optimiser
echo "⚡ Optimisation..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Vérifier le storage link
if [ ! -L "public/storage" ]; then
    echo "🔗 Création du lien symbolique storage..."
    php artisan storage:link
fi

# 4. Optimiser Composer
echo "📚 Optimisation Composer..."
composer dump-autoload --optimize --no-dev

# 5. Vérifier les permissions
echo "🔐 Vérification des permissions..."
chmod -R 775 storage bootstrap/cache

echo "✅ Déploiement terminé !"
```

Rendez-le exécutable :
```bash
chmod +x deploy-production.sh
```

## 🔧 Résolution de Problèmes Courants

### Problème 1 : Routes 404
**Solution :**
1. Vérifiez que `mod_rewrite` est activé (Apache)
2. Vérifiez le `.htaccess` à la racine
3. Vérifiez que `APP_URL` dans `.env` est correct
4. Videz les caches : `php artisan route:clear && php artisan route:cache`

### Problème 2 : Images 404
**Solution :**
1. Vérifiez le lien symbolique : `ls -la public/storage`
2. Recréez-le si nécessaire : `php artisan storage:link`
3. Vérifiez `APP_URL` dans `.env`
4. Vérifiez les permissions : `chmod -R 775 storage`

### Problème 3 : Erreur 500
**Solution :**
1. Activez temporairement `APP_DEBUG=true` dans `.env`
2. Consultez `storage/logs/laravel.log`
3. Vérifiez les permissions des fichiers
4. Vérifiez la configuration de la base de données

### Problème 4 : Upload de Fichiers Échoue
**Solution :**
1. Vérifiez `upload_max_filesize` et `post_max_size` dans `php.ini`
2. Créez un `.user.ini` dans `public/` avec les limites
3. Vérifiez les permissions de `storage/app/public`

## 📝 Checklist Finale

- [ ] PHP 8.2+ installé
- [ ] Extensions PHP requises installées
- [ ] Fichiers transférés (sans node_modules, .git, etc.)
- [ ] `.env` créé et configuré
- [ ] `APP_KEY` généré
- [ ] `composer install --no-dev` exécuté
- [ ] Base de données créée et migrée
- [ ] `php artisan storage:link` exécuté
- [ ] Serveur web configuré (Apache/Nginx)
- [ ] Permissions des fichiers correctes
- [ ] Caches optimisés
- [ ] Routes testées
- [ ] Images accessibles
- [ ] Logs vérifiés

## 🎯 Commandes Rapides de Déploiement

```bash
# Déploiement complet en une commande
php artisan config:clear && \
php artisan cache:clear && \
php artisan route:clear && \
php artisan view:clear && \
php artisan config:cache && \
php artisan route:cache && \
php artisan view:cache && \
php artisan storage:link && \
composer dump-autoload --optimize --no-dev && \
chmod -R 775 storage bootstrap/cache
```

## 📞 Support

En cas de problème, vérifiez :
1. Les logs : `tail -f storage/logs/laravel.log`
2. Les permissions : `ls -la storage bootstrap/cache`
3. La configuration : `php artisan config:show`
4. Les routes : `php artisan route:list`




