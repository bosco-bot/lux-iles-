# URL Webhook Stripe - Configuration

## 🔗 **ROUTE WEBHOOK STRIPE**

### Route définie dans le code :
```
/api/payments/webhook/stripe
```

### Fichier : `routes/api.php` (ligne 44)
```php
Route::post('/payments/webhook/stripe', [\App\Http\Controllers\Api\PaymentController::class, 'webhook'])
    ->name('api.payments.webhook.stripe');
```

---

## 🌐 **URLS COMPLÈTES**

### En LOCAL (développement) :
```
http://localhost/api/payments/webhook/stripe
```
ou si vous utilisez un port différent :
```
http://localhost:8000/api/payments/webhook/stripe
```

### En PRODUCTION :
```
https://votredomaine.com/api/payments/webhook/stripe
```

**Exemple avec le domaine réel :**
```
https://lux-iles.embmission.com/api/payments/webhook/stripe
```
ou
```
https://www.luxiles.fr/api/payments/webhook/stripe
```

---

## ⚙️ **CONFIGURATION DANS STRIPE**

### 1. Accéder au Tableau de Bord Stripe
1. Connectez-vous à : https://dashboard.stripe.com
2. Allez dans **Développeurs** → **Webhooks**
3. Cliquez sur **Ajouter un endpoint**

### 2. Remplir les informations

**URL de l'endpoint :**
```
https://votredomaine.com/api/payments/webhook/stripe
```
*(Remplacez `votredomaine.com` par votre vrai domaine)*

**Événements à écouter :**
Sélectionnez les événements suivants :
- ✅ `payment_intent.succeeded`
- ✅ `payment_intent.payment_failed`
- ✅ `charge.succeeded`
- ✅ `charge.failed`
- ✅ `payment_intent.canceled`

*(Ou sélectionnez "Recevoir tous les événements" pour tester)*

### 3. Copier le Secret Webhook

Après la création, Stripe génère un **Secret Webhook** qui commence par `whsec_...`

**⚠️ IMPORTANT :** Ce secret doit être ajouté dans votre application :
- Soit dans la table `global_settings` avec la clé `stripe_webhook_secret`
- Soit dans le fichier `.env` avec `STRIPE_WEBHOOK_SECRET=whsec_...`

---

## 📋 **ÉTAPES COMPLÈTES**

### Étape 1 : Obtenir l'URL complète
```
https://votredomaine.com/api/payments/webhook/stripe
```

### Étape 2 : Dans Stripe Dashboard
1. **Développeurs** → **Webhooks**
2. **Ajouter un endpoint**
3. Coller l'URL complète
4. Sélectionner les événements
5. **Ajouter l'endpoint**

### Étape 3 : Copier le Secret Webhook
- Le secret commence par `whsec_...`
- Le copier dans `global_settings` → `stripe_webhook_secret`
- Ou dans `.env` → `STRIPE_WEBHOOK_SECRET`

### Étape 4 : Tester
- Stripe peut envoyer un événement de test
- Vérifier les logs de votre application

---

## ✅ **FORMAT FINAL POUR STRIPE**

**URL à copier dans Stripe Dashboard :**
```
https://votredomaine.com/api/payments/webhook/stripe
```

**Remplacez `votredomaine.com` par :**
- Votre domaine de production réel
- Exemple : `lux-iles.embmission.com` ou `www.luxiles.fr`

---

## 🔒 **SÉCURITÉ**

- ✅ La route est publique (pas de middleware auth)
- ✅ La sécurité est assurée par la vérification de la signature Stripe
- ✅ Le contrôleur vérifie le `stripe_webhook_secret` pour valider les requêtes

---

## 📝 **NOTE IMPORTANTE**

**En développement local :**
- Utilisez Stripe CLI pour tester les webhooks localement
- Ou utilisez un service comme ngrok pour exposer votre localhost

**En production :**
- L'URL doit être accessible publiquement (HTTPS requis)
- Le secret webhook doit être stocké de manière sécurisée






