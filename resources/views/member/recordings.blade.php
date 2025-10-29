@extends('member.dashboard_member')
@section('content')

<div class="container-fluid px-3 px-md-4 py-3 py-md-4">
    <!-- Header Section -->
    <div class="recordings-header mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="page-title mb-1">
                    <i class="fas fa-play-circle gradient-icon"></i>
                    Livestream 
                </h2>
                <p class="text-muted mb-0 small">Watch past livestream sessions</p>
            </div>
            
            </div>
        </div>
    </div>
    
    @if($streams->isEmpty())
        <!-- Empty State -->
        <div class="empty-state-container">
            <div class="empty-state-content">
                <div class="empty-state-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h4 class="empty-state-title">No Recordings Yet</h4>
                <p class="empty-state-text">
                    Past livestream recordings will appear here once they're available.
                </p>
                <div class="empty-state-illustration">
                    <i class="fas fa-broadcast-tower"></i>
                    <i class="fas fa-arrow-right"></i>
                    <i class="fas fa-film"></i>
                </div>
            </div>
        </div>
    @else
        <!-- Recordings Grid -->
        <div class="recordings-grid">
            @foreach($streams as $stream)
                <div class="recording-card-wrapper">
                    <div class="recording-card">
                        <!-- Thumbnail -->
                        <div class="recording-thumbnail">
                            @if($stream->thumbnail_url)
                                <img src="{{ $stream->thumbnail_url }}" 
                                     alt="{{ $stream->title }}" 
                                     class="thumbnail-image"
                                     loading="lazy">
                            @else
                                <div class="thumbnail-placeholder">
                                    <i class="fas fa-video"></i>
                                </div>
                            @endif
                            
                            <!-- Overlay on hover -->
                            <div class="thumbnail-overlay">
                                <div class="play-button">
                                    <i class="fas fa-play"></i>
                                </div>
                            </div>
                            
                            <!-- Duration Badge -->
                            @if($stream->formatted_duration)
                                <div class="duration-badge">
                                    <i class="fas fa-clock"></i>
                                    {{ $stream->formatted_duration }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Card Body -->
                        <div class="recording-body">
                            <h5 class="recording-title" title="{{ $stream->title ?? 'Untitled Stream' }}">
                                {{ $stream->title ?? 'Untitled Stream' }}
                            </h5>
                            
                            <div class="recording-meta">
                                <div class="meta-item">
                                    <div class="broadcaster-info">
                                        <div class="broadcaster-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="broadcaster-name">{{ $stream->user->first_name }} {{ $stream->user->last_name }}</span>
                                    </div>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-calendar-alt"></i>
                                    <span>{{ $stream->ended_at->format('M d, Y') }}</span>
                                </div>
                                
                                <div class="meta-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $stream->ended_at->format('h:i A') }}</span>
                                </div>
                            </div>
                            
                            <a href="{{ route('member.recordings.watch', $stream->id) }}" 
                               class="btn-watch">
                                <i class="fas fa-play"></i>
                                <span>Watch Recording</span>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        @if($streams->hasPages())
            <div class="pagination-wrapper">
                {{ $streams->links() }}
            </div>
        @endif
    @endif
</div>

<style>
/* CSS Variables */
:root {
    --primary-color: #6366f1;
    --primary-hover: #4f46e5;
    --gradient-primary: linear-gradient(135deg, #6366f1, #8b5cf6);
    --gradient-accent: linear-gradient(135deg, #ec4899, #f43f5e);
    --bg-overlay: rgba(0, 0, 0, 0.7);
    --card-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    --card-shadow-hover: 0 8px 24px rgba(0, 0, 0, 0.15);
    --border-radius: 16px;
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Header Section */
.recordings-header {
    animation: fadeInDown 0.5s ease;
}

.page-title {
    font-size: clamp(1.5rem, 4vw, 2rem);
    font-weight: 700;
    color: #1e293b;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin: 0;
}

.gradient-icon {
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.recordings-stats {
    display: flex;
    gap: 0.75rem;
}

.stat-chip {
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    padding: 0.5rem 1rem;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #475569;
    font-size: 0.875rem;
    box-shadow: var(--card-shadow);
}

.stat-chip i {
    color: var(--primary-color);
}

/* Recordings Grid */
.recordings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

@media (min-width: 768px) {
    .recordings-grid {
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    }
}

@media (min-width: 1200px) {
    .recordings-grid {
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    }
}

/* Recording Card */
.recording-card-wrapper {
    animation: fadeInUp 0.5s ease backwards;
}

.recording-card-wrapper:nth-child(1) { animation-delay: 0.1s; }
.recording-card-wrapper:nth-child(2) { animation-delay: 0.2s; }
.recording-card-wrapper:nth-child(3) { animation-delay: 0.3s; }
.recording-card-wrapper:nth-child(4) { animation-delay: 0.4s; }
.recording-card-wrapper:nth-child(5) { animation-delay: 0.5s; }
.recording-card-wrapper:nth-child(6) { animation-delay: 0.6s; }

.recording-card {
    background: white;
    border-radius: var(--border-radius);
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: var(--transition);
    height: 100%;
    display: flex;
    flex-direction: column;
    border: 1px solid #f1f5f9;
}

.recording-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--card-shadow-hover);
}

/* Thumbnail */
.recording-thumbnail {
    position: relative;
    background: #000;
    aspect-ratio: 16 / 9;
    overflow: hidden;
    cursor: pointer;
}

.thumbnail-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.recording-card:hover .thumbnail-image {
    transform: scale(1.08);
}

.thumbnail-placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    background: linear-gradient(135deg, #1e293b, #334155);
    color: #64748b;
    font-size: 3rem;
}

/* Thumbnail Overlay */
.thumbnail-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: var(--bg-overlay);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.recording-card:hover .thumbnail-overlay {
    opacity: 1;
}

.play-button {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    font-size: 1.5rem;
    transform: scale(0.8);
    transition: transform 0.3s ease;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
}

.recording-card:hover .play-button {
    transform: scale(1);
}

/* Duration Badge */
.duration-badge {
    position: absolute;
    bottom: 12px;
    right: 12px;
    background: rgba(0, 0, 0, 0.85);
    backdrop-filter: blur(8px);
    color: white;
    padding: 0.375rem 0.75rem;
    border-radius: 8px;
    font-size: 0.75rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.375rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
}

.duration-badge i {
    font-size: 0.7rem;
}

/* Card Body */
.recording-body {
    padding: 1.25rem;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.recording-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 1rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-height: 1.4;
    min-height: 2.8rem;
}

/* Meta Information */
.recording-meta {
    display: flex;
    flex-direction: column;
    gap: 0.625rem;
    margin-bottom: 1.25rem;
    flex: 1;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #64748b;
}

.meta-item i {
    width: 16px;
    text-align: center;
    color: #94a3b8;
}

.broadcaster-info {
    display: flex;
    align-items: center;
    gap: 0.625rem;
}

.broadcaster-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: var(--gradient-primary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.broadcaster-name {
    font-weight: 600;
    color: #475569;
}

/* Watch Button */
.btn-watch {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.625rem;
    width: 100%;
    padding: 0.75rem 1.25rem;
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    font-size: 0.9375rem;
    text-decoration: none;
    transition: var(--transition);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
}

.btn-watch:hover {
    background: linear-gradient(135deg, #5851d6, #64439e);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35);
    color: white;
    text-decoration: none;
}

.btn-watch:active {
    transform: translateY(0);
}

/* Empty State */
.empty-state-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 500px;
    padding: 2rem;
}

.empty-state-content {
    text-align: center;
    max-width: 400px;
    animation: fadeIn 0.6s ease;
}

.empty-state-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: #94a3b8;
}

.empty-state-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin-bottom: 0.75rem;
}

.empty-state-text {
    font-size: 1rem;
    color: #64748b;
    margin-bottom: 2rem;
    line-height: 1.6;
}

.empty-state-illustration {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 1rem;
    font-size: 2rem;
    color: #cbd5e1;
}

/* Pagination */
.pagination-wrapper {
    display: flex;
    justify-content: center;
    padding: 2rem 0;
}

/* Animations */
@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes fadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

/* Responsive Design */
@media (max-width: 767px) {
    .recordings-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .recording-body {
        padding: 1rem;
    }
    
    .recording-title {
        font-size: 1rem;
    }
    
    .stat-chip {
        font-size: 0.8125rem;
        padding: 0.4rem 0.875rem;
    }
    
    .empty-state-icon {
        width: 100px;
        height: 100px;
        font-size: 2.5rem;
    }
    
    .empty-state-title {
        font-size: 1.25rem;
    }
}

@media (max-width: 575px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .recordings-header {
        margin-bottom: 1.5rem;
    }
    
    .play-button {
        width: 50px;
        height: 50px;
        font-size: 1.25rem;
    }
    
    .duration-badge {
        bottom: 8px;
        right: 8px;
        padding: 0.25rem 0.5rem;
        font-size: 0.7rem;
    }
}

/* Dark Mode Support */
@media (prefers-color-scheme: dark) {
    .recording-card {
        background: #1e293b;
        border-color: #334155;
    }
    
    .recording-title {
        color: #f1f5f9;
    }
    
    .broadcaster-name {
        color: #cbd5e1;
    }
    
    .meta-item {
        color: #94a3b8;
    }
    
    .empty-state-title {
        color: #f1f5f9;
    }
    
    .empty-state-text {
        color: #94a3b8;
    }
    
    .stat-chip {
        background: linear-gradient(135deg, #334155, #475569);
        color: #e2e8f0;
    }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Focus Styles for Accessibility */
.btn-watch:focus,
.recording-card:focus-within {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>

@endsection