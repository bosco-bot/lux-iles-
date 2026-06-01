# AUDIT TECHNIQUE RGPD - PLATEFORME LUXÎLES

**Date de l'audit :** 2026-01-XX  
**Auditeur :** Expert technique  
**Objectif :** Collecte de toutes les informations nécessaires à la rédaction des mentions légales et de la politique de confidentialité

---

## 1. COLLECTE DE DONNÉES PERSONNELLES

### 1.1. Formulaire de Contact (`/contact`)
**Fichier :** `resources/views/pages/contact.blade.php`  
**Contrôleur :** `app/Http/Controllers/ContactController.php`

**Champs collectés :**
- Prénom (`first_name`) - **OBLIGATOIRE**
- Nom (`last_name`) - **OBLIGATOIRE**
- Email (`email`) - **OBLIGATOIRE**
- Téléphone (`phone`) - **OPTIONNEL**
- Sujet (`subject`) - **OBLIGATOIRE** (sélection parmi : Demande de renseignements, Réservation, Conciergerie, Partenariat, Autre)
- Message (`message`) - **OBLIGATOIRE** (max 5000 caractères)

**Destination des données :**
- Envoi par email à l'administrateur via `EmailService` (SMTP Gmail)
- Stockage temporaire dans les logs Laravel (`storage/logs/laravel.log`)
- **AUCUN stockage en base de données** (pas de table `contacts` ou `messages_contact`)

**Validation :**
- Validation Laravel avec règles : `required|string|max:100` pour noms, `required|email|max:255` pour email, `nullable|string|max:20` pour téléphone, `required|string|max:5000` pour message

---

### 1.2. Formulaire d'Inscription (`/register`)
**Fichier :** `resources/views/pages/auth/register.blade.php`  
**Contrôleur :** `app/Http/Controllers/Api/AuthController.php` (méthode `register`)

**Champs collectés :**
- Prénom (`first_name`) - **OBLIGATOIRE**
- Nom (`last_name`) - **OBLIGATOIRE**
- Email (`email`) - **OBLIGATOIRE** (unique)
- Téléphone (`phone`) - **OPTIONNEL**
- Mot de passe (`password`) - **OBLIGATOIRE** (minimum 8 caractères)
- Confirmation mot de passe (`password_confirmation`) - **OBLIGATOIRE**
- Acceptation des conditions générales (`acceptTerms`) - **OBLIGATOIRE** (checkbox)

**Destination des données :**
- Stockage en base de données dans la table `users`
- Hashage du mot de passe avec bcrypt (Laravel)
- Email de bienvenue envoyé via `EmailService`

**Table `users` (schéma) :**
- `id` (BIGINT)
- `first_name` (VARCHAR 100)
- `last_name` (VARCHAR 100)
- `email` (VARCHAR 255, UNIQUE)
- `phone` (VARCHAR 20, nullable)
- `password` (VARCHAR 255, hashé)
- `address` (TEXT, nullable)
- `is_admin` (BOOLEAN, default false)
- `is_active` (BOOLEAN, default true)
- `email_verified_at` (TIMESTAMP, nullable)
- `remember_token` (VARCHAR 100, nullable)
- `created_at`, `updated_at` (TIMESTAMP)

---

### 1.3. Formulaire de Réservation (`/booking/create` → `/booking/payment`)
**Fichier :** `resources/views/pages/booking.blade.php` et `resources/views/pages/payment.blade.php`  
**Contrôleur :** `app/Http/Controllers/BookingController.php`

**Champs collectés lors de la réservation :**
- Villa ID (`villa_id`) - **OBLIGATOIRE**
- Date d'arrivée (`check_in`) - **OBLIGATOIRE**
- Date de départ (`check_out`) - **OBLIGATOIRE**
- Nombre d'adultes (`adults`) - **OBLIGATOIRE** (min: 1)
- Nombre d'enfants (`children`) - **OPTIONNEL** (min: 0)
- Nombre de bébés (`infants`) - **OPTIONNEL** (min: 0)
- Demandes spéciales (`special_requests`) - **OPTIONNEL** (max 1000 caractères)

