<?php

namespace App\Events;

use App\Models\Event;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewEventPosted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
    }

    public function broadcastOn()
    {
        return new Channel('events');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->event->id,
            'title' => $this->event->title,
            'description' => $this->event->description,
            'event_date' => $this->event->event_date,
            'event_time' => $this->event->event_time,
            'type' => $this->event->event_type,
        ];
    }
}