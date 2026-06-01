# 📋 Traitement des Réservations "En Attente" (Pending)

**Date** : 29/12/2025

---

## 🎯 Vue d'ensemble

Ce document explique comment les réservations avec le statut **"pending"** (en attente) sont traitées dans le système LUXÎLES.

---

## 📊 Statuts des Réservations

Les réservations peuvent avoir les statuts suivants :
- **`pending`** : En attente de paiement (statut initial)
- **`confirmed`** : Confirmée (manuellement ou automatiquement)
- **`deposit_paid`** : Acompte payé
- **`fully_paid`** : Complètement payée (acompte + solde)
- **`cancelled`** : Annulée
- **`completed`** : Séjour terminé
- **`refunded`** : Remboursée

---

## 🔄 FLUX DE TRAITEMENT D'UNE RÉSERVATION

### ÉTAPE 1 : Création de la Réservation (Statut : `pending`)

**Lieu** : `BookingController@confirm`

Lorsqu'un client confirme sa réservation :

1. **Création de la réservation** avec statut `pending` :
```php
$reservation = Reservation::create([
    'reservation_number' => $reservationNumber,
    'villa_id' => $villa->id,
    'user_id' => $user->id,
    'status' => 'pending', // ⚠️ Statut initial
    // ... autres champs
]);
```

