@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Page Header -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    <h1 class="fw-bold text-primary mb-1">Add New Member</h1>
                    <p class="text-muted">Create a new member profile</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-1"></i> Back to Members
                    </a>
                </div>
            </div>

            <!-- Form Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-plus fs-4 me-2"></i>
                        <h4 class="card-title mb-0">Member Information</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('admin.members.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="generated_password" id="hiddenPassword" value="{{ $generatedPassword ?? '' }}">
                        
                        <div class="alert alert-info mb-4">
                            <i class="bi bi-info-circle-fill me-2"></i>
                            <strong>Note:</strong> A default secure password will be automatically generated for the new member’s account and sent to your Gmail account.
                        </div>
                        
                        <!-- Generated Password Display -->
                        <div class="password-section mb-5">
                            <div class="section-header mb-4">
                                <h5 class="section-title"><i class="bi bi-key-fill text-warning me-2"></i>Generated Password</h5>
                            </div>
                            <div class="col-md-10">
                                <div class="password-container">
                                    <input type="text" class="form-control password-input" id="generatedPassword" readonly 
                                           value="{{ $generatedPassword ?? '' }}" 
                                           onclick="this.select()" title="Click to select all text">
                                    <button class="btn copy-btn" type="button" onclick="copyPassword()">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <small class="form-text">Click to select • Copy button available • User can change after first login</small>
                            </div>
                        </div>
                        
                        <div class="info-section mb-5">
                            <div class="section-header mb-4">
                                <h5 class="section-title"><i class="bi bi-person-fill text-primary me-2"></i>Basic Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control modern-input @error('first_name') is-invalid @enderror" 
                                               id="first_name" name="first_name" value="{{ old('first_name') }}" 
                                               placeholder="First Name" required>
                                        <label for="first_name">First Name <span class="text-danger">*</span></label>
                                        @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="form-floating">
                                        <input type="text" class="form-control modern-input @error('last_name') is-invalid @enderror" 
                                               id="last_name" name="last_name" value="{{ old('last_name') }}" 
                                               placeholder="Last Name" required>
                                        <label for="last_name">Last Name <span class="text-danger">*</span></label>
                                        @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                
                                <div class="col-12 mb-4">
                                    <div class="form-floating">
                                        <input type="email" class="form-control modern-input @error('email') is-invalid @enderror" 
                                               id="email" name="email" value="{{ old('email') }}" 
                                               placeholder="Email Address" required>
                                        <label for="email">Email Address <span class="text-danger">*</span></label>
                                        @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                

                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <a href="{{ route('admin.members.index') }}" class="btn cancel-btn">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                            <button type="submit" class="btn submit-btn">
                                <i class="bi bi-person-plus me-2"></i>Create Member
                            </button>
                        </div>
                    </form>
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
        background: linear-gradient(120deg, #64b5f6 0%, #2196f3 100%);
    }
    
    /* Form Section Styling */
    .section-header {
        border-left: 4px solid #2196f3;
        padding-left: 15px;
        margin-bottom: 20px;
    }
    
    .section-title {
        color: #1976d2;
        font-weight: 600;
        margin: 0;
    }
    
    /* Modern Form Inputs */
    .modern-input {
        border: 2px solid #e3f2fd;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: #fafafa;
    }
    
    .modern-input:focus {
        border-color: #2196f3;
        box-shadow: 0 0 0 3px rgba(33, 150, 243, 0.1);
        background: white;
        transform: translateY(-1px);
    }
    
    .form-floating > .modern-input {
        padding-top: 1.625rem;
        padding-bottom: 0.625rem;
    }
    
    .form-floating > label {
        color: #666;
        font-weight: 500;
    }
    
    /* Password Section */
    .password-section {
        background: linear-gradient(135deg, #e3f2fd 0%, #bbdefb 100%);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #a2c0d9;
    }
    
    .password-container {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }
    
    .password-input {
        font-family: 'Courier New', monospace;
        font-weight: bold;
        background: white;
        border: 2px solid #64b5f6;
        border-radius: 8px;
        flex: 1;
    }
    
    .copy-btn {
        background: #2196f3;
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }
    
    .copy-btn:hover {
        background: #1976d2;
        transform: translateY(-1px);
    }
    
    .form-text {
        color: #1565c0;
        font-size: 0.85em;
        margin-top: 8px;
    }
    
    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 12px;
        margin-top: 32px;
        padding-top: 24px;
        border-top: 2px solid #f5f5f5;
    }
    
    .cancel-btn {
        background: #f5f5f5;
        color: #666;
        border: 2px solid #e0e0e0;
        border-radius: 10px;
        padding: 12px 24px;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .cancel-btn:hover {
        background: #eeeeee;
        color: #555;
        transform: translateY(-1px);
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #2196f3 0%, #1976d2 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 12px 32px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(33, 150, 243, 0.3);
        transition: all 0.2s ease;
    }
    
    .submit-btn:hover {
        background: linear-gradient(135deg, #1976d2 0%, #1565c0 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(33, 150, 243, 0.4);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Password is already generated on the server side
    // No additional action needed
});

// Function to copy password to clipboard
function copyPassword() {
    const passwordElement = document.getElementById('generatedPassword');
    const password = passwordElement.value;
    const button = event.target;
    
    if (!password) {
        alert('No password to copy');
        return;
    }
    
    // Try modern clipboard API first
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(password).then(function() {
            showCopySuccess(button);
        }).catch(function(err) {
            console.error('Clipboard API failed:', err);
            // Fallback to legacy method
            fallbackCopyTextToClipboard(password, button);
        });
    } else {
        // Fallback to legacy method
        fallbackCopyTextToClipboard(password, button);
    }
}

// Fallback copy method for older browsers or non-secure contexts
function fallbackCopyTextToClipboard(text, button) {
    // Create a temporary textarea element
    const textArea = document.createElement("textarea");
    textArea.value = text;
    
    // Avoid scrolling to bottom
    textArea.style.top = "0";
    textArea.style.left = "0";
    textArea.style.position = "fixed";
    textArea.style.opacity = "0";
    
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        const successful = document.execCommand('copy');
        if (successful) {
            showCopySuccess(button);
        } else {
            showCopyError(button, text);
        }
    } catch (err) {
        console.error('Fallback copy failed:', err);
        showCopyError(button, text);
    }
    
    document.body.removeChild(textArea);
}

// Show success feedback
function showCopySuccess(button) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-check"></i> Copied!';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-success');
    
    // Reset after 2 seconds
    setTimeout(function() {
        button.innerHTML = originalText;
        button.classList.remove('btn-success');
        button.classList.add('btn-outline-secondary');
    }, 2000);
}

// Show error feedback
function showCopyError(button, password) {
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Failed';
    button.classList.remove('btn-outline-secondary');
    button.classList.add('btn-danger');
    
    // Show password in a prompt for manual copying
    setTimeout(function() {
        const userInput = prompt('Copy this password manually:', password);
        button.innerHTML = originalText;
        button.classList.remove('btn-danger');
        button.classList.add('btn-outline-secondary');
    }, 1000);
}

</script>
@endsection
