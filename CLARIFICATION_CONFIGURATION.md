# 🔍 Clarification : Ce qui est déjà fait vs Ce qui reste à faire

## ✅ CE QUI EST DÉJÀ FAIT (Localement)

### 1. ✅ Informations Légales
**Statut** : **DÉJÀ CONFIGURÉES**

Les informations légales sont **déjà dans la base de données** avec les valeurs que vous avez fournies :
- ✅ Nom : BLUE SECRET
- ✅ Adresse : 4 LOT DOMAINE DU GRAND BLEU, PALAIS STE MARGUERITE, 97160 LE MOULE
- ✅ Téléphone : +33 7 66 33 41 98
- ✅ Email : contact.luxiles@gmail.com
- ✅ SIRET : 85262415400013
- ✅ TVA : FR31852624154

**Action** : Aucune action nécessaire ! Ces valeurs sont déjà enregistrées.

---

### 2. ✅ Assets Compilés
**Statut** : **DÉJÀ COMPILÉS**

Le fichier `public/build/manifest.json` existe, ce qui signifie que les assets ont déjà été compilés avec `npm run build`.

**Action** : Aucune action nécessaire en local ! Il faudra juste recompiler sur le serveur de production pour s'assurer que c'est à jour.

---

## ⚠️ CE QUI DOIT ÊTRE CONFIGURÉ (En production)

### 1. 🔑 Clés Stripe
**Statut** : **VÉRIFIER EN PRODUCTION** (déjà configuré en local, mais utiliser les clés de production)

**Important** : Les clés Stripe ne sont PAS dans `.env`, elles sont stockées dans la table `global_settings` via `SettingsHelper`.

**Où configurer** :
1. Aller dans `/admin/settings`
2. Chercher la section "Stripe" (si elle existe) ou utiliser la commande artisan :
   ```bash
   php artisan setup:stripe
   ```
   
   Ou directement dans la base de données :
   ```sql
   INSERT INTO global_settings (key, value, type, description, category) 
   VALUES 
   ('stripe_public_key', 'pk_live_...', 'string', 'Clé publique Stripe', 'paiement'),
   ('stripe_secret_key', 'sk_live_...', 'string', 'Clé secrète Stripe', 'paiement'),
   ('stripe_webhook_secret', 'whsec_...', 'string', 'Secret webhook Stripe', 'paiement')
   ON DUPLICATE KEY UPDATE value = VALUES(value);
   ```

**⚠️ IMPORTANT** :
- En local, vous pouvez utiliser les clés de test Stripe
- En production, vous DEVEZ utiliser les clés de production Stripe
- Vous devez aussi configurer l'URL du webhook dans le dashboard Stripe : `https://votre-domaine.com/api/payments/webhook/stripe`

---

### 2. 📧 Configuration SMTP
**Statut** : **À CONFIGURER EN PRODUCTION**

**Important** : La configuration SMTP utilise aussi `SettingsHelper`, pas `.env`.

**Où configurer** :
1. **Via l'interface admin** : `/admin/settings` → Section Email (si elle existe)
2. **Via commande artisan** :
   ```bash
   php artisan setup:email
   ```
   
   Ou directement dans la base de données :
   ```sql
   INSERT INTO global_settings (key, value, type, description, category) 
   VALUES 
   ('email_smtp_host', 'smtp.gmail.com', 'string', 'Serveur SMTP', 'email'),
   ('email_smtp_port', '587', 'integer', 'Port SMTP', 'email'),
   ('email_smtp_username', 'votre-email@gmail.com', 'string', 'Nom d\'utilisateur SMTP', 'email'),
   ('email_smtp_password', 'votre-mot-de-passe-app', 'string', 'Mot de passe SMTP', 'email'),
   ('email_smtp_encryption', 'tls', 'string', 'Chiffrement SMTP', 'email'),
   ('email_from_address', 'contact.luxiles@gmail.com', 'string', 'Email expéditeur', 'email'),
   ('email_from_name', 'LUXÎLES', 'string', 'Nom expéditeur', 'email')
   ON DUPLICATE KEY UPDATE value = VALUES(value);
   ```

**⚠️ IMPORTANT** :
- Pour Gmail, vous devez utiliser un "Mot de passe d'application" (pas votre mot de passe normal)
- Les valeurs par défaut dans le code sont des exemples, vous devez les remplacer

---

### 3. 📁 Permissions des Fichiers
**Statut** : **À FAIRE SUR LE SERVEUR DE PRODUCTION**

**Pourquoi** : En local, vous avez probablement tous les droits. Sur le serveur de production, le serveur web (Apache/Nginx) doit pouvoir écrire dans `storage/` et `bootstrap/cache/`.

**Action sur le serveur** :
```bash
cd /chemin/vers/votre/projet
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache  # Adaptez selon votre serveur
php artisan storage:link
```

**⚠️ IMPORTANT** : 
- Ceci doit être fait SUR LE SERVEUR DE PRODUCTION, pas en local
- L'utilisateur `www-data` peut varier selon votre serveur (peut être `apache`, `nginx`, etc.)

---

### 4. 🏗️ Assets (npm run build)
**Statut** : **À RECOMPILER EN PRODUCTION**

**Pourquoi** : Même si les assets sont déjà compilés en local, vous devez les recompiler sur le serveur de production pour vous assurer qu'ils sont à jour et optimisés.

**Action sur le serveur** :
```bash
cd /chemin/vers/votre/projet
npm install --production
npm run build
```

**⚠️ IMPORTANT** :
- Vous pouvez aussi compiler en local et transférer le dossier `public/build/`
- Mais il est recommandé de compiler directement sur le serveur

---

## 📋 RÉCAPITULATIF

| Élément | Local (Développement) | Production (À faire) |
|---------|----------------------|---------------------|
| **Informations légales** | ✅ Déjà configurées | ✅ Déjà configurées (transférées avec la BDD) |
| **Assets compilés** | ✅ Déjà compilés | ⚠️ Recompiler (`npm run build`) |
| **Clés Stripe** | ✅ Déjà configurées (test) | ⚠️ Vérifier/remplacer par clés production via SettingsHelper |
| **Configuration SMTP** | ⚠️ À configurer | ⚠️ À configurer via SettingsHelper |
| **Permissions fichiers** | ✅ OK (droits utilisateur) | ⚠️ À configurer (`chmod 775 storage`) |

---

## 🎯 EN RÉSUMÉ

**Ce qui est déjà fait et ne nécessite AUCUNE action** :
- ✅ Informations légales (déjà dans la BDD)
- ✅ Code source complet et fonctionnel
- ✅ Structure de base de données

**Ce qui doit être fait EN PRODUCTION uniquement** :
- ⚠️ Configurer les clés Stripe (via `/admin/settings` ou commande artisan)
- ⚠️ Configurer SMTP (via `/admin/settings` ou commande artisan)
- ⚠️ Configurer les permissions (`chmod 775 storage`)
- ⚠️ Recompiler les assets (`npm run build`)

**Note** : Stripe et SMTP utilisent `SettingsHelper` qui lit depuis la table `global_settings` de la base de données, pas depuis `.env`. C'est pourquoi il faut les configurer après le déploiement, soit via l'interface admin, soit via SQL, soit via une commande artisan si elle existe.

