@extends('member.dashboard_member')
@section('content')

<div class="container-fluid px-lg-5 py-4">
    <div class="row g-4">
        <!-- Main Content Area -->
        <div class="col-lg-8">
            <!-- Video Player -->
            <div class="video-player-wrapper">
                <div class="video-player-container">
                    <video 
                        id="cloudinary-player" 
                        controls 
                        class="cld-video-player"
                        playsinline
                        data-cld-public-id="{{ $stream->cloudinary_public_id }}"
                    >
                        <source src="{{ $stream->replay_url }}" type="video/mp4">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
            
            <!-- Stream Header -->
            <div class="stream-header">
                <h2 class="stream-title">{{ $stream->title ?? 'Untitled Stream' }}</h2>
                <div class="stream-meta">
                    <span class="meta-item">
                        <i class="fas fa-eye"></i>
                        <span class="meta-label">Views</span>
                        <span class="meta-value">{{ number_format($stream->views ?? 0) }}</span>
                    </span>
                    <span class="meta-divider">•</span>
                    <span class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        {{ $stream->ended_at->format('M d, Y') }}
                    </span>
                </div>
            </div>

            <!-- Creator Info Card -->
            <div class="creator-card">
                <div class="creator-info">
                    <div class="creator-avatar">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <div class="creator-details">
                        <h5 class="creator-name">{{ $stream->user->name }}</h5>
                        <p class="creator-meta">
                            Streamed on {{ $stream->ended_at->format('F d, Y') }} at {{ $stream->ended_at->format('h:i A') }}
                        </p>
                    </div>
                </div>
                <div class="stream-stats">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">Duration</span>
                            <span class="stat-value">{{ $stream->formatted_duration }}</span>
                        </div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-hdd"></i>
                        </div>
                        <div class="stat-content">
                            <span class="stat-label">File Size</span>
                            <span class="stat-value">{{ $stream->formatted_file_size }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <div class="sidebar-content">
                <div class="sidebar-header">
                    <h5 class="sidebar-title">
                        <i class="fas fa-video"></i>
                        More Recordings
                    </h5>
                </div>
                
                @php
                    $otherStreams = App\Models\LiveStream::where('status', 'ended')
                        ->whereNotNull('replay_url')
                        ->where('id', '!=', $stream->id)
                        ->latest('ended_at')
                        ->limit(5)
                        ->get();
                @endphp
                
                <div class="recordings-list">
                    @forelse($otherStreams as $other)
                        <a href="{{ route('member.recordings.watch', $other->id) }}" class="recording-card">
                            <div class="recording-thumbnail-wrapper">
                                @if($other->thumbnail_url)
                                    <img src="{{ $other->thumbnail_url }}" alt="{{ $other->title }}" class="recording-thumbnail">
                                @else
                                    <div class="recording-thumbnail-placeholder">
                                        <i class="fas fa-play-circle"></i>
                                    </div>
                                @endif
                                <div class="recording-duration">
                                    <i class="fas fa-clock"></i>
                                    {{ $other->formatted_duration }}
                                </div>
                            </div>
                            <div class="recording-details">
                                <h6 class="recording-title">{{ Str::limit($other->title ?? 'Untitled', 50) }}</h6>
                                <div class="recording-info">
                                    <span class="recording-author">
                                        <i class="fas fa-user"></i>
                                        {{ $other->user->name }}
                                    </span>
                                    <span class="recording-date">
                                        {{ $other->ended_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="empty-state">
                            <i class="fas fa-video-slash"></i>
                            <p>No other recordings available</p>
                        </div>
                    @endforelse
                </div>
                
                <a href="{{ route('member.recordings') }}" class="btn-view-all">
                    <span>View All Recordings</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
:root {
    --primary-color: #3b82f6;
    --primary-dark: #2563eb;
    --text-primary: #1f2937;
    --text-secondary: #6b7280;
    --text-muted: #9ca3af;
    --border-color: #e5e7eb;
    --bg-light: #f9fafb;
    --bg-white: #ffffff;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 6px;
    --radius-md: 10px;
    --radius-lg: 14px;
}

/* Video Player Styles */
.video-player-wrapper {
    margin-bottom: 1.5rem;
}

.video-player-container {
    background: #000;
    border-radius: var(--radius-lg);
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    position: relative;
    aspect-ratio: 16/9;
    max-height: 600px;
}

#cloudinary-player,
.native-video-player {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background: #000;
    display: block;
}

/* Stream Header */
.stream-header {
    margin-bottom: 1.5rem;
}

.stream-title {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.75rem;
    line-height: 1.3;
}

.stream-meta {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.meta-item i {
    font-size: 0.875rem;
}

.meta-label {
    font-weight: 500;
}

.meta-value {
    font-weight: 600;
    color: var(--text-primary);
}

.meta-divider {
    color: var(--border-color);
}

/* Creator Card */
.creator-card {
    background: var(--bg-white);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    padding: 1.5rem;
    box-shadow: var(--shadow-sm);
}

.creator-info {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid var(--border-color);
}

.creator-avatar {
    width: 56px;
    height: 56px;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.creator-details {
    flex: 1;
    min-width: 0;
}

.creator-name {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.25rem 0;
}

.creator-meta {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin: 0;
}

.stream-stats {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.stat-icon {
    width: 40px;
    height: 40px;
    background: var(--bg-light);
    border-radius: var(--radius-sm);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1rem;
    flex-shrink: 0;
}

.stat-content {
    display: flex;
    flex-direction: column;
}

.stat-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.stat-value {
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--text-primary);
}

/* Sidebar */
.sidebar-content {
    background: var(--bg-white);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-md);
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    position: sticky;
    top: 20px;
}

.sidebar-header {
    padding: 1.25rem 1.5rem;
    border-bottom: 1px solid var(--border-color);
    background: var(--bg-light);
}

.sidebar-title {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.sidebar-title i {
    color: var(--primary-color);
}

/* Recordings List */
.recordings-list {
    padding: 0.75rem;
    max-height: 600px;
    overflow-y: auto;
}

.recordings-list::-webkit-scrollbar {
    width: 6px;
}

.recordings-list::-webkit-scrollbar-track {
    background: var(--bg-light);
}

.recordings-list::-webkit-scrollbar-thumb {
    background: var(--border-color);
    border-radius: 3px;
}

.recordings-list::-webkit-scrollbar-thumb:hover {
    background: var(--text-muted);
}

.recording-card {
    display: block;
    text-decoration: none;
    color: inherit;
    padding: 0.75rem;
    border-radius: var(--radius-md);
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.recording-card:hover {
    background: var(--bg-light);
    border-color: var(--border-color);
    transform: translateX(4px);
}

.recording-card:last-child {
    margin-bottom: 0;
}

.recording-thumbnail-wrapper {
    position: relative;
    border-radius: var(--radius-sm);
    overflow: hidden;
    margin-bottom: 0.75rem;
    aspect-ratio: 16/9;
}

.recording-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.recording-card:hover .recording-thumbnail {
    transform: scale(1.05);
}

.recording-thumbnail-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 2rem;
}

.recording-duration {
    position: absolute;
    bottom: 0.5rem;
    right: 0.5rem;
    background: rgba(0, 0, 0, 0.85);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.recording-duration i {
    font-size: 0.65rem;
}

.recording-details {
    padding: 0 0.25rem;
}

.recording-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin: 0 0 0.5rem 0;
    line-height: 1.4;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.recording-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.recording-author,
.recording-date {
    font-size: 0.8rem;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.35rem;
}

.recording-author i {
    font-size: 0.7rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--text-muted);
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 0.875rem;
}

/* View All Button */
.btn-view-all {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 1rem 1.5rem;
    background: var(--primary-color);
    color: white;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    border-top: 1px solid var(--border-color);
    transition: all 0.2s ease;
}

.btn-view-all:hover {
    background: var(--primary-dark);
    color: white;
}

.btn-view-all i {
    font-size: 0.875rem;
    transition: transform 0.2s ease;
}

.btn-view-all:hover i {
    transform: translateX(4px);
}

/* Responsive Design */
@media (max-width: 991px) {
    .sidebar-content {
        position: relative;
        top: 0;
    }
    
    .stream-stats {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 767px) {
    .stream-title {
        font-size: 1.5rem;
    }
    
    .creator-info {
        flex-direction: column;
        align-items: flex-start;
        text-align: center;
    }
    
    .creator-avatar {
        width: 48px;
        height: 48px;
        font-size: 1.5rem;
    }
}
</style>

<!-- Cloudinary Video Player -->
<link href="https://unpkg.com/cloudinary-video-player@1.9.4/dist/cld-video-player.min.css" rel="stylesheet">
<script src="https://unpkg.com/cloudinary-core@latest/cloudinary-core-shrinkwrap.min.js"></script>
<script src="https://unpkg.com/cloudinary-video-player@1.9.4/dist/cld-video-player.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoElement = document.getElementById('cloudinary-player');
    const replayUrl = '{{ $stream->replay_url }}';
    
    // Use native player directly for better compatibility
    fallbackToNativePlayer();
    
    function fallbackToNativePlayer() {
        videoElement.innerHTML = '';
        videoElement.className = 'native-video-player';
        videoElement.setAttribute('controls', 'controls');
        videoElement.setAttribute('preload', 'metadata');
        videoElement.setAttribute('playsinline', '');
        
        const isWebM = replayUrl.includes('.webm');
        
        if (isWebM) {
            // WebM with VP8
            const webmSource = document.createElement('source');
            webmSource.src = replayUrl;
            webmSource.type = 'video/webm';
            videoElement.appendChild(webmSource);
            
            // MP4 fallback
            const parts = replayUrl.split('/video/upload/');
            if (parts.length === 2) {
                const mp4Url = parts[0] + '/video/upload/f_mp4,q_auto/' + parts[1].replace('.webm', '.mp4');
                const mp4Source = document.createElement('source');
                mp4Source.src = mp4Url;
                mp4Source.type = 'video/mp4';
                videoElement.appendChild(mp4Source);
            }
        } else {
            // Direct MP4
            const mp4Source = document.createElement('source');
            mp4Source.src = replayUrl;
            mp4Source.type = 'video/mp4';
            videoElement.appendChild(mp4Source);
        }
        
        videoElement.load();
        
        videoElement.onerror = function() {
            console.error('Video load failed:', replayUrl);
            alert('Cannot load video. Please check your connection.');
        };
    }
});

console.log('Video URL:', '{{ $stream->replay_url }}');
</script>

@endsection