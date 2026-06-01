# 🔔 Rôle des Webhooks Stripe

**Date** : 29/12/2025

---

## 🎯 QU'EST-CE QU'UN WEBHOOK ?

Un **webhook** est un mécanisme qui permet à Stripe d'**envoyer automatiquement des notifications** à votre serveur lorsque certains événements se produisent (paiement réussi, échec, remboursement, etc.).

C'est comme un **"téléphone"** entre Stripe et votre application : Stripe appelle votre serveur pour lui dire "hey, un paiement vient d'être confirmé !".

---

## 🔄 POURQUOI LES WEBHOOKS SONT ESSENTIELS ?

### Problème sans Webhook

Sans webhook, votre application ne sait qu'un paiement est réussi que si :
1. Le client reste sur la page
2. La connexion internet est stable
3. Le client ne ferme pas la page avant la confirmation

**Problème** : Si le client ferme la page ou perd la connexion, votre serveur ne saura jamais que le paiement a réussi !

### Solution avec Webhook

Avec les webhooks, **Stripe garantit** que votre serveur sera notifié :
- ✅ Même si le client ferme la page
- ✅ Même si la connexion est instable
- ✅ Même si le serveur était temporairement indisponible (Stripe réessaie)
- ✅ Double sécurité (confirmation côté client + webhook)

---

## 📊 ROLE DES WEBHOOKS DANS VOTRE APPLICATION

### 1. ✅ Double Vérification

Votre application utilise **deux mécanismes** pour confirmer un paiement :

1. **Confirmation côté client** (`PaymentController@confirm`)
   - Le client paie → Votre application confirme immédiatement
   - Mise à jour rapide pour l'utilisateur

2. **Webhook Stripe** (`PaymentService->handleWebhook`)
   - Stripe envoie une notification → Votre serveur vérifie
   - Garantit la synchronisation même si le client ferme la page

**Résultat** : Double sécurité pour garantir que le paiement est toujours traité.

---

### 2. 🔔 Événements Gérés par les Webhooks

Votre application écoute **4 types d'événements** Stripe :

#### a) `payment_intent.succeeded` ✅
**Quand** : Un paiement a réussi

**Actions automatiques** :
- ✅ Met à jour le paiement : `status = 'completed'`
- ✅ Met à jour la réservation : `status = 'deposit_paid'` ou `fully_paid`
- ✅ Génère les documents PDF (contrat, facture, reçus)
- ✅ Envoie des emails de confirmation
- ✅ Crée des notifications admin

**Code** :
```php
case 'payment_intent.succeeded':
    $this->handlePaymentIntentSucceeded($event->data->object);
    // → Met à jour le paiement et la réservation automatiquement
    break;
```

---

#### b) `payment_intent.payment_failed` ❌
**Quand** : Un paiement a échoué (carte refusée, fonds insuffisants, etc.)

**Actions automatiques** :
- ❌ Met à jour le paiement : `status = 'failed'`
- 📝 Enregistre la raison de l'échec
- ⚠️ La réservation reste en `pending` ou `deposit_paid`

**Code** :
```php
case 'payment_intent.payment_failed':
    $this->handlePaymentIntentFailed($event->data->object);
    // → Marque le paiement comme échoué
    break;
```

---

#### c) `payment_intent.canceled` 🚫
**Quand** : Un paiement a été annulé

**Actions automatiques** :
- 🚫 Met à jour le paiement : `status = 'failed'`
- 📝 Enregistre l'annulation

---

#### d) `charge.refunded` 💰
**Quand** : Un remboursement a été effectué

**Actions automatiques** :
- 💰 Met à jour le paiement : `status = 'refunded'`
- 📝 Met à jour la réservation si nécessaire

---

## 🔄 FLUX COMPLET AVEC WEBHOOKS

```
1. Client paie via Stripe
   ↓
2. Client confirme le paiement (côté client)
   → Votre serveur confirme immédiatement
   → Statut mis à jour rapidement
   ↓
3. Stripe traite le paiement (en arrière-plan)
   ↓
4. Stripe envoie un webhook à votre serveur
   → payment_intent.succeeded
   ↓
5. Votre serveur vérifie et confirme (double sécurité)
   → Garantit que le paiement est vraiment traité
   → Génère les documents
   → Envoie les emails
```

---

## 🛡️ SÉCURITÉ DES WEBHOOKS

### Signature de Sécurité

Stripe **signe** chaque webhook avec une clé secrète pour garantir qu'il vient bien de Stripe :

```php
$event = Webhook::constructEvent($payload, $signature, $secret);
```

**Protection** :
- ✅ Vérifie que le webhook vient vraiment de Stripe
- ✅ Empêche les attaques de falsification
- ✅ Utilise le secret webhook stocké dans `global_settings`

---

## 📍 CONFIGURATION DES WEBHOOKS

