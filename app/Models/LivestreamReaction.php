<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LivestreamReaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'room_name',
        'participant_identity',
        'timestamp'
    ];

    protected $casts = [
        'timestamp' => 'datetime'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
