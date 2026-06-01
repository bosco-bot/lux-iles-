# Dépannage - Erreur lors de la création de réservation

## Comment vérifier l'erreur exacte

L'erreur est capturée et loggée dans Laravel. Pour voir l'erreur exacte :

### Sur le serveur (via SSH)

```bash
# Voir les dernières erreurs
tail -n 100 storage/logs/laravel.log

# Ou chercher spécifiquement les erreurs de réservation
grep -i "Erreur lors de la création de la réservation" storage/logs/laravel.log -A 10
```

### Via le panneau d'administration (si accessible)

Si `APP_DEBUG=true` est activé, l'erreur devrait s'afficher directement.

## Causes possibles

### 1. Problème avec les tarifs saisonniers
Si la villa a des `seasonalPrices` mais que les `seasons` ne sont pas correctement chargées, cela peut causer une erreur dans `calculatePriceForPeriod`.

**Solution** : Vérifier que la table `seasons` a des données valides.

### 2. Problème avec les notifications
Si la création de notification échoue, cela peut bloquer la transaction.

**Solution** : Vérifier que la table `notifications` existe et est correctement configurée.

### 3. Problème avec Stripe
Si les clés Stripe ne sont pas configurées, la création du PaymentIntent peut échouer (mais devrait être dans un try-catch séparé).

**Solution** : Vérifier les clés Stripe dans `/admin/settings`.

### 4. Problème de validation
Si certaines données ne sont pas valides (dates, montants, etc.), la création peut échouer.

## Solution temporaire : Améliorer le message d'erreur

Pour voir l'erreur exacte, vous pouvez modifier temporairement le contrôleur pour retourner le message d'erreur dans la réponse JSON (en développement uniquement).

**ATTENTION** : Ne faites cela qu'en développement, jamais en production avec des données sensibles !

```php
// Dans BookingController.php, ligne ~433
\Log::error('Erreur lors de la création de la réservation: ' . $e->getMessage());
\Log::error('Stack trace: ' . $e->getTraceAsString());

return response()->json([
    'success' => false,
    'message' => 'Une erreur est survenue lors de la confirmation de votre réservation. Veuillez réessayer.',
    // En développement seulement :
    'error' => config('app.debug') ? $e->getMessage() : null,
], 500);
```








