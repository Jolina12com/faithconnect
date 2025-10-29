@extends('admin.dashboard')

@section('title', 'Donation Details')

@section('content')
<div class="row">
    <div class="col-12">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.donations.index') }}">Donations</a></li>
                <li class="breadcrumb-item active" aria-current="page">Donation Details</li>
            </ol>
        </nav>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <h1 class="page-title">Donation Details</h1>
    </div>
    <div class="col-md-6 text-end">
        <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i> Back to Donations
        </a>
        <a href="{{ route('admin.donations.edit', $donation->id) }}" class="btn btn-primary ms-2">
            <i class="fas fa-edit me-2"></i> Edit Donation
        </a>
        <form action="{{ route('admin.donations.destroy', $donation->id) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger ms-2" onclick="return confirm('Are you sure you want to delete this donation?')">
                <i class="fas fa-trash me-2"></i> Delete
            </button>
        </form>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Donation Information</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th style="width: 30%;">Donation ID</th>
                            <td>{{ $donation->id }}</td>
                        </tr>
                        <tr>
                            <th>Member</th>
                            <td>{{ $donation->first_name }} {{ $donation->last_name }} ({{ $donation->email }})</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <td>₱{{ number_format($donation->amount, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Donation Date</th>
                            <td>{{ \Carbon\Carbon::parse($donation->donation_date)->format('F d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Payment Method</th>
                            <td>{{ $donation->payment_method }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <td>{{ $donation->category ?? 'Not specified' }}</td>
                        </tr>
                        <tr>
                            <th>Receipt Number</th>
                            <td>{{ $donation->receipt_number ?? 'Not issued' }}</td>
                        </tr>
                        <tr>
                            <th>Recurring</th>
                            <td>
                                @if($donation->is_recurring)
                                <span class="badge bg-success">Yes</span>
                                @else
                                <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Created</th>
                            <td>{{ \Carbon\Carbon::parse($donation->created_at)->format('F d, Y g:i A') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ \Carbon\Carbon::parse($donation->updated_at)->format('F d, Y g:i A') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Notes</h5>
            </div>
            <div class="card-body">
                @if($donation->notes)
                <p>{{ $donation->notes }}</p>
                @else
                <p class="text-muted">No notes available for this donation.</p>
                @endif
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Member Donation History</h5>
            </div>
            <div class="card-body">
                <p>
                    <strong>Total Donations:</strong>
                    ₱{{ number_format(\DB::table('donations')->where('user_id', $donation->user_id)->sum('amount'), 2) }}
                </p>
                <p>
                    <strong>Number of Donations:</strong>
                    {{ \DB::table('donations')->where('user_id', $donation->user_id)->count() }}
                </p>
                <a href="{{ route('admin.donations.index') }}?user_id={{ $donation->user_id }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-history me-1"></i> View Member's Donation History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection 