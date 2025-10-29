@extends('member.dashboard_member')

@section('content')
<div class="container-fluid container-lg py-3 py-md-4 py-lg-5">
    <!-- Header Section -->
    <div class="row mb-3 mb-lg-4 align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-primary h3 h2-md">
                <i class="fas fa-tags me-2"></i>{{ $topic->name }}
            </h2>
            <p class="text-muted lead mb-0 small mb-2 mb-md-0">{{ $topic->description ?? 'Sermons on this topic' }}</p>
        </div>
        <div class="col-md-5">
            <a href="{{ route('member.sermons.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-1"></i> Back to All Sermons
            </a>
        </div>
    </div>

    <!-- Topic Info Card -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="card-title mb-2">{{ $topic->name }}</h5>
                    @if($topic->description)
                        <p class="card-text text-muted">{{ $topic->description }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-md-end">
                    <span class="badge bg-primary fs-6">{{ $sermons->total() }} Sermons</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Sermons Grid -->
    <div class="row g-2 g-md-3 g-lg-4">
        @forelse($sermons as $sermon)
            <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                <div class="card h-100 sermon-card border-0 shadow-sm hover-shadow transition-all">
                    <div class="position-relative sermon-media">
                        @if($sermon->video_path)
                            <div class="ratio ratio-16x9">
                                <video class="rounded-top"
                                    poster="{{ $sermon->thumbnail_path ? asset('storage/' . $sermon->thumbnail_path) : asset('images/sermon-placeholder.jpg') }}"
                                    preload="none">
                                    <source src="{{ asset('storage/' . $sermon->video_path) }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                        @elseif($sermon->audio_path)
                            <div class="ratio ratio-16x9 bg-light">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-headphones-alt fa-4x text-primary opacity-50"></i>
                                </div>
                            </div>
                        @else
                            <div class="ratio ratio-16x9 bg-secondary">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-bible fa-4x text-white opacity-50"></i>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="card-body d-flex flex-column">
                        <h6 class="card-title fw-bold mb-2">{{ Str::limit($sermon->title, 60) }}</h6>
                        
                        @if($sermon->speaker_name)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-user-tie me-1"></i>{{ $sermon->speaker_name }}
                            </p>
                        @endif

                        @if($sermon->date_preached)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-calendar-alt me-1"></i>{{ $sermon->date_preached->format('M j, Y') }}
                            </p>
                        @endif

                        @if($sermon->scripture_reference)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-book-open me-1"></i>{{ $sermon->scripture_reference }}
                            </p>
                        @endif

                        @if($sermon->series)
                            <p class="text-muted small mb-2">
                                <i class="fas fa-list me-1"></i>{{ $sermon->series->title }}
                </p>
                @endif

                        @if($sermon->topics->count() > 0)
                            <div class="mb-2">
                                @foreach($sermon->topics->take(2) as $sermonTopic)
                                    <span class="badge bg-light text-dark me-1 mb-1">{{ $sermonTopic->name }}</span>
                                @endforeach
                                @if($sermon->topics->count() > 2)
                                    <span class="badge bg-light text-dark">+{{ $sermon->topics->count() - 2 }} more</span>
                                @endif
                            </div>
                        @endif

                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center text-muted small">
                                    <div class="me-3"><i class="fas fa-eye me-1"></i>{{ $sermon->view_count }}</div>
                                    <div><i class="fas fa-download me-1"></i>{{ $sermon->download_count }}</div>
                                </div>
                            </div>

                            <div class="btn-group btn-group-sm w-100">
                                <a href="{{ route('member.sermons.show', $sermon->slug) }}" 
                                   class="btn btn-primary">
                                    <i class="fas fa-play-circle me-1"></i>View
                                </a>

                                <button class="btn btn-outline-dark dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    @if($sermon->video_path)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('member.sermons.download', ['sermon' => $sermon->slug, 'type' => 'video']) }}">
                                                <i class="fas fa-download me-2"></i>Download Video
                                            </a>
                                        </li>
                                    @endif
                                    @if($sermon->audio_path)
                                        <li>
                                            <a class="dropdown-item" href="{{ route('member.sermons.download', ['sermon' => $sermon->slug, 'type' => 'audio']) }}">
                                                <i class="fas fa-download me-2"></i>Download Audio
                                            </a>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item toggle-favorite" href="#" data-id="{{ $sermon->id }}">
                                            <i class="fas fa-heart me-2"></i>Add to Favorites
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="fas fa-info-circle fa-3x mb-3 text-primary"></i>
                    <h5>No sermons found for this topic</h5>
                    <p>There are no sermons available for this topic yet.</p>
                    <a href="{{ route('member.sermons.index') }}" class="btn btn-primary">Browse All Sermons</a>
                </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($sermons->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $sermons->links() }}
            </div>
    </div>
    @endif
</div>

@section('scripts')
<script>
    $(document).ready(function() {
        // Toggle favorite functionality
        $('.toggle-favorite').on('click', function(e) {
            e.preventDefault();
            const sermonId = $(this).data('id');
            const link = $(this);
            
            $.ajax({
                url: "{{ route('member.sermons.toggle-favorite') }}",
                type: "POST",
                data: {
                    sermon_id: sermonId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.status === 'added') {
                        link.html('<i class="fas fa-heart me-2"></i>Remove from Favorites');
                        link.addClass('text-danger');
                    } else {
                        link.html('<i class="fas fa-heart me-2"></i>Add to Favorites');
                        link.removeClass('text-danger');
                    }
                    
                    if (typeof toastr !== 'undefined') {
                        toastr.success(response.message);
                    } else {
                        alert(response.message);
                    }
                },
                error: function(xhr) {
                    if (typeof toastr !== 'undefined') {
                        toastr.error('An error occurred. Please try again.');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });
    });
</script>
@endsection
@endsection