<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatbotAnalytics;
use App\Models\User;

class ChatbotAnalyticsSeeder extends Seeder
{
    public function run()
    {
        // Get some users to associate with analytics
        $users = User::take(5)->get();
        
        if ($users->isEmpty()) {
            $this->command->info('No users found. Please run UserSeeder first.');
            return;
        }

        $emotions = ['happy', 'sad', 'angry', 'excited', 'confused', 'grateful', 'worried', 'peaceful'];
        
        // Create sample analytics data
        foreach ($users as $user) {
            for ($i = 0; $i < rand(3, 8); $i++) {
                ChatbotAnalytics::create([
                    'user_id' => $user->id,
                    'emotion' => $emotions[array_rand($emotions)],
                    'message' => null, // Don't store actual messages for privacy
                    'response_type' => 'emotion_detected',
                    'is_bot_message' => false,
                    'created_at' => now()->subDays(rand(0, 30)),
                ]);
            }
        }

        $this->command->info('Chatbot analytics test data created successfully!');
    }
}