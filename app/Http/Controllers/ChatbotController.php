<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\ChatbotAnalytics;
use App\Services\ProfanityFilter;
use App\Models\Event;
use App\Models\Announcement;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request)
    {
        $message = $request->input('message');
        $userId = auth()->id();
        
        // Filter profanity
        $profanityFilter = new ProfanityFilter();
        $filteredMessage = $profanityFilter->filter($message);
        
        // Return filtered message for frontend
        if ($request->has('filter_only')) {
            return response()->json(['filtered_message' => $filteredMessage]);
        }
        
        // Detect sentiment
        $sentiment = $this->detectSentiment($message);
        
        // Store user message
        ChatbotAnalytics::create([
            'user_id' => $userId,
            'message' => $filteredMessage,
            'emotion' => $sentiment,
            'is_bot_message' => false
        ]);
        
        // Check for prayer request
        if ($this->isPrayerRequest($message)) {
            return $this->handlePrayerRequest($userId, $sentiment);
        }
        
        // Check for church info
        if ($this->isChurchInfoRequest($message)) {
            return $this->handleChurchInfo($sentiment);
        }
        
        // Check if user wants to connect to admin/pastor
        if ($this->wantsAdminConnection($message)) {
            return $this->handleAdminConnection($userId, $sentiment);
        }
        
        // Check if asking for Bible verse
        if ($this->isBibleVerseRequest($message)) {
            return $this->handleBibleVerseRequest($message, $userId, $sentiment);
        }
        
        // Default empathetic response
        $response = $this->getEmpatheticResponse($message, $sentiment);
        
        ChatbotAnalytics::create([
            'user_id' => $userId,
            'message' => $response,
            'response_type' => 'general',
            'is_bot_message' => true
        ]);
        
        return response()->json(['response' => $response, 'sentiment' => $sentiment]);
    }
    
    private function getBibleVerseData($reference)
    {
        try {
            $encodedRef = str_replace(' ', '+', $reference);
            $response = Http::get("https://bible-api.com/{$encodedRef}");
            
            if ($response->successful()) {
                $data = $response->json();
                return [
                    'reference' => $data['reference'],
                    'text' => trim($data['text']),
                    'translation' => $data['translation_name'] ?? 'KJV'
                ];
            }
        } catch (\Exception $e) {
            return null;
        }
        return null;
    }
    
    public function getMoreVerses(Request $request)
    {
        $topic = $request->input('topic', 'encouragement');
        
        $versesByTopic = [
            'encouragement' => ['joshua 1:9', 'philippians 4:13', 'isaiah 41:10', 'deuteronomy 31:6'],
            'peace' => ['john 14:27', 'philippians 4:6-7', 'psalm 23:4', 'isaiah 26:3'],
            'comfort' => ['psalm 34:18', '2 corinthians 1:3-4', 'matthew 11:28', 'psalm 147:3'],
            'strength' => ['isaiah 40:31', 'psalm 46:1', 'ephesians 6:10', '2 corinthians 12:9'],
            'hope' => ['jeremiah 29:11', 'romans 15:13', 'psalm 42:11', 'hebrews 11:1']
        ];
        
        $verses = $versesByTopic[$topic] ?? $versesByTopic['encouragement'];
        $results = [];
        
        foreach ($verses as $ref) {
            $data = $this->getBibleVerseData($ref);
            if ($data) $results[] = $data;
        }
        
        return response()->json(['verses' => $results]);
    }
    
    private function extractVerseTopic($message)
    {
        $message = strtolower($message);
        if (stripos($message, 'peace') !== false) return 'peace';
        if (stripos($message, 'love') !== false) return 'love';
        if (stripos($message, 'comfort') !== false) return 'comfort';
        if (stripos($message, 'strength') !== false) return 'strength';
        if (stripos($message, 'hope') !== false) return 'hope';
        return 'encouragement';
    }
    
    private function isBibleVerseRequest($message)
    {
        $keywords = ['verse', 'bible', 'scripture', 'john', 'matthew', 'psalm', 'genesis'];
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) {
                return true;
            }
        }
        return false;
    }
    
    private function extractVerseReference($message)
    {
        // Extract verse reference pattern (e.g., "John 3:16")
        preg_match('/([1-3]?\s?[a-z]+)\s*(\d+):?(\d+)?(-\d+)?/i', $message, $matches);
        
        if (!empty($matches)) {
            $ref = trim($matches[0]);
            // Ensure proper format: "book chapter:verse"
            return preg_replace('/\s+/', ' ', $ref);
        }
        
        return 'john 3:16'; // Default verse
    }
    
    private function wantsAdminConnection($message)
    {
        $keywords = ['admin', 'pastor', 'talk to someone', 'human', 'person', 'staff', 'priest', 'minister', 'connect me'];
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) return true;
        }
        return false;
    }
    
    private function detectSentiment($message)
    {
        $message = strtolower($message);
        
        $sad = ['sad', 'depressed', 'lonely', 'hurt', 'pain', 'cry', 'broken', 'lost', 'hopeless'];
        $happy = ['happy', 'joy', 'blessed', 'grateful', 'thankful', 'excited', 'wonderful', 'amazing'];
        $anxious = ['worried', 'anxious', 'scared', 'afraid', 'nervous', 'stress', 'fear'];
        $angry = ['angry', 'mad', 'frustrated', 'upset', 'annoyed'];
        
        foreach ($sad as $word) if (stripos($message, $word) !== false) return 'sad';
        foreach ($happy as $word) if (stripos($message, $word) !== false) return 'happy';
        foreach ($anxious as $word) if (stripos($message, $word) !== false) return 'anxious';
        foreach ($angry as $word) if (stripos($message, $word) !== false) return 'angry';
        
        return 'neutral';
    }
    
    private function getEmpatheticResponse($message, $sentiment)
    {
        $responses = [
            'sad' => "I'm sorry you're feeling this way. 💙 Remember, God is close to the brokenhearted. Would you like me to share a comforting Bible verse?",
            'happy' => "That's wonderful! 😊 I'm so glad to hear that! God is good! How else can I help you today?",
            'anxious' => "I understand you're feeling worried. 🕊️ God tells us not to be anxious. Would you like a verse about peace?",
            'angry' => "I hear you. Take a deep breath. 🙏 God can help us through difficult emotions. Can I share a calming verse?",
            'neutral' => "Hello! 👋 How can I help you today? I can share Bible verses, prayer support, or church information."
        ];
        
        return $responses[$sentiment] ?? $responses['neutral'];
    }
    
    private function isPrayerRequest($message)
    {
        $keywords = ['pray', 'prayer', 'pray for', 'need prayer', 'prayer request'];
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) return true;
        }
        return false;
    }
    
    private function handlePrayerRequest($userId, $sentiment)
    {
        $response = $sentiment === 'sad' 
            ? "🙏 I'm lifting you up in prayer right now. You're not alone. Would you like to share your prayer request with our pastor?"
            : "🙏 I'd be honored to pray for you. Would you like to share your prayer request with our pastor?";
        
        ChatbotAnalytics::create([
            'user_id' => $userId,
            'message' => $response,
            'response_type' => 'prayer_request',
            'is_bot_message' => true
        ]);
        
        return response()->json([
            'response' => $response,
            'show_prayer_form' => true
        ]);
    }
    
    private function isChurchInfoRequest($message)
    {
        $keywords = ['service', 'schedule', 'event', 'church', 'when', 'time', 'announcement'];
        foreach ($keywords as $keyword) {
            if (stripos($message, $keyword) !== false) return true;
        }
        return false;
    }
    
    private function handleChurchInfo($sentiment)
    {
        $events = Event::where('event_date', '>=', now())->orderBy('event_date')->take(3)->get();
        $announcements = Announcement::where('published_at', '<=', now())->orderByDesc('published_at')->take(2)->get();
        
        $response = "⛪ Here's what's happening at our church:\n\n";
        
        if ($events->count() > 0) {
            $response .= "📅 Upcoming Events:\n";
            foreach ($events as $event) {
                $response .= "• {$event->title} - " . date('M d, Y', strtotime($event->event_date)) . "\n";
            }
        }
        
        if ($announcements->count() > 0) {
            $response .= "\n📢 Latest Announcements:\n";
            foreach ($announcements as $announcement) {
                $response .= "• {$announcement->title}\n";
            }
        }
        
        $response .= "\n🕐 Regular Services: Sunday 9 AM & 6 PM";
        
        return response()->json(['response' => $response]);
    }
    
    private function handleAdminConnection($userId, $sentiment)
    {
        $response = $sentiment === 'sad' || $sentiment === 'anxious'
            ? "I understand you need to talk to someone. 💙 Let me connect you with our pastor right away."
            : "I'll connect you with our pastor. Please wait a moment...";
        
        ChatbotAnalytics::create([
            'user_id' => $userId,
            'message' => $response,
            'response_type' => 'admin_connect',
            'is_bot_message' => true
        ]);
        
        return response()->json([
            'response' => $response,
            'connect_to_admin' => true
        ]);
    }
    
    private function handleBibleVerseRequest($message, $userId, $sentiment)
    {
        $verse = $this->extractVerseReference($message);
        $bibleData = $this->getBibleVerseData($verse);
        
        if (!$bibleData) {
            return response()->json(['response' => "Sorry, I couldn't find that verse. Try format like 'John 3:16'"]);
        }
        
        $response = "📖 {$bibleData['reference']}\n\n\"{$bibleData['text']}\"";
        
        ChatbotAnalytics::create([
            'user_id' => $userId,
            'message' => $response,
            'response_type' => 'bible_verse',
            'is_bot_message' => true
        ]);
        
        return response()->json([
            'response' => $response,
            'show_more_verses' => true,
            'verse_topic' => $this->extractVerseTopic($message)
        ]);
    }
    
    public function filterProfanity(Request $request)
    {
        $message = $request->input('message');
        
        // Get profanity words from en.txt file
        $filePath = storage_path('app/profanity/en.txt');
        
        if (!file_exists($filePath)) {
            return response()->json(['filtered_message' => $message]);
        }
        
        $profanityWords = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $filteredMessage = $message;
        
        foreach ($profanityWords as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
                $filteredMessage = preg_replace($pattern, str_repeat('*', strlen($word)), $filteredMessage);
            }
        }
        
        return response()->json(['filtered_message' => $filteredMessage]);
    }
}
