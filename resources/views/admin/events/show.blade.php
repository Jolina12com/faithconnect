@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-lg-8">
            <!-- Event Details Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">{{ $event->title }}</h4>
                        <div class="d-flex gap-2">
                            <a href="{{ route('admin.events.edit', $event) }}" class="btn btn-light">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-light" onclick="return confirm('Are you sure you want to delete this event?')">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-calendar-date text-primary fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Date & Time</h6>
                                    <p class="mb-0">
                                        {{ \Carbon\Carbon::parse($event->event_date)->format('F d, Y') }}
                                        @if($event->event_time)
                                            at {{ \Carbon\Carbon::parse($event->event_time)->format('g:i A') }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                <i class="bi bi-geo-alt text-primary fs-4 me-3"></i>
                                <div>
                                    <h6 class="mb-1">Location</h6>
                                    <p class="mb-0">{{ $event->location }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if($event->description)
                        <div class="mt-4">
                            <h6 class="mb-3">Description</h6>
                            <p class="mb-0">{{ $event->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Polls Section -->
            <div class="mb-4">
                <h4 class="mb-4">Event Polls</h4>
                @include('admin.events._poll_display')
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Create Poll Card -->
            @include('admin.events._poll_form')
        </div>
    </div>
</div>


<style>
    .progress {
        background-color: #e9ecef;
        border-radius: 0.5rem;
    }
    .progress-bar {
        transition: width 0.6s ease;
    }
</style>

@endsection
