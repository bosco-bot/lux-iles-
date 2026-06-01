# LUXÎLES — Plateforme de location de villas de luxe

Application Laravel 12 pour [luxiles.fr](https://luxiles.fr) : catalogue de villas aux Caraïbes, réservation en ligne, espace client et back-office LUXÎLES.

## Prérequis

- PHP 8.2+
- Composer
- Node.js 18+ et npm
- MySQL 8+ (ou MariaDB)
- Extension PHP : `pdo_mysql`, `mbstring`, `xml`, `curl`, `gd` ou `imagick`

## Installation (développement)

```bash
composer install
cp .env.example .env
php artisan key:generate
# Configurer DB_* dans .env puis :
php artisan migrate
php artisan storage:link
npm install && npm run build
php artisan serve
```

## Comptes et accès

| Zone | URL | Protection |
|------|-----|------------|
| Site public | `/` | — |
| Espace client | `/espace-client` | `auth` |
| Administration | `/admin` | `auth` + middleware `admin` (`is_admin`) |

## Fonctionnalités CDC v4 (implémentées)

| Réf. | Module |
|------|--------|
| §3.1 | LUXÎLES Privilege Club (paliers, maintenance annuelle, notifications) |
| §3.2 | Codes promotionnels (saisie manuelle, validation serveur) |
| §3.3 | Saisons et tarifs (chevauchement = tarif max) |
| §3.4 | Avis voyageurs (modération, réponse publique, délai 30 jours) |
| §3.5 | Équipements et filtres de recherche |
| §3.6 | Durée minimale de séjour par villa |
| §3.7 | Capacité maximale par villa |
| §3.8 | Statistiques de trafic (`/admin/traffic`) |
| §3.9 | Création client admin + invitation mot de passe |
| §3.10 | Documents client (PDF/Word, 15 Mo) |
| §3.11 | Réservation manuelle admin |

## Commandes utiles

```bash
# Tests automatisés (SQLite en mémoire)
php artisan test

# File d'attente / rappels (cron)
php artisan schedule:work

# Privilege Club
php artisan privilege-club:sync-after-stays
php artisan privilege-club:annual-maintenance

# Maintenance (à exécuter en CLI, pas via URL web)
php artisan optimize:clear
php artisan storage:link
php artisan migrate
```

> **Sécurité :** les anciennes routes web `/clear-cache`, `/link-storage` et `/update-seasons-db` ont été supprimées. Utilisez les commandes Artisan ci-dessus en SSH.

## Tests

La suite couvre notamment :

- Validation réservation (`min_stay`, capacité)
- Tarifs saisonniers chevauchés
- Codes promo
- Filtres équipements
- Avis voyageurs
- Privilege Club
- Statistiques de trafic
- Contrôles d'accès admin / client

```bash
php artisan test
```

## Planification (production)

Configurer un cron :

```cron
* * * * * cd /chemin/vers/lux-iles && php artisan schedule:run >> /dev/null 2>&1
```

Tâches planifiées : synchronisation iCal, rappels email, Privilege Club, etc. (voir `routes/console.php`).

## Documentation projet

- Cahier des charges : `docs/LUXÎLES luxiles fr cahier de charge.txt`
- Déploiement : `GUIDE_DEPLOIEMENT_PRODUCTION.md`, `DEPLOIEMENT_ACTUEL.md`

## Licence

Projet propriétaire LUXÎLES. Code applicatif sous licence MIT pour les dépendances Laravel tierces.
