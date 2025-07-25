import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

import Echo from 'laravel-echo';
import io from 'socket.io-client';

window.io = io;

window.Echo = new Echo({
    broadcaster: 'socket.io',
    host: 'http://127.0.0.1:6001',
    wsPort: 6001,
    forceTLS: false,
    transports: ['websocket'],
});

// window.Echo = new Echo({
//     broadcaster: 'socket.io',
//     host: 'https://3b36-176-111-179-155.ngrok-free.app',
//     transports: ['websocket'],
//     secure: true,
//     forceTLS: true,
// });