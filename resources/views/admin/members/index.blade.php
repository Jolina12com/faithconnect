@extends('admin.dashboard')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header with Animation -->
            <div class="d-flex align-items-center mb-4 animate__animated animate__fadeIn">
                <div>
                    <h1 class="fw-bold text-primary mb-1">Manage Members</h1>
                    <p class="text-muted">Manage your organization's members and their status</p>
                </div>
                <div class="ms-auto">
                    <div class="btn-group rounded-pill shadow-sm" role="group">
                        <a href="{{ route('admin.members.index') }}" class="btn {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bi bi-people-fill me-1"></i> All
                        </a>
                        <a href="{{ route('admin.members.index', ['status' => 'member']) }}" class="btn {{ request('status') == 'member' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bi bi-person-check me-1"></i> Members
                        </a>
                        <a href="{{ route('admin.members.index', ['status' => 'new_member']) }}" class="btn {{ request('status') == 'new_member' ? 'btn-primary' : 'btn-outline-secondary' }}">
                            <i class="bi bi-star me-1"></i> New Members Only
                        </a>
                    </div>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {!! session('success') !!}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show animate__animated animate__fadeIn" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Action Bar -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn">
                <div class="card-body p-3">
                    <div class="row align-items-center">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <form action="{{ route('admin.members.index') }}" method="GET" class="d-flex">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0">
                                        <i class="bi bi-search text-muted"></i>
                                    </span>
                                    <input type="text" name="search" class="form-control border-start-0"
                                           placeholder="Search members..." value="{{ request('search') }}">
                                    <button type="submit" class="btn btn-primary">Search</button>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <a href="{{ route('admin.members.create') }}" class="btn btn-primary d-inline-flex align-items-center">
                                <i class="bi bi-person-plus me-2"></i>
                                Add New Member
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Members Table Card -->
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-people fs-4 me-2"></i>
                        <h4 class="card-title mb-0">Members List</h4>
                        <span class="badge bg-white text-primary ms-2 rounded-pill">{{ $members->count() }} {{ Str::plural('member', $members->count()) }}</span>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th width="5%" class="ps-4">#ID</th>
                                    <th width="25%">Name</th>
                                    <th width="25%">Email</th>
                                    <th width="25%">Membership Status</th>
                                    <th width="20%" class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($members as $member)
                                <tr class="animate__animated animate__fadeIn">
                                    <td class="ps-4 fw-bold text-muted" data-label="ID">{{ $member->id }}</td>
                                    <td data-label="Name">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle bg-primary text-white me-2">
                                                {{ strtoupper(substr($member->user_name, 0, 1)) }}
                                            </div>
                                            <div>{{ $member->user_name }}</div>
                                        </div>
                                    </td>
                                    <td data-label="Email">
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-envelope text-muted me-2 d-none d-md-inline"></i>
                                            <span class="text-break">{{ $member->user_email }}</span>
                                        </div>
                                    </td>
                                    <td class="status-column" data-label="Status">
                                        {!! $member->status_badge !!}
                                    </td>
                                    <td data-label="Actions">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.members.show', $member->id) }}"
                                               class="btn btn-sm btn-primary-soft" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.members.edit', $member->id) }}"
                                               class="btn btn-sm btn-warning-soft" title="Edit Member">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger-soft"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#deleteMemberModal"
                                                    data-member-id="{{ $member->id }}"
                                                    data-member-name="{{ $member->user_name }}"
                                                    title="Delete Member">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <div class="empty-state animate__animated animate__fadeIn">
                                            <i class="bi bi-people text-muted empty-state-icon"></i>
                                            <h4>No members found</h4>
                                            <p class="text-muted">Try adjusting your search or filter to find what you're looking for.</p>
                                            <a href="{{ route('admin.members.create') }}" class="btn btn-primary mt-3">
                                                <i class="bi bi-person-plus me-2"></i>
                                                Add New Member
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($members->count() > 0 && method_exists($members, 'links'))
                <div class="card-footer bg-white p-3">
                    {{ $members->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Delete Member Modal -->
<div class="modal fade" id="deleteMemberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">Confirm Deletion</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete member "<span id="memberNameToDelete"></span>"?</p>
                <p class="text-danger mb-0"><i class="bi bi-exclamation-triangle me-2"></i> This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteMemberForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Permanently</button>
                </form>
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

    /* Empty State Styling */
    .empty-state {
        padding: 2rem;
        text-align: center;
    }

    .empty-state-icon {
        font-size: 3rem;
        margin-bottom: 1rem;
    }

    /* Table hover enhancement */
    .table-hover tbody tr:hover {
        background-color: rgba(78, 115, 223, 0.05);
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

    /* Status column styling */
    .status-column {
        padding-left: 1rem !important;
    }

    /* Apply fixed widths to table columns */
    .table th, .table td {
        box-sizing: border-box;
    }

    /* Responsive adjustments */
    @media (max-width: 767.98px) {
        .btn-group {
            width: 100%;
        }

        .btn-group .btn {
            flex: 1;
        }

        .table-responsive {
            border-radius: 0;
        }

        .status-column {
            padding-left: 0.5rem !important;
        }

        /* Make badges full width on mobile */
        .badge {
            width: 100%;
            text-align: center;
        }
        
        /* Mobile card layout for table */
        .table thead {
            display: none;
        }
        
        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }
        
        .table tr {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 1rem;
            padding: 1rem;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .table td {
            border: none;
            padding: 0.5rem 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .table td:before {
            content: attr(data-label);
            font-weight: bold;
            color: #6c757d;
            flex: 0 0 40%;
        }
        
        .table td:nth-child(1):before { content: "ID: "; }
        .table td:nth-child(2):before { content: "Name: "; }
        .table td:nth-child(3):before { content: "Email: "; }
        .table td:nth-child(4):before { content: "Status: "; }
        .table td:nth-child(5):before { content: "Actions: "; }
        
        .table td:nth-child(5) {
            justify-content: center;
        }
    }
</style>
<script>
    // Function to copy password to clipboard
    function copyPassword() {
        const passwordElement = document.getElementById('generated-password');
        if (passwordElement) {
            const password = passwordElement.textContent;
            navigator.clipboard.writeText(password).then(function() {
                // Show success feedback
                const button = event.target;
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
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy password. Please copy manually: ' + password);
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Handle delete modal
        const deleteMemberModal = document.getElementById('deleteMemberModal');
        if (deleteMemberModal) {
            deleteMemberModal.addEventListener('show.bs.modal', function(event) {
                // Button that triggered the modal
                const button = event.relatedTarget;

                // Extract info from data attributes
                const memberId = button.getAttribute('data-member-id');
                const memberName = button.getAttribute('data-member-name');

                // Update the modal's content
                document.getElementById('memberNameToDelete').textContent = memberName;
                document.getElementById('deleteMemberForm').action = "{{ route('admin.members.index') }}/" + memberId;
            });
        }
    });
</script>
@endsection
