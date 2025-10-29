<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationCountChanged implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $userId;
    public $count;
    public $message;

    public function __construct($userId, $count, $message = null)
    {
        $this->userId = $userId;
        $this->count = $count;
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('notifications.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'NotificationCountChanged';
    }

    public function broadcastWith()
    {
        return [
            'count' => $this->count,
            'message' => $this->message
        ];
    }
}