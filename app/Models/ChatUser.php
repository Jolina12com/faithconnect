<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $fillable = [
        'name',
        'session_id',
        'last_active',
        'total_conversations',
        'preferences'
    ];

    protected $casts = [
        'preferences' => 'array',
        'last_active' => 'datetime'
    ];
}
