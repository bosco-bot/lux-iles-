# 🎯 CE QUE VOUS DEVEZ FAIRE : Configuration Webhook Stripe

## ✅ CE QUI EST DÉJÀ FAIT DANS LE PROJET

### **Code implémenté (100% prêt) :**

1. ✅ **Route webhook** configurée
   - Fichier : `routes/api.php`
   - URL : `https://lux-iles.embmission.com/api/payments/webhook/stripe`
   - Méthode : `POST`

2. ✅ **Contrôleur webhook** implémenté
   - Fichier : `app/Http/Controllers/Api/PaymentController.php`
   - Méthode : `webhook()`
   - Récupère le payload et la signature
   - Vérifie la sécurité

3. ✅ **Service de traitement** implémenté
   - Fichier : `app/Services/PaymentService.php`
   - Méthode : `handleWebhook()`
   - Traite tous les événements Stripe

4. ✅ **Événements gérés** :
   - ✅ `payment_intent.succeeded` → Paiement réussi
   - ✅ `payment_intent.payment_failed` → Paiement échoué
   - ✅ `payment_intent.canceled` → Paiement annulé
   - ✅ `charge.refunded` → Remboursement

5. ✅ **Sécurité** :
   - Vérification de signature automatique
   - Protection contre les webhooks frauduleux
   - Logs des erreurs

6. ✅ **Mise à jour automatique** :
   - Statut des paiements
   - Statut des réservations
   - Enregistrement des transactions

---

## ❌ CE QUE VOUS DEVEZ FAIRE (VOTRE PARTIE)

### **Étape 1 : Accéder au Dashboard Stripe**

