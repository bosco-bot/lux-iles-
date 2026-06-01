# Message à Envoyer au Client - Configuration Webhook Stripe

---

**Sujet :** Configuration Webhook Stripe - Action requise

---

Bonjour,

Pour finaliser l'intégration des paiements Stripe sur votre site LUXÎLES, nous devons configurer le système de webhooks. Cette étape permet à Stripe de notifier automatiquement notre application lorsqu'un paiement est réussi, échoué ou annulé.

## 📋 **CE QUE VOUS DEVEZ FAIRE**

### Étape 1 : Accéder à votre Tableau de Bord Stripe

1. Connectez-vous à votre compte Stripe : https://dashboard.stripe.com
2. Assurez-vous d'être en mode **TEST** ou **PRODUCTION** selon votre besoin
3. Allez dans **Développeurs** → **Webhooks** (menu de gauche)

### Étape 2 : Créer un Nouvel Endpoint Webhook

1. Cliquez sur le bouton **"Ajouter un endpoint"** (ou **"Add endpoint"**)

2. Dans le champ **"URL de l'endpoint"**, entrez l'URL suivante :

   ```
   https://votredomaine.com/api/payments/webhook/stripe
   ```
   
   **⚠️ IMPORTANT :** Remplacez `votredomaine.com` par le domaine réel de votre site (exemple : `lux-iles.embmission.com` ou `www.luxiles.fr`)
   
   **Note :** Si votre site n'est pas encore en ligne, nous utiliserons d'abord une URL de test que je vous fournirai.

### Étape 3 : Sélectionner les Événements

Dans la section **"Événements à écouter"**, sélectionnez au minimum les événements suivants :

- ✅ `payment_intent.succeeded` (Paiement réussi)
- ✅ `payment_intent.payment_failed` (Échec du paiement)
- ✅ `payment_intent.canceled` (Paiement annulé)
- ✅ `charge.succeeded` (Charge réussie)
- ✅ `charge.failed` (Échec de la charge)

*(Vous pouvez également sélectionner "Recevoir tous les événements" pour une configuration complète)*

### Étape 4 : Enregistrer et Copier le Secret

1. Cliquez sur **"Ajouter l'endpoint"** (ou **"Add endpoint"**)
2. Une fois l'endpoint créé, vous verrez une page de détails
3. Dans la section **"Signature secret"** (ou **"Signing secret"**), vous verrez un secret qui commence par `whsec_...`
4. **COPIEZ ce secret** (cliquez sur l'icône de copie ou sélectionnez-le manuellement)

---

## 📤 **CE QUE VOUS DEVEZ ME FOURNIR EN RETOUR**

Une fois la configuration effectuée, merci de me transmettre les informations suivantes :

1. **Le Secret Webhook** :
   - C'est la chaîne de caractères qui commence par `whsec_...`
   - Exemple : `whsec_1234567890abcdefghijklmnopqrstuvwxyz`
   - ⚠️ **IMPORTANT :** Ne partagez ce secret que par un canal sécurisé (email chiffré ou message privé)

2. **Le domaine utilisé** (pour vérification) :
   - Exemple : `lux-iles.embmission.com` ou `www.luxiles.fr`

3. **Le mode Stripe** (TEST ou PRODUCTION) :
   - Indiquez si vous avez configuré le webhook en mode TEST ou PRODUCTION

---

## ⏱️ **QUAND EFFECTUER CETTE CONFIGURATION**

- **Mode TEST :** Vous pouvez le faire maintenant pour tester les paiements
- **Mode PRODUCTION :** À faire juste avant la mise en ligne du site (une fois le domaine actif)

---

## ❓ **QUESTIONS FRÉQUENTES**

**Q : Dois-je créer un webhook pour TEST et un autre pour PRODUCTION ?**  
R : Oui, idéalement. Créez un endpoint en mode TEST pour les tests, et un autre en mode PRODUCTION pour la mise en ligne.

**Q : Que faire si mon site n'est pas encore en ligne ?**  
R : Nous pouvons d'abord configurer le webhook en mode TEST avec une URL de test. Une fois le site en ligne, nous configurerons le webhook PRODUCTION.

**Q : Combien de temps cela prend-il ?**  
R : Environ 5 minutes. C'est une configuration simple dans le tableau de bord Stripe.

---

## 🔒 **SÉCURITÉ**

Le secret webhook est une information sensible. Veuillez :
- Ne pas le partager publiquement
- Ne pas le commiter dans un dépôt public
- Me le transmettre par un canal sécurisé

---

Merci de procéder à cette configuration et de me transmettre le secret webhook une fois terminé.

N'hésitez pas si vous avez des questions ou besoin d'assistance.

Cordialement,

---

**P.S.** : Si vous préférez, je peux effectuer cette configuration pour vous si vous me donnez un accès temporaire à votre compte Stripe (avec des droits limités aux webhooks uniquement).






