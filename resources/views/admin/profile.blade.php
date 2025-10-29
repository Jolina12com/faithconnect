@extends('admin.dashboard')

@section('content')
<style>
    /* General Styling */
    .profile-container {
        background-color: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.05);
        padding: 30px;
    }

    .section-title {
        color: #333;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f1f1f1;
    }

    /* Profile Image Section */
    .profile-image-container {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto 20px;
    }

    .profile-image {
        width: 180px;
        height: 180px;
        object-fit: cover;
        border-radius: 50%;
        border: 4px solid #fff;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .upload-icon {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background-color: #0d6efd;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
    }

    .upload-icon:hover {
        background-color: #0b5ed7;
        transform: scale(1.05);
    }

    .hidden-file-input {
        display: none;
    }

    .user-name {
        font-size: 1.5rem;
        font-weight: 600;
        margin-top: 10px;
    }

    .user-role {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 20px;
    }

    /* Form Styling */
    .form-card {
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        margin-bottom: 20px;
        transition: all 0.3s ease;
    }

    .form-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    .card-header {
        background-color: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 15px 20px;
        border-radius: 10px 10px 0 0;
    }

    .card-body {
        padding: 25px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-control {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .form-label {
        font-weight: 500;
        margin-bottom: 8px;
        color: #495057;
    }

    .btn-primary {
        background-color: #0d6efd;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #0b5ed7;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-outline-secondary {
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .btn-outline-secondary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Password Strength Indicator */
    .password-strength {
        height: 5px;
        border-radius: 5px;
        margin-top: 5px;
        transition: all 0.3s ease;
    }

    .password-feedback {
        font-size: 0.8rem;
        margin-top: 5px;
    }

    /* Alert Styling */
    .alert {
        border-radius: 8px;
        margin-bottom: 20px;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .profile-section {
            margin-bottom: 30px;
        }
    }
</style>

<div class="container profile-container py-4">
    <h4 class="section-title">My Profile</h4>

    <div class="row">
        <div class="col-lg-4 profile-section">
            <!-- Profile Image Section -->
            <div class="form-card text-center p-4">
                <div class="profile-image-container">
                    @if (Auth::user()->profile_picture)
                        <img src="{{ asset('storage/' . Auth::user()->profile_picture) }}" alt="Profile Picture" class="profile-image" id="profileImage">
                    @else
                        <img src="{{ asset('storage/default-profile.jpg') }}" alt="Default Profile Picture" class="profile-image" id="profileImage">
                    @endif

                    <label for="profile_picture" class="upload-icon">
                        <i class="fas fa-camera"></i>
                    </label>
                </div>

                <h5 class="user-name">{{ Auth::user()->full_name }}</h5>
                <p class="user-role">Administrator</p>

                <!-- Hidden Profile Picture Upload Form -->
                <form action="{{ route('admin.profile.upload') }}" method="POST" enctype="multipart/form-data" id="profilePictureForm">
                    @csrf
                    <input type="file" name="profile_picture" id="profile_picture" class="hidden-file-input" accept="image/*">

                    <div class="mt-3">
                        <button type="button" id="changePhotoBtn" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-image me-1"></i> Change Photo
                        </button>
                    </div>
                </form>

                <div class="mt-4">
                    <div class="d-flex justify-content-between text-muted mb-2">
                        <small>Profile Completion</small>
                        <small>85%</small>
                    </div>
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-primary" role="progressbar" style="width: 85%;" aria-valuenow="85" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-muted mb-1"><i class="fas fa-envelope me-2"></i> {{ Auth::user()->email }}</p>
                    <p class="text-muted mb-1"><i class="fas fa-calendar me-2"></i> Joined {{ Auth::user()->created_at->format('M d, Y') }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <!-- Success Message -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Profile Update Form -->
            <div class="form-card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> Personal Information</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.profile.update') }}" method="POST" id="profileForm">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" id="first_name" name="first_name" class="form-control @error('first_name') is-invalid @enderror" value="{{ Auth::user()->first_name }}" required>
                                    @error('first_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="middle_name" class="form-label">Middle Name (Optional)</label>
                                    <input type="text" id="middle_name" name="middle_name" class="form-control @error('middle_name') is-invalid @enderror" value="{{ Auth::user()->middle_name }}">
                                    @error('middle_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" id="last_name" name="last_name" class="form-control @error('last_name') is-invalid @enderror" value="{{ Auth::user()->last_name }}" required>
                                    @error('last_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ Auth::user()->email }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" id="phone_number" name="phone_number" class="form-control @error('phone_number') is-invalid @enderror" value="{{ Auth::user()->phone_number }}">
                                    @error('phone_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" id="date_of_birth" name="date_of_birth" class="form-control @error('date_of_birth') is-invalid @enderror" value="{{ Auth::user()->date_of_birth }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                                        <option value="" {{ !Auth::user()->gender ? 'selected' : '' }}>Select gender</option>
                                        <option value="male" {{ Auth::user()->gender == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ Auth::user()->gender == 'female' ? 'selected' : '' }}>Female</option>
                                        <option value="other" {{ Auth::user()->gender == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="address" class="form-label">Address</label>
                                    <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" value="{{ Auth::user()->address }}">
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Password Change Form -->
            <div class="form-card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fas fa-lock me-2"></i> Change Password</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.password.change') }}" method="POST" id="passwordForm">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label for="current_password" class="form-label">Current Password</label>
                            <div class="input-group">
                                <input type="password" id="current_password" name="current_password" class="form-control @error('current_password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            @error('current_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password" class="form-label">New Password</label>
                            <div class="input-group">
                                <input type="password" id="new_password" name="new_password" class="form-control @error('new_password') is-invalid @enderror" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div class="password-strength" id="passwordStrength"></div>
                            <div class="password-feedback" id="passwordFeedback"></div>
                            @error('new_password')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control" required>
                                <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                            <div id="passwordMatch" class="small mt-1"></div>
                        </div>

                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-primary" id="changePasswordBtn">
                                <i class="fas fa-key me-1"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Profile picture preview and upload
        const profileInput = document.getElementById('profile_picture');
        const profileImage = document.getElementById('profileImage');
        const profileForm = document.getElementById('profilePictureForm');
        const changePhotoBtn = document.getElementById('changePhotoBtn');

        // Handle the "Change Photo" button click
        changePhotoBtn.addEventListener('click', function() {
            profileInput.click();
        });

        // Handle the upload icon click
        document.querySelector('.upload-icon').addEventListener('click', function() {
            profileInput.click();
        });

        // Preview and auto-submit when file is selected
        profileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                // Preview the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    profileImage.src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Submit the form
                profileForm.submit();
            }
        });

        // Password toggle visibility
        document.querySelectorAll('.toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');

                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-eye-slash');
                } else {
                    input.type = 'password';
                    icon.classList.remove('fa-eye-slash');
                    icon.classList.add('fa-eye');
                }
            });
        });

        // Password strength meter
        const newPassword = document.getElementById('new_password');
        const confirmation = document.getElementById('new_password_confirmation');
        const strengthBar = document.getElementById('passwordStrength');
        const feedback = document.getElementById('passwordFeedback');
        const matchFeedback = document.getElementById('passwordMatch');

        newPassword.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let message = '';

            // Calculate password strength
            if (password.length >= 8) strength += 1;
            if (password.match(/[a-z]+/)) strength += 1;
            if (password.match(/[A-Z]+/)) strength += 1;
            if (password.match(/[0-9]+/)) strength += 1;
            if (password.match(/[^a-zA-Z0-9]+/)) strength += 1;

            // Update the UI based on strength
            switch(strength) {
                case 0:
                case 1:
                    strengthBar.style.width = '20%';
                    strengthBar.style.backgroundColor = '#dc3545';
                    message = 'Very weak password';
                    break;
                case 2:
                    strengthBar.style.width = '40%';
                    strengthBar.style.backgroundColor = '#ffc107';
                    message = 'Weak password';
                    break;
                case 3:
                    strengthBar.style.width = '60%';
                    strengthBar.style.backgroundColor = '#fd7e14';
                    message = 'Moderate password';
                    break;
                case 4:
                    strengthBar.style.width = '80%';
                    strengthBar.style.backgroundColor = '#20c997';
                    message = 'Strong password';
                    break;
                case 5:
                    strengthBar.style.width = '100%';
                    strengthBar.style.backgroundColor = '#198754';
                    message = 'Very strong password';
                    break;
            }

            feedback.textContent = message;
            checkPasswordMatch();
        });

        // Password match checker
        confirmation.addEventListener('input', checkPasswordMatch);

        function checkPasswordMatch() {
            if (confirmation.value === '') {
                matchFeedback.textContent = '';
                matchFeedback.className = 'small mt-1';
                return;
            }

            if (newPassword.value === confirmation.value) {
                matchFeedback.textContent = 'Passwords match ✓';
                matchFeedback.className = 'small mt-1 text-success';
            } else {
                matchFeedback.textContent = 'Passwords do not match ✗';
                matchFeedback.className = 'small mt-1 text-danger';
            }
        }

        // Form validation
        const passwordForm = document.getElementById('passwordForm');
        passwordForm.addEventListener('submit', function(event) {
            if (newPassword.value !== confirmation.value) {
                event.preventDefault();
                matchFeedback.textContent = 'Passwords do not match ✗';
                matchFeedback.className = 'small mt-1 text-danger';
                confirmation.focus();
            }
        });

        // Auto dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    });
</script>
@endsection

