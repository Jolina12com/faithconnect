@extends('admin.dashboard')

@section('content')
<div class="container-fluid py-3 py-md-4">
    <!-- Header Section -->
    <div class="header-section mb-4">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
            <div>
                <h2 class="page-title mb-1">
                    <i class="fas fa-broadcast-tower text-primary"></i>
                    Livestream Management
                </h2>
                <p class="text-muted mb-0 small">Manage and monitor all livestream activities</p>
            </div>
            <a href="{{ route('broadcaster') }}" class="btn btn-primary btn-go-live">
                <i class="fas fa-circle pulse-icon"></i>
                <span>Go Live</span>
            </a>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="stat-card stat-card-total">
                <div class="stat-icon">
                    <i class="fas fa-video"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $streams->total() }}</div>
                    <div class="stat-label">Total Streams</div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="stat-card stat-card-live">
                <div class="stat-icon">
                    <i class="fas fa-circle pulse-icon"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $streams->where('status', 'live')->count() }}</div>
                    <div class="stat-label">Live Now</div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="stat-card stat-card-ended">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $streams->where('status', 'ended')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Streams Table/Cards -->
    <div class="card streams-card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list-ul me-2"></i>
                All Streams
            </h5>
        </div>
        <div class="card-body p-0">
            <!-- Desktop Table View -->
            <div class="table-responsive d-none d-lg-block">
                <table class="table streams-table mb-0">
                    <thead>
                        <tr>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Broadcaster</th>
                            <th>Status</th>
                            <th>Started</th>
                            <th>Duration</th>
                            <th>Size</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($streams as $stream)
                            @if($stream->status === 'processing')
                                @continue
                            @endif
                            <tr>
                                <td>
                                    @if($stream->thumbnail_url)
                                        <img src="{{ $stream->thumbnail_url }}" 
                                             alt="Thumbnail" 
                                             class="stream-thumbnail">
                                    @else
                                        <div class="stream-thumbnail-placeholder">
                                            <i class="fas fa-video"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="stream-title">{{ $stream->title ?? 'Untitled Stream' }}</div>
                                    <small class="text-muted">ID: {{ $stream->id }}</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="broadcaster-avatar">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="ms-2">{{ $stream->user->first_name }} {{ $stream->user->last_name }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($stream->status === 'live')
                                        <span class="status-badge status-live">
                                            <i class="fas fa-circle"></i> Live
                                        </span>
                                    @elseif($stream->status === 'processing')
                                        <span class="status-badge status-processing">
                                            <i class="fas fa-spinner fa-spin"></i> Processing
                                        </span>
                                    @else
                                        <span class="status-badge status-ended">
                                            <i class="fas fa-check"></i> Ended
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <div class="date-info">
                                        <div>{{ $stream->started_at->format('M d, Y') }}</div>
                                        <small class="text-muted">{{ $stream->started_at->format('h:i A') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="info-badge">
                                        @if($stream->duration > 0)
                                            <i class="fas fa-clock me-1"></i>{{ $stream->formatted_duration }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <span class="info-badge">
                                        @if($stream->file_size > 0)
                                            <i class="fas fa-hdd me-1"></i>{{ $stream->formatted_file_size }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        @if($stream->replay_url && $stream->status === 'ended')
                                            <button type="button"
                                                    class="btn btn-sm btn-play watch-stream"
                                                    data-stream-id="{{ $stream->id }}"
                                                    data-stream-title="{{ $stream->title ?? 'Untitled' }}"
                                                    data-replay-url="{{ $stream->replay_url }}"
                                                    data-broadcaster="{{ $stream->user->first_name }} {{ $stream->user->last_name }}"
                                                    data-date="{{ optional($stream->ended_at)->format('F d, Y \a\t h:i A') ?? 'In Progress' }}"
                                                    data-duration="{{ $stream->formatted_duration ?? '-' }}"
                                                    data-size="{{ $stream->formatted_file_size ?? '-' }}"
                                                    title="Watch Recording">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-info view-viewers"
                                                data-stream-id="{{ $stream->id }}"
                                                data-stream-title="{{ $stream->title ?? 'Untitled' }}"
                                                title="View Attendance">
                                            <i class="fas fa-users"></i>
                                        </button>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-delete delete-stream"
                                                data-stream-id="{{ $stream->id }}"
                                                data-stream-title="{{ $stream->title ?? 'Untitled' }}"
                                                title="Delete Stream">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-video fa-4x text-muted mb-3"></i>
                                        <h5 class="text-muted">No streams yet</h5>
                                        <p class="text-muted">Start your first livestream to see it here!</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Card View -->
            <div class="d-lg-none">
                @forelse($streams as $stream)
                    @if($stream->status === 'processing')
                        @continue
                    @endif
                    <div class="stream-mobile-card">
                        <div class="stream-mobile-header">
                            @if($stream->thumbnail_url)
                                <img src="{{ $stream->thumbnail_url }}" 
                                     alt="Thumbnail" 
                                     class="stream-mobile-thumbnail">
                            @else
                                <div class="stream-mobile-thumbnail-placeholder">
                                    <i class="fas fa-video"></i>
                                </div>
                            @endif
                            <div class="stream-mobile-title-section">
                                <h6 class="stream-mobile-title">{{ $stream->title ?? 'Untitled Stream' }}</h6>
                                <p class="stream-mobile-broadcaster">
                                    <i class="fas fa-user-circle"></i>
                                    {{ $stream->user->first_name }} {{ $stream->user->last_name }}
                                </p>
                            </div>
                        </div>
                        
                        <div class="stream-mobile-body">
                            <div class="stream-mobile-status">
                                @if($stream->status === 'live')
                                    <span class="status-badge status-live">
                                        <i class="fas fa-circle"></i> Live
                                    </span>
                                @elseif($stream->status === 'processing')
                                    <span class="status-badge status-processing">
                                        <i class="fas fa-spinner fa-spin"></i> Processing
                                    </span>
                                @else
                                    <span class="status-badge status-ended">
                                        <i class="fas fa-check"></i> Ended
                                    </span>
                                @endif
                            </div>
                            
                            <div class="stream-mobile-info">
                                <div class="info-item">
                                    <i class="fas fa-calendar"></i>
                                    <span>{{ $stream->started_at->format('M d, Y') }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $stream->duration > 0 ? $stream->formatted_duration : '-' }}</span>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-hdd"></i>
                                    <span>{{ $stream->file_size > 0 ? $stream->formatted_file_size : '-' }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="stream-mobile-actions">
                            @if($stream->replay_url && $stream->status === 'ended')
                                <button type="button"
                                        class="btn btn-sm btn-play watch-stream flex-fill"
                                        data-stream-id="{{ $stream->id }}"
                                        data-stream-title="{{ $stream->title ?? 'Untitled' }}"
                                        data-replay-url="{{ $stream->replay_url }}"
                                        data-broadcaster="{{ $stream->user->first_name }} {{ $stream->user->last_name }}"
                                        data-date="{{ optional($stream->ended_at)->format('F d, Y \a\t h:i A') ?? 'In Progress' }}"
                                        data-duration="{{ $stream->formatted_duration ?? '-' }}"
                                        data-size="{{ $stream->formatted_file_size ?? '-' }}">
                                    <i class="fas fa-play me-1"></i> Watch
                                </button>
                            @endif
                            
                            <button type="button" 
                                    class="btn btn-sm btn-delete delete-stream"
                                    data-stream-id="{{ $stream->id }}"
                                    data-stream-title="{{ $stream->title ?? 'Untitled' }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state-mobile">
                        <i class="fas fa-video fa-4x text-muted mb-3"></i>
                        <h5 class="text-muted">No streams yet</h5>
                        <p class="text-muted">Start your first livestream to see it here!</p>
                    </div>
                @endforelse
            </div>
            
            @if($streams->hasPages())
                <div class="pagination-wrapper">
                    {{ $streams->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Watch Recording Modal -->
<div class="modal fade" id="watchModal" tabindex="-1" aria-labelledby="watchModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="watchModalTitle">
                    <i class="fas fa-play-circle me-2"></i>Stream Recording
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="video-container">
                    <video 
                        id="admin-player" 
                        controls 
                        preload="metadata"
                        class="w-100"
                        playsinline
                    >
                        <source id="video-source" src="" type="">
                        Your browser does not support the video tag.
                    </video>
                </div>
                
                <div class="stream-details">
                    <div class="row g-3">
                        <div class="col-6 col-md-3">
                            <div class="detail-item">
                                <i class="fas fa-user-circle"></i>
                                <div>
                                    <small>Broadcaster</small>
                                    <div id="modal-broadcaster"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="detail-item">
                                <i class="fas fa-calendar"></i>
                                <div>
                                    <small>Date</small>
                                    <div id="modal-date"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="detail-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <small>Duration</small>
                                    <div id="modal-duration"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="detail-item">
                                <i class="fas fa-hdd"></i>
                                <div>
                                    <small>Size</small>
                                    <div id="modal-size"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Viewers Modal -->
<div class="modal fade" id="viewersModal" tabindex="-1" aria-labelledby="viewersModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewersModalTitle">
                    <i class="fas fa-users me-2"></i>Stream Attendance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="viewers-loading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="viewers-content" style="display: none;">
                    <div class="alert alert-info">
                        <strong>Total Viewers:</strong> <span id="total-viewers">0</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Joined</th>
                                    <th>Left</th>
                                    <th>Duration</th>
                                </tr>
                            </thead>
                            <tbody id="viewers-list">
                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="viewers-error" style="display: none;" class="alert alert-danger">
                    Failed to load viewers
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger" id="deleteModalTitle">
                    <i class="fas fa-exclamation-triangle me-2"></i>Delete Stream
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-3">Are you sure you want to delete <strong id="streamTitle"></strong>?</p>
                <div class="alert alert-danger d-flex align-items-start">
                    <i class="fas fa-exclamation-circle me-2 mt-1"></i>
                    <div>
                        <strong>Warning:</strong> This will permanently delete the recording from Cloudinary and cannot be undone.
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> Cancel
                </button>
                <button type="button" class="btn btn-danger" id="confirmDelete">
                    <i class="fas fa-trash-alt me-1"></i> Delete Permanently
                </button>
            </div>
        </div>
    </div>
</div>

<style>
/* Global Styles */
:root {
    --primary-color: #6366f1;
    --primary-hover: #4f46e5;
    --success-color: #10b981;
    --danger-color: #ef4444;
    --warning-color: #f59e0b;
    --secondary-color: #64748b;
    --border-radius: 12px;
    --box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    --box-shadow-hover: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Header Section */
.header-section {
    animation: fadeInDown 0.5s ease;
}

.page-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
}

.btn-go-live {
    background: linear-gradient(135deg, var(--danger-color), #dc2626);
    border: none;
    border-radius: 25px;
    padding: 0.6rem 1.5rem;
    font-weight: 600;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-go-live:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
}

.pulse-icon {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* Statistics Cards */
.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: var(--box-shadow);
    transition: all 0.3s ease;
    border: 1px solid #f1f5f9;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--box-shadow-hover);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.stat-card-total .stat-icon {
    background: linear-gradient(to right, #42a5f5, #1e88e5);
    color: white;
}

.stat-card-live .stat-icon {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.stat-card-ended .stat-icon {
    background: linear-gradient(135deg, #64748b, #475569);
    color: white;
}

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #1e293b;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #64748b;
    font-weight: 500;
}

/* Streams Card */
.streams-card {
    border-radius: var(--border-radius);
    border: 1px solid #f1f5f9;
    box-shadow: var(--box-shadow);
    overflow: hidden;
}

.streams-card .card-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem;
}

.streams-card .card-header h5 {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.1rem;
}

/* Desktop Table */
.streams-table {
    font-size: 0.875rem;
}

.streams-table thead th {
    background: #f8fafc;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.05em;
    padding: 1rem;
    border-bottom: 2px solid #e2e8f0;
}

.streams-table tbody tr {
    transition: all 0.2s ease;
    border-bottom: 1px solid #f1f5f9;
}

.streams-table tbody tr:hover {
    background: #f8fafc;
}

.streams-table td {
    padding: 1rem;
    vertical-align: middle;
}

.stream-thumbnail {
    width: 80px;
    height: 45px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stream-thumbnail-placeholder {
    width: 80px;
    height: 45px;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
}

.stream-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.25rem;
}

.broadcaster-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
    padding: 0.375rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.status-live {
    background: #dcfce7;
    color: #166534;
}

.status-processing {
    background: #dbeafe;
    color: #1e40af;
}

.status-ended {
    background: #f1f5f9;
    color: #475569;
}

.date-info {
    line-height: 1.4;
}

.info-badge {
    display: inline-flex;
    align-items: center;
    color: #64748b;
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.btn-play {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-hover));
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.btn-play:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    color: white;
}

.btn-delete {
    background: #fee2e2;
    color: var(--danger-color);
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.btn-delete:hover {
    background: var(--danger-color);
    color: white;
    transform: scale(1.05);
}

.btn-info {
    background: #3b82f6;
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
    transition: all 0.2s ease;
}

.btn-info:hover {
    background: #2563eb;
    color: white;
    transform: scale(1.05);
}

/* Mobile Card View */
.stream-mobile-card {
    border-bottom: 1px solid #f1f5f9;
    padding: 1rem;
    transition: background 0.2s ease;
}

.stream-mobile-card:hover {
    background: #f8fafc;
}

.stream-mobile-card:last-child {
    border-bottom: none;
}

.stream-mobile-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 0.75rem;
}

.stream-mobile-thumbnail {
    width: 100px;
    height: 56px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stream-mobile-thumbnail-placeholder {
    width: 100px;
    height: 56px;
    background: linear-gradient(135deg, #e2e8f0, #cbd5e1);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #94a3b8;
    flex-shrink: 0;
}

.stream-mobile-title-section {
    flex: 1;
    min-width: 0;
}

.stream-mobile-title {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 0.375rem;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}

.stream-mobile-broadcaster {
    font-size: 0.8rem;
    color: #64748b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.375rem;
}

.stream-mobile-body {
    margin-bottom: 0.75rem;
}

.stream-mobile-status {
    margin-bottom: 0.75rem;
}

.stream-mobile-info {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8rem;
    color: #64748b;
}

.info-item i {
    color: #94a3b8;
    width: 14px;
    text-align: center;
}

.stream-mobile-actions {
    display: flex;
    gap: 0.5rem;
}

.stream-mobile-actions .btn {
    font-size: 0.85rem;
}

.empty-state-mobile {
    text-align: center;
    padding: 3rem 1rem;
}

/* Modal Styles */
.modal-content {
    border-radius: var(--border-radius);
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    border-bottom: 1px solid #e2e8f0;
    padding: 1.25rem 1.5rem;
}

.modal-title {
    color: #1e293b;
    font-weight: 600;
    font-size: 1.1rem;
}

.video-container {
    background: #000;
    position: relative;
}

.video-container video {
    max-height: 600px;
    display: block;
}

.stream-details {
    padding: 1.5rem;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
}

.detail-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.detail-item i {
    font-size: 1.25rem;
    color: var(--primary-color);
    margin-top: 0.25rem;
}

.detail-item small {
    display: block;
    font-size: 0.75rem;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}

.detail-item > div {
    flex: 1;
}

.detail-item > div > div {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.9rem;
}

/* Pagination */
.pagination-wrapper {
    padding: 1.5rem;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: center;
}

/* Empty State */
.empty-state {
    padding: 2rem 1rem;
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

/* Responsive Design */
@media (max-width: 991px) {
    .page-title {
        font-size: 1.25rem;
    }
    
    .stat-value {
        font-size: 1.5rem;
    }
    
    .stat-label {
        font-size: 0.8rem;
    }
    
    .stat-icon {
        width: 45px;
        height: 45px;
        font-size: 1.1rem;
    }
}

@media (max-width: 575px) {
    .stat-card {
        padding: 1rem;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .stat-value {
        font-size: 1.25rem;
    }
    
    .stat-label {
        font-size: 0.75rem;
    }
    
    .btn-go-live {
        width: 100%;
        justify-content: center;
    }
    
    .modal-xl {
        margin: 0.5rem;
    }
    
    .video-container video {
        max-height: 300px;
    }
    
    .stream-details {
        padding: 1rem;
    }
    
    .detail-item i {
        font-size: 1rem;
    }
}

/* Bootstrap 4 Compatibility (if needed) */
.btn-close {
    background: transparent url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath d='M.293.293a1 1 0 011.414 0L8 6.586 14.293.293a1 1 0 111.414 1.414L9.414 8l6.293 6.293a1 1 0 01-1.414 1.414L8 9.414l-6.293 6.293a1 1 0 01-1.414-1.414L6.586 8 .293 1.707a1 1 0 010-1.414z'/%3e%3c/svg%3e") center/1em auto no-repeat;
    border: 0;
    border-radius: 0.25rem;
    width: 1em;
    height: 1em;
    padding: 0.25em;
    opacity: 0.5;
    cursor: pointer;
}

.btn-close:hover {
    opacity: 0.75;
}

/* Dark mode support (optional) */
@media (prefers-color-scheme: dark) {
    .streams-card,
    .stat-card {
        background: #1e293b;
        border-color: #334155;
    }
    
    .page-title,
    .stream-title,
    .stat-value,
    .modal-title {
        color: #f1f5f9;
    }
    
    .stat-label,
    .text-muted,
    .stream-mobile-broadcaster,
    .info-item {
        color: #94a3b8 !important;
    }
    
    .streams-table thead th {
        background: #334155;
        color: #cbd5e1;
    }
    
    .streams-table tbody tr:hover {
        background: #334155;
    }
    
    .stream-mobile-card:hover {
        background: #334155;
    }
    
    .modal-content {
        background: #1e293b;
        color: #f1f5f9;
    }
    
    .modal-header,
    .stream-details {
        background: #334155;
        border-color: #475569;
    }
    
    .detail-item > div > div {
        color: #f1f5f9;
    }
}

/* Loading States */
.loading-spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    border-top-color: white;
    animation: spinner 0.6s linear infinite;
}

@keyframes spinner {
    to { transform: rotate(360deg); }
}

/* Smooth Scrolling */
html {
    scroll-behavior: smooth;
}

/* Focus Styles for Accessibility */
button:focus,
.btn:focus {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize variables
    const watchModal = $('#watchModal');
    const deleteModal = $('#deleteModal');
    const viewersModal = $('#viewersModal');
    const videoPlayer = document.getElementById('admin-player');
    const videoSource = document.getElementById('video-source');
    let streamIdToDelete = null;
    
    // Handle watch stream button clicks
    document.querySelectorAll('.watch-stream').forEach(button => {
        button.addEventListener('click', function() {
            const title = this.dataset.streamTitle;
            const replayUrl = this.dataset.replayUrl;
            const broadcaster = this.dataset.broadcaster;
            const date = this.dataset.date;
            const duration = this.dataset.duration;
            const size = this.dataset.size;
            
            // Update modal content
            document.getElementById('watchModalTitle').textContent = title;
            document.getElementById('modal-broadcaster').textContent = broadcaster;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-duration').textContent = duration;
            document.getElementById('modal-size').textContent = size;
            
            // Reset video player first
            videoPlayer.pause();
            videoPlayer.currentTime = 0;
            videoSource.src = '';
            videoPlayer.load();
            
            // Detect format and create multiple sources for better compatibility
            const isWebM = replayUrl.includes('.webm');
            const isMP4 = replayUrl.includes('.mp4');
            
            // Clear existing sources
            while (videoPlayer.firstChild) {
                videoPlayer.removeChild(videoPlayer.firstChild);
            }
            
            if (isWebM) {
                // Add WebM source
                const webmSource = document.createElement('source');
                webmSource.src = replayUrl;
                webmSource.type = 'video/webm';
                videoPlayer.appendChild(webmSource);
                
                // Add MP4 fallback via Cloudinary transformation
                try {
                    const parts = replayUrl.split('/video/upload/');
                    if (parts.length === 2) {
                        const left = parts[0];
                        let right = parts[1];
                        right = right.replace(/\.webm($|\?)/, '.mp4$1');
                        const mp4Url = `${left}/video/upload/f_mp4,q_auto/${right}`;
                        
                        const mp4Source = document.createElement('source');
                        mp4Source.src = mp4Url;
                        mp4Source.type = 'video/mp4';
                        videoPlayer.appendChild(mp4Source);
                    }
                } catch (e) {
                    console.warn('Failed to create MP4 fallback:', e);
                }
            } else if (isMP4) {
                // Add MP4 source
                const mp4Source = document.createElement('source');
                mp4Source.src = replayUrl;
                mp4Source.type = 'video/mp4';
                videoPlayer.appendChild(mp4Source);
            } else {
                // Fallback to original method
                videoSource.src = replayUrl;
                videoSource.type = 'video/mp4';
            }
            
            // Add preload attribute
            videoPlayer.setAttribute('preload', 'auto');
            
            // Add error message fallback
            const errorMsg = document.createElement('p');
            errorMsg.textContent = 'Your browser does not support the video format. Please try a different browser.';
            errorMsg.style.color = 'white';
            errorMsg.style.textAlign = 'center';
            errorMsg.style.padding = '2rem';
            videoPlayer.appendChild(errorMsg);
            
            videoPlayer.load();
            
            // Try to play after load
            videoPlayer.addEventListener('loadedmetadata', function() {
                console.log('Video loaded successfully');
            }, { once: true });
            
            videoPlayer.addEventListener('waiting', function() {
                console.log('Video buffering...');
            });
            
            videoPlayer.addEventListener('canplay', function() {
                console.log('Video ready to play');
            });
            
            videoPlayer.addEventListener('error', function(e) {
                console.error('Video error:', e);
                alert('Cannot play video. Format may not be supported by your browser.');
            }, { once: true });
            
            // Show modal
            watchModal.modal('show');
        });
    });
    
    // Stop video when modal closes
    watchModal.on('hidden.bs.modal', function() {
        videoPlayer.pause();
        videoPlayer.currentTime = 0;
        videoSource.src = '';
    });
    
    // Handle delete button clicks
    document.querySelectorAll('.delete-stream').forEach(button => {
        button.addEventListener('click', function() {
            streamIdToDelete = this.dataset.streamId;
            const streamTitle = this.dataset.streamTitle;
            
            document.getElementById('streamTitle').textContent = streamTitle;
            deleteModal.modal('show');
        });
    });
    
    // Handle confirm delete
    document.getElementById('confirmDelete').addEventListener('click', async function() {
        if (!streamIdToDelete) return;
        
        const deleteBtn = this;
        const originalContent = deleteBtn.innerHTML;
        deleteBtn.disabled = true;
        deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Deleting...';
        
        try {
            const response = await fetch(`/admin/livestreams/${streamIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                deleteModal.modal('hide');
                // Show success message before reload
                if (typeof toastr !== 'undefined') {
                    toastr.success('Stream deleted successfully');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    location.reload();
                }
            } else {
                throw new Error(data.error || 'Failed to delete stream');
            }
        } catch (error) {
            console.error('Delete error:', error);
            alert('Failed to delete stream: ' + error.message);
            deleteBtn.disabled = false;
            deleteBtn.innerHTML = originalContent;
        }
    });
    
    // Reset delete button when modal closes
    deleteModal.on('hidden.bs.modal', function() {
        const deleteBtn = document.getElementById('confirmDelete');
        deleteBtn.disabled = false;
        deleteBtn.innerHTML = '<i class="fas fa-trash"></i> Delete Permanently';
        streamIdToDelete = null;
    });
    
    // Handle view viewers button clicks
    document.querySelectorAll('.view-viewers').forEach(button => {
        button.addEventListener('click', async function() {
            const streamId = this.dataset.streamId;
            const streamTitle = this.dataset.streamTitle;
            
            document.getElementById('viewersModalTitle').innerHTML = 
                `<i class="fas fa-users me-2"></i>Attendance: ${streamTitle}`;
            
            // Show modal and loading state
            viewersModal.modal('show');
            document.getElementById('viewers-loading').style.display = 'block';
            document.getElementById('viewers-content').style.display = 'none';
            document.getElementById('viewers-error').style.display = 'none';
            
            try {
                const response = await fetch(`/livekit/stream/${streamId}/viewers`);
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('total-viewers').textContent = data.total_count;
                    
                    const viewersList = document.getElementById('viewers-list');
                    viewersList.innerHTML = '';
                    
                    if (data.viewers.length === 0) {
                        viewersList.innerHTML = '<tr><td colspan="4" class="text-center">No viewers recorded</td></tr>';
                    } else {
                        data.viewers.forEach(viewer => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td><i class="fas fa-user-circle me-2"></i>${viewer.name}</td>
                                <td>${viewer.joined_at}</td>
                                <td>${viewer.left_at}</td>
                                <td>${viewer.duration}</td>
                            `;
                            viewersList.appendChild(row);
                        });
                    }
                    
                    document.getElementById('viewers-loading').style.display = 'none';
                    document.getElementById('viewers-content').style.display = 'block';
                } else {
                    throw new Error('Failed to load viewers');
                }
            } catch (error) {
                console.error('Error loading viewers:', error);
                document.getElementById('viewers-loading').style.display = 'none';
                document.getElementById('viewers-error').style.display = 'block';
            }
        });
    });
});
</script>


<link href="https://unpkg.com/cloudinary-video-player@1.9.4/dist/cld-video-player.min.css" rel="stylesheet">
<script src="https://unpkg.com/cloudinary-core@latest/cloudinary-core-shrinkwrap.min.js"></script>
<script src="https://unpkg.com/cloudinary-video-player@1.9.4/dist/cld-video-player.min.js"></script>

@endsection