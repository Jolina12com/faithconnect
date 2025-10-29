@extends('member.dashboard_member')
@section('content')
<div class="container">
    <style>
        :root {
            --primary-color: #2d20b5;
            --secondary-color: #3c6af3;
            --light-bg: #f8f8fc;
            --dark-text: #333;
            --light-text: #fff;
            --border-radius: 12px;
      
            --transition: all 0.3s ease;
        }

        .chat-container {
            top: 10%;
            width: 1200px;
            max-width: 1400px;
            height: 600px;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
            background-color: white;
            margin: 0 auto;
            transition: all 0.4s ease;
        }

        .chat-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(45, 32, 181, 0.15);
        }

        .chat-header {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 18px 25px;
            text-align: center;
            font-size: 1.3em;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 10;
        }

        .chat-messages {
            flex: 1;
            padding: 25px;
            overflow-y: auto;
            background-color: #f9fafc;
            scroll-behavior: smooth;
        }

        .message {
            margin-bottom: 20px;
            max-width: 75%;
            padding: 14px 18px;
            border-radius: 20px;
            line-height: 1.5;
            position: relative;
            animation: messageAppear 0.3s ease-out forwards;
            word-wrap: break-word;
        }

        @keyframes messageAppear {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bot-message {
            background-color: white;
            color: var(--dark-text);
            border-top-left-radius: 4px;
            align-self: flex-start;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-right: auto;
            border-left: 3px solid var(--primary-color);
        }

        .user-message {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border-top-right-radius: 4px;
            align-self: flex-end;
            margin-left: auto;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border-right: 3px solid var(--secondary-color);
        }

        .chat-input {
            display: flex;
            padding: 15px 20px;
            background-color: white;
            border-top: 1px solid #eaeaea;
            transition: all 0.3s ease;
        }

        .input-area {
            display: flex;
            align-items: center;
            padding: 10px 20px;
            flex: 1;
            background-color: #f5f7fa;
            border-radius: 50px;
            margin-right: 15px;
            transition: all 0.3s ease;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .input-area:focus-within {
            background-color: white;
            box-shadow: 0 3px 10px rgba(45, 32, 181, 0.1);
            transform: translateY(-2px);
        }

        .input-icon {
            color: var(--primary-color);
            font-size: 1.3rem;
            margin-right: 15px;
            transition: all 0.3s ease;
        }

        .input-field {
            flex: 1;
            border: none;
            outline: none;
            font-size: 1rem;
            font-weight: 500;
            color: var(--dark-text);
            background: transparent;
            font-family: inherit;
            padding: 10px 0;
            transition: all 0.3s ease;
        }

        .input-field::placeholder {
            color: #aab0bc;
            transition: all 0.3s ease;
        }

        .input-field:focus::placeholder {
            opacity: 0.7;
        }

        .button-area {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px 15px;
            background-color: white;
            border-top: 1px solid #f5f5f5;
        }

        .emotion-buttons {
            display: flex;
            gap: 12px;
        }

        .emotion-button {
            background: none;
            border: none;
            font-size: 1.4rem;
            cursor: pointer;
            transition: all 0.3s ease;
            color: #aab0bc;
            padding: 5px;
            border-radius: 50%;
        }

        .emotion-button:hover {
            transform: scale(1.2) translateY(-3px);
            color: var(--primary-color);
            background-color: rgba(45, 32, 181, 0.05);
        }

        .send-button {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            width: 42px;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(45, 32, 181, 0.2);
        }

        .send-button:hover {
            transform: scale(1.1) translateY(-2px);
            box-shadow: 0 5px 15px rgba(45, 32, 181, 0.3);
        }

        .bible-reference {
            font-style: italic;
            margin-top: 8px;
            font-size: 0.9em;
            color: #6c757d;
            text-align: right;
        }

        .typing-indicator {
            display: flex;
            padding: 12px 18px;
            background-color: white;
            border-radius: 20px;
            width: fit-content;
            margin-bottom: 20px;
            align-items: center;
            display: none;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            border-left: 3px solid var(--primary-color);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background-color: var(--primary-color);
            border-radius: 50%;
            margin: 0 3px;
            animation: typing-animation 1.5s infinite ease-in-out;
        }

        .typing-dot:nth-child(1) { animation-delay: 0s; }
        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

        @keyframes typing-animation {
            0%, 60%, 100% { transform: translateY(0); opacity: 0.5; }
            30% { transform: translateY(-5px); opacity: 1; }
        }

        /* Chat message time stamp */
        .message-timestamp {
            font-size: 0.7em;
            margin-top: 5px;
            opacity: 0.6;
            text-align: right;
        }

        /* Chat message animations */
        .message.bot-message {
            animation: botMessageAppear 0.4s ease-out forwards;
        }

        .message.user-message {
            animation: userMessageAppear 0.3s ease-out forwards;
        }

        @keyframes botMessageAppear {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes userMessageAppear {
            from {
                opacity: 0;
                transform: translateX(10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Updated media query for better mobile responsiveness */
        @media (max-width: 768px) {
            .chat-container {
                width: 100%;
                height: calc(100vh - 40px);
                max-width: 100%;
                border-radius: 0;
                margin: 0;
            }
            
            .message {
                max-width: 85%;
            }
        }

        /* New styles for the welcome screen */
        .welcome-screen {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--dark-text);
            text-align: center;
            padding: 40px;
            box-shadow: none;
            position: relative;
            overflow: hidden;
            border-radius: var(--border-radius);
        }

        .welcome-screen::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, rgba(45, 32, 181, 0.05) 0%, rgba(60, 106, 243, 0.05) 100%);
            z-index: 1;
        }

        .welcome-logo {
            font-size: 4rem;
            margin-bottom: 30px;
            position: relative;
            z-index: 2;
            animation: float 3s ease-in-out infinite;
            color: var(--primary-color);
        }

        .welcome-title {
            font-size: 2.5rem;
            margin-bottom: 20px;
            font-weight: 700;
            overflow: hidden;
            white-space: nowrap;
            margin: 0 auto;
            letter-spacing: .05em;
            animation: 
                typing 3.5s steps(40, end),
                blink-caret .75s step-end infinite;
            position: relative;
            z-index: 2;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            max-width: 80%;
        }

        .welcome-subtitle {
            font-size: 1.3rem;
            margin-bottom: 50px;
            position: relative;
            z-index: 2;
            opacity: 0;
            animation: fadeIn 1s ease-in forwards 3.5s;
            color: #666;
            max-width: 70%;
            line-height: 1.6;
        }

        .name-form {
            width: 100%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
            opacity: 0;
            animation: slideUp 1s ease-out forwards 4s;
        }

        .name-input {
            color: black;
            width: 100%;
            padding: 16px 25px;
            border-radius: 50px;
            margin-bottom: 25px;
            font-size: 1.2rem;
            text-align: center;
            outline: none;
            border: 2px solid transparent;
            background: white;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .name-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 8px 25px rgba(45, 32, 181, 0.2);
            transform: translateY(-3px);
        }

        .name-input::placeholder {
            color: #aaa;
        }

        .start-button {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 15px 40px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }

        .start-button:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.15);
        }

        .start-button:disabled {
            background: #ddd;
            color: #999;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .start-button::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        .welcome-decoration {
            position: absolute;
            width: 300px;
            height: 300px;
            background: linear-gradient(120deg, rgba(45, 32, 181, 0.1) 0%, rgba(60, 106, 243, 0.1) 100%);
            border-radius: 50%;
            z-index: 0;
        }

        .decoration-1 {
            top: -150px;
            left: -150px;
            animation: float 6s ease-in-out infinite;
        }

        .decoration-2 {
            bottom: -150px;
            right: -150px;
            animation: float 8s ease-in-out infinite reverse;
        }

        .welcome-icon {
            font-size: 4rem;
            margin-bottom: 20px;
            color: var(--primary-color);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        @keyframes float {
            0%, 100% { transform: translateY(0) rotate(0deg); }
            50% { transform: translateY(-15px) rotate(2deg); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes shine {
            0% { transform: translateX(-100%) rotate(45deg); }
            100% { transform: translateX(100%) rotate(45deg); }
        }

        @keyframes typing {
            from { width: 0 }
            to { width: 100% }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent }
            50% { border-color: var(--primary-color) }
        }

        /* Animation for transition */
        .fade-out {
            animation: fadeOut 0.5s forwards;
        }

        .fade-in {
            animation: fadeIn 0.5s forwards;
        }

        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }

        /* Hide the chat interface initially */
        .chat-interface {
            display: none;
        }

        @media (max-width: 768px) {
            .chat-container {
                width: 100%;
                max-width: 100%;
            }
        }

        /* Add particles background effect styles */
        .particles-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }

        .particle {
            position: absolute;
            border-radius: 50%;
            opacity: 0.3;
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
        }

        @keyframes particleAnimation {
            0% {
                transform: translateY(0) rotate(0deg);
                opacity: 0;
            }
            20% {
                opacity: 0.3;
            }
            80% {
                opacity: 0.3;
            }
            100% {
                transform: translateY(-800px) rotate(360deg);
                opacity: 0;
            }
        }

        /* User typing indicator styles */
        .user-typing {
            padding: 8px 15px;
            font-size: 0.85em;
            color: #6c757d;
            font-style: italic;
            opacity: 0;
            transition: opacity 0.3s ease;
            text-align: right;
            margin-bottom: 10px;
        }

        .user-typing.show {
            opacity: 1;
        }

        /* More verses button styles */
        .more-verses-btn {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            border: none;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.85em;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 8px rgba(45, 32, 181, 0.2);
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .more-verses-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 32, 181, 0.3);
        }

        .more-verses-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .more-verses-container {
            margin-top: 10px;
            text-align: left;
        }

        /* Quick actions styles */
        .quick-actions {
            display: flex;
            gap: 8px;
            padding: 10px 20px;
            background-color: white;
            border-top: 1px solid #f5f5f5;
            flex-wrap: wrap;
            justify-content: center;
        }

        .quick-action-btn {
            background: linear-gradient(120deg, #f8f9fa 0%, #e9ecef 100%);
            color: var(--dark-text);
            border: 1px solid #dee2e6;
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.85em;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .quick-action-btn:hover {
            background: linear-gradient(120deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(45, 32, 181, 0.2);
        }
    </style>

    <div class="chat-container">
        <!-- Welcome Screen -->
        <div id="welcome-screen" class="welcome-screen">
            <div class="particles-container" id="particles-container"></div>
            <div class="welcome-decoration decoration-1"></div>
            <div class="welcome-decoration decoration-2"></div>
            
         
            
            <h1 class="welcome-title" id="welcome-title">Welcome to Faith Connect — we're glad you're here.</h1>
            <p class="welcome-subtitle">A spiritual companion for your journey of faith. Connect with guidance, reflection, and support in your daily walk.</p>

            <form class="name-form" id="name-form">
                <input type="text" id="user-name" class="name-input" placeholder="What name would you prefer I use?" required>
                <button type="submit" id="start-chat" class="start-button" disabled>Start Your Journey</button>
            </form>
        </div>

        <!-- Chat Interface (initially hidden) -->
        <div id="chat-interface" class="chat-interface">
            <div class="chat-header" id="chat-header">
                <i class="fas fa-cross me-2"></i>
                <span>Church Community Chat</span>
            </div>
            <div class="chat-messages" id="chat-messages">
                <!-- Messages will appear here -->
                <div id="user-typing" class="user-typing">You are typing...</div>
            </div>
            <div class="chat-input">
                <div class="input-area">
                    <div class="input-icon">
                        <i class="fas fa-heart-pulse"></i>
                    </div>
                    <input type="text" id="user-input" class="input-field" placeholder="Share your thoughts or feelings..." autocomplete="off">
                </div>
            </div>
            <div class="button-area">
                <div class="emotion-buttons">
                    <button class="emotion-button" title="Happy">😊</button>
                    <button class="emotion-button" title="Sad">😔</button>
                    <button class="emotion-button" title="Anxious">😰</button>
                    <button class="emotion-button" title="Tired">😴</button>
                    <button class="emotion-button" title="Grateful">🙏</button>
                    <button class="emotion-button" title="Hopeful">✨</button>
                </div>
                <button id="send-button" class="send-button" title="Send">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div class="quick-actions" id="quick-actions">
                <button class="quick-action-btn" data-action="prayer">🙏 I need prayer</button>
                <button class="quick-action-btn" data-action="verse">📖 Give me a verse</button>
                <button class="quick-action-btn" data-action="pastor">👨‍💼 Talk to pastor</button>
                <button class="quick-action-btn" data-action="info">❓ Church info</button>
            </div>
        </div>
    </div>

    <script src="/js/chatbot-fix.js" defer></script>
    <script>
        // Dynamic Bible API function
        async function getVerseForEmotion(emotion) {
            const emotionToTopic = {
                happy: 'encouragement',
                sad: 'comfort',
                anxious: 'peace',
                tired: 'strength',
                grateful: 'encouragement',
                hopeful: 'hope',
                confused: 'encouragement',
                afraid: 'peace',
                angry: 'peace',
                lonely: 'comfort'
            };
            
            const topic = emotionToTopic[emotion] || 'encouragement';
            
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
                    if (data.verses && data.verses.length > 0) {
                        const verse = data.verses[0];
                        return { 
                            verse: verse.text, 
                            reference: verse.reference,
                            topic: topic,
                            allVerses: data.verses
                        };
                    }
                }
            } catch (error) {
                console.error('Error fetching verse:', error);
            }
            
            return { 
                verse: 'For God so loved the world that he gave his one and only Son.', 
                reference: 'John 3:16',
                topic: 'encouragement'
            };
        }



        // Enhanced pattern recognition
        const emotionPatterns = {
            happy: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(happy|joy|joyful|glad|cheerful|delighted|excited|content|pleased|thrilled|blessed|masaya|maligaya|tuwa|natutuwa|masigla)/i,
            sad: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(sad|depressed|down|blue|unhappy|upset|gloomy|sorrow|heartbroken|grief|miserable|malungkot|lungkot|nalulungkot|iyak|umiiyak)/i,
            anxious: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(anxious|worried|nervous|stress|stressed|panic|fear|concern|uneasy|distressed|anxiety|overwhelmed|balisa|kabado|nag-aalala|alalang-alala|takot|natatakot)/i,
            tired: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(tired|exhausted|weary|fatigue|drained|sleepy|worn out|lethargic|beat|spent|pagod|napagod|pagod na pagod|inaantok|antok|hinihingal)/i,
            grateful: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(grateful|thankful|blessed|appreciate|appreciative|gratitude|indebted|fortunate|mapagpasalamat|nagpapasalamat|salamat|pasasalamat)/i,
            hopeful: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(hopeful|optimistic|hope|looking forward|positive|encouraged|enthusiasm|expectant|may pag-asa|pag-asa|umaasa|positibo|inspirado)/i,
            confused: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(confused|unsure|uncertain|puzzled|perplexed|lost|bewildered|questioning|doubt|nalilito|litong-lito|naguguluhan|duda|tanong)/i,
            afraid: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(afraid|scared|fearful|frightened|terrified|alarmed|petrified|dread|panic|takot|natatakot|kinakabahan|kabado|nangangatog)/i,
            angry: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(angry|mad|furious|irritated|annoyed|outraged|frustrated|irate|enraged|hostile|galit|nagagalit|inis|naiinis|asara|inis na inis)/i,
            lonely: /(i'm|i am|im|feeling|feel|felt|ako ay|nakakaramdam|nararamdaman).*(lonely|alone|isolated|abandoned|solitary|friendless|forsaken|neglected|alienated|malungkot|nag-iisa|nag-iisa lang|iniwan|walang kasama)/i
        };

        // Advanced patterns for specific situations
        const advancedPatterns = {
            crisis: /suicide|kill.*myself|end.*life|worthless|no.*point|give.*up|can't.*go.*on|ayaw.*na.*mabuhay|pagod.*na.*sa.*buhay/i,
            depression: /depress|hopeless|empty|numb|can't.*feel|walang.*silbi|walang.*kwenta/i,
            relationship: /marriage|divorce|boyfriend|girlfriend|family.*fight|break.*up|cheating|asawa|nobyo|nobya|hiwalay/i,
            financial: /money|job.*lost|unemployed|bills|debt|broke|walang.*pera|nawalan.*trabaho|utang/i,
            health: /sick|cancer|hospital|pain|dying|sakit|ospital|mamatay/i,
            addiction: /drink|drugs|gambling|addiction|can't.*stop|lasing|droga|sugal|bisyo/i,
            churchInfo: /service|worship|sunday|when|time|schedule|simbahan|misa|linggo/i
        };

        // Crisis resources
        const crisisResources = {
            hotlines: [
                "🆘 Crisis Hotline: 988 (US)",
                "🆘 Philippines: 0917-899-8727 (HOPELINE)",
                "🚨 Emergency: 911"
            ],
            message: "Your life has value and meaning. Please reach out for immediate help."
        };

        // Helper function to determine if input might be gibberish
        function isGibberish(input) {
            // Check for very long words without vowels
            const noVowels = /[bcdfghjklmnpqrstvwxyz]{5,}/i.test(input);

            // Check for long strings of random characters
            const randomChars = /[^a-zA-Z\s]{5,}/.test(input);

            // Check if input has way too many consonants compared to vowels
            const consonants = (input.match(/[bcdfghjklmnpqrstvwxyz]/gi) || []).length;
            const vowels = (input.match(/[aeiou]/gi) || []).length;
            const badRatio = consonants > 0 && vowels > 0 && consonants / vowels > 5;

            return noVowels || randomChars || badRatio;
        }

        // Add this after the bibleVerses object and before the chatFlow object
        const learningSystem = {
            conversationHistory: [],
            learnedResponses: {},
            
            // Store conversation for learning
            storeConversation: function(userInput, botResponse, emotion) {
                this.conversationHistory.push({
                    input: userInput,
                    response: botResponse,
                    emotion: emotion,
                    timestamp: new Date().toISOString()
                });
                
                // Store in localStorage for persistence
                localStorage.setItem('chatbotLearning', JSON.stringify({
                    history: this.conversationHistory,
                    learnedResponses: this.learnedResponses
                }));
            },
            
            // Learn from successful responses
            learnFromResponse: function(userInput, botResponse, emotion) {
                if (!this.learnedResponses[emotion]) {
                    this.learnedResponses[emotion] = [];
                }
                
                this.learnedResponses[emotion].push({
                    input: userInput,
                    response: botResponse
                });
            },
            
            // Get learned response
            getLearnedResponse: function(emotion, userInput) {
                if (this.learnedResponses[emotion]) {
                    // Find similar past inputs
                    const similarResponses = this.learnedResponses[emotion].filter(item => 
                        this.calculateSimilarity(item.input, userInput) > 0.7
                    );
                    
                    if (similarResponses.length > 0) {
                        // Return the most similar response
                        return similarResponses[0].response;
                    }
                }
                return null;
            },
            
            // Calculate similarity between two strings
            calculateSimilarity: function(str1, str2) {
                const words1 = str1.toLowerCase().split(' ');
                const words2 = str2.toLowerCase().split(' ');
                const commonWords = words1.filter(word => words2.includes(word));
                return commonWords.length / Math.max(words1.length, words2.length);
            },
            
            // Load learned data from localStorage
            loadLearnedData: function() {
                const savedData = localStorage.getItem('chatbotLearning');
                if (savedData) {
                    const data = JSON.parse(savedData);
                    this.conversationHistory = data.history || [];
                    this.learnedResponses = data.learnedResponses || {};
                }
            }
        };

        // Chat flow management
        const chatFlow = {
            initial: {
                processInput: function(userName) {
                    return {
                        messages: [
                            `Hello, ${userName}! . How are you feeling today?`
                        ],
                        next: "feelingResponse"
                    };
                }
            },
            feelingResponse: {
                processInput: function(input) {
                    // PRIORITY 1: Crisis detection
                    if (advancedPatterns.crisis.test(input.toLowerCase())) {
                        return {
                            emotion: "crisis",
                            next: "crisisResponse"
                        };
                    }

                    // Check for prayer requests (English and Tagalog)
                    if (/pray|prayer|praying|request.*pray|need.*pray|want.*pray|can.*pray|dasal|ipagdasal|kailangan.*dasal|pwede.*dasal|gusto.*dasal|dasal po|magdasal/i.test(input.toLowerCase())) {
                        return {
                            emotion: "prayerRequest",
                            next: "prayerResponse"
                        };
                    }

                    // Check for specific situations
                    for (const [situation, pattern] of Object.entries(advancedPatterns)) {
                        if (pattern.test(input.toLowerCase())) {
                            return {
                                emotion: situation,
                                next: "situationResponse"
                            };
                        }
                    }

                    // Check for gibberish or very unusual input
                    if (isGibberish(input)) {
                        return {
                            emotion: "gibberish",
                            next: "gibberishResponse"
                        };
                    }

                    // Check if input is too short (just 1-2 characters)
                    if (input.length < 2) {
                        return {
                            emotion: "tooShort",
                            next: "tooShortResponse"
                        };
                    }

                    // Detect emotion from user input (English and Tagalog)
                    for (const [emotion, pattern] of Object.entries(emotionPatterns)) {
                        if (pattern.test(input.toLowerCase())) {
                            return {
                                emotion: emotion,
                                next: "verseResponse"
                            };
                        }
                    }

                    // If no specific emotion detected but input seems valid
                    return {
                        emotion: "unspecified",
                        next: "askForClarification"
                    };
                }
            },
            prayerResponse: {
                processInput: function() {
                    return {
                        messages: [
                            "I'd be happy to pray with you.",
                            "Panginoon, kami po ay lumalapit sa Inyo na may mga dalangin sa aming puso. Alam Niyo po ang aming mga pangangailangan bago pa man namin ito sabihin. Bigyan Niyo po kami ng lakas, karunungan, at kapayapaan sa araw na ito. Iparamdam Niyo po ang Inyong presensya at pag-ibig. Sa pangalan ni Jesus, Amen.",
                            "Is there anything specific you'd like me to pray about? / May nais ka pa bang ipagdasal?"
                        ],
                        next: "conversationContinuation"
                    };
                }
            },
            verseResponse: {
                processInput: async function(emotion) {
                    let responseMessages = [];

                    // Varied responses for more natural conversation
                    const responses = {
                        happy: [
                            `${userName}, I'm so glad to hear you're feeling happy! 😊 Joy is a gift from God.`,
                            `That's wonderful, ${userName}! Your happiness is contagious. Let's celebrate this moment together!`,
                            `I can feel your joy, ${userName}! It's beautiful when our hearts are full of gladness.`
                        ],
                        sad: [
                            `${userName}, I'm truly sorry you're going through this. 💙 Please know that God is close to the brokenhearted.`,
                            `I hear your pain, ${userName}. It's okay to feel sad. God collects every tear and understands your sorrow.`,
                            `${userName}, my heart goes out to you. Remember, even in darkness, God's light shines brightest.`
                        ],
                        anxious: [
                            `${userName}, I understand how overwhelming anxiety can feel. 🕊️ Let's find peace together in God's promises.`,
                            `I hear your worry, ${userName}. God cares deeply about what troubles your heart.`,
                            `${userName}, anxiety is heavy, but you don't have to carry it alone. God invites us to cast our cares on Him.`
                        ],
                        tired: [
                            `${userName}, it sounds like you need rest. 😴 Jesus said, "Come to me, all who are weary."`,
                            `I can sense your exhaustion, ${userName}. God offers renewal and strength to the weary.`,
                            `${userName}, being tired is a sign you've been carrying much. Let's find rest in God's presence.`
                        ],
                        grateful: [
                            `${userName}, your gratitude is beautiful! 🙏 A thankful heart is a magnet for God's blessings.`,
                            `I love hearing your thankfulness, ${userName}! Gratitude transforms our perspective.`,
                            `${userName}, your grateful spirit is inspiring! God delights in a thankful heart.`
                        ],
                        hopeful: [
                            `${userName}, your hope is powerful! ✨ Hope anchors the soul and points us to God's promises.`,
                            `I'm encouraged by your hope, ${userName}! God is the source of all hope and new beginnings.`,
                            `${userName}, holding onto hope is an act of faith. God honors that trust.`
                        ],
                        confused: [
                            `${userName}, it's completely okay to feel confused. 🤔 God promises to guide us through uncertainty.`,
                            `I understand your confusion, ${userName}. Sometimes clarity comes one step at a time.`,
                            `${userName}, confusion is part of the journey. God's wisdom is available to those who seek it.`
                        ],
                        afraid: [
                            `${userName}, I hear your fear. 💪 Remember, God's perfect love casts out all fear.`,
                            `Fear is natural, ${userName}, but you're not alone. God is your refuge and strength.`,
                            `${userName}, even when afraid, you can find courage in God's presence with you.`
                        ],
                        angry: [
                            `${userName}, I acknowledge your anger. 😤 It's okay to feel this way. Let's find peace together.`,
                            `I hear your frustration, ${userName}. God can handle our anger and transform it into peace.`,
                            `${userName}, anger shows you care deeply. Let's channel that energy toward healing.`
                        ],
                        lonely: [
                            `${userName}, I'm sorry you're feeling lonely. 🤗 Please know that God is always with you, and so am I.`,
                            `Loneliness is painful, ${userName}. But you're never truly alone - God walks beside you.`,
                            `${userName}, I'm here with you. God promises to never leave or forsake us.`
                        ]
                    };

                    // Select varied response
                    const emotionResponses = responses[emotion] || [
                        `Thank you for sharing, ${userName}. God's word offers comfort for every situation.`
                    ];
                    const randomResponse = emotionResponses[Math.floor(Math.random() * emotionResponses.length)];
                    responseMessages.push(randomResponse);

                    // Add follow-up question first
                    const followUps = {
                        happy: "Would you like to share what brought you this joy? Or shall we pray in thanksgiving?",
                        sad: "Would you like to talk more about what's troubling you? I'm here to listen and pray with you.",
                        anxious: "What's weighing on your heart right now? Sometimes sharing our worries helps lighten the load.",
                        tired: "Have you been able to rest lately? Would you like me to pray for renewal and strength?",
                        grateful: "What are you most thankful for today? Sharing our blessings multiplies the joy!",
                        hopeful: "What are you hoping for? I'd love to pray with you about your dreams and aspirations.",
                        confused: "What decision or situation is causing confusion? Let's seek God's wisdom together.",
                        afraid: "What's causing you fear? Remember, we can face anything with God's strength.",
                        angry: "What's frustrating you? It's healthy to express these feelings in a safe space.",
                        lonely: "Would you like to connect with our church community? Or shall we spend time in prayer together?"
                    };
                    
                    responseMessages.push(followUps[emotion] || "Is there anything specific you'd like to talk about or pray about today?");

                    return {
                        messages: responseMessages,
                        next: "conversationContinuation",
                        emotion: emotion,
                        sendVerse: true
                    };

                }
            },
            gibberishResponse: {
                processInput: function() {
                    return {
                        messages: [
                            "I'm sorry, but I didn't quite understand what you typed. It seems like there might have been some confusion.",
                            "Would you like to try again? You can tell me how you're feeling today, or ask about church services or prayer.",
                            "For example, you could say 'I'm feeling anxious' or 'I need prayer'."
                        ],
                        next: "feelingResponse"
                    };
                }
            },
            tooShortResponse: {
                processInput: function() {
                    return {
                        messages: [
                            "I noticed your message was very brief. Could you please share a little more about how you're feeling today?",
                            "For example, you might say 'I'm feeling happy' or 'I'm feeling worried about something'."
                        ],
                        next: "feelingResponse"
                    };
                }
            },
            askForClarification: {
                message: "I'm not sure I understood how you're feeling. Could you share a bit more about your emotions today?",
                next: "feelingResponse"
            },
            conversationContinuation: {
                processInput: function(input) {
                    // Increment message count
                    if (!window.messageCount) window.messageCount = 0;
                    window.messageCount++;
                    
                    // Check for pastor connection
                    if (/connect.*pastor|talk.*pastor|speak.*pastor|pastor|admin|help.*someone|talk.*someone|human|staff|priest|minister/i.test(input.toLowerCase())) {
                        return {
                            messages: [
                                `${userName}, I understand you'd like to speak with a pastor. That's a wonderful step!`,
                                "Would you like me to connect you now? Click the button below to start a conversation with our pastoral team."
                            ],
                            next: "conversationContinuation",
                            showPastorButton: true
                        };
                    }
                    
                    // Proactively offer pastor connection after 3+ messages
                    if (window.messageCount >= 3 && Math.random() < 0.7) {
                        return {
                            messages: [
                                `${userName}, I've been listening to your heart, and I want you to know that sometimes it helps to talk with someone personally.`,
                                "Would you like to connect with one of our pastors for a more personal conversation? They're available to provide deeper guidance and prayer support."
                            ],
                            next: "pastorOfferResponse",
                            showPastorButton: true
                        };
                    }
                    
                    // Check for prayer requests with more comprehensive pattern
                    if (/pray|prayer|praying|request.*pray|need.*pray|want.*pray/i.test(input.toLowerCase())) {
                        return {
                            messages: [
                                "I'd be happy to pray with you.",
                                "Dear Lord, we come before you with the concerns on our heart. You know our needs before we ask, and your love for us is constant. Give us strength, wisdom, and peace as we walk through this day. Help us to feel your presence and know your comfort. In Jesus' name, Amen.",
                                "Is there anything specific you'd like me to pray about?"
                            ],
                            next: "conversationContinuation"
                        };
                    }

                    // Check for questions about church services
                    if (/service|worship|sunday|when|time|schedule/i.test(input)) {
                        return {
                            messages: [
                                "Our regular worship services are held on Sundays at 9:00 AM and 11:00 AM. We also have Wednesday evening Bible study at 7:00 PM.",
                                "Would you like to know about any specific ministries or activities at our church?"
                            ],
                            next: "conversationContinuation"
                        };
                    }

                    // Check for emotional content in the input
                    let detectedEmotion = null;
                    for (const [emotion, pattern] of Object.entries(emotionPatterns)) {
                        if (pattern.test(input.toLowerCase())) {
                            detectedEmotion = emotion;
                            break;
                        }
                    }

                    // If emotion is detected, provide a contextual response
                    if (detectedEmotion) {
                        let responseMessages = [];
                        
                        // Check if emotion changed
                        const emotionChanged = conversationContext.lastEmotion && 
                                             conversationContext.lastEmotion !== detectedEmotion;
                        
                        if (emotionChanged) {
                            responseMessages.push(`${userName}, I notice your feelings have shifted from ${conversationContext.lastEmotion} to ${detectedEmotion}. That's completely natural. Let me support you through this.`);
                        } else {
                            // Contextual responses with user name
                            const contextualResponses = {
                                happy: `${userName}, your joy is contagious! Let's celebrate this moment together.`,
                                sad: `${userName}, I'm here with you in this difficult time. God is close to the brokenhearted.`,
                                anxious: `${userName}, I hear your anxiety. Let's find peace in God's promises together.`,
                                tired: `${userName}, you've been carrying a lot. Let's find rest in God's word.`,
                                grateful: `${userName}, your gratitude is beautiful. God loves a thankful heart.`,
                                hopeful: `${userName}, your hope inspires me. God is the source of all hope.`,
                                confused: `${userName}, it's okay to feel confused. Let's seek God's guidance together.`,
                                afraid: `${userName}, I hear your fear. God's perfect love casts out all fear.`,
                                angry: `${userName}, I understand your frustration. Let's find peace in God's wisdom.`,
                                lonely: `${userName}, you're not alone. God is always with you, and so am I.`
                            };
                            responseMessages.push(contextualResponses[detectedEmotion] || `${userName}, I hear what you're saying. Let's find comfort in God's word.`);
                        }
                        
                        conversationContext.lastEmotion = detectedEmotion;

                        // Fetch verse from API
                        getVerseForEmotion(detectedEmotion).then(verse => {
                            if (verse) {
                                setTimeout(() => {
                                    addBotMessage(`Here's a verse that might speak to your heart: "${verse.verse}" - ${verse.reference}`, detectedEmotion);
                                }, 1500);
                            }
                        });
                        responseMessages.push("Would you like to share more about what's on your mind?");

                        return {
                            messages: responseMessages,
                            next: "conversationContinuation"
                        };
                    }

                    // Smart suggestions based on conversation history
                    const messageCount = conversationContext.topics.length;
                    let smartResponse = [];
                    
                    if (messageCount > 5) {
                        smartResponse.push(`${userName}, we've had a meaningful conversation. Would you like me to summarize our discussion or connect you with a pastor for deeper guidance?`);
                    } else if (messageCount > 2) {
                        smartResponse.push(`${userName}, I'm here to listen. What else is on your heart today?`);
                    } else {
                        smartResponse.push(`${userName}, I'm here to support you. You can share your feelings, ask for prayer, or discuss any concerns.`);
                    }
                    
                    return {
                        messages: smartResponse,
                        next: "conversationContinuation"
                    };
                }
            },
            pastorOfferResponse: {
                processInput: function(input) {
                    // Check for positive responses
                    if (/yes|yeah|yep|sure|okay|ok|oo|sige|gusto|want|need|opo/i.test(input.toLowerCase())) {
                        return {
                            messages: [
                                `Perfect, ${userName}! I'm connecting you with our pastoral team now.`,
                                "Click the link below to start your conversation with a pastor who can provide personalized guidance and prayer support."
                            ],
                            next: "conversationContinuation",
                            showPastorButton: true
                        };
                    }
                    
                    // Check for negative responses
                    if (/no|nope|not now|maybe later|hindi|ayaw|wag|later/i.test(input.toLowerCase())) {
                        return {
                            messages: [
                                `That's perfectly fine, ${userName}. I'm here whenever you need me.`,
                                "Is there anything else I can help you with today? Perhaps more prayer or encouragement?"
                            ],
                            next: "conversationContinuation"
                        };
                    }
                    
                    // Default response for unclear input
                    return {
                        messages: [
                            "I'm not sure if you'd like to connect with a pastor or continue our conversation.",
                            "Just say 'yes' if you'd like to talk to a pastor, or 'no' if you'd prefer to continue chatting with me."
                        ],
                        next: "pastorOfferResponse"
                    };
                }
            },
            crisisResponse: {
                processInput: function() {
                    return {
                        messages: [
                            `${userName}, I'm deeply concerned about you. Your life has immense value and meaning.`,
                            crisisResources.message,
                            crisisResources.hotlines.join("\n"),
                            "Please reach out to someone immediately. You don't have to face this alone.",
                            "Would you like me to connect you with a pastor right now?"
                        ],
                        next: "conversationContinuation",
                        showPastorButton: true,
                        urgent: true
                    };
                }
            },
            situationResponse: {
                processInput: function(situation) {
                    const responses = {
                        depression: [
                            `${userName}, depression is real and you're brave for sharing this.`,
                            "God sees your pain and walks with you through this darkness.",
                            "Would you like to talk to someone who can provide professional support?"
                        ],
                        relationship: [
                            `${userName}, relationship struggles are painful.`,
                            "God's love remains constant even when human relationships are difficult.",
                            "Would you like prayer for your relationships or to speak with a counselor?"
                        ],
                        financial: [
                            `${userName}, financial stress can be overwhelming.`,
                            "Remember, God knows your needs and promises to provide.",
                            "Our church has resources that might help. Would you like to connect with someone?"
                        ],
                        health: [
                            `${userName}, health concerns are frightening.`,
                            "God is your healer and comforter in times of illness.",
                            "Would you like prayer for healing and peace?"
                        ],
                        addiction: [
                            `${userName}, addiction is a battle, but recovery is possible.`,
                            "God's grace is sufficient, and there's no shame in seeking help.",
                            "Would you like to connect with our recovery ministry?"
                        ],
                        churchInfo: [
                            "Our worship services are Sundays at 9:00 AM and 11:00 AM.",
                            "Wednesday Bible study at 7:00 PM.",
                            "Would you like information about specific ministries?"
                        ]
                    };
                    
                    return {
                        messages: responses[situation] || [
                            `${userName}, thank you for sharing with me.`,
                            "I'm here to listen and support you."
                        ],
                        next: "conversationContinuation",
                        showPastorButton: situation !== 'churchInfo'
                    };
                }
            }
        };

        // Enhanced chat state with context memory
        let currentState = "initial";
        let chatHistory = [];
        let userName = "";
        let conversationContext = {
            lastEmotion: null,
            emotionCount: {},
            topics: [],
            prayerRequests: [],
            startTime: null,
            previousTopics: [],
            emotionHistory: [],
            preferredVerseTopics: [],
            crisisDetected: false,
            lastInteraction: null
        };

        // DOM Elements
        const welcomeScreen = document.getElementById("welcome-screen");
        const chatInterface = document.getElementById("chat-interface");
        const chatHeader = document.getElementById("chat-header");
        const chatMessages = document.getElementById("chat-messages");
        const userInput = document.getElementById("user-input");
        const sendButton = document.getElementById("send-button");
        const nameForm = document.getElementById("name-form");
        const nameInput = document.getElementById("user-name");
        const startChatButton = document.getElementById("start-chat");

        // Initialize welcome screen
        function initWelcomeScreen() {
            // Create particles for background effect
            createParticles();
            
            // Enable/disable start button based on name input
            nameInput.addEventListener('input', function() {
                startChatButton.disabled = !nameInput.value.trim();
            });

            // Handle form submission
            nameForm.addEventListener('submit', function(e) {
                e.preventDefault();
                userName = nameInput.value.trim();
                if (userName) {
                    transitionToChatInterface();
                }
            });
        }

        // Create particles for welcome screen
        function createParticles() {
            const container = document.getElementById('particles-container');
            const particleCount = 15; // Number of particles
            
            for (let i = 0; i < particleCount; i++) {
                const particle = document.createElement('div');
                particle.className = 'particle';
                
                // Random size between 5px and 20px
                const size = Math.random() * 15 + 5;
                particle.style.width = `${size}px`;
                particle.style.height = `${size}px`;
                
                // Random starting position
                const posX = Math.random() * 100;
                const posY = Math.random() * 100 + 100; // Start below the visible area
                particle.style.left = `${posX}%`;
                particle.style.bottom = `${-posY}px`;
                
                // Random animation duration between 15s and 30s
                const duration = Math.random() * 15 + 15;
                particle.style.animation = `particleAnimation ${duration}s linear infinite`;
                
                // Random animation delay
                const delay = Math.random() * 10;
                particle.style.animationDelay = `${delay}s`;
                
                container.appendChild(particle);
            }
        }

        // Transition from welcome screen to chat interface
        function transitionToChatInterface() {
            // Add fade-out animation to welcome screen
            welcomeScreen.classList.add('fade-out');

            // After animation completes, hide welcome screen and show chat interface
            setTimeout(() => {
                welcomeScreen.style.display = 'none';
                chatInterface.style.display = 'flex';
                chatInterface.classList.add('fade-in');

                // Update chat header with user's name
                chatHeader.textContent = `Church Community Chat with ${userName}`;

                // Initialize chat with personalized greeting
                initChat();
            }, 500); // Match this with the animation duration
        }

        // Initialize chat with time-based greeting
        function initChat() {
            conversationContext.startTime = new Date();
            const hour = new Date().getHours();
            let greeting = "Hello";
            if (hour < 12) greeting = "Good morning";
            else if (hour < 18) greeting = "Good afternoon";
            else greeting = "Good evening";
            
            const initialResponse = chatFlow.initial.processInput(userName);
            const personalizedGreeting = `${greeting}, ${userName}! 🙏 I'm here to support you on your faith journey. How are you feeling today?`;
            addBotMessage(personalizedGreeting);
            currentState = initialResponse.next;
        }

        // Add bot message to chat
        function addBotMessage(message, emotion = null) {
            // Create typing indicator
            const typingIndicator = document.createElement("div");
            typingIndicator.className = "typing-indicator";
            for (let i = 0; i < 3; i++) {
                const dot = document.createElement("div");
                dot.className = "typing-dot";
                typingIndicator.appendChild(dot);
            }
            chatMessages.appendChild(typingIndicator);

            // Show typing indicator
            typingIndicator.style.display = "flex";
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // After a delay, show the actual message
            setTimeout(() => {
                // Remove typing indicator
                typingIndicator.remove();

                // Create message element
                const messageElement = document.createElement("div");
                messageElement.className = "message bot-message";

                // Check if message contains a Bible verse (contains " - ")
                if (message.includes('" - ')) {
                    const parts = message.split('" - ');
                    const verseText = parts[0] + '"';
                    const reference = parts[1];

                    messageElement.innerHTML = verseText;

                    const referenceElement = document.createElement("div");
                    referenceElement.className = "bible-reference";
                    referenceElement.textContent = "- " + reference;
                    messageElement.appendChild(referenceElement);
                    
                    // Add + More button directly to verse message
                    if (emotion) {
                        const buttonDiv = document.createElement('div');
                        buttonDiv.className = 'more-verses-container';
                        
                        const moreButton = document.createElement('button');
                        moreButton.className = 'more-verses-btn';
                        moreButton.innerHTML = '<i class="fas fa-plus"></i> More verses';
                        
                        const handleMoreClick = async function(btn, container) {
                            btn.disabled = true;
                            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
                            
                            const emotionToTopic = {
                                happy: 'encouragement', sad: 'comfort', anxious: 'peace',
                                tired: 'strength', grateful: 'encouragement', hopeful: 'hope',
                                confused: 'encouragement', afraid: 'peace', angry: 'peace', lonely: 'comfort'
                            };
                            
                            const topic = emotionToTopic[emotion] || 'encouragement';
                            
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
                                    const verses = data.verses.slice(1);
                                    
                                    verses.forEach((verse, index) => {
                                        setTimeout(() => {
                                            const verseDiv = document.createElement('div');
                                            verseDiv.className = 'message bot-message';
                                            verseDiv.innerHTML = `<strong>${verse.reference}</strong><br>"${verse.text}"`;
                                            
                                            if (index === verses.length - 1) {
                                                const newButtonDiv = document.createElement('div');
                                                newButtonDiv.className = 'more-verses-container';
                                                const newBtn = document.createElement('button');
                                                newBtn.className = 'more-verses-btn';
                                                newBtn.innerHTML = '<i class="fas fa-plus"></i> More verses';
                                                newBtn.onclick = () => handleMoreClick(newBtn, newButtonDiv);
                                                newButtonDiv.appendChild(newBtn);
                                                verseDiv.appendChild(newButtonDiv);
                                            }
                                            
                                            chatMessages.appendChild(verseDiv);
                                            chatMessages.scrollTop = chatMessages.scrollHeight;
                                        }, index * 500);
                                    });
                                    
                                    container.remove();
                                } else {
                                    btn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error';
                                    setTimeout(() => {
                                        btn.disabled = false;
                                        btn.innerHTML = '<i class="fas fa-plus"></i> More verses';
                                    }, 2000);
                                }
                            } catch (error) {
                                btn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Error';
                                console.error('Error:', error);
                                setTimeout(() => {
                                    btn.disabled = false;
                                    btn.innerHTML = '<i class="fas fa-plus"></i> More verses';
                                }, 2000);
                            }
                        };
                        
                        moreButton.onclick = () => handleMoreClick(moreButton, buttonDiv);
                        
                        buttonDiv.appendChild(moreButton);
                        messageElement.appendChild(buttonDiv);
                    }
                } else {
                    messageElement.innerHTML = message;
                }

                // Add timestamp
                const timestamp = document.createElement("div");
                timestamp.className = "message-timestamp";
                timestamp.textContent = formatTime(new Date());
                messageElement.appendChild(timestamp);

                // Add to chat and scroll to bottom
                chatMessages.appendChild(messageElement);
                chatMessages.scrollTop = chatMessages.scrollHeight;

                // Add to history
                chatHistory.push({role: "bot", content: message, timestamp: new Date()});

                // Store analytics for bot message
                storeAnalytics(message, currentEmotion, true);
            }, 1000); // 1 second typing delay
        }

        // Add user message to chat
        async function addUserMessage(message) {
            // Filter profanity
            const filtered = await filterMessage(message);
            
            const messageElement = document.createElement("div");
            messageElement.className = "message user-message";
            messageElement.textContent = filtered;
            
            // Add timestamp
            const timestamp = document.createElement("div");
            timestamp.className = "message-timestamp";
            timestamp.textContent = formatTime(new Date());
            messageElement.appendChild(timestamp);
            
            chatMessages.appendChild(messageElement);
            chatMessages.scrollTop = chatMessages.scrollHeight;

            // Add to history
            chatHistory.push({role: "user", content: message, timestamp: new Date()});

            // Store analytics for user message
            storeAnalytics(message, currentEmotion, false);
        }

               // Format time for message timestamps
        function formatTime(date) {
            let hours = date.getHours();
            let minutes = date.getMinutes();
            const ampm = hours >= 12 ? 'PM' : 'AM';
            
            hours = hours % 12;
            hours = hours ? hours : 12; // the hour '0' should be '12'
            minutes = minutes < 10 ? '0' + minutes : minutes;
            
            return hours + ':' + minutes + ' ' + ampm;
        }

        // Database-powered profanity filter
        async function filterMessage(text) {
            try {
                const response = await fetch('/chatbot/filter-profanity', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ message: text })
                });
                
                if (response.ok) {
                    const data = await response.json();
                    return data.filtered_message || text;
                }
            } catch (error) {
                console.error('Profanity filter error:', error);
            }
            return text;
        }

        // Process user input