1. Allez sur [https://dashboard.stripe.com](https://dashboard.stripe.com)
2. **Choisissez le bon mode** :
   - **Mode TEST** : [https://dashboard.stripe.com/test](https://dashboard.stripe.com/test) (pour tester)
   - **Mode LIVE** : [https://dashboard.stripe.com](https://dashboard.stripe.com) (pour la production)

---

### **Étape 2 : Créer l'endpoint webhook**

1. Dans le menu de gauche, cliquez sur **"Developers"**
2. Cliquez sur **"Webhooks"**
3. Cliquez sur le bouton **"Add endpoint"** (en haut à droite)

4. **Remplissez le formulaire** :

   **Endpoint URL :**
   ```
   https://lux-iles.embmission.com/api/payments/webhook/stripe
   ```

   **Description (optionnel) :**
   ```
   Webhook LUXÎLES - Gestion des paiements
   ```

5. **Sélectionnez les événements à écouter** :

   Cochez ces 4 événements :
   - ✅ `payment_intent.succeeded`
   - ✅ `payment_intent.payment_failed`
   - ✅ `payment_intent.canceled`
   - ✅ `charge.refunded`

6. Cliquez sur **"Add endpoint"**

---

### **Étape 3 : Récupérer le "Signing secret"**

1. Après création, vous verrez votre endpoint dans la liste
2. **Cliquez sur l'endpoint** que vous venez de créer
3. Dans la section **"Signing secret"**, vous verrez :
   ```
   whsec_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
   ```
4. Cliquez sur **"Reveal"** pour afficher le secret complet
5. **Copiez le secret** (il commence par `whsec_`)

⚠️ **IMPORTANT :** Gardez ce secret en sécurité, ne le partagez pas !

---

### **Étape 4 : Configurer le secret dans l'application**

Vous avez **3 méthodes** pour configurer le secret :

#### **Méthode 1 : Via commande Artisan (RECOMMANDÉ)**

Sur votre serveur, exécutez :

```bash
php artisan stripe:setup-keys \
  --webhook-secret="whsec_VOTRE_SECRET_ICI"
```

**Exemple complet avec toutes les clés :**
```bash
php artisan stripe:setup-keys \
  --public-key="pk_live_51Sii3lPatYzDdo0Y..." \
  --secret-key="sk_live_51Sii3lPatYzDdo0Y..." \
  --webhook-secret="whsec_abc123..."
```

#### **Méthode 2 : Via base de données (phpMyAdmin)**

1. Connectez-vous à phpMyAdmin
2. Sélectionnez votre base de données
3. Allez dans la table `global_settings`
4. Trouvez la ligne avec `key = 'stripe_webhook_secret'`
5. Modifiez la valeur dans la colonne `value` :
   ```
   whsec_VOTRE_SECRET_ICI
   ```
6. Cliquez sur **"Exécuter"**

**OU** exécutez cette requête SQL :
```sql
UPDATE global_settings 
SET value = 'whsec_VOTRE_SECRET_ICI' 
WHERE `key` = 'stripe_webhook_secret';
```

#### **Méthode 3 : Via script PHP**

Créez un fichier `setup-webhook-secret.php` à la racine :

```php
<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

// Remplacez par votre secret webhook
$webhookSecret = 'whsec_VOTRE_SECRET_ICI';

SettingsHelper::set('stripe_webhook_secret', $webhookSecret, 'string');
SettingsHelper::clearCache();

echo "✅ Secret webhook configuré avec succès !\n";
echo "Secret : " . substr($webhookSecret, 0, 20) . "...\n";
```

Puis exécutez :
```bash
php setup-webhook-secret.php
```

---

### **Étape 5 : Vider le cache**

Après configuration, videz le cache Laravel :

```bash
php artisan cache:clear
php artisan config:clear
```

---

### **Étape 6 : Tester le webhook**

1. Retournez dans **Stripe Dashboard** → **Developers** → **Webhooks**
2. Cliquez sur votre endpoint
3. Cliquez sur **"Send test webhook"**
4. Sélectionnez un événement (ex: `payment_intent.succeeded`)
5. Cliquez sur **"Send test webhook"**

6. **Vérifiez les logs** de votre application :
   ```bash
   tail -f storage/logs/laravel.log
   ```

   Vous devriez voir :
   ```
   Webhook Stripe reçu {"event_type":"payment_intent.succeeded",...}
   ```

---

## 📋 CHECKLIST DE CONFIGURATION

Cochez chaque étape au fur et à mesure :

- [ ] **Accès au Dashboard Stripe** (mode TEST ou LIVE)
- [ ] **Endpoint webhook créé** dans Stripe
- [ ] **URL correcte** : `https://lux-iles.embmission.com/api/payments/webhook/stripe`
- [ ] **4 événements sélectionnés** :
  - [ ] `payment_intent.succeeded`
  - [ ] `payment_intent.payment_failed`
  - [ ] `payment_intent.canceled`
  - [ ] `charge.refunded`
- [ ] **Secret webhook récupéré** (commence par `whsec_`)
- [ ] **Secret configuré** dans l'application (via Artisan, SQL ou script)
- [ ] **Cache vidé** (`php artisan cache:clear`)
- [ ] **Test webhook envoyé** depuis Stripe
- [ ] **Logs vérifiés** (confirmation de réception)

---

## ⚠️ IMPORTANT : MODE TEST vs LIVE

### **Mode TEST :**
- Utilisez pour **tester** les fonctionnalités
- Clés : `pk_test_...` et `sk_test_...`
- Webhook secret : `whsec_test_...`
- Dashboard : [https://dashboard.stripe.com/test](https://dashboard.stripe.com/test)

### **Mode LIVE :**
- Utilisez pour la **production** (vrais paiements)
- Clés : `pk_live_...` et `sk_live_...`
- Webhook secret : `whsec_live_...`
- Dashboard : [https://dashboard.stripe.com](https://dashboard.stripe.com)

⚠️ **Vous devez créer un endpoint webhook pour CHAQUE mode (TEST et LIVE) !**

---

## 🆘 EN CAS DE PROBLÈME

### **Problème 1 : Secret non configuré**

**Symptôme :** Erreur dans les logs "Webhook secret non configuré"

**Solution :** Vérifiez que le secret est bien stocké dans `global_settings`

### **Problème 2 : Signature invalide**

**Symptôme :** Erreur 400 "Invalid signature"

**Solution :** 
- Vérifiez que le secret est correct
- Vérifiez que l'URL webhook est exactement : `https://lux-iles.embmission.com/api/payments/webhook/stripe`

### **Problème 3 : Webhook non reçu**

**Symptôme :** Aucun log dans `laravel.log`

**Solution :**
- Vérifiez que l'URL est accessible publiquement
- Vérifiez les logs du serveur (Apache/Nginx)
- Testez avec un outil comme [webhook.site](https://webhook.site)

---

## 📞 RÉSUMÉ

**Ce qui est fait :** ✅ 100% du code est prêt

**Ce que vous devez faire :**
1. Créer l'endpoint dans Stripe Dashboard
2. Récupérer le secret webhook
3. Configurer le secret dans l'application
4. Tester

**Temps estimé :** 10-15 minutes

---

**Une fois ces étapes terminées, les webhooks Stripe seront opérationnels !** 🎉
