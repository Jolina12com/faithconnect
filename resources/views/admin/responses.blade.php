@extends('admin.dashboard')

@section('content')
<style>
    /* Animations */
    @keyframes fadeInUp {
        0% {
            opacity: 0;
            transform: translateY(20px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }

    body {
        font-family: 'Segoe UI', sans-serif;
        background: linear-gradient(145deg, #f0f4ff, #ffffff);
        color: #333;
        animation: fadeInUp 1s ease;
    }

    .dashboard {
        padding: 2rem;
        max-width: 1200px;
        margin: auto;
    }

    /* Poll Analytics Cards */
    .stats {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat {
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        flex: 1 1 220px;
        padding: 1.2rem;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: transform 0.3s;
    }

    .stat:hover {
        transform: translateY(-5px);
    }

    .stat h2 {
        font-size: 1.3rem;
    }

    .badge-info {
        background: linear-gradient(to right, #42a5f5, #1e88e5);
        color: white;
        padding: 0.35rem 1rem;
        border-radius: 30px;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /* Charts Section */
    .charts {
        display: flex;
        flex-wrap: wrap;
        gap: 2rem;
        margin-bottom: 2rem;
    }

    .chart-card {
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 14px rgba(0,0,0,0.05);
        flex: 1 1 300px;
        transition: transform 0.3s;
    }

    .chart-card:hover {
        transform: scale(1.02);
    }

    /* Detailed Responses */
    .responses-section {
        background: #fff;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        transition: transform 0.3s;
    }

    .responses-section:hover {
        transform: scale(1.01);
    }

    .response-group {
        margin-bottom: 1.5rem;
        border-bottom: 1px dashed #ddd;
        padding-bottom: 1.5rem;
    }

    .response-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .option-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.35rem 1rem;
        border-radius: 30px;
        font-size: 0.9rem;
        font-weight: 600;
    }

    .option-badge.attending {
        background: linear-gradient(to right, #4caf50, #66bb6a);
        color: white;
    }

    .option-badge.maybe {
        background: linear-gradient(to right, #ff9800, #ffa726);
        color: white;
    }

    .option-badge.not-attending {
        background: linear-gradient(to right, #f44336, #ef5350);
        color: white;
    }

    .option-badge:not([class*="attending"]):not([class*="maybe"]) {
        background: linear-gradient(to right, #f44336, #ef5350);
        color: white;
    }

    .response-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .response-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px dashed #eee;
    }

    .response-item:last-child {
        border-bottom: none;
    }

    .response-details {
        flex-grow: 1;
        margin-right: 1rem;
    }

    .response-comment {
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }

    .export-btn {
        background: linear-gradient(to right, #2196f3, #1e88e5);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .export-btn:hover {
        background: #1565c0;
    }

    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .stats, .charts {
            flex-direction: column;
        }

        .response-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
        }
    }
</style>

<div class="dashboard">
    <!-- Poll Analytics Section -->
    <div class="stats">
        <div class="stat">
            <h2>Attending</h2>
            <span class="badge-info">{{ $analytics['attending'] }}</span>
        </div>
        <div class="stat">
            <h2>Maybe</h2>
            <span class="badge-info">{{ $analytics['maybe'] }}</span>
        </div>
        <div class="stat">
            <h2>Not Attending</h2>
            <span class="badge-info">{{ $analytics['not_attending'] }}</span>
        </div>
        <div class="stat">
            <h2>Total Responses</h2>
            <span class="badge-info">{{ $analytics['total_responses'] }}</span>
        </div>
    </div>

    <!-- Response Trends Chart -->
    <div class="charts">
        <div class="chart-card">
            <h3>Response Trends</h3>
            <canvas id="responseTrendsChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Detailed Responses Section -->
    <div class="responses-section">
        <div class="response-header">
            <h2> {{ $event->title }}</h2>
            <a href="{{ route('admin.events.responses.export', $event->id) }}" class="export-btn">
                <i class="fas fa-download"></i> Export
            </a>
        </div>

        @foreach($groupedResponses as $optionId => $group)
            <div class="response-group">
                <div class="response-header">
                    @php
                        $optionClassMap = [
                            'attending' => 'attending',
                            'maybe' => 'maybe',
                            'not attending' => 'not-attending',
                            'not-attending' => 'not-attending',
                            // add more mappings if needed
                        ];
                        $optionClass = $optionClassMap[strtolower($group['option']->option_value)] ?? '';
                    @endphp
                    <span class="option-badge {{ $optionClass }}">
                        {{ $group['option']->option_text }}
                        <span class="badge bg-white text-dark ml-2">{{ $group['responses']->count() }}</span>
                    </span>
                </div>
                
                <ul class="response-list">
                    @foreach($group['responses'] as $response)
                        <li class="response-item">
                            <div class="response-details">
                                <strong>{{ $response->user->name }}</strong>
                                @if($response->comment)
                                    <div class="response-comment">{{ $response->comment }}</div>
                                @endif
                            </div>
                            <small class="text-muted">
                                {{ $response->created_at->format('M d, Y g:i A') }}
                            </small>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Response Trends Chart
    const ctx = document.getElementById('responseTrendsChart').getContext('2d');
    const trends = @json($responseTrends);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: trends.map(t => t.date),
            datasets: [{
                label: 'Responses per Day',
                data: trends.map(t => t.count),
                borderColor: '#2196f3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
});
</script>


@endsection