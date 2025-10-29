import React, { useState, useEffect, useRef } from 'react';
import axios from 'axios';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

const Chat = () => {
    const [users, setUsers] = useState([]);
    const [selectedUser, setSelectedUser] = useState(null);
    const [messages, setMessages] = useState([]);
    const [newMessage, setNewMessage] = useState('');
    const [typingUsers, setTypingUsers] = useState({});
    const chatBoxRef = useRef(null);
    const authId = document.querySelector('meta[name="user-id"]')?.content;

    // Initialize Pusher
    useEffect(() => {
        window.Pusher = Pusher;
        
        const echo = new Echo({
            broadcaster: 'pusher',
            key: document.querySelector('meta[name="pusher-key"]')?.content,
            cluster: document.querySelector('meta[name="pusher-cluster"]')?.content,
            forceTLS: true,
            authEndpoint: "/broadcasting/auth",
            auth: {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                }
            }
        });

        // Listen for new messages
        echo.private(`chat.${authId}`)
            .listen('.NewMessage', (e) => {
                if (e.sender_id === selectedUser?.id) {
                    fetchMessages(selectedUser.id);
                }
                updateUnreadCount(e.sender_id);
            })
            .listenForWhisper('typing', (e) => {
                if (e.sender_id === selectedUser?.id) {
                    setTypingUsers(prev => ({
                        ...prev,
                        [e.sender_id]: e.sender_name
                    }));
                    setTimeout(() => {
                        setTypingUsers(prev => {
                            const newState = { ...prev };
                            delete newState[e.sender_id];
                            return newState;
                        });
                    }, 3000);
                }
            });

        return () => {
            echo.leave(`chat.${authId}`);
        };
    }, [authId, selectedUser]);

    // Fetch users
    useEffect(() => {
        fetchUsers();
    }, []);

    // Fetch messages when user is selected
    useEffect(() => {
        if (selectedUser) {
            fetchMessages(selectedUser.id);
        }
    }, [selectedUser]);

    const fetchUsers = async () => {
        try {
            const response = await axios.get('/chat/users');
            setUsers(response.data);
        } catch (error) {
            console.error('Error fetching users:', error);
        }
    };

    const fetchMessages = async (userId) => {
        try {
            const response = await axios.get(`/chat/messages/${userId}`);
            setMessages(response.data);
            scrollToBottom();
            markMessagesAsRead(userId);
        } catch (error) {
            console.error('Error fetching messages:', error);
        }
    };

    const sendMessage = async () => {
        if (!newMessage.trim() || !selectedUser) return;

        try {
            await axios.post('/chat/send', {
                receiver_id: selectedUser.id,
                message: newMessage
            });
            setNewMessage('');
            fetchMessages(selectedUser.id);
        } catch (error) {
            console.error('Error sending message:', error);
        }
    };

    const handleTyping = () => {
        if (!selectedUser) return;
        
        window.Echo.private(`chat.${selectedUser.id}`)
            .whisper('typing', {
                sender_id: authId,
                sender_name: document.querySelector('meta[name="user-name"]')?.content
            });
    };

    const markMessagesAsRead = async (userId) => {
        try {
            await axios.post('/chat/mark-read', { sender_id: userId });
        } catch (error) {
            console.error('Error marking messages as read:', error);
        }
    };

    const updateUnreadCount = (userId) => {
        const badge = document.getElementById(`unread-${userId}`);
        if (badge) {
            const currentCount = parseInt(badge.innerText) || 0;
            badge.innerText = currentCount + 1;
        }
    };

    const scrollToBottom = () => {
        if (chatBoxRef.current) {
            chatBoxRef.current.scrollTop = chatBoxRef.current.scrollHeight;
        }
    };

    return (
        <div className="chat-container">
            <div className="row g-0">
                {/* User List */}
                <div className="col-md-4 col-lg-3 users-section">
                    <div className="users-header">
                        <div className="input-group">
                            <input
                                type="text"
                                className="form-control"
                                placeholder="Search users..."
                                onChange={(e) => {
                                    const searchTerm = e.target.value.toLowerCase();
                                    document.querySelectorAll('.user').forEach(user => {
                                        const userName = user.querySelector('.user-name').textContent.toLowerCase();
                                        user.style.display = userName.includes(searchTerm) ? 'flex' : 'none';
                                    });
                                }}
                            />
                        </div>
                    </div>
                    <ul className="list-unstyled" id="user-list">
                        {users.map(user => (
                            <li
                                key={user.id}
                                className={`user d-flex justify-content-between align-items-center ${selectedUser?.id === user.id ? 'active' : ''}`}
                                onClick={() => setSelectedUser(user)}
                            >
                                <div>
                                    <div className="user-name">{user.name}</div>
                                    <div className="user-status">
                                        <span className="online-indicator"></span>
                                        {user.online ? 'Online' : 'Offline'}
                                    </div>
                                </div>
                                <span className="unread-badge" id={`unread-${user.id}`}></span>
                            </li>
                        ))}
                    </ul>
                </div>

                {/* Chat Area */}
                <div className="col-md-8 col-lg-9 chat-section">
                    {!selectedUser ? (
                        <div className="empty-state">
                            <i className="fas fa-comments"></i>
                            <h5>Select a user to start chatting</h5>
                            <p className="text-muted">Your messages will appear here</p>
                        </div>
                    ) : (
                        <div className="chat-content">
                            <div className="chat-header">
                                <div className="d-flex align-items-center">
                                    <h5 className="recipient-name">{selectedUser.name}</h5>
                                    <span className="ms-2 badge bg-success">
                                        {selectedUser.online ? 'Online' : 'Offline'}
                                    </span>
                                </div>
                            </div>

                            <div id="chat-box" ref={chatBoxRef}>
                                {messages.map((msg, index) => (
                                    <div
                                        key={index}
                                        className={`message ${msg.sender_id === authId ? 'sent' : 'received'}`}
                                    >
                                        <div className="message-content">{msg.message}</div>
                                        <div className="timestamp">
                                            {msg.formatted_time}
                                            {msg.sender_id === authId && (
                                                <i className={`fas fa-check${msg.status === 'read' ? '-double' : ''}`}></i>
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>

                            {typingUsers[selectedUser.id] && (
                                <div className="typing-indicator">
                                    <i className="fas fa-keyboard me-1"></i>
                                    {typingUsers[selectedUser.id]} is typing...
                                </div>
                            )}

                            <div className="message-input-container">
                                <div className="input-group">
                                    <textarea
                                        className="form-control"
                                        value={newMessage}
                                        onChange={(e) => {
                                            setNewMessage(e.target.value);
                                            handleTyping();
                                        }}
                                        onKeyDown={(e) => {
                                            if (e.key === 'Enter' && !e.shiftKey) {
                                                e.preventDefault();
                                                sendMessage();
                                            }
                                        }}
                                        placeholder="Type a message..."
                                        rows="1"
                                    />
                                    <button
                                        className="btn btn-primary"
                                        onClick={sendMessage}
                                        disabled={!newMessage.trim()}
                                    >
                                        <i className="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
};

export default Chat; 