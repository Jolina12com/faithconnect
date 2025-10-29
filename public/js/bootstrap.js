import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: window.pusherKey,
    cluster: window.pusherCluster,
    forceTLS: true,
    encrypted: true,
    wsHost: `ws-${window.pusherCluster}.pusher.com`,
    wsPort: 443,
    wssPort: 443,
    enabledTransports: ['ws', 'wss'],
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
        }
    }
});

console.log("Echo Initialized:", window.Echo);

