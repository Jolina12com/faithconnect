<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventPoll extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'deadline',
        'allow_comments',
        'notify_responses',
    ];

    protected $casts = [
        'deadline' => 'date',
        'allow_comments' => 'boolean',
        'notify_responses' => 'boolean',
    ];

    /**
     * Get the event that owns the poll.
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the options for this poll.
     */
    public function options(): HasMany
    {
        return $this->hasMany(EventPollOption::class, 'poll_id');
    }

    /**
     * Get the responses for this poll.
     */
    public function responses(): HasMany
    {
        return $this->hasMany(EventPollResponse::class, 'poll_id');
    }

    /**
     * Get counts for each option.
     */
    public function getOptionCounts(): array
    {
        $counts = [];
        $options = $this->options()->get();
        
        foreach ($options as $option) {
            $counts[$option->option_value] = [
                'text' => $option->option_text,
                'count' => $this->responses()->where('option_id', $option->id)->count(),
            ];
        }
        
        return $counts;
    }

    /**
     * Calculate response rate (if organization has members).
     */
    public function getResponseRate(): ?float
    {
        // Assuming you have a way to get total member count
        // You might need to adjust this logic based on your application structure
        $totalMemberCount = \App\Models\User::count(); 
        
        if ($totalMemberCount > 0) {
            $responseCount = $this->responses()->count();
            return ($responseCount / $totalMemberCount) * 100;
        }
        
        return null;
    }

    /**
     * Check if deadline has passed.
     */
    public function isDeadlinePassed(): bool
    {
        if (!$this->deadline) {
            return false;
        }
        
        return $this->deadline->isPast();
    }
}
