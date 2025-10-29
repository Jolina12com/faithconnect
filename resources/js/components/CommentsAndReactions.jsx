import React, { useState, useEffect, useRef } from 'react';
import { DataPacket_Kind } from 'livekit-client';
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { 
    faCommentAlt, 
    faTimesCircle, 
    faPaperPlane, 
    faSmile, 
    faThumbsUp, 
    faHeart, 
    faLaugh, 
    faSurprise, 
    faSadTear,
    faChevronDown,
    faBell,
    faTrash,
    faEllipsisH
} from '@fortawesome/free-solid-svg-icons';

const REACTION_TYPES = {
    LIKE: { icon: faThumbsUp, emoji: '👍', color: '#4267B2' },
    LOVE: { icon: faHeart, emoji: '❤️', color: '#E41B17' },
    LAUGH: { icon: faLaugh, emoji: '😂', color: '#FFD700' },
    WOW: { icon: faSurprise, emoji: '😮', color: '#FF9500' },
    SAD: { icon: faSadTear, emoji: '😢', color: '#1E90FF' }
};

// Helper function for relative time
const getRelativeTime = (timestamp) => {
    const now = new Date();
    const commentTime = new Date(timestamp);
    const diffInSeconds = Math.floor((now - commentTime) / 1000);
    
    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 120) return '1m ago';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + 'm ago';
    if (diffInSeconds < 7200) return '1h ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + 'h ago';
    
    return commentTime.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

