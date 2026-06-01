# Vérification du Déploiement - Notifications Email Admin

## ✅ Étapes de Vérification

### 1. Vérifier que les fichiers sont bien déployés

Sur le serveur, vérifiez que les fichiers existent :

```bash
# Se connecter au serveur
ssh votre_utilisateur@votre_serveur

# Vérifier les fichiers
ls -la app/Notifications/PaymentReceivedNotification.php
ls -la app/Notifications/ReservationCreatedNotification.php
ls -la app/Providers/AppServiceProvider.php
```

### 2. Vider et reconstruire les caches (OBLIGATOIRE)

Si vous ne l'avez pas encore fait, exécutez ces commandes :

```bash
cd /chemin/vers/lux-iles  # Exemple : cd ~/lux-iles.embmission.com

# Vider les caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Reconstruire les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 3. Vérifier la Configuration SMTP en Base de Données

```bash
# Option 1 : Via tinker
php artisan tinker

# Dans tinker, exécutez :
>>> \App\Helpers\SettingsHelper::get('email_smtp_host')
>>> \App\Helpers\SettingsHelper::get('email_smtp_username')
>>> \App\Helpers\SettingsHelper::get('email_smtp_password')
>>> \App\Helpers\SettingsHelper::get('email_from_address')
>>> exit

# Option 2 : Via commande email
php artisan email:setup-config
```

**Les valeurs attendues :**
- `email_smtp_host` : Doit être défini (ex: `smtp.gmail.com`)
- `email_smtp_username` : Doit être défini (ex: `contact.luxiles@gmail.com`)
- `email_smtp_password` : Doit être défini (mot de passe d'application)
- `email_from_address` : Doit être défini (ex: `contact.luxiles@gmail.com`)

### 4. Vérifier les Logs Laravel

Surveillez les logs pour détecter d'éventuelles erreurs :

```bash
tail -f storage/logs/laravel.log
```

### 5. Tester une Notification Email

#### Test 1 : Créer une Réservation Test

1. Connectez-vous en tant que **client** sur le site
2. Créez une réservation
3. Vérifiez que l'admin reçoit un email avec :
   - Sujet : "📅 Nouvelle réservation - [numéro]"
   - Contenu détaillé avec les informations de la réservation

#### Test 2 : Effectuer un Paiement Test

1. Effectuez un paiement (acompte ou solde)
2. Vérifiez que l'admin reçoit un email avec :
   - Sujet : "💰 Nouveau paiement reçu - [numéro]"
   - Contenu détaillé avec les informations du paiement

### 6. Vérifier que les Emails sont Bien Envoyés

#### Méthode 1 : Vérifier les Logs

```bash
# Sur le serveur
grep -i "email" storage/logs/laravel.log | tail -20
```

Cherchez des messages comme :
- "Email envoyé avec succès"
- "Erreur envoi email"

#### Méthode 2 : Vérifier la Boîte Mail de l'Admin

- Connectez-vous à la boîte email de l'administrateur
- Vérifiez les emails entrants (et aussi les spams)
- Les emails doivent arriver quasi-instantanément après l'action

---

## ❌ En Cas de Problème

### Problème : Les emails ne partent pas

1. **Vérifier les paramètres SMTP** :
   ```bash
   php artisan email:setup-config
   ```

2. **Vérifier les logs** :
   ```bash
   tail -n 50 storage/logs/laravel.log
   ```

3. **Tester l'envoi d'email** :
   ```bash
   php artisan tinker
   >>> $emailService = app(\App\Services\EmailService::class);
   >>> $emailService->testEmail('votre-email@test.com');
   >>> exit
   ```

### Problème : "Class not found" ou erreur de cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan config:cache
composer dump-autoload
```

### Problème : Configuration SMTP non trouvée

Vérifiez que les paramètres sont bien dans la table `global_settings` :

```sql
-- Via MySQL
SELECT * FROM global_settings WHERE `key` LIKE 'email_%';
```

Ou via tinker :
```bash
php artisan tinker
>>> DB::table('global_settings')->where('key', 'like', 'email_%')->get();
```

---

## ✅ Checklist de Vérification Finale

- [ ] Les 3 fichiers sont présents sur le serveur
- [ ] Les caches ont été vidés et reconstruits
- [ ] La configuration SMTP est présente en base de données
- [ ] Les logs ne montrent pas d'erreurs
- [ ] Test de création de réservation : email reçu
- [ ] Test de paiement : email reçu

---

## 🎯 Résultat Attendu

**Lorsqu'une réservation est créée :**
- ✅ L'admin reçoit un email avec les détails de la réservation
- ✅ Une notification apparaît dans l'interface admin (en base)
- ✅ Une notification temps réel apparaît (broadcast)

**Lorsqu'un paiement est effectué :**
- ✅ L'admin reçoit un email avec les détails du paiement
- ✅ Une notification apparaît dans l'interface admin (en base)
- ✅ Une notification temps réel apparaît (broadcast)

---

## 📧 Configuration Email Requise

Pour que les emails fonctionnent, assurez-vous que ces paramètres sont configurés dans `global_settings` :

- `email_smtp_host` (ex: `smtp.gmail.com`)
- `email_smtp_port` (ex: `587`)
- `email_smtp_username` (ex: `contact.luxiles@gmail.com`)
- `email_smtp_password` (mot de passe d'application Gmail)
- `email_smtp_encryption` (ex: `tls`)
- `email_from_address` (ex: `contact.luxiles@gmail.com`)
- `email_from_name` (ex: `LUXÎLES`)





