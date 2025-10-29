@extends('member.dashboard_member')
@section('title', 'Church Announcements')
@section('content')

<style>
    :root {
        --primary-color: #4a90e2;
        --primary-light: #f0f6ff;
        --primary-gradient: linear-gradient(135deg, #4a90e2 0%, #357abd 100%);
        --text-dark: #2d3748;
        --text-medium: #4a5568;
        --text-light: #718096;
        --bg-light: #f8fafc;
        --white: #ffffff;
        --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
        --shadow-md: 0 4px 6px rgba(0,0,0,0.07);
        --shadow-lg: 0 10px 15px rgba(0,0,0,0.1);
        --radius-sm: 8px;
        --radius-md: 12px;
        --radius-lg: 16px;
        --font-sans: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }

    body {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
    }

    .announcements-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
        font-family: var(--font-sans);
        position: relative;
    }



    .page-header {
        text-align: center;
        margin-bottom: 60px;
        position: relative;
        padding: 40px 0;
    }

    .page-header::before {
        content: '📢';
        font-size: 50px;
        display: block;
        margin-bottom: 15px;
    }

    .page-header::after {
        content: "";
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 60px;
        height: 3px;
        background: var(--primary-color);
        border-radius: 3px;
    }

    .page-title {
        font-size: 36px;
        color: var(--text-dark);
        margin-bottom: 15px;
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    .page-subtitle {
        font-size: 18px;
        color: var(--text-light);
        max-width: 600px;
        margin: 0 auto;
        line-height: 1.6;
        font-weight: 400;
    }

    .announcements-list {
        display: grid;
        grid-template-columns: 1fr;
        gap: 25px;
        margin-top: 40px;
    }

    .announcement {
        background: var(--white);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-md);
        overflow: hidden;
        transition: all 0.3s ease;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .announcement:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-color);
    }

    .announcement-pinned {
        border-left: 4px solid var(--primary-color);
        background: linear-gradient(135deg, #fff 0%, var(--primary-light) 100%);
    }

    .announcement-header {
        padding: 25px 30px 20px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        background: var(--primary-light);
    }

    .announcement-title {
        font-size: 24px;
        color: var(--text-dark);
        margin: 0;
        font-weight: 700;
        line-height: 1.3;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .announcement-meta {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        gap: 8px;
    }

    .announcement-date {
        font-size: 14px;
        color: var(--text-light);
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .announcement-date i {
        color: var(--primary-color);
    }

    .announcement-pin {
        background: var(--primary-color);
        color: white;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .announcement-pin i {
        transform: rotate(-45deg);
        font-size: 12px;
    }

    .announcement-content {
        padding: 25px 30px;
        font-size: 16px;
        line-height: 1.7;
        color: var(--text-medium);
    }

    .announcement-footer {
        padding: 20px 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-top: 1px solid rgba(0,0,0,0.05);
        background: var(--bg-light);
    }

    .share-button {
        font-size: 14px;
        color: white;
        background: var(--primary-color);
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .share-button:hover {
        background: var(--primary-gradient);
        transform: translateY(-1px);
        box-shadow: var(--shadow-md);
    }

    .no-announcements {
        text-align: center;
        padding: 80px 0;
        color: var(--text-light);
        background: var(--bg-light);
        border-radius: var(--radius-lg);
        border: 1px dashed rgba(0,0,0,0.1);
    }

    .no-announcements i {
        font-size: 70px;
        margin-bottom: 25px;
        color: rgba(0,0,0,0.1);
    }

    .no-announcements h3 {
        font-size: 24px;
        font-weight: 600;
        margin-bottom: 12px;
        color: var(--text-medium);
    }

    .new-badge {
        background: var(--primary-color);
        color: white;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .notification-toast {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background: var(--primary-gradient);
        color: white;
        padding: 16px 22px;
        border-radius: var(--radius-md);
        box-shadow: var(--shadow-lg);
        z-index: 1000;
        max-width: 350px;
        display: flex;
        align-items: center;
        gap: 15px;
        transform: translateY(100px);
        opacity: 0;
        transition: all 0.4s ease;
    }

    .notification-toast.show {
        transform: translateY(0);
        opacity: 1;
    }

    .notification-toast i {
        font-size: 20px;
    }

    .notification-close {
        background: none;
        border: none;
        color: rgba(255,255,255,0.7);
        font-size: 20px;
        cursor: pointer;
        margin-left: auto;
        transition: color 0.2s ease;
    }

    .notification-close:hover {
        color: white;
    }

    .announcement-tag {
        font-size: 12px;
        background: var(--primary-light);
        color: var(--primary-color);
        padding: 4px 10px;
        border-radius: 15px;
        font-weight: 500;
        margin-right: 8px;
    }

    .announcement-actions {
        display: flex;
        gap: 10px;
    }

    .read-more-btn {
        color: var(--text-light);
        background: none;
        border: none;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 6px;
        transition: color 0.2s ease;
    }

    .read-more-btn:hover {
        color: var(--primary-color);
    }

    .fade-in {
        animation: fadeIn 0.7s ease-out forwards;
    }

    .staggered-fade-in {
        opacity: 0;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }



    @media (max-width: 768px) {
        .page-title {
            font-size: 30px;
        }

        .announcement-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 12px;
        }

        .announcement-meta {
            align-items: flex-start;
            margin-top: 8px;
        }

        .announcement-date {
            margin-top: 5px;
        }

        .announcement-content {
            padding: 20px 25px;
        }

        .announcement-footer {
            padding: 15px 25px 20px;
        }
    }


</style>

<div class="announcements-container">
    <div class="page-header fade-in">
        <h1 class="page-title">Church Announcements</h1>
        <p class="page-subtitle">Stay updated with the latest news, events, and important information from our church community</p>
    </div>

    <div class="announcements-list">
        @forelse($announcements as $index => $announcement)
            <div class="announcement staggered-fade-in {{ $announcement->is_pinned ? 'announcement-pinned' : '' }}"
                 id="announcement-{{ $announcement->id }}"
                 style="animation-delay: {{ 100 * $index }}ms">
                <div class="announcement-header">
                    <div>
                        <h2 class="announcement-title">
                            {{ $announcement->title }}
                            @if($announcement->created_at->gt(now()->subDays(3)))
                                <span class="new-badge">New</span>
                            @endif
                        </h2>
                        @if(isset($announcement->category))
                            <div class="mt-2">
                                <span class="announcement-tag">{{ $announcement->category }}</span>
                            </div>
                        @endif
                    </div>
                    <div class="announcement-meta">
                        @if($announcement->is_pinned)
                            <span class="announcement-pin">
                                <i class="fas fa-thumbtack"></i> Important
                            </span>
                        @endif
                        <div class="announcement-date">
                            <i class="far fa-calendar-alt"></i>
                            {{ $announcement->created_at->format('F j, Y') }}
                        </div>
                    </div>
                </div>
                <div class="announcement-content">
                    {{ $announcement->message }}
                </div>
                <div class="announcement-footer">
                    <button class="read-more-btn">
                        <i class="fas fa-eye"></i> Read full announcement
                    </button>
                    <div class="announcement-actions">
                        <button class="share-button" onclick="shareAnnouncement('{{ $announcement->title }}')">
                            <i class="fas fa-share-alt"></i> Share
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="no-announcements fade-in">
                <i class="far fa-bell-slash"></i>
                <h3>No Announcements</h3>
                <p>There are currently no announcements to display.<br>Please check back later for updates.</p>
            </div>
        @endforelse
    </div>
</div>

<div id="notification-toast" class="notification-toast">
    <i class="fas fa-bullhorn"></i>
    <div id="toast-message"></div>
    <button class="notification-close" onclick="closeToast()">&times;</button>
</div>

<script>
    // Apply staggered animation effect
    document.addEventListener('DOMContentLoaded', function() {
        const announcements = document.querySelectorAll('.staggered-fade-in');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('fade-in');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1 });

        announcements.forEach(announcement => {
            observer.observe(announcement);
        });
    });

    // Share announcement function
    function shareAnnouncement(title) {
        if (navigator.share) {
            navigator.share({
                title: 'Church Announcement: ' + title,
                text: 'Check out this church announcement: ' + title,
                url: window.location.href,
            })
            .then(() => console.log('Successful share'))
            .catch((error) => console.log('Error sharing:', error));
        } else {
            // Fallback for browsers that don't support Web Share API
            const url = window.location.href;
            const textArea = document.createElement('textarea');
            textArea.value = url;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            showToast('Link copied to clipboard!');
        }
    }

    // Toast notification functions
    function showToast(message, duration = 3000) {
        const toast = document.getElementById('notification-toast');
        const toastMessage = document.getElementById('toast-message');

        toastMessage.textContent = message;
        toast.classList.add('show');

        setTimeout(() => {
            closeToast();
        }, duration);
    }

    function closeToast() {
        const toast = document.getElementById('notification-toast');
        toast.classList.remove('show');
    }

    // Read full announcement expansion
    document.addEventListener('DOMContentLoaded', function() {
        const readMoreButtons = document.querySelectorAll('.read-more-btn');

        readMoreButtons.forEach(button => {
            button.addEventListener('click', function() {
                const content = this.closest('.announcement').querySelector('.announcement-content');

                if (content.classList.contains('expanded')) {
                    content.classList.remove('expanded');
                    content.style.maxHeight = null;
                    this.innerHTML = '<i class="fas fa-eye"></i> Read full announcement';
                } else {
                    content.classList.add('expanded');
                    content.style.maxHeight = content.scrollHeight + 'px';
                    this.innerHTML = '<i class="fas fa-eye-slash"></i> Show less';
                }
            });
        });
    });

    // Pusher integration for real-time announcements
    document.addEventListener('DOMContentLoaded', function() {
        // Enable Pusher logging - only for development
        Pusher.logToConsole = {{ config('app.debug') ? 'true' : 'false' }};

        const echo = new Echo({
            broadcaster: 'pusher',
            key: '{{ config('broadcasting.connections.pusher.key') }}',
            cluster: '{{ config('broadcasting.connections.pusher.options.cluster') }}',
            forceTLS: true
        });

        echo.channel('announcements')
            .listen('AnnouncementPosted', (e) => {
                console.log('New Announcement Received:', e);

                // Show a notification
                showToast(`📢 New Announcement: ${e.title}`);

                // You could also refresh the page or add the new announcement to the list
                // For better UX, we'll reload the page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            });
    });
</script>

@endsection
