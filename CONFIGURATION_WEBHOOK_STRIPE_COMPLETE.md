# 🔗 Configuration des Webhooks Stripe - Guide Complet

## 📋 Vue d'ensemble

Les webhooks Stripe permettent à votre application de recevoir des notifications en temps réel lorsque des événements se produisent dans Stripe (paiement réussi, échoué, remboursement, etc.).

---

## 🗄️ 1. STOCKAGE DES INFORMATIONS DANS L'APPLICATION

### **Base de données : Table `global_settings`**

Les informations de webhook sont stockées dans la table `global_settings` :

| Clé | Description | Format | Exemple |
|-----|-------------|--------|---------|
| `stripe_webhook_secret` | Secret de signature du webhook | `whsec_...` | `whsec_abc123...` |

### **Accès via le code :**

```php
use App\Helpers\SettingsHelper;

// Récupérer le secret webhook
$webhookSecret = SettingsHelper::get('stripe_webhook_secret');

// Définir le secret webhook
SettingsHelper::set('stripe_webhook_secret', 'whsec_...', 'string');
```

---

## 🛣️ 2. ROUTE WEBHOOK DANS L'APPLICATION

### **Fichier : `routes/api.php`**

```php
// Route webhook Stripe (publique, sans CSRF)
Route::post('/payments/webhook/stripe', 
    [\App\Http\Controllers\Api\PaymentController::class, 'webhook']
)->name('api.payments.webhook.stripe');
```

### **URL complète :**
```
https://lux-iles.embmission.com/api/payments/webhook/stripe
```

**⚠️ IMPORTANT :** Cette route est **publique** (sans authentification) car Stripe doit pouvoir y accéder directement.

---

## 🎯 3. CONTRÔLEUR WEBHOOK

### **Fichier : `app/Http/Controllers/Api/PaymentController.php`**

La méthode `webhook()` :

