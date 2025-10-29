<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StreamViewer extends Model
{
    protected $fillable = [
        'stream_id',
        'user_id',
        'viewer_name',
        'participant_identity',
        'joined_at',
        'left_at',
        'duration_seconds'
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
    ];

    public function stream()
    {
        return $this->belongsTo(LiveStream::class, 'stream_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