2. **Création des paiements** avec statut `pending` :
   - **Acompte (deposit)** : 30-50% du total (selon configuration)
   - **Solde (balance)** : Reste du montant (à payer avant l'arrivée)
   - **Garantie (deposit_guarantee)** : Si configuré sur la villa

```php
$depositPayment = Payment::create([
    'reservation_id' => $reservation->id,
    'type' => 'deposit',
    'amount' => $depositAmount,
    'status' => 'pending', // ⚠️ En attente
    'payment_method' => 'stripe',
]);

Payment::create([
    'reservation_id' => $reservation->id,
    'type' => 'balance',
    'amount' => $balanceAmount,
    'status' => 'pending', // ⚠️ En attente
    'due_date' => $balanceDueDate->toDateString(),
]);
```

3. **Création du PaymentIntent Stripe** pour l'acompte :
```php
$paymentIntent = $paymentService->createPaymentIntent($depositPayment);
$clientSecret = $paymentIntent->client_secret;
```

4. **Actions automatiques** :
   - ✅ Email de confirmation envoyé (en arrière-plan)
   - ✅ Notification admin créée
   - ✅ Numéro de réservation généré

**RÉSULTAT** : Réservation créée avec statut `pending` et paiement de l'acompte prêt à être effectué.

---

### ÉTAPE 2 : Paiement de l'Acompte

**Lieu** : `Api\PaymentController@confirm`

Le client effectue le paiement via Stripe :

1. **Confirmation du paiement** :
```php
$paymentIntent = $this->paymentService->confirmPayment($payment, $request->payment_intent_id);
```

2. **Si le paiement réussit** (`status === 'succeeded'`) :
   - Le paiement est mis à jour : `status = 'completed'`
   - La réservation passe à : `status = 'deposit_paid'`

**Code dans PaymentService** :
```php
if ($payment->type === 'deposit') {
    // Si c'est l'acompte, passer la réservation à "deposit_paid"
    if ($reservation->status === 'confirmed' || $reservation->status === 'pending') {
        $reservation->update(['status' => 'deposit_paid']);
    }
}
```

3. **Actions automatiques après paiement** :
   - ✅ Email de confirmation de paiement envoyé
   - ✅ Notification admin créée
   - ✅ Documents PDF générés (via webhook ou manuellement)
   - ✅ Reçu d'arrhes généré

**RÉSULTAT** : Réservation avec statut `deposit_paid`, acompte payé.

---

### ÉTAPE 3 : Paiement du Solde

**Lieu** : `Api\PaymentController@confirm` (même processus)

Le client paie le solde (généralement 30 jours avant l'arrivée) :

1. **Paiement du solde confirmé** :
```php
if ($payment->type === 'balance') {
    // Vérifier si tous les paiements sont complétés
    $allPaymentsCompleted = $reservation->payments()
        ->whereIn('type', ['deposit', 'balance'])
        ->where('status', 'completed')
        ->count() >= 2;
    
    if ($allPaymentsCompleted) {
        $reservation->update(['status' => 'fully_paid']);
    }
}
```

2. **Actions automatiques** :
   - ✅ Email de confirmation envoyé
   - ✅ Notification admin créée
   - ✅ Reçu de solde généré

**RÉSULTAT** : Réservation avec statut `fully_paid`, complètement payée.

---

## 🔍 TRAITEMENT AUTOMATIQUE (Webhooks Stripe)

**Lieu** : `Api\PaymentController@handleWebhook`

Stripe envoie automatiquement des webhooks lors des événements de paiement :

1. **Événement `payment_intent.succeeded`** :
   - Le paiement est marqué comme `completed`
   - La réservation est mise à jour automatiquement
   - Les documents sont générés

2. **Événement `payment_intent.payment_failed`** :
   - Le paiement est marqué comme `failed`
   - La réservation reste en `pending` ou `deposit_paid`

---

## 📋 GESTION MANUELLE (Admin)

### Changement de Statut Manuel

**Lieu** : `Admin\ReservationController@update`

Les administrateurs peuvent changer manuellement le statut d'une réservation :

```php
$reservation->update([
    'status' => $request->status, // 'pending', 'confirmed', 'deposit_paid', etc.
]);
```

**Cas d'usage** :
- Marquer une réservation comme `confirmed` manuellement
- Annuler une réservation (`cancelled`)
- Marquer comme terminée (`completed`)

---

## ⚠️ PROBLÈME IDENTIFIÉ

Dans le code actuel, il y a une petite incohérence dans `PaymentService.php` :

```php
if ($payment->type === 'deposit') {
    // Si c'est l'acompte, passer la réservation à "deposit_paid"
    if ($reservation->status === 'confirmed') { // ⚠️ Devrait aussi accepter 'pending'
        $reservation->update(['status' => 'deposit_paid']);
    }
}
```

**Problème** : Lors de la création, le statut est `pending`, mais le code vérifie seulement `confirmed`.

**Solution recommandée** :
```php
if ($reservation->status === 'confirmed' || $reservation->status === 'pending') {
    $reservation->update(['status' => 'deposit_paid']);
}
```

Ou mieux encore, simplement :
```php
if (in_array($reservation->status, ['pending', 'confirmed'])) {
    $reservation->update(['status' => 'deposit_paid']);
}
```

---

## 📊 RÉSUMÉ DU FLUX

```
1. Client confirme réservation
   ↓
   Réservation créée : status = 'pending'
   Paiements créés : status = 'pending'
   PaymentIntent Stripe créé
   ↓
   
2. Client paie l'acompte
   ↓
   Paiement acompte : status = 'completed'
   Réservation : status = 'deposit_paid'
   Documents générés (contrat, facture, reçu arrhes)
   ↓
   
3. Client paie le solde (30 jours avant)
   ↓
   Paiement solde : status = 'completed'
   Réservation : status = 'fully_paid'
   Reçu solde généré
   ↓
   
4. Séjour terminé
   ↓
   Réservation : status = 'completed'
```

---

## 🔧 ACTIONS POUR LES RÉSERVATIONS "PENDING"

### Pour le Client

1. **Accéder à la page de paiement** :
   - URL : `/booking/payment?villa_id=...&check_in=...&check_out=...`
   - Le client peut voir les détails et payer

2. **Dans l'espace client** :
   - Voir la réservation dans "Mes Réservations"
   - Voir le statut "En attente"
   - Accéder au lien de paiement si disponible

### Pour l'Administrateur

1. **Voir les réservations en attente** :
   - URL : `/admin/reservations`
   - Filtrer par statut "pending"

2. **Actions possibles** :
   - **Confirmer manuellement** : Changer le statut à `confirmed`
   - **Voir les détails** : Accéder à la page de détails
   - **Contacter le client** : Via la messagerie
   - **Annuler** : Si nécessaire

3. **Notifications** :
   - Une notification est créée pour chaque nouvelle réservation
   - Visible dans le dropdown notifications (admin)

---

## 📝 NOTES IMPORTANTES

1. **Les réservations "pending" sont valides** :
   - Elles ont un numéro de réservation unique
   - Les dates sont réservées (bloquées)
   - Les documents peuvent être générés

2. **Expiration** :
   - Le système ne gère pas actuellement d'expiration automatique
   - Une réservation "pending" reste en attente indéfiniment
   - Un admin peut l'annuler manuellement

3. **Documents** :
   - Les documents PDF sont générés après le paiement (via webhook)
   - Ou peuvent être générés manuellement par un admin

4. **Emails** :
   - Un email est envoyé à la création (même si "pending")
   - Un email est envoyé après chaque paiement

---

*Document généré le 29/12/2025*