1. **Récupère le payload** (données de l'événement)
2. **Récupère la signature** depuis les headers Stripe
3. **Vérifie la signature** avec le secret webhook
4. **Traite l'événement** selon son type
5. **Retourne une réponse** à Stripe

```php
public function webhook(Request $request)
{
    $payload = $request->getContent();
    $signature = $request->header('Stripe-Signature');
    $secret = SettingsHelper::get('stripe_webhook_secret');

    if (!$secret) {
        Log::warning('Secret webhook Stripe non configuré');
        return response('Webhook secret non configuré', 500);
    }

    try {
        $event = $this->paymentService->handleWebhook($payload, $signature, $secret);
        
        return response()->json([
            'received' => true,
            'event_id' => $event->id,
            'event_type' => $event->type,
        ]);
    } catch (\Exception $e) {
        Log::error('Erreur lors du traitement du webhook Stripe', [
            'error' => $e->getMessage(),
        ]);
        return response('Erreur lors du traitement du webhook', 400);
    }
}
```

---

## 🔧 4. SERVICE DE TRAITEMENT DES WEBHOOKS

### **Fichier : `app/Services/PaymentService.php`**

Le service `handleWebhook()` traite les différents types d'événements :

#### **Événements gérés :**

| Événement Stripe | Action dans l'application |
|------------------|---------------------------|
| `payment_intent.succeeded` | ✅ Marque le paiement comme "completed"<br>✅ Met à jour le statut de la réservation |
| `payment_intent.payment_failed` | ❌ Marque le paiement comme "failed"<br>❌ Enregistre la raison de l'échec |
| `payment_intent.canceled` | 🚫 Marque le paiement comme "cancelled" |
| `charge.refunded` | 💰 Marque le paiement comme "refunded"<br>💰 Crée un enregistrement de remboursement |

---

## ⚙️ 5. CONFIGURATION CÔTÉ STRIPE

### **Étape 1 : Accéder au Dashboard Stripe**

1. Connectez-vous à [https://dashboard.stripe.com](https://dashboard.stripe.com)
2. Sélectionnez le mode approprié :
   - **Mode TEST** : [https://dashboard.stripe.com/test](https://dashboard.stripe.com/test)
   - **Mode LIVE** : [https://dashboard.stripe.com](https://dashboard.stripe.com)

### **Étape 2 : Créer un endpoint webhook**

1. Allez dans **Developers** → **Webhooks**
2. Cliquez sur **Add endpoint**
3. Remplissez les informations :

   **URL de l'endpoint :**
   ```
   https://lux-iles.embmission.com/api/payments/webhook/stripe
   ```

   **Événements à écouter :**
   - ✅ `payment_intent.succeeded`
   - ✅ `payment_intent.payment_failed`
   - ✅ `payment_intent.canceled`
   - ✅ `charge.refunded`

4. Cliquez sur **Add endpoint**

### **Étape 3 : Récupérer le "Signing secret"**

1. Après création, cliquez sur l'endpoint créé
2. Dans la section **Signing secret**, cliquez sur **Reveal**
3. **Copiez le secret** (commence par `whsec_...`)

### **Étape 4 : Configurer dans l'application**

#### **Méthode 1 : Via commande Artisan (RECOMMANDÉ)**

```bash
php artisan stripe:setup-keys \
  --public-key="pk_live_..." \
  --secret-key="sk_live_..." \
  --webhook-secret="whsec_..."
```

#### **Méthode 2 : Via base de données**

```sql
UPDATE global_settings 
SET value = 'whsec_VOTRE_SECRET_ICI' 
WHERE `key` = 'stripe_webhook_secret';
```

#### **Méthode 3 : Via script PHP**

```php
<?php
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Helpers\SettingsHelper;

SettingsHelper::set('stripe_webhook_secret', 'whsec_VOTRE_SECRET', 'string');
SettingsHelper::clearCache();

echo "Secret webhook configuré !\n";
```

---

## 🔐 6. SÉCURITÉ DES WEBHOOKS

### **Vérification de la signature**

L'application vérifie automatiquement que les webhooks proviennent bien de Stripe en utilisant :

1. **Le secret webhook** stocké dans `global_settings`
2. **La signature** envoyée dans le header `Stripe-Signature`
3. **Le payload** (contenu de la requête)

Si la signature ne correspond pas, le webhook est **rejeté** et une erreur est loggée.

### **Protection CSRF**

La route webhook est **exclue de la protection CSRF** car Stripe ne peut pas fournir de token CSRF.

---

## 📊 7. TRAITEMENT DES ÉVÉNEMENTS

### **Exemple : Paiement réussi**

1. **Client paie** sur votre site
2. **Stripe envoie** un webhook `payment_intent.succeeded`
3. **L'application reçoit** le webhook
4. **Vérification** de la signature
5. **Mise à jour** du paiement :
   - Statut : `pending` → `completed`
   - `paid_at` : date actuelle
   - `stripe_charge_id` : ID de la charge
6. **Mise à jour** de la réservation :
   - Si arrhes : `pending` → `deposit_paid`
   - Si solde : `deposit_paid` → `fully_paid`
7. **Réponse** à Stripe : `200 OK`

---

## 🧪 8. TEST DES WEBHOOKS

### **Via le Dashboard Stripe**

1. Allez dans **Developers** → **Webhooks**
2. Cliquez sur votre endpoint
3. Cliquez sur **Send test webhook**
4. Sélectionnez un événement (ex: `payment_intent.succeeded`)
5. Cliquez sur **Send test webhook**

### **Vérification dans l'application**

1. **Vérifiez les logs** : `storage/logs/laravel.log`
2. **Vérifiez la base de données** : Table `payments`
3. **Vérifiez le statut** de la réservation

---

## 📝 9. LOGS ET DÉBOGAGE

### **Logs automatiques**

L'application enregistre automatiquement :

- ✅ Réception d'un webhook
- ✅ Type d'événement
- ✅ ID de l'événement
- ❌ Erreurs de traitement
- ⚠️ Avertissements (secret manquant, etc.)

### **Fichier de logs**

```
storage/logs/laravel.log
```

### **Exemple de log**

```
[2026-01-23 10:30:45] local.INFO: Webhook Stripe reçu {
    "event_type": "payment_intent.succeeded",
    "event_id": "evt_1234567890"
}

[2026-01-23 10:30:45] local.INFO: Paiement confirmé avec succès {
    "payment_id": 123,
    "reservation_id": 456,
    "amount": 4368.83
}
```

---

## ⚠️ 10. PROBLÈMES COURANTS

### **Problème 1 : Secret webhook non configuré**

**Symptôme :** Erreur 500 dans les logs

**Solution :** Configurer le secret webhook (voir section 5)

### **Problème 2 : Signature invalide**

**Symptôme :** Erreur 400 dans les logs

**Causes possibles :**
- Secret webhook incorrect
- URL webhook incorrecte
- Payload modifié en transit

**Solution :** Vérifier le secret et l'URL dans Stripe

### **Problème 3 : Webhook non reçu**

**Causes possibles :**
- URL inaccessible depuis Internet
- Firewall bloquant les requêtes Stripe
- Route non configurée correctement

**Solution :** 
- Vérifier que l'URL est accessible publiquement
- Tester avec `curl` ou un outil en ligne
- Vérifier les logs du serveur

---

## 🎯 11. RÉSUMÉ DE LA CONFIGURATION

### **Checklist de configuration :**

- [ ] **Clés Stripe configurées** (public + secret)
- [ ] **Endpoint webhook créé** dans Stripe Dashboard
- [ ] **URL webhook correcte** : `https://lux-iles.embmission.com/api/payments/webhook/stripe`
- [ ] **Événements sélectionnés** : `payment_intent.succeeded`, `payment_intent.payment_failed`, etc.
- [ ] **Secret webhook récupéré** depuis Stripe
- [ ] **Secret webhook configuré** dans `global_settings`
- [ ] **Route webhook accessible** publiquement
- [ ] **Test webhook envoyé** depuis Stripe Dashboard
- [ ] **Logs vérifiés** pour confirmer la réception

---

## 📞 12. SUPPORT

En cas de problème :

1. **Vérifiez les logs** : `storage/logs/laravel.log`
2. **Vérifiez le Dashboard Stripe** : Section Webhooks → Logs
3. **Testez l'endpoint** avec un outil comme Postman
4. **Vérifiez la configuration** dans `global_settings`

---

**✅ Configuration terminée ! Les webhooks Stripe sont maintenant opérationnels.** 🎉
