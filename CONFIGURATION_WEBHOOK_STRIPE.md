# 🔗 CONFIGURATION WEBHOOK STRIPE

## 📋 Informations actuelles

**Route webhook configurée :**
- URL : `/api/payments/webhook/stripe`
- Méthode : `POST`
- Contrôleur : `App\Http\Controllers\Api\PaymentController@webhook`
- Secret stocké dans : `global_settings.stripe_webhook_secret`

## 🚀 Configuration sur Stripe Dashboard

### **ÉTAPE 1 : Accéder au dashboard Stripe**

1. **Connectez-vous** : https://dashboard.stripe.com
2. **Mode TEST** ou **PRODUCTION** selon votre environnement

### **ÉTAPE 2 : Créer le webhook**

1. **Allez dans** : Développeurs → Webhooks
2. **Cliquez** : "Ajouter un endpoint"
3. **URL du endpoint** :
   - **TEST** : `https://lux-iles.embmission.com/api/payments/webhook/stripe`
   - **PRODUCTION** : `https://votredomaine.com/api/payments/webhook/stripe`

4. **Événements à écouter** (sélectionnez) :
   - ✅ `payment_intent.succeeded`
   - ✅ `payment_intent.payment_failed`
   - ✅ `payment_intent.canceled`

5. **Cliquez** : "Ajouter endpoint"

### **ÉTAPE 3 : Récupérer le secret**

Après création, Stripe vous donne un **Secret webhook** qui commence par `whsec_...`

**COPIEZ CE SECRET** - il ne sera plus visible après !

---

## ⚙️ Configuration dans votre base de données

### **Via phpMyAdmin/cPanel :**

**Requête SQL à exécuter :**
```sql
UPDATE `global_settings`
SET `value` = 'VOTRE_SECRET_WEBHOOK_ICI'
WHERE `key` = 'stripe_webhook_secret';
```

**Remplacez** `VOTRE_SECRET_WEBHOOK_ICI` par le secret réel (whsec_...)

### **Via Laravel Tinker (si accès SSH) :**
```bash
cd public_html/lux-iles
php artisan tinker

# Dans Tinker :
App\Helpers\SettingsHelper::set('stripe_webhook_secret', 'whsec_...', 'string');
exit;
```

---

## ✅ Vérification

### **Test du webhook :**

1. **Effectuez un paiement test** sur votre site
2. **Vérifiez les logs Laravel** :
   ```bash
   tail -f storage/logs/laravel.log
   ```

3. **Vous devriez voir** :
   ```
   [INFO] Webhook Stripe traité : payment_intent.succeeded
   ```

### **Événements gérés :**

- `payment_intent.succeeded` → Met à jour le paiement en "completed"
- `payment_intent.payment_failed` → Met à jour le paiement en "failed"
- `payment_intent.canceled` → Met à jour le paiement en "cancelled"

---

## 🔐 Sécurité

- ✅ **Vérification de signature** Stripe activée
- ✅ **Secret stocké** de manière sécurisée
- ✅ **Logs détaillés** pour monitoring
- ✅ **Gestion d'erreurs** robuste

---

## 📞 Support

**Si problème :**
1. Vérifiez que l'URL est accessible : `https://votre-domaine.com/api/payments/webhook/stripe`
2. Vérifiez que le secret est correctement stocké
3. Consultez les logs Laravel pour les erreurs

---

## 🎯 RÉCAPITULATIF À ME FOURNIR

Pour finaliser la configuration :

1. **Le Secret Webhook** (whsec_...) de Stripe
2. **Le domaine utilisé** (lux-iles.embmission.com ou autre)
3. **Le mode** (TEST ou PRODUCTION)

Une fois ces informations fournies, je pourrai vous aider à configurer le webhook dans votre base de données !