window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";

const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
} else {
    console.error("❌ CSRF token not found!");
}

// Initialize Echo with the global Pusher instance
window.Echo = new window.Echo({
    broadcaster: "pusher",
    key: window.pusherKey,
    cluster: window.pusherCluster,
    forceTLS: true,
    encrypted: true,
    wsHost: `ws-${window.pusherCluster}.pusher.com`,
    wsPort: 443,
    wssPort: 443,
    enabledTransports: ['wss'],
    authEndpoint: "/broadcasting/auth",
    auth: {
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.content || '',
        }
    }
});

// Debug log
console.log("Echo Initialized:", window.Echo);

// Get the user ID either from a meta tag or from a global variable (set in Blade)
const userId = document.querySelector('meta[name="user-id"]')?.getAttribute("content") || window.authId;

// Chat channels (typing handled by Firebase)
if (userId) {
    window.Echo.private(`chat.${userId}`)
        .listen("NewMessage", (e) => {
            console.log("💬 New message received:", e);
            const chatBox = document.getElementById("chat-box");
            if (chatBox) {
                const alignment = e.message.sender_id == userId ? 'text-right' : 'text-left';
                const time = new Date().toLocaleTimeString();
                chatBox.innerHTML += `
                    <div class="message ${alignment}">
                        <p>${e.message.message}</p>
                        <small>${time}</small>
                    </div>`;
                scrollToBottom();
            }
        });
}

// Simple helper to scroll the chat box to the bottom
function scrollToBottom() {
    const chatBox = document.getElementById("chat-box");
    if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
}