### 1. Dans le Code (Déjà configuré ✅)

**Route** : `/api/payments/webhook/stripe`

**Code** :
```php
Route::post('/webhook/stripe', [PaymentController::class, 'webhook'])
    ->name('api.payments.webhook.stripe');
```

### 2. Dans Stripe Dashboard (À configurer)

1. Aller dans **Stripe Dashboard** → **Developers** → **Webhooks**
2. Cliquer sur **"Add endpoint"**
3. URL : `https://votre-domaine.com/api/payments/webhook/stripe`
4. Événements à écouter :
   - `payment_intent.succeeded`
   - `payment_intent.payment_failed`
   - `payment_intent.canceled`
   - `charge.refunded`
5. Copier le **Webhook signing secret** (`whsec_...`)
6. L'enregistrer dans votre application via :
   ```bash
   php artisan stripe:setup-keys --webhook-secret=whsec_...
   ```

---

## 🎯 AVANTAGES DES WEBHOOKS

### ✅ Fiabilité
- Garantit la synchronisation même en cas de problème réseau
- Stripe réessaie automatiquement si votre serveur est indisponible

### ✅ Double Sécurité
- Confirmation immédiate côté client (meilleure UX)
- Vérification via webhook (garantit la sincérité)

### ✅ Traçabilité
- Tous les événements sont loggés
- Facile à débugger en cas de problème

### ✅ Automatisation
- Pas besoin d'intervention manuelle
- Documents générés automatiquement
- Emails envoyés automatiquement

---

## ⚠️ POINTS D'ATTENTION

### 1. Idempotence
Les webhooks peuvent être envoyés **plusieurs fois** (en cas de retry).  
**Solution** : Votre code vérifie toujours l'état actuel avant de mettre à jour.

### 2. Ordre des Événements
Les webhooks peuvent arriver **dans le désordre**.  
**Solution** : Votre code vérifie l'état final du paiement, pas seulement l'événement.

### 3. Sécurité
Toujours vérifier la **signature** du webhook.  
**Solution** : Votre code utilise `Webhook::constructEvent()` qui vérifie la signature.

---

## 📊 EXEMPLE CONCRET

### Scénario : Client paie et ferme la page

**Sans webhook** :
1. Client paie → Paiement réussi sur Stripe ✅
2. Client ferme la page avant la confirmation ❌
3. Votre serveur ne sait pas que le paiement a réussi ❌
4. Réservation reste en `pending` ❌
5. Problème ! ❌

**Avec webhook** :
1. Client paie → Paiement réussi sur Stripe ✅
2. Client ferme la page avant la confirmation ❌
3. **Stripe envoie un webhook** ✅
4. Votre serveur reçoit la notification ✅
5. Réservation mise à jour automatiquement ✅
6. Documents générés automatiquement ✅
7. Email envoyé au client ✅
8. Tout fonctionne ! ✅

---

## 🔧 COMMENT TESTER LES WEBHOOKS

### 1. En développement local (avec ngrok)

```bash
# Démarrer ngrok
ngrok http 8000

# Copier l'URL (ex: https://abc123.ngrok.io)
# L'ajouter dans Stripe Dashboard comme URL de webhook
# URL complète : https://abc123.ngrok.io/api/payments/webhook/stripe
```

### 2. Vérifier les logs

Dans `storage/logs/laravel.log`, vous verrez :
```
Webhook Stripe reçu: payment_intent.succeeded
Paiement confirmé avec succès: payment_id=123
```

### 3. Vérifier dans Stripe Dashboard

Dans **Developers** → **Webhooks** → Votre endpoint :
- Voir les tentatives de livraison
- Voir les événements envoyés
- Voir les erreurs éventuelles

---

## 📋 RÉSUMÉ

### Rôle Principal
**Garantir la synchronisation** entre Stripe et votre application, même si le client ferme la page ou perd la connexion.

### Événements Gérés
1. ✅ `payment_intent.succeeded` → Paiement réussi
2. ❌ `payment_intent.payment_failed` → Paiement échoué
3. 🚫 `payment_intent.canceled` → Paiement annulé
4. 💰 `charge.refunded` → Remboursement effectué

### Actions Automatiques
- ✅ Mise à jour du statut des paiements
- ✅ Mise à jour du statut des réservations
- ✅ Génération des documents PDF
- ✅ Envoi des emails
- ✅ Création de notifications

### Sécurité
- ✅ Signature vérifiée avec secret webhook
- ✅ Protection contre les falsifications
- ✅ Double vérification (client + webhook)

---

## ✅ CONCLUSION

Les webhooks Stripe sont **essentiels** pour garantir que votre application est toujours synchronisée avec Stripe, même en cas de problème réseau ou si le client ferme la page.

**Sans webhook** : Risque de perte de synchronisation  
**Avec webhook** : Synchronisation garantie ✅

---

*Document généré le 29/12/2025*










