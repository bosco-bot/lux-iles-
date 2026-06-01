# Liste complète des tables de la base de données LUXÎLES

## Tables présentes en local (34 tables)

### Tables système Laravel
1. `cache` - Cache de l'application
2. `cache_locks` - Verrous du cache
3. `failed_jobs` - Jobs en échec
4. `job_batches` - Batchs de jobs (⚠️ **PROBABLEMENT MANQUANTE SUR LE SERVEUR**)
5. `jobs` - Jobs en file d'attente
6. `migrations` - Historique des migrations
7. `password_reset_tokens` - Tokens de réinitialisation de mot de passe
8. `sessions` - Sessions utilisateurs

### Tables métier principales
9. `cancellation_policies` - Politiques d'annulation
10. `document_attachments` - Pièces jointes des documents
11. `documents` - Documents (contrats, factures, reçus)
12. `email_templates` - Modèles d'emails
13. `equipments` - Équipements des villas
14. `favorites` - Villas favorites des utilisateurs
15. `global_settings` - Paramètres globaux (⚠️ **À CRÉER SI MANQUANTE**)
16. `islands` - Îles (Martinique, Guadeloupe, etc.)
17. `message_attachments` - Pièces jointes des messages
18. `messages` - Messagerie client/admin
19. `notifications` - Notifications système
20. `payments` - Paiements
21. `platform_syncs` - Synchronisations avec plateformes externes
22. `reservation_guests` - Invités des réservations
23. `reservations` - Réservations
24. `roles` - Rôles utilisateurs
25. `seasons` - Saisons tarifaires
26. `settings_history` - Historique des modifications de paramètres
27. `user_roles` - Relation utilisateurs/rôles
28. `users` - Utilisateurs
29. `villa_availability_blocks` - Blocages de disponibilité
30. `villa_equipments` - Relation villas/équipements
31. `villa_ical_configs` - Configuration iCal pour les villas
32. `villa_photos` - Photos des villas
33. `villa_seasonal_prices` - Prix saisonniers des villas
34. `villas` - Villas

## Tables à vérifier/créer sur le serveur

### 1. `job_batches` (probablement manquante)
**Fichier SQL :** `database/create_job_batches_table.sql`

Cette table est créée par la migration Laravel `0001_01_01_000002_create_jobs_table.php` et est nécessaire pour le système de batchs de jobs.

### 2. `global_settings` (à créer si nécessaire)
**Fichier SQL :** `database/create_global_settings_table.sql`

Cette table stocke les paramètres globaux de l'application (informations légales, SMTP, Stripe, etc.).

## Instructions pour vérifier

Pour identifier quelle table manque exactement, exécutez cette requête sur le serveur :

```sql
SHOW TABLES;
```

Comparez la liste avec les 34 tables listées ci-dessus.

## Script de vérification

Vous pouvez aussi exécuter cette commande sur le serveur pour voir toutes les tables :

```bash
mysql -u votre_utilisateur -p votre_base_de_donnees -e "SHOW TABLES;" | sort
```








