import Echo from 'laravel-echo';

import Pusher from 'pusher-js';
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    debug: true
});

window.Echo.connector.pusher.connection.bind('connected', () => {
      console.log('connected');
    });

window.Echo.connector.pusher.connection.bind('disconnected', () => {
       console.log('disconnected');
    });

window.Echo.channel('comments')
    .listen('comment.posted', (e) => {
        console.log('Nuevo comentario:', e.comment);
        // ... (actualizar la interfaz de usuario)
    });
