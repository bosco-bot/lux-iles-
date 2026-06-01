# 🔄 MISE À JOUR - LIENS SERVICES FOOTER + ROUTES DESTINATIONS

## ⚠️ IMPORTANT : Correction de l'erreur RouteNotFoundException

**Erreur rencontrée :** `Route [destination.saint-barthelemy] not defined`

**Cause :** Le fichier `routes/web.php` sur le serveur ne contient pas les routes des destinations.

**Solution :** Uploader le fichier `routes/web.php` mis à jour qui contient :
- Les routes des destinations (martinique, guadeloupe, saint-barthelemy, etc.)
- Les routes des services (conciergerie, chef-domicile, etc.)

---

## 📋 Fichiers à mettre à jour sur le serveur

### A. Fichiers modifiés à uploader

**Via FTP/SFTP** vers `public_html/lux-iles/` :

```
routes/web.php                    ⚠️ CRITIQUE - Contient routes destinations + services
resources/views/pages/contact.blade.php
resources/views/components/footer.blade.php
```

---

## 🚀 Procédure de mise à jour

### Étape 1 : Sauvegarde (recommandé)

```bash
# Via terminal Hostinger ou cPanel
cd public_html/lux-iles

# Créer une sauvegarde des fichiers actuels
cp routes/web.php routes/web.php.backup
cp resources/views/pages/contact.blade.php resources/views/pages/contact.blade.php.backup
cp resources/views/components/footer.blade.php resources/views/components/footer.blade.php.backup
```

### Étape 2 : Upload des fichiers

**Via FileZilla ou votre client FTP :**

1. **Se connecter** à votre serveur Hostinger
2. **Uploader** les 3 fichiers suivants vers `public_html/lux-iles/` :

   - `routes/web.php`
   - `resources/views/pages/contact.blade.php`
   - `resources/views/components/footer.blade.php`

3. **Écraser** les fichiers existants

### Étape 3 : Nettoyer le cache (⚠️ CRITIQUE)

```bash
# Via terminal Hostinger ou cPanel
cd public_html/lux-iles

# ⚠️ IMPORTANT : Vider le cache des routes AVANT de reconstruire
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Reconstruire les caches pour la production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

**⚠️ Note importante :** Si vous ne videz pas le cache des routes (`route:clear`), Laravel continuera d'utiliser l'ancien cache qui ne contient pas les nouvelles routes.

### Étape 4 : Vérifier les routes

```bash
# Vérifier que les routes des services sont bien enregistrées
php artisan route:list --name=services

# Vérifier que les routes des destinations sont bien enregistrées
php artisan route:list --name=destination
```

**Routes des services à vérifier :**
- `services.conciergerie`
- `services.chef-domicile`
- `services.transferts-prives`
- `services.activites-exclusives`

**Routes des destinations à vérifier :**
- `destination.martinique`
- `destination.guadeloupe`
- `destination.saint-barthelemy`
- `destination.saint-martin`
- `destination.les-saintes`

---

## 📋 Liste complète des fichiers modifiés

| Fichier | Chemin serveur | Statut | Description |
|---------|----------------|--------|-------------|
| Routes | `routes/web.php` | ⚠️ **CRITIQUE** | Contient les routes des destinations ET des services (à uploader absolument) |
| Page Contact | `resources/views/pages/contact.blade.php` | ✅ Modifié | Ajout de la détection du paramètre `subject` et nouveaux sujets |
| Footer | `resources/views/components/footer.blade.php` | ✅ Modifié | Mise à jour des liens des services et destinations |

---

## ✅ Validation post-déploiement

### A. Routes fonctionnelles

**Routes des services - Testez chaque route dans votre navigateur :**

- [ ] `https://votre-domaine.com/services/conciergerie` → Redirige vers `/contact?subject=Conciergerie 24/7`
- [ ] `https://votre-domaine.com/services/chef-domicile` → Redirige vers `/contact?subject=Chef à domicile`
- [ ] `https://votre-domaine.com/services/transferts-prives` → Redirige vers `/contact?subject=Transferts privés`
- [ ] `https://votre-domaine.com/services/activites-exclusives` → Redirige vers `/contact?subject=Activités exclusives`

**Routes des destinations - Testez chaque route dans votre navigateur :**

