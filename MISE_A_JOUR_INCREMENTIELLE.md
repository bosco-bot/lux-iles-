# 🔄 MISE À JOUR INCRÉMENTIELLE - PAGES LÉGALES

## 📋 Fichiers à mettre à jour sur le serveur

### A. Nouveaux fichiers à uploader

**Via FTP/SFTP** vers `public_html/lux-iles/` :

```
resources/views/pages/mentions-legales.blade.php
resources/views/pages/politique-confidentialite.blade.php
resources/views/pages/politique-cookies.blade.php
resources/views/pages/cgv.blade.php
resources/views/components/footer.blade.php
```

### B. Routes à vérifier

Les routes sont déjà définies dans `routes/web.php` :

```php
// Ces routes existent déjà sur le serveur
Route::get('/mentions-legales', function () {
    return view('pages.mentions-legales');
})->name('mentions-legales');

Route::get('/politique-confidentialite', function () {
    return view('pages.politique-confidentialite');
})->name('politique-confidentialite');

Route::get('/politique-cookies', function () {
    return view('pages.politique-cookies');
})->name('politique-cookies');

Route::get('/cgv', function () {
    return view('pages.cgv');
})->name('cgv');
```

---

## 🚀 Procédure de mise à jour

### Étape 1 : Sauvegarde (recommandé)

```bash
# Via terminal Hostinger ou cPanel
cd public_html/lux-iles

# Créer une sauvegarde des fichiers actuels
cp resources/views/components/footer.blade.php resources/views/components/footer.blade.php.backup
```

### Étape 2 : Upload des fichiers

**Via FileZilla ou votre client FTP :**

1. **Se connecter** à votre serveur Hostinger
2. **Uploader** les 5 fichiers suivants vers `public_html/lux-iles/` :

   - `resources/views/pages/mentions-legales.blade.php`
   - `resources/views/pages/politique-confidentialite.blade.php`
   - `resources/views/pages/politique-cookies.blade.php`
   - `resources/views/pages/cgv.blade.php`
   - `resources/views/components/footer.blade.php`

3. **Écraser** les fichiers existants

### Étape 3 : Nettoyer le cache

```bash
# Via terminal Hostinger ou cPanel
cd public_html/lux-iles

# Nettoyer le cache des vues
php artisan view:clear

# Optionnel : vider tous les caches
php artisan cache:clear
php artisan config:clear
```

### Étape 4 : Tests

**Tester chaque page :**

```bash
# Via navigateur ou curl
curl https://votre-domaine.com/mentions-legales
curl https://votre-domaine.com/politique-confidentialite
curl https://votre-domaine.com/politique-cookies
curl https://votre-domaine.com/cgv
```

---

## 📋 Liste complète des fichiers modifiés

| Fichier | Chemin serveur | Statut |
|---------|----------------|--------|
| Mentions légales | `resources/views/pages/mentions-legales.blade.php` | ✅ Nouveau |
| Politique confidentialité | `resources/views/pages/politique-confidentialite.blade.php` | ✅ Nouveau |
| Politique cookies | `resources/views/pages/politique-cookies.blade.php` | ✅ Nouveau |
| CGV | `resources/views/pages/cgv.blade.php` | ✅ Nouveau |
| Footer modifié | `resources/views/components/footer.blade.php` | ✅ Modifié |

---

## ✅ Validation post-déploiement

### A. Pages accessibles

- [ ] `https://votre-domaine.com/mentions-legales`
- [ ] `https://votre-domaine.com/politique-confidentialite`
- [ ] `https://votre-domaine.com/politique-cookies`
- [ ] `https://votre-domaine.com/cgv`

### B. Footer mis à jour

- [ ] Liens séparés par des pipes `|` dans le footer
- [ ] 4 liens visibles : Mentions légales | Politique de confidentialité | Politique de cookies | CGV

### C. Navigation fonctionnelle

- [ ] Liens du footer redirigent vers les bonnes pages
- [ ] Bouton "Retour à l'accueil" sur chaque page légale

---

## 🔄 Rollback (en cas de problème)

Si quelque chose ne fonctionne pas :

```bash
# Restaurer le footer
cp resources/views/components/footer.blade.php.backup resources/views/components/footer.blade.php

# Supprimer les nouvelles pages (si nécessaire)
rm resources/views/pages/mentions-legales.blade.php
rm resources/views/pages/politique-confidentialite.blade.php
rm resources/views/pages/politique-cookies.blade.php
rm resources/views/pages/cgv.blade.php

# Nettoyer le cache
php artisan view:clear
php artisan cache:clear
```

---

## ⚡ Temps estimé : 5-10 minutes

1. **Upload FTP** : 2 minutes
2. **Cache clearing** : 1 minute
3. **Tests** : 2 minutes
4. **Validation** : 1 minute

---

**🎯 C'est tout ! Pas besoin de redéployer toute l'application.**