@extends('member.dashboard_member')

@section('content')
<div class="verse-hero">
    <div class="floating-particles"></div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="verse-card">
                    <div class="card-glow"></div>
                    <div class="verse-header">
                        <div class="icon-wrapper">
                            <i class="fas fa-book-bible"></i>
                        </div>
                        <h1 class="verse-title">Daily Verse</h1>
                        <p class="verse-subtitle">God's Word for Today</p>
                    </div>
                    
                    <div id="verse-container" class="verse-content">
                        <div class="loading-animation">
                            <div class="bible-loader">
                                <div class="book-pages"></div>
                                <div class="book-pages"></div>
                                <div class="book-pages"></div>
                            </div>
                            <p class="loading-text">Seeking God's Word...</p>
                        </div>
                    </div>
                    

                </div>
            </div>
        </div>
    </div>
</div>

<style>
.verse-hero {
    min-height: 100vh;
    background: #f8f9fa;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    padding: 2rem 0;
}

.floating-particles {
    position: absolute;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(2px 2px at 20px 30px, rgba(255,255,255,0.3), transparent),
        radial-gradient(2px 2px at 40px 70px, rgba(255,255,255,0.2), transparent),
        radial-gradient(1px 1px at 90px 40px, rgba(255,255,255,0.4), transparent);
    background-repeat: repeat;
    background-size: 100px 100px;
    animation: float 20s infinite linear;
}

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    100% { transform: translateY(-100px) rotate(360deg); }
}

.verse-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 30px;
    padding: 3rem;
    box-shadow: 0 30px 80px rgba(0, 0, 0, 0.2);
    position: relative;
    overflow: hidden;
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(72, 98, 212, 0.3), transparent);
    animation: rotate 8s linear infinite;
    z-index: -1;
}

@keyframes rotate {
    100% { transform: rotate(360deg); }
}

.verse-header {
    text-align: center;
    margin-bottom: 3rem;
}

.icon-wrapper {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, #6e84e5, #2c49a7);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1.5rem;
    box-shadow: 0 15px 35px rgba(103, 126, 234, 0.4);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.icon-wrapper i {
    font-size: 2rem;
    color: white;
}

.verse-title {
    font-size: 3rem;
    font-weight: 700;
    background: linear-gradient(135deg, #667eea, #514ba2);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    margin-bottom: 0.5rem;
}

.verse-subtitle {
    font-size: 1.2rem;
    color: #666;
    margin: 0;
}

.verse-content {
    min-height: 300px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    margin-bottom: 3rem;
}

.loading-animation {
    text-align: center;
}

.bible-loader {
    position: relative;
    width: 60px;
    height: 40px;
    margin: 0 auto 1rem;
}

.book-pages {
    position: absolute;
    width: 50px;
    height: 35px;
    background: linear-gradient(135deg, #667eea, #4b4fa2);
    border-radius: 3px;
    animation: flip 1.5s infinite;
}

.book-pages:nth-child(1) { animation-delay: 0s; }
.book-pages:nth-child(2) { animation-delay: 0.3s; }
.book-pages:nth-child(3) { animation-delay: 0.6s; }

@keyframes flip {
    0%, 80%, 100% { transform: rotateY(0deg); opacity: 1; }
    40% { transform: rotateY(90deg); opacity: 0.5; }
}

.loading-text {
    color: #667eea;
    font-weight: 500;
    animation: breathe 2s infinite;
}

@keyframes breathe {
    0%, 100% { opacity: 0.7; }
    50% { opacity: 1; }
}

.verse-display {
    text-align: center;
    animation: slideUp 0.8s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.verse-text {
    font-size: 2rem;
    font-weight: 300;
    line-height: 1.6;
    color: #333;
    margin-bottom: 2rem;
    position: relative;
    font-style: italic;
}

.verse-text::before,
.verse-text::after {
    content: '"';
    font-size: 4rem;
    color: #667eea;
    opacity: 0.3;
    position: absolute;
    font-family: serif;
}

.verse-text::before {
    top: -10px;
    left: -30px;
}

.verse-text::after {
    bottom: -40px;
    right: -30px;
}

.verse-reference {
    font-size: 1.5rem;
    font-weight: 600;
    color: #667eea;
    position: relative;
}

.verse-reference::before {
    content: '';
    position: absolute;
    top: 50%;
    left: -50px;
    width: 30px;
    height: 2px;
    background: #667eea;
    transform: translateY(-50%);
}

.verse-reference::after {
    content: '';
    position: absolute;
    top: 50%;
    right: -50px;
    width: 30px;
    height: 2px;
    background: #667eea;
    transform: translateY(-50%);
}



@media (max-width: 768px) {
    .verse-title { font-size: 2rem; }
    .verse-text { font-size: 1.5rem; }
    .verse-card { padding: 2rem; margin: 1rem; }
    .verse-text::before, .verse-text::after { display: none; }
    .verse-reference::before, .verse-reference::after { display: none; }
}
</style>

<script>
const verseReferences = [
    'John 3:16', 'Philippians 4:13', 'Jeremiah 29:11', 'Proverbs 3:5-6',
    'Romans 8:28', 'Isaiah 41:10', 'Matthew 11:28', 'Psalm 23:1',
    'Joshua 1:9', 'Psalm 46:1', '2 Corinthians 5:7', 'Ephesians 2:8-9',
    'Romans 12:2', 'Galatians 5:22-23', 'Matthew 6:33', 'Proverbs 16:3',
    'Isaiah 40:31', 'Psalm 119:105', 'James 1:2-3', '1 Corinthians 13:4-7'
];

async function fetchVerse() {
    const container = document.getElementById('verse-container');
    container.innerHTML = `
        <div class="loading-animation">
            <div class="bible-loader">
                <div class="book-pages"></div>
                <div class="book-pages"></div>
                <div class="book-pages"></div>
            </div>
            <p class="loading-text">Seeking God's Word...</p>
        </div>
    `;
    
    const randomRef = verseReferences[Math.floor(Math.random() * verseReferences.length)];
    
    try {
        const response = await fetch(`https://bible-api.com/${randomRef.replace(/ /g, '+')}`);
        const data = await response.json();
        
        setTimeout(() => {
            container.innerHTML = `
                <div class="verse-display">
                    <p class="verse-text">${data.text.trim()}</p>
                    <p class="verse-reference">${data.reference}</p>
                </div>
            `;
        }, 1000);
    } catch (error) {
        container.innerHTML = '<p class="text-danger">Failed to load verse. Please try again.</p>';
    }
}



document.addEventListener('DOMContentLoaded', fetchVerse);
</script>
@endsection