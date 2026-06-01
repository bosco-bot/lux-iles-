# 📧 Guide d'Utilisation - Système d'Emails avec PHPMailer

## ✅ Implémentation Complète

Le système d'emails automatiques avec PHPMailer est maintenant **100% opérationnel** !

---

## 📦 Ce qui a été créé

### 1. **EmailService** (`app/Services/EmailService.php`)
Service complet pour gérer l'envoi d'emails avec PHPMailer :
- Configuration SMTP depuis `global_settings`
- Méthodes pour chaque type d'email :
  - `sendReservationConfirmation()` - Confirmation de réservation
  - `sendPaymentReminder()` - Rappel de paiement
  - `sendPaymentConfirmation()` - Confirmation de paiement
  - `sendArrivalReminder()` - Rappel avant arrivée
  - `sendWelcomeEmail()` - Email de bienvenue
  - `sendCancellationEmail()` - Email d'annulation
  - `testEmail()` - Test de configuration

### 2. **Templates d'Emails** (`resources/views/emails/`)
7 templates HTML professionnels :
- ✅ `reservation-confirmation.blade.php`
- ✅ `payment-reminder.blade.php`
- ✅ `payment-confirmation.blade.php`
- ✅ `arrival-reminder.blade.php`
- ✅ `welcome.blade.php`
- ✅ `cancellation.blade.php`
- ✅ `test.blade.php`

### 3. **Jobs Laravel** (`app/Jobs/`)
Jobs pour envois différés en arrière-plan :
- ✅ `SendReservationConfirmationJob`
- ✅ `SendPaymentReminderJob`
- ✅ `SendArrivalReminderJob`

### 4. **Intégration dans les Contrôleurs**
- ✅ `BookingController` : Envoi automatique après création de réservation
- ✅ `PaymentController` : Envoi automatique après confirmation de paiement

### 5. **Commandes Artisan**
- ✅ `php artisan email:setup-config` - Configurer les paramètres SMTP
- ✅ `php artisan email:send-reminders` - Envoyer les rappels automatiques

### 6. **Planification Automatique**
- ✅ Tâche planifiée dans `routes/console.php` : Envoi quotidien des rappels à 9h00

---

## 🚀 Configuration Initiale

### Étape 1 : Configurer les paramètres SMTP

Exécutez la commande interactive :

```bash
php artisan email:setup-config
```

Ou avec des options :

```bash
php artisan email:setup-config \
  --smtp-host=smtp.gmail.com \
  --smtp-port=587 \
  --smtp-username=votre-email@gmail.com \
  --smtp-password=votre-mot-de-passe \
  --smtp-encryption=tls \
  --from-address=noreply@lux-iles.com \
  --from-name="LUXÎLES"
```

### Étape 2 : Tester la configuration

La commande vous proposera d'envoyer un email de test. Sinon, testez manuellement :

```bash
php artisan tinker
```

```php
$emailService = app(\App\Services\EmailService::class);
$emailService->testEmail('votre-email@example.com');
```

---

## 📋 Paramètres SMTP Stockés

Les paramètres sont stockés dans la table `global_settings` :

| Clé | Type | Description |
|-----|------|-------------|
| `email_smtp_host` | string | Serveur SMTP (ex: smtp.gmail.com) |
| `email_smtp_port` | integer | Port SMTP (587 pour TLS, 465 pour SSL) |
| `email_smtp_username` | string | Nom d'utilisateur SMTP |
| `email_smtp_password` | string | Mot de passe SMTP |
| `email_smtp_encryption` | string | Chiffrement (tls ou ssl) |
| `email_from_address` | string | Adresse email expéditeur |
| `email_from_name` | string | Nom expéditeur |

---

## 🔄 Envois Automatiques

### 1. Confirmation de Réservation
**Déclencheur** : Après création d'une réservation  
**Méthode** : `SendReservationConfirmationJob::dispatch($reservation)`  
**Fichier** : `app/Http/Controllers/BookingController.php` (ligne ~377)

### 2. Confirmation de Paiement
**Déclencheur** : Après confirmation d'un paiement Stripe  
**Méthode** : `$emailService->sendPaymentConfirmation($payment)`  
**Fichier** : `app/Http/Controllers/Api/PaymentController.php` (ligne ~160)

### 3. Rappels Automatiques
**Déclencheur** : Tâche planifiée quotidienne à 9h00  
**Commande** : `php artisan email:send-reminders`  
**Fichier** : `routes/console.php`

**Rappels envoyés automatiquement :**
- 📧 **Rappels de paiement** : 3 jours avant et le jour de l'échéance
- 📅 **Rappels d'arrivée** : 7 jours avant et 1 jour avant

---

## 💻 Utilisation Manuelle

### Envoyer un email depuis le code

