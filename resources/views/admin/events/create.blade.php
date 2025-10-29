@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <!-- Page Header with Animation -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    @if ($eventType === 'wedding')
                        <h1 class="fw-bold text-primary mb-1">Schedule New Wedding</h1>
                        <p class="text-muted">Schedule and manage a new wedding ceremony</p>
                    @elseif ($eventType === 'baptism')
                        <h1 class="fw-bold text-primary mb-1">Schedule New Baptism</h1>
                        <p class="text-muted">Schedule and manage a new baptism ceremony</p>
                    @else
                        <h1 class="fw-bold text-primary mb-1">Add New Event</h1>
                        <p class="text-muted">Create and schedule a new event for your members</p>
                    @endif
                </div>
                <div class="ms-auto">
                    @if ($eventType === 'wedding')
                        <a href="{{ route('admin.events.index', ['type' => 'wedding']) }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Weddings
                        </a>
                    @elseif ($eventType === 'baptism')
                        <a href="{{ route('admin.events.index', ['type' => 'baptism']) }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Baptisms
                        </a>
                    @else
                        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                            <i class="bi bi-arrow-left me-2"></i>
                            Back to Events
                        </a>
                    @endif
                </div>
            </div>

            <!-- Error Alerts -->
            @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm mb-4 animate__animated animate__fadeIn">
                <div class="d-flex">
                    <div class="me-3">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <h5 class="alert-heading mb-1">Please correct the following errors:</h5>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif

            <!-- Event Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        @if ($eventType === 'wedding')
                            <i class="bi bi-heart fs-4 me-2"></i>
                            <h4 class="card-title mb-0">Wedding Details</h4>
                        @elseif ($eventType === 'baptism')
                            <i class="bi bi-water fs-4 me-2"></i>
                            <h4 class="card-title mb-0">Baptism Details</h4>
                        @else
                            <i class="bi bi-calendar-plus fs-4 me-2"></i>
                            <h4 class="card-title mb-0">Event Details</h4>
                        @endif
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.events.index') }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        <input type="hidden" name="event_type" value="{{ $eventType }}">

                        <div class="row g-3">
                            <!-- Event Title -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" placeholder="Event Title"
                                           value="{{ old('title') }}" required>
                                    <label for="title">
                                        <i class="bi bi-type-h1 me-1"></i>
                                        @if ($eventType === 'wedding')
                                            Wedding Title
                                        @elseif ($eventType === 'baptism')
                                            Baptism Title
                                        @else
                                            Event Title
                                        @endif
                                    </label>
                                    @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <textarea class="form-control @error('description') is-invalid @enderror"
                                              id="description" name="description"
                                              style="height: 120px" placeholder="Description">{{ old('description') }}</textarea>
                                    <label for="description">
                                        <i class="bi bi-card-text me-1"></i>
                                        Description
                                    </label>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Wedding-specific fields -->
                            @if ($eventType === 'wedding')
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="groom_id" name="groom_id">
                                            <option value="">Select from members (optional)</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('groom_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="groom_id">
                                            <i class="bi bi-person me-1"></i>
                                            Groom (Member)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="bride_id" name="bride_id">
                                            <option value="">Select from members (optional)</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('bride_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="bride_id">
                                            <i class="bi bi-person me-1"></i>
                                            Bride (Member)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('groom_name') is-invalid @enderror"
                                               id="groom_name" name="groom_name" placeholder="Groom Name"
                                               value="{{ old('groom_name') }}" required>
                                        <label for="groom_name">
                                            <i class="bi bi-person me-1"></i>
                                            Groom Name
                                        </label>
                                        @error('groom_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('bride_name') is-invalid @enderror"
                                               id="bride_name" name="bride_name" placeholder="Bride Name"
                                               value="{{ old('bride_name') }}" required>
                                        <label for="bride_name">
                                            <i class="bi bi-person me-1"></i>
                                            Bride Name
                                        </label>
                                        @error('bride_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <!-- Baptism-specific fields -->
                            @if ($eventType === 'baptism')
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select" id="person_id" name="person_id">
                                            <option value="">Select from members (optional)</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('person_id') == $user->id ? 'selected' : '' }}>{{ $user->full_name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="person_id">
                                            <i class="bi bi-person me-1"></i>
                                            Person (Member)
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('person_name') is-invalid @enderror"
                                               id="person_name" name="person_name" placeholder="Person Name"
                                               value="{{ old('person_name') }}" required>
                                        <label for="person_name">
                                            <i class="bi bi-person me-1"></i>
                                            Person Name
                                        </label>
                                        @error('person_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control @error('birth_date') is-invalid @enderror"
                                               id="birth_date" name="birth_date" 
                                               value="{{ old('birth_date') }}">
                                        <label for="birth_date">
                                            <i class="bi bi-calendar-date me-1"></i>
                                            Birth Date
                                        </label>
                                        @error('birth_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('parents') is-invalid @enderror"
                                               id="parents" name="parents" placeholder="Parents"
                                               value="{{ old('parents') }}">
                                        <label for="parents">
                                            <i class="bi bi-people me-1"></i>
                                            Parents
                                        </label>
                                        @error('parents')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12">
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="is_child" name="is_child" value="1" {{ old('is_child', '1') == '1' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_child">
                                            This is a child baptism
                                        </label>
                                    </div>
                                </div>
                            @endif

                            <!-- Event Date and Location -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control @error('event_date') is-invalid @enderror"
                                           id="event_date" name="event_date" placeholder="Event Date"
                                           value="{{ old('event_date', date('Y-m-d')) }}" required>
                                    <label for="event_date">
                                        <i class="bi bi-calendar-date me-1"></i>
                                        @if ($eventType === 'wedding')
                                            Wedding Date
                                        @elseif ($eventType === 'baptism')
                                            Baptism Date
                                        @else
                                            Event Date
                                        @endif
                                    </label>
                                    @error('event_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('location') is-invalid @enderror"
                                           id="location" name="location" placeholder="Location"
                                           value="{{ old('location') }}" required>
                                    <label for="location">
                                        <i class="bi bi-geo-alt me-1"></i>
                                        Location
                                    </label>
                                    @error('location')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Additional Options -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="time" class="form-control" id="event_time" name="event_time"
                                           placeholder="Event Time" value="{{ old('event_time') }}">
                                    <label for="event_time">
                                        <i class="bi bi-clock me-1"></i>
                                        @if ($eventType === 'wedding')
                                            Wedding Time
                                        @elseif ($eventType === 'baptism')
                                            Baptism Time
                                        @else
                                            Event Time
                                        @endif
                                        (Optional)
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="event_color" name="event_color">
                                        @if ($eventType === 'wedding')
                                            <option value="#E91E63" selected>Pink (Default)</option>
                                            <option value="#D81B60">Deep Pink</option>
                                            <option value="#C2185B">Rich Pink</option>
                                            <option value="#AD1457">Dark Pink</option>
                                            <option value="#880E4F">Very Dark Pink</option>
                                        @elseif ($eventType === 'baptism')
                                            <option value="#2196F3" selected>Blue (Default)</option>
                                            <option value="#1E88E5">Deep Blue</option>
                                            <option value="#1976D2">Rich Blue</option>
                                            <option value="#1565C0">Dark Blue</option>
                                            <option value="#0D47A1">Very Dark Blue</option>
                                        @else
                                            <option value="#3788d8" selected>Blue (Default)</option>
                                            <option value="#28a745">Green</option>
                                            <option value="#dc3545">Red</option>
                                            <option value="#ffc107">Yellow</option>
                                            <option value="#6f42c1">Purple</option>
                                        @endif
                                    </select>
                                    <label for="event_color">
                                        <i class="bi bi-palette me-1"></i>
                                        Event Color
                                    </label>
                                </div>
                            </div>

                            <!-- Officiating Minister and Witnesses/Godparents for weddings and baptisms -->
                            @if ($eventType === 'wedding' || $eventType === 'baptism')
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control @error('officiating_minister') is-invalid @enderror"
                                               id="officiating_minister" name="officiating_minister" placeholder="Officiating Minister"
                                               value="{{ old('officiating_minister') }}">
                                        <label for="officiating_minister">
                                            <i class="bi bi-person-badge me-1"></i>
                                            Officiating Minister
                                        </label>
                                        @error('officiating_minister')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        @if ($eventType === 'wedding')
                                            <input type="text" class="form-control @error('witnesses') is-invalid @enderror"
                                                   id="witnesses" name="witnesses" placeholder="Witnesses"
                                                   value="{{ old('witnesses') }}">
                                            <label for="witnesses">
                                                <i class="bi bi-people me-1"></i>
                                                Witnesses
                                            </label>
                                            @error('witnesses')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @else
                                            <input type="text" class="form-control @error('godparents') is-invalid @enderror"
                                                   id="godparents" name="godparents" placeholder="Godparents"
                                                   value="{{ old('godparents') }}">
                                            <label for="godparents">
                                                <i class="bi bi-people me-1"></i>
                                                Godparents
                                            </label>
                                            @error('godparents')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-select @error('status') is-invalid @enderror" 
                                                id="status" name="status" required>
                                            <option value="scheduled" {{ old('status', 'scheduled') == 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                        </select>
                                        <label for="status">
                                            <i class="bi bi-flag me-1"></i>
                                            Status
                                        </label>
                                        @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <textarea class="form-control @error('notes') is-invalid @enderror"
                                                  id="notes" name="notes" style="height: 100px" 
                                                  placeholder="Notes">{{ old('notes') }}</textarea>
                                        <label for="notes">
                                            <i class="bi bi-journal-text me-1"></i>
                                            Notes
                                        </label>
                                        @error('notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif

                            <!-- Attendance Poll Section (for all event types) -->
                            <div class="col-12 mt-3">
                                <div class="card border shadow-sm rounded-3">
                                    <div class="card-header bg-light d-flex align-items-center">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="enable_poll" name="enable_poll" value="1" {{ old('enable_poll') ? 'checked' : '' }}>
                                            <label class="form-check-label fw-bold" for="enable_poll">
                                                <i class="bi bi-bar-chart-fill me-1 text-primary"></i>
                                                Enable Attendance Poll
                                            </label>
                                        </div>
                                    </div>
                                    <div class="card-body poll-options" id="pollOptions">
                                        <p class="small text-muted mb-3">
                                            <i class="bi bi-info-circle me-1"></i>
                                            Create poll options for members to respond to this event
                                        </p>
                                        
                                        <!-- Default Poll Options -->
                                        <div class="mb-3">
                                            <label class="form-label">Default Response Options</label>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="poll_options[]" id="option_attending" value="attending" checked>
                                                <label class="form-check-label" for="option_attending">
                                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Attending</span>
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="poll_options[]" id="option_maybe" value="maybe" checked>
                                                <label class="form-check-label" for="option_maybe">
                                                    <span class="badge bg-warning text-dark"><i class="bi bi-question-circle me-1"></i>Maybe</span>
                                                </label>
                                            </div>
                                            <div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="poll_options[]" id="option_not_attending" value="not_attending" checked>
                                                <label class="form-check-label" for="option_not_attending">
                                                    <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Not Attending</span>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <!-- Custom Poll Options -->
                                        <div class="mb-3">
                                            <label class="form-label d-flex align-items-center">
                                                <span>Custom Response Options</span>
                                                <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="addCustomOption">
                                                    <i class="bi bi-plus-circle"></i> Add
                                                </button>
                                            </label>
                                            
                                            <div id="customOptionsContainer">
                                                @if(old('custom_options'))
                                                    @foreach(old('custom_options') as $index => $option)
                                                    <div class="input-group mb-2 custom-option">
                                                        <span class="input-group-text"><i class="bi bi-chat-text"></i></span>
                                                        <input type="text" class="form-control" name="custom_options[]" 
                                                               placeholder="e.g., Bringing a guest" value="{{ $option }}">
                                                        <button type="button" class="btn btn-outline-danger remove-option">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                        
                                        <!-- Poll Settings -->
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label" for="poll_deadline">Response Deadline</label>
                                                <div class="input-group">
                                                    <span class="input-group-text"><i class="bi bi-calendar-check"></i></span>
                                                    <input type="date" class="form-control" id="poll_deadline" name="poll_deadline" 
                                                           value="{{ old('poll_deadline') }}">
                                                </div>
                                                <div class="form-text">Optional deadline for responses</div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">Additional Settings</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="allow_comments" name="allow_comments" value="1" 
                                                           {{ old('allow_comments') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="allow_comments">
                                                        Allow members to add comments
                                                    </label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="notify_responses" name="notify_responses" value="1"
                                                           {{ old('notify_responses') ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="notify_responses">
                                                        Notify me of new responses
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    @if ($eventType === 'wedding')
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>Schedule Wedding
                                        </button>
                                    @elseif ($eventType === 'baptism')
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>Schedule Baptism
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-2"></i>Create Event
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Quick Tips Section -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <h5 class="card-title d-flex align-items-center">
                        <i class="bi bi-lightbulb text-warning me-2"></i>
                        Quick Tips
                    </h5>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 small">Add a clear title that describes your event</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 small">Include location details for easy navigation</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 small">Enable polls to track attendance and member responses</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex">
                                <div class="flex-shrink-0 text-primary">
                                    <i class="bi bi-info-circle"></i>
                                </div>
                                <div class="flex-grow-1 ms-2">
                                    <p class="mb-0 small">Set a response deadline before the event date</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<style>
    /* Custom Background Gradients */
    .bg-gradient-primary {
        background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
    }

    /* Form floating label enhancements */
    .form-floating > .form-control:focus ~ label,
    .form-floating > .form-control:not(:placeholder-shown) ~ label {
        color: #4e73df;
        opacity: 1;
    }

    .form-floating > .form-control:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Button enhancements */
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #224abe;
        border-color: #224abe;
    }

    /* Card enhancements */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }
    
    /* Poll section styles */
    #pollOptions {
        transition: opacity 0.3s ease, height 0.3s ease;
    }
    
    #pollOptions.disabled {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .custom-option {
        animation: fadeIn 0.4s ease;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Alert enhancements */
    .alert-danger {
        background-color: #fff5f5;
        color: #dc3545;
    }

    /* Rounded corners */
    .rounded-4 {
        border-radius: 10px;
    }

    /* Input focus states */
    textarea:focus,
    input[type="text"]:focus,
    input[type="date"]:focus,
    input[type="time"]:focus,
    select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Custom form validation states */
    .was-validated .form-control:valid, .form-control.is-valid {
        border-color: #28a745;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    .was-validated .form-control:invalid, .form-control.is-invalid {
        border-color: #dc3545;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* .badge {
        position: absolute;
        top: 0;
        right: 0;
        transform: translate(40%, -20%);
    } */

</style>

<script>
    // Initialize the poll options visibility
    $(document).ready(function() {
        const enablePollCheckbox = $('#enable_poll');
        const pollOptionsDiv = $('#pollOptions');
        
        // Set initial state
        pollOptionsDiv.css('display', enablePollCheckbox.is(':checked') ? 'block' : 'none');
        
        // Toggle visibility on checkbox change
        enablePollCheckbox.change(function() {
            pollOptionsDiv.slideToggle(300);
        });
        
        // Custom options handling
        let optionCounter = {{ old('custom_options') ? count(old('custom_options')) : 0 }};
        
        $('#addCustomOption').click(function() {
            optionCounter++;
            const newOption = $('<div class="input-group mb-2 custom-option">' +
                '<span class="input-group-text"><i class="bi bi-chat-text"></i></span>' +
                '<input type="text" class="form-control" name="custom_options[]" placeholder="e.g., Bringing a guest">' +
                '<button type="button" class="btn btn-outline-danger remove-option"><i class="bi bi-trash"></i></button>' +
                '</div>');
            
            $('#customOptionsContainer').append(newOption);
            
            // Add remove handler
            newOption.find('.remove-option').click(function() {
                $(this).closest('.custom-option').remove();
            });
        });
        
        // Add remove handler for existing options
        $('.remove-option').click(function() {
            $(this).closest('.custom-option').remove();
        });
    });
</script>
@endsection