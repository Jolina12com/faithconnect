<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotAnalytics extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'emotion',
        'response_type',
        'is_bot_message'
    ];

    protected $casts = [
        'is_bot_message' => 'boolean'
    ];

    // Relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
