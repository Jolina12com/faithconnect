@extends('admin.dashboard')
@section('content')
<div class="container py-4">
    <!-- Page Header with Stats -->
    <div class="row align-items-center mb-4 animate__animated animate__fadeIn">
        <div class="col-md-6">
            <p class="text-muted">Manage and organize your organization's events</p>
        </div>
        <div class="col-md-6 text-md-end">
            <div class="d-flex flex-wrap justify-content-md-end gap-2">
                <!-- Dynamic Create Button based on event type -->
                @if ($eventType === 'wedding')
                    <a href="{{ route('admin.events.create', ['type' => 'wedding']) }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i>
                        Schedule Wedding
                    </a>
                @elseif ($eventType === 'baptism')
                    <a href="{{ route('admin.events.create', ['type' => 'baptism']) }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i>
                        Schedule Baptism
                    </a>
                @else
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary d-flex align-items-center">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create Event
                    </a>
                @endif
                <button class="btn btn-outline-secondary d-flex align-items-center" id="filterButton">
                    <i class="bi bi-funnel me-2"></i>
                    Filter
                </button>
            </div>
        </div>
    </div>

    <!-- Event Type Tabs -->
    <div class="row mb-4">
        <div class="col-12">
            <ul class="nav nav-tabs nav-fill">
                <li class="nav-item">
                    <a class="nav-link {{ $eventType === 'all' ? 'active' : '' }}" href="{{ route('admin.events.index') }}">
                        <i class="bi bi-calendar-range me-2"></i>All Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $eventType === 'regular' ? 'active' : '' }}" href="{{ route('admin.events.index', ['type' => 'regular']) }}">
                        <i class="bi bi-calendar-event me-2"></i>Regular Events
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $eventType === 'wedding' ? 'active' : '' }}" href="{{ route('admin.events.index', ['type' => 'wedding']) }}">
                        <i class="bi bi-heart me-2"></i>Weddings
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $eventType === 'baptism' ? 'active' : '' }}" href="{{ route('admin.events.index', ['type' => 'baptism']) }}">
                        <i class="bi bi-water me-2"></i>Baptisms
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Filter Panel (Hidden by Default) -->
    <div class="row mb-4 d-none" id="filterPanel">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-3">
                    <form action="{{ route('admin.events.index') }}" method="GET" class="row g-3">
                        <div class="col-md-4">
                            <div class="form-floating">
                                <input type="text" class="form-control" id="searchKeyword" name="search" 
                                    placeholder="Search keywords" value="{{ $search ?? '' }}">
                                <label for="searchKeyword">
                                    <i class="bi bi-search me-1"></i>
                                    Search Keywords
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateFrom" name="date_from" 
                                    placeholder="From" value="{{ $dateFrom ?? '' }}">
                                <label for="dateFrom">
                                    <i class="bi bi-calendar-minus me-1"></i>
                                    From Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-floating">
                                <input type="date" class="form-control" id="dateTo" name="date_to" 
                                    placeholder="To" value="{{ $dateTo ?? '' }}">
                                <label for="dateTo">
                                    <i class="bi bi-calendar-plus me-1"></i>
                                    To Date
                                </label>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-center">
                            <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-primary text-white rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-event fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Total {{ ucfirst($eventType !== 'all' ? $eventType : 'Events') }}</h6>
                            <h3 class="fw-bold mb-0">{{ $events->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3 mb-md-0">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-success text-white rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Upcoming</h6>
                            <h3 class="fw-bold mb-0">{{ $events->where('event_date', '>=', date('Y-m-d'))->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center">
                        <div class="icon-box bg-info text-white rounded-3 p-3 me-3">
                            <i class="bi bi-calendar-x fs-4"></i>
                        </div>
                        <div>
                            <h6 class="text-muted mb-1">Past</h6>
                            <h3 class="fw-bold mb-0">{{ $events->where('event_date', '<', date('Y-m-d'))->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table Card -->
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
        <div class="card-header bg-gradient-primary text-white p-3">
            <div class="d-flex align-items-center">
                @if ($eventType === 'wedding')
                    <i class="bi bi-heart fs-4 me-2"></i>
                    <h4 class="card-title mb-0">Weddings</h4>
                @elseif ($eventType === 'baptism')
                    <i class="bi bi-water fs-4 me-2"></i>
                    <h4 class="card-title mb-0">Baptisms</h4>
                @else
                    <i class="bi bi-calendar-range fs-4 me-2"></i>
                    <h4 class="card-title mb-0">{{ $eventType === 'regular' ? 'Regular Events' : 'All Events' }}</h4>
                @endif
                <span class="badge bg-white text-primary rounded-pill ms-2">{{ $events->count() }}</span>
                <div class="ms-auto d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-sort-down me-1"></i>
                            Sort by: 
                            @switch($sort ?? 'date_desc')
                                @case('date_asc')
                                    Date (Oldest)
                                    @break
                                @case('title_asc')
                                    Title (A-Z)
                                    @break
                                @case('title_desc')
                                    Title (Z-A)
                                    @break
                                @default
                                    Date (Newest)
                            @endswitch
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                            <li>
                                <a class="dropdown-item {{ ($sort ?? 'date_desc') === 'date_desc' ? 'active' : '' }}" 
                                   href="{{ route('admin.events.index', array_merge(request()->except('sort'), ['sort' => 'date_desc', 'type' => $eventType])) }}">
                                   Date (Newest)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ ($sort ?? '') === 'date_asc' ? 'active' : '' }}" 
                                   href="{{ route('admin.events.index', array_merge(request()->except('sort'), ['sort' => 'date_asc', 'type' => $eventType])) }}">
                                   Date (Oldest)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ ($sort ?? '') === 'title_asc' ? 'active' : '' }}" 
                                   href="{{ route('admin.events.index', array_merge(request()->except('sort'), ['sort' => 'title_asc', 'type' => $eventType])) }}">
                                   Title (A-Z)
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item {{ ($sort ?? '') === 'title_desc' ? 'active' : '' }}" 
                                   href="{{ route('admin.events.index', array_merge(request()->except('sort'), ['sort' => 'title_desc', 'type' => $eventType])) }}">
                                   Title (Z-A)
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        @if ($events->isEmpty())
        <div class="card-body p-5 text-center">
            <div class="empty-state animate__animated animate__fadeIn">
                <div class="empty-state-icon mb-4">
                    <i class="bi bi-calendar-x fs-1 text-muted"></i>
                </div>
                @if ($eventType === 'wedding')
                    <h4>No Weddings Found</h4>
                    <p class="text-muted mb-4">There are no weddings scheduled yet. Create your first wedding to get started.</p>
                    <a href="{{ route('admin.events.create', ['type' => 'wedding']) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Schedule First Wedding
                    </a>
                @elseif ($eventType === 'baptism')
                    <h4>No Baptisms Found</h4>
                    <p class="text-muted mb-4">There are no baptisms scheduled yet. Create your first baptism to get started.</p>
                    <a href="{{ route('admin.events.create', ['type' => 'baptism']) }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Schedule First Baptism
                    </a>
                @else
                    <h4>No Events Found</h4>
                    <p class="text-muted mb-4">There are no events in the system yet. Create your first event to get started.</p>
                    <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        Create First Event
                    </a>
                @endif
            </div>
        </div>
        @else
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="eventsTable">
                    <thead class="table-light">
                        <tr>
                            <th class="px-4 py-3">Event Details</th>
                            <th class="px-4 py-3 d-none d-md-table-cell">Description</th>
                            <th class="px-4 py-3">Date & Time</th>
                            <th class="px-4 py-3 d-none d-md-table-cell">Location</th>
                            <!-- Show special columns for wedding and baptism types -->
                            @if ($eventType === 'wedding')
                                <th class="px-4 py-3 d-none d-md-table-cell">Couple</th>
                            @elseif ($eventType === 'baptism')
                                <th class="px-4 py-3 d-none d-md-table-cell">Person</th>
                            @endif
                            <th class="px-4 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($events as $event)
                        <tr>
                            <td class="px-4 py-3" data-label="Event">
                                <div class="d-flex align-items-center">
                                    <div class="event-color-dot me-3" style="background-color: {{ $event->color ?? '#3788d8' }}"></div>
                                    <div>
                                        <h6 class="mb-0 fw-medium">{{ $event->title }}</h6>
                                        <span class="d-md-none small text-muted">{{ $event->location }}</span>
                                        @if ($event->event_type !== 'regular')
                                            <span class="badge {{ $event->event_type === 'wedding' ? 'bg-danger' : 'bg-info' }} text-white">
                                                {{ ucfirst($event->event_type) }}
                                            </span>
                                        @endif
                                        @if (!empty($event->status))
                                            <span class="badge {{ $event->status === 'completed' ? 'bg-success' : ($event->status === 'cancelled' ? 'bg-danger' : 'bg-warning') }} text-white">
                                                {{ ucfirst($event->status) }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 d-none d-md-table-cell" data-label="Description">
                                <div class="text-truncate" style="max-width: 250px;">
                                    {{ $event->description ?: 'No description provided' }}
                                </div>
                            </td>
                            <td class="px-4 py-3" data-label="Date">
                                <div>
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-calendar2-event text-primary me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($event->event_date)->format('M d, Y') }}</span>
                                    </div>
                                    @if($event->event_time)
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-clock text-primary me-2"></i>
                                        <span>{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 d-none d-md-table-cell" data-label="Location">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt text-primary me-2"></i>
                                    {{ $event->location }}
                                </div>
                            </td>
                            <!-- Show special columns for wedding and baptism types -->
                            @if ($eventType === 'wedding')
                                <td class="px-4 py-3 d-none d-md-table-cell">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person-heart text-primary me-2"></i>
                                            <span>{{ $event->groom_name }} & {{ $event->bride_name }}</span>
                                        </div>
                                        @if($event->officiating_minister)
                                        <div class="d-flex align-items-center mt-1 small text-muted">
                                            <i class="bi bi-person-badge me-2"></i>
                                            <span>{{ $event->officiating_minister }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            @elseif ($eventType === 'baptism')
                                <td class="px-4 py-3 d-none d-md-table-cell">
                                    <div>
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-person text-primary me-2"></i>
                                            <span>{{ $event->person_name }}</span>
                                            @if($event->is_child)
                                                <span class="badge bg-info ms-2">Child</span>
                                            @else
                                                <span class="badge bg-secondary ms-2">Adult</span>
                                            @endif
                                        </div>
                                        @if($event->officiating_minister)
                                        <div class="d-flex align-items-center mt-1 small text-muted">
                                            <i class="bi bi-person-badge me-2"></i>
                                            <span>{{ $event->officiating_minister }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </td>
                            @endif
                            <td class="px-4 py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Edit Event">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info view-event-btn" data-bs-toggle="modal" data-bs-target="#viewEventModal" data-event-id="{{ $event->id }}" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                    @if($event->poll)
                                    <a href="{{ route('admin.events.responses', $event->id) }}" class="btn btn-sm btn-outline-success" data-bs-toggle="tooltip" title="View Poll Responses">
                                        <i class="bi bi-bar-chart"></i>
                                    </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-event-btn" data-bs-toggle="modal" data-bs-target="#deleteEventModal" data-event-id="{{ $event->id }}" data-event-title="{{ $event->title }}" title="Delete Event">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Shared Modal for View Event -->
<div class="modal fade" id="viewEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-info-circle me-2"></i>
                    Event Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="eventModalContent">
                    <!-- Content will be loaded dynamically -->
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="editEventLink" class="btn btn-primary">
                    <i class="bi bi-pencil me-2"></i>
                    Edit Event
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Shared Modal for Delete Event -->
<div class="modal fade" id="deleteEventModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <p class="fs-5">Are you sure you want to delete this event?</p>
                <div class="alert alert-warning d-flex" role="alert">
                    <div class="me-3">
                        <i class="bi bi-exclamation-circle fs-4"></i>
                    </div>
                    <div>
                        <p class="mb-0">This action cannot be undone. The event <strong id="eventTitleToDelete"></strong> will be permanently removed from the system.</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <form id="deleteEventForm" action="" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>
                        Delete Event
                    </button>
                </form>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Background Gradients */
    .bg-gradient-primary {
        background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
    }

    /* Event Color Indicator */
    .event-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    .event-color-indicator {
        height: 8px;
        border-radius: 4px;
        width: 100%;
    }

    /* Icon Box */
    .icon-box {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 48px;
        height: 48px;
    }

    /* Card enhancements */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }

    /* Rounded corners */
    .rounded-4 {
        border-radius: 10px;
    }

    /* Empty State */
    .empty-state {
        padding: 2rem 1rem;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background-color: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }

    /* Table row active state */
    .table > tbody > tr:hover {
        background-color: #f8f9fa;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .table-responsive {
            border: 0;
        }

        .pagination {
            justify-content: center;
        }
        
        /* Mobile card layout for table */
        .table thead {
            display: none;
        }
        
        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }
        
        .table tr {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table td {
            border: none;
            padding: 0.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }
        
        .table td:before {
            content: attr(data-label);
            font-weight: bold;
            color: #6c757d;
            flex: 0 0 30%;
            margin-right: 1rem;
        }
        
        .table td:nth-child(1):before { content: "Event: "; }
        .table td:nth-child(2):before { content: "Description: "; }
        .table td:nth-child(3):before { content: "Date: "; }
        .table td:nth-child(4):before { content: "Location: "; }
        .table td:nth-child(5):before { content: "Details: "; }
        .table td:nth-child(6):before { content: "Actions: "; }
        
        .table td:last-child {
            justify-content: center;
        }
        
        .text-truncate {
            max-width: none !important;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }
    }
    
    @media (max-width: 576px) {
        .nav-tabs .nav-link {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .nav-tabs .nav-link i {
            display: none;
        }
        
        .icon-box {
            width: 40px;
            height: 40px;
        }
        
        .card-body {
            padding: 1rem;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        new bootstrap.Tooltip(tooltip);
    });

    // Filter panel toggle
    const filterButton = document.getElementById('filterButton');
    const filterPanel = document.getElementById('filterPanel');

    if (filterButton && filterPanel) {
        filterButton.addEventListener('click', function() {
            filterPanel.classList.toggle('d-none');

            // Animate the panel
            if (!filterPanel.classList.contains('d-none')) {
                filterPanel.classList.add('animate__animated', 'animate__fadeIn');
            }
        });
    }

    // Set default dates for date filters if empty
    const today = new Date();
    const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDayOfMonth = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const dateFromInput = document.getElementById('dateFrom');
    const dateToInput = document.getElementById('dateTo');

    if (dateFromInput && !dateFromInput.value) {
        dateFromInput.valueAsDate = firstDayOfMonth;
    }

    if (dateToInput && !dateToInput.value) {
        dateToInput.valueAsDate = lastDayOfMonth;
    }

    // Handle view event modal
    const viewEventButtons = document.querySelectorAll('.view-event-btn');
    viewEventButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            const eventModalContent = document.getElementById('eventModalContent');
            const editEventLink = document.getElementById('editEventLink');
            
            // Update the edit link
            editEventLink.href = `/admin/events/${eventId}/edit`;
            
            // Get the parent row
            const row = this.closest('tr');
            
            // Correctly extract event data from the table row
            const event = {
                title: row.querySelector('.fw-medium').textContent.trim(),
                color: row.querySelector('.event-color-dot').style.backgroundColor,
                date: row.querySelector('.bi-calendar2-event').nextElementSibling.textContent.trim(),
                location: '',
                description: ''
            };
            
            // Handle time - it might not always be present
            const timeElement = row.querySelector('.bi-clock');
            event.time = timeElement && timeElement.nextElementSibling ? timeElement.nextElementSibling.textContent.trim() : '';
            
            // Get location - check both mobile and desktop versions
            const locationCell = row.querySelector('.d-none.d-md-table-cell .bi-geo-alt');
            if (locationCell && locationCell.nextElementSibling) {
                event.location = locationCell.nextElementSibling.textContent.trim();
            } else {
                // Try the mobile version
                const mobileLocation = row.querySelector('.d-md-none.small.text-muted');
                event.location = mobileLocation ? mobileLocation.textContent.trim() : 'No location specified';
            }
            
            // Get description
            const descriptionCell = row.querySelector('.text-truncate');
            event.description = descriptionCell ? descriptionCell.textContent.trim() : 'No description provided';
            
            // Build the HTML
            let html = `
                <div class="event-color-indicator mb-3" style="background-color: ${event.color}"></div>
                <h4 class="mb-3">${event.title}</h4>

                <div class="d-flex mb-3">
                    <div class="text-muted me-3">
                        <i class="bi bi-calendar3 fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Date & Time</h6>
                        <p class="mb-0">
                            ${event.date}
                            ${event.time ? ' at ' + event.time : ''}
                        </p>
                    </div>
                </div>

                <div class="d-flex mb-3">
                    <div class="text-muted me-3">
                        <i class="bi bi-geo-alt fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Location</h6>
                        <p class="mb-0">${event.location}</p>
                    </div>
                </div>

                <div class="d-flex">
                    <div class="text-muted me-3">
                        <i class="bi bi-card-text fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-1">Description</h6>
                        <p class="mb-0">${event.description}</p>
                    </div>
                </div>
            `;
            
            eventModalContent.innerHTML = html;
        });
    });

    // Handle delete event modal
    const deleteEventButtons = document.querySelectorAll('.delete-event-btn');
    deleteEventButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            const eventTitle = this.getAttribute('data-event-title');
            const deleteEventForm = document.getElementById('deleteEventForm');
            const eventTitleToDelete = document.getElementById('eventTitleToDelete');
            
            // Update form action and event title
            deleteEventForm.action = `/admin/events/${eventId}`;
            eventTitleToDelete.textContent = eventTitle;
        });
    });

    // Handle filter panel toggle
    const filterButton = document.getElementById('filterButton');
    const filterPanel = document.getElementById('filterPanel');
    
    // Show filter panel if any filters are active
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('search') || urlParams.has('date_from') || urlParams.has('date_to')) {
        filterPanel.classList.remove('d-none');
    }
    
    filterButton.addEventListener('click', () => {
        filterPanel.classList.toggle('d-none');
    });
});
</script>
@endsection