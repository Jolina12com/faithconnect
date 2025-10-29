// Add these missing functions to your main_dashboard.blade.php

// 1. Add missing chatbot analytics function
async function fetchChatbotAnalytics() {
    try {
        const response = await fetch('/chatbot-analytics', {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            updateChatbotAnalytics(data);
        }
    } catch (error) {
        console.error('Error fetching chatbot analytics:', error);
        // Show fallback data
        updateChatbotAnalytics({
            total_emotions: 0,
            active_users: 0,
            top_emotions: [],
            recent_emotions: []
        });
    }
}

function updateChatbotAnalytics(data) {
    document.getElementById('totalConversations').textContent = data.total_emotions || 0;
    document.getElementById('activeUsers').textContent = data.active_users || 0;
    
    // Update emotion chart
    const emotionChart = document.getElementById('emotionChart');
    if (emotionChart && data.top_emotions) {
        emotionChart.innerHTML = '';
        data.top_emotions.slice(0, 5).forEach(emotion => {
            const bar = document.createElement('div');
            bar.className = 'emotion-bar';
            bar.style.height = `${emotion.percentage}%`;
            bar.innerHTML = `<div class="label">${emotion.emotion}</div>`;
            emotionChart.appendChild(bar);
        });
    }
    
    // Update top emotions
    const topEmotions = document.getElementById('topEmotions');
    if (topEmotions && data.top_emotions) {
        topEmotions.innerHTML = '';
        data.top_emotions.slice(0, 3).forEach(emotion => {
            const tag = document.createElement('div');
            tag.className = 'emotion-tag';
            tag.innerHTML = `
                ${emotion.emotion}
                <span class="count">${emotion.count}</span>
            `;
            topEmotions.appendChild(tag);
        });
    }
}

// 2. Add missing modal functions
function showEventModal(date, day, events) {
    const modal = document.getElementById('event-modal');
    const dateEl = document.getElementById('event-modal-date');
    const listEl = document.getElementById('event-modal-list');
    
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
    document.getElementById('event-modal').style.display = 'none';
}

// 3. Add error handling for missing elements
function safeUpdateElement(id, value) {
    const element = document.getElementById(id);
    if (element) {
        element.textContent = value;
    }
}