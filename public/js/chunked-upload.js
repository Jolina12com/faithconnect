// Initialize Resumable.js
const resumable = new Resumable({
    target: '/admin/sermons/chunked-upload',
    chunkSize: 1 * 1024 * 1024, // 1MB chunks
    simultaneousUploads: 3,
    testChunks: true,
    throttleProgressCallbacks: 1,
    fileParameterName: 'file',
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
});

// Handle file selection
resumable.on('fileAdded', function(file) {
    // Validate file type
    if (!file.file.type.startsWith('video/')) {
        alert('Please select a video file.');
        resumable.removeFile(file);
        return;
    }

    // Validate file size (1GB limit)
    if (file.size > 1024 * 1024 * 1024) {
        alert('File size exceeds 1GB. Please select a smaller file.');
        resumable.removeFile(file);
        return;
    }

    // Update UI
    uploadArea.style.display = 'none';
    videoPreviewContainer.classList.remove('d-none');
    fileName.textContent = file.fileName;

    // Create video preview
    const url = URL.createObjectURL(file.file);
    videoPreview.querySelector('source').src = url;
    videoPreview.load();

    // Get video duration
    videoPreview.onloadedmetadata = function() {
        const durationSec = Math.round(videoPreview.duration);
        const durationMin = Math.round(durationSec / 60);
        durationMinutesInput.value = durationMin;
        durationInput.value = durationSec;
    };
});

// Handle upload progress
resumable.on('fileProgress', function(file) {
    const percentComplete = Math.round(file.progress() * 100);
    uploadProgress.classList.remove('d-none');
    progressBar.style.width = percentComplete + '%';
    progressBar.setAttribute('aria-valuenow', percentComplete);
    progressBar.textContent = percentComplete + '%';
});

// Handle successful upload
resumable.on('fileSuccess', function(file, response) {
    try {
        const data = JSON.parse(response);
        if (data.success) {
            window.location.href = '/admin/sermons';
        } else {
            alert('Upload failed: ' + (data.message || 'Unknown error'));
            resetUploadUI();
        }
    } catch (e) {
        alert('Invalid server response');
        resetUploadUI();
    }
});

// Handle upload error
resumable.on('fileError', function(file, message) {
    alert('Upload failed: ' + message);
    resetUploadUI();
});

// Reset UI after upload
function resetUploadUI() {
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.disabled = false;
    submitButton.innerHTML = '<i class="fas fa-upload me-2"></i>Upload Sermon';
    uploadProgress.classList.add('d-none');
    progressBar.style.width = '0%';
    progressBar.textContent = '0%';
}

// Initialize Resumable.js with the file input
document.addEventListener('DOMContentLoaded', function() {
    const videoInput = document.getElementById('video');
    resumable.assignBrowse(videoInput);
    resumable.assignDrop(uploadArea);
}); 