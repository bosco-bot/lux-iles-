import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Laravel Echo pour le broadcasting en temps réel
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Configuration Echo pour Pusher ou Soketi
const pusherConfig = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'app-key',
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        }
    },
};

// Si on utilise Soketi (host et port définis), utiliser la config Soketi
if (import.meta.env.VITE_PUSHER_HOST) {
    pusherConfig.wsHost = import.meta.env.VITE_PUSHER_HOST;
    pusherConfig.wsPort = import.meta.env.VITE_PUSHER_PORT || 6001;
    pusherConfig.wssPort = import.meta.env.VITE_PUSHER_PORT || 6001;
    pusherConfig.forceTLS = false;
    pusherConfig.encrypted = false;
    pusherConfig.disableStats = true;
    pusherConfig.enabledTransports = ['ws', 'wss'];
} else {
    // Configuration Pusher standard
    pusherConfig.cluster = import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1';
    pusherConfig.forceTLS = true;
    pusherConfig.encrypted = true;
}

window.Echo = new Echo(pusherConfig);