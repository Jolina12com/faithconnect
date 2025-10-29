
   
   // Chat variables
    let authId = window.authId || null;
    let receiver_id = null;
    let typingTimeout;
    let currentRecipientName = '';
    let pusherInstance = null;
    let activeReactionMessageId = null;

    // DOM elements
    const mobileBackBtn = document.getElementById('mobileBackBtn');
    const mobileUsersBtn = document.getElementById('mobileUsersBtn');
    const usersSection = document.getElementById('usersSection');
    const mobileOverlay = document.getElementById('mobileOverlay');
    const messageInput = document.getElementById('messageInput');
    const sendBtn = document.getElementById('sendBtn');
    const usersList = document.getElementById('usersList');
    const messagesContainer = document.getElementById('messagesContainer');
    const typingIndicator = document.getElementById('typingIndicator');
    const emptyState = document.getElementById('emptyState');
    const chatContent = document.getElementById('chatContent');

    // Bible API integration
    let bibleVerses = {};
    let isLoadingVerses = false;
    
    // Fetch Bible verses from API
    async function fetchBibleVerses(category) {
        if (isLoadingVerses) return [];
        
        try {
            isLoadingVerses = true;
            const response = await fetch('/chat/bible-verses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ category })
            });
            
            if (response.ok) {
                const data = await response.json();
                return data.verses || [];
            }
        } catch (error) {
            console.error('Error fetching Bible verses:', error);
        } finally {
            isLoadingVerses = false;
        }
        return [];
    }

    const churchQuickReplies = [
        "🙏 Praying for you!",
        "✝️ God bless you!",
        "❤️ Grace and peace",
        "🕊️ I'll keep you in my prayers",
        "⛪ See you at church!",
        "📖 Let's study together"
    ];

    // Mobile Navigation Functions
    function showMobileUsers() {
        usersSection.classList.add('mobile-open');
        mobileOverlay.classList.add('active');
    }

    function hideMobileUsers() {
        usersSection.classList.remove('mobile-open');
        mobileOverlay.classList.remove('active');
    }

    function showMobileChat() {
        emptyState.classList.add('d-none');
        chatContent.classList.remove('d-none');
        if (window.innerWidth <= 768) {
            hideMobileUsers();
        }
    }

    // Event Listeners
    if (mobileBackBtn) {
        mobileBackBtn.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                emptyState.classList.remove('d-none');
                chatContent.classList.add('d-none');
                showMobileUsers();
            }
        });
    }

    if (mobileUsersBtn) {
        mobileUsersBtn.addEventListener('click', showMobileUsers);
    }

    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', hideMobileUsers);
    }

    // Auto-resize textarea
    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 100) + 'px';
        
        if (receiver_id && firebase.database) {
            firebase.database().ref('typing/' + authId + '_' + receiver_id).set({
                isTyping: true,
                timestamp: firebase.database.ServerValue.TIMESTAMP
            });
            
            clearTimeout(typingTimeout);
            typingTimeout = setTimeout(() => {
                firebase.database().ref('typing/' + authId + '_' + receiver_id).set({
                    isTyping: false,
                    timestamp: firebase.database.ServerValue.TIMESTAMP
                });
            }, 3000);
        }
    });

    // Firebase Presence System
    function setupPresenceSystem() {
        if (!firebase.database) {
            console.error('Firebase database not available');
            return;
        }
        
        const db = firebase.database();
        const myConnectionsRef = db.ref(`users/${authId}/connections`);
        const lastOnlineRef = db.ref(`users/${authId}/lastOnline`);
        const connectedRef = db.ref('.info/connected');
        
        connectedRef.on('value', (snapshot) => {
            if (snapshot.val() === false) {
                return;
            }
            
            const con = myConnectionsRef.push();
            con.onDisconnect().remove();
            lastOnlineRef.onDisconnect().set(firebase.database.ServerValue.TIMESTAMP);
            
            con.set(true);
            lastOnlineRef.set(firebase.database.ServerValue.TIMESTAMP);
        });
    }

    function monitorUserPresence(userId) {
        const userStatusRef = firebase.database().ref(`users/${userId}/connections`);
        
        userStatusRef.on('value', (snapshot) => {
            const isOnline = snapshot.exists();
            updateUserOnlineStatus(userId, isOnline);
        });
    }

    function updateUserOnlineStatus(userId, isOnline) {
        const userItem = document.querySelector(`[data-id="${userId}"]`);
        if (userItem) {
            const statusIndicator = userItem.querySelector('.online-status');
            if (statusIndicator) {
                if (isOnline) {
                    statusIndicator.style.background = 'var(--online-color)';
                    userItem.classList.add('user-online');
                } else {
                    statusIndicator.style.background = '#ccc';
                    userItem.classList.remove('user-online');
                }
            }
        }
        
        if (receiver_id == userId) {
            const chatStatus = document.getElementById('chatUserStatus');
            if (chatStatus) {
                if (isOnline) {
                    chatStatus.textContent = 'Online';
                    chatStatus.classList.add('online');
                } else {
                    firebase.database().ref(`users/${userId}/lastOnline`).once('value')
                        .then(snapshot => {
                            const lastOnline = snapshot.val();
                            if (lastOnline) {
                                const lastSeenText = formatLastSeen(lastOnline);
                                chatStatus.textContent = lastSeenText;
                                chatStatus.classList.remove('online');
                            }
                        });
                }
            }
        }
    }

    function formatLastSeen(timestamp) {
        const now = Date.now();
        const diff = now - timestamp;
        
        const minutes = Math.floor(diff / 60000);
        const hours = Math.floor(diff / 3600000);
        const days = Math.floor(diff / 86400000);
        
        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `Last seen ${minutes}m ago`;
        if (hours < 24) return `Last seen ${hours}h ago`;
        if (days < 7) return `Last seen ${days}d ago`;
        return 'Last seen recently';
    }

    // Church Features
    function addBibleVerseButton() {
        const inputActions = document.querySelector('.input-actions');
        if (!inputActions || document.getElementById('bibleVerseBtn')) return;
        
        const bibleVerseBtn = document.createElement('button');
        bibleVerseBtn.id = 'bibleVerseBtn';
        bibleVerseBtn.className = 'input-btn bible-verse-btn';
        bibleVerseBtn.title = 'Share Bible Verse';
        bibleVerseBtn.innerHTML = '<i class="fas fa-plus"></i>';
        bibleVerseBtn.style.background = 'linear-gradient(135deg, #8b7355 0%, #6d5a44 100%)';
        bibleVerseBtn.style.color = 'white';
        bibleVerseBtn.style.position = 'relative';
        
        inputActions.insertBefore(bibleVerseBtn, inputActions.firstChild);
        bibleVerseBtn.addEventListener('click', showBibleVerseModal);
    }

    function showBibleVerseModal() {
        const existingModal = document.getElementById('bibleVerseModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        const modal = document.createElement('div');
        modal.id = 'bibleVerseModal';
        modal.innerHTML = `
            <div class="bible-modal-overlay">
                <div class="bible-modal-content">
                    <div class="bible-modal-header">
                        <h5><i class="fas fa-bible"></i> Share a Bible Verse</h5>
                        <button class="bible-modal-close" onclick="closeBibleVerseModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="bible-modal-body">
                        <div class="bible-search-container">
                            <input type="text" id="verseSearch" class="bible-search-input" placeholder="Search verses by topic or reference...">
                            <button class="bible-search-btn" onclick="searchVerses()">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="bible-category-tabs">
                            ${['encouragement', 'peace', 'love', 'guidance', 'prayer', 'praise', 'faith', 'hope', 'forgiveness', 'strength', 'comfort', 'wisdom', 'healing', 'protection', 'thanksgiving'].map(category => 
                                `<button class="bible-tab" data-category="${category}">
                                    ${category.charAt(0).toUpperCase() + category.slice(1)}
                                </button>`
                            ).join('')}
                        </div>
                        <div class="bible-verses-list" id="bibleVersesList"></div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        
        document.querySelectorAll('.bible-tab').forEach((tab, index) => {
            if (index === 0) tab.classList.add('active');
            tab.addEventListener('click', function() {
                document.querySelectorAll('.bible-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                loadVersesByCategory(this.dataset.category);
            });
        });
        
        loadVersesByCategory('encouragement');
    }

    async function loadVersesByCategory(category) {
        const versesList = document.getElementById('bibleVersesList');
        if (!versesList) return;
        
        versesList.innerHTML = '<div class="text-center py-4"><div class="loading-spinner"></div><div class="mt-2">Loading verses...</div></div>';
        
        try {
            const verses = await fetchBibleVerses(category);
            
            if (verses && verses.length > 0) {
                const versesHTML = verses.map(v => `
                    <div class="bible-verse-item" onclick='selectBibleVerse(${JSON.stringify(v).replace(/'/g, "&#39;")})'>
                        <div class="bible-verse-reference">${v.reference}</div>
                        <div class="bible-verse-text">${v.text}</div>
                    </div>
                `).join('');
                
                const moreButton = `
                    <div class="load-more-verses" onclick="loadMoreVerses('${category}')">
                        <i class="fas fa-plus"></i>
                        <span>+ More ${category.charAt(0).toUpperCase() + category.slice(1)} Verses</span>
                    </div>
                `;
                
                versesList.innerHTML = versesHTML + moreButton;
            } else {
                versesList.innerHTML = '<div class="text-center py-4 text-muted">No verses available for this category.</div>';
            }
        } catch (error) {
            console.error('Error loading verses:', error);
            versesList.innerHTML = '<div class="text-center py-4 text-danger">Failed to load verses. Please try again.</div>';
        }
    }

    window.selectBibleVerse = function(verse) {
        const verseMessage = `📖 ${verse.reference}\n\n"${verse.text}"`;
        messageInput.value = verseMessage;
        closeBibleVerseModal();
        messageInput.focus();
    };

    window.closeBibleVerseModal = function() {
        const modal = document.getElementById('bibleVerseModal');
        if (modal) {
            modal.remove();
        }
    };

    // Search verses functionality
    window.searchVerses = async function() {
        const searchInput = document.getElementById('verseSearch');
        const query = searchInput.value.trim();
        
        if (!query) {
            alert('Please enter a search term');
            return;
        }
        
        const versesList = document.getElementById('bibleVersesList');
        versesList.innerHTML = '<div class="text-center py-4"><div class="loading-spinner"></div><div class="mt-2">Searching verses...</div></div>';
        
        try {
            const response = await fetch('/chat/search-verses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ query })
            });
            
            if (response.ok) {
                const data = await response.json();
                const verses = data.verses || [];
                
                if (verses.length > 0) {
                    versesList.innerHTML = verses.map(v => `
                        <div class="bible-verse-item" onclick='selectBibleVerse(${JSON.stringify(v).replace(/'/g, "&#39;")})'>
                            <div class="bible-verse-reference">${v.reference}</div>
                            <div class="bible-verse-text">${highlightKeywords(v.text, query)}</div>
                        </div>
                    `).join('');
                } else {
                    versesList.innerHTML = '<div class="text-center py-4 text-muted">No verses found for "' + query + '"</div>';
                }
            } else {
                throw new Error('Search failed');
            }
        } catch (error) {
            console.error('Search error:', error);
            versesList.innerHTML = '<div class="text-center py-4 text-danger">Search failed. Please try again.</div>';
        }
    };

    function highlightKeywords(text, keyword) {
        const regex = new RegExp(`(${keyword})`, 'gi');
        return text.replace(regex, '<mark style="background-color: #ffeb3b; padding: 1px 2px; border-radius: 2px;">$1</mark>');
    }

    // Fast keyword search
    window.searchVerses = async function() {
        const query = document.getElementById('verseSearch').value.trim();
        if (!query) return;
        
        const versesList = document.getElementById('bibleVersesList');
        versesList.innerHTML = '<div class="text-center py-4"><div class="loading-spinner"></div></div>';
        
        try {
            const allVerses = [];
            const categories = ['love', 'peace', 'hope', 'faith', 'strength', 'comfort'];
            
            await Promise.all(categories.map(async (category) => {
                const verses = await fetchBibleVerses(category);
                allVerses.push(...verses);
            }));
            
            const matches = allVerses.filter(v => 
                v.text.toLowerCase().includes(query.toLowerCase()) ||
                v.reference.toLowerCase().includes(query.toLowerCase())
            );
            
            if (matches.length > 0) {
                versesList.innerHTML = matches.map(v => {
                    const safeVerse = JSON.stringify(v).replace(/'/g, '&apos;');
                    return `<div class="bible-verse-item" onclick='selectBibleVerse(${safeVerse})'>
                        <div class="bible-verse-reference">${highlightKeywords(v.reference, query)}</div>
                        <div class="bible-verse-text">${highlightKeywords(v.text, query)}</div>
                    </div>`;
                }).join('');
            } else {
                versesList.innerHTML = `<div class="text-center py-4 text-muted">No verses found for "${query}"</div>`;
            }
        } catch (error) {
            versesList.innerHTML = '<div class="text-center py-4 text-danger">Search failed</div>';
        }
    };

    function updateBackendStatus(online) {
        fetch('/admin/chat/update-status', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ online })
        }).catch(e => console.log('Status update failed:', e));
    }

    function checkUserStatus(userId) {
        fetch(`/admin/chat/user-status/${userId}`)
            .then(res => res.json())
            .then(data => updateUserOnlineStatus(userId, data.online))
            .catch(e => console.log('Status check failed:', e));
    }

    // Enter key search
    document.addEventListener('keydown', function(e) {
        if (e.target.id === 'verseSearch' && e.key === 'Enter') {
            searchVerses();
        }
    });

    // Load more verses functionality
    window.loadMoreVerses = async function(category) {
        const versesList = document.getElementById('bibleVersesList');
        const loadMoreBtn = versesList.querySelector('.load-more-verses');
        
        if (loadMoreBtn) {
            loadMoreBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading More...';
        }
        
        try {
            const response = await fetch('/chat/more-verses', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ category, offset: 5 })
            });
            
            if (response.ok) {
                const data = await response.json();
                const moreVerses = data.verses || [];
                
                if (moreVerses.length > 0) {
                    // Remove load more button
                    if (loadMoreBtn) loadMoreBtn.remove();
                    
                    // Add new verses
                    const newVersesHTML = moreVerses.map(v => `
                        <div class="bible-verse-item" onclick='selectBibleVerse(${JSON.stringify(v).replace(/'/g, "&#39;")})'>
                            <div class="bible-verse-reference">${v.reference}</div>
                            <div class="bible-verse-text">${v.text}</div>
                        </div>
                    `).join('');
                    
                    versesList.insertAdjacentHTML('beforeend', newVersesHTML);
                    
                    // Add load more button again if there might be more verses
                    if (moreVerses.length >= 5) {
                        versesList.insertAdjacentHTML('beforeend', `
                            <div class="load-more-verses" onclick="loadMoreVerses('${category}')">
                                <i class="fas fa-plus"></i>
                                <span>Load More ${category.charAt(0).toUpperCase() + category.slice(1)} Verses</span>
                            </div>
                        `);
                    }
                } else {
                    if (loadMoreBtn) {
                        loadMoreBtn.innerHTML = '<i class="fas fa-check"></i> No More Verses';
                        loadMoreBtn.style.background = '#6c757d';
                        loadMoreBtn.style.cursor = 'default';
                    }
                }
            }
        } catch (error) {
            console.error('Error loading more verses:', error);
            if (loadMoreBtn) {
                loadMoreBtn.innerHTML = '<i class="fas fa-exclamation-triangle"></i> Error Loading';
                loadMoreBtn.style.background = '#dc3545';
            }
        }
    };

    function addQuickReplyButtons() {
        const inputSection = document.querySelector('.message-input-section');
        if (!inputSection || document.getElementById('quickRepliesContainer')) return;
        
        const quickRepliesContainer = document.createElement('div');
        quickRepliesContainer.id = 'quickRepliesContainer';
        quickRepliesContainer.className = 'quick-replies-container';
        quickRepliesContainer.innerHTML = `
            <div class="quick-replies-label">Quick Replies:</div>
            <div class="quick-replies-scroll">
                ${churchQuickReplies.map(reply => 
                    `<button class="quick-reply-btn" onclick="insertQuickReply('${reply.replace(/'/g, "\\'")}')">${reply}</button>`
                ).join('')}
            </div>
        `;
        
        inputSection.insertBefore(quickRepliesContainer, inputSection.firstChild);
    }

    window.insertQuickReply = function(text) {
        messageInput.value = text;
        messageInput.focus();
    };



    // Profanity filter function
    async function filterProfanity(text) {
        try {
            const response = await fetch('/chat/filter-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message: text })
            });
            
            if (response.ok) {
                const data = await response.json();
                return data.filtered_message || text;
            }
        } catch (error) {
            console.error('Profanity filter error:', error);
        }
        return text;
    }

    function initializeChurchFeatures() {
        addBibleVerseButton();
        addQuickReplyButtons();
    }

    // Send message functionality
    let isSending = false;
    async function sendMessage() {
        let message = messageInput.value.trim();
        if (!message || !receiver_id || isSending) return;
        
        isSending = true;
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<div class="loading-spinner"></div>';
        
        try {
            // Apply word filter if available
            if (message && typeof filterBadWords === 'function') {
                message = filterBadWords(message);
            }
            
            // Apply profanity filter
            message = await filterProfanity(message);
            
            const messageId = firebase.database().ref().child('messages').push().key;
            await handleTextMessage(messageId, message);
        } finally {
            isSending = false;
            resetSendButton();
        }
    }

    async function handleTextMessage(messageId, message) {
        const messageData = {
            id: messageId,
            sender_id: authId,
            receiver_id: receiver_id,
            message: message,
            status: 'sent',
            created_at: Date.now()
        };
        
        messageInput.value = "";
        messageInput.style.height = 'auto';
        
        const firebaseData = {...messageData, created_at: firebase.database.ServerValue.TIMESTAMP};
        
        try {
            await firebase.database().ref('messages/' + messageId).set(firebaseData);
            
            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ 
                    receiver_id, 
                    message, 
                    firebase_msg_id: messageId 
                })
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Message sent successfully:', data);
            appendMessageToChat(messageData);
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Failed to send message: ' + error.message + '. Please try again.');
            throw error;
        }
    }

    function resetSendButton() {
        sendBtn.disabled = false;
        sendBtn.innerHTML = '<i class="fas fa-paper-plane"></i>';
    }

    sendBtn.addEventListener('click', async () => {
        await sendMessage();
    });

    messageInput.addEventListener('keydown', async (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            await sendMessage();
        }
    });

    // Load users function
    function loadUsers() {
        return new Promise((resolve, reject) => {
            usersList.innerHTML = '<div class="text-center py-4"><div class="loading-spinner"></div><div class="mt-2 text-muted">Loading conversations...</div></div>';
            
            fetch('/chat/users')
                .then(res => {
                    if (!res.ok) throw new Error('Failed to load users');
                    return res.json();
                })
                .then(users => {
                    usersList.innerHTML = '';
                    
                    if (users.length === 0) {
                        usersList.innerHTML = '<div class="text-center py-4 text-muted">No conversations yet</div>';
                        resolve();
                        return;
                    }
                    
                    users.forEach(user => {
                        const userElement = document.createElement('button');
                        userElement.className = `user-item ${user.has_conversation ? 'has-conversation' : 'no-conversation'}`;
                        userElement.setAttribute('data-id', user.id);
                        userElement.innerHTML = `
                            <div class="user-avatar">
                                ${user.first_name ? user.first_name.charAt(0).toUpperCase() : 'U'}
                                <div class="online-status"></div>
                            </div>
                            <div class="user-info">
                                <div class="user-name">${user.name || 'User'}</div>
                                <div class="user-preview">${user.has_conversation ? '💬 Previous conversation' : 'Click to start chatting'}</div>
                            </div>
                            <div class="user-meta">
                                <div class="unread-badge" id="unread-${user.id}"></div>
                            </div>
                        `;
                        
                        usersList.appendChild(userElement);
                        
                        // Monitor presence for this user
                        if (typeof monitorUserPresence === 'function') {
                            monitorUserPresence(user.id);
                        }
                    });
                    
                    attachUserClickHandlers();
                    
                    // Auto-select first user if available
                    if (users.length > 0) {
                        const firstUser = users[0];
                        receiver_id = firstUser.id;
                        currentRecipientName = firstUser.name;
                        
                        const firstUserElement = document.querySelector(`[data-id="${firstUser.id}"]`);
                        if (firstUserElement) {
                            firstUserElement.classList.add('active');
                            showMobileChat();
                            updateChatHeader();
                            fetchConversationName().then(() => {
                                fetchMessages();
                                setupTypingIndicator();
                            });
                        }
                    }
                    
                    resolve();
                })
                .catch(reject);
        });
    }

    // Attach click handlers to user items
    function attachUserClickHandlers() {
        document.querySelectorAll('.user-item').forEach(user => {
            user.addEventListener('click', function() {
                receiver_id = this.dataset.id;
                currentRecipientName = this.querySelector('.user-name').textContent;
                
                document.querySelectorAll('.user-item').forEach(u => u.classList.remove('active'));
                this.classList.add('active');
                
                showMobileChat();
                updateChatHeader();
                
                const badge = document.getElementById(`unread-${receiver_id}`);
                if (badge) badge.textContent = "";
                
                fetchConversationName().then(() => {
                    fetchMessages();
                    setupTypingIndicator();
                });
            });
        });
    }

    function updateChatHeader() {
        document.getElementById('chatUserName').textContent = currentRecipientName;
        const chatAvatar = document.getElementById('chatAvatar');
        chatAvatar.textContent = currentRecipientName.charAt(0).toUpperCase();
        
        // Update status to show offline initially
        const chatStatus = document.getElementById('chatUserStatus');
        if (chatStatus) {
            chatStatus.textContent = 'Offline';
            chatStatus.classList.remove('online');
        }
    }

    // Fetch conversation name before loading messages
    function fetchConversationName() {
        return new Promise((resolve) => {
            if (!receiver_id) {
                resolve();
                return;
            }
            
            // Get user name from the user list
            const userElement = document.querySelector(`[data-id="${receiver_id}"]`);
            if (userElement) {
                const userName = userElement.querySelector('.user-name').textContent;
                if (userName && userName !== 'User') {
                    currentRecipientName = userName;
                    updateChatHeader();
                }
            }
            resolve();
        });
    }

    // Fetch messages function
    function fetchMessages() {
        if (!receiver_id) return;
        
        messagesContainer.innerHTML = '<div class="text-center py-4"><div class="loading-spinner"></div><div class="mt-2 text-muted">Loading messages...</div></div>';
        
        firebase.database().ref(`hidden_messages/${authId}`).once('value')
            .then(hiddenSnapshot => {
                const hiddenMessages = hiddenSnapshot.val() || {};
                
                return firebase.database().ref('messages')
                    .orderByChild('created_at')
                    .once('value')
                    .then(snapshot => {
                        const allMessages = snapshot.val() || {};
                        
                        const messages = Object.values(allMessages).filter(msg => 
                            ((msg.sender_id == authId && msg.receiver_id == receiver_id) || 
                            (msg.sender_id == receiver_id && msg.receiver_id == authId)) &&
                            !hiddenMessages[msg.id]
                        );
                        
                        messages.sort((a, b) => a.created_at - b.created_at);
                        displayMessages(messages);
                        
                        // Mark messages as read
                        messages.forEach(msg => {
                            if (msg.sender_id != authId && msg.status !== 'read') {
                                firebase.database().ref('messages/' + msg.id).update({
                                    status: 'read',
                                    read_at: firebase.database.ServerValue.TIMESTAMP
                                });
                            }
                        });
                        
                        // Mark as read in backend
                        fetch('/chat/mark-read', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ sender_id: receiver_id })
                        });
                    });
            })
            .catch(error => {
                console.error("Error fetching messages:", error);
                messagesContainer.innerHTML = '<div class="alert alert-danger m-3">Error loading messages. Please refresh.</div>';
            });
    }

    function displayMessages(messages) {
        messagesContainer.innerHTML = "";
        
        if (messages.length === 0) {
            messagesContainer.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-comments"></i>
                    <h5>No messages yet</h5>
                    <p class="text-muted">Start the conversation!</p>
                </div>`;
            return;
        }
        
        messages.forEach((msg, index) => {
            const isOwnMessage = msg.sender_id == authId;
            const messageClass = isOwnMessage ? 'sent' : 'received';
            const time = new Date(msg.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
            
            const messageMenu = (isOwnMessage && !msg.deleted) ? 
                `<div class="message-menu">
                    <button class="message-menu-btn" onclick="showMessageMenu(event, '${msg.id}')">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                </div>` : '';
            
            const deletedClass = msg.deleted ? 'deleted-message' : '';
            
            const messageHTML = `
                <div class="message-group">
                    <div class="message ${messageClass} ${deletedClass}" data-id="${msg.id}">
                        ${messageMenu}
                        <div class="message-content">${msg.message}</div>
                        <div class="message-time">
                            ${time} ${isOwnMessage ? '<i class="fas fa-check"></i>' : ''}
                        </div>
                    </div>
                </div>`;
            
            messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        });
        
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }

    function appendMessageToChat(messageData) {
        if (!messagesContainer) return;
        
        const emptyStateInChat = messagesContainer.querySelector('.empty-state');
        if (emptyStateInChat) {
            emptyStateInChat.remove();
        }
        
        const isOwnMessage = messageData.sender_id == authId;
        const messageClass = isOwnMessage ? 'sent' : 'received';
        const time = new Date(messageData.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        
        const messageHTML = `
            <div class="message-group">
                <div class="message ${messageClass}" data-id="${messageData.id}">
                    <div class="message-content">${messageData.message}</div>
                    <div class="message-time">
                        ${time} ${isOwnMessage ? '<i class="fas fa-check"></i>' : ''}
                    </div>
                </div>
            </div>`;
        
        messagesContainer.insertAdjacentHTML('beforeend', messageHTML);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
        
        if (messageData.sender_id == receiver_id && messageData.status !== 'read') {
            firebase.database().ref('messages/' + messageData.id).update({
                status: 'read',
                read_at: firebase.database.ServerValue.TIMESTAMP
            });
        }
    }

    // Typing indicator setup
    function setupTypingIndicator() {
        if (!receiver_id) return;
        
        firebase.database().ref('typing/' + receiver_id + '_' + authId).on('value', (snapshot) => {
            const data = snapshot.val();
            
            if (data && data.isTyping && Date.now() - data.timestamp < 5000) {
                typingIndicator.innerHTML = `${currentRecipientName} is typing<span class="typing-dots"><span></span><span></span><span></span></span>`;
            } else {
                typingIndicator.innerHTML = '';
            }
        });
    }

    // Message menu functions
    window.showMessageMenu = function(event, messageId) {
        event.stopPropagation();
        
        const existingMenu = document.querySelector('.message-menu-popup');
        if (existingMenu) {
            existingMenu.remove();
        }
        
        const menuElement = document.createElement('div');
        menuElement.className = 'message-menu-popup';
        menuElement.innerHTML = `
            <button class="menu-item delete" onclick="deleteMessage('${messageId}', 'self')">
                <i class="fas fa-trash-alt"></i> Delete for me
            </button>
            <button class="menu-item delete" onclick="deleteMessage('${messageId}', 'everyone')">
                <i class="fas fa-trash"></i> Delete for everyone
            </button>
        `;
        
        const button = event.currentTarget;
        button.parentNode.appendChild(menuElement);
        
        document.addEventListener('click', function closeMenu(e) {
            if (!menuElement.contains(e.target) && e.target !== button) {
                menuElement.remove();
                document.removeEventListener('click', closeMenu);
            }
        });
    };

    window.deleteMessage = function(messageId, deleteType) {
        const confirmMessage = deleteType === 'everyone' 
            ? 'Are you sure you want to delete this message for everyone?' 
            : 'Are you sure you want to delete this message?';
            
        if (confirm(confirmMessage)) {
            const messageRef = firebase.database().ref('messages/' + messageId);
            
            messageRef.once('value').then(snapshot => {
                const messageData = snapshot.val();
                if (!messageData) return;
                
                if (deleteType === 'everyone') {
                    return messageRef.update({
                        message: '⚠️ This message was deleted',
                        deleted: true,
                        deleted_by: authId,
                        deleted_at: firebase.database.ServerValue.TIMESTAMP,
                        original_message: messageData.message
                    });
                } else {
                    return firebase.database().ref(`hidden_messages/${authId}/${messageId}`).set({
                        hidden_at: firebase.database.ServerValue.TIMESTAMP
                    });
                }
            }).then(() => {
                return fetch('/chat/delete-message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ 
                        message_id: messageId,
                        delete_type: deleteType
                    })
                });
            }).then(() => {
                fetchMessages();
            }).catch(error => {
                console.error('Error deleting message:', error);
            });
        }
    };

    // Search functionality
    // Search functionality
    document.getElementById('user-search').addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        document.querySelectorAll('.user-item').forEach(item => {
            const userName = item.querySelector('.user-name').textContent.toLowerCase();
            const userPreview = item.querySelector('.user-preview').textContent.toLowerCase();
            
            if (userName.includes(searchTerm) || userPreview.includes(searchTerm)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    });

    // Real-time message handling functions
    function setupMessageListener() {
        if (!firebase.apps.length) {
            console.error('Firebase not initialized');
            return;
        }
        
        const db = firebase.database();
        const messagesRef = db.ref('messages');
        
        let lastMessageTimestamp = Date.now();
        
        messagesRef.on('child_added', (snapshot) => {
            const data = snapshot.val();
            if (!data) return;
            
            // Only process new messages (not historical ones)
            if (data.created_at <= lastMessageTimestamp) {
                return;
            }
            
            if (data.sender_id == authId || data.receiver_id == authId) {
                handleIncomingMessage(data);
            }
        });
        
        messagesRef.on('child_changed', (snapshot) => {
            const data = snapshot.val();
            if (!data) return;
            
            if (data.sender_id == authId || data.receiver_id == authId) {
                handleMessageUpdate(data);
            }
        });
    }

    function handleIncomingMessage(messageData) {
        // Only process messages that are less than 5 seconds old
        if (Date.now() - messageData.created_at > 5000) {
            return;
        }
        
        const isForCurrentChat = (
            (messageData.sender_id == receiver_id && messageData.receiver_id == authId) ||
            (messageData.sender_id == authId && messageData.receiver_id == receiver_id)
        );
        
        if (isForCurrentChat && receiver_id) {
            // Only append if it's from the other person (not from me)
            if (messageData.sender_id == receiver_id) {
                // Check if message already exists
                const existingMessage = document.querySelector(`[data-id="${messageData.id}"]`);
                if (!existingMessage) {
                    appendMessageToChat(messageData);
                    playNotificationSound();
                }
            }
        } else if (messageData.receiver_id == authId) {
            updateUnreadBadge(messageData.sender_id);
        }
        
        updateUserListPreview(messageData);
    }

    function handleMessageUpdate(messageData) {
        const messageElement = document.querySelector(`[data-id="${messageData.id}"]`);
        if (messageElement) {
            const contentElement = messageElement.querySelector('.message-content');
            if (contentElement) {
                contentElement.textContent = messageData.message;
            }
            
            if (messageData.deleted) {
                messageElement.classList.add('deleted-message');
            }
        }
    }

    function playNotificationSound() {
        try {
            const audio = new Audio('/sounds/notification.mp3');
            audio.play().catch(e => console.log('Could not play notification sound:', e));
        } catch (e) {
            console.log('Notification sound not available');
        }
    }

    function updateUnreadBadge(senderId) {
        const badge = document.getElementById(`unread-${senderId}`);
        if (badge) {
            const currentCount = badge.textContent ? parseInt(badge.textContent) : 0;
            badge.textContent = isNaN(currentCount) ? 1 : currentCount + 1;
        }
    }

    function updateUserListPreview(messageData) {
        const otherUserId = messageData.sender_id == authId ? messageData.receiver_id : messageData.sender_id;
        const userElement = document.querySelector(`[data-id="${otherUserId}"]`);
        
        if (userElement) {
            const previewElement = userElement.querySelector('.user-preview');
            if (previewElement) {
                const preview = messageData.message.length > 30 ? 
                    messageData.message.substring(0, 30) + '...' : 
                    messageData.message;
                previewElement.textContent = preview;
            }
            
            const timeElement = userElement.querySelector('.user-time');
            if (timeElement) {
                const time = new Date(messageData.created_at).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                timeElement.textContent = time;
            }
        }
    }

    function loadUnreadCounts() {
        fetch('/chat/unread-counts')
            .then(res => res.json())
            .then(counts => {
                Object.keys(counts).forEach(userId => {
                    const badge = document.getElementById(`unread-${userId}`);
                    if (badge && counts[userId] > 0) {
                        badge.textContent = counts[userId];
                    }
                });
            })
            .catch(error => {
                console.error('Failed to load unread counts:', error);
            });
    }

    // Initialize chat system
    document.addEventListener('DOMContentLoaded', function() {
        try {
            // Check Firebase
            if (typeof firebase === 'undefined' || !firebase.apps.length) {
                console.error('Firebase not available');
                usersList.innerHTML = '<div class="alert alert-danger m-3">Firebase not loaded. Please refresh the page.</div>';
                return;
            }
            
            console.log('Initializing chat system...');
            
            // Setup presence
            setupPresenceSystem();
            
            // Load users and initialize
            loadUsers()
                .then(() => {
                    console.log('Users loaded');
                    setupMessageListener();
                    loadUnreadCounts();
                    initializeChurchFeatures();
                    console.log('Chat system ready');
                })
                .catch(error => {
                    console.error('Initialization error:', error);
                    usersList.innerHTML = '<div class="alert alert-danger m-3">Failed to load chat. Please refresh.</div>';
                });
                
        } catch (error) {
            console.error('Critical error:', error);
            alert('Chat system failed. Please refresh the page.');
        }
    });

    // Handle window resize
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768) {
            hideMobileUsers();
        }
    });

    // Clear typing indicator when leaving page
    window.addEventListener('beforeunload', () => {
        if (receiver_id && firebase.database) {
            firebase.database().ref('typing/' + authId + '_' + receiver_id).set({
                isTyping: false,
                timestamp: firebase.database.ServerValue.TIMESTAMP
            });
        }
    });
