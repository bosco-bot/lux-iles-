# Configuration du Système de Notifications et Chat en Temps Réel

## ✅ Ce qui a été implémenté

### Backend
1. ✅ Configuration du broadcasting (config/broadcasting.php)
2. ✅ Routes de channels (routes/channels.php)
3. ✅ Événements de broadcast :
   - `MessageSent` - pour les messages instantanés
   - `NotificationCreated` - pour les notifications en temps réel
4. ✅ Notifications avec broadcast :
   - `ReservationCreatedNotification`
   - `PaymentReceivedNotification`
   - `MessageReceivedNotification`
5. ✅ Intégration dans les contrôleurs :
   - BookingController - broadcast lors de création de réservation
   - PaymentController - broadcast lors de paiement
   - MessageController (Admin et Client) - broadcast lors d'envoi de message

### Frontend
1. ✅ Configuration Laravel Echo (resources/js/bootstrap.js)
2. ✅ Écoute des notifications en temps réel (layouts/admin.blade.php)
3. ✅ Écoute des messages en temps réel (pages/admin/messages.blade.php)
4. ✅ Interface de notifications avec dropdown
5. ✅ Chat instantané avec ajout automatique des messages

## 📦 Packages à installer

```bash
npm install --save-dev laravel-echo pusher-js
```

Puis compiler les assets :
```bash
npm run build
# ou pour le développement
npm run dev
```

## ⚙️ Configuration

### Option 1 : Pusher (Service cloud payant)

Ajoutez dans votre `.env` :
```env
BROADCAST_CONNECTION=pusher

PUSHER_APP_ID=your_app_id
PUSHER_APP_KEY=your_app_key
PUSHER_APP_SECRET=your_app_secret
PUSHER_APP_CLUSTER=mt1
```

Et dans votre `.env` pour Vite (si vous utilisez Vite) :
```env
VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
```

### Option 2 : Soketi (Gratuit, auto-hébergé)

1. Installer Soketi :
```bash
npm install -g @soketi/soketi
```

2. Démarrer Soketi :
```bash
soketi start
```

3. Configuration dans `.env` :
```env
BROADCAST_CONNECTION=soketi

PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

4. Configuration Vite dans `.env` :
```env
VITE_PUSHER_APP_KEY=app-key
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
```

5. Mettre à jour `resources/js/bootstrap.js` pour Soketi :
```javascript
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_HOST || '127.0.0.1',
    wsPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    wssPort: import.meta.env.VITE_PUSHER_PORT || 6001,
    forceTLS: false,
    encrypted: false,
    disableStats: true,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        }
    },
});
```

## 🔧 Installation du package Pusher PHP (requis pour Pusher/Soketi)

```bash
composer require pusher/pusher-php-server
```

## 🚀 Démarrer les services

### Pour le développement avec Soketi :
```bash
# Terminal 1 : Soketi
soketi start

# Terminal 2 : Laravel
php artisan serve

# Terminal 3 : Queue worker (si vous utilisez les queues)
php artisan queue:work

# Terminal 4 : Compiler les assets (si en développement)
npm run dev
```

### Pour le développement avec Pusher :
```bash
# Terminal 1 : Laravel
php artisan serve

# Terminal 2 : Queue worker
php artisan queue:work

# Terminal 3 : Compiler les assets
npm run dev
```

## ✅ Test du système

1. **Notifications** :
   - Créer une réservation → Les admins reçoivent une notification en temps réel
   - Confirmer un paiement → Les admins reçoivent une notification en temps réel
   - Envoyer un message → Le destinataire reçoit une notification en temps réel

2. **Chat instantané** :
   - Ouvrir deux navigateurs (ou onglets) avec deux utilisateurs différents
   - Envoyer un message depuis l'un → Le message apparaît instantanément chez l'autre

## 📝 Notes importantes

- Les routes de broadcasting sont automatiquement incluses dans Laravel 12
- Le middleware d'authentification pour les canaux privés est géré automatiquement
- Les notifications sont stockées en base de données ET broadcastées en temps réel
- Les messages sont broadcastés via l'événement `MessageSent`

## 🔍 Dépannage

### Les notifications ne s'affichent pas en temps réel
- Vérifier que Soketi/Pusher est démarré
- Vérifier la configuration dans `.env`
- Vérifier la console du navigateur pour les erreurs
- Vérifier que `bootstrap.js` est compilé et inclus dans la page

### Les messages ne s'affichent pas en temps réel
- Vérifier que l'événement `MessageSent` est bien déclenché
- Vérifier la console du navigateur
- Vérifier que les canaux privés sont correctement autorisés dans `routes/channels.php`

### Erreur "Broadcasting route not found"
- Vérifier que le package `pusher/pusher-php-server` est installé
- Vérifier que `BROADCAST_CONNECTION` est correctement configuré dans `.env`









