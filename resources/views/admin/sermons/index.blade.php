@extends('admin.dashboard')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 text-gray-800 mb-1">Sermon Library</h1>
            <p class="text-muted">Manage and organize your church's sermon collection</p>
        </div>
        <a href="{{ route('admin.sermons.create') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Upload New Sermon
        </a>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Sermons</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sermons->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-video fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">This Month</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $sermons->where('date_preached', '>=', now()->startOfMonth())->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Latest Upload</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $sermons->count() > 0 ? ($sermons->sortByDesc('date_preached')->first()->date_preached ? \Carbon\Carbon::parse($sermons->sortByDesc('date_preached')->first()->date_preached)->format('M d') : 'N/A') : 'N/A' }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Total Views</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $sermons->sum('view_count') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-wrap justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Sermon Management</h6>
            <div class="d-flex">
                <div class="input-group mr-2">
                    <input type="text" class="form-control" placeholder="Search sermons..." id="sermon-search">
                    <div class="input-group-append">
                        <button class="btn btn-primary" type="button">
                            <i class="fas fa-search fa-sm"></i>
                        </button>
                    </div>
                </div>
                <div class="dropdown">
                    <button class="btn btn-outline-primary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-filter me-1"></i> Filter
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                        <li><a class="dropdown-item" href="#">All Sermons</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">Last 3 Months</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#">With Video</a></li>
                        <li><a class="dropdown-item" href="#">With Audio</a></li>
                        <li><a class="dropdown-item" href="#">Featured</a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="py-3">Title</th>
                            <th class="py-3">Speaker</th>
                            <th class="py-3">Scripture</th>
                            <th class="py-3">Preview</th>
                            <th class="py-3">Date</th>
                            <th class="py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($sermons as $sermon)
                            <tr>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        @if($sermon->featured)
                                        <div class="sermon-icon bg-warning text-white rounded me-3 p-2">
                                            <i class="fas fa-star"></i>
                                        </div>
                                        @else
                                        <div class="sermon-icon bg-primary text-white rounded me-3 p-2">
                                            <i class="fas fa-microphone"></i>
                                        </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $sermon->title }}</h6>
                                            <small class="text-muted">{{ $sermon->series->title ?? 'No Series' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    {{ $sermon->speaker_name ?? 'Unknown Speaker' }}
                                </td>
                                <td class="align-middle">
                                    {{ $sermon->scripture_reference ?? 'N/A' }}
                                </td>
                                <td class="align-middle">
                                    <div class="d-flex">
                                        @if($sermon->video_path)
                                            <div class="sermon-preview me-2">
                                                <video width="80" height="45" class="rounded shadow-sm">
                                                    <source src="{{ asset('storage/' . $sermon->video_path) }}" type="video/mp4">
                                                </video>
                                                <div class="play-overlay" data-video="{{ asset('storage/' . $sermon->video_path) }}">
                                                    <i class="fas fa-play-circle"></i>
                                                </div>
                                            </div>
                                        @endif
                                        @if($sermon->audio_path)
                                            <button class="btn btn-sm btn-outline-info" data-audio="{{ asset('storage/' . $sermon->audio_path) }}">
                                                <i class="fas fa-headphones"></i>
                                            </button>
                                        @endif
                                        @if(!$sermon->video_path && !$sermon->audio_path)
                                            <span class="badge bg-secondary">No media</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div>
                                        <div class="mb-1">{{ $sermon->date_preached ? \Carbon\Carbon::parse($sermon->date_preached)->format('M d, Y') : 'N/A' }}</div>
                                        <small class="text-muted">
                                            <i class="fas fa-eye me-1"></i>{{ $sermon->view_count }}
                                            <i class="fas fa-download ms-2 me-1"></i>{{ $sermon->download_count }}
                                        </small>
                                    </div>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.sermons.show', $sermon->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="View Sermon">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.sermons.edit', $sermon->id) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Edit Sermon">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-sermon"
                                                data-sermon-id="{{ $sermon->id }}"
                                                data-sermon-title="{{ $sermon->title }}"
                                                data-bs-toggle="tooltip" title="Delete Sermon">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="empty-state">
                                        <div class="empty-state-icon mb-3">
                                            <i class="fas fa-video-slash fa-3x text-muted"></i>
                                        </div>
                                        <h5>No Sermons Found</h5>
                                        <p class="text-muted mb-3">You haven't uploaded any sermons yet.</p>
                                        <a href="{{ route('admin.sermons.create') }}" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-2"></i>Upload Your First Sermon
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <nav>
                <div class="pagination justify-content-end mb-0">
                    <!-- Placeholder for pagination -->
                </div>
            </nav>
        </div>
    </div>

    <!-- Video Modal -->
    <div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="videoModalLabel">Sermon Preview</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="ratio ratio-16x9">
                        <video id="modalVideo" controls class="w-100">
                            <source src="" type="video/mp4">
                            Your browser does not support the video tag.
                        </video>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Audio Modal -->
    <div class="modal fade" id="audioModal" tabindex="-1" aria-labelledby="audioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="audioModalLabel">Listen to Sermon</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <audio id="modalAudio" controls class="w-100">
                        <source src="" type="audio/mpeg">
                        Your browser does not support the audio element.
                    </audio>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteSermonModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Deletion</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete "<span id="sermonTitleToDelete"></span>"?</p>
                    <p class="text-danger mb-0"><i class="fas fa-exclamation-triangle me-2"></i> This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form id="deleteSermonForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete Permanently</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .sermon-preview {
        position: relative;
        cursor: pointer;
    }

    .play-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: rgba(0, 0, 0, 0.3);
        border-radius: 0.25rem;
        transition: all 0.2s ease;
    }

    .play-overlay i {
        color: white;
        font-size: 1.5rem;
        opacity: 0.9;
        transition: all 0.2s ease;
    }

    .sermon-preview:hover .play-overlay {
        background-color: rgba(0, 0, 0, 0.5);
    }

    .sermon-preview:hover .play-overlay i {
        opacity: 1;
        transform: scale(1.1);
    }

    .sermon-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
    }

    .description-cell {
        max-width: 300px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .border-left-primary {
        border-left: 0.25rem solid #4e73df !important;
    }

    .border-left-success {
        border-left: 0.25rem solid #1cc88a !important;
    }

    .border-left-info {
        border-left: 0.25rem solid #36b9cc !important;
    }

    .border-left-warning {
        border-left: 0.25rem solid #f6c23e !important;
    }

    @media (max-width: 992px) {
        .card-header {
            flex-direction: column;
            align-items: flex-start !important;
        }

        .card-header .d-flex {
            margin-top: 10px;
            width: 100%;
        }

        .input-group {
            margin-right: 0 !important;
            margin-bottom: 10px;
            width: 100%;
        }

        .dropdown {
            width: 100%;
        }

        .dropdown button {
            width: 100%;
        }
    }

    @media (max-width: 768px) {
        .description-cell {
            max-width: 150px;
        }

        .table-responsive {
            border: 0;
        }

        .table thead {
            display: none;
        }

        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 15px;
            border-bottom: 2px solid #e3e6f0;
        }

        .table td {
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-align: right;
            padding: 10px 15px;
            border-bottom: 1px solid #e3e6f0;
        }

        .table td:before {
            content: attr(data-label);
            font-weight: bold;
            text-align: left;
            width: 30%;
        }

        .table td:last-child {
            border-bottom: 0;
        }

        .modal-dialog {
            margin: 0.5rem;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize tooltips
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Video preview functionality
        const playOverlays = document.querySelectorAll('.play-overlay');
        const videoModal = document.getElementById('videoModal');
        const modalVideo = document.getElementById('modalVideo');
        let videoModalInstance = null;

        if (videoModal) {
            videoModalInstance = new bootstrap.Modal(videoModal);
        }

        playOverlays.forEach(overlay => {
            overlay.addEventListener('click', function() {
                const videoSrc = this.getAttribute('data-video');
                if (modalVideo && videoSrc) {
                    const source = modalVideo.querySelector('source');
                    if (source) {
                        source.src = videoSrc;
                        modalVideo.load();
                        if (videoModalInstance) {
                            videoModalInstance.show();
                        }
                    }
                }
            });
        });

        // Play video when modal is shown
        if (videoModal) {
            videoModal.addEventListener('shown.bs.modal', function () {
                if (modalVideo) {
                    modalVideo.play();
                }
            });

            // Pause video when modal is hidden
            videoModal.addEventListener('hide.bs.modal', function () {
                if (modalVideo) {
                    modalVideo.pause();
                }
            });
        }

        // Audio preview functionality
        const audioButtons = document.querySelectorAll('[data-audio]');
        const audioModal = document.getElementById('audioModal');
        const modalAudio = document.getElementById('modalAudio');
        let audioModalInstance = null;

        if (audioModal) {
            audioModalInstance = new bootstrap.Modal(audioModal);
        }

        audioButtons.forEach(button => {
            button.addEventListener('click', function() {
                const audioSrc = this.getAttribute('data-audio');
                if (modalAudio && audioSrc) {
                    const source = modalAudio.querySelector('source');
                    if (source) {
                        source.src = audioSrc;
                        modalAudio.load();
                        if (audioModalInstance) {
                            audioModalInstance.show();
                        }
                    }
                }
            });
        });

        // Play audio when modal is shown
        if (audioModal) {
            audioModal.addEventListener('shown.bs.modal', function () {
                if (modalAudio) {
                    modalAudio.play();
                }
            });

            // Pause audio when modal is hidden
            audioModal.addEventListener('hide.bs.modal', function () {
                if (modalAudio) {
                    modalAudio.pause();
                }
            });
        }

        // Delete sermon functionality
        const deleteSermonModal = document.getElementById('deleteSermonModal');
        const sermonTitleToDelete = document.getElementById('sermonTitleToDelete');
        const deleteSermonForm = document.getElementById('deleteSermonForm');
        let deleteModalInstance = null;

        if (deleteSermonModal) {
            deleteModalInstance = new bootstrap.Modal(deleteSermonModal);
        }

        // Use event delegation for delete buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-sermon')) {
                const button = e.target.closest('.delete-sermon');
                const sermonId = button.getAttribute('data-sermon-id');
                const sermonTitle = button.getAttribute('data-sermon-title');

                if (sermonTitleToDelete && deleteSermonForm && sermonId) {
                    sermonTitleToDelete.textContent = sermonTitle;
                    deleteSermonForm.action = `{{ url('admin/sermons') }}/${sermonId}`;

                    if (deleteModalInstance) {
                        deleteModalInstance.show();
                    }
                }
            }
        });

        // Search functionality
        const searchInput = document.getElementById('sermon-search');
        if (searchInput) {
            searchInput.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const sermonRows = document.querySelectorAll('tbody tr');

                sermonRows.forEach(row => {
                    const titleElement = row.querySelector('h6');
                    const speakerElement = row.querySelector('td:nth-child(2)');
                    const scriptureElement = row.querySelector('td:nth-child(3)');
                    
                    if (titleElement && speakerElement && scriptureElement) {
                        const title = titleElement.textContent.toLowerCase();
                        const speaker = speakerElement.textContent.toLowerCase();
                        const scripture = scriptureElement.textContent.toLowerCase();

                        if (title.includes(searchTerm) || speaker.includes(searchTerm) || scripture.includes(searchTerm)) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    }
                });
            });
        }
    });
</script>
@endsection