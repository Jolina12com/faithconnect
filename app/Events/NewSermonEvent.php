<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Sermon;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewSermonEvent implements ShouldBroadcastNow

{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $sermon;

    /**
     * Create a new event instance.
     */
    public function __construct(Sermon $sermon)
    {
        $this->sermon = $sermon;
    }

    public function broadcastOn()
    {
        return new Channel('sermons');
    }

    public function broadcastWith()
    {
        return [
            'type' => 'sermon',
            'id' => $this->sermon->id,
            'title' => $this->sermon->title,
            'message' => 'New sermon: ' . $this->sermon->title,
            'description' => $this->sermon->description,
            'date_preached' => $this->sermon->date_preached,
            'speaker_name' => $this->sermon->speaker_name,
            'duration' => $this->sermon->duration,
            'scripture_reference' => $this->sermon->scripture_reference,
            'created_at' => now()->toDateTimeString()
        ];

    }

}
