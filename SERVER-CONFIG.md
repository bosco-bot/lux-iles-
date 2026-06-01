# Configuration du Serveur Web - Résoudre le problème /public dans l'URL

## 🔴 Problème
Vous devez ajouter `/public` à la fin de chaque URL (ex: `monsite.com/public`)

## ✅ Solution : Configurer le serveur web pour pointer vers le dossier `public`

### Option 1 : Apache avec .htaccess (Recommandé si vous utilisez Apache)

#### Étape 1 : Créer un fichier `.htaccess` à la racine du projet

Créez un fichier `.htaccess` dans `/var/www/lux-iles/.htaccess` (à la racine, pas dans public) :

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

#### Étape 2 : Vérifier que mod_rewrite est activé

```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

#### Étape 3 : Configurer Apache pour autoriser .htaccess

Éditez votre configuration Apache :

```bash
sudo nano /etc/apache2/sites-available/lux-iles.conf
```

Assurez-vous que votre VirtualHost a cette configuration :

```apache
<VirtualHost *:80>
    ServerName votre-domaine.com
    DocumentRoot /var/www/lux-iles/public
    
    <Directory /var/www/lux-iles>
        AllowOverride All
        Require all granted
    </Directory>
    
    <Directory /var/www/lux-iles/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/lux-iles-error.log
    CustomLog ${APACHE_LOG_DIR}/lux-iles-access.log combined
</VirtualHost>
```

**IMPORTANT** : `DocumentRoot` doit pointer vers `/var/www/lux-iles/public`

Puis activez et rechargez :

```bash
sudo a2ensite lux-iles.conf
sudo systemctl reload apache2
```

---

### Option 2 : Nginx (Recommandé pour la performance)

Éditez votre configuration Nginx :

```bash
sudo nano /etc/nginx/sites-available/lux-iles
```

Configuration complète :

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name votre-domaine.com www.votre-domaine.com;
    
    # ⚠️ IMPORTANT : root doit pointer vers le dossier public
    root /var/www/lux-iles/public;
    index index.php index.html;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

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
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**IMPORTANT** : `root /var/www/lux-iles/public;` (avec `/public` à la fin)

Activez et testez :

```bash
sudo ln -s /etc/nginx/sites-available/lux-iles /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

---

### Option 3 : Hébergement partagé (cPanel, etc.)

Si vous utilisez un hébergement partagé :

#### Solution A : Déplacer les fichiers

1. Déplacez TOUT le contenu du dossier `public` à la racine de votre domaine
2. Déplacez les autres fichiers (app, config, etc.) dans un dossier parent
3. Modifiez `bootstrap/app.php` pour pointer vers le bon chemin

#### Solution B : Utiliser un sous-domaine

Créez un sous-domaine qui pointe vers `/public`

#### Solution C : Modifier le DocumentRoot dans cPanel

Dans cPanel :
- Allez dans "Domaines" ou "Sous-domaines"
- Modifiez le DocumentRoot pour pointer vers `public_html/public` (ou `domaine.com/public`)

---

## 🔍 Vérification

Après configuration, testez :

1. Accédez à `http://votre-domaine.com` (sans `/public`)
2. Le site doit se charger normalement
3. Vérifiez que les assets (CSS, JS, images) se chargent correctement

## 🚨 Problèmes courants

### Erreur 403 Forbidden
→ Vérifiez les permissions :
```bash
sudo chown -R www-data:www-data /var/www/lux-iles
sudo chmod -R 755 /var/www/lux-iles
```

### Erreur 500 Internal Server Error
→ Vérifiez les logs :
```bash
tail -f /var/log/apache2/error.log  # Pour Apache
tail -f /var/log/nginx/error.log     # Pour Nginx
tail -f /var/www/lux-iles/storage/logs/laravel.log
```

### Les images ne s'affichent pas
→ Vérifiez que le lien symbolique existe :
```bash
php artisan storage:link
ls -la public/storage  # Doit être un lien symbolique
```

## 📝 Résumé

**La clé** : Le `DocumentRoot` (Apache) ou `root` (Nginx) doit **TOUJOURS** pointer vers le dossier `public` de Laravel, pas vers la racine du projet.

- ✅ Correct : `/var/www/lux-iles/public`
- ❌ Incorrect : `/var/www/lux-iles`




