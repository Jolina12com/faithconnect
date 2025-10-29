@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header with Animation -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    <h1 class="fw-bold text-primary mb-1">Edit Member</h1>
                    <p class="text-muted">Update member information and membership status</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Members
                    </a>
                </div>
            </div>

            <!-- Edit Member Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-gear fs-4 me-2"></i>
                        <h4 class="card-title mb-0">Member Information</h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                            <i class="bi bi-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Oops!</strong> There were some issues with your input.
                            <ul class="mb-0 mt-2">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form action="{{ route('admin.members.update', $member->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-4">
                            <!-- Personal Information Section -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header bg-light py-3">
                                        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Personal Information</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label">Name <span class="text-danger">*</span></label>
                                            <div class="row g-2">
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control @error('first_name') is-invalid @enderror" id="first_name" name="first_name" placeholder="First name" value="{{ old('first_name', $member->user->first_name ?? '') }}" required>
                                                    @error('first_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control @error('middle_name') is-invalid @enderror" id="middle_name" name="middle_name" placeholder="Middle name" value="{{ old('middle_name', $member->user->middle_name ?? '') }}">
                                                    @error('middle_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-4">
                                                    <input type="text" class="form-control @error('last_name') is-invalid @enderror" id="last_name" name="last_name" placeholder="Last name" value="{{ old('last_name', $member->user->last_name ?? '') }}" required>
                                                    @error('last_name')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-envelope text-muted"></i>
                                                </span>
                                                <input type="email" class="form-control border-start-0 @error('email') is-invalid @enderror"
                                                    id="email" name="email" value="{{ old('email', $member->user->email) }}" required>
                                                @error('email')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="phone_number" class="form-label">Phone Number</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-telephone text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0 @error('phone_number') is-invalid @enderror"
                                                    id="phone_number" name="phone_number" value="{{ old('phone_number', $member->phone_number) }}">
                                                @error('phone_number')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-geo-alt text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0 @error('address') is-invalid @enderror"
                                                    id="address" name="address" value="{{ old('address', $member->address) }}">
                                                @error('address')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_of_birth" class="form-label">Date of Birth</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-calendar3 text-muted"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0 @error('date_of_birth') is-invalid @enderror"
                                                    id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $member->date_of_birth) }}">
                                                @error('date_of_birth')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="gender" class="form-label">Gender</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-gender-ambiguous text-muted"></i>
                                                </span>
                                                <select class="form-select border-start-0 @error('gender') is-invalid @enderror" id="gender" name="gender">
                                                    <option value="">-- Select Gender --</option>
                                                    <option value="Male" {{ old('gender', $member->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                                    <option value="Female" {{ old('gender', $member->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                                    <option value="Other" {{ old('gender', $member->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                                    <option value="Prefer not to say" {{ old('gender', $member->gender) == 'Prefer not to say' ? 'selected' : '' }}>Prefer not to say</option>
                                                </select>
                                                @error('gender')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="marital_status" class="form-label">Marital Status</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-heart text-muted"></i>
                                                </span>
                                                <select class="form-select border-start-0 @error('marital_status') is-invalid @enderror"
                                                id="marital_status" name="marital_status">
                                            <option value="">-- Select Marital Status --</option>
                                            <option value="Single" {{ old('marital_status', $member->marital_status) == 'single' ? 'selected' : '' }}>Single</option>
                                            <option value="Married" {{ old('marital_status', $member->marital_status) == 'married' ? 'selected' : '' }}>Married</option>
                                            <option value="Divorced" {{ old('marital_status', $member->marital_status) == 'divorced' ? 'selected' : '' }}>Divorced</option>
                                            <option value="Widowed" {{ old('marital_status', $member->marital_status) == 'widowed' ? 'selected' : '' }}>Widowed</option>
                                        </select>

                                                @error('marital_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="emergency_contact" class="form-label">Emergency Contact</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-telephone-plus text-muted"></i>
                                                </span>
                                                <input type="text" class="form-control border-start-0 @error('emergency_contact') is-invalid @enderror"
                                                    id="emergency_contact" name="emergency_contact" value="{{ old('emergency_contact', $member->emergency_contact) }}">
                                                @error('emergency_contact')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Membership Details Section -->
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm rounded-4 h-100">
                                    <div class="card-header bg-light py-3">
                                        <h5 class="mb-0"><i class="bi bi-card-checklist me-2"></i>Membership Details</h5>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="mb-3">
                                            <label for="membership_status" class="form-label">Membership Status <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-shield-check text-muted"></i>
                                                </span>
                                                <select class="form-select border-start-0 @error('membership_status') is-invalid @enderror"
                                                    id="membership_status" name="membership_status" required>
                                                    <option value="">-- Select Status --</option>
                                                    <option value="active_member" {{ old('membership_status', $member->membership_status) == 'active_member' ? 'selected' : '' }}>Member</option>
                                                    <option value="new_member" {{ old('membership_status', $member->membership_status) == 'new_member' ? 'selected' : '' }}>New Member</option>
                                                </select>
                                                @error('membership_status')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="date_of_membership" class="form-label">Date of Membership</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-calendar-event text-muted"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0 @error('date_of_membership') is-invalid @enderror"
                                                    id="date_of_membership" name="date_of_membership" value="{{ old('date_of_membership', $member->date_of_membership) }}">
                                                @error('date_of_membership')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="baptism_date" class="form-label">Baptismal Date</label>
                                            <div class="input-group">
                                                <span class="input-group-text bg-white border-end-0">
                                                    <i class="bi bi-water text-muted"></i>
                                                </span>
                                                <input type="date" class="form-control border-start-0 @error('baptism_date') is-invalid @enderror"
                                                    id="baptism_date" name="baptism_date" value="{{ old('baptism_date', $member->baptism_date) }}">
                                                @error('baptism_date')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>


                                        <div class="mb-3">
                                            <p class="mb-1 text-muted small">
                                                <i class="bi bi-clock-history me-1"></i> Created: {{ $member->created_at->format('M d, Y H:i') }}
                                            </p>
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-clock-history me-1"></i> Last Updated: {{ $member->updated_at->format('M d, Y H:i') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Form Actions -->
                            <div class="col-12 mt-4">
                                <div class="d-flex justify-content-end gap-3">
                                    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle me-2"></i>Cancel
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="bi bi-save me-2"></i>Update Member
                                    </button>
                                </div>
                            </div>
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
        background: linear-gradient(120deg, #4e73df 0%, #224abe 100%);
    }

    /* Soft Background Colors for Badges and Buttons */
    .bg-success-soft {
        background-color: rgba(40, 167, 69, 0.15);
    }

    .bg-warning-soft {
        background-color: rgba(255, 193, 7, 0.15);
    }

    .bg-danger-soft {
        background-color: rgba(220, 53, 69, 0.15);
    }

    .bg-secondary-soft {
        background-color: rgba(108, 117, 125, 0.15);
    }

    .bg-primary-soft {
        background-color: rgba(78, 115, 223, 0.15);
    }

    /* Soft Background Buttons */
    .btn-primary-soft {
        color: #4e73df;
        background-color: rgba(78, 115, 223, 0.15);
        border-color: transparent;
    }

    .btn-primary-soft:hover {
        color: #fff;
        background-color: #4e73df;
    }

    .btn-warning-soft {
        color: #f6c23e;
        background-color: rgba(246, 194, 62, 0.15);
        border-color: transparent;
    }

    .btn-warning-soft:hover {
        color: #fff;
        background-color: #f6c23e;
    }

    .btn-danger-soft {
        color: #e74a3b;
        background-color: rgba(231, 74, 59, 0.15);
        border-color: transparent;
    }

    .btn-danger-soft:hover {
        color: #fff;
        background-color: #e74a3b;
    }

    /* Avatar Circle */
    .avatar-circle {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
    }

    /* Rounded corners */
    .rounded-4 {
        border-radius: 10px;
    }

    /* Button enhancements */
    .btn-primary {
        background-color: #4e73df;
        border-color: #4e73df;
    }

    .btn-primary:hover {
        background-color: #224abe;
        border-color: #224abe;
    }

    /* Card enhancements */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.175);
    }

    /* Form styling */
    .form-control:focus, .form-select:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.25rem rgba(78, 115, 223, 0.25);
    }

    .input-group-text {
        color: #6c757d;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .btn-group {
            width: 100%;
        }

        .btn-group .btn {
            flex: 1;
        }
    }
</style>
@endsection
