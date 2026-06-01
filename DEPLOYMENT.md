# Guide de Déploiement - LUXÎLES

Ce guide vous explique comment déployer l'application LUXÎLES sur un serveur de production.

## 📋 Prérequis

- Serveur avec PHP 8.2+ installé
- MySQL/MariaDB ou PostgreSQL
- Composer installé
- Git installé
- Accès SSH au serveur
- Nom de domaine configuré (optionnel mais recommandé)

## 🚀 Étapes de Déploiement

### 1. Préparer le serveur

#### Sur le serveur, installer les dépendances nécessaires :

```bash
# Mettre à jour le système
sudo apt update && sudo apt upgrade -y

# Installer PHP et extensions nécessaires
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-mbstring php8.2-curl php8.2-zip php8.2-gd php8.2-bcmath -y

# Installer Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer

# Installer MySQL/MariaDB
sudo apt install mysql-server -y

# Installer Nginx (ou Apache)
sudo apt install nginx -y
```

### 2. Cloner le projet sur le serveur

```bash
# Se placer dans le répertoire web (exemple avec /var/www)
cd /var/www

# Cloner votre projet (remplacez par votre URL Git)
git clone https://github.com/votre-username/lux-iles.git
# OU si vous utilisez un autre système de versioning
# Téléchargez et extrayez votre archive

cd lux-iles
```

### 3. Installer les dépendances

```bash
# Installer les dépendances PHP
composer install --optimize-autoloader --no-dev

# Installer les dépendances Node.js (si nécessaire)
npm install
npm run build
```

### 4. Configuration de l'environnement

```bash
# Copier le fichier .env.example
cp .env.example .env

# Générer la clé d'application
php artisan key:generate

# Éditer le fichier .env avec vos paramètres
nano .env
```

#### Configuration du fichier `.env` :

```env
APP_NAME="LUXÎLES"
APP_ENV=production
APP_KEY=base64:... (généré automatiquement)
APP_DEBUG=false
APP_URL=https://votre-domaine.com

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lux_iles
DB_USERNAME=votre_utilisateur_db
DB_PASSWORD=votre_mot_de_passe_db

# Configuration du stockage
FILESYSTEM_DISK=local
```

### 5. Configuration de la base de données

```bash
# Créer la base de données
mysql -u root -p
```

Dans MySQL :
```sql
CREATE DATABASE lux_iles CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'lux_iles_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe';
GRANT ALL PRIVILEGES ON lux_iles.* TO 'lux_iles_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Importer le schéma de la base de données
mysql -u lux_iles_user -p lux_iles < database/schema.sql

# OU utiliser les migrations Laravel
php artisan migrate --force
```

### 6. Configuration des permissions

```bash
# Définir les permissions pour le stockage et le cache
sudo chown -R www-data:www-data /var/www/lux-iles
sudo chmod -R 755 /var/www/lux-iles
sudo chmod -R 775 /var/www/lux-iles/storage
sudo chmod -R 775 /var/www/lux-iles/bootstrap/cache
```

### 7. Optimiser Laravel pour la production

```bash
# Optimiser l'autoloader
composer install --optimize-autoloader --no-dev

# Mettre en cache la configuration
php artisan config:cache

# Mettre en cache les routes
php artisan route:cache

# Mettre en cache les vues
php artisan view:cache

# Créer le lien symbolique pour le stockage
php artisan storage:link
```

### 8. Configuration de Nginx

Créer un fichier de configuration Nginx :

```bash
sudo nano /etc/nginx/sites-available/lux-iles
```

Contenu du fichier :

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name votre-domaine.com www.votre-domaine.com;
    root /var/www/lux-iles/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

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

Activer le site :

```bash
sudo ln -s /etc/nginx/sites-available/lux-iles /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 9. Configuration SSL (HTTPS) - Optionnel mais recommandé

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtenir un certificat SSL
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com
```

### 10. Configuration PHP pour la production

Éditer `php.ini` :

```bash
sudo nano /etc/php/8.2/fpm/php.ini
```

Modifications recommandées :

```ini
upload_max_filesize = 20M
post_max_size = 50M
memory_limit = 256M
max_execution_time = 300
```

Redémarrer PHP-FPM :

```bash
sudo systemctl restart php8.2-fpm
```

### 11. Configuration du cron pour Laravel

```bash
# Éditer le crontab
sudo crontab -e -u www-data
```

Ajouter cette ligne :

```
* * * * * cd /var/www/lux-iles && php artisan schedule:run >> /dev/null 2>&1
```

### 12. Vérifications finales

```bash
# Vérifier que tout fonctionne
php artisan about

# Vérifier les logs en cas d'erreur
tail -f storage/logs/laravel.log
```

## 🔄 Mises à jour futures

Pour mettre à jour l'application après un déploiement :

```bash
cd /var/www/lux-iles

# Récupérer les dernières modifications
git pull origin main

# Installer les nouvelles dépendances
composer install --optimize-autoloader --no-dev

# Exécuter les migrations
php artisan migrate --force

# Vider et recréer les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Redémarrer les services si nécessaire
sudo systemctl restart php8.2-fpm
sudo systemctl reload nginx
```

## 🔒 Sécurité

- ✅ Ne jamais commiter le fichier `.env`
- ✅ Utiliser `APP_DEBUG=false` en production
- ✅ Configurer un firewall (UFW)
- ✅ Utiliser HTTPS avec SSL
- ✅ Mettre à jour régulièrement les dépendances
- ✅ Configurer des sauvegardes automatiques de la base de données

## 📝 Notes importantes

1. **Stockage des fichiers** : Les fichiers uploadés sont dans `storage/app/public`. Assurez-vous que le lien symbolique est créé avec `php artisan storage:link`.

2. **Permissions** : Les dossiers `storage` et `bootstrap/cache` doivent être accessibles en écriture par le serveur web.

3. **Base de données** : Faites des sauvegardes régulières de votre base de données.

4. **Logs** : Surveillez les logs dans `storage/logs/laravel.log` pour détecter les erreurs.

## 🆘 Dépannage

### Erreur 500
- Vérifier les permissions des dossiers `storage` et `bootstrap/cache`
- Vérifier les logs : `tail -f storage/logs/laravel.log`
- Vérifier la configuration PHP-FPM

### Erreur de base de données
- Vérifier les identifiants dans `.env`
- Vérifier que la base de données existe et est accessible

### Images non affichées
- Vérifier que `php artisan storage:link` a été exécuté
- Vérifier les permissions du dossier `storage/app/public`

## 📞 Support

En cas de problème, vérifiez :
1. Les logs Laravel : `storage/logs/laravel.log`
2. Les logs Nginx : `/var/log/nginx/error.log`
3. Les logs PHP-FPM : `/var/log/php8.2-fpm.log`




