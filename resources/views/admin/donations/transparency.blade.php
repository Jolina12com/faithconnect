@extends('admin.dashboard')

@section('title', 'Donation Transparency')

@section('content')
<div class="donations-hero">
    <div class="container py-4">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('admin.donations.index') }}">Donations</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Transparency Report</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-8">
                <h1 class="page-title text-dark">Donation Transparency</h1>
                <p class="text-muted">Transparent reporting for congregation members</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.donations.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i> Back to Donations
                </a>
                <button class="btn btn-primary ms-2" onclick="printReport()">
                    <i class="fas fa-print me-2"></i> Print Report
                </button>
            </div>
        </div>

        <div class="donations-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Monthly Donation Totals for Member Transparency</h5>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="publicToggle" checked>
                    <label class="form-check-label" for="publicToggle">Public Display</label>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info donation-alert">
                    <i class="fas fa-info-circle me-2"></i>
                    This report shows monthly donation totals for transparency to the congregation. Individual donor information is kept confidential.
                </div>

                @php
                    $donations = \DB::table('donations')
                        ->selectRaw('DATE_FORMAT(donation_date, "%Y-%m") as month, SUM(amount) as total_amount, COUNT(*) as donation_count')
                        ->where('donation_type', 'monetary')
                        ->whereNotNull('amount')
                        ->groupBy('month')
                        ->orderBy('month', 'desc')
                        ->get();
                @endphp

                @if($donations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle donations-table" id="transparencyTable">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Total Amount</th>
                                <th>Number of Donations</th>
                                <th>Average Donation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($donations as $item)
                            <tr class="donation-row">
                                <td>{{ \Carbon\Carbon::parse($item->month . '-01')->format('F Y') }}</td>
                                <td><strong class="amount">₱{{ number_format($item->total_amount, 2) }}</strong></td>
                                <td>{{ $item->donation_count }}</td>
                                <td><strong class="amount">₱{{ number_format($item->total_amount / $item->donation_count, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-primary">
                                <th>Total</th>
                                <th><strong class="amount">₱{{ number_format($donations->sum('total_amount'), 2) }}</strong></th>
                                <th>{{ $donations->sum('donation_count') }}</th>
                                <th><strong class="amount">₱{{ $donations->sum('donation_count') > 0 ? number_format($donations->sum('total_amount') / $donations->sum('donation_count'), 2) : '0.00' }}</strong></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <div class="empty-state">
                    <i class="fas fa-hand-holding-heart fa-3x text-muted mb-3"></i>
                    <h5>No donation data available</h5>
                    <p class="text-muted">Start tracking donations to see transparency reports!</p>
                </div>
                @endif
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-lg-7">
                <div class="donations-card">
                    <div class="card-header">
                        <h5 class="mb-0">Donation Trend (Last 12 Months)</h5>
                    </div>
                    <div class="card-body">
                        @if($donations->count() > 0)
                        <canvas id="donationTrendChart" height="300"></canvas>
                        @else
                        <div class="empty-state">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <h5>No trend data available</h5>
                            <p class="text-muted">Chart will appear when donation data is available.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="donations-card">
                    <div class="card-header">
                        <h5 class="mb-0">Donation Allocation</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-4">
                            Our church's commitment to transparency includes showing how donations are used to support our various ministries and operations.
                        </p>
                        <div class="allocation-list">
                            <div class="allocation-item">
                                <span class="allocation-label">Ministries and Outreach</span>
                                <span class="allocation-badge">40%</span>
                            </div>
                            <div class="allocation-item">
                                <span class="allocation-label">Building Maintenance</span>
                                <span class="allocation-badge">25%</span>
                            </div>
                            <div class="allocation-item">
                                <span class="allocation-label">Staff and Administration</span>
                                <span class="allocation-badge">20%</span>
                            </div>
                            <div class="allocation-item">
                                <span class="allocation-label">Community Services</span>
                                <span class="allocation-badge">10%</span>
                            </div>
                            <div class="allocation-item">
                                <span class="allocation-label">Future Growth Fund</span>
                                <span class="allocation-badge">5%</span>
                            </div>
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

.donation-alert {
    border-radius: 15px;
    border: none;
    box-shadow: 0 8px 25px rgba(23, 162, 184, 0.2);
    border-left: 5px solid #17a2b8;
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

.amount {
    color: #28a745;
    font-size: 1.1rem;
    font-weight: 700;
}

.allocation-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.allocation-item {
    display: flex;
    justify-content: between;
    align-items: center;
    padding: 1rem;
    background: rgba(248, 249, 250, 0.5);
    border-radius: 12px;
    border: 1px solid rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
}

.allocation-item:hover {
    background: rgba(102, 126, 234, 0.05);
    transform: translateY(-1px);
}

.allocation-label {
    flex: 1;
    font-weight: 500;
    color: #333;
}

.allocation-badge {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-weight: 600;
    font-size: 0.9rem;
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

@media print {
    .navbar, .sidebar, .breadcrumb, .btn, .form-check {
        display: none !important;
    }
    
    .donations-card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    body {
        background-color: white !important;
    }
    
    .page-title {
        text-align: center;
        margin-bottom: 20px !important;
    }
}

@media (max-width: 768px) {
    .donations-card {
        margin: 0 1rem 2rem 1rem;
    }
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    @if($donations->count() > 0)
    const ctx = document.getElementById('donationTrendChart')?.getContext('2d');
    
    if (ctx) {
        const donationData = @json($donations);
        
        // Sort and prepare data
        const sortedData = donationData.sort((a, b) => a.month.localeCompare(b.month));
        const labels = sortedData.map(item => {
            const date = new Date(item.month + '-01');
            return date.toLocaleDateString('en-US', { month: 'short', year: 'numeric' });
        });
        const values = sortedData.map(item => parseFloat(item.total_amount));
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Donations',
                    data: values,
                    fill: true,
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderColor: 'rgba(102, 126, 234, 1)',
                    tension: 0.4,
                    pointBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 6
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
                                return 'Total: ₱' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }
    @endif
    
    // Toggle public display
    document.getElementById('publicToggle')?.addEventListener('change', function() {
        const table = document.getElementById('transparencyTable');
        if (table) {
            table.classList.toggle('public-display', this.checked);
        }
    });
});

function printReport() {
    window.print();
}
</script>


<style>
@media print {
    .navbar, .sidebar, .breadcrumb, .btn, .form-check {
        display: none !important;
    }
    
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    
    .content {
        margin: 0 !important;
        padding: 0 !important;
    }
    
    body {
        background-color: white !important;
    }
    
    .page-title {
        text-align: center;
        margin-bottom: 20px !important;
    }
}
</style> 
@endsection