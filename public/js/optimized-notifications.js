// Optimized real-time notifications - single source of truth
class OptimizedNotifications {
    constructor() {
        this.userId = window.authId;
        this.baseUrl = window.location.origin;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
        
        if (this.userId && window.Echo) this.init();
    }

    init() {
        this.setupChannels();
        this.loadInitialCounts();
        this.bindEvents();
        
        // Test badge visibility
        setTimeout(() => {
            const badge = document.getElementById('notif-badge');
            if (badge) {
                console.log('Badge element found:', badge);
                console.log('Badge display:', badge.style.display);
                console.log('Badge classes:', badge.className);
            } else {
                console.error('Badge element not found!');
            }
        }, 2000);
    }

    setupChannels() {
        // Notifications channel
        window.Echo.private(`notifications.${this.userId}`)
            .listen('NotificationCountChanged', (e) => {
                this.updateBadge('#notif-badge', e.count);
                this.updateBadge('#mobile-notif-badge', e.count);
                this.updateBadge('#sidebar-notif-badge', e.count);
                this.updateBadge('.notification-badge', e.count);
                this.updateTitle(e.count);
                if (e.message) this.showToast(e.message);
                console.log('Notification count updated:', e.count);
            });

        // Chat channel  
        window.Echo.private(`chat.${this.userId}`)
            .listen('MessageReceived', (e) => {
                this.updateBadge('#unread-count', e.unread_count);
                this.updateBadge('#mobile-msg-badge', e.unread_count);
                this.updateBadge('#sidebar-msg-badge', e.unread_count);
                this.updateBadge('.chat-badge', e.unread_count);
                this.showToast(`Message from ${e.sender_name}`);
                console.log('Chat count updated:', e.unread_count);
            });
    }

    async loadInitialCounts() {
        try {
            const [notif, chat] = await Promise.all([
                fetch(`${this.baseUrl}/notifications/count`),
                fetch(`${this.baseUrl}/chat/unread-count`)
            ]);
            
            if (notif.ok && chat.ok) {
                const [notifData, chatData] = await Promise.all([notif.json(), chat.json()]);
                
                // Update all notification badge selectors
                this.updateBadge('#notif-badge', notifData.count);
                this.updateBadge('#mobile-notif-badge', notifData.count);
                this.updateBadge('#sidebar-notif-badge', notifData.count);
                this.updateBadge('.notification-badge', notifData.count);
                
                // Update chat badges
                this.updateBadge('#unread-count', chatData.unread);
                this.updateBadge('#mobile-msg-badge', chatData.unread);
                this.updateBadge('#sidebar-msg-badge', chatData.unread);
                this.updateBadge('.chat-badge', chatData.unread);
                
                this.updateTitle(notifData.count);
                console.log('Initial counts loaded:', notifData.count, chatData.unread);
            }
        } catch (error) {
            console.error('Failed to load counts:', error);
        }
    }

    updateBadge(selector, count) {
        const elements = selector.startsWith('#') ? [document.querySelector(selector)] : document.querySelectorAll(selector);
        elements.forEach(badge => {
            if (badge) {
                if (count > 0) {
                    badge.textContent = count;
                    badge.style.display = 'inline';
                    badge.classList.remove('d-none');
                } else {
                    badge.style.display = 'none';
                    badge.classList.add('d-none');
                }
            }
        });
    }

    updateTitle(count) {
        const baseTitle = document.title.replace(/^\(\d+\)\s/, '');
        document.title = count > 0 ? `(${count}) ${baseTitle}` : baseTitle;
    }

    bindEvents() {
        document.addEventListener('click', (e) => {
            const notification = e.target.closest('.clickable-notification');
            if (notification) {
                const id = notification.dataset.notificationId;
                if (id) window.location.href = `${this.baseUrl}/notifications/${id}/redirect`;
            }
        });
    }

    showToast(message) {
        const toast = document.createElement('div');
        toast.className = 'alert alert-info position-fixed';
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 250px;';
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }
}

// Initialize once DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.optimizedNotifications = new OptimizedNotifications();
    });
} else {
    window.optimizedNotifications = new OptimizedNotifications();
}