- [ ] `https://votre-domaine.com/destination/martinique` → Redirige vers `/villas?island=1`
- [ ] `https://votre-domaine.com/destination/guadeloupe` → Redirige vers `/villas?island=2`
- [ ] `https://votre-domaine.com/destination/saint-barthelemy` → Redirige vers `/villas?island=3`
- [ ] `https://votre-domaine.com/destination/saint-martin` → Redirige vers `/villas?island=4`
- [ ] `https://votre-domaine.com/destination/les-saintes` → Redirige vers `/villas?island=5`

### B. Footer mis à jour

**Liens Services :**
- [ ] Les 4 liens "Services" dans le footer sont fonctionnels
- [ ] Clic sur "Conciergerie 24/7" → Redirige vers la page de contact avec le sujet pré-sélectionné
- [ ] Clic sur "Chef à domicile" → Redirige vers la page de contact avec le sujet pré-sélectionné
- [ ] Clic sur "Transferts privés" → Redirige vers la page de contact avec le sujet pré-sélectionné
- [ ] Clic sur "Activités exclusives" → Redirige vers la page de contact avec le sujet pré-sélectionné

**Liens Destinations :**
- [ ] Les 3 liens "Destinations" dans le footer sont fonctionnels
- [ ] Clic sur "Saint-Barthélemy" → Redirige vers `/villas?island=3` (pas d'erreur 500)
- [ ] Clic sur "Guadeloupe" → Redirige vers `/villas?island=2` (pas d'erreur 500)
- [ ] Clic sur "Martinique" → Redirige vers `/villas?island=1` (pas d'erreur 500)

### C. Formulaire de contact

- [ ] Le champ "Sujet" est pré-rempli quand on arrive depuis un lien de service
- [ ] Les nouveaux sujets apparaissent dans la liste déroulante :
  - Conciergerie 24/7
  - Chef à domicile
  - Transferts privés
  - Activités exclusives

---

## 🔄 Rollback (en cas de problème)

Si quelque chose ne fonctionne pas :

```bash
# Restaurer les fichiers sauvegardés
cp routes/web.php.backup routes/web.php
cp resources/views/pages/contact.blade.php.backup resources/views/pages/contact.blade.php
cp resources/views/components/footer.blade.php.backup resources/views/components/footer.blade.php

# Nettoyer le cache
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## 📝 Résumé des modifications

### 1. Routes ajoutées (`routes/web.php`)

**Routes pour les destinations (îles) :**
```php
Route::get('/destination/martinique', function () {
    return redirect()->route('villas.index', ['island' => 1]);
})->name('destination.martinique');

Route::get('/destination/guadeloupe', function () {
    return redirect()->route('villas.index', ['island' => 2]);
})->name('destination.guadeloupe');

Route::get('/destination/saint-barthelemy', function () {
    return redirect()->route('villas.index', ['island' => 3]);
})->name('destination.saint-barthelemy');

Route::get('/destination/saint-martin', function () {
    return redirect()->route('villas.index', ['island' => 4]);
})->name('destination.saint-martin');

Route::get('/destination/les-saintes', function () {
    return redirect()->route('villas.index', ['island' => 5]);
})->name('destination.les-saintes');
```

**Routes pour les services (redirection vers contact avec sujet pré-rempli) :**
```php
Route::get('/services/conciergerie', function () {
    return redirect()->route('contact.index', ['subject' => 'Conciergerie 24/7']);
})->name('services.conciergerie');

Route::get('/services/chef-domicile', function () {
    return redirect()->route('contact.index', ['subject' => 'Chef à domicile']);
})->name('services.chef-domicile');

Route::get('/services/transferts-prives', function () {
    return redirect()->route('contact.index', ['subject' => 'Transferts privés']);
})->name('services.transferts-prives');

Route::get('/services/activites-exclusives', function () {
    return redirect()->route('contact.index', ['subject' => 'Activités exclusives']);
})->name('services.activites-exclusives');
```

### 2. Page Contact modifiée

- Détection du paramètre `subject` dans l'URL
- Pré-sélection automatique du sujet dans le select
- Ajout de 4 nouveaux sujets dans la liste déroulante

### 3. Footer mis à jour

- Remplacement des liens `#` par les routes Laravel
- Tous les liens pointent vers les nouvelles routes de services

---

## ⚡ Temps estimé : 5-10 minutes

1. **Sauvegarde** : 1 minute
2. **Upload FTP** : 2 minutes
3. **Cache clearing** : 1 minute
4. **Tests** : 2 minutes
5. **Validation** : 1 minute

---

**🎯 C'est tout ! Pas besoin de redéployer toute l'application.**
