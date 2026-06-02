# CDC LUXÎLES v4.0 — Récapitulatif livraison & suivi

## Statut CDC v4.0 initial : ✅ COMPLET

| Étape | §CDC  | Thème                          | Tests |
|-------|-------|--------------------------------|-------|
| 1     | —     | Middleware EnsureAdmin         | ✅    |
| 2     | §3.3  | Saisons dates calendaires + max() | ✅    |
| 3     | §3.6/7| Validation min_stay + capacité | ✅    |
| 4     | §3.9  | Inscription manuelle client    | ✅    |
| 5     | §3.11 | Réservation manuelle           | ✅    |
| 6     | §3.10 | Documents dossier client       | ✅    |
| 7     | §3.2  | Codes promotionnels            | ✅    |
| 8     | §3.5  | Équipements & filtres          | ✅    |
| 9     | §3.4  | Avis voyageurs                 | ✅    |
| 10    | §3.1  | Privilege Club (+ checklist WhatsApp admin) | ✅    |
| 11    | §3.8  | Analytics trafic               | ✅    |
| 12    | —     | Tests, sécurité, README        | ✅    |

---

## Évolutions post-livraison (recette 2026-06)

Suivi des travaux après tag `v4.0.1` — hors périmètre CDC initial mais nécessaires à l’exploitation.

| Thème | Statut | Détail |
|-------|--------|--------|
| **Disponibilité villas — étape A** | ✅ | Service + API admin + Flatpickr création réservation manuelle |
| **Disponibilité villas — étape B** | ✅ | Édition admin + conflit dans `calculate-price` |
| **Disponibilité villas — étape C** | ✅ | Unification fiche villa + `/booking/create` + validation serveur |
| Paiements résa manuelle (sync annulation) | ✅ | `ManualReservationPaymentSyncTest` |
| Paiement Stripe masqué (résa manuelle) | ✅ | `ManualReservationClientPaymentTest` |
| Cloche notifications espace client | ✅ | `ClientNotificationController` + dropdown dashboard |
| Liens notifications admin (404) | ✅ | `resolveNotificationUrl()` + URLs relatives |
| Envoi code promo admin (email / WhatsApp) | ✅ | Fiche client + templates |
| Messagerie — bloc « Réservation active » | ✅ | Correction `text-white` → `text-lux-blue` |
| Recette §3.11 dates admin | ✅ | Villa obligatoire avant activation des champs date |

### Disponibilité — architecture (A + B + C)

**Source unique :** `app/Services/VillaAvailabilityService.php`  
**Contextes :** `app/Services/VillaAvailabilityContext.php`

| Contexte | Réservations comptées | Blocages calendrier |
|----------|----------------------|---------------------|
| `publicSite()` | Confirmées / payées / terminées, séjours futurs | ✅ Toujours |
| `admin()` | + `pending`, toutes dates | ✅ Toujours |

| Parcours | Fichiers | Rôle |
|--------|----------|------|
| Fiche villa | `VillaController::show`, `villa-detail.blade.php` | Calendrier + Flatpickr |
| Réservation | `BookingController::create`, `booking.blade.php` | Idem + `confirm` / `calculate-price` |
| Admin création | `reservation-create.blade.php`, `GET admin/villas/{id}/blocked-dates` | Flatpickr + API |
| Admin édition | `reservation-edit.blade.php`, `ReservationController::update` | Exclusion de la résa en cours |

**Pris en compte dans chaque parcours :**

- Réservations actives (selon contexte)
- Périodes bloquées (`villa_availability_blocks` : manuel admin, iCal)

---

## Suite de tests

| Filtre | Résultat |
|--------|----------|
| `php artisan test` (global) | 59 passés, 2 échecs* (juin 2026) |
| `VillaAvailabilityServiceTest` | 4/4 |
| `ManualReservationPaymentSyncTest` | 5/5 |
| `ManualReservationClientPaymentTest` | 4/4 |

\* Échecs connus hors disponibilité : `PrivilegeClubWhatsappChecklistTest`, `PromoCodeTest` (à corriger avant tag `v4.0.2`).

```bash
php artisan test
php artisan test --filter=VillaAvailabilityServiceTest
php artisan test --filter=ManualReservation
```

---

## Fichiers ajoutés ou centraux (évolutions récentes)

```
app/Services/VillaAvailabilityService.php
app/Services/VillaAvailabilityContext.php
app/Http/Controllers/ClientNotificationController.php
app/Services/WhatsAppClickToChatService.php
resources/views/emails/promo-code.blade.php
resources/views/components/reservation-offline-payment-notice.blade.php
tests/Unit/VillaAvailabilityServiceTest.php
tests/Feature/ManualReservationPaymentSyncTest.php
tests/Feature/ManualReservationClientPaymentTest.php
```

---

## Recette CDC — points encore à valider manuellement

| §CDC | Sujet |
|------|--------|
| §3.1 | Privilege Club bout en bout (email palier, WhatsApp checklist) |
| §3.2 | Code promo appliqué en réservation en ligne |
| §3.3 | Chevauchement saisons → tarif max |
| §3.8 | Page `/admin/traffic` |
| §3.11 | Création / édition réservation manuelle avec calendrier |

---

## Hors périmètre (avenant requis)

- Blog
- Application mobile iOS / Android
- Paiement en ligne automatisé
- Synchronisation Airbnb / Booking / Abritel
- Traduction multilingue
- Programme de parrainage

---

## Tags Git

| Tag | Description |
|-----|-------------|
| `v4.0.0` | CDC LUXÎLES v4.0 — livraison complète |
| `v4.0.1` | Conformité recette (§3.1, §3.4, §3.6/§3.7) |
| `v4.0.2` | *Prévu* — disponibilité unifiée (A+B+C), notifications client, correctifs recette |
