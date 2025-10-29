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

    /* General Layout */
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
        animation: fadeInUp 1s ease-in-out;
    }

    /* Avatar circle for member listing */
    .avatar-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 1.2rem;
    }

    .recent-member-item {
        transition: all 0.2s;
        border-radius: 8px;
    }

    .recent-member-item:hover {
        background-color: #f8f9fa;
    }

    /* Headings */
    h1, h2, h3 {
        animation: fadeInUp 1s ease;
    }

    /* Stat Cards */
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

    /* Chart Cards */
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
        text-align: center;
        transition: transform 0.3s;
    }

    .chart-card:hover {
        transform: scale(1.02);
    }

    /* Sermons */
    .sermons {
        background: #fff;
        padding: 1.5rem;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        margin-bottom: 2rem;
        transition: transform 0.3s;
    }

    .sermons:hover {
        transform: scale(1.01);
    }

    .sermon-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px dashed #ddd;
    }

    .sermon-action button {
        background: linear-gradient(to right, #2196f3, #1e88e5);
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .sermon-action button:hover {
        background: #1565c0;
    }

    /* Logs */
    .log-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .log-list li {
        padding: 0.75rem 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px dashed #ddd;
        font-size: 0.95rem;
        color: #444;
    }

    .log-list li::before {
        content: '🕓';
        font-size: 1rem;
        color: #42a5f5;
    }

    /* Chat */
    .chat-box {
        max-width: 100%;
        border: 1px solid #ddd;
        border-radius: 1rem;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        height: 500px;
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        transition: box-shadow 0.3s;
    }

    .chat-window {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
    }

    .message {
        margin-bottom: 1rem;
        max-width: 75%;
        padding: 0.75rem 1rem;
        border-radius: 1rem;
        font-size: 0.9rem;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        transition: background 0.3s;
    }

    .message.user {
        background-color: #e3f2fd;
        align-self: flex-start;
    }

    .message.admin {
        background-color: #bbdefb;
        align-self: flex-end;
    }

    .message span {
        display: block;
        font-size: 0.75rem;
        margin-top: 0.25rem;
        color: #777;
    }

    .chat-input {
        display: flex;
        border-top: 1px solid #ccc;
        padding: 0.5rem;
        background: #fff;
    }

    .chat-input input {
        flex: 1;
        padding: 0.75rem;
        border: none;
        outline: none;
        font-size: 1rem;
    }

    .chat-input button {
        background: #2196f3;
        color: white;
        padding: 0.75rem 1rem;
        border: none;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: background 0.3s;
    }

    .chat-input button:hover {
        background: #1976d2;
    }

    /* Calendar */
    .calendar-container {
        max-width: 900px;
        margin: 2rem auto;
        padding: 2rem;
        background: linear-gradient(135deg, #ffffff, #f1f8ff);
        border-radius: 1rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        animation: fadeInUp 1s ease;
    }

    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .calendar-header h2 {
        margin: 0;
        flex-grow: 1;
        text-align: center;
    }

    .calendar-header button {
        background: linear-gradient(to right, #42a5f5, #1e88e5);
        color: white;
        border: none;
        border-radius: 4px;
        padding: 0.5rem 1rem;
        cursor: pointer;
        transition: all 0.3s;
    }

    .calendar-header button:hover {
        background: linear-gradient(to right, #1e88e5, #0d47a1);
        transform: translateY(-2px);
    }

    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 10px;
    }

    .day-name, .calendar-day {
        text-align: center;
        padding: 0.75rem;
    }

    .day-name {
        font-weight: bold;
        background: #f0f0f0;
        border-radius: 0.5rem;
    }

    .calendar-day {
        height: 80px;
        border-radius: 0.5rem;
        position: relative;
        background-color: #fafafa;
        border: 1px solid #eee;
        transition: all 0.3s;
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    .calendar-day.event {
        background-color: #e3f2fd;
        border-color: #2196f3;
        cursor: pointer;
    }

    .calendar-day:hover {
        background: #bbdefb;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .calendar-day .date {
        position: absolute;
        top: 6px;
        left: 8px;
        font-size: 0.9rem;
        color: #444;
    }

    .event-name {
        font-size: 0.75rem;
        margin-top: 25px;
        color: #2196f3;
        font-weight: 600;
        padding: 3px 5px;
        border-radius: 3px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        text-align: left;
        margin-bottom: 2px;
        background-color: rgba(255, 255, 255, 0.7);
    }

    .event-more {
        font-size: 0.7rem;
        color: #1976d2;
        background-color: rgba(187, 222, 251, 0.7);
        padding: 2px 5px;
        border-radius: 3px;
        margin-top: 2px;
        text-align: center;
        font-weight: 500;
    }

    /* Analytics section */
    .analytics-content {
        padding: 15px;
    }

    .analytics-section {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        gap: 5px;
        background: #f8f9fa;
        padding: 10px;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .stat-item:hover {
        background: #e9ecef;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #666;
        font-weight: 500;
    }

    .stat-value {
        font-size: 1.2rem;
        color: #42a5f5;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 2rem;
        color: #64b5f6;
        background-color: #f5faff;
        border-radius: 8px;
    }

    /* Enhanced Chatbot Analytics Styles */
    .gauge-container {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        position: relative;
    }

    .top-emotions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 12px;
        padding: 15px 0;
    }

    .emotion-card {
        background: linear-gradient(135deg, #f8f9ff, #e8f2ff);
        border-radius: 12px;
        padding: 15px;
        text-align: center;
        border-left: 4px solid #2196f3;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .emotion-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(33, 150, 243, 0.2);
    }

    .emotion-icon {
        font-size: 2rem;
        margin-bottom: 8px;
        display: block;
    }

    .emotion-name {
        font-weight: 600;
        color: #2196f3;
        font-size: 0.9rem;
        margin-bottom: 4px;
        text-transform: capitalize;
    }

    .emotion-count {
        font-size: 1.2rem;
        font-weight: 700;
        color: #1976d2;
    }

    .emotion-percentage {
        font-size: 0.8rem;
        color: #666;
        margin-top: 2px;
    }

    /* Emotion color mapping */
    .emotion-card[data-emotion="happy"] { border-left-color: #4caf50; }
    .emotion-card[data-emotion="sad"] { border-left-color: #2196f3; }
    .emotion-card[data-emotion="angry"] { border-left-color: #f44336; }
    .emotion-card[data-emotion="excited"] { border-left-color: #ff9800; }
    .emotion-card[data-emotion="peaceful"] { border-left-color: #9c27b0; }
    .emotion-card[data-emotion="worried"] { border-left-color: #ff5722; }
    .emotion-card[data-emotion="grateful"] { border-left-color: #8bc34a; }
    .emotion-card[data-emotion="confused"] { border-left-color: #607d8b; }
    .emotion-card[data-emotion="neutral"] { border-left-color: #9e9e9e; }

    /* Chart loading states */
    .chart-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        color: #64b5f6;
        font-style: italic;
    }

    .chart-error {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
        color: #f44336;
        font-style: italic;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats, .charts {
            flex-direction: column;
        }

        .calendar-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .chart-card,
    .card,
    .sermons {
        flex: 1 1 400px;
        max-width: 100%;
    }

    /* Event modal */
    .event-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 1000;
        justify-content: center;
        align-items: center;
    }
    
    .event-modal-content {
        background: white;
        padding: 25px;
        border-radius: 10px;
        max-width: 500px;
        width: 90%;
        max-height: 80vh;
        overflow-y: auto;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        animation: fadeInUp 0.3s;
    }
    
    .event-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 1px solid #eee;
    }
    
    .event-modal-close {
        font-size: 1.5rem;
        cursor: pointer;
        color: #666;
        transition: color 0.2s;
    }
    
    .event-modal-close:hover {
        color: #f44336;
    }
    
    .event-modal-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    
    .event-modal-item {
        padding: 12px;
        border-radius: 8px;
        background-color: #f8f9fa;
        border-left: 4px solid #2196f3;
        transition: all 0.2s;
        cursor: pointer;
    }
    
    .event-modal-item:hover {
        background-color: #e3f2fd;
        transform: translateY(-2px);
    }
    
    .event-modal-title {
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }
    
    .event-modal-info {
        display: flex;
        font-size: 0.8rem;
        color: #666;
        gap: 15px;
    }
</style>

<div class="dashboard">


    <!-- Member Analytics -->
    <div class="stats">
        <div class="stat">
            <h2>Total Members</h2>
            <span class="badge-info" id="total-members">0</span>
        </div>
        <div class="stat">
            <h2>New Members</h2>
            <span class="badge-info" id="new-members">0</span>
        </div>
    </div>

    <div class="charts">
        <div class="chart-card">
            <h3>Member Growth Trend</h3>
            <canvas id="memberGrowthChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>Membership Status</h3>
            <canvas id="membersChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>Recent Members</h3>
            <div class="analytics-content">
                <div class="analytics-section" id="recent-members-list">
                    <div class="empty-state">
                        <p>Loading recent members...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Analytics -->
    <div class="stats">
        <div class="stat">
            <h2>Total Donations</h2>
            <span class="badge-info" id="total-donations">0</span>
        </div>
        <div class="stat">
            <h2>Total Amount</h2>
            <span class="badge-info" id="total-amount">₱0.00</span>
        </div>
    </div>

    <div class="charts">
        <div class="chart-card">
            <h3>Donation Distribution</h3>
            <canvas id="donationTypesChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>Monthly Donations</h3>
            <canvas id="monthlyDonationsChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>Recent Donations</h3>
            <div class="analytics-content">
                <div class="analytics-section" id="recent-donations-list">
                    <div class="empty-state">
                        <p>Loading recent donations...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Events analytics section -->
    <div class="stats">
        <div class="stat">
            <h2>Total Events</h2>
            <span class="badge-info" id="total-events">0</span>
        </div>
        <div class="stat">
            <h2>Upcoming Events</h2>
            <span class="badge-info" id="upcoming-events">0</span>
        </div>
        <div class="stat">
            <h2>Events with Polls</h2>
            <span class="badge-info" id="poll-events">0</span>
        </div>
    </div>

    <div class="charts">
        <div class="chart-card">
            <h3>Monthly Events</h3>
            <canvas id="eventsChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>Upcoming Events</h3>
            <div class="analytics-content">
                <div class="analytics-section" id="upcoming-events-list">
                    <!-- Upcoming events will be populated here -->
                    <div class="empty-state">
                        <p>Loading upcoming events...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chatbot Analytics Section -->
    <div class="stats">
        <div class="stat">
            <h2>Total Emotions</h2>
            <span class="badge-info" id="totalConversations">0</span>
        </div>
        <div class="stat">
            <h2>Active Users (24h)</h2>
            <span class="badge-info" id="activeUsers">0</span>
        </div>
    </div>

    <div class="charts">
        <div class="chart-card">
            <h3>💭 Emotion Distribution</h3>
            <canvas id="emotionDoughnutChart" width="250" height="250"></canvas>
        </div>
        <div class="chart-card">
            <h3>📊 User Engagement</h3>
            <div class="gauge-container">
                <canvas id="engagementGauge" width="250" height="200"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>🔥 Top Emotions</h3>
            <div class="analytics-content">
                <div class="top-emotions-grid" id="topEmotionsGrid">
                    <div class="empty-state">
                        <p>Loading emotion data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Calendar -->
    <div class="calendar-container card">
        <div class="calendar-header">
            <button id="prev-month" class="btn btn-sm btn-outline-primary">&lt; Prev</button>
            <h2 id="calendar-title">April 2025</h2>
            <button id="next-month" class="btn btn-sm btn-outline-primary">Next &gt;</button>
        </div>
        <div class="calendar-grid" id="calendar"></div>
    </div>
</div>

<!-- Add modal for multiple events -->
<div class="event-modal" id="event-modal">
    <div class="event-modal-content">
        <div class="event-modal-header">
            <h4 id="event-modal-date">Events on Date</h4>
            <span class="event-modal-close" onclick="closeEventModal()">&times;</span>
        </div>
        <div class="event-modal-list" id="event-modal-list">
            <!-- Events will be added here dynamically -->
        </div>
    </div>
</div>




<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Chart objects
let membersChart = null;
let memberGrowthChart = null;

// Fetch member analytics data
async function fetchMemberAnalytics() {
    try {
        const response = await fetch('{{ route("admin.members.analytics") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch member analytics data');
        }
        
        const data = await response.json();
        updateDashboard(data);
    } catch (error) {
        console.error('Error fetching member analytics:', error);
    }
}

// Update dashboard with the fetched data
function updateDashboard(data) {
    // Update stats with null checks
    const totalMembersEl = document.getElementById('total-members');
    if (totalMembersEl) totalMembersEl.textContent = data.totalMembers;
    
    const newMembersEl = document.getElementById('new-members');
    if (newMembersEl) newMembersEl.textContent = data.newMembers || 0;
    
    // Update membership status chart
    updateMembersChart(data.membershipStatus);
    
    // Update member growth chart
    updateMemberGrowthChart(data.monthlyNewMembers);
    
    // Update recent members list
    updateRecentMembersList(data.recentMembers);
}

// Update members breakdown chart
function updateMembersChart(statusData) {
    // Format labels to be more readable
    const formattedLabels = Object.keys(statusData).map(status => {
        return status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
    });
    
    const ctx = document.getElementById('membersChart').getContext('2d');
    
    // Destroy previous chart instance if exists
    if (membersChart) {
        membersChart.destroy();
    }
    
    // Create new chart
    membersChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: formattedLabels,
            datasets: [{
                label: 'Members',
                data: Object.values(statusData),
                backgroundColor: [
                    '#2196f3', '#64b5f6', '#90caf9', '#bbdefb', 
                    '#42a5f5', '#1e88e5', '#1976d2', '#0d47a1'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
}

// Update member growth trend chart
function updateMemberGrowthChart(monthlyData) {
    const canvas = document.getElementById('memberGrowthChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Destroy previous chart instance if exists
    if (memberGrowthChart) {
        memberGrowthChart.destroy();
    }
    
    // Create new chart
    memberGrowthChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(monthlyData),
            datasets: [{
                label: 'New Members',
                data: Object.values(monthlyData),
                borderColor: '#2196f3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Update recent members list
function updateRecentMembersList(recentMembers) {
    const container = document.getElementById('recent-members-list');
    if (!container) return;
    
    if (!recentMembers || recentMembers.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <p>No recent members found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    recentMembers.forEach(member => {
        const date = new Date(member.created_at);
        const formattedDate = date.toLocaleDateString();
        
        // Safe name extraction
        let memberName = 'Unknown';
        let initial = '?';
        
        if (member.user) {
            if (member.user.name) {
                memberName = member.user.name;
                initial = memberName.charAt(0).toUpperCase();
            } else if (member.user.first_name) {
                memberName = `${member.user.first_name} ${member.user.last_name || ''}`;
                initial = member.user.first_name.charAt(0).toUpperCase();
            }
        }
        
        html += `
            <div class="recent-member-item">
                <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                    <div class="avatar-circle bg-primary text-white me-3">
                        ${initial}
                    </div>
                    <div>
                        <div class="fw-bold">${memberName}</div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i> ${formattedDate}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <a href="/admin/members/${member.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Calendar script (from existing code)
const calendar = document.getElementById('calendar');
const title = document.getElementById('calendar-title');

// Initialize with empty events array, we'll populate it from the API
let events = [];

// Current month and year (these are now variables that can change)
let currentMonth = new Date().getMonth();
let currentYear = new Date().getFullYear();

// Function to render calendar
function renderCalendar(year, month) {
    if (!calendar) return;
    
    // Clear previous calendar
    calendar.innerHTML = '';
    
    // Update title
    if (title) {
        const monthName = new Date(year, month).toLocaleString('default', { month: 'long' });
        title.innerText = `${monthName} ${year}`;
    }
    
    // Debug events list
    console.log(`Rendering calendar for ${year}-${month+1}`);
    console.log('Available events:', events);

    // Add day names
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        const dayEl = document.createElement('div');
        dayEl.className = 'day-name';
        dayEl.innerText = day;
        calendar.appendChild(dayEl);
    });

    // Helper function to standardize date format
    function standardizeDate(dateString) {
        if (!dateString) return '';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return dateString;
            return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
        } catch (e) {
            console.error('Error parsing date:', e);
            return dateString;
        }
    }
    
    // Calculate first day of month and last date
    const firstDay = new Date(year, month, 1).getDay(); // 0 = Sunday
    const lastDate = new Date(year, month + 1, 0).getDate(); // last day of month
    
    // Add blank spaces for days before the 1st
    for (let i = 0; i < firstDay; i++) {
        const blank = document.createElement('div');
        blank.className = 'calendar-day';
        calendar.appendChild(blank);
    }
    
    // Add days with events
    for (let day = 1; day <= lastDate; day++) {
        const fullDate = `${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
        // Find all events for this day instead of just one
        const dayEvents = events.filter(e => {
            // Standardize both dates for comparison
            const eventDate = standardizeDate(e.event_date);
            // Compare standardized dates
            return eventDate === fullDate;
        });
        const hasEvents = dayEvents.length > 0;
        
        if (hasEvents) {
            console.log(`Found ${dayEvents.length} events for ${fullDate}:`, dayEvents);
        }
        
        const dayEl = document.createElement('div');
        dayEl.className = 'calendar-day';
        if (hasEvents) {
            dayEl.classList.add('event');
        }
        
        let eventHTML = '';
        
        // Add the date number
        eventHTML += `<div class="date">${day}</div>`;
        
        // Add event titles (limited to 2 with a count indicator if more)
        if (hasEvents) {
            if (dayEvents.length <= 2) {
                // Show all events (1 or 2)
                dayEvents.forEach(event => {
                    const eventStyle = event.color ? `border-left: 3px solid ${event.color};` : '';
                    eventHTML += `
                        <div class="event-name" title="${event.title}" style="${eventStyle}" 
                             data-event-id="${event.id}">
                            ${event.title}
                        </div>
                    `;
                });
            } else {
                // Show first event and a count of remaining
                const firstEvent = dayEvents[0];
                const eventStyle = firstEvent.color ? `border-left: 3px solid ${firstEvent.color};` : '';
                eventHTML += `
                    <div class="event-name" title="${firstEvent.title}" style="${eventStyle}" 
                         data-event-id="${firstEvent.id}">
                        ${firstEvent.title}
                    </div>
                    <div class="event-more" data-date="${fullDate}" data-day="${day}">
                        +${dayEvents.length - 1} more
                    </div>
                `;
            }
        }
        
        dayEl.innerHTML = eventHTML;
        
        // Add click handlers
        if (hasEvents) {
            // Add event listeners to the child elements
            dayEl.addEventListener('click', (e) => {
                // Check if clicking on the "more" link
                if (e.target.classList.contains('event-more')) {
                    e.stopPropagation();
                    const date = e.target.getAttribute('data-date');
                    const displayDay = e.target.getAttribute('data-day');
                    showEventModal(date, displayDay, dayEvents);
                } 
                // Check if clicking on a specific event
                else if (e.target.classList.contains('event-name')) {
                    e.stopPropagation();
                    const eventId = e.target.getAttribute('data-event-id');
                    window.location.href = `/admin/events/${eventId}`;
                }
                // Otherwise navigate to the first event
                else {
                    window.location.href = `/admin/events/${dayEvents[0].id}`;
                }
            });
        }
        
        calendar.appendChild(dayEl);
    }
}

// Navigate to previous month
function prevMonth() {
    currentMonth--;
    if (currentMonth < 0) {
        currentMonth = 11;
        currentYear--;
    }
    renderCalendar(currentYear, currentMonth);
    
    // If we're looking at a month different from the current one,
    // we might need to fetch additional events
    if (currentMonth !== new Date().getMonth() || currentYear !== new Date().getFullYear()) {
        fetchEventsForMonth(currentYear, currentMonth);
    }
}

// Navigate to next month
function nextMonth() {
    currentMonth++;
    if (currentMonth > 11) {
        currentMonth = 0;
        currentYear++;
    }
    renderCalendar(currentYear, currentMonth);
    
    // If we're looking at a month different from the current one,
    // we might need to fetch additional events
    if (currentMonth !== new Date().getMonth() || currentYear !== new Date().getFullYear()) {
        fetchEventsForMonth(currentYear, currentMonth);
    }
}

// Fetch events specifically for a given month
async function fetchEventsForMonth(year, month) {
    try {
        const startOfMonth = `${year}-${String(month + 1).padStart(2, '0')}-01`;
        const lastDay = new Date(year, month + 1, 0).getDate();
        const endOfMonth = `${year}-${String(month + 1).padStart(2, '0')}-${String(lastDay).padStart(2, '0')}`;
        
        const response = await fetch(`{{ route('admin.events.range') }}?start=${startOfMonth}&end=${endOfMonth}`, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const monthEvents = await response.json();
            // Merge with existing events, removing duplicates
            const eventIds = events.map(e => e.id);
            const newEvents = monthEvents.filter(e => !eventIds.includes(e.id));
            events = [...events, ...newEvents];
            
            // Re-render the calendar with the updated events
            renderCalendar(currentYear, currentMonth);
        }
    } catch (error) {
        console.error('Error fetching events for month:', error);
    }
}

// Fetch events and analytics data
async function fetchEventAnalytics() {
    try {
        const response = await fetch('{{ route("admin.events.analytics") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch event analytics data');
        }
        
        const data = await response.json();
        updateEventStats(data);
        
        // Update events array and re-render calendar
        if (data.calendarEvents) {
            // Format the event_date to ensure proper comparison in the calendar
            events = data.calendarEvents.map(event => {
                // Make sure event_date is in proper format for string comparison
                if (event.event_date) {
                    // Parse the date to ensure proper formatting
                    const date = new Date(event.event_date);
                    if (!isNaN(date.getTime())) {
                        const year = date.getFullYear();
                        const month = String(date.getMonth() + 1).padStart(2, '0');
                        const day = String(date.getDate()).padStart(2, '0');
                        event.event_date = `${year}-${month}-${day}`;
                    }
                }
                return event;
            });
            
            console.log('Calendar events:', events);
            renderCalendar(currentYear, currentMonth);
        }
    } catch (error) {
        console.error('Error fetching event analytics:', error);
    }
}

// Update dashboard with event stats
function updateEventStats(data) {
    // Update stats
    document.getElementById('total-events').textContent = data.totalEvents || 0;
    document.getElementById('upcoming-events').textContent = data.upcomingEvents || 0;
    document.getElementById('poll-events').textContent = data.eventsWithPolls || 0;
    
    // Update monthly events chart
    updateEventsChart(data.monthlyEvents || {});
    
    // Update upcoming events list
    updateUpcomingEventsList(data.nextEvents || []);
}

// Update monthly events chart
function updateEventsChart(monthlyData) {
    const ctx = document.getElementById('eventsChart').getContext('2d');
    
    // Check if chart instance exists and destroy it
    if (window.eventsChart && typeof window.eventsChart.destroy === 'function') {
        window.eventsChart.destroy();
    }
    
    // Create new chart
    window.eventsChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: Object.keys(monthlyData),
            datasets: [{
                label: 'Events per Month',
                data: Object.values(monthlyData),
                backgroundColor: '#64b5f6',
                borderColor: '#2196f3',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
}

// Update upcoming events list
function updateUpcomingEventsList(upcomingEvents) {
    const container = document.getElementById('upcoming-events-list');
    
    if (!upcomingEvents || upcomingEvents.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <p>No upcoming events found</p>
            </div>
        `;
        return;
    }
    
    let html = '';
    
    upcomingEvents.forEach(event => {
        const date = new Date(event.event_date);
        const formattedDate = date.toLocaleDateString();
        
        html += `
            <div class="recent-member-item">
                <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                    <div class="avatar-circle" style="background-color: ${event.color || '#3788d8'}; color: white;">
                        ${event.title.charAt(0).toUpperCase()}
                    </div>
                    <div class="ms-3">
                        <div class="fw-bold">${event.title}</div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i> ${formattedDate}
                            ${event.event_time ? `at ${event.event_time}` : ''}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-geo-alt me-1"></i> ${event.location}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <a href="/admin/events/${event.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Fetch donation analytics data
async function fetchDonationAnalytics() {
    try {
        const response = await fetch('{{ route("admin.donations.analytics") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error('Failed to fetch donation analytics data');
        }
        
        const data = await response.json();
        updateDonationDashboard(data);
    } catch (error) {
        console.error('Error fetching donation analytics:', error);
    }
}

// Update dashboard with donation data
function updateDonationDashboard(data) {
    const totalDonationsEl = document.getElementById('total-donations');
    if (totalDonationsEl) totalDonationsEl.textContent = data.totalDonations;
    
    const totalAmountEl = document.getElementById('total-amount');
    if (totalAmountEl) totalAmountEl.textContent = '₱' + data.totalAmount;
    
    updateDonationTypesChart(data.donationTypes);
    updateMonthlyDonationsChart(data.monthlyAmounts);
    updateRecentDonationsList(data.recentDonations);
}

// Update donation types chart
function updateDonationTypesChart(typesData) {
    const canvas = document.getElementById('donationTypesChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    if (window.donationTypesChart && typeof window.donationTypesChart.destroy === 'function') {
        window.donationTypesChart.destroy();
    }
    
    const labels = Object.keys(typesData).map(type => {
        const formatted = type.charAt(0).toUpperCase() + type.slice(1);
        return formatted === 'Monetary' ? 'Cash' : formatted;
    });
    
    const values = Object.values(typesData);
    const total = values.reduce((sum, val) => sum + val, 0);
    
    window.donationTypesChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Donations',
                data: values,
                backgroundColor: [
                    '#4CAF50', // Green for cash
                    '#FF9800', // Orange for food
                    '#2196F3', // Blue for materials
                    '#9C27B0', // Purple for medical
                    '#607D8B'  // Blue grey for other
                ],
                borderWidth: 1,
                borderRadius: 4
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `${context.label}: ${context.raw} (${percentage}%)`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    precision: 0
                }
            }
        }
    });
}

// Update monthly donations chart
function updateMonthlyDonationsChart(monthlyData) {
    const canvas = document.getElementById('monthlyDonationsChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    if (window.monthlyDonationsChart && typeof window.monthlyDonationsChart.destroy === 'function') {
        window.monthlyDonationsChart.destroy();
    }
    
    window.monthlyDonationsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: Object.keys(monthlyData),
            datasets: [{
                label: 'Monthly Donations (₱)',
                data: Object.values(monthlyData),
                borderColor: '#2196f3',
                backgroundColor: 'rgba(33, 150, 243, 0.1)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Update recent donations list
function updateRecentDonationsList(recentDonations) {
    const container = document.getElementById('recent-donations-list');
    if (!container) return;
    
    if (!recentDonations || recentDonations.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No recent donations found</p></div>';
        return;
    }
    
    let html = '';
    recentDonations.forEach(donation => {
        const date = new Date(donation.created_at).toLocaleDateString();
        const donorName = donation.first_name ? `${donation.first_name} ${donation.last_name}` : donation.donor_name;
        const amount = donation.amount ? `₱${parseFloat(donation.amount).toFixed(2)}` : donation.item_name;
        
        html += `
            <div class="recent-member-item">
                <div class="d-flex align-items-center mb-2 p-2 border-bottom">
                    <div class="avatar-circle bg-success text-white me-3">
                        ${donorName.charAt(0).toUpperCase()}
                    </div>
                    <div>
                        <div class="fw-bold">${donorName}</div>
                        <div class="text-muted small">
                            <i class="bi bi-calendar me-1"></i> ${date}
                        </div>
                        <div class="text-muted small">
                            ${donation.donation_type}: ${amount}
                        </div>
                    </div>
                    <div class="ms-auto">
                        <a href="/admin/donations/${donation.id}" class="btn btn-sm btn-outline-success">
                            <i class="bi bi-eye"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Fetch chatbot analytics data
async function fetchChatbotAnalytics() {
    try {
        const response = await fetch('{{ route("admin.chatbot.analytics") }}', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        console.log('Chatbot analytics data:', data); // Debug log
        updateChatbotAnalytics(data);
    } catch (error) {
        console.error('Error fetching chatbot analytics:', error);
        // Show fallback data with some indication of error
        updateChatbotAnalytics({
            total_emotions: 0,
            active_users: 0,
            top_emotions: [],
            recent_emotions: [],
            error: true
        });
    }
}

// Chart instances
let emotionDoughnutChart = null;
let engagementGauge = null;

// Emotion icons mapping
const emotionIcons = {
    happy: '😊', sad: '😢', angry: '😠', excited: '🤩', 
    peaceful: '😌', worried: '😟', grateful: '🙏', confused: '😕',
    neutral: '😐', tired: '😴', anxious: '😰', depression: '😞',
    churchInfo: '⛪', prayerRequest: '🙏', unspecified: '❓'
};

// Update chatbot analytics display
function updateChatbotAnalytics(data) {
    console.log('Updating chatbot analytics with data:', data);
    
    // Update total conversations/emotions
    const totalEl = document.getElementById('totalConversations');
    if (totalEl) totalEl.textContent = data.total_emotions || 0;
    
    // Update active users
    const activeEl = document.getElementById('activeUsers');
    if (activeEl) activeEl.textContent = data.active_users || 0;
    
    // Update doughnut chart
    updateEmotionDoughnutChart(data.top_emotions || []);
    
    // Update engagement gauge
    updateEngagementGauge(data.active_users || 0, data.total_emotions || 0);
    
    // Update top emotions grid
    updateTopEmotionsGrid(data.top_emotions || []);
}

// Update emotion doughnut chart
function updateEmotionDoughnutChart(emotions) {
    const canvas = document.getElementById('emotionDoughnutChart');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart
    if (emotionDoughnutChart) {
        emotionDoughnutChart.destroy();
    }
    
    if (emotions.length === 0) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        ctx.fillStyle = '#666';
        ctx.font = '14px Inter';
        ctx.textAlign = 'center';
        ctx.fillText('No emotion data', canvas.width/2, canvas.height/2);
        return;
    }
    
    const colors = [
        '#4CAF50', '#2196F3', '#F44336', '#FF9800', '#9C27B0',
        '#FF5722', '#8BC34A', '#607D8B', '#9E9E9E', '#795548'
    ];
    
    emotionDoughnutChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: emotions.map(e => e.emotion),
            datasets: [{
                data: emotions.map(e => e.count),
                backgroundColor: colors.slice(0, emotions.length),
                borderWidth: 2,
                borderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const emotion = emotions[context.dataIndex];
                            return `${emotion.emotion}: ${emotion.count} (${emotion.percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%'
        }
    });
}

// Update engagement gauge
function updateEngagementGauge(activeUsers, totalEmotions) {
    const canvas = document.getElementById('engagementGauge');
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    
    // Destroy existing chart
    if (engagementGauge) {
        engagementGauge.destroy();
    }
    
    // Calculate engagement score (0-100)
    const maxExpected = 50; // Expected max active users
    const engagementScore = Math.min((activeUsers / maxExpected) * 100, 100);
    
    engagementGauge = new Chart(ctx, {
        type: 'doughnut',
        data: {
            datasets: [{
                data: [engagementScore, 100 - engagementScore],
                backgroundColor: [
                    engagementScore > 70 ? '#4CAF50' : 
                    engagementScore > 40 ? '#FF9800' : '#F44336',
                    '#E0E0E0'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            cutout: '75%',
            plugins: {
                legend: { display: false },
                tooltip: { enabled: false }
            }
        },
        plugins: [{
            afterDraw: function(chart) {
                const ctx = chart.ctx;
                const centerX = chart.width / 2;
                const centerY = chart.height / 2;
                
                ctx.fillStyle = '#333';
                ctx.font = 'bold 24px Inter';
                ctx.textAlign = 'center';
                ctx.fillText(Math.round(engagementScore) + '%', centerX, centerY - 5);
                
                ctx.fillStyle = '#666';
                ctx.font = '12px Inter';
                ctx.fillText('Engagement', centerX, centerY + 15);
            }
        }]
    });
}

// Update top emotions grid
function updateTopEmotionsGrid(emotions) {
    const container = document.getElementById('topEmotionsGrid');
    if (!container) return;
    
    if (emotions.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No emotions recorded yet</p></div>';
        return;
    }
    
    container.innerHTML = '';
    
    emotions.slice(0, 6).forEach(emotion => {
        const card = document.createElement('div');
        card.className = 'emotion-card';
        card.setAttribute('data-emotion', emotion.emotion);
        
        const icon = emotionIcons[emotion.emotion] || '❓';
        
        card.innerHTML = `
            <span class="emotion-icon">${icon}</span>
            <div class="emotion-name">${emotion.emotion}</div>
            <div class="emotion-count">${emotion.count}</div>
            <div class="emotion-percentage">${emotion.percentage}%</div>
        `;
        
        container.appendChild(card);
    });
}

// Add missing modal functions
function showEventModal(date, day, events) {
    const modal = document.getElementById('event-modal');
    const dateEl = document.getElementById('event-modal-date');
    const listEl = document.getElementById('event-modal-list');
    
    if (!modal || !dateEl || !listEl) return;
    
    dateEl.textContent = `Events on ${new Date(date).toLocaleDateString()}`;
    
    let html = '';
    events.forEach(event => {
        html += `
            <div class="event-modal-item" onclick="window.location.href='/admin/events/${event.id}'">
                <div class="event-modal-title">${event.title}</div>
                <div class="event-modal-info">
                    <span>📅 ${event.event_date}</span>
                    ${event.event_time ? `<span>🕐 ${event.event_time}</span>` : ''}
                    <span>📍 ${event.location}</span>
                </div>
            </div>
        `;
    });
    
    listEl.innerHTML = html;
    modal.style.display = 'flex';
}

function closeEventModal() {
    const modal = document.getElementById('event-modal');
    if (modal) modal.style.display = 'none';
}

// Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all analytics
    fetchMemberAnalytics();
    fetchEventAnalytics();
    fetchDonationAnalytics();
    fetchChatbotAnalytics();
    
    // Initialize calendar
    renderCalendar(currentYear, currentMonth);
    
    // Add event listeners for calendar navigation
    const prevBtn = document.getElementById('prev-month');
    const nextBtn = document.getElementById('next-month');
    if (prevBtn) prevBtn.addEventListener('click', prevMonth);
    if (nextBtn) nextBtn.addEventListener('click', nextMonth);
    
    // Update data periodically
    setInterval(fetchMemberAnalytics, 300000);     // Every 5 minutes
    setInterval(fetchEventAnalytics, 300000);      // Every 5 minutes
    setInterval(fetchDonationAnalytics, 300000);   // Every 5 minutes
    setInterval(fetchChatbotAnalytics, 60000);     // Every 1 minute (more frequent for real-time feel)
    
    console.log('Dashboard initialized successfully');
});



// Function to show event modal
function showEventModal(date, day, events) {
    const modal = document.getElementById('event-modal');
    const modalList = document.getElementById('event-modal-list');
    const modalDate = document.getElementById('event-modal-date');
    
    // Format the date for display
    const displayDate = new Date(date).toLocaleDateString('default', { 
        month: 'long', 
        day: 'numeric',
        year: 'numeric'
    });
    
    modalDate.textContent = `Events on ${displayDate}`;
    
    // Clear previous events
    modalList.innerHTML = '';
    
    // Add each event to the modal
    events.forEach(event => {
        const eventEl = document.createElement('div');
        eventEl.className = 'event-modal-item';
        if (event.color) {
            eventEl.style.borderLeftColor = event.color;
        }
        
        const time = event.event_time ? formatTime(event.event_time) : 'All day';
        
        eventEl.innerHTML = `
            <div class="event-modal-title">${event.title}</div>
            <div class="event-modal-info">
                <span><i class="bi bi-clock"></i> ${time}</span>
                <span><i class="bi bi-geo-alt"></i> ${event.location || 'No location'}</span>
            </div>
        `;
        
        // Add click handler to navigate to event details
        eventEl.addEventListener('click', () => {
            window.location.href = `/admin/events/${event.id}`;
        });
        
        modalList.appendChild(eventEl);
    });
    
    // Show the modal
    modal.style.display = 'flex';
}

// Function to close event modal
function closeEventModal() {
    const modal = document.getElementById('event-modal');
    modal.style.display = 'none';
}

// Format time from database format to readable format
function formatTime(timeString) {
    if (!timeString) return 'All day';
    
    try {
        // Parse time string (assuming HH:MM:SS format)
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours, 10);
        
        // Convert to 12-hour format
        const period = hour >= 12 ? 'PM' : 'AM';
        const hour12 = hour % 12 || 12;
        
        return `${hour12}:${minutes} ${period}`;
    } catch (e) {
        return timeString; // Fall back to original if parsing fails
    }
}

// Close modal when clicking outside content
window.addEventListener('click', (e) => {
    const modal = document.getElementById('event-modal');
    if (e.target === modal) {
        closeEventModal();
    }
});
</script>
@endsection