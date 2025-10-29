@extends('admin.dashboard')

@section('title', 'Donations')

@section('content')
<div class="donations-hero">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Donations</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="page-title text-dark">Donations Management</h1>
                <p class="text-muted">Manage and track all donations</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.donations.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i> Add New Donation
                </a>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show donation-alert" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif

        <div class="donations-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">All Donations</h5>
                <div>
                    <a href="{{ route('admin.donations.monthly') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-chart-bar me-1"></i> Monthly Reports
                    </a>
                    <a href="{{ route('admin.donations.transparency') }}" class="btn btn-sm btn-outline-info ms-2">
                        <i class="fas fa-file-invoice me-1"></i> Member Transparency
                    </a>
                </div>
            </div>

            <div class="card-body">
                @if($donations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle donations-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Donor Name</th>
                                <th>Donation Type</th>
                                <th>Details</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $donation)
                            <tr class="donation-row">
                                <td><span class="donation-id">#{{ $donation->id }}</span></td>
                                <td>
                                    <div class="donor-info">
                                        <strong>{{ $donation->donor_name ?? ($donation->first_name . ' ' . $donation->last_name) }}</strong>
                                        @if($donation->user_id)
                                            <small class="text-muted d-block">Registered Member</small>
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    @if($donation->donation_type === 'monetary')
                                        <span class="donation-badge monetary">💵 Monetary</span>
                                    @elseif($donation->donation_type === 'food')
                                        <span class="donation-badge food">🍚 Food</span>
                                    @elseif($donation->donation_type === 'materials')
                                        <span class="donation-badge materials">👕 Materials</span>
                                    @elseif($donation->donation_type === 'medical')
                                        <span class="donation-badge medical">💊 Medical</span>
                                    @else
                                        <span class="donation-badge other">📦 Other</span>
                                    @endif
                                </td>

                                <td>
                                    <div class="donation-details">
                                        @if($donation->donation_type === 'monetary')
                                            <strong class="amount">₱{{ number_format($donation->amount, 2) }}</strong>
                                            <small class="payment-method">{{ $donation->payment_method }}</small>
                                        @else
                                            <strong class="item-name">{{ $donation->item_name }}</strong>
                                            <small class="quantity">{{ $donation->quantity }} {{ $donation->unit }}</small>
                                            @if($donation->condition)
                                                <span class="condition-badge">{{ ucfirst($donation->condition) }}</span>
                                            @endif
                                            @if($donation->expiry_date)
                                                <small class="expiry-date">
                                                    Expires: {{ \Carbon\Carbon::parse($donation->expiry_date)->format('M d, Y') }}
                                                </small>
                                            @endif
                                        @endif
                                    </div>
                                </td>

                                <td>
                                    <span class="donation-date">{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</span>
                                </td>

                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.donations.show', $donation->id) }}" class="action-btn view-btn" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.donations.edit', $donation->id) }}" class="action-btn edit-btn" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.donations.destroy', $donation->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete-btn" title="Delete"
                                                onclick="return confirm('Are you sure you want to delete this donation record?')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-4">
                    {{ $donations->links() }}
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                    <h5>No donations yet</h5>
                    <p class="text-muted">Start tracking donations by adding the first one!</p>
                    <a href="{{ route('admin.donations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Add First Donation
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.donations-hero {
    background: #f8f9fa;
    min-height: 100vh;
    padding-bottom: 2rem;
}

.donations-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    overflow: hidden;
}

.donation-alert {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 25px rgba(40, 167, 69, 0.2);
    border-left: 5px solid #28a745;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
}

.donations-table {
    border: none;
}

.donations-table thead th {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.donation-row {
    transition: all 0.3s ease;
    border: none;
}

.donation-row:hover {
    background: rgba(102, 126, 234, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.donation-row td {
    padding: 1rem;
    border: none;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    vertical-align: middle;
}

.donation-id {
    font-weight: 600;
    color: #667eea;
    font-size: 0.9rem;
}

.donor-info strong {
    color: #333;
    font-size: 1rem;
}

.donor-info small {
    color: #6c757d;
    font-size: 0.8rem;
}

.donation-badge {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.donation-badge.monetary {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.donation-badge.food {
    background: linear-gradient(135deg, #fff3cd, #ffeaa7);
    color: #856404;
}

.donation-badge.materials {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    color: #0c5460;
}

.donation-badge.medical {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

.donation-badge.other {
    background: linear-gradient(135deg, #e2e3e5, #d6d8db);
    color: #383d41;
}

.donation-details {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.donation-details .amount {
    color: #28a745;
    font-size: 1.1rem;
    font-weight: 700;
}

.donation-details .item-name {
    color: #17a2b8;
    font-size: 1rem;
    font-weight: 600;
}

.donation-details .payment-method,
.donation-details .quantity {
    color: #6c757d;
    font-size: 0.85rem;
}

.condition-badge {
    background: #17a2b8;
    color: white;
    padding: 0.2rem 0.5rem;
    border-radius: 10px;
    font-size: 0.7rem;
    margin-left: 0.5rem;
}

.expiry-date {
    color: #fd7e14 !important;
    font-weight: 500;
}

.donation-date {
    color: #495057;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.action-btn {
    width: 35px;
    height: 35px;
    border-radius: 8px;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    text-decoration: none;
    font-size: 0.9rem;
}

.view-btn {
    background: linear-gradient(135deg, #17a2b8, #138496);
    color: white;
}

.view-btn:hover {
    background: linear-gradient(135deg, #138496, #117a8b);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

.edit-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
}

.edit-btn:hover {
    background: linear-gradient(135deg, #5a6fd8, #6a4190);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.delete-btn {
    background: linear-gradient(135deg, #dc3545, #c82333);
    color: white;
}

.delete-btn:hover {
    background: linear-gradient(135deg, #c82333, #bd2130);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.3);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6c757d;
}

.empty-state i {
    opacity: 0.5;
}

.empty-state h5 {
    margin-top: 1rem;
    color: #495057;
}

.empty-state .btn {
    margin-top: 1rem;
    background: linear-gradient(135deg, #667eea, #764ba2);
    border: none;
    border-radius: 25px;
    padding: 0.75rem 2rem;
}

@media (max-width: 768px) {
    .donations-card {
        margin: 0 1rem;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .action-btn {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
}
</style>

@endsection