const CommentsAndReactions = ({ room, username, isBroadcaster = false, streamId }) => {
    const [isOpen, setIsOpen] = useState(false);
    const [comments, setComments] = useState([]);
    const [newComment, setNewComment] = useState('');
    const [reactions, setReactions] = useState([]);
    const [unreadCount, setUnreadCount] = useState(0);
    const [lastMessagePreview, setLastMessagePreview] = useState('');
    const [showScrollButton, setShowScrollButton] = useState(false);
    const [startY, setStartY] = useState(null);
    const [currentY, setCurrentY] = useState(null);
    const [isDragging, setIsDragging] = useState(false);
    const commentsRef = useRef(null);
    const panelRef = useRef(null);
    const prevStreamIdRef = useRef(null);
    
    // Reset comments when stream ID changes
    useEffect(() => {
        if (streamId && streamId !== prevStreamIdRef.current) {
            console.log('Stream ID changed, resetting comments. New stream ID:', streamId);
            setComments([]);
            setReactions([]);
            setUnreadCount(0);
            setLastMessagePreview('');
            
            // Save the new stream ID
            prevStreamIdRef.current = streamId;
            
            // Clear stored comments for this stream
            localStorage.removeItem('stream_comments');
            
            // Add system message
            const systemMessage = {
                id: Date.now(),
                text: 'Welcome! Be gentle with the comments, we are all in this together!',
                username: 'System',
                timestamp: new Date().toISOString(),
                isSystem: true
            };
            
            setComments([systemMessage]);
        } else if (!streamId && prevStreamIdRef.current) {
            console.log('Stream ID was removed, keeping existing comments');
        }
    }, [streamId]);
    
    // Touch handlers for draggable behavior
    const handleTouchStart = (e) => {
        setStartY(e.touches[0].clientY);
        setIsDragging(true);
    };
    
    const handleTouchMove = (e) => {
        if (!isDragging) return;
        setCurrentY(e.touches[0].clientY);
        
        const deltaY = currentY - startY;
        
        // Only allow dragging down
        if (deltaY > 0 && panelRef.current) {
            panelRef.current.style.transform = `translateY(${deltaY}px)`;
            panelRef.current.style.transition = 'none';
        }
    };
    
    const handleTouchEnd = () => {
        if (!isDragging || !currentY || !startY || !panelRef.current) {
            setIsDragging(false);
            return;
        }
        
        const deltaY = currentY - startY;
        
        // Reset the transform style
        panelRef.current.style.transition = 'transform 0.3s ease';
        
        // If dragged down far enough, close the panel
        if (deltaY > 100) {
            panelRef.current.style.transform = 'translateY(100%)';
            setTimeout(() => {
                setIsOpen(false);
                panelRef.current.style.transform = '';
            }, 300);
        } else {
            // Otherwise snap back
            panelRef.current.style.transform = '';
        }
        
        setIsDragging(false);
        setStartY(null);
        setCurrentY(null);
    };
    
    // Auto-scroll to the latest comment
    useEffect(() => {
        if (commentsRef.current) {
            const isScrolledToBottom = commentsRef.current.scrollHeight - commentsRef.current.clientHeight <= commentsRef.current.scrollTop + 50;
            
            if (isScrolledToBottom) {
                commentsRef.current.scrollTop = commentsRef.current.scrollHeight;
            } else {
                setShowScrollButton(true);
            }
            
            // If not open, increment unread count and update preview
            if (!isOpen && comments.length > 0) {
                setUnreadCount(prev => prev + 1);
                const latestComment = comments[comments.length - 1];
                setLastMessagePreview(`${latestComment.username}: ${latestComment.text.substring(0, 20)}${latestComment.text.length > 20 ? '...' : ''}`);
                
                // Show preview briefly
                const previewTimeout = setTimeout(() => {
                    setLastMessagePreview('');
                }, 5000);
                
                return () => clearTimeout(previewTimeout);
            }
        }
    }, [comments, isOpen]);
    
    // Reset unread count when opening panel
    useEffect(() => {
        if (isOpen) {
            setUnreadCount(0);
            setLastMessagePreview('');
        }
    }, [isOpen]);
    
    // Handle scroll events to show/hide scroll button
    const handleScroll = () => {
        if (commentsRef.current) {
            const isScrolledToBottom = commentsRef.current.scrollHeight - commentsRef.current.clientHeight <= commentsRef.current.scrollTop + 50;
            setShowScrollButton(!isScrolledToBottom);
        }
    };
    
    // Scroll to bottom function
    const scrollToBottom = () => {
        if (commentsRef.current) {
            commentsRef.current.scrollTop = commentsRef.current.scrollHeight;
            setShowScrollButton(false);
        }
    };
    
    // Persist messages to localStorage
    useEffect(() => {
        // Save comments to localStorage when updated
        if (comments.length > 0) {
            try {
                localStorage.setItem('stream_comments', JSON.stringify(comments.slice(-50))); // Store last 50 comments
            } catch (e) {
                console.error('Error saving comments to localStorage:', e);
            }
        }
    }, [comments]);
    
    // Load persisted messages on mount
    useEffect(() => {
        try {
            const savedComments = localStorage.getItem('stream_comments');
            if (savedComments) {
                setComments(JSON.parse(savedComments));
            }
        } catch (e) {
            console.error('Error loading comments from localStorage:', e);
        }
    }, []);
    
    // Set up data channel and event listeners
    useEffect(() => {
        if (!room) return;
        
        console.log('Setting up data channel for room:', room.name);
        if (streamId) {
            console.log('Current stream ID for comments:', streamId);
        } else {
            console.log('No stream ID available for comments');
        }
        
        // Handle incoming data
        const handleData = (payload, participant) => {
            try {
                const data = JSON.parse(new TextDecoder().decode(payload));
                console.log('Received data:', data.type, 'from:', participant.identity);
                
                if (data.type === 'comment') {
                    // Check if this is our own message coming back from the server
                    const isDuplicate = comments.some(comment => 
                        // Check if we already have this message by ID (for new messages that include ID)
                        (data.commentId && comment.id === data.commentId) || 
                        // Or by exact match of content, username and very close timestamp (for backward compatibility)
                        (comment.text === data.text && 
                         comment.username === data.username && 
                         comment.isLocalSender === true)
                    );
                    
                    // Only add the comment if it's not a duplicate
                    if (!isDuplicate) {
                        setComments(prev => [...prev, {
                            id: data.commentId || Date.now(),
                            text: data.text,
                            username: data.username,
                            timestamp: new Date().toISOString(),
                            isBroadcaster: data.isBroadcaster
                        }]);
                    }
                }
                
                if (data.type === 'reaction') {
                    // Add reaction with animation
                    const newReaction = {
                        id: Date.now(),
                        type: data.reactionType,
                        username: data.username,
                        x: Math.random() * 80 + 10, // Random horizontal position (10-90%)
                    };
                    
                    setReactions(prev => [...prev, newReaction]);
                    
                    // Remove reaction after animation completes
                    setTimeout(() => {
                        setReactions(prev => prev.filter(r => r.id !== newReaction.id));
                    }, 3000);
                }
                
                // Handle clear comments command
                if (data.type === 'clear_comments') {
                    // Clear comments
                    setComments([]);
                    localStorage.removeItem('stream_comments');
                    
                    // Add system message
                    const systemMessage = {
                        id: Date.now(),
                        text: 'Comments have been cleared by the broadcaster',
                        username: 'System',
                        timestamp: new Date().toISOString(),
                        isSystem: true
                    };
                    
                    setComments([systemMessage]);
                }
            } catch (e) {
                console.error('Error parsing data:', e);
            }
        };
        
        // Subscribe to data messages
        room.on('dataReceived', handleData);
        
        // Clean up
        return () => {
            room.off('dataReceived', handleData);
        };
    }, [room]);
    
    // Send comment
    const sendComment = () => {
        if (!newComment.trim() || !room) return;
        
        // Create the comment object
        const commentText = newComment.trim();
        const timestamp = new Date().toISOString();
        const commentId = Date.now();
        
        // Create local comment object to display immediately
        const localComment = {
            id: commentId,
            text: commentText,
            username: username || 'Anonymous',
            timestamp: timestamp,
            isBroadcaster: isBroadcaster,
            isLocalSender: true // Mark as locally sent
        };
        
        // Add to local state immediately for instant feedback
        setComments(prev => [...prev, localComment]);
        
        // Create the data object to send over LiveKit
        const comment = {
            type: 'comment',
            text: commentText,
            username: username || 'Anonymous',
            isBroadcaster,
            commentId // Include ID to prevent duplicates
        };
        
        // Encode and send the data
        const encoder = new TextEncoder();
        const data = encoder.encode(JSON.stringify(comment));
        
        room.localParticipant.publishData(data, DataPacket_Kind.RELIABLE);
        
        // Clear the input field
        setNewComment('');
        
        // Scroll to bottom to show the new comment
        setTimeout(scrollToBottom, 50);
    };
    
    // Send reaction
    const sendReaction = (reactionType) => {
        if (!room) return;
        
        const reaction = {
            type: 'reaction',
            reactionType,
            username: username || 'Anonymous'
        };
        
        const encoder = new TextEncoder();
        const data = encoder.encode(JSON.stringify(reaction));
        
        room.localParticipant.publishData(data, DataPacket_Kind.LOSSY);
    };
    
    // Handle input key press
    const handleKeyPress = (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendComment();
        }
    };
    
    // Add clear comments function (for broadcasters only)
    const clearComments = () => {
        if (!isBroadcaster || !room) return;
        
        // Send clear command to all viewers
        const clearCommand = {
            type: 'clear_comments',
            timestamp: new Date().toISOString()
        };
        
        const encoder = new TextEncoder();
        const data = encoder.encode(JSON.stringify(clearCommand));
        
        room.localParticipant.publishData(data, DataPacket_Kind.RELIABLE);
        
        // Clear local comments
        setComments([]);
        localStorage.removeItem('stream_comments');
        
        // Add system message
        const systemMessage = {
            id: Date.now(),
            text: 'Comments have been cleared by the broadcaster',
            username: 'System',
            timestamp: new Date().toISOString(),
            isSystem: true
        };
        
        setComments([systemMessage]);
    };
    
    return (
        <>
            {/* Floating reactions */}
            <div className="reactions-container">
                {reactions.map(reaction => (
                    <div 
                        key={reaction.id}
                        className="floating-reaction"
                        style={{ left: `${reaction.x}%` }}
                    >
                        <div className="reaction-emoji">{REACTION_TYPES[reaction.type].emoji}</div>
                    </div>
                ))}
            </div>
            
            {/* Message preview */}
            {lastMessagePreview && !isOpen && (
                <div className="message-preview">
                    <div className="preview-avatar">{lastMessagePreview[0]}</div>
                    <div className="preview-content">{lastMessagePreview}</div>
                </div>
            )}
            
            {/* Toggle button */}
            <button 
                className={`comments-toggle ${isOpen ? 'active' : ''}`} 
                onClick={() => setIsOpen(!isOpen)}
                title={isOpen ? "Close comments" : "Open comments"}
                style={{ display: isOpen ? 'none' : 'flex' }} /* Hide when chat is open */
            >
                <FontAwesomeIcon icon={faCommentAlt} />
                {unreadCount > 0 && !isOpen && (
                    <span className="unread-badge">{unreadCount > 99 ? '99+' : unreadCount}</span>
                )}
            </button>
            
            {/* Comments panel */}
            <div 
                className={`comments-panel ${isOpen ? 'open' : ''}`}
                ref={panelRef}
            >
                <div 
                    className="comments-header"
                    onTouchStart={handleTouchStart}
                    onTouchMove={handleTouchMove}
                    onTouchEnd={handleTouchEnd}
                >
                    <div className="drag-indicator"></div>
                    <h3>
                        <div className="header-title">
                            <span className="chat-icon">💬</span> Live Chat
                            <span className="viewers-count">{room?.participants?.size || 0} viewers</span>
                        </div>
                        <div className="header-controls">
                            {isBroadcaster && (
                                <button 
                                    className="clear-comments-button" 
                                    onClick={clearComments}
                                    title="Clear all comments"
                                >
                                    <FontAwesomeIcon icon={faTrash} />
                                </button>
                            )}
                            <button 
                                className="close-button"
                                onClick={() => setIsOpen(false)}
                                title="Close chat"
                            >
                                <FontAwesomeIcon icon={faTimesCircle} />
                            </button>
                        </div>
                    </h3>
                </div>
                
                <div 
                    className="comments-list" 
                    ref={commentsRef}
                    onScroll={handleScroll}
                >
                    {comments.length === 0 ? (
                        <div className="no-comments">
                            <div className="no-comments-icon">💬</div>
                            <p>No comments yet</p>
                            <p className="no-comments-subtext">Be the first to say something!</p>
                        </div>
                    ) : (
                        comments.map((comment, index) => (
                            <div 
                                key={comment.id} 
                                className={`comment ${comment.isBroadcaster ? 'broadcaster-comment' : ''} ${comment.isSystem ? 'system-message' : ''} ${comment.isLocalSender ? 'my-comment' : ''}`}
                            >
                                {!comment.isSystem && (
                                    <div className="comment-avatar">
                                        {comment.username[0].toUpperCase()}
                                    </div>
                                )}
                                <div className="comment-content">
                                    <div className="comment-bubble">
                                        {!comment.isSystem && (
                                            <div className="comment-username">
                                                {comment.isBroadcaster ? '🎬 ' : ''}{comment.isLocalSender ? `${comment.username} (You)` : comment.username}
                                            </div>
                                        )}
                                        <div className="comment-text">{comment.text}</div>
                                    </div>
                                    <div className="comment-meta">
                                        <span className="comment-time">
                                            {getRelativeTime(comment.timestamp)}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                    
                    {showScrollButton && (
                        <button 
                            className="scroll-bottom-button"
                            onClick={scrollToBottom}
                            title="Scroll to latest messages"
                        >
                            <FontAwesomeIcon icon={faChevronDown} />
                            <span>New messages</span>
                        </button>
                    )}
                </div>
                
                <div className="reactions-bar">
                    {Object.entries(REACTION_TYPES).map(([type, { icon, color, emoji }]) => (
                        <button 
                            key={type}
                            className="reaction-button"
                            onClick={() => sendReaction(type)}
                            style={{ color }}
                            title={`Send ${type.toLowerCase()} reaction`}
                        >
                            <div className="reaction-emoji">{emoji}</div>
                        </button>
                    ))}
                </div>
                
                <div className="comment-input-container">
                    <div className="input-wrapper">
                        <textarea
                            value={newComment}
                            onChange={(e) => setNewComment(e.target.value)}
                            onKeyPress={handleKeyPress}
                            placeholder="Write a comment..."
                            className="comment-input"
                            maxLength={200}
                        />
                    </div>
                    <button 
                        className="send-button"
                        onClick={sendComment}
                        disabled={!newComment.trim()}
                        aria-label="Send message"
                    >
                        <FontAwesomeIcon icon={faPaperPlane} />
                    </button>
                </div>
            </div>
            
            <style>
                {`
                    .comments-toggle {
                        position: fixed;
                        bottom: 20px;
                        right: 20px;
                        width: 50px;
                        height: 50px;
                        border-radius: 50%;
                        background: #1877F2;
                        color: white;
                        border: none;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.3);
                        font-size: 1.2rem;
                        cursor: pointer;
                        z-index: 1000;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        transition: all 0.2s ease;
                    }
                    
                    .comments-toggle:hover {
                        transform: scale(1.05);
                        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
                    }
                    
                    .comments-toggle.active {
                        background: #606770;
                    }
                    
                    .unread-badge {
                        position: absolute;
                        top: -5px;
                        right: -5px;
                        background: #FF3B30;
                        color: white;
                        border-radius: 20px;
                        padding: 2px 6px;
                        min-width: 20px;
                        height: 20px;
                        font-size: 0.7rem;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        border: 2px solid white;
                        font-weight: bold;
                    }
                    
                    .message-preview {
                        position: fixed;
                        bottom: 80px;
                        right: 20px;
                        background: white;
                        color: #1C1E21;
                        padding: 8px;
                        border-radius: 18px;
                        max-width: 280px;
                        font-size: 0.9rem;
                        z-index: 999;
                        box-shadow: 0 2px 12px rgba(0,0,0,0.15);
                        animation: popIn 0.3s ease-out;
                        display: flex;
                        align-items: center;
                    }
                    
                    .preview-avatar {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        background: #1877F2;
                        color: white;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                        margin-right: 8px;
                    }
                    
                    .preview-content {
                        flex: 1;
                        padding-right: 5px;
                    }
                    
                    .comments-panel {
                        position: fixed;
                        bottom: -800px;
                        right: 20px;
                        width: 340px;
                        height: 500px;
                        max-height: 80vh;
                        background: white;
                        border-radius: 8px;
                        box-shadow: 0 2px 20px rgba(0,0,0,0.2);
                        z-index: 999;
                        display: flex;
                        flex-direction: column;
                        transition: bottom 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                    }
                    
                    .comments-panel.open {
                        bottom: 20px;
                    }
                    
                    .comments-header {
                        padding: 12px 16px;
                        border-bottom: 1px solid #E4E6EB;
                        background: white;
                        border-radius: 8px 8px 0 0;
                        position: relative;
                        cursor: grab;
                        user-select: none;
                    }
                    
                    .drag-indicator {
                        width: 40px;
                        height: 4px;
                        background: #CED0D4;
                        border-radius: 4px;
                        position: absolute;
                        top: 6px;
                        left: 50%;
                        transform: translateX(-50%);
                    }
                    
                    .comments-header h3 {
                        margin: 8px 0 0;
                        color: #1C1E21;
                        font-size: 1rem;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        width: 100%;
                    }
                    
                    .header-title {
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        font-weight: 600;
                    }
                    
                    .chat-icon {
                        font-size: 1.2rem;
                    }
                    
                    .viewers-count {
                        font-size: 0.8rem;
                        color: #65676B;
                        font-weight: normal;
                        margin-left: 8px;
                    }
                    
                    .header-controls {
                        display: flex;
                        gap: 8px;
                    }
                    
                    .clear-comments-button, .close-button {
                        background: #E4E6EB;
                        border: none;
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        color: #606770;
                        transition: all 0.2s;
                    }
                    
                    .clear-comments-button:hover, .close-button:hover {
                        background: #D8DADF;
                    }
                    
                    .system-message {
                        margin: 10px auto;
                        text-align: center;
                        max-width: 85%;
                    }
                    
                    .system-message .comment-bubble {
                        background: #F0F2F5;
                        border: 1px solid #CED0D4;
                        color: #65676B;
                        font-size: 0.9rem;
                        padding: 8px 12px;
                        border-radius: 18px;
                        display: inline-block;
                    }
                    
                    .comments-list {
                        flex: 1;
                        overflow-y: auto;
                        padding: 16px;
                        display: flex;
                        flex-direction: column;
                        gap: 12px;
                        background: white;
                        scroll-behavior: smooth;
                        position: relative;
                    }
                    
                    .no-comments {
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        justify-content: center;
                        height: 100%;
                        color: #65676B;
                        text-align: center;
                    }
                    
                    .no-comments-icon {
                        font-size: 2.5rem;
                        margin-bottom: 10px;
                        opacity: 0.7;
                    }
                    
                    .no-comments p {
                        margin: 5px 0;
                    }
                    
                    .no-comments-subtext {
                        font-size: 0.85rem;
                        opacity: 0.7;
                    }
                    
                    .comment {
                        display: flex;
                        align-items: flex-start;
                        gap: 8px;
                        animation: fadeIn 0.3s ease;
                        margin-bottom: 8px;
                    }
                    
                    .comment-avatar {
                        width: 32px;
                        height: 32px;
                        border-radius: 50%;
                        background: #E4E6EB;
                        color: #606770;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-weight: bold;
                        flex-shrink: 0;
                    }
                    
                    .broadcaster-comment .comment-avatar {
                        background: #1877F2;
                        color: white;
                    }
                    
                    .comment-content {
                        display: flex;
                        flex-direction: column;
                        gap: 2px;
                        max-width: calc(100% - 40px);
                    }
                    
                    .comment-bubble {
                        background: #F0F2F5;
                        padding: 8px 12px;
                        border-radius: 18px;
                        position: relative;
                    }
                    
                    .broadcaster-comment .comment-bubble {
                        background: #E7F3FF;
                    }
                    
                    /* Style for the user's own comments */
                    .my-comment .comment-bubble {
                        background: #E3F2FD; /* Light blue background */
                        border-left: 3px solid #2196F3; /* Blue accent on the left */
                    }
                    
                    .my-comment .comment-username {
                        color: #2196F3; /* Blue color for the username */
                        font-weight: bold;
                    }
                    
                    .comment-username {
                        font-weight: 600;
                        font-size: 0.85rem;
                        margin-bottom: 2px;
                        color: #050505;
                    }
                    
                    .broadcaster-comment .comment-username {
                        color: #1877F2;
                    }
                    
                    .comment-text {
                        font-size: 0.95rem;
                        line-height: 1.3;
                        word-break: break-word;
                        color: #050505;
                    }
                    
                    .comment-meta {
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        padding-left: 12px;
                    }
                    
                    .comment-time {
                        font-size: 0.75rem;
                        color: #65676B;
                    }
                    
                    .scroll-bottom-button {
                        position: absolute;
                        bottom: 10px;
                        left: 50%;
                        transform: translateX(-50%);
                        border: none;
                        padding: 8px 16px;
                        border-radius: 20px;
                        background: #1877F2;
                        color: white;
                        font-weight: 500;
                        font-size: 0.85rem;
                        display: flex;
                        align-items: center;
                        gap: 6px;
                        cursor: pointer;
                        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
                        z-index: 10;
                        transition: all 0.2s ease;
                    }
                    
                    .scroll-bottom-button:hover {
                        background: #166FE5;
                        transform: translateX(-50%) scale(1.05);
                    }
                    
                    .reactions-bar {
                        display: flex;
                        justify-content: space-around;
                        padding: 8px 16px;
                        border-top: 1px solid #E4E6EB;
                        background: white;
                    }
                    
                    .reaction-button {
                        background: none;
                        border: none;
                        cursor: pointer;
                        padding: 6px;
                        border-radius: 50%;
                        transition: all 0.2s ease;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }
                    
                    .reaction-emoji {
                        font-size: 1.4rem;
                        filter: grayscale(0.3);
                        transition: all 0.2s;
                    }
                    
                    .reaction-button:hover .reaction-emoji {
                        transform: scale(1.3);
                        filter: grayscale(0);
                    }
                    
                    .comment-input-container {
                        display: flex;
                        padding: 8px 16px 16px;
                        gap: 8px;
                        background: white;
                        border-radius: 0 0 8px 8px;
                        align-items: flex-end;
                        position: relative;
                        z-index: 20; /* Higher z-index to stay on top */
                        box-shadow: 0 -2px 10px rgba(0,0,0,0.05);
                    }
                    
                    .input-wrapper {
                        flex: 1;
                        background: #F0F2F5;
                        border-radius: 20px;
                        padding: 0 12px;
                        display: flex;
                        align-items: center;
                    }
                    
                    .comment-input {
                        flex: 1;
                        padding: 10px 0;
                        border: none;
                        background: transparent;
                        color: #050505;
                        resize: none;
                        max-height: 100px;
                        font-family: inherit;
                        font-size: 0.95rem;
                    }
                    
                    .comment-input:focus {
                        outline: none;
                    }
                    
                    .comment-input::placeholder {
                        color: #65676B;
                    }
                    
                    .send-button {
                        width: 44px;
                        height: 44px;
                        border-radius: 50%;
                        background: #2196F3;
                        color: white;
                        border: none;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        cursor: pointer;
                        transition: all 0.2s ease;
                        margin-right: 4px;
                        position: relative;
                        z-index: 30;
                        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                    }
                    
                    .send-button:hover {
                        background: #166FE5;
                        transform: scale(1.05);
                    }
                    
                    .send-button:disabled {
                        background: #E4E6EB;
                        color: #BCC0C4;
                        cursor: not-allowed;
                    }
                    
                    .reactions-container {
                        position: fixed;
                        bottom: 0;
                        left: 0;
                        width: 100%;
                        height: 70vh;
                        pointer-events: none;
                        z-index: 998;
                        overflow: hidden;
                    }
                    
                    .floating-reaction {
                        position: absolute;
                        bottom: 0;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        animation: floatUp 4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
                    }
                    
                    .floating-reaction .reaction-emoji {
                        font-size: 2.5rem;
                        filter: drop-shadow(0 0 3px rgba(0,0,0,0.2));
                    }
                    
                    @keyframes floatUp {
                        0% {
                            bottom: 0;
                            opacity: 0;
                            transform: scale(0.5) rotate(-15deg);
                        }
                        10% {
                            opacity: 1;
                            transform: scale(1) rotate(10deg);
                        }
                        20% {
                            transform: scale(1.2) rotate(-5deg);
                        }
                        30% {
                            transform: scale(1) rotate(5deg);
                        }
                        80% {
                            opacity: 1;
                            transform: scale(1) rotate(0);
                        }
                        100% {
                            bottom: 100%;
                            opacity: 0;
                            transform: scale(0.8) rotate(0);
                        }
                    }
                    
                    @keyframes fadeIn {
                        from { opacity: 0; transform: translateY(10px); }
                        to { opacity: 1; transform: translateY(0); }
                    }
                    
                    @keyframes popIn {
                        0% { opacity: 0; transform: scale(0.8) translateY(10px); }
                        70% { opacity: 1; transform: scale(1.05) translateY(0); }
                        100% { opacity: 1; transform: scale(1) translateY(0); }
                    }
                    
                    /* Scrollbar styling */
                    .comments-list::-webkit-scrollbar {
                        width: 8px;
                    }
                    
                    .comments-list::-webkit-scrollbar-track {
                        background: transparent;
                    }
                    
                    .comments-list::-webkit-scrollbar-thumb {
                        background: #BCC0C4;
                        border-radius: 4px;
                        border: 2px solid white;
                    }
                    
                    .comments-list::-webkit-scrollbar-thumb:hover {
                        background: #8D949E;
                    }
                    
                    @media (max-width: 768px) {
                        .comments-panel {
                            width: 100%;
                            height: 80vh;
                            max-height: none;
                            bottom: -100vh;
                            right: 0;
                            left: 0;
                            border-radius: 12px 12px 0 0;
                            box-shadow: 0 -2px 20px rgba(0,0,0,0.2);
                        }
                        
                        .comments-panel.open {
                            bottom: 0;
                        }
                        
                        /* Ensure comments toggle doesn't show when panel is open */
                        .comments-panel.open + .comments-toggle {
                            display: none !important;
                        }
                        
                        .comments-header {
                            border-radius: 12px 12px 0 0;
                            padding: 16px;
                        }
                        
                        /* Hide the floating close button on mobile and only use the drag handle */
                        .close-button {
                            display: none; /* Simply hide it on mobile */
                        }
                        
                        /* Create more space for the message input area */
                        .comment-input-container {
                            padding: 12px 16px 20px;
                            position: relative;
                            z-index: 10;
                        }
                        
                        /* Make send button larger on mobile */
                        .send-button {
                            width: 44px;
                            height: 44px;
                            margin-right: 4px;
                            position: relative;
                            z-index: 30;
                            background: #2196F3;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                        }
                        
                        .comments-list {
                            padding: 16px;
                        }
                        
                        .reaction-button .reaction-emoji {
                            font-size: 1.8rem;
                        }
                        
                        .comments-toggle {
                            width: 56px;
                            height: 56px;
                            font-size: 1.4rem;
                        }
                        
                        .unread-badge {
                            min-width: 24px;
                            height: 24px;
                            font-size: 0.8rem;
                        }
                    }
                `}
            </style>
        </>
    );
};

export default CommentsAndReactions;