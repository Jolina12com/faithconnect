@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="card shadow border-0">
        <div class="card-header bg-gradient-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fas fa-microphone-alt me-2"></i>Upload New Sermon</h5>
                <a href="{{ route('admin.sermons.index') }}" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Back to Sermons
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <div class="d-flex">
                        <i class="fas fa-exclamation-triangle me-2 mt-1"></i>
                        <div>
                            <strong>Please fix the following errors:</strong>
                            <ul class="mb-0 mt-1">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('admin.sermons.store') }}" method="POST" enctype="multipart/form-data" id="uploadForm">
                @csrf

                <div class="row g-4">
                    <div class="col-md-8">
                        <div class="mb-4">
                            <label for="title" class="form-label fw-bold">Sermon Title <span class="text-danger">*</span></label>
                            <input type="text" name="title" id="title" class="form-control form-control-lg"
                                   placeholder="Enter the sermon title" value="{{ old('title') }}" required>
                            <div class="form-text">A slug will be automatically generated from the title</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="scripture_reference" class="form-label fw-bold">Scripture Reference</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-bible text-primary"></i>
                                    </span>
                                    <input type="text" name="scripture_reference" id="scripture_reference" class="form-control"
                                           placeholder="e.g. John 3:16-21" value="{{ old('scripture_reference') }}">
                                </div>
                                <div class="form-text">Book, chapter and verse(s) referenced in the sermon</div>
                            </div>
                            <div class="col-md-6">
                            <label for="series_id" class="form-label fw-bold">Sermon Series</label>
                            <select name="series_id" id="series_id" class="form-select">
                                <option value="">--- No Series ---</option>
                                @if(isset($sermonSeries) && $sermonSeries->count() > 0)
                                    @foreach($sermonSeries as $series)
                                        @if($series && isset($series->id))
                                            <option value="{{ $series->id }}" {{ old('series_id') == $series->id ? 'selected' : '' }}>
                                                {{ $series->title ?? 'Untitled Series' }}
                                            </option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                            <div class="form-text">Optional: Add this sermon to a series</div>
                        </div>
                    </div>
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" class="form-control" rows="4"
                                    placeholder="Enter a brief description of this sermon">{{ old('description') }}</textarea>
                            <div class="form-text">Provide a summary, key points, or references used in this sermon</div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="date_preached" class="form-label fw-bold">Sermon Date <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-calendar-alt text-primary"></i>
                                    </span>
                                    <input type="date" name="date_preached" id="date_preached"
                                          class="form-control" value="{{ old('date_preached', date('Y-m-d')) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="speaker_name" class="form-label fw-bold">Speaker</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-user text-primary"></i>
                                    </span>
                                    <input type="text" name="speaker_name" id="speaker_name" class="form-control"
                                          placeholder="Name of the speaker" value="{{ old('speaker_name') }}">
                                </div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="duration" class="form-label fw-bold">Duration (minutes)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fas fa-clock text-primary"></i>
                                    </span>
                                    <input type="number" name="duration_minutes" id="duration_minutes" class="form-control"
                                           min="1" max="999" placeholder="Minutes" value="{{ old('duration_minutes') }}">
                                    <input type="hidden" name="duration" id="duration" value="{{ old('duration') }}">
                                </div>
                                <div class="form-text">This will be converted to seconds automatically</div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-check form-switch mt-4">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="featured" name="featured" value="1"
                                           {{ old('featured') ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="featured">
                                        <i class="fas fa-star text-warning me-1"></i>Featured Sermon
                                    </label>
                                </div>
                                <div class="form-text">Featured sermons appear on the homepage and at the top of listings</div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="fas fa-file-video me-2"></i>Video Upload
                                </h6>

                                <div id="upload-container" class="upload-container mb-3">
                                    <div class="upload-area text-center p-4 rounded" id="uploadArea">
                                        <i class="fas fa-cloud-upload-alt fa-3x text-primary mb-3"></i>
                                        <p class="mb-2">Drag & drop video file here</p>
                                        <span class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-folder-open me-1"></i> Browse Files
                                        </span>
                                        <input type="file" name="video" id="video"
                                              class="file-input" accept="video/*">
                                    </div>

                                    <div id="video-preview" class="d-none">
                                        <div class="ratio ratio-16x9 mb-2 border rounded">
                                            <video controls id="videoPreview">
                                                <source src="" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-truncate file-name" id="fileName"></small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeFile">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="progress mb-3 d-none" id="uploadProgress">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary"
                                         role="progressbar" style="width: 0%" aria-valuenow="0"
                                         aria-valuemin="0" aria-valuemax="100">0%</div>
                                </div>

                                <div class="text-center">
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Supported formats: MP4, WebM, MOV<br>
                                        Maximum size: 500MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="fas fa-file-audio me-2"></i>Audio Upload
                                </h6>

                                <div id="audio-upload-container" class="upload-container mb-3">
                                    <div class="upload-area text-center p-4 rounded" id="audioUploadArea">
                                        <i class="fas fa-music fa-3x text-primary mb-3"></i>
                                        <p class="mb-2">Drag & drop audio file here</p>
                                        <span class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-folder-open me-1"></i> Browse Files
                                        </span>
                                        <input type="file" name="audio" id="audio"
                                              class="file-input" accept="audio/*">
                                    </div>

                                    <div id="audio-preview" class="d-none">
                                        <div class="border rounded p-2 mb-2">
                                            <audio controls id="audioPreview" class="w-100">
                                                <source src="" type="audio/mpeg">
                                                Your browser does not support the audio element.
                                            </audio>
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-truncate file-name" id="audioFileName"></small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeAudioFile">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Supported formats: MP3, WAV, M4A<br>
                                        Maximum size: 200MB
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="card bg-light border-0 mb-4">
                            <div class="card-body">
                                <h6 class="card-title text-primary mb-3">
                                    <i class="fas fa-image me-2"></i>Thumbnail Image
                                </h6>

                                <div id="thumbnail-upload-container" class="upload-container mb-3">
                                    <div class="upload-area text-center p-4 rounded" id="thumbnailUploadArea">
                                        <i class="fas fa-image fa-3x text-primary mb-3"></i>
                                        <p class="mb-2">Drag & drop image here</p>
                                        <span class="btn btn-primary btn-sm mt-2">
                                            <i class="fas fa-folder-open me-1"></i> Browse Files
                                        </span>
                                        <input type="file" name="thumbnail" id="thumbnail"
                                              class="file-input" accept="image/*">
                                    </div>

                                    <div id="thumbnail-preview" class="d-none">
                                        <div class="border rounded mb-2 text-center">
                                            <img id="thumbnailPreview" class="img-fluid thumbnail-img" src="" alt="Thumbnail preview">
                                        </div>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted text-truncate file-name" id="thumbnailFileName"></small>
                                            <button type="button" class="btn btn-sm btn-outline-danger" id="removeThumbnail">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-info-circle me-1"></i>
                                        Recommended size: 1280x720px<br>
                                        Formats: JPG, PNG, WebP
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-primary w-100" id="uploadButton">
                                <i class="fas fa-upload me-2"></i>Upload Sermon
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Primary gradient background */
    .bg-gradient-primary {
        background: linear-gradient(to right, #4e73df, #224abe);
    }

    /* File upload styling */
    .upload-container {
        position: relative;
    }

    .upload-area {
        border: 2px dashed #d2d6de;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .upload-area:hover, .upload-area.dragover {
        border-color: #4e73df;
        background-color: #eef1ff;
    }

    .file-input {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    /* Thumbnail preview styling */
    .thumbnail-img {
        max-height: 150px;
        object-fit: contain;
    }

    /* Input focus style */
    .form-control:focus {
        border-color: #bac8f3;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    /* Form validation styling */
    .form-control.is-invalid {
        border-color: #e74a3b;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23e74a3b'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23e74a3b' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }

    /* Alert styling */
    .alert {
        border-left: 4px solid;
        border-top: 0;
        border-right: 0;
        border-bottom: 0;
        border-radius: 0.25rem;
    }

    .alert-success {
        border-left-color: #1cc88a;
    }

    .alert-danger {
        border-left-color: #e74a3b;
    }

    /* Media queries for responsiveness */
    @media (max-width: 767.98px) {
        .card-header {
            padding: 0.75rem 1rem;
        }

        .card-header h5 {
            font-size: 1rem;
        }

        .card-body {
            padding: 1rem;
        }
    }
</style>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Elements - Video
        const uploadArea = document.getElementById('uploadArea');
        const videoInput = document.getElementById('video');
        const videoPreview = document.getElementById('videoPreview');
        const videoPreviewContainer = document.getElementById('video-preview');
        const fileName = document.getElementById('fileName');
        const removeFileBtn = document.getElementById('removeFile');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressBar = uploadProgress.querySelector('.progress-bar');

        // Elements - Audio
        const audioUploadArea = document.getElementById('audioUploadArea');
        const audioInput = document.getElementById('audio');
        const audioPreview = document.getElementById('audioPreview');
        const audioPreviewContainer = document.getElementById('audio-preview');
        const audioFileName = document.getElementById('audioFileName');
        const removeAudioBtn = document.getElementById('removeAudioFile');

        // Elements - Thumbnail
        const thumbnailUploadArea = document.getElementById('thumbnailUploadArea');
        const thumbnailInput = document.getElementById('thumbnail');
        const thumbnailPreview = document.getElementById('thumbnailPreview');
        const thumbnailPreviewContainer = document.getElementById('thumbnail-preview');
        const thumbnailFileName = document.getElementById('thumbnailFileName');
        const removeThumbnailBtn = document.getElementById('removeThumbnail');

        // Form
        const uploadForm = document.getElementById('uploadForm');
        const durationInput = document.getElementById('duration');
        const durationMinutesInput = document.getElementById('duration_minutes');

        // Convert minutes to seconds for storage
        durationMinutesInput.addEventListener('input', function() {
            const minutes = parseInt(this.value) || 0;
            durationInput.value = minutes * 60; // Store duration in seconds
        });

        // Handle file selection via direct input - VIDEO
        videoInput.addEventListener('change', function(e) {
            handleVideoFiles(this.files);
        });

        // Handle file selection via direct input - AUDIO
        audioInput.addEventListener('change', function(e) {
            handleAudioFiles(this.files);
        });

        // Handle file selection via direct input - THUMBNAIL
        thumbnailInput.addEventListener('change', function(e) {
            handleThumbnailFiles(this.files);
        });

        // Handle drag and drop - VIDEO
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        // Handle drag and drop - AUDIO
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            audioUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        // Handle drag and drop - THUMBNAIL
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            thumbnailUploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => highlight(uploadArea), false);
            audioUploadArea.addEventListener(eventName, () => highlight(audioUploadArea), false);
            thumbnailUploadArea.addEventListener(eventName, () => highlight(thumbnailUploadArea), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => unhighlight(uploadArea), false);
            audioUploadArea.addEventListener(eventName, () => unhighlight(audioUploadArea), false);
            thumbnailUploadArea.addEventListener(eventName, () => unhighlight(thumbnailUploadArea), false);
        });

        function highlight(element) {
            element.classList.add('dragover');
        }

        function unhighlight(element) {
            element.classList.remove('dragover');
        }

        // Handle dropped files - VIDEO
        uploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleVideoFiles(files);
        });

        // Handle dropped files - AUDIO
        audioUploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleAudioFiles(files);
        });

        // Handle dropped files - THUMBNAIL
        thumbnailUploadArea.addEventListener('drop', function(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleThumbnailFiles(files);
        });

        // Note: The hidden file inputs already cover the upload areas (opacity: 0),
        // so clicking the area opens the picker once. Avoid programmatic clicks to prevent double dialogs.

        // Chunked uploader class
        class ChunkedUploader {
            constructor(file, uploadUrl, chunkSize = 1024 * 1024) {
                this.file = file;
                this.uploadUrl = uploadUrl;
                this.chunkSize = chunkSize;
                this.totalChunks = Math.ceil(file.size / chunkSize);
                this.currentChunk = 0;
                this.uploadId = this.generateUploadId();
                this.progressCallback = null;
                this.errorCallback = null;
                this.successCallback = null;
            }
            generateUploadId() { return Date.now() + '_' + Math.random().toString(36).substr(2, 9); }
            setProgressCallback(cb) { this.progressCallback = cb; }
            setErrorCallback(cb) { this.errorCallback = cb; }
            setSuccessCallback(cb) { this.successCallback = cb; }
            async upload() {
                try {
                    for (let i = 0; i < this.totalChunks; i++) {
                        await this.uploadChunk(i);
                        this.currentChunk = i + 1;
                        if (this.progressCallback) {
                            const progress = Math.round((this.currentChunk / this.totalChunks) * 100);
                            this.progressCallback(progress);
                        }
                    }
                    const result = await this.finalizeUpload();
                    if (this.successCallback) this.successCallback(result);
                } catch (err) { if (this.errorCallback) this.errorCallback(err); }
            }
            async uploadChunk(chunkIndex) {
                const start = chunkIndex * this.chunkSize;
                const end = Math.min(start + this.chunkSize, this.file.size);
                const chunk = this.file.slice(start, end);
                const formData = new FormData();
                formData.append('chunk', chunk);
                formData.append('chunkIndex', chunkIndex);
                formData.append('totalChunks', this.totalChunks);
                formData.append('uploadId', this.uploadId);
                formData.append('fileName', this.file.name);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                const res = await fetch('/admin/sermons/upload-chunk', { method: 'POST', body: formData });
                if (!res.ok) throw new Error(`Chunk upload failed: ${res.statusText}`);
                return res.json();
            }
            async finalizeUpload() {
                const formData = new FormData();
                formData.append('uploadId', this.uploadId);
                formData.append('fileName', this.file.name);
                formData.append('totalChunks', this.totalChunks);
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                const res = await fetch('/admin/sermons/finalize-upload', { method: 'POST', body: formData });
                if (!res.ok) throw new Error(`Upload finalization failed: ${res.statusText}`);
                return res.json();
            }
        }

        // Handle file processing - VIDEO
        function handleVideoFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                // Check if it's a video
                if (!file.type.startsWith('video/')) {
                    alert('Please select a video file.');
                    return;
                }

                // Check file size (500MB limit)
                if (file.size > 500 * 1024 * 1024) {
                    alert('Warning: File size exceeds 500MB. This may cause upload issues.');
                }

                // Update UI
                uploadArea.style.display = 'none';
                videoPreviewContainer.classList.remove('d-none');

                // Display file name
                fileName.textContent = file.name;

                // Create video preview
                const url = URL.createObjectURL(file);
                videoPreview.querySelector('source').src = url;
                videoPreview.load();

                // Get video duration for the duration field
                videoPreview.onloadedmetadata = function() {
                    // Duration in seconds
                    const durationSec = Math.round(videoPreview.duration);
                    // Convert to minutes for display
                    const durationMin = Math.round(durationSec / 60);

                    durationMinutesInput.value = durationMin;
                    durationInput.value = durationSec;
                };

                // Start chunked upload for large files
                if (file.size > 20 * 1024 * 1024) { // 20MB threshold
                    startChunkedUpload(file);
                } else {
                    simulateUploadProgress();
                }
            }
        }

        // Handle file processing - AUDIO
        function handleAudioFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                // Check if it's an audio
                if (!file.type.startsWith('audio/')) {
                    alert('Please select an audio file.');
                    return;
                }

                // Check file size (200MB limit)
                if (file.size > 200 * 1024 * 1024) {
                    alert('Warning: File size exceeds 200MB. This may cause upload issues.');
                }

                // Update UI
                audioUploadArea.style.display = 'none';
                audioPreviewContainer.classList.remove('d-none');

                // Display file name
                audioFileName.textContent = file.name;

                // Create audio preview
                const url = URL.createObjectURL(file);
                audioPreview.querySelector('source').src = url;
                audioPreview.load();

                // Get audio duration if not already set by video
                if (!durationInput.value) {
                    audioPreview.onloadedmetadata = function() {
                        // Duration in seconds
                        const durationSec = Math.round(audioPreview.duration);
                        // Convert to minutes for display
                        const durationMin = Math.round(durationSec / 60);

                        durationMinutesInput.value = durationMin;
                        durationInput.value = durationSec;
                    };
                }
            }
        }

        // Handle file processing - THUMBNAIL
        function handleThumbnailFiles(files) {
            if (files.length > 0) {
                const file = files[0];

                // Check if it's an image
                if (!file.type.startsWith('image/')) {
                    alert('Please select an image file.');
                    return;
                }

                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Warning: File size exceeds 5MB. This may cause upload issues.');
                }

                // Update UI
                thumbnailUploadArea.style.display = 'none';
                thumbnailPreviewContainer.classList.remove('d-none');

                // Display file name
                thumbnailFileName.textContent = file.name;

                // Create thumbnail preview
                const url = URL.createObjectURL(file);
                thumbnailPreview.src = url;
            }
        }

        // Remove selected files
        removeFileBtn.addEventListener('click', function() {
            // Reset file input
            videoInput.value = '';

            // Reset UI
            uploadArea.style.display = 'block';
            videoPreviewContainer.classList.add('d-none');

            // Hide progress
            uploadProgress.classList.add('d-none');
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
        });

        removeAudioBtn.addEventListener('click', function() {
            // Reset file input
            audioInput.value = '';

            // Reset UI
            audioUploadArea.style.display = 'block';
            audioPreviewContainer.classList.add('d-none');
        });

        removeThumbnailBtn.addEventListener('click', function() {
            // Reset file input
            thumbnailInput.value = '';

            // Reset UI
            thumbnailUploadArea.style.display = 'block';
            thumbnailPreviewContainer.classList.add('d-none');
        });

        // Simulate upload progress (for UI demonstration)
        function simulateUploadProgress() {
            uploadProgress.classList.remove('d-none');

            let progress = 0;
            const interval = setInterval(() => {
                progress += Math.random() * 10;
                if (progress > 100) {
                    progress = 100;
                    clearInterval(interval);
                }

                const roundedProgress = Math.round(progress);
                progressBar.style.width = roundedProgress + '%';
                progressBar.setAttribute('aria-valuenow', roundedProgress);
                progressBar.textContent = roundedProgress + '%';

                if (roundedProgress === 100) {
                    setTimeout(() => {
                        progressBar.classList.remove('progress-bar-animated');
                    }, 500);
                }
            }, 300);
        }

        function startChunkedUpload(file) {
            uploadProgress.classList.remove('d-none');
            // Use 5MB chunks for better throughput
            const uploader = new ChunkedUploader(file, '/admin/sermons/upload-chunk', 5 * 1024 * 1024);
            uploader.setProgressCallback((progress) => {
                progressBar.style.width = progress + '%';
                progressBar.setAttribute('aria-valuenow', progress);
                progressBar.textContent = progress + '%';
                if (progress === 100) {
                    progressBar.classList.remove('progress-bar-animated');
                }
            });
            uploader.setErrorCallback((error) => {
                console.error('Upload failed:', error);
                alert('Upload failed: ' + error.message);
                resetVideoUpload();
            });
            uploader.setSuccessCallback((result) => {
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'chunked_upload_id';
                hiddenInput.value = uploader.uploadId;
                document.getElementById('uploadForm').appendChild(hiddenInput);

                if (result && result.path) {
                    const pathInput = document.createElement('input');
                    pathInput.type = 'hidden';
                    pathInput.name = 'video_path';
                    pathInput.value = result.path;
                    document.getElementById('uploadForm').appendChild(pathInput);
                }
            });
            uploader.upload();
        }

        function resetVideoUpload() {
            videoInput.value = '';
            uploadArea.style.display = 'block';
            videoPreviewContainer.classList.add('d-none');
            uploadProgress.classList.add('d-none');
            progressBar.style.width = '0%';
            progressBar.textContent = '0%';
            progressBar.setAttribute('aria-valuenow', '0');
        }

        // Form validation
        uploadForm.addEventListener('submit', function(e) {
            // Calculate duration in seconds from minutes input if not already set
            if (durationMinutesInput.value && !durationInput.value) {
                durationInput.value = durationMinutesInput.value * 60;
            }

            if (!this.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            this.classList.add('was-validated');
        });

        // Initialize any Bootstrap components
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection
