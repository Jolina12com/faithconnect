<?php

namespace App\Http\Controllers;

use App\Models\ChatbotAnalytics;
use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ChatbotAnalyticsController extends Controller
{
    public function store(Request $request)
    {
        // Store user data
        $user = ChatUser::updateOrCreate(
            ['session_id' => $request->session_id],
            [
                'name' => $request->user_name,
                'last_active' => now(),
                'total_conversations' => DB::raw('total_conversations + 1')
            ]
        );

        // Store analytics
        ChatbotAnalytics::create([
            'user_id' => $user->id,
            'emotion' => $request->emotion,
            'message' => $request->message,
            'response_type' => $request->response_type
        ]);

        return response()->json(['message' => 'Analytics stored successfully']);
    }

    public function storeMessage(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'emotion' => 'required|string',
                'message' => 'nullable|string'
            ]);

            // Only store emotion and user_id, not the actual message
            $analytics = ChatbotAnalytics::create([
                'user_id' => $validated['user_id'],
                'emotion' => $validated['emotion'],
                'message' => null, // Don't store message content
                'is_bot_message' => false
            ]);
            
            Log::info('Chatbot emotion stored', [
                'user_id' => $validated['user_id'],
                'emotion' => $validated['emotion']
            ]);

            return response()->json([
                'message' => 'Emotion stored successfully',
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            Log::error('Error storing chatbot emotion: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error storing emotion',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAnalytics()
    {
        try {
            $totalEmotions = ChatbotAnalytics::whereNotNull('emotion')->count();
            
            // Top emotions with counts
            $topEmotions = ChatbotAnalytics::whereNotNull('emotion')
                ->select('emotion', DB::raw('count(*) as count'))
                ->groupBy('emotion')
                ->orderByDesc('count')
                ->get()
                ->map(function($item) {
                    return [
                        'emotion' => $item->emotion,
                        'count' => $item->count,
                        'percentage' => 0 // Will calculate below
                    ];
                });

            // Calculate percentages
            if ($totalEmotions > 0) {
                $topEmotions = $topEmotions->map(function($item) use ($totalEmotions) {
                    $item['percentage'] = round(($item['count'] / $totalEmotions) * 100, 1);
                    return $item;
                });
            }

            $activeUsers = User::whereHas('chatbotAnalytics', function($query) {
                $query->where('created_at', '>=', now()->subDay());
            })->count();

            // Recent emotions (without message content)
            $recentEmotions = ChatbotAnalytics::with('user')
                ->whereNotNull('emotion')
                ->latest()
                ->take(10)
                ->get()
                ->map(function($record) {
                    return [
                        'user' => $record->user->first_name ?? 'Anonymous',
                        'emotion' => $record->emotion,
                        'time' => $record->created_at->diffForHumans()
                    ];
                });

            return response()->json([
                'total_emotions' => $totalEmotions,
                'top_emotions' => $topEmotions,
                'active_users' => $activeUsers,
                'recent_emotions' => $recentEmotions
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching chatbot analytics: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error fetching analytics',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
