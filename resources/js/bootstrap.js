import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Real-time via Laravel Echo using Pusher protocol (supports Pusher or Laravel Reverb)
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

(() => {
	const key = import.meta.env.VITE_PUSHER_APP_KEY || import.meta.env.VITE_REVERB_APP_KEY;
	if (!key) return;

	window.Pusher = Pusher;
	window.Echo = new Echo({
		broadcaster: 'pusher',
		key,
		cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt1',
		wsHost: (import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST || window.location.hostname),
		wsPort: Number(import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 8080),
		wssPort: Number(import.meta.env.VITE_REVERB_PORT || import.meta.env.VITE_PUSHER_PORT || 8080),
		forceTLS: !!(import.meta.env.VITE_PUSHER_FORCE_TLS || import.meta.env.VITE_REVERB_FORCE_TLS),
		enabledTransports: ['ws', 'wss'],
	});
})();
