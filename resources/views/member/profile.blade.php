@extends('member.dashboard_member')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col">
            <h1 class="fw-bold">Profile Settings</h1>
            <p class="text-muted">Manage your profile and account settings.</p>
        </div>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Error Message -->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Left Panel - Profile Image & Quick Info -->
        <div class="col-lg-4">
            <div class="card shadow border-0 rounded-4 mb-4">
                <div class="card-body p-4 text-center">
                    <div class="position-relative mx-auto mb-4" style="width: 180px; height: 180px;">
                        <!-- Display Profile Picture -->
                        @if (Auth::user()->profile_picture)
                            <img id="profileImageDisplay" src="{{ asset('storage/' . Auth::user()->profile_picture) }}"
                                alt="Profile Picture"
                                class="rounded-circle border border-3 border-light shadow"
                                style="width: 180px; height: 180px; object-fit: cover;">
                        @else
                            <img id="profileImageDisplay" src="{{ asset('storage/default-profile.jpg') }}"
                                alt="Default Profile Picture"
                                class="rounded-circle border border-3 border-light shadow"
                                style="width: 180px; height: 180px; object-fit: cover;">
                        @endif

                        <!-- Edit image overlay button (opens file picker directly) -->
                        <button type="button" id="profileImageButton" class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow"
                               style="width: 44px; height: 44px;">
                            <i class="bi bi-camera-fill fs-5"></i>
                        </button>
                    </div>

                    <h4 class="mb-1 fw-bold">{{ Auth::user()->first_name }} {{ Auth::user()->middle_name }} {{ Auth::user()->last_name }}</h4>
                    <p class="text-muted mb-3">
                        <i class="bi bi-envelope me-1"></i>{{ Auth::user()->email }}
                    </p>
                </div>
            </div>

            <!-- Member Stats Card -->
            <div class="card shadow border-0 rounded-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">Account Summary</h5>
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between">
                            <span><i class="bi bi-calendar-check me-2 text-primary"></i>Member Since</span>
                            <span class="fw-medium">{{ Auth::user()->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between">
                            <span><i class="bi bi-shield-check me-2 text-primary"></i>Account Status</span>
                            <span class="badge bg-success rounded-pill">Active</span>
                        </div>
                        <div class="list-group-item border-0 px-0 d-flex justify-content-between">
                            <span><i class="bi bi-clock-history me-2 text-primary"></i>Last Update</span>
                            <span class="fw-medium">{{ Auth::user()->updated_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Profile Settings -->
        <div class="col-lg-8">
            <div class="card shadow border-0 rounded-4">
                <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4">
                    <ul class="nav nav-tabs card-header-tabs" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-medium" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal-info" type="button" role="tab" aria-controls="personal-info" aria-selected="true">
                                <i class="bi bi-person me-2"></i>Personal Info
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-medium" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button" role="tab" aria-controls="security" aria-selected="false">
                                <i class="bi bi-shield-lock me-2"></i>Security
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="card-body p-4">
                    <div class="tab-content" id="profileTabsContent">
                        <!-- Personal Info Tab -->
                        <div class="tab-pane fade show active" id="personal-info" role="tabpanel" aria-labelledby="personal-tab">
                            <form action="{{ route('profile.update') }}" method="POST" class="mt-3" id="profileForm">
                                @csrf
                                @method('PUT')

                                <div class="row mb-3">
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label for="first_name" class="form-label">First Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" id="first_name" name="first_name" class="form-control border-start-0 @error('first_name') is-invalid @enderror" value="{{ Auth::user()->first_name }}" required>
                                            @error('first_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3 mb-md-0">
                                        <label for="middle_name" class="form-label">Middle Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" id="middle_name" name="middle_name" class="form-control border-start-0 @error('middle_name') is-invalid @enderror" value="{{ Auth::user()->middle_name }}">
                                            @error('middle_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <label for="last_name" class="form-label">Last Name</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-person"></i>
                                            </span>
                                            <input type="text" id="last_name" name="last_name" class="form-control border-start-0 @error('last_name') is-invalid @enderror" value="{{ Auth::user()->last_name }}" required>
                                            @error('last_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Email Field (moved to new row) -->
                                <div class="row mb-3">
                                    <div class="col-md-12">
                                        <label for="email" class="form-label">Email Address</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-envelope"></i>
                                            </span>
                                            <input type="email" id="email" name="email" class="form-control border-start-0 @error('email') is-invalid @enderror" value="{{ Auth::user()->email }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="phone_number" class="form-label">Phone Number</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-telephone"></i>
                                            </span>
                                            <input type="tel" id="phone_number" name="phone_number" class="form-control border-start-0 @error('phone_number') is-invalid @enderror" value="{{ $member->phone_number ?? '' }}">
                                            @error('phone_number')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-calendar"></i>
                                            </span>
                                            <input type="date" id="date_of_birth" name="date_of_birth" class="form-control border-start-0 @error('date_of_birth') is-invalid @enderror" value="{{ $member->date_of_birth ?? '' }}">
                                            @error('date_of_birth')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6 mb-3 mb-md-0">
                                        <label for="gender" class="form-label">Gender</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-gender-ambiguous"></i>
                                            </span>
                                            <select id="gender" name="gender" class="form-select border-start-0 @error('gender') is-invalid @enderror">
                                                <option value="" {{ !isset($member->gender) ? 'selected' : '' }}>Select gender</option>
                                                <option value="male" {{ isset($member->gender) && $member->gender == 'male' ? 'selected' : '' }}>Male</option>
                                                <option value="female" {{ isset($member->gender) && $member->gender == 'female' ? 'selected' : '' }}>Female</option>
                                                <option value="other" {{ isset($member->gender) && $member->gender == 'other' ? 'selected' : '' }}>Other</option>
                                                <option value="prefer_not_to_say" {{ isset($member->gender) && $member->gender == 'prefer_not_to_say' ? 'selected' : '' }}>Prefer not to say</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="marital_status" class="form-label">Marital Status</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light border-end-0">
                                                <i class="bi bi-people"></i>
                                            </span>
                                            <select id="marital_status" name="marital_status" class="form-select border-start-0 @error('marital_status') is-invalid @enderror">
                                                <option value="" {{ !isset($member->marital_status) ? 'selected' : '' }}>Select status</option>
                                                <option value="single" {{ isset($member->marital_status) && $member->marital_status == 'single' ? 'selected' : '' }}>Single</option>
                                                <option value="married" {{ isset($member->marital_status) && $member->marital_status == 'married' ? 'selected' : '' }}>Married</option>
                                                <option value="divorced" {{ isset($member->marital_status) && $member->marital_status == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                                <option value="widowed" {{ isset($member->marital_status) && $member->marital_status == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                                <option value="other" {{ isset($member->marital_status) && $member->marital_status == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                            @error('marital_status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="address" class="form-label">Address</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-geo-alt"></i>
                                        </span>
                                        <input type="text" id="address" name="address" class="form-control border-start-0 @error('address') is-invalid @enderror" value="{{ $member->address ?? '' }}">
                                        @error('address')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-exclamation-circle"></i>
                                        </span>
                                        <input type="text" id="emergency_contact" name="emergency_contact" class="form-control border-start-0 @error('emergency_contact') is-invalid @enderror" value="{{ $member->emergency_contact ?? '' }}" placeholder="Name and phone number">
                                        @error('emergency_contact')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="form-text">Please provide a name and phone number for emergency contact</div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light me-2">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-primary px-4">
                                        <i class="bi bi-check-circle me-1"></i>Save Changes
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Security Tab -->
                        <div class="tab-pane fade" id="security" role="tabpanel" aria-labelledby="security-tab">
                            <form action="{{ route('password.change') }}" method="POST" class="mt-3">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock"></i>
                                        </span>
                                        <input type="password" id="current_password" name="current_password" class="form-control border-start-0 @error('current_password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="current_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('current_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" id="new_password" name="new_password" class="form-control border-start-0 @error('new_password') is-invalid @enderror" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        @error('new_password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="password-strength mt-2 small d-none">
                                        <div class="progress" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                        </div>
                                        <div class="mt-1 text-muted">Password strength: <span class="strength-text">Weak</span></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0">
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                        <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="form-control border-start-0" required>
                                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="new_password_confirmation">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-end">
                                    <button type="reset" class="btn btn-light me-2">
                                        <i class="bi bi-x-circle me-1"></i>Cancel
                                    </button>
                                    <button type="submit" class="btn btn-warning px-4">
                                        <i class="bi bi-shield-check me-1"></i>Update Password
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Profile Image Upload Form (file input triggered directly) -->
<form action="{{ route('profile.upload') }}" method="POST" enctype="multipart/form-data" id="profilePictureForm" style="display:none;">
    @csrf
    <input type="file" id="profile_picture" name="profile_picture" accept="image/*">
</form>

<script>
    // Profile picture: trigger file input from camera button, preview to main image and auto-submit
    (function(){
        const fileInput = document.getElementById('profile_picture');
        const displayImg = document.getElementById('profileImageDisplay');
        const uploadForm = document.getElementById('profilePictureForm');
        const triggerBtn = document.getElementById('profileImageButton');

        if (!fileInput || !displayImg || !uploadForm || !triggerBtn) return;

        // Open file picker when camera button clicked
        triggerBtn.addEventListener('click', function() {
            fileInput.click();
        });

        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, or GIF)');
                this.value = '';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                displayImg.src = event.target.result;
            }
            reader.readAsDataURL(file);

            // Auto-submit the hidden upload form after a short delay so preview is visible
            setTimeout(() => uploadForm.submit(), 300);
        });
    })();

    // Auto dismiss alerts after 5 seconds
    (function(){
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                try {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                } catch (e) {
                    // ignore if bootstrap not available
                }
            });
        }, 5000);
    })();
</script>

<style>
    /* Mobile responsiveness */
    @media (max-width: 991.98px) {
        .col-lg-4, .col-lg-8 {
            margin-bottom: 2rem;
        }
        
        .position-relative {
            width: 150px !important;
            height: 150px !important;
        }
        
        #profileImageDisplay {
            width: 150px !important;
            height: 150px !important;
        }
    }
    
    @media (max-width: 768px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .nav-tabs {
            flex-wrap: wrap;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.9rem;
            padding: 0.5rem 0.75rem;
        }
        
        .row.mb-3 .col-md-4,
        .row.mb-3 .col-md-6,
        .row.mb-3 .col-md-12 {
            margin-bottom: 1rem;
        }
        
        .input-group {
            flex-wrap: nowrap;
        }
        
        .input-group-text {
            min-width: 45px;
            justify-content: center;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .list-group-item {
            flex-direction: column;
            align-items: flex-start !important;
        }
        
        .list-group-item span:first-child {
            margin-bottom: 0.25rem;
        }
    }
    
    @media (max-width: 576px) {
        .position-relative {
            width: 120px !important;
            height: 120px !important;
        }
        
        #profileImageDisplay {
            width: 120px !important;
            height: 120px !important;
        }
        
        .card {
            margin: 0 -0.5rem;
        }
        
        .card-body {
            padding: 1rem;
        }
        
        .nav-tabs .nav-link {
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }
        
        .nav-tabs .nav-link i {
            display: none;
        }
        
        .btn {
            font-size: 0.9rem;
        }
        
        .d-flex.justify-content-end {
            flex-direction: column;
        }
        
        .d-flex.justify-content-end .btn {
            margin-bottom: 0.5rem;
            margin-right: 0 !important;
        }
        
        .d-flex.justify-content-end .btn:last-child {
            margin-bottom: 0;
        }
    }
</style>

@endsection