@extends('member.dashboard_member')

@section('content')
<div class="donations-hero">
    <div class="container py-4">
        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="page-title text-dark">My Donations</h1>
                <p class="text-muted">View your donation history</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('member.donations.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus-circle me-2"></i> New Donation
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
            <div class="card-body">
                @if($donations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle donations-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Details</th>
                                <th>Amount/Quantity</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $donation)
                            <tr class="donation-row">
                                <td>
                                    <span class="donation-date">{{ \Carbon\Carbon::parse($donation->donation_date)->format('M d, Y') }}</span>
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
                                            <small class="payment-method">{{ $donation->payment_method }}</small>
                                        @else
                                            <strong class="item-name">{{ $donation->item_name }}</strong>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="donation-details">
                                        @if($donation->donation_type === 'monetary')
                                            <strong class="amount">₱{{ number_format($donation->amount, 2) }}</strong>
                                        @else
                                            <small class="quantity">{{ $donation->quantity }} {{ $donation->unit }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $donation->category ?? '-' }}</td>
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
                    <p class="text-muted">Start making a difference today!</p>
                    <a href="{{ route('member.donations.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Make Your First Donation
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

.donation-date {
    color: #495057;
    font-weight: 500;
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
}
</style>

@endsection