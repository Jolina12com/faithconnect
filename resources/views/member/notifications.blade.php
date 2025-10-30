@extends('member.dashboard_member')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
            <h4 class="mb-0 fw-bold"><i class="fas fa-bell me-2"></i>Notifications</h4>
            @if(Auth::user()->unreadNotifications->isNotEmpty())
                <span class="badge bg-danger rounded-pill">{{ Auth::user()->unreadNotifications->count() }}</span>
            @endif
        </div>

        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(Auth::user()->unreadNotifications->isEmpty() && Auth::user()->readNotifications->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-bell-slash fs-1 text-muted"></i>
                    <p class="mt-3 text-muted">No notifications available.</p>
                </div>
            @else
                <ul class="nav nav-pills mb-3">
                    <li class="nav-item">
                        <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#unread">
                            Unread
                            @if(Auth::user()->unreadNotifications->isNotEmpty())
                                <span class="badge bg-danger ms-1">{{ Auth::user()->unreadNotifications->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#read">
                            Read
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="unread">
                        @if(Auth::user()->unreadNotifications->isEmpty())
                            <div class="text-center py-3">
                                <i class="fas fa-check-circle fs-3 text-success"></i>
                                <p class="mt-2 text-muted">All caught up!</p>
                            </div>
                        @else
                            @foreach(Auth::user()->unreadNotifications as $notification)
                                <div class="notification-item p-3 border-bottom clickable-notification" 
                                     data-notification-id="{{ $notification->id }}"
                                     data-notification-type="{{ $notification->data['type'] ?? 'general' }}"
                                     style="cursor: pointer;">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            @if($notification->data['type'] === 'event')
                                                <i class="fas fa-calendar text-primary fs-4"></i>
                                            @elseif($notification->data['type'] === 'sermon')
                                                <i class="fas fa-microphone text-info fs-4"></i>
                                            @elseif($notification->data['type'] === 'announcement')
                                                <i class="fas fa-bullhorn text-warning fs-4"></i>
                                            @else
                                                <i class="fas fa-bell text-secondary fs-4"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $notification->data['title'] ?? $notification->data['message'] }}</h6>
                                            @if(isset($notification->data['message']) && $notification->data['message'] !== ($notification->data['title'] ?? ''))
                                                <p class="text-muted mb-2">{{ $notification->data['message'] }}</p>
                                            @endif
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                        <div class="ms-2">
                                            <form action="{{ route('notifications.markAsRead', $notification->id) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation();">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>

                    <div class="tab-pane fade" id="read">
                        @if(Auth::user()->readNotifications->isEmpty())
                            <div class="text-center py-3">
                                <i class="fas fa-inbox fs-3 text-muted"></i>
                                <p class="mt-2 text-muted">No read notifications.</p>
                            </div>
                        @else
                            @foreach(Auth::user()->readNotifications as $notification)
                                <div class="notification-item p-3 border-bottom clickable-notification opacity-75" 
                                     data-notification-id="{{ $notification->id }}"
                                     data-notification-type="{{ $notification->data['type'] ?? 'general' }}"
                                     style="cursor: pointer;">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            @if($notification->data['type'] === 'event')
                                                <i class="fas fa-calendar text-muted fs-4"></i>
                                            @elseif($notification->data['type'] === 'sermon')
                                                <i class="fas fa-microphone text-muted fs-4"></i>
                                            @elseif($notification->data['type'] === 'announcement')
                                                <i class="fas fa-bullhorn text-muted fs-4"></i>
                                            @else
                                                <i class="fas fa-bell text-muted fs-4"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-muted">{{ $notification->data['title'] ?? $notification->data['message'] }}</h6>
                                            @if(isset($notification->data['message']) && $notification->data['message'] !== ($notification->data['title'] ?? ''))
                                                <p class="text-muted mb-2">{{ $notification->data['message'] }}</p>
                                            @endif
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i>
                                                {{ $notification->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .notification-item:hover {
        background-color: rgba(0, 123, 255, 0.05);
        transition: all 0.2s ease;
    }
    .clickable-notification {
        transition: all 0.2s ease;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .card-header h4 {
            margin-bottom: 0.5rem;
        }
        
        .nav-pills {
            flex-wrap: wrap;
        }
        
        .nav-pills .nav-item {
            margin-bottom: 0.5rem;
        }
        
        .notification-item {
            padding: 1rem !important;
        }
        
        .notification-item .d-flex {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .notification-item .me-3 {
            margin-right: 0 !important;
            margin-bottom: 0.5rem;
            align-self: center;
        }
        
        .notification-item .ms-2 {
            margin-left: 0 !important;
            margin-top: 0.5rem;
            align-self: center;
        }
        
        .notification-item .flex-grow-1 {
            text-align: center;
            width: 100%;
        }
    }
    
    @media (max-width: 576px) {
        .card {
            margin: 0 -0.5rem;
        }
        
        .notification-item h6 {
            font-size: 1rem;
        }
        
        .notification-item p {
            font-size: 0.9rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.clickable-notification').forEach(function(notification) {
            notification.addEventListener('click', function() {
                const notificationId = this.dataset.notificationId;
                window.location.href = `/notifications/${notificationId}/redirect`;
            });
        });
    });
</script>
@endsection