async function processUserInput() {
    const message = userInput.value.trim();
    if (message === "") return;

    // Add user message to chat
    await addUserMessage(message);
    userInput.value = "";

    // Process message based on current state
    switch(currentState) {
        case "feelingResponse":
            const feelingResult = chatFlow.feelingResponse.processInput(message);
            currentEmotion = feelingResult.emotion;
            
            // Track emotion context
            conversationContext.lastEmotion = feelingResult.emotion;
            conversationContext.emotionCount[feelingResult.emotion] = 
                (conversationContext.emotionCount[feelingResult.emotion] || 0) + 1;
            
            // Try to get a learned response first
            const learnedResponse = learningSystem.getLearnedResponse(feelingResult.emotion, message);
            
            if (learnedResponse) {
                setTimeout(() => {
                    addBotMessage(learnedResponse);
                }, 1000);
            } else {
                // Use the original response system
                if (feelingResult.next === "verseResponse") {
                    chatFlow.verseResponse.processInput(feelingResult.emotion).then(verseResponse => {
                        let delay = 1000;
                        verseResponse.messages.forEach((msg, index) => {
                            setTimeout(() => {
                                addBotMessage(msg);
                                learningSystem.learnFromResponse(message, msg, feelingResult.emotion);
                            }, delay);
                            delay += 1500;
                        });
                        
                        // Send verse after messages
                        if (verseResponse.sendVerse) {
                            setTimeout(async () => {
                                const verse = await getVerseForEmotion(verseResponse.emotion);
                                if (verse) {
                                    addBotMessage(`Here's a verse I believe will speak to your heart: "${verse.verse}" - ${verse.reference}`, verseResponse.emotion);
                                }
                            }, delay);
                        }
                        
                        currentState = verseResponse.next;
                    });
                } else if (feelingResult.next === "crisisResponse") {
                    conversationContext.crisisDetected = true;
                    const crisisResponse = chatFlow.crisisResponse.processInput();
                    let delay = 1000;
                    crisisResponse.messages.forEach((msg, index) => {
                        setTimeout(() => {
                            addBotMessage(msg);
                            if (index === crisisResponse.messages.length - 1 && crisisResponse.showPastorButton) {
                                setTimeout(() => {
                                    addPastorConnectionButton();
                                }, 500);
                            }
                        }, delay);
                        delay += 1500;
                    });
                    currentState = crisisResponse.next;
                } else if (feelingResult.next === "situationResponse") {
                    const situationResponse = chatFlow.situationResponse.processInput(feelingResult.emotion);
                    let delay = 1000;
                    situationResponse.messages.forEach((msg, index) => {
                        setTimeout(() => {
                            addBotMessage(msg);
                            if (index === situationResponse.messages.length - 1 && situationResponse.showPastorButton) {
                                setTimeout(() => {
                                    addPastorConnectionButton();
                                }, 500);
                            }
                        }, delay);
                        delay += 1500;
                    });
                    currentState = situationResponse.next;
                } else {
                    // Handle other response types
                    setTimeout(() => {
                        addBotMessage("I'm here to listen. Can you tell me more about how you're feeling?");
                    }, 1000);
                    currentState = "conversationContinuation";
                }
            }
            break;

        case "conversationContinuation":
            // Track topics
            if (message.length > 10) {
                conversationContext.topics.push(message.substring(0, 50));
            }
            
            const continuationResult = chatFlow.conversationContinuation.processInput(message);
            let delay = 1000;
            continuationResult.messages.forEach((msg, index) => {
                setTimeout(() => {
                    addBotMessage(msg);
                    // Add pastor button after last message
                    if (index === continuationResult.messages.length - 1 && continuationResult.showPastorButton) {
                        setTimeout(() => {
                            addPastorConnectionButton();
                        }, 500);
                    }
                }, delay);
                delay += 1500;
            });
            currentState = continuationResult.next;
            break;

        case "pastorOfferResponse":
            const pastorResult = chatFlow.pastorOfferResponse.processInput(message);
            let pastorDelay = 1000;
            pastorResult.messages.forEach((msg, index) => {
                setTimeout(() => {
                    addBotMessage(msg);
                    // Add pastor button after last message if needed
                    if (index === pastorResult.messages.length - 1 && pastorResult.showPastorButton) {
                        setTimeout(() => {
                            addPastorConnectionButton();
                        }, 500);
                    }
                }, pastorDelay);
                pastorDelay += 1500;
            });
            currentState = pastorResult.next;
            break;

        default:
            // Just in case we get into an unknown state
            setTimeout(() => {
                addBotMessage("I'm here to help. Is there something specific about our church community you'd like to know?");
            }, 1000);
            currentState = "conversationContinuation";
    }
}

        // Event Listeners
        sendButton.addEventListener("click", processUserInput);
        
        // User typing indicator
        const userTypingIndicator = document.getElementById("user-typing");
        let typingTimer;
        
        userInput.addEventListener("input", function() {
            if (this.value.trim() !== "") {
                userTypingIndicator.classList.add("show");
                
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    userTypingIndicator.classList.remove("show");
                }, 3000);
            } else {
                userTypingIndicator.classList.remove("show");
            }
        });
        
        userInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                userTypingIndicator.classList.remove("show");
                processUserInput();
            }
        });

        // Handle emotion button clicks
        document.querySelectorAll(".emotion-button").forEach(button => {
            button.addEventListener("click", function() {
                const emotion = this.getAttribute("title").toLowerCase();
                userInput.value = `I'm feeling ${emotion}`;
                userInput.focus();
            });
        });

        // Handle quick action buttons
        document.querySelectorAll(".quick-action-btn").forEach(button => {
            button.addEventListener("click", function() {
                const action = this.getAttribute("data-action");
                const actionMessages = {
                    prayer: "I need prayer",
                    verse: "Can you give me a Bible verse?",
                    pastor: "I want to talk to a pastor",
                    info: "What are your service times?"
                };
                
                userInput.value = actionMessages[action];
                processUserInput();
            });
        });

        // Initialize the welcome screen
       // Fix 1: Correct the emotion buttons event listener
