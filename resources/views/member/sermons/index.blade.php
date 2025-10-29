@extends('member.dashboard_member')

@section('content')
<div class="container-fluid container-lg py-3 py-md-4 py-lg-5">
    <!-- Header Section with improved responsiveness -->
    <div class="row mb-3 mb-lg-4 align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-primary h3 h2-md">
                <i class="fas fa-pray me-2"></i>Sermon Library
            </h2>
            <p class="text-muted lead mb-0 small mb-2 mb-md-0">Grow in faith with our collection of inspiring messages</p>
        </div>
        <div class="col-md-5">
            <form action="{{ route('member.sermons.index') }}" method="GET" class="position-relative mt-2 mt-md-0">
                <div class="input-group">
                    <input type="text" name="search" class="form-control rounded-pill ps-3 pe-5"
                           placeholder="Search sermons..." value="{{ request('search') }}">
                    <button class="btn btn-primary rounded-circle position-absolute end-0 top-0 me-2 mt-1"
                            style="z-index: 5; width: 38px; height: 38px;" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Filters Section with collapsible behavior on mobile -->
    <div class="bg-light rounded-3 p-2 p-md-3 mb-3 mb-md-4 shadow-sm">
        <div class="d-md-none mb-2">
            <button class="btn btn-sm btn-outline-primary w-100" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse" aria-expanded="false">
                <i class="fas fa-filter me-1"></i> Filter Options <i class="fas fa-chevron-down ms-1"></i>
            </button>
        </div>
        <div class="collapse d-md-block" id="filterCollapse">
            <form method="GET" action="{{ route('member.sermons.index') }}">
                <div class="row g-2">
                    <div class="col-md-3 col-6">
                        <select class="form-select form-select-sm" name="series_id" onchange="this.form.submit()">
                            <option value="">All Series</option>
                            @foreach($series as $s)
                                <option value="{{ $s->id }}" {{ request('series_id') == $s->id ? 'selected' : '' }}>
                                    {{ $s->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select class="form-select form-select-sm" name="topic_id" onchange="this.form.submit()">
                            <option value="">All Topics</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}" {{ request('topic_id') == $topic->id ? 'selected' : '' }}>
                                    {{ $topic->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select class="form-select form-select-sm" name="featured" onchange="this.form.submit()">
                            <option value="">All Sermons</option>
                            <option value="1" {{ request('featured') == '1' ? 'selected' : '' }}>Featured Only</option>
                        </select>
                    </div>
                    <div class="col-md-3 col-6">
                        <select class="form-select form-select-sm" name="media_type" onchange="this.form.submit()">
                            <option value="">All Media</option>
                            <option value="video" {{ request('media_type') == 'video' ? 'selected' : '' }}>Video Only</option>
                            <option value="audio" {{ request('media_type') == 'audio' ? 'selected' : '' }}>Audio Only</option>
                        </select>
                    </div>
                </div>
                <!-- Preserve search query -->
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif
            </form>
        </div>
    </div>

    <!-- Main Content - Sermon Cards with improved responsive layout -->
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
                            <div class="position-absolute top-50 start-50 translate-middle play-button">
                                <button type="button" class="btn btn-primary rounded-circle p-2"
                                      data-bs-toggle="modal" data-bs-target="#videoModal{{ $sermon->id }}">
                                    <i class="fas fa-play fa-lg"></i>
                                </button>
                            </div>
                        @elseif($sermon->audio_path)
                            <div class="ratio ratio-16x9 bg-light">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-headphones-alt fa-4x text-primary opacity-50"></i>
                                </div>
                            </div>
                        @else
                            <div class="ratio ratio-16x9 bg-light">
                                <div class="d-flex align-items-center justify-content-center">
                                    <i class="fas fa-bible fa-4x text-primary opacity-50"></i>
                                </div>
                            </div>
                        @endif

                        <!-- Badge for sermon type -->
                        <div class="position-absolute top-0 end-0 m-2">
                            @if($sermon->video_path)
                                <span class="badge bg-danger"><i class="fas fa-video me-1"></i><span class="d-none d-sm-inline">Video</span></span>
                            @elseif($sermon->audio_path)
                                <span class="badge bg-info"><i class="fas fa-headphones me-1"></i><span class="d-none d-sm-inline">Audio</span></span>
                            @else
                                <span class="badge bg-secondary"><i class="fas fa-file-alt me-1"></i><span class="d-none d-sm-inline">Text</span></span>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-2 p-sm-3">
                        <h5 class="card-title fw-bold fs-6 fs-md-5 mb-1 mb-md-2">{{ $sermon->title }}</h5>
                        <div class="d-flex align-items-center mb-1 mb-md-2 small">
                            <i class="fas fa-user-tie text-primary me-2"></i>
                            <span>{{ $sermon->speaker_name ?? 'Unknown Speaker' }}</span>
                        </div>

                        <div class="d-flex align-items-center mb-1 mb-md-2 small">
                            <i class="far fa-calendar-alt text-primary me-2"></i>
                            <span>{{ $sermon->date_preached ? \Carbon\Carbon::parse($sermon->date_preached)->format('M d, Y') : 'Date Unknown' }}</span>
                        </div>

                        @if($sermon->scripture_reference)
                            <div class="d-flex align-items-center mb-1 mb-md-2 small">
                                <i class="fas fa-book-open text-primary me-2"></i>
                                <span>{{ $sermon->scripture_reference }}</span>
                            </div>
                        @endif

                        @if($sermon->description)
                            <p class="card-text small mt-2 text-muted d-none d-sm-block">
                                {{ \Illuminate\Support\Str::limit($sermon->description, 80) }}
                            </p>
                        @endif
                    </div>

                    <div class="card-footer bg-white border-top-0 pt-0 p-2 p-sm-3">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="d-flex align-items-center text-muted small">
                                <div class="me-3"><i class="fas fa-eye me-1"></i>{{ $sermon->view_count }}</div>
                                <div><i class="fas fa-download me-1"></i>{{ $sermon->download_count }}</div>
                            </div>

                            <div class="btn-group btn-group-sm">
                                @if($sermon->video_path)
                                    <button type="button"
                                       class="btn btn-sm btn-outline-primary"
                                       data-bs-toggle="modal" data-bs-target="#videoModal{{ $sermon->id }}">
                                        <i class="fas fa-play-circle me-1"></i><span class="d-none d-md-inline">Watch</span>
                                    </button>
                                @endif

                                @if($sermon->audio_path)
                                    <a href="{{ asset('storage/' . $sermon->audio_path) }}"
                                       class="btn btn-sm btn-outline-secondary" target="_blank">
                                        <i class="fas fa-headphones-alt me-1"></i><span class="d-none d-md-inline">Listen</span>
                                    </a>
                                @endif

                                <button class="btn btn-sm btn-outline-dark dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('member.sermons.show', $sermon->slug) }}">
                                            <i class="fas fa-eye me-2"></i>View Details
                                        </a>
                                    </li>
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
                <div class="text-center py-4 py-md-5 bg-light rounded-3">
                    <i class="fas fa-video-slash fa-3x fa-md-4x text-muted mb-3"></i>
                    <h4 class="text-muted h5 h4-md">No Sermons Available</h4>
                    <p class="text-muted small">Please check back later for new messages.</p>

                    @if(request('search'))
                        <a href="{{ route('member.sermons.index') }}" class="btn btn-sm btn-outline-primary mt-2 mt-md-3">
                            <i class="fas fa-sync-alt me-1"></i> Clear Search
                        </a>
                    @endif
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination with improved styling -->
    <div class="d-flex justify-content-center mt-4 mt-md-5">
        {{ $sermons->links('pagination::bootstrap-5') }}
    </div>
</div>

<!-- Video Modal -->
@foreach($sermons as $sermon)
    @if($sermon->video_path)
    <div class="modal fade" id="videoModal{{ $sermon->id }}" tabindex="-1" aria-labelledby="videoModalLabel{{ $sermon->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="videoModalLabel{{ $sermon->id }}">{{ $sermon->title }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <video id="sermonVideo{{ $sermon->id }}" controls
                            poster="{{ $sermon->thumbnail_path ? asset('storage/' . $sermon->thumbnail_path) : asset('images/sermon-placeholder.jpg') }}">
                            <source src="{{ asset('storage/' . $sermon->video_path) }}" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="d-flex align-items-center me-auto small text-muted">
                        <div class="me-3"><i class="fas fa-user-tie me-1"></i>{{ $sermon->speaker_name ?? 'Unknown Speaker' }}</div>
                        <div><i class="fas fa-bible me-1"></i>{{ $sermon->scripture_reference }}</div>
                    </div>
                    <a href="{{ asset('storage/' . $sermon->video_path) }}" class="btn btn-sm btn-outline-primary" download>
                        <i class="fas fa-download me-1"></i>Download
                    </a>
                    <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach

<!-- Improved responsive styles -->
<style>
    .sermon-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .sermon-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
    }

    .play-button {
        opacity: 0.8;
        transition: opacity 0.3s ease, transform 0.3s ease;
    }

    .sermon-media:hover .play-button {
        opacity: 1;
        transform: translate(-50%, -50%) scale(1.1);
    }

    .hover-shadow:hover {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .transition-all {
        transition: all 0.3s ease;
    }

    /* Responsive typography helper classes */
    .h2-md {
        font-size: 1.5rem;
    }

    .h4-md {
        font-size: 1.25rem;
    }

    .fs-md-5 {
        font-size: 1rem;
    }

    /* Custom breakpoint adjustments */
    @media (min-width: 768px) {
        .h2-md {
            font-size: 2rem;
        }

        .h4-md {
            font-size: 1.5rem;
        }

        .fs-md-5 {
            font-size: 1.25rem;
        }

        .fa-md-4x {
            font-size: 4em;
        }
    }

    /* Touch-friendly targets for mobile */
    @media (max-width: 767.98px) {
        .btn-group-sm > .btn,
        .form-select,
        .dropdown-item {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }
    }

    /* Modal video enhancements */
    .modal-dialog.modal-lg {
        max-width: 90%;
        margin: 1.75rem auto;
    }

    @media (min-width: 992px) {
        .modal-dialog.modal-lg {
            max-width: 800px;
        }
    }

    @media (min-width: 1200px) {
        .modal-dialog.modal-lg {
            max-width: 1000px;
        }
    }
</style>

<!-- Modal Video Control Script -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        function pauseAllMediaExcept(exceptEl) {
            const mediaEls = document.querySelectorAll('video, audio');
            mediaEls.forEach(function(el) {
                if (el !== exceptEl) {
                    try { el.pause(); } catch (e) {}
                }
            });
        }

        // Autoplay only when modal is shown; pause others
        const videoModals = document.querySelectorAll('.modal');
        videoModals.forEach(function(modal) {
            modal.addEventListener('shown.bs.modal', function() {
                const video = this.querySelector('video');
                if (video) {
                    pauseAllMediaExcept(video);
                    const playPromise = video.play();
                    if (playPromise && typeof playPromise.then === 'function') {
                        playPromise.catch(function() {/* ignored */});
                    }
                }
            });

            // Pause and reset when modal is closed
            modal.addEventListener('hidden.bs.modal', function() {
                const video = this.querySelector('video');
                if (video) {
                    try { video.pause(); } catch (e) {}
                    try { video.currentTime = 0; } catch (e) {}
                }
            });
        });

        // Auto-resize video modals on orientation change for mobile devices
        window.addEventListener('orientationchange', function() {
            const activeModal = document.querySelector('.modal.show');
            if (activeModal) {
                const modalDialog = activeModal.querySelector('.modal-dialog');
                if (window.innerHeight > window.innerWidth) {
                    // Portrait
                    modalDialog.style.maxWidth = '90%';
                } else {
                    // Landscape
                    modalDialog.style.maxWidth = '95%';
                }
            }
        });

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
                    
                    // Show toast notification
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
