# Guide de Déploiement - Paiement du Solde

## 📋 Fichiers Modifiés/Créés

### Fichiers Backend (PHP)
1. `app/Http/Controllers/EspaceClientController.php` - Méthode `payBalance()` ajoutée
2. `app/Http/Controllers/BookingController.php` - Sauvegarde de `adults`, `children`, `infants`
3. `app/Models/Reservation.php` - Ajout de `adults`, `children`, `infants` dans `$fillable` et `$casts`
4. `app/Notifications/ReservationCreatedNotification.php` - Correction import `BroadcastMessage` (déjà fait)

### Fichiers Routes
5. `routes/web.php` - Nouvelle route `espace-client.pay-balance` ajoutée

### Fichiers Vues (Blade)
6. `resources/views/pages/espace-client.blade.php` - Bouton "Régler le solde" rendu fonctionnel
7. `resources/views/pages/pay-balance.blade.php` - **NOUVEAU FICHIER** - Page de paiement du solde
8. `resources/views/pages/payments.blade.php` - Bouton "Payer" ajouté pour les paiements en attente

### Fichiers Base de Données
9. `database/migrations/2026_01_01_120000_add_guests_details_to_reservations_table.php` - **NOUVEAU FICHIER** - Migration Laravel
10. `database/add_guests_details_to_reservations_table.sql` - **NOUVEAU FICHIER** - Script SQL direct

---

## 🚀 Étapes de Déploiement

### Étape 1 : Déployer les fichiers PHP et Vues

Transférez tous les fichiers modifiés/créés sur le serveur :

```bash
# Depuis votre machine locale
scp app/Http/Controllers/EspaceClientController.php user@serveur:/chemin/vers/lux-iles/app/Http/Controllers/
scp app/Http/Controllers/BookingController.php user@serveur:/chemin/vers/lux-iles/app/Http/Controllers/
scp app/Models/Reservation.php user@serveur:/chemin/vers/lux-iles/app/Models/
scp routes/web.php user@serveur:/chemin/vers/lux-iles/routes/
scp resources/views/pages/espace-client.blade.php user@serveur:/chemin/vers/lux-iles/resources/views/pages/
scp resources/views/pages/pay-balance.blade.php user@serveur:/chemin/vers/lux-iles/resources/views/pages/
scp resources/views/pages/payments.blade.php user@serveur:/chemin/vers/lux-iles/resources/views/pages/
scp app/Notifications/ReservationCreatedNotification.php user@serveur:/chemin/vers/lux-iles/app/Notifications/

# OU utilisez votre méthode habituelle (FTP, Git, etc.)
```

### Étape 2 : Mettre à jour la Base de Données

**Option A : Utiliser le script SQL direct (Recommandé)**

```bash
# Sur le serveur
cd /chemin/vers/lux-iles
mysql -u votre_utilisateur -p votre_base_de_donnees < database/add_guests_details_to_reservations_table.sql
```

**Option B : Utiliser la migration Laravel**

```bash
# Sur le serveur
cd /chemin/vers/lux-iles
php artisan migrate
```

### Étape 3 : Vider le Cache (Important !)

```bash
# Sur le serveur
cd /chemin/vers/lux-iles
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### Étape 4 : Vérifier les Permissions

Assurez-vous que les fichiers ont les bonnes permissions :

```bash
# Sur le serveur
cd /chemin/vers/lux-iles
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

---

## ✅ Checklist de Vérification

Après le déploiement, vérifiez :

- [ ] Les routes sont bien enregistrées : `php artisan route:list | grep pay-balance`
- [ ] La page de paiement du solde est accessible (connectez-vous et testez le bouton "Régler le solde")
- [ ] Les colonnes `adults`, `children`, `infants` existent dans la table `reservations`
- [ ] Les nouvelles réservations sauvegardent correctement la décomposition des voyageurs
- [ ] Le paiement du solde fonctionne avec Stripe (testez avec une carte de test)
- [ ] Le bouton "Payer" apparaît dans la page des paiements pour les paiements en attente

---

## 🔍 Vérification de la Base de Données

Pour vérifier que les colonnes ont été ajoutées :

```sql
-- Se connecter à MySQL
mysql -u votre_utilisateur -p votre_base_de_donnees

-- Vérifier la structure de la table
DESCRIBE reservations;

-- Vous devriez voir :
-- adults (tinyint unsigned)
-- children (tinyint unsigned)
-- infants (tinyint unsigned)
```

---

## 🐛 En cas de Problème

### Si la route n'est pas trouvée :
```bash
php artisan route:clear
php artisan route:cache  # Si vous utilisez le cache de routes
```

### Si les colonnes n'existent pas :
Exécutez à nouveau le script SQL ou la migration

### Si une erreur 500 apparaît :
- Vérifiez les logs : `tail -f storage/logs/laravel.log`
- Vérifiez que tous les fichiers ont été transférés
- Vérifiez les permissions des fichiers

---

## 📝 Notes Importantes

1. **Migration SQL** : Le script SQL met à jour les données existantes en considérant `number_of_guests` comme `adults` pour les anciennes réservations.

2. **Compatibilité** : Les anciennes réservations fonctionneront toujours (avec `adults = number_of_guests`, `children = 0`, `infants = 0`).

3. **Stripe** : Assurez-vous que les clés Stripe sont bien configurées dans `/admin/settings`.

4. **Tests** : Testez d'abord avec une carte de test Stripe (`4242 4242 4242 4242`) avant de passer en production.








