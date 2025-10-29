<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventPollResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_id',
        'user_id',
        'comment',
    ];

    /**
     * Get the poll that this response belongs to.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(EventPoll::class, 'poll_id');
    }

    /**
     * Get the option that was selected in this response.
     */
    public function option(): BelongsTo
    {
        return $this->belongsTo(EventPollOption::class, 'option_id');
    }

    /**
     * Get the user who submitted this response.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}