**Données utilisateur utilisées (depuis le compte connecté) :**
- Prénom (`guest_first_name`) - depuis `users.first_name`
- Nom (`guest_last_name`) - depuis `users.last_name`
- Email (`guest_email`) - depuis `users.email`
- Téléphone (`guest_phone`) - depuis `users.phone` (si disponible)
- Adresse (`guest_address`) - depuis `users.address` (si disponible)

**Destination des données :**
- Stockage en base de données dans la table `reservations`
- Stockage des paiements dans la table `payments`
- Envoi d'email de confirmation au client
- Notification par email aux administrateurs

**Table `reservations` (schéma) :**
- `id` (BIGINT)
- `reservation_number` (VARCHAR 50, UNIQUE)
- `villa_id` (BIGINT, FK)
- `user_id` (BIGINT, FK, nullable)
- `guest_first_name` (VARCHAR 100)
- `guest_last_name` (VARCHAR 100)
- `guest_email` (VARCHAR 255)
- `guest_phone` (VARCHAR 20, nullable)
- `guest_address` (TEXT, nullable)
- `check_in_date` (DATE)
- `check_out_date` (DATE)
- `number_of_nights` (INT)
- `number_of_guests` (TINYINT)
- `adults` (TINYINT)
- `children` (TINYINT)
- `infants` (TINYINT)
- `base_price`, `cleaning_fee`, `service_fee`, `vat_amount`, `tourist_tax`, `total_price` (DECIMAL)
- `deposit_amount`, `balance_amount`, `deposit_guarantee` (DECIMAL)
- `special_requests` (TEXT, nullable)
- `status` (ENUM: pending, confirmed, deposit_paid, fully_paid, cancelled, completed, refunded)
- `created_at`, `updated_at` (TIMESTAMP)

**Table `payments` (schéma) :**
- `id` (BIGINT)
- `reservation_id` (BIGINT, FK)
- `payment_number` (VARCHAR 50, UNIQUE)
- `type` (ENUM: deposit, balance, deposit_guarantee, refund, adjustment)
- `amount` (DECIMAL)
- `currency` (VARCHAR 3, default 'EUR')
- `status` (ENUM: pending, processing, completed, failed, refunded, cancelled)
- `payment_method` (ENUM: stripe, bank_transfer, other)
- `stripe_payment_intent_id` (VARCHAR 255, nullable)
- `stripe_charge_id` (VARCHAR 255, nullable)
- `due_date` (DATE, nullable)
- `paid_at` (TIMESTAMP, nullable)
- `metadata` (JSON, nullable)
- `created_at`, `updated_at` (TIMESTAMP)

---

### 1.4. Formulaire de Profil Utilisateur (`/espace-client/profil`)
**Fichier :** `resources/views/pages/profile.blade.php`  
**Contrôleur :** `app/Http/Controllers/Api/ProfileController.php`

**Champs modifiables :**
- Prénom (`first_name`)
- Nom (`last_name`)
- Email (`email`)
- Téléphone (`phone`)
- Adresse (`address`)

**Destination des données :**
- Mise à jour de la table `users`

---

### 1.5. Formulaire de Réinitialisation de Mot de Passe (`/forgot-password`)
**Fichier :** `resources/views/pages/auth/forgot-password.blade.php`  
**Contrôleur :** `app/Http/Controllers/Api/AuthController.php` (méthode `forgotPassword`)

**Champs collectés :**
- Email (`email`) - **OBLIGATOIRE**

**Destination des données :**
- Génération d'un token de réinitialisation
- Stockage temporaire dans la table `password_reset_tokens`
- Envoi d'email avec lien de réinitialisation

---

## 2. COOKIES ET TRACKERS

