// Function to add pastor connection button
function addPastorConnectionButton() {
    const chatMessages = document.getElementById('chat-messages');
    const buttonContainer = document.createElement('div');
    buttonContainer.style.cssText = 'margin: 10px 0; text-align: left;';
    
    const connectButton = document.createElement('a');
    connectButton.href = '/chat';
    connectButton.style.cssText = `
        background: linear-gradient(120deg, #28a745 0%, #20c997 100%);
        color: white;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
        transition: all 0.3s ease;
    `;
    connectButton.innerHTML = '<i class="fas fa-user-tie"></i> Connect with Pastor';
    
    connectButton.onmouseover = function() {
        this.style.transform = 'translateY(-2px)';
        this.style.boxShadow = '0 5px 15px rgba(40, 167, 69, 0.4)';
    };
    
    connectButton.onmouseout = function() {
        this.style.transform = 'translateY(0)';
        this.style.boxShadow = '0 3px 10px rgba(40, 167, 69, 0.3)';
    };
    
    buttonContainer.appendChild(connectButton);
    chatMessages.appendChild(buttonContainer);
    chatMessages.scrollTop = chatMessages.scrollHeight;
}

// Profanity filter is now handled in the main chatbot file