document.querySelectorAll(".emotion-button").forEach(button => {
    button.addEventListener("click", function() {
        const emotion = this.getAttribute("title").toLowerCase();
        userInput.value = `I'm feeling ${emotion}`;
        // Focus on input to make it clear we've added text
        userInput.focus();
    });
});

// Fix 2: Ensure proper initialization
initWelcomeScreen();

// Fix 3: Improve the transition logic
function transitionToChatInterface() {
    // Add fade-out animation to welcome screen
    welcomeScreen.classList.add('fade-out');

    // After animation completes, hide welcome screen and show chat interface
    setTimeout(() => {
        welcomeScreen.style.display = 'none';
        chatInterface.style.display = 'flex';
        chatInterface.classList.add('fade-in');

        // Make sure the flex direction is column
        chatInterface.style.flexDirection = 'column';

        // Update chat header with user's name
        chatHeader.textContent = `Church Community Chat with ${userName}`;

        // Initialize chat with personalized greeting
        initChat();
    }, 500); // Match this with the animation duration
}

// Fix 4: Ensure chat interface has proper styling
function ensureProperStyling() {
    // Make sure chat interface has proper layout
    chatInterface.style.display = 'none'; // Initially hidden
    chatInterface.style.flexDirection = 'column';
    chatInterface.style.height = '100%';

    // Make sure messages container takes available space
    chatMessages.style.flex = '1';
    chatMessages.style.overflowY = 'auto';

    // Fix any potential issues with the chat-header
    chatHeader.style.width = '100%';
}

