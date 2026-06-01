# Guide de Déploiement - Notifications Email Admin

Ce guide explique comment déployer les modifications des notifications email sur le serveur.

## 📋 Fichiers à Déployer

Les 3 fichiers suivants ont été modifiés :

1. `app/Notifications/PaymentReceivedNotification.php`
2. `app/Notifications/ReservationCreatedNotification.php`
3. `app/Providers/AppServiceProvider.php`

---

## 🚀 Méthode 1 : Transfert par SCP (Recommandé)

### Étape 1 : Depuis votre machine locale

```bash
# Remplacez par vos informations de connexion
USER="votre_utilisateur"
SERVER="votre_serveur.com"  # Exemple : kats6173@oliviolet (si SSH)
CHEMIN_SERVEUR="/chemin/vers/lux-iles"  # Exemple : ~/lux-iles.embmission.com

# Transférer les 3 fichiers
scp app/Notifications/PaymentReceivedNotification.php ${USER}@${SERVER}:${CHEMIN_SERVEUR}/app/Notifications/
scp app/Notifications/ReservationCreatedNotification.php ${USER}@${SERVER}:${CHEMIN_SERVEUR}/app/Notifications/
scp app/Providers/AppServiceProvider.php ${USER}@${SERVER}:${CHEMIN_SERVEUR}/app/Providers/
```

### Exemple concret (si vous connaissez le chemin exact) :

```bash
scp app/Notifications/PaymentReceivedNotification.php kats6173@oliviolet:~/lux-iles.embmission.com/app/Notifications/
scp app/Notifications/ReservationCreatedNotification.php kats6173@oliviolet:~/lux-iles.embmission.com/app/Notifications/
scp app/Providers/AppServiceProvider.php kats6173@oliviolet:~/lux-iles.embmission.com/app/Providers/
```

---

## 📤 Méthode 2 : Transfert par FTP/SFTP (Client graphique)

### Avec FileZilla, WinSCP, ou Cyberduck :

1. **Connectez-vous au serveur** avec vos identifiants FTP/SFTP

2. **Naviguez vers les dossiers suivants** :
   - `app/Notifications/`
   - `app/Providers/`

3. **Transférez les 3 fichiers** :
   - `PaymentReceivedNotification.php` → `app/Notifications/`
   - `ReservationCreatedNotification.php` → `app/Notifications/`
   - `AppServiceProvider.php` → `app/Providers/`

4. **Remplacer les fichiers existants** (si demandé)

---

## 🔄 Méthode 3 : Via Git (Si vous utilisez Git)

### Si votre code est versionné sur Git :

```bash
# Sur votre machine locale
git add app/Notifications/PaymentReceivedNotification.php
git add app/Notifications/ReservationCreatedNotification.php
git add app/Providers/AppServiceProvider.php
git commit -m "Activation emails notifications admin"
git push

# Sur le serveur (via SSH)
cd /chemin/vers/lux-iles
git pull
```

---

## ⚙️ Après le Transfert des Fichiers

### Étape 1 : Se connecter au serveur (SSH)

```bash
ssh votre_utilisateur@votre_serveur.com
cd /chemin/vers/lux-iles
```

### Étape 2 : Vider les caches Laravel (IMPORTANT)

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Étape 3 : Reconstruire le cache (Optionnel mais recommandé)

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Étape 4 : Optimiser l'autoloader Composer

```bash
composer dump-autoload --optimize
```

### Étape 5 : Redémarrer PHP-FPM (si nécessaire)

```bash
# Selon votre configuration PHP
sudo systemctl restart php8.2-fpm
# OU
sudo systemctl restart php8.1-fpm
# OU selon votre version PHP
```

---

## ✅ Vérification après Déploiement

### 1. Vérifier que les fichiers sont bien présents

```bash
# Sur le serveur
ls -la app/Notifications/PaymentReceivedNotification.php
ls -la app/Notifications/ReservationCreatedNotification.php
ls -la app/Providers/AppServiceProvider.php
```

### 2. Vérifier les logs Laravel (en cas de problème)

```bash
tail -f storage/logs/laravel.log
```

### 3. Tester une notification

- Créer une réservation test
- Effectuer un paiement test
- Vérifier que l'admin reçoit bien l'email

---

## 🔍 En cas de Problème

### Erreur : "Class not found"

```bash
# Sur le serveur
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Erreur : "SMTP configuration not found"

Vérifiez que les paramètres SMTP sont bien configurés dans la base de données :

```bash
# Sur le serveur, vérifier les paramètres
php artisan tinker
>>> \App\Helpers\SettingsHelper::get('email_smtp_host')
>>> \App\Helpers\SettingsHelper::get('email_smtp_username')
>>> \App\Helpers\SettingsHelper::get('email_smtp_password')
```

Ou utilisez la commande :

```bash
php artisan email:setup-config
```

---

## 📝 Résumé des Commandes Essentielles

```bash
# 1. Transférer les fichiers (depuis votre machine)
scp app/Notifications/*.php user@serveur:/chemin/app/Notifications/
scp app/Providers/AppServiceProvider.php user@serveur:/chemin/app/Providers/

# 2. Sur le serveur : Vider les caches
php artisan config:clear
php artisan cache:clear

# 3. Sur le serveur : Reconstruire les caches
php artisan config:cache

# 4. Sur le serveur : Optimiser Composer
composer dump-autoload --optimize

# 5. Redémarrer PHP-FPM (si nécessaire)
sudo systemctl restart php8.2-fpm
```

---

## ⚠️ Points Importants

1. **Toujours vider les caches** après modification de fichiers de configuration ou providers
2. **Vérifier les permissions** des fichiers (doivent être lisibles par PHP)
3. **Les emails ne fonctionneront** que si les paramètres SMTP sont configurés dans `global_settings`
4. **Pas de modification de base de données** nécessaire (les paramètres SMTP existent déjà)

---

## 📧 Vérifier la Configuration SMTP

Avant de tester, assurez-vous que les paramètres SMTP sont configurés :

```bash
php artisan email:setup-config
```

Ou vérifiez directement en base de données dans la table `global_settings` :
- `email_smtp_host`
- `email_smtp_port`
- `email_smtp_username`
- `email_smtp_password`
- `email_smtp_encryption`
- `email_from_address`
- `email_from_name`