```php
use App\Services\EmailService;

$emailService = app(EmailService::class);

// Confirmation de réservation
$emailService->sendReservationConfirmation($reservation);

// Rappel de paiement
$emailService->sendPaymentReminder($payment);

// Confirmation de paiement
$emailService->sendPaymentConfirmation($payment);

// Rappel d'arrivée
$emailService->sendArrivalReminder($reservation, 7); // 7 jours avant

// Email de bienvenue
$emailService->sendWelcomeEmail($user);

// Email d'annulation
$emailService->sendCancellationEmail($reservation);
```

### Utiliser les Jobs (envoi différé)

```php
use App\Jobs\SendReservationConfirmationJob;
use App\Jobs\SendPaymentReminderJob;
use App\Jobs\SendArrivalReminderJob;

// Envoyer en arrière-plan
SendReservationConfirmationJob::dispatch($reservation);
SendPaymentReminderJob::dispatch($payment);
SendArrivalReminderJob::dispatch($reservation, 7);
```

---

## 🔧 Configuration SMTP pour Gmail

Si vous utilisez Gmail, voici les paramètres :

```
SMTP Host: smtp.gmail.com
SMTP Port: 587
Encryption: tls
Username: votre-email@gmail.com
Password: votre-mot-de-passe-app (pas le mot de passe normal)
```

**Important** : Pour Gmail, vous devez :
1. Activer l'authentification à 2 facteurs
2. Générer un "Mot de passe d'application" dans les paramètres Google
3. Utiliser ce mot de passe d'application dans la configuration SMTP

---

## 🔧 Configuration SMTP pour Autres Services

### Outlook / Office 365
```
SMTP Host: smtp.office365.com
SMTP Port: 587
Encryption: tls
```

### Mailtrap (pour tests)
```
SMTP Host: smtp.mailtrap.io
SMTP Port: 2525
Encryption: tls
Username: votre-username-mailtrap
Password: votre-password-mailtrap
```

### SendGrid
```
SMTP Host: smtp.sendgrid.net
SMTP Port: 587
Encryption: tls
Username: apikey
Password: votre-clé-api-sendgrid
```

---

## 📊 Planification des Tâches

Pour que les rappels automatiques fonctionnent, vous devez configurer le scheduler Laravel :

### Sur le serveur (crontab)

Ajoutez cette ligne dans votre crontab :

```bash
* * * * * cd /chemin/vers/votre/projet && php artisan schedule:run >> /dev/null 2>&1
```

### En développement local

Pour tester la planification en local :

```bash
php artisan schedule:work
```

Cette commande exécutera les tâches planifiées toutes les minutes.

---

## 🐛 Dépannage

### Erreur : "SMTP connect() failed"
- Vérifiez que le serveur SMTP est correct
- Vérifiez le port et le chiffrement
- Vérifiez les identifiants (username/password)

### Erreur : "Authentication failed"
- Vérifiez le nom d'utilisateur et le mot de passe
- Pour Gmail, utilisez un "Mot de passe d'application"
- Vérifiez que l'authentification à 2 facteurs est activée (Gmail)

### Les emails ne partent pas
- Vérifiez les logs : `storage/logs/laravel.log`
- Activez le mode debug dans `EmailService.php` : `$this->mailer->SMTPDebug = 2;`
- Testez avec `php artisan email:setup-config` et l'option de test

### Les jobs ne s'exécutent pas
- Vérifiez que la queue est en cours d'exécution : `php artisan queue:work`
- Vérifiez la configuration de la queue dans `.env` : `QUEUE_CONNECTION=database` ou `sync`

---

## 📝 Notes Importantes

1. **Sécurité** : Les mots de passe SMTP sont stockés en clair dans `global_settings`. En production, considérez l'utilisation de variables d'environnement.

2. **Performance** : Les emails sont envoyés via des Jobs Laravel pour ne pas bloquer les requêtes HTTP.

3. **Logs** : Tous les envois d'emails sont loggés dans `storage/logs/laravel.log`.

4. **Templates** : Les templates sont personnalisables dans `resources/views/emails/`.

5. **Queue** : Pour utiliser les jobs, configurez la queue dans `.env` :
   ```
   QUEUE_CONNECTION=database
   ```
   Puis exécutez : `php artisan queue:work`

---

## ✅ Checklist de Mise en Production

- [ ] Configurer les paramètres SMTP avec `php artisan email:setup-config`
- [ ] Tester l'envoi d'un email de test
- [ ] Configurer le crontab pour le scheduler Laravel
- [ ] Configurer la queue Laravel (si utilisation des jobs)
- [ ] Vérifier que les emails partent correctement
- [ ] Vérifier les logs pour détecter d'éventuelles erreurs
- [ ] Tester tous les types d'emails (confirmation, rappels, etc.)

---

**Date de création** : 15/12/2025  
**Version** : 1.0