### 2.1. Cookies Techniques (Nécessaires au fonctionnement)

**Cookies de session Laravel :**
- Nom : `lux_iles_session` (ou nom configuré dans `config/session.php`)
- Type : HTTP-only, Secure (si HTTPS), SameSite=Lax
- Durée : Session (expire à la fermeture du navigateur)
- Usage : Authentification, protection CSRF, gestion de session
- Configuration : `config/session.php`
  - `http_only` : true
  - `secure` : configuré via `SESSION_SECURE_COOKIE` (env)
  - `same_site` : 'lax' (par défaut)

**Token CSRF :**
- Stocké dans une meta tag HTML : `<meta name="csrf-token" content="...">`
- Utilisé pour toutes les requêtes POST/PUT/DELETE
- Pas de cookie dédié, mais inclus dans les headers HTTP

**Cookie "Remember Me" :**
- Nom : `remember_web_...` (Laravel)
- Durée : 2 semaines (configurable)
- Usage : Maintien de la session utilisateur

### 2.2. Cookies de Tracking/Analytics

**AUCUN cookie de tracking/analytics détecté :**
- ❌ Pas de Google Analytics
- ❌ Pas de Facebook Pixel / Meta Pixel
- ❌ Pas de Hotjar
- ❌ Pas de Crisp / Intercom
- ❌ Pas d'autres outils d'analytics tiers

**Note :** Le projet est prêt pour l'ajout futur d'analytics, mais aucun n'est actuellement installé.

### 2.3. Cookies Tiers (Services Externes)

**Aucun cookie tiers détecté** pour le moment.

---

## 3. SERVICES TIERS ET INTÉGRATIONS

### 3.1. Services de Paiement

**Stripe (Paiement en ligne)**
- **Service :** Stripe Payments
- **URL API :** `https://api.stripe.com/v1/`
- **Script frontend :** `https://js.stripe.com/v3/` (chargé dans `payment.blade.php` et `pay-balance.blade.php`)
- **Données transmises :**
  - Montant du paiement
  - Numéro de réservation
  - Email du client
  - Métadonnées (reservation_id, payment_id, user_id)
- **Données reçues :**
  - PaymentIntent ID
  - Charge ID
  - Statut du paiement
- **Webhook :** `POST /api/payments/webhook/stripe` (route publique, sans CSRF)
- **Clés API :** Stockées dans la table `global_settings` (clés : `stripe_public_key`, `stripe_secret_key`)
- **Politique de confidentialité Stripe :** https://stripe.com/fr/privacy
- **Conformité :** PCI-DSS (Stripe gère la conformité)

**Fichiers concernés :**
- `app/Services/PaymentService.php`
- `app/Http/Controllers/Api/PaymentController.php`
- `resources/views/pages/payment.blade.php`
- `resources/views/pages/pay-balance.blade.php`

---

### 3.2. Services de Cartographie

**Google Maps (Embed)**
- **Service :** Google Maps Embed API
- **URL :** `https://maps.google.com/maps?q={latitude},{longitude}&hl=fr&z=15&output=embed`
- **Usage :** Affichage de la localisation des villas sur la page détail
- **Données transmises :** Coordonnées GPS (latitude, longitude) de la villa
- **Cookies :** Google Maps peut déposer des cookies (consentement requis si analytics activés)
- **Politique de confidentialité Google :** https://policies.google.com/privacy

**Fichiers concernés :**
- `resources/views/pages/villa-detail.blade.php` (ligne 277)

---

### 3.3. Services de Polices Web

**Google Fonts**
- **Service :** Google Fonts CDN
- **URL :** `https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,600;1,400&display=swap`
- **Polices utilisées :**
  - Montserrat (300, 400, 500, 600)
  - Playfair Display (400, 600, italic 400)
- **Données transmises :** Adresse IP, User-Agent, référent (par le navigateur)
- **Cookies :** Google Fonts peut déposer des cookies (consentement requis si analytics activés)
- **Politique de confidentialité Google :** https://policies.google.com/privacy
- **Note :** Pour la conformité RGPD, considérer l'hébergement local des polices

