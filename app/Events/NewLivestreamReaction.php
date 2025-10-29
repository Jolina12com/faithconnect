<?php

namespace App\Events;

use App\Models\LivestreamReaction;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewLivestreamReaction implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public LivestreamReaction $reaction
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('livestream.' . $this->reaction->room_name),
        ];
    }

    public function broadcastAs(): string
    {
        return 'new.reaction';
    }

    public function broadcastWith(): array
    {
        return [
            'id' => $this->reaction->id,
            'type' => $this->reaction->type,
            'user' => [
                'id' => $this->reaction->user->id,
                'name' => $this->reaction->user->name,
            ],
            'created_at' => $this->reaction->created_at->toDateTimeString(),
        ];
    }
}
