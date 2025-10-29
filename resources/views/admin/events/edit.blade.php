@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10 col-12">
            <!-- Page Header with Animation -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    <h1 class="fw-bold text-primary mb-1">Edit Event</h1>
                    <p class="text-muted">Update the details for "{{ $event->title }}"</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
                        <i class="bi bi-arrow-left me-2"></i>
                        Back to Events
                    </a>
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
                        <i class="bi bi-calendar-check fs-4 me-2"></i>
                        <h4 class="card-title mb-0">Event Details</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Event Title -->
                            <div class="col-12">
                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control @error('title') is-invalid @enderror"
                                           id="title" name="title" placeholder="Event Title"
                                           value="{{ old('title', $event->title) }}" required>
                                    <label for="title">
                                        <i class="bi bi-type-h1 me-1"></i>
                                        Event Title
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
                                              style="height: 120px" placeholder="Description">{{ old('description', $event->description) }}</textarea>
                                    <label for="description">
                                        <i class="bi bi-card-text me-1"></i>
                                        Description
                                    </label>
                                    @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Event Date and Location -->
                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <input type="date" class="form-control @error('event_date') is-invalid @enderror"
                                           id="event_date" name="event_date" placeholder="Event Date"
                                           value="{{ old('event_date', $event->event_date) }}" required>
                                    <label for="event_date">
                                        <i class="bi bi-calendar-date me-1"></i>
                                        Event Date
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
                                           value="{{ old('location', $event->location) }}" required>
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
                                           placeholder="Event Time" value="{{ old('event_time', $event->event_time) }}">
                                    <label for="event_time">
                                        <i class="bi bi-clock me-1"></i>
                                        Event Time (Optional)
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating mb-3">
                                    <select class="form-select" id="event_color" name="event_color">
                                        <option value="#3788d8" {{ old('event_color', $event->event_color) == '#3788d8' ? 'selected' : '' }}>Blue (Default)</option>
                                        <option value="#28a745" {{ old('event_color', $event->event_color) == '#28a745' ? 'selected' : '' }}>Green</option>
                                        <option value="#dc3545" {{ old('event_color', $event->event_color) == '#dc3545' ? 'selected' : '' }}>Red</option>
                                        <option value="#ffc107" {{ old('event_color', $event->event_color) == '#ffc107' ? 'selected' : '' }}>Yellow</option>
                                        <option value="#6f42c1" {{ old('event_color', $event->event_color) == '#6f42c1' ? 'selected' : '' }}>Purple</option>
                                    </select>
                                    <label for="event_color">
                                        <i class="bi bi-palette me-1"></i>
                                        Event Color
                                    </label>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="col-12 mt-4">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="submit" class="btn btn-primary btn-lg px-4 d-flex align-items-center">
                                        <i class="bi bi-save me-2"></i>
                                        Update Event
                                    </button>
                                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary btn-lg px-4">
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Action Card -->
            <div class="card border-0 shadow-sm rounded-4 mt-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="card-title d-flex align-items-center mb-0">
                            <i class="bi bi-gear text-muted me-2"></i>
                            Additional Actions
                        </h5>
                        <form action="{{ route('admin.events.destroy', $event->id) }}" method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this event?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger d-flex align-items-center">
                                <i class="bi bi-trash me-2"></i>
                                Delete Event
                            </button>
                        </form>
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
</style>

<script>
    // Form validation
    document.addEventListener('DOMContentLoaded', function() {
        // Fetch all forms that need validation
        const forms = document.querySelectorAll('.needs-validation');

        // Loop over them and prevent submission
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
@endsection
