# Message Court - Configuration Webhook Stripe

---

**Sujet :** Configuration Webhook Stripe - Action requise

---

Bonjour,

Pour finaliser l'intégration des paiements Stripe, nous devons configurer le système de webhooks. Voici ce que vous devez faire :

## 📋 **INSTRUCTIONS**

1. **Connectez-vous à Stripe** : https://dashboard.stripe.com
2. **Allez dans** : Développeurs → Webhooks
3. **Cliquez sur** : "Ajouter un endpoint"
4. **Entrez l'URL** : `https://votredomaine.com/api/payments/webhook/stripe`
   *(Remplacez `votredomaine.com` par votre domaine réel)*
5. **Sélectionnez les événements** : `payment_intent.succeeded`, `payment_intent.payment_failed`, `payment_intent.canceled`
6. **Enregistrez** et **copiez le Secret** (commence par `whsec_...`)

## 📤 **À ME FOURNIR**

Merci de me transmettre :
1. **Le Secret Webhook** (commence par `whsec_...`)
2. **Le domaine utilisé**
3. **Le mode** (TEST ou PRODUCTION)

**Temps estimé : 5 minutes**

N'hésitez pas si vous avez des questions.

Cordialement,






