<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Events\UserTyping;
use App\Events\MessageRead;
use App\Events\ChatMessageReceived;
use App\Services\ProfanityFilter;

class ChatController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        // If you have a password change middleware, add it here
        if (class_exists('\App\Http\Middleware\CheckPasswordChanged')) {
            $this->middleware('\App\Http\Middleware\CheckPasswordChanged');
        }
    }

    public function index()
    {
        try {
            $users = User::where('id', '!=', Auth::id())->get();
            return view('member.userchat', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error in ChatController@index: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load chat. Please try again later.');
        }
    }

    public function fetchMessages($receiverId)
    {
        // Validate receiver ID
        if (!is_numeric($receiverId) || $receiverId <= 0) {
            return response()->json(['error' => 'Invalid receiver ID'], 400);
        }

        try {
            $messages = Message::where(function ($query) use ($receiverId) {
                    $query->where('sender_id', Auth::id())->where('receiver_id', $receiverId)
                          ->orWhere('sender_id', $receiverId)->where('receiver_id', Auth::id());
                })
                ->with('sender:id,first_name,last_name')
                ->orderBy('created_at', 'asc')
                ->limit(100)
                ->get()
                ->map(function ($msg) {
                    $msg->setAttribute('formatted_time', $msg->created_at->format('h:i A'));
                    return $msg;
                });

            // Batch update for better performance
            Message::where('sender_id', $receiverId)
                ->where('receiver_id', Auth::id())
                ->whereIn('status', ['sent', 'delivered'])
                ->update(['status' => 'read']);

            broadcast(new MessageRead($receiverId, Auth::id()));

            return response()->json($messages);
        } catch (\Exception $e) {
            \Log::error('Error fetching messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch messages'], 500);
        }
    }
    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer|exists:users,id',
            'message' => 'required|string|max:1000',
            'firebase_msg_id' => 'nullable|string'
        ]);

        try {
            // Filter bad words in the message
            $profanityFilter = new ProfanityFilter();
            $filteredMessage = $profanityFilter->filter($request->message);

            // Create the message in your database
            $message = Message::create([
                'sender_id' => auth()->id(),
                'receiver_id' => $request->receiver_id,
                'message' => $filteredMessage,
                'firebase_msg_id' => $request->firebase_msg_id,
                'status' => 'sent'
            ]);

            // Broadcast new message to receiver
            $unreadCount = Message::where('receiver_id', $request->receiver_id)
                                 ->where('status', 'sent')
                                 ->count();
            $senderName = auth()->user()->first_name . ' ' . auth()->user()->last_name;
            broadcast(new ChatMessageReceived($request->receiver_id, $unreadCount, $senderName));

            return response()->json(['status' => 'success', 'message_id' => $message->id]);
        } catch (\Exception $e) {
            \Log::error('Error sending message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to send message'], 500);
        }
    }
    

    public function getBibleVerses(Request $request)
    {
        $category = $request->input('category', 'encouragement');
        
        try {
            // Use Bible API to fetch verses
            $verses = $this->fetchVersesFromAPI($category);
            return response()->json(['verses' => $verses]);
        } catch (\Exception $e) {
            \Log::error('Bible API error: ' . $e->getMessage());
            return response()->json(['verses' => $this->getFallbackVerses($category)]);
        }
    }
    
    private function fetchVersesFromAPI($category)
    {
        $verseReferences = [
            'encouragement' => ['Joshua 1:9', 'Philippians 4:13', 'Isaiah 41:10', 'Psalm 46:1', '2 Corinthians 12:9'],
            'peace' => ['John 14:27', 'Philippians 4:6-7', 'Psalm 23:4', 'Isaiah 26:3', 'Romans 15:13'],
            'love' => ['1 Corinthians 13:4-7', 'John 15:12', '1 John 4:19', 'Romans 8:38-39', 'John 3:16'],
            'guidance' => ['Proverbs 3:5-6', 'Psalm 32:8', 'Jeremiah 29:11', 'Isaiah 30:21', 'Psalm 119:105'],
            'prayer' => ['Matthew 6:9-13', '1 Thessalonians 5:16-18', 'James 5:16', 'Philippians 4:6', 'Matthew 7:7'],
            'praise' => ['Psalm 100:4', 'Psalm 150:6', 'Psalm 95:1', 'Psalm 34:1', 'Hebrews 13:15'],
            'faith' => ['Hebrews 11:1', 'Romans 10:17', 'Mark 11:24', '2 Corinthians 5:7', 'Ephesians 2:8'],
            'hope' => ['Romans 15:13', 'Jeremiah 29:11', 'Psalm 42:11', 'Romans 8:28', 'Hebrews 6:19'],
            'forgiveness' => ['1 John 1:9', 'Ephesians 4:32', 'Matthew 6:14', 'Colossians 3:13', 'Psalm 103:12'],
            'strength' => ['Philippians 4:13', 'Isaiah 40:31', '2 Corinthians 12:9', 'Psalm 28:7', 'Nehemiah 8:10'],
            'comfort' => ['2 Corinthians 1:3-4', 'Psalm 23:4', 'Matthew 11:28', 'Isaiah 41:10', 'Psalm 34:18'],
            'wisdom' => ['James 1:5', 'Proverbs 2:6', 'Proverbs 3:13', 'Ecclesiastes 7:12', '1 Corinthians 1:30'],
            'healing' => ['Jeremiah 17:14', 'Psalm 103:2-3', 'Isaiah 53:5', 'James 5:14-15', '3 John 1:2'],
            'protection' => ['Psalm 91:11', 'Proverbs 18:10', 'Psalm 121:7-8', 'Isaiah 54:17', 'Psalm 46:1'],
            'thanksgiving' => ['1 Thessalonians 5:18', 'Psalm 100:4', 'Colossians 3:17', 'Ephesians 5:20', 'Psalm 107:1']
        ];
        
        $references = $verseReferences[$category] ?? $verseReferences['encouragement'];
        $verses = [];
        
        foreach ($references as $ref) {
            try {
                $encodedRef = str_replace(' ', '+', $ref);
                $response = file_get_contents("https://bible-api.com/{$encodedRef}");
                $data = json_decode($response, true);
                
                if ($data && isset($data['text'])) {
                    $verses[] = [
                        'reference' => $data['reference'] ?? $ref,
                        'text' => trim($data['text']),
                        'translation' => $data['translation_name'] ?? 'KJV'
                    ];
                }
            } catch (\Exception $e) {
                // Skip failed requests
                continue;
            }
        }
        
        return $verses;
    }
    
    private function getFallbackVerses($category)
    {
        $fallbackVerses = [
            'encouragement' => [
                ['reference' => 'Joshua 1:9', 'text' => 'Be strong and courageous. Do not be afraid; do not be discouraged, for the Lord your God will be with you wherever you go.'],
                ['reference' => 'Philippians 4:13', 'text' => 'I can do all things through Christ who strengthens me.']
            ],
            'peace' => [
                ['reference' => 'John 14:27', 'text' => 'Peace I leave with you; my peace I give you.']
            ]
        ];
        
        return $fallbackVerses[$category] ?? $fallbackVerses['encouragement'];
    }
    
    public function filterMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);
        
        try {
            $message = $request->input('message');
            $profanityFilter = new ProfanityFilter();
            $filteredMessage = $profanityFilter->filter($message);
            
            return response()->json(['filtered_message' => $filteredMessage]);
        } catch (\Exception $e) {
            \Log::error('Error filtering message: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to filter message'], 500);
        }
    }
    
    public function searchVerses(Request $request)
    {
        $request->validate([
            'query' => 'required|string|max:100'
        ]);
        
        $query = strtolower(trim($request->input('query')));
        
        if (empty($query)) {
            return response()->json(['verses' => []]);
        }
        
        try {
            // Search by reference first
            if (preg_match('/\b(\d?\s?\w+)\s+(\d+)/', $query, $matches)) {
                $reference = trim($matches[0]);
                $verses = $this->searchByReference($reference);
                if (!empty($verses)) {
                    return response()->json(['verses' => $verses]);
                }
            }
            
            // Search by topic/keyword
            $allVerses = [];
            $categories = ['encouragement', 'peace', 'love', 'guidance', 'prayer', 'praise', 'faith', 'hope', 'forgiveness', 'strength', 'comfort', 'wisdom', 'healing', 'protection', 'thanksgiving'];
            
            foreach ($categories as $category) {
                if (strpos($category, $query) !== false || strpos($query, $category) !== false) {
                    $categoryVerses = $this->fetchVersesFromAPI($category);
                    $allVerses = array_merge($allVerses, $categoryVerses);
                }
            }
            
            // Remove duplicates
            $uniqueVerses = [];
            foreach ($allVerses as $verse) {
                $uniqueVerses[$verse['reference']] = $verse;
            }
            
            return response()->json(['verses' => array_values($uniqueVerses)]);
            
        } catch (\Exception $e) {
            \Log::error('Verse search error: ' . $e->getMessage());
            return response()->json(['verses' => []]);
        }
    }
    
    private function searchByReference($reference)
    {
        try {
            $encodedRef = str_replace(' ', '+', $reference);
            $response = file_get_contents("https://bible-api.com/{$encodedRef}");
            $data = json_decode($response, true);
            
            if ($data && isset($data['text'])) {
                return [[
                    'reference' => $data['reference'] ?? $reference,
                    'text' => trim($data['text']),
                    'translation' => $data['translation_name'] ?? 'KJV'
                ]];
            }
        } catch (\Exception $e) {
            // Continue to topic search
        }
        return [];
    }
    
    public function getMoreVerses(Request $request)
    {
        $category = $request->input('category', 'encouragement');
        $offset = $request->input('offset', 5);
        
        try {
            $moreReferences = [
                'encouragement' => ['Romans 8:28', 'Deuteronomy 31:6', '1 Peter 5:7', 'Psalm 27:1', 'Isaiah 40:29'],
                'peace' => ['Matthew 11:28', 'Psalm 29:11', 'John 16:33', 'Colossians 3:15', 'Isaiah 55:11'],
                'love' => ['Ephesians 3:17-19', 'Romans 5:8', '1 John 3:1', 'Psalm 136:1', 'Jeremiah 31:3'],
                'guidance' => ['Psalm 25:9', 'Isaiah 42:16', 'Proverbs 16:9', 'Psalm 37:23', 'James 1:5'],
                'prayer' => ['Romans 8:26', 'Colossians 4:2', '1 Peter 5:7', 'Luke 18:1', 'Psalm 145:18'],
                'praise' => ['Psalm 103:1', 'Psalm 146:2', '1 Chronicles 16:25', 'Psalm 9:1', 'Revelation 4:11'],
                'faith' => ['Romans 1:17', 'Galatians 2:20', 'James 2:17', '1 Peter 1:7', 'Matthew 17:20'],
                'hope' => ['1 Peter 1:3', 'Psalm 130:7', 'Lamentations 3:22-23', 'Isaiah 40:31', 'Romans 5:3-4'],
                'forgiveness' => ['Acts 3:19', 'Isaiah 1:18', 'Micah 7:18', 'Romans 3:23-24', 'Hebrews 8:12'],
                'strength' => ['Exodus 15:2', 'Psalm 46:1', '1 Corinthians 16:13', 'Ephesians 6:10', 'Habakkuk 3:19'],
                'comfort' => ['Romans 15:4', 'Psalm 119:76', 'Isaiah 66:13', 'John 14:1', 'Revelation 21:4'],
                'wisdom' => ['Proverbs 9:10', 'Colossians 2:3', 'Psalm 111:10', 'Proverbs 27:5', 'Daniel 2:20'],
                'healing' => ['Exodus 15:26', 'Psalm 147:3', 'Matthew 4:23', '1 Peter 2:24', 'Malachi 4:2'],
                'protection' => ['Psalm 23:4', 'Nahum 1:7', 'Psalm 32:7', '2 Thessalonians 3:3', 'Psalm 125:2'],
                'thanksgiving' => ['Psalm 118:1', 'Philippians 4:6', '1 Chronicles 16:34', 'Psalm 136:26', 'Daniel 2:23']
            ];
            
            $references = $moreReferences[$category] ?? $moreReferences['encouragement'];
            $verses = [];
            
            foreach ($references as $ref) {
                try {
                    $encodedRef = str_replace(' ', '+', $ref);
                    $response = file_get_contents("https://bible-api.com/{$encodedRef}");
                    $data = json_decode($response, true);
                    
                    if ($data && isset($data['text'])) {
                        $verses[] = [
                            'reference' => $data['reference'] ?? $ref,
                            'text' => trim($data['text']),
                            'translation' => $data['translation_name'] ?? 'KJV'
                        ];
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }
            
            return response()->json(['verses' => $verses]);
            
        } catch (\Exception $e) {
            \Log::error('More verses error: ' . $e->getMessage());
            return response()->json(['verses' => []]);
        }
    }



    public function getUnreadCount()
    {
        $userId = Auth::id();
        $cacheKey = "chat_unread_count_{$userId}";
        
        // Cache for 1 minute
        $unreadCount = \Cache::remember($cacheKey, 60, function () use ($userId) {
            return Message::where('receiver_id', $userId)
                          ->where('status', 'sent')
                          ->count();
        });

        return response()->json(['unread' => $unreadCount]);
    }

    public function userTyping(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
        ]);
        
        try {
            $user = Auth::user();
            // Broadcast via the UserTyping event
            broadcast(new UserTyping(Auth::id(), $request->receiver_id));
            
            return response()->json([
                'success' => true, 
                'message' => 'Typing indicator sent'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Failed to send typing indicator: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // Add the mark-read endpoint
    public function markAsRead(Request $request)
{
    $request->validate([
        'sender_id' => 'required|exists:users,id',
    ]);
    
    try {
        $updatedCount = Message::where('receiver_id', Auth::id())
            ->where('sender_id', $request->sender_id)
            ->whereIn('status', ['sent', 'delivered']) // Check for both statuses
            ->update(['status' => 'read']);
            
        if ($updatedCount > 0) {
            broadcast(new MessageRead(Auth::id(), $request->sender_id));
        }
        
        return response()->json(['success' => true, 'updated' => $updatedCount]);
    } catch (\Exception $e) {
        return response()->json(['success' => false, 'message' => 'Failed to mark messages as read'], 500);
    }
}

    // Add delete message functionality
    public function deleteMessage(Request $request)
    {
        $request->validate([
            'message_id' => 'required',
            'delete_type' => 'required|in:self,everyone'
        ]);
        
        try {
            $messageId = $request->message_id;
            $deleteType = $request->delete_type;
            
            // Find the message
            $message = Message::where('firebase_msg_id', $messageId)->first();
            
            // If message not found, it may only exist in Firebase
            if (!$message) {
                return response()->json([
                    'success' => true,
                    'message' => 'Message deleted in Firebase only'
                ]);
            }
            
            // Ensure the user is the sender if deleting for everyone
            if ($deleteType === 'everyone' && $message->sender_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only delete your own messages for everyone'
                ], 403);
            }
            
            if ($deleteType === 'everyone') {
                // Update the message to indicate it was deleted
                $message->update([
                    'message' => '⚠️ This message was deleted',
                    'deleted' => true,
                    'deleted_by' => Auth::id(),
                    'deleted_at' => now(),
                    'original_message' => $message->message // Store original message for audit
                ]);
            } else {
                // Delete only for the current user
                // We don't actually delete from DB, just mark that it's hidden for this user
                // The actual hiding happens in Firebase
                $message->update([
                    'hidden_for_' . Auth::id() => true,
                    'hidden_at_' . Auth::id() => now()
                ]);
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Message deleted successfully',
                'delete_type' => $deleteType
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting message: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Failed to delete message: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getUsers()
    {
        try {
            $currentUserId = Auth::id();
            
            $users = User::where('id', '!=', $currentUserId)
                ->select('id', 'first_name', 'last_name', 'profile_picture')
                ->withCount(['sentMessages as conversation_count' => function($query) use ($currentUserId) {
                    $query->where(function($q) use ($currentUserId) {
                        $q->where('receiver_id', $currentUserId)
                          ->orWhere('sender_id', $currentUserId);
                    });
                }])
                ->orderByDesc('conversation_count')
                ->get()
                ->map(function($user) {
                    return [
                        'id' => $user->id,
                        'name' => $user->first_name . ' ' . $user->last_name,
                        'first_name' => $user->first_name,
                        'profile_picture' => $user->profile_picture,
                        'online' => false,
                        'has_conversation' => $user->conversation_count > 0
                    ];
                });
            
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error fetching users: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load users'], 500);
        }
    }
    
    /**
     * Display the simple chat interface.
     *
     * @return \Illuminate\View\View
     */
    public function simpleChat()
    {
        try {
            $users = User::where('id', '!=', Auth::id())->get();
            return view('member.chat-simple', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error in ChatController@simpleChat: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load chat. Please try again later.');
        }
    }
    
    /**
     * Display a very basic chat test interface.
     *
     * @return \Illuminate\View\View
     */
    public function basicChat()
    {
        try {
            $users = User::where('id', '!=', Auth::id())->get();
            return view('member.chat-basic', compact('users'));
        } catch (\Exception $e) {
            \Log::error('Error in ChatController@basicChat: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getUnreadCounts()
    {
        $userId = auth()->id();
        $cacheKey = "chat_unread_counts_{$userId}";
        
        // Cache for 1 minute
        $unreadCounts = \Cache::remember($cacheKey, 60, function () use ($userId) {
            return \App\Models\Message::where('receiver_id', $userId)
                ->where('status', '!=', 'read')
                ->selectRaw('sender_id, COUNT(*) as count')
                ->groupBy('sender_id')
                ->pluck('count', 'sender_id')
                ->toArray();
        });
        
        return response()->json($unreadCounts);
    }

    public function getUsersWithLastMessages()
    {
        try {
            $user = auth()->user();
            
            // Get users with their last messages and unread counts in fewer queries
            $users = User::where('id', '!=', $user->id)
                ->select('id', 'first_name', 'last_name', 'profile_picture')
                ->with([
                    'sentMessages' => function($query) use ($user) {
                        $query->where('receiver_id', $user->id)
                              ->latest()
                              ->limit(1);
                    },
                    'receivedMessages' => function($query) use ($user) {
                        $query->where('sender_id', $user->id)
                              ->latest()
                              ->limit(1);
                    }
                ])
                ->get()
                ->map(function($otherUser) use ($user) {
                    // Get the most recent message between users
                    $lastSent = $otherUser->sentMessages->first();
                    $lastReceived = $otherUser->receivedMessages->first();
                    
                    $lastMessage = null;
                    if ($lastSent && $lastReceived) {
                        $lastMessage = $lastSent->created_at > $lastReceived->created_at ? $lastSent : $lastReceived;
                    } else {
                        $lastMessage = $lastSent ?: $lastReceived;
                    }
                    
                    $otherUser->last_message = $lastMessage;
                    $otherUser->last_message_time = $lastMessage ? $lastMessage->created_at : null;
                    
                    // Get unread count
                    $otherUser->unread_count = Message::where('sender_id', $otherUser->id)
                        ->where('receiver_id', $user->id)
                        ->where('status', '!=', 'read')
                        ->count();
                        
                    return $otherUser;
                })
                ->sortByDesc('last_message_time')
                ->values();
            
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error getting users with messages: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load users'], 500);
        }
    }

    public function getUserStatus($userId)
    {
        if (!is_numeric($userId) || $userId <= 0) {
            return response()->json(['error' => 'Invalid user ID'], 400);
        }
        
        try {
            $user = User::find($userId);
            if (!$user) {
                return response()->json(['online' => false, 'last_seen' => null]);
            }
            
            return response()->json([
                'online' => $user->is_online ?? false,
                'last_seen' => $user->last_seen_at
            ]);
        } catch (\Exception $e) {
            \Log::error('Error getting user status: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to get user status'], 500);
        }
    }

    public function updateUserStatus(Request $request)
    {
        $user = Auth::user();
        $user->update([
            'is_online' => $request->input('online', true),
            'last_seen_at' => now()
        ]);
        
        return response()->json(['success' => true]);
    }
}
