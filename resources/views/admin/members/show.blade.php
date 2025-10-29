@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header with Animation -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    <h1 class="fw-bold text-primary mb-1">Member Details</h1>
                    <p class="text-muted">Viewing complete information for this member</p>
                </div>
                <div class="ms-auto">
                    <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Members
                    </a>
                </div>
            </div>

            <!-- Member Details Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden animate__animated animate__fadeIn">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="avatar-circle bg-white text-primary me-3">
                        {{ strtoupper(substr($member->user->first_name ?? $member->user->last_name ?? 'U', 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="card-title mb-0 fw-bold">
                                {{ trim(($member->user->first_name ?? '') . ' ' . ($member->user->middle_name ?? '') . ' ' . ($member->user->last_name ?? '')) ?: 'Unnamed User' }}
                            </h4>
                            <p class="mb-0 opacity-75">
                                <i class="bi bi-envelope me-1"></i> {{ $member->user->email ?? 'N/A' }}
                            </p>
                        </div>
                        <div class="ms-auto">
                            {!! $member->status_badge !!}
                                </span>
                           
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="row g-0">
                        <!-- Personal Information Column -->
                        <div class="col-md-6 border-end">
                            <div class="p-4">
                                <h5 class="mb-4 pb-2 border-bottom">
                                    <i class="bi bi-person-badge me-2 text-primary"></i>Personal Information
                                </h5>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-telephone me-2"></i>Phone:
                                    </div>
                                    <div class="fw-medium">{{ $member->phone_number ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-geo-alt me-2"></i>Address:
                                    </div>
                                    <div class="fw-medium">{{ $member->address ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-calendar3 me-2"></i>Date of Birth:
                                    </div>
                                    <div class="fw-medium">{{ $member->date_of_birth ?? 'N/A' }}</div>
                                </div>

                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-gender-ambiguous me-2"></i>Gender:
                                    </div>
                                    <div class="fw-medium">{{ $member->gender ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-heart me-2"></i>Marital Status:
                                    </div>
                                    <div class="fw-medium">{{ $member->marital_status ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-telephone-plus me-2"></i>Emergency:
                                    </div>
                                    <div class="fw-medium">{{ $member->emergency_contact ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Membership Details Column -->
                        <div class="col-md-6">
                            <div class="p-4">
                                <h5 class="mb-4 pb-2 border-bottom">
                                    <i class="bi bi-card-checklist me-2 text-primary"></i>Membership Details
                                </h5>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-calendar-event me-2"></i>Member Since:
                                    </div>
                                    <div class="fw-medium">{{ $member->date_of_membership ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-water me-2"></i>Baptism Date:
                                    </div>
                                    <div class="fw-medium">{{ $member->baptism_date ?? 'N/A' }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-clock-history me-2"></i>Created:
                                    </div>
                                    <div class="fw-medium">{{ $member->created_at->format('M d, Y H:i') }}</div>
                                </div>
                                
                                <div class="mb-3 d-flex">
                                    <div class="text-muted" style="width: 140px;">
                                        <i class="bi bi-clock-history me-2"></i>Updated:
                                    </div>
                                    <div class="fw-medium">{{ $member->updated_at->format('M d, Y H:i') }}</div>
                                </div>
                                
                                @if($member->notes)
                                <div class="mt-4">
                                    <h6><i class="bi bi-sticky me-2 text-primary"></i>Notes:</h6>
                                    <div class="p-3 bg-light rounded-3 mt-2">
                                        {{ $member->notes }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card-footer bg-white p-4">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.members.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-2"></i>Back to Members
                        </a>
                        
                        <div>
                            <a href="{{ route('admin.members.edit', $member->id) }}" class="btn btn-warning me-2">
                                <i class="bi bi-pencil me-2"></i>Edit Member
                            </a>
                            <form action="{{ route('admin.members.destroy', $member->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Are you sure you want to delete this member?')">
                                    <i class="bi bi-trash me-2"></i>Delete Member
                                </button>
                            </form>
                        </div>
                    </div>
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

    /* Avatar Circle */
    .avatar-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
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

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .col-md-6.border-end {
            border-right: none !important;
            border-bottom: 1px solid #dee2e6;
            margin-bottom: 1.5rem;
        }
    }
</style>
@endsection