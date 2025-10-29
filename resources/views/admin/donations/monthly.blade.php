@extends('admin.dashboard')

@section('title', 'Monthly Donation Reports')

@section('content')
<div class="donations-hero">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.donations.index') }}">Donations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Monthly Reports</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="page-title text-dark">Monthly Donation Reports</h1>
                <p class="text-muted">Track donation trends and monthly totals</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Donations
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="donations-card">
                    <div class="card-header">
                        <h5 class="mb-0">Monthly Donation Trends for {{ $currentYear }}</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="donationChart" height="300"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="donations-card">
                    <div class="card-header">
                        <h5 class="mb-0">Monthly Totals</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle donations-table">
                                <thead>
                                    <tr>
                                        <th>Month</th>
                                        <th>Total</th>
                                        <th>Count</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($months as $index => $month)
                                    <tr class="donation-row">
                                        <td>{{ $month }}</td>
                                        <td><strong class="amount">₱{{ number_format($totals[$index], 2) }}</strong></td>
                                        <td>{{ $counts[$index] }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="text-center">No data available</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-12">
                <div class="donations-card">
                    <div class="card-header">
                        <h5 class="mb-0">Year-to-Year Comparison</h5>
                    </div>
                    <div class="card-body">
                        <div class="empty-state">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5>Year-to-Year Analysis</h5>
                            <p class="text-muted">This section will display year-to-year comparison charts when historical data becomes available.</p>
                        </div>
                    </div>
                </div>
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
    margin-bottom: 2rem;
}

.donations-card .card-header {
    background: linear-gradient(135deg, #f8f9fa, #e9ecef);
    border: none;
    padding: 1.5rem;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
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

.amount {
    color: #28a745;
    font-size: 1.1rem;
    font-weight: 700;
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

@media (max-width: 768px) {
    .donations-card {
        margin: 0 1rem 2rem 1rem;
    }
}
</style>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('donationChart').getContext('2d');

    var months = @json($months);
    var totals = @json($totals);
    var counts = @json($counts);

    var donationChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: months,
            datasets: [{
                label: 'Total Donations (₱)',
                data: totals,
                backgroundColor: 'rgba(102, 126, 234, 0.7)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 1,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '₱' + value.toLocaleString();
                        }
                    }
                }
            },
            plugins: {
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            var value = context.raw;
                            var count = counts[context.dataIndex];
                            
                            return [
                                'Total: ₱' + value.toLocaleString(),
                                'Donations: ' + count
                            ];
                        }
                    }
                }
            }
        }
    });
});
</script>
@endpush 