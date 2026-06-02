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
| Affichage remise promo (client + admin) | ✅ | Composant `reservation-promo-discount`, fiche `/admin/reservations/{id}` |
| Règle 24 h — acompte en ligne | ✅ | `pending` bloque le calendrier public ; expiration auto via `reservations:expire-unpaid-pending` |
| Messagerie — bloc « Réservation active » | ✅ | Correction `text-white` → `text-lux-blue` |
| Recette §3.11 dates admin | ✅ | Villa obligatoire, calendrier, conflits grisés, création manuelle Domaine du Lagon |

### Disponibilité — architecture (A + B + C)

**Source unique :** `app/Services/VillaAvailabilityService.php`  
**Contextes :** `app/Services/VillaAvailabilityContext.php`

| Contexte | Réservations comptées | Blocages calendrier |
|----------|----------------------|---------------------|
| `publicSite()` | Confirmées / payées / terminées + `pending` (24 h max), séjours futurs | ✅ Toujours |
| `admin()` | + `pending`, toutes dates | ✅ Toujours |

**Règle acompte en ligne (24 h) :** à la confirmation d’une réservation `direct`, `payment_expires_at = now() + 24 h`. Tant que le statut reste `pending`, les dates sont bloquées côté public et admin. Si l’acompte n’est pas payé à temps, la commande planifiée `reservations:expire-unpaid-pending` (horaire) annule la réservation, libère le calendrier et envoie l’email d’annulation. Les réservations manuelles admin (`source = manual`) ne sont pas concernées.

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
| `php artisan test` (global) | **64 passés** (juin 2026) |
| `VillaAvailabilityServiceTest` | 4/4 |
| `ExpireUnpaidPendingReservationsTest` | 3/3 |
| `ManualReservationPaymentSyncTest` | 5/5 |
| `ManualReservationClientPaymentTest` | 4/4 |
| `PromoCodeTest` | 3/3 |
| `PrivilegeClubWhatsappChecklistTest` | 3/3 |

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
app/Console/Commands/ExpireUnpaidPendingReservations.php
config/booking.php
app/Http/Controllers/ClientNotificationController.php
app/Services/WhatsAppClickToChatService.php
resources/views/emails/promo-code.blade.php
resources/views/components/reservation-offline-payment-notice.blade.php
resources/views/components/reservation-promo-discount.blade.php
tests/Feature/ExpireUnpaidPendingReservationsTest.php
tests/Unit/VillaAvailabilityServiceTest.php
tests/Feature/ManualReservationPaymentSyncTest.php
tests/Feature/ManualReservationClientPaymentTest.php
```

---

## Recette CDC — suivi manuel (juin 2026)

| §CDC | Sujet | Statut |
|------|--------|--------|
| §3.1 | Privilege Club (email palier, WhatsApp checklist) | ✅ Recette OK (tier simulé, checklist admin validée) |
| §3.2 | Code promo en réservation en ligne | ✅ Recette OK (résa `LX-OMI255-2026`, code `GP2040`) |
| §3.3 | Chevauchement saisons → tarif max | ✅ Recette OK (Domaine du Lagon V-002 : juin 400 €/nuit → 1 600 €, juillet max 700 €/nuit → 2 800 €) |
| §3.8 | Page `/admin/traffic` | ✅ Recette OK (visiteurs, pages vues, graphique, top pages, sources) |
| §3.11 | Création / édition réservation manuelle avec calendrier | ✅ Recette OK (11→18 juin, calendrier conflits, client sans Stripe, acompte 30 % affiché) |

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
| `v4.0.2` | *Prévu* — dispo unifiée (A+B+C), notifications client, affichage promo, correctifs recette |