// Call this function on page load
window.addEventListener('load', function() {
    ensureProperStyling();
    learningSystem.loadLearnedData();
});

// Fix 5: Add a function to check if the interface is showing properly
function checkInterfaceVisibility() {
    // If the welcome screen has been submitted but chat interface isn't visible
    if (userName && chatInterface.style.display !== 'flex') {
        console.log("Interface visibility issue detected. Fixing...");
        welcomeScreen.style.display = 'none';
        chatInterface.style.display = 'flex';
        chatInterface.style.flexDirection = 'column';
        chatInterface.style.height = '100%';
    }
}

// Add periodic check for interface visibility (just in case)
setInterval(checkInterfaceVisibility, 2000);

// Function to get a time-appropriate greeting
function getTimeBasedGreeting() {
    const hour = new Date().getHours();

    if (hour >= 5 && hour < 12) {
        return "Good Morning";
    } else if (hour >= 12 && hour < 18) {
        return "Good Afternoon";
    } else {
        return "Good Evening";
    }
}

// Function to update the welcome title with dynamic greeting
function updateWelcomeGreeting() {
    const welcomeTitle = document.getElementById('welcome-title');
    if (welcomeTitle) {
        const greeting = getTimeBasedGreeting();
        const baseText = "Welcome to Faith Connect — we're glad you're here.";
        welcomeTitle.textContent = `${greeting}, ${baseText}`;
        
        // Reset animation
        welcomeTitle.style.animation = 'none';
        welcomeTitle.offsetHeight; // Trigger reflow
        welcomeTitle.style.animation = 'typing 3.5s steps(40, end), blink-caret .75s step-end infinite';
    }
}

