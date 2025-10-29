// Enhanced Bible API integration
async function fetchBibleVerse(reference) {
    try {
        const encodedRef = reference.replace(/ /g, '+');
        const response = await fetch(`https://bible-api.com/${encodedRef}`);
        if (response.ok) {
            const data = await response.json();
            return {
                verse: data.text.trim(),
                reference: data.reference,
                translation: data.translation_name || 'KJV'
            };
        }
    } catch (error) {
        console.error('Error fetching Bible verse:', error);
    }
    return null;
}

// Function to get more verses on a topic
async function getMoreVerses(topic) {
    try {
        const response = await fetch('/chatbot/more-verses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ topic })
        });
        
        if (response.ok) {
            const data = await response.json();
            return data.verses;
        }
    } catch (error) {
        console.error('Error fetching more verses:', error);
    }
    return [];
}

// Connect to pastor/admin
async function connectToPastor() {
    try {
        const response = await fetch('/chatbot/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ message: 'I want to talk to a pastor' })
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.connect_to_admin) {
                window.location.href = '/chat';
            }
        }
    } catch (error) {
        console.error('Error connecting to pastor:', error);
    }
}