**Fichiers concernés :**
- `resources/views/layouts/app.blade.php` (ligne 10)
- `resources/views/layouts/admin.blade.php` (ligne 11)
- `resources/views/layouts/dashboard.blade.php`

---

### 3.4. Services de CDN (Bibliothèques JavaScript/CSS)

**jsDelivr CDN**
- **Service :** jsDelivr (CDN open source)
- **Bibliothèques chargées :**
  - Bootstrap 5.3.0 : `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css`
  - Bootstrap JS : `https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js`
  - Font Awesome 6.4.0 : `https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css`
  - Axios : `https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js`
  - FullCalendar 6.1.10 : `https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js`
  - Flatpickr : `https://cdn.jsdelivr.net/npm/flatpickr` (dans booking.blade.php)
  - Plotly 3.1.1 : `https://cdn.plot.ly/plotly-3.1.1.min.js` (dashboard admin)
- **Données transmises :** Adresse IP, User-Agent, référent
- **Cookies :** jsDelivr ne dépose généralement pas de cookies de tracking
- **Politique de confidentialité jsDelivr :** https://www.jsdelivr.com/terms/privacy-policy-jsdelivr-net

**Fichiers concernés :**
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/admin.blade.php`
- `resources/views/layouts/dashboard.blade.php`
- `resources/views/pages/booking.blade.php`
- `resources/views/pages/admin/dashboard.blade.php`

---

### 3.5. Services d'Email

**Gmail SMTP (via PHPMailer et Laravel Mail)**
- **Service :** Gmail SMTP
- **Serveur SMTP :** `smtp.gmail.com`
- **Port :** 587 (TLS)
- **Authentification :** Mot de passe d'application Gmail
- **Configuration :** Stockée dans la table `global_settings`
  - `email_smtp_host` : smtp.gmail.com
  - `email_smtp_port` : 587
  - `email_smtp_username` : luxiles.smtp@gmail.com
  - `email_smtp_password` : (mot de passe d'application)
  - `email_smtp_encryption` : tls
  - `email_from_address` : contact.luxiles@gmail.com
  - `email_from_name` : LUXÎLES
- **Données transmises :** Contenu des emails (noms, emails, messages, données de réservation)
- **Politique de confidentialité Google :** https://policies.google.com/privacy

**Fichiers concernés :**
- `app/Services/EmailService.php`
- `app/Providers/AppServiceProvider.php`

**Types d'emails envoyés :**
- Email de bienvenue (inscription)
- Email de confirmation de réservation
- Email de confirmation de paiement
- Email de rappel de paiement
- Email de rappel d'arrivée
- Email d'annulation
- Email de notification de contact (admin)
- Email de notification de nouvelle réservation (admin)
- Email de notification de paiement reçu (admin)
- Email de réinitialisation de mot de passe

---

### 3.6. Services de Temps Réel (WebSockets)

**Soketi / Laravel Echo (Notifications en temps réel)**
- **Service :** Soketi (alternative open source à Pusher)
- **Usage :** Notifications en temps réel dans le dashboard admin
- **Configuration :** Via variables d'environnement (`VITE_PUSHER_*`)
- **Données transmises :** Notifications (nouvelles réservations, paiements, messages)
- **Cookies :** Aucun cookie dédié
- **Note :** Service interne (hébergé sur le même serveur)

**Fichiers concernés :**
- `resources/js/bootstrap.js`
- `package.json` (dépendance `@soketi/soketi`)

---

## 4. HÉBERGEMENT ET INFRASTRUCTURE

### 4.1. Hébergeur Web

**Hostinger (mentionné dans les discussions)**
- **Statut :** Hébergement non encore payé (selon les discussions)
- **Type :** Hébergement partagé (probablement)
- **Localisation :** Non spécifiée (à vérifier)
- **Données hébergées :**
  - Base de données MySQL
  - Fichiers de l'application Laravel
  - Fichiers uploadés (photos de villas) dans `storage/app/public`
  - Logs dans `storage/logs`

**Fichiers de configuration :**
- `public/.htaccess` (configuration Apache)
- Pas de `Dockerfile` à la racine (présence dans `vendor/laravel/sail` uniquement)
- Pas de `vercel.json` (pas de déploiement Vercel)

---

### 4.2. Base de Données

**MySQL 8.0+ (compatible PostgreSQL 12+)**
- **Localisation :** Même serveur que l'application (hébergement partagé)
- **Tables principales contenant des données personnelles :**
  - `users` : Données utilisateurs (noms, emails, téléphones, adresses, mots de passe hashés)
  - `reservations` : Données de réservation (noms, emails, téléphones, adresses, dates, montants)
  - `payments` : Données de paiement (liens vers réservations, montants, statuts)
  - `messages` : Messages entre clients et administrateurs
  - `documents` : Documents générés (contrats, factures, reçus)
  - `favorites` : Villas favorites des utilisateurs
  - `sessions` : Sessions utilisateurs (Laravel)

**Sauvegarde :** Non documentée dans le code (à vérifier avec l'hébergeur)

---

## 5. SÉCURITÉ ET PROTECTION DES DONNÉES

### 5.1. Protection CSRF (Cross-Site Request Forgery)

**Implémentation :**
- Token CSRF Laravel sur toutes les requêtes POST/PUT/DELETE
- Meta tag dans le HTML : `<meta name="csrf-token" content="{{ csrf_token() }}">`
- Header HTTP : `X-CSRF-TOKEN` dans toutes les requêtes AJAX
- Middleware Laravel : `VerifyCsrfToken` (actif par défaut)
- Exception : Route webhook Stripe (`/api/payments/webhook/stripe`) exemptée de CSRF

**Fichiers concernés :**
- `resources/views/layouts/app.blade.php` (ligne 6)
- `public/js/auth.js` (lignes 11-14)
- `routes/api.php` (ligne 44)

---

### 5.2. Protection XSS (Cross-Site Scripting)

**Implémentation :**
- Échappement automatique dans les templates Blade : `{{ $variable }}`
- Échappement HTML avec `{!! $variable !!}` uniquement pour le contenu de confiance
- Validation et sanitisation des entrées utilisateur via Laravel Validation
- Utilisation de `strip_tags()` dans `EmailService` pour les emails texte

**Fichiers concernés :**
- Tous les fichiers Blade utilisent `{{ }}` pour l'échappement
- `app/Services/EmailService.php` (ligne 106)

---

### 5.3. Protection SQL Injection

**Implémentation :**
- Utilisation de l'ORM Eloquent (requêtes préparées automatiques)
- Validation des entrées avant insertion en base
- Pas de requêtes SQL brutes avec concaténation de variables utilisateur

---

### 5.4. Hashage des Mots de Passe

**Implémentation :**
- Hashage bcrypt via Laravel (`Hash::make()`)
- Mots de passe jamais stockés en clair
- Vérification via `Hash::check()`

---

### 5.5. En-têtes de Sécurité HTTP

**Configuration :**
- Pas d'en-têtes de sécurité explicites dans le code (à configurer au niveau serveur)
- Recommandations :
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: DENY` ou `SAMEORIGIN`
  - `X-XSS-Protection: 1; mode=block`
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains` (si HTTPS)
  - `Content-Security-Policy` (à configurer selon les besoins)

**Fichiers concernés :**
- `public/.htaccess` (peut être configuré ici pour Apache)

---

### 5.6. Validation et Sanitisation des Données

**Implémentation :**
- Validation Laravel sur tous les formulaires
- Règles de validation strictes (types, longueurs, formats)
- Sanitisation via `strip_tags()`, `trim()`, `htmlspecialchars()` (automatique dans Blade)

**Exemples de validation :**
- Email : `required|email|max:255`
- Téléphone : `nullable|string|max:20`
- Noms : `required|string|max:100`
- Messages : `required|string|max:5000`

---

## 6. DURÉE DE CONSERVATION DES DONNÉES

**Non documentée dans le code** - À définir dans la politique de confidentialité :
- Données utilisateurs : ?
- Données de réservation : ?
- Données de paiement : ?
- Logs : ?
- Emails : ?

---

## 7. DROITS DES UTILISATEURS (RGPD)

**Fonctionnalités à vérifier :**
- ✅ Accès aux données personnelles (via profil utilisateur)
- ❓ Modification des données (via profil utilisateur)
- ❓ Suppression de compte (non documentée dans le code)
- ❓ Export des données (non documentée dans le code)
- ❓ Droit à l'oubli (non documentée dans le code)
- ❓ Portabilité des données (non documentée dans le code)

**Recommandation :** Implémenter les fonctionnalités manquantes pour la conformité RGPD.

---

## 8. TRANSFERT DE DONNÉES HORS UE

**Services tiers identifiés :**
- **Stripe :** États-Unis (transfert hors UE) - Clauses contractuelles types (SCC)
- **Google (Maps, Fonts) :** États-Unis (transfert hors UE) - Clauses contractuelles types (SCC)
- **jsDelivr :** États-Unis (transfert hors UE) - Vérifier les garanties

**Recommandation :** Mentionner ces transferts dans la politique de confidentialité et s'assurer que les services tiers sont conformes (clauses contractuelles types, Privacy Shield, etc.).

---

## 9. RÉSUMÉ POUR LE JURISTE

### Données collectées :
1. **Contact :** Prénom, nom, email, téléphone (optionnel), sujet, message
2. **Inscription :** Prénom, nom, email, téléphone (optionnel), mot de passe (hashé)
3. **Réservation :** Données utilisateur + dates, nombre de voyageurs, demandes spéciales
4. **Profil :** Prénom, nom, email, téléphone, adresse

### Cookies :
- Cookies techniques (session, CSRF, remember me) - **Nécessaires, pas de consentement requis**
- **Aucun cookie de tracking/analytics** - Pas de consentement requis actuellement

### Services tiers :
- **Stripe** (paiement) - Transfert hors UE
- **Google Maps** (cartographie) - Transfert hors UE
- **Google Fonts** (polices) - Transfert hors UE
- **jsDelivr** (CDN) - Transfert hors UE
- **Gmail SMTP** (emails) - Transfert hors UE

### Sécurité :
- Protection CSRF ✅
- Protection XSS ✅
- Hashage des mots de passe ✅
- Validation des données ✅
- En-têtes de sécurité : À configurer au niveau serveur

### Conformité RGPD :
- Droits des utilisateurs : Partiellement implémentés (accès/modification ✅, suppression/export ❌)
- Durée de conservation : Non documentée
- Transferts hors UE : Présents (Stripe, Google, jsDelivr)

---

## 10. RECOMMANDATIONS TECHNIQUES

1. **Implémenter les fonctionnalités RGPD manquantes :**
   - Suppression de compte
   - Export des données (format JSON/CSV)
   - Droit à l'oubli (anonymisation/suppression)

2. **Configurer les en-têtes de sécurité HTTP** (au niveau serveur ou `.htaccess`)

3. **Documenter la durée de conservation des données** dans la politique de confidentialité

4. **Considérer l'hébergement local des polices Google Fonts** pour éviter le transfert de données vers Google

5. **Ajouter un bandeau de consentement aux cookies** si des analytics sont ajoutés plus tard

6. **Mettre en place un système de sauvegarde régulier** de la base de données

7. **Configurer HTTPS** (si ce n'est pas déjà fait) pour sécuriser les transferts de données

---

**Fin du rapport d'audit technique RGPD**



