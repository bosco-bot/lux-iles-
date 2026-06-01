# Configuration Soketi pour LUXÎLES

## ✅ Avantages de Soketi

- ✅ **100% Gratuit** (contrairement à Pusher qui est payant)
- ✅ **Open Source** et auto-hébergé
- ✅ **Compatible avec Pusher** (utilise le même protocole)
- ✅ **Parfait pour le développement** et la production
- ✅ **Aucune limitation** de connexions ou de messages

## 📦 Installation

### Option 1 : Installation locale (déjà faite)
```bash
npm install --save-dev @soketi/soketi
```

### Option 2 : Installation globale (nécessite sudo)
```bash
sudo npm install -g @soketi/soketi
```

## 🚀 Démarrer Soketi

### Si installation locale :
```bash
cd /home/bosco/Bureau/lux-iles
npx soketi start
```

### Si installation globale :
```bash
soketi start
```

Soketi démarrera sur `http://127.0.0.1:6001` par défaut.

## ⚙️ Configuration dans .env

Ajoutez ces lignes dans votre fichier `.env` :

```env
BROADCAST_CONNECTION=soketi

PUSHER_APP_ID=app-id
PUSHER_APP_KEY=app-key
PUSHER_APP_SECRET=app-secret
PUSHER_HOST=127.0.0.1
PUSHER_PORT=6001
PUSHER_SCHEME=http

VITE_PUSHER_APP_KEY=app-key
VITE_PUSHER_HOST=127.0.0.1
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
```

## 📝 Script npm pour faciliter le démarrage

Vous pouvez ajouter un script dans `package.json` :

```json
{
  "scripts": {
    "soketi": "npx soketi start"
  }
}
```

Puis lancer avec :
```bash
npm run soketi
```

## ✅ Test

1. Démarrer Soketi : `npx soketi start`
2. Démarrer Laravel : `php artisan serve`
3. Ouvrir l'application dans le navigateur
4. Vérifier la console du navigateur (F12) - vous devriez voir les connexions WebSocket

## 🔍 Dépannage

- Si Soketi ne démarre pas : vérifier qu'aucun autre service n'utilise le port 6001
- Si les connexions échouent : vérifier que `PUSHER_HOST` et `PUSHER_PORT` correspondent
- En production : pensez à utiliser un reverse proxy (nginx) pour Soketi