// Update greeting when page loads
document.addEventListener('DOMContentLoaded', function() {
    updateWelcomeGreeting();
    // Update greeting every minute to keep it current
    setInterval(updateWelcomeGreeting, 60000);
});

// You can also update this in the transition function
function transitionToChatInterface() {
    // Add fade-out animation to welcome screen
    welcomeScreen.classList.add('fade-out');

    // After animation completes, hide welcome screen and show chat interface
    setTimeout(() => {
        welcomeScreen.style.display = 'none';
        chatInterface.style.display = 'flex';
        chatInterface.classList.add('fade-in');

        // Make sure the flex direction is column
        chatInterface.style.flexDirection = 'column';

        // Update chat header with user's name and time-based greeting
        chatHeader.textContent = `${getTimeBasedGreeting()}, ${userName}`;

        // Initialize chat with personalized greeting
        initChat();
    }, 500); // Match this with the animation duration
}

// If you want to change the initial greeting shown on welcome screen
function initWelcomeScreen() {
    // Update the welcome title with time-based greeting
    updateWelcomeGreeting();

    // Enable/disable start button based on name input
    nameInput.addEventListener('input', function() {
        startChatButton.disabled = !nameInput.value.trim();
    });

    // Handle form submission
    nameForm.addEventListener('submit', function(e) {
        e.preventDefault();
        userName = nameInput.value.trim();
        if (userName) {
            transitionToChatInterface();
        }
    });
}

// Add this after the learningSystem object and before the chatFlow object

// Function to store only emotion analytics (not messages)
async function storeAnalytics(message, emotion, isBotMessage = false) {
    // Only store user emotions, not bot messages or non-emotion messages
    if (isBotMessage || !emotion || emotion === 'null') {
        return true;
    }
    
    try {
        const response = await fetch('/chatbot-analytics/store-message', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                user_id: {{ auth()->id() ?? 'null' }},
                emotion: emotion,
                message: null // Don't store actual message
            })
        });
        
        if (!response.ok) {
            console.error('Failed to store analytics');
            return false;
        }
        
        return true;
    } catch (error) {
        console.error('Error storing analytics:', error);
        return false;
    }
}

// Add currentEmotion variable at the top with other state variables
let currentEmotion = null;
    </script>

@endsection
