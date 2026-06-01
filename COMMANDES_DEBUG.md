# Commandes pour déboguer l'erreur de réservation

## 1. Voir les dernières lignes du log (sans filtre)

```bash
tail -n 100 storage/logs/laravel.log
```

## 2. Voir toutes les erreurs récentes

```bash
grep -i "error\|exception\|failed" storage/logs/laravel.log | tail -n 50
```

## 3. Voir les erreurs de la dernière heure

```bash
tail -n 500 storage/logs/laravel.log | grep -i "error" -A 10
```

## 4. Vider le log et refaire une tentative

```bash
# Sauvegarder l'ancien log
cp storage/logs/laravel.log storage/logs/laravel.log.backup

# Vider le log
echo "" > storage/logs/laravel.log

# Maintenant, essayez de créer une réservation
# Puis voir le log
cat storage/logs/laravel.log
```

## 5. Vérifier les permissions du fichier de log

```bash
ls -la storage/logs/laravel.log
# Doit être accessible en écriture par le serveur web
```

## 6. Vérifier si APP_DEBUG est activé

```bash
grep APP_DEBUG .env
# Si APP_DEBUG=false, changez temporairement en true pour voir les erreurs
```

## 7. Tester la création de réservation avec plus de détails

Après avoir activé APP_DEBUG=true et vidé le log, essayez de créer une réservation.
L'erreur devrait apparaître soit :
- Dans la réponse JSON (champ `error`)
- Dans les logs (storage/logs/laravel.log)
- Directement dans le navigateur si APP_DEBUG=true








