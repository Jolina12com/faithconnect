<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPollOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_id',
        'option_text',
        'option_value',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    /**
     * Get the poll that this option belongs to.
     */
    public function poll(): BelongsTo
    {
        return $this->belongsTo(EventPoll::class, 'poll_id');
    }

    /**
     * Get the responses for this option.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(EventPollResponse::class, 'option_id');
    }

    /**
     * Get the response count for this option.
     */
    public function getResponseCount(): int
    {
        return $this->responses()->count();
    }
}
