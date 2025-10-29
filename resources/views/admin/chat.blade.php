@extends('admin.dashboard')

@section('content')
<!-- Add Pusher configuration -->
<script>
    window.pusherKey = "{{ config('broadcasting.connections.pusher.key') }}";
    window.pusherCluster = "{{ config('broadcasting.connections.pusher.options.cluster') }}";
    window.authId = {{ Auth::id() ?? 'null' }};
</script>
<!-- Add word filter script -->
<script src="/js/word-filter.js"></script>
<link rel="stylesheet" href="/css/chat.css">

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Messages</h4>
                <button class="btn btn-outline-primary mobile-only mobile-users-btn" id="mobileUsersBtn">
                    <i class="fas fa-users"></i> Conversations
                </button>
            </div>
            
            <div class="chat-container">
                <!-- Mobile Overlay -->
                <div class="mobile-overlay" id="mobileOverlay"></div>
                
                <div class="row g-0 h-100">
                    <!-- Users Section -->
                    <div class="col-md-4 col-lg-3 users-section" id="usersSection">
                        <div class="users-header">
                            <h6>Conversations</h6>
                            <div class="input-group">
                                <input type="text" class="form-control" id="user-search" placeholder="Search conversations...">
                            </div>
                        </div>
                        
                        <div class="users-list" id="usersList">
                            <!-- Users will be loaded dynamically -->
                            <div class="text-center py-4">
                                <div class="loading-spinner"></div>
                                <div class="mt-2 text-muted">Loading conversations...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Chat Section -->
                    <div class="col-md-8 col-lg-9 chat-section">
                        <!-- Empty State -->
                        <div id="emptyState" class="empty-state">
                            <i class="fas fa-comments"></i>
                            <h5>Select a conversation</h5>
                            <p class="text-muted">Choose a contact to start messaging</p>
                        </div>
                        
                        <!-- Chat Content -->
                        <div id="chatContent" class="d-none h-100 d-flex flex-column">
                            <!-- Chat Header -->
                            <div class="chat-header">
                                <button class="mobile-back-btn mobile-only action-btn me-2" id="mobileBackBtn">
                                    <i class="fas fa-arrow-left"></i>
                                </button>
                                
                                <div class="chat-user-info">
                                    <div class="chat-avatar" id="chatAvatar">
                                        U
                                    </div>
                                    <div class="chat-user-details">
                                        <h5 id="chatUserName">User Name</h5>
                                        <p class="chat-user-status" id="chatUserStatus">Offline</p>
                                    </div>
                                </div>
                                
                                <div class="chat-actions">
                                    <button class="action-btn" title="More Info">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Messages Container -->
                            <div class="messages-container" id="messagesContainer">
                                <!-- Messages will be loaded here -->
                            </div>
                            
                            <!-- Typing Indicator -->
                            <div class="typing-indicator" id="typingIndicator"></div>
                            
                            <!-- Message Input -->
                            <div class="message-input-section">
                                <div class="input-container">
                                    <textarea 
                                        class="message-input" 
                                        id="messageInput" 
                                        placeholder="Type a message..." 
                                        rows="1"
                                    ></textarea>
                                    
                                    <div class="input-actions">
                                        <button class="input-btn send-btn" id="sendBtn" title="Send message">
                                            <i class="fas fa-paper-plane"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/js/chat.js"></script>
@endsection