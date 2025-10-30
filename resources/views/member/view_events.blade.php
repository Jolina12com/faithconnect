@extends('member.dashboard_member')

@section('content')
<div class="container py-5">
    <!-- Page Title with Subtle Animation -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex align-items-center">
                <div class="ms-auto d-flex align-items-center">
                    <span class="badge bg-primary rounded-pill px-3 py-2 d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2"></i>
                        {{ $events->count() }} Events
                    </span>
                </div>
            </div>
            <hr class="mt-3 mb-0">
        </div>
    </div>

    <!-- Calendar Card with Enhanced Styling -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-calendar-week fs-4 me-2"></i>
                        <h4 class="card-title mb-0 fw-bold">Event Calendar</h4>
                        <div class="ms-auto d-none d-md-block">
                            <small class="text-white-50">Browse and plan your upcoming events</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0 p-md-4">
                    <div id="calendar"
                         class="fc-theme-standard"
                         data-events="{{ json_encode($events->map(function($e) {
                             return [
                                 'id' => $e->id,
                                 'title' => $e->title,
                                 'start' => $e->event_date,
                                 'description' => $e->description,
                                 'location' => $e->location,
                                 'backgroundColor' => $e->color ?? '#3788d8',
                                 'borderColor' => $e->color ?? '#3788d8',
                                 'has_poll' => $e->poll ? true : false
                             ];
                         })) }}">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Upcoming Events Table with Enhanced Styling -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-secondary p-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-list-ul fs-4 me-2"></i>
                        <h4 class="card-title mb-0 fw-bold">Upcoming Events</h4>
                        <div class="ms-auto">
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="eventSearch" placeholder="Search events...">
                                <span class="input-group-text bg-white">
                                    <i class="bi bi-search"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="eventsTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">Title</th>
                                    <th class="px-4 py-3 d-none d-md-table-cell">Description</th>
                                    <th class="px-4 py-3">Date</th>
                                    <th class="px-4 py-3">Location</th>
                                    <th class="px-4 py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($events as $event)
                                    <tr class="event-row">
                                        <td class="px-4 py-3" data-label="Title">
                                            <div class="d-flex align-items-center">
                                                <div class="event-color-dot" style="background-color: {{ $event->color ?? '#3788d8' }}"></div>
                                                <span class="ms-2 fw-medium">{{ $event->title }}</span>
                                                @if($event->poll)
                                                    <span class="badge bg-info rounded-pill ms-2">
                                                        <i class="bi bi-bar-chart-fill"></i>
                                                    </span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 d-none d-md-table-cell" data-label="Description">
                                            <div class="text-truncate" style="max-width: 300px;">
                                                {{ $event->description }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3" data-label="Date">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar2-event text-muted me-2"></i>
                                                <p class="mb-0">
                                                    {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                                                    @if($event->event_time)
                                                        <br><small>{{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}</small>
                                                    @endif
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3" data-label="Location">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-geo-alt text-muted me-2"></i>
                                                <span class="text-break">{{ $event->location }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center" data-label="Actions">
                                            <button type="button" class="btn btn-sm btn-outline-primary view-event" 
                                                data-event-id="{{ $event->id }}"
                                                data-bs-toggle="modal"
                                                data-bs-target="#eventDetailModal"
                                                data-has-poll="{{ $event->poll ? '1' : '0' }}"
                                                @if($event->poll)
                                                data-poll-deadline="{{ $event->poll->deadline ?? '' }}"
                                                data-allow-comments="{{ $event->poll->allow_comments ? '1' : '0' }}"
                                                @endif
                                                >
                                                <i class="bi bi-eye"></i> <span class="d-md-none">View Details</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="bi bi-calendar-x fs-1 text-muted mb-3"></i>
                                                <h5>No Events Found</h5>
                                                <p class="text-muted">There are no upcoming events scheduled at this time.</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Inside your member view, replace the event detail modal section -->
<div class="modal fade" id="eventDetailModal" tabindex="-1" aria-labelledby="eventDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="eventDetailModalLabel">Event Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="eventDetails">
                    <!-- Loading indicator -->
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Loading event details...</p>
                    </div>
                </div>
                
                <!-- Attendance Poll Section -->
                <div id="eventPollSection" class="mt-4 pt-3 border-top d-none">
                    <form id="pollResponseForm" action="{{ route('member.events.poll.submit', ['id' => ':event_id']) }}" method="POST">
                        @csrf
                        <input type="hidden" id="eventId" name="event_id">
                        <input type="hidden" id="pollId" name="poll_id">
                        
                        <h5 class="d-flex align-items-center mb-3">
                            <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                            Attendance Poll
                        </h5>
                        
                        <div class="card border shadow-sm rounded-3 mb-3">
                            <div class="card-body">
                                <div id="pollOptions" class="mb-3">
                                    <!-- Poll options will be inserted here -->
                                </div>
                                
                                <div id="pollDeadline" class="small text-muted mb-3">
                                    <!-- Deadline info will be inserted here -->
                                </div>
                                
                                <!-- Comments Section -->
                                <div id="commentsSection" class="mb-3 d-none">
                                    <label for="comment" class="form-label">Add a comment (optional)</label>
                                    <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="poll-stats small">
                                        <span id="responseCount" class="text-muted">0 responses so far</span>
                                    </div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-check2-circle me-1"></i>
                                        Submit Response
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Styles remain unchanged -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>
    /* All the styles from the original file remain here */
    /* Custom Background Gradients */
    .bg-gradient-primary {
        background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
    }

    .bg-gradient-secondary {
        background: linear-gradient(120deg, #36b9cc 0%, #1a8a9e 100%);
    }

    /* Event Color Dot */
    .event-color-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
    }

    /* Calendar Enhancements */
    .fc-theme-standard .fc-toolbar-title {
        font-size: 1.5rem;
        font-weight: bold;
    }

    .fc .fc-button-primary {
        background-color: #4e73df;
        border-color: #4e73df;
        box-shadow: none;
    }

    .fc .fc-button-primary:hover {
        background-color: #224abe;
        border-color: #224abe;
    }

    .fc .fc-button-primary:disabled {
        background-color: #6c87e6;
        border-color: #6c87e6;
    }

    .fc-daygrid-day-number {
        font-weight: 500;
    }

    .fc-event {
        border-radius: 4px;
        padding: 2px 4px;
        font-size: 0.8rem;
        transition: transform 0.15s ease;
    }

    .fc-event:hover {
        transform: translateY(-1px);
    }

    /* Empty State Styling */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 2rem;
    }

    /* Table Enhancements */
    .table > :not(caption) > * > * {
        padding: 0.75rem 1rem;
    }

    /* Poll Option Styles */
    .poll-option {
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .poll-option:hover {
        background-color: rgba(78, 115, 223, 0.05);
    }

    .poll-option.selected {
        background-color: rgba(78, 115, 223, 0.1);
        border-left: 3px solid #4e73df;
    }

    /* Attendance List Styles */
    .poll-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.35em 0.65em;
        font-size: 0.75em;
        font-weight: 700;
        border-radius: 0.25rem;
    }

    .badge-attending {
        background-color: #28a745;
        color: white;
    }

    .badge-maybe {
        background-color: #ffc107;
        color: #212529;
    }

    .badge-not-attending {
        background-color: #dc3545;
        color: white;
    }

    .badge-custom {
        background-color: #6c757d;
        color: white;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .fc .fc-toolbar {
            flex-direction: column;
            gap: 0.5rem;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
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
            flex: 0 0 25%;
            margin-right: 1rem;
        }
        
        .table td:nth-child(1):before { content: "Title: "; }
        .table td:nth-child(2):before { content: "Description: "; }
        .table td:nth-child(3):before { content: "Date: "; }
        .table td:nth-child(4):before { content: "Location: "; }
        .table td:nth-child(5):before { content: "Actions: "; }
        
        .table td:last-child {
            justify-content: center;
        }
        
        .text-truncate {
            max-width: none !important;
            white-space: normal;
            overflow: visible;
            text-overflow: unset;
        }
        
        .card-header .input-group {
            margin-top: 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .modal-dialog {
            margin: 0.5rem;
        }
    }

    /* Animation for hover effects */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }

    /* Card header icons */
    .card-header i {
        opacity: 0.8;
    }
    
    /* Poll animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .fade-in {
        animation: fadeIn 0.4s ease;
    }
</style>

<!-- JavaScript for handling events and polls -->
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>

<script>document.addEventListener('DOMContentLoaded', function () {
    // Initialize Bootstrap Tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Initialize Bootstrap Modal
    const eventDetailModal = document.getElementById('eventDetailModal');
    let eventModal = null;
    if (eventDetailModal) {
        eventModal = new bootstrap.Modal(eventDetailModal);
    }

    // Initialize FullCalendar
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;

    const events = JSON.parse(calendarEl.dataset.events);

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: window.innerWidth < 768 ? 'listMonth' : 'dayGridMonth',
        height: 'auto',
        events: events,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,listMonth'
        },
        buttonText: {
            today: 'Today',
            month: 'Month',
            week: 'Week',
            list: 'List'
        },
        aspectRatio: 1.8,
        contentHeight: 'auto',
        windowResize: function() {
            if (window.innerWidth < 768) {
                calendar.changeView('listMonth');
            } else {
                calendar.changeView('dayGridMonth');
            }
            calendar.updateSize();
        },
        eventClick: function(info) {
            if (eventModal) {
                fetchEventDetails(info.event.id);
                eventModal.show();
            }
        },
        eventTimeFormat: {
            hour: '2-digit',
            minute: '2-digit',
            meridiem: 'short'
        },
        eventDidMount: function(info) {
            // Add tooltips
            const tooltip = new bootstrap.Tooltip(info.el, {
                title: info.event.title + ' - ' + info.event.extendedProps.location,
                placement: 'top',
                trigger: 'hover',
                container: 'body'
            });
        }
    });

    calendar.render();

    // Search functionality
    const searchInput = document.getElementById('eventSearch');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('#eventsTable tbody tr.event-row');

            rows.forEach(row => {
                const textContent = row.textContent.toLowerCase();
                if (textContent.includes(searchTerm)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    }

    // Event detail view buttons
    const viewButtons = document.querySelectorAll('.view-event');
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const eventId = this.getAttribute('data-event-id');
            fetchEventDetails(eventId);
        });
    });

    // Function to fetch event details via AJAX
    function fetchEventDetails(eventId) {
        // Reset event details to loading state
        const eventDetails = document.getElementById('eventDetails');
        if (!eventDetails) return;
        
        eventDetails.innerHTML = `
            <div class="text-center py-4">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Loading event details...</p>
            </div>
        `;
        
        // Hide poll section until we know if this event has a poll
        const pollSection = document.getElementById('eventPollSection');
        if (pollSection) {
            // Reset the poll section structure before hiding
            resetPollSectionStructure(pollSection, eventId);
            pollSection.classList.add('d-none');
        }
        
        fetch(`/member/events/${eventId}`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            if (data.event) {
                showEventDetails(data.event);
                
                // Check if event has a poll and fetch poll details if needed
                if (data.event.poll || data.poll) {
                    try {
                        fetchPollDetails(eventId);
                    } catch (error) {
                        console.error('Error in fetchPollDetails:', error);
                        // Don't let poll errors break the entire event display
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error fetching event details:', error);
            if (eventDetails) {
                eventDetails.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading event details. Please try again.
                    </div>
                `;
            }
        });
    }

    // Function to reset the poll section structure
    function resetPollSectionStructure(pollSection, eventId) {
        pollSection.innerHTML = `
            <form id="pollResponseForm" action="/member/events/${eventId}/poll-response" method="POST">
                @csrf
                <input type="hidden" id="eventId" name="event_id" value="${eventId}">
                <input type="hidden" id="pollId" name="poll_id">
                
                <h5 class="d-flex align-items-center mb-3">
                    <i class="bi bi-bar-chart-fill text-primary me-2"></i>
                    Attendance Poll
                </h5>
                
                <div class="card border shadow-sm rounded-3 mb-3">
                    <div class="card-body">
                        <div id="pollOptions" class="mb-3">
                            <!-- Poll options will be inserted here -->
                            <div class="text-center">
                                <div class="spinner-border spinner-border-sm text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <span class="ms-2">Loading poll options...</span>
                            </div>
                        </div>
                        
                        <div id="pollDeadline" class="small text-muted mb-3">
                            <!-- Deadline info will be inserted here -->
                        </div>
                        
                        <!-- Comments Section -->
                        <div id="commentsSection" class="mb-3 d-none">
                            <label for="comment" class="form-label">Add a comment (optional)</label>
                            <textarea class="form-control" id="comment" name="comment" rows="2"></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="poll-stats small">
                                <span id="responseCount" class="text-muted">0 responses so far</span>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check2-circle me-1"></i>
                                Submit Response
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        `;
    }

    function showEventDetails(event) {
        const eventDetails = document.getElementById('eventDetails');
        if (!eventDetails) return;
        
        const date = new Date(event.event_date || event.start);

        // Format date nicely
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const formattedDate = date.toLocaleDateString('en-US', options);

        let timeInfo = '';
        if (event.event_time) {
            const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };
            timeInfo = `<div class="mb-3">
                <i class="bi bi-clock me-2"></i>
                <strong>Time:</strong> ${event.event_time}
            </div>`;
        }

        eventDetails.innerHTML = `
            <h4 class="text-primary mb-3">${event.title}</h4>
            <div class="mb-3">
                <i class="bi bi-calendar-date me-2"></i>
                <strong>Date:</strong> ${formattedDate}
            </div>
            ${timeInfo}
            <div class="mb-3">
                <i class="bi bi-geo-alt me-2"></i>
                <strong>Location:</strong> ${event.location || 'No location specified'}
            </div>
            <div class="mb-0">
                <i class="bi bi-info-circle me-2"></i>
                <strong>Description:</strong>
                <p class="mt-2">${event.description || 'No description available'}</p>
            </div>
        `;

        // Handle Poll Section
        const pollSection = document.getElementById('eventPollSection');
        if (pollSection) {
            if (event.poll) {
                pollSection.classList.remove('d-none');
            } else {
                pollSection.classList.add('d-none');
            }
        }
    }

    // Function to fetch poll details
    function fetchPollDetails(eventId) {
        // Show poll section
        const pollSection = document.getElementById('eventPollSection');
        if (!pollSection) {
            console.error('Poll section element not found');
            return;
        }
        
        pollSection.classList.remove('d-none');
        
        // Verify the form structure is reset and set event ID
        const eventIdField = document.getElementById('eventId');
        if (!eventIdField) {
            console.error('Event ID field not found');
            // Instead of just returning, let's reset the structure
            resetPollSectionStructure(pollSection, eventId);
            return;
        }
        
        eventIdField.value = eventId;
        
        // Check for loading status in the poll options area only
        const pollOptionsContainer = document.getElementById('pollOptions');
        if (pollOptionsContainer) {
            pollOptionsContainer.innerHTML = `
                <div class="text-center py-2">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading poll options...</p>
                </div>
            `;
        }
        
        // Fetch poll data from the server
        fetch(`/member/events/${eventId}/poll`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            if (data.poll) {
                // Double-check for poll ID field again
                const pollIdField = document.getElementById('pollId');
                if (!pollIdField) {
                    console.error('Poll ID field not found after fetch');
                    // Reset structure and try again
                    resetPollSectionStructure(pollSection, eventId);
                    
                    // Now try to find the field again
                    const newPollIdField = document.getElementById('pollId');
                    if (!newPollIdField) {
                        throw new Error('Cannot find or create poll ID field');
                    } else {
                        // Set poll ID
                        newPollIdField.value = data.poll.id;
                    }
                } else {
                    // Set poll ID
                    pollIdField.value = data.poll.id;
                }
                
                // Update form action (find it again to be safe)
                const form = document.getElementById('pollResponseForm');
                if (!form) {
                    console.error('Poll response form not found');
                    return;
                }
                
                // Update form action
                form.action = `/member/events/${eventId}/poll-response`;
                
                // Find poll options container again to be safe
                const pollOptionsContainer = document.getElementById('pollOptions');
                if (!pollOptionsContainer) {
                    console.error('Poll options container not found');
                    return;
                }
                
                pollOptionsContainer.innerHTML = '';
                
                // Check if poll has options
                if (!data.poll.options || !Array.isArray(data.poll.options) || data.poll.options.length === 0) {
                    pollOptionsContainer.innerHTML = `
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            No poll options available.
                        </div>
                    `;
                    return;
                }
                
                data.poll.options.forEach(option => {
                    // Ensure option has required properties
                    if (!option || !option.id) {
                        console.warn('Skipping invalid poll option:', option);
                        return;
                    }
                    
                    const optionValue = option.option_value || 'custom';
                    const optionText = option.option_text || 'Option';
                    
                    const badgeClass = getBadgeClassForOption(optionValue);
                    const iconClass = getIconClassForOption(optionValue);
                    
                    pollOptionsContainer.innerHTML += `
                        <div class="form-check mb-3 poll-option p-2">
                            <input class="form-check-input" type="radio" name="option_id" id="opt_${option.id}" value="${option.id}">
                            <label class="form-check-label d-flex align-items-center" for="opt_${option.id}">
                                <span class="poll-badge ${badgeClass} me-2">
                                    <i class="bi ${iconClass} me-1"></i>${optionText}
                                </span>
                            </label>
                        </div>
                    `;
                });
                
                // Show/hide comments section - find it again to be safe
                const commentsSection = document.getElementById('commentsSection');
                if (commentsSection) {
                    if (data.poll.allow_comments) {
                        commentsSection.classList.remove('d-none');
                    } else {
                        commentsSection.classList.add('d-none');
                    }
                }
                
                // Show poll deadline if exists - find it again to be safe
                const pollDeadlineContainer = document.getElementById('pollDeadline');
                if (pollDeadlineContainer) {
                    if (data.poll.deadline) {
                        try {
                            const deadline = new Date(data.poll.deadline);
                            const formattedDeadline = deadline.toLocaleDateString('en-US', {
                                year: 'numeric', month: 'long', day: 'numeric'
                            });
                            pollDeadlineContainer.innerHTML = `
                                <i class="bi bi-calendar-check me-1"></i>
                                Please respond by: <strong>${formattedDeadline}</strong>
                            `;
                        } catch (e) {
                            console.error('Error formatting deadline date:', e);
                            pollDeadlineContainer.innerHTML = '';
                        }
                    } else {
                        pollDeadlineContainer.innerHTML = '';
                    }
                }
                
                // Update response count - find it again to be safe
                const responseCount = document.getElementById('responseCount');
                if (responseCount) {
                    const totalResponses = Object.values(data.poll.responseCounts || {}).reduce((a, b) => a + b, 0);
                    responseCount.textContent = `${totalResponses} ${totalResponses === 1 ? 'response' : 'responses'} so far`;
                }
                
                // If user has already responded, check their option
                if (data.userResponse) {
                    const radio = document.querySelector(`input[name="option_id"][value="${data.userResponse.option_id}"]`);
                    if (radio) {
                        radio.checked = true;
                        const pollOption = radio.closest('.poll-option');
                        if (pollOption) {
                            pollOption.classList.add('selected');
                        }
                    }
                    
                    const commentField = document.getElementById('comment');
                    if (commentField && data.userResponse.comment) {
                        commentField.value = data.userResponse.comment;
                    }
                }
                
                // Add event listeners to poll options for styling
                document.querySelectorAll('.poll-option').forEach(option => {
                    option.addEventListener('click', function(e) {
                        const radio = this.querySelector('input[type="radio"]');
                        if (radio && !e.target.matches('input')) {
                            radio.checked = true;
                            
                            // Remove selected class from all options
                            document.querySelectorAll('.poll-option').forEach(opt => {
                                opt.classList.remove('selected');
                            });
                            
                            // Add selected class to this option
                            this.classList.add('selected');
                        }
                    });
                });
            } else {
                // Show a message when no poll exists
                pollSection.innerHTML = `
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        No poll is available for this event.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error fetching poll details:', error);
            // Keep the structure but show an error message
            const pollOptionsContainer = document.getElementById('pollOptions');
            if (pollOptionsContainer) {
                pollOptionsContainer.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading poll options: ${error.message}
                    </div>
                `;
            }
        });
    }

    // Helper functions for poll badges
    function getBadgeClassForOption(value) {
        if (!value) return 'badge-custom';
        
        switch(value) {
            case 'attending': return 'badge-attending';
            case 'maybe': return 'badge-maybe';
            case 'not_attending': return 'badge-not-attending';
            default: return 'badge-custom';
        }
    }
    
    function getIconClassForOption(value) {
        if (!value) return 'bi-chat-text';
        
        switch(value) {
            case 'attending': return 'bi-check-circle';
            case 'maybe': return 'bi-question-circle';
            case 'not_attending': return 'bi-x-circle';
            default: return 'bi-chat-text';
        }
    }
    
    // Modify the form submission handler to keep the same behavior
    document.addEventListener('click', function(e) {
        // Find the nearest submit button
        const submitButton = e.target.closest('button[type="submit"]');
        if (!submitButton) return;
        
        // Find the form that contains this button
        const form = submitButton.closest('form');
        if (!form || form.id !== 'pollResponseForm') return;
        
        // Now we handle the submit event
        e.preventDefault();
        
        // Check if an option is selected
        const selectedOption = form.querySelector('input[name="option_id"]:checked');
        if (!selectedOption) {
            alert('Please select an option before submitting.');
            return;
        }
        
        // Form data for submission
        const formData = new FormData(form);
        
        // Show loading state
        const originalButtonText = submitButton.innerHTML;
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            Submitting...
        `;
        
        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            credentials: 'same-origin'
        })
        .then(response => {
            if (!response.ok) {
                return response.text().then(text => {
                    try {
                        const data = JSON.parse(text);
                        throw new Error(data.message || 'Server error');
                    } catch (e) {
                        throw new Error(text || 'Server error');
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                // Show success message
                const alertDiv = document.createElement('div');
                alertDiv.className = 'alert alert-success alert-dismissible fade show mt-3';
                alertDiv.innerHTML = `
                    <i class="bi bi-check-circle-fill me-2"></i>
                    ${data.message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                `;
                form.prepend(alertDiv);
                
                // Update response count
                if (data.poll && data.poll.responseCounts) {
                    const responseCount = document.getElementById('responseCount');
                    if (responseCount) {
                        const totalResponses = Object.values(data.poll.responseCounts).reduce((a, b) => a + b, 0);
                        responseCount.textContent = `${totalResponses} ${totalResponses === 1 ? 'response' : 'responses'} so far`;
                    }
                }

                // Update the selected option styling
                const selectedOption = form.querySelector('input[name="option_id"]:checked');
                if (selectedOption) {
                    // Remove selected class from all options
                    document.querySelectorAll('.poll-option').forEach(opt => {
                        opt.classList.remove('selected');
                    });
                    
                    // Add selected class to the chosen option
                    const pollOption = selectedOption.closest('.poll-option');
                    if (pollOption) {
                        pollOption.classList.add('selected');
                    }
                }
            } else {
                throw new Error(data.message || 'Failed to submit response');
            }
        })
        .catch(error => {
            console.error('Error submitting poll response:', error);
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show mt-3';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                Error: ${error.message || 'Failed to submit your response. Please try again.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            form.prepend(alertDiv);
        })
        .finally(() => {
            // Restore button state
            submitButton.disabled = false;
            submitButton.innerHTML = originalButtonText;
            
            // Scroll to top of form to show alert
            form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        });
    });
});
</script>
@endsection