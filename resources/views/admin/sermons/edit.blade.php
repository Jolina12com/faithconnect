@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10 col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Sermon</h5>
                    <a href="{{ route('admin.sermons.index') }}" class="btn btn-sm btn-light">
                        <i class="fas fa-arrow-left me-1"></i> Back to Sermons
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.sermons.update', $sermon->id) }}" method="POST" enctype="multipart/form-data" id="sermonForm">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Sermon Title</label>
                            <input type="text" id="title" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ $sermon->title }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea id="description" name="description" class="form-control @error('description') is-invalid @enderror" rows="4">{{ $sermon->description }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="speaker_name" class="form-label fw-bold">Speaker</label>
                                <input type="text" id="speaker_name" name="speaker_name" class="form-control @error('speaker_name') is-invalid @enderror" value="{{ $sermon->speaker_name }}">
                                @error('speaker_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="scripture_reference" class="form-label fw-bold">Scripture Reference</label>
                                <input type="text" id="scripture_reference" name="scripture_reference" class="form-control @error('scripture_reference') is-invalid @enderror" value="{{ $sermon->scripture_reference }}">
                                @error('scripture_reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="date_preached" class="form-label fw-bold">Date Preached</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="far fa-calendar"></i></span>
                                <input type="date" id="date_preached" name="date_preached" class="form-control @error('date_preached') is-invalid @enderror" value="{{ $sermon->date_preached ? \Carbon\Carbon::parse($sermon->date_preached)->format('Y-m-d') : '' }}">
                                @error('date_preached')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="featured" name="featured" {{ $sermon->featured ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold" for="featured">Featured Sermon</label>
                            </div>
                            <div class="form-text">Featured sermons will be highlighted on the website.</div>
                        </div>

                        <div class="mb-4">
                            <label for="series_id" class="form-label fw-bold">Series</label>
                            <select id="series_id" name="series_id" class="form-select @error('series_id') is-invalid @enderror">
                                <option value="">No Series</option>
                                @if(isset($sermonSeries) && $sermonSeries->count() > 0)
                                    @foreach($sermonSeries as $series)
                                        @if($series && isset($series->id))
                                            <option value="{{ $series->id }}"
                                                {{ (old('series_id', $sermon->series_id ?? null) == $series->id) ? 'selected' : '' }}>
                                                {{ $series->title ?? 'Untitled Series' }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            @error('series_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        @if($sermon->video_path)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Video</label>
                            <div class="ratio ratio-16x9 mb-2">
                                <video controls class="rounded">
                                    <source src="{{ $sermon->video_path }}" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-muted small me-3">Current file: {{ basename($sermon->video_path) }}</span>
                                <div class="form-check form-switch ms-auto">
                                    <input class="form-check-input" type="checkbox" id="replaceVideo">
                                    <label class="form-check-label" for="replaceVideo">Replace video</label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-4 video-upload" style="{{ $sermon->video_path ? 'display: none;' : '' }}">
                            <label for="video" class="form-label fw-bold">Upload {{ $sermon->video_path ? 'New' : '' }} Video</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-film"></i></span>
                                <input type="file" id="video" name="video" class="form-control @error('video') is-invalid @enderror" accept="video/*">
                                @error('video')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Maximum file size: 500MB</div>
                        </div>

                        @if($sermon->audio_path)
                        <div class="mb-4">
                            <label class="form-label fw-bold">Current Audio</label>
                            <div class="mb-2">
                                <audio controls class="w-100">
                                    <source src="{{ $sermon->audio_path }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="text-muted small me-3">Current file: {{ basename($sermon->audio_path) }}</span>
                                <div class="form-check form-switch ms-auto">
                                    <input class="form-check-input" type="checkbox" id="replaceAudio">
                                    <label class="form-check-label" for="replaceAudio">Replace audio</label>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="mb-4 audio-upload" style="{{ $sermon->audio_path ? 'display: none;' : '' }}">
                            <label for="audio" class="form-label fw-bold">Upload {{ $sermon->audio_path ? 'New' : '' }} Audio</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-headphones"></i></span>
                                <input type="file" id="audio" name="audio" class="form-control @error('audio') is-invalid @enderror" accept="audio/*">
                                @error('audio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-text">Maximum file size: 200MB</div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#confirmCancel">
                                <i class="fas fa-times me-1"></i> Cancel
                            </button>
                            <button type="submit" class="btn btn-success px-4">
                                <i class="fas fa-save me-1"></i> Update Sermon
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmCancel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Discard Changes?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel? Any unsaved changes will be lost.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay</button>
                <a href="{{ route('admin.sermons.index') }}" class="btn btn-danger">Discard Changes</a>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Show/hide video upload based on replace checkbox
        const replaceVideoCheckbox = document.getElementById('replaceVideo');
        const videoUploadDiv = document.querySelector('.video-upload');

        if (replaceVideoCheckbox) {
            replaceVideoCheckbox.addEventListener('change', function() {
                videoUploadDiv.style.display = this.checked ? 'block' : 'none';
            });
        }

        // Show/hide audio upload based on replace checkbox
        const replaceAudioCheckbox = document.getElementById('replaceAudio');
        const audioUploadDiv = document.querySelector('.audio-upload');

        if (replaceAudioCheckbox) {
            replaceAudioCheckbox.addEventListener('change', function() {
                audioUploadDiv.style.display = this.checked ? 'block' : 'none';
            });
        }

        // Form validation
        const form = document.getElementById('sermonForm');
        form.addEventListener('submit', function(event) {
            let isValid = true;
            const title = document.getElementById('title');

            if (title.value.trim() === '') {
                title.classList.add('is-invalid');
                isValid = false;
            } else {
                title.classList.remove('is-invalid');
            }

            if (!isValid) {
                event.preventDefault();
            }
        });
    });
</script>

@endsection
