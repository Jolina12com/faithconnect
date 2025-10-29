<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChatMessageReceived implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $receiverId;
    public $unreadCount;
    public $senderName;

    public function __construct($receiverId, $unreadCount, $senderName)
    {
        $this->receiverId = $receiverId;
        $this->unreadCount = $unreadCount;
        $this->senderName = $senderName;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat.' . $this->receiverId);
    }

    public function broadcastAs()
    {
        return 'MessageReceived';
    }

    public function broadcastWith()
    {
        return [
            'unread_count' => $this->unreadCount,
            'sender_name' => $this->senderName
        ];
    }
}