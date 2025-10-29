<?php

namespace App\Events;
use App\Models\Message;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;

class NewMessage implements ShouldBroadcastNow
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $sender_id;
    public $receiver_id;

    public function __construct($message)
    {
        $this->message = $message;
        $this->sender_id = $message->sender_id;
        $this->receiver_id = $message->receiver_id;
    }

    public function broadcastOn()
    {
        // Use a private channel that matches what the admin chat is subscribing to
        return new PrivateChannel('chat.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'sender_id' => $this->message->sender_id,
            'receiver_id' => $this->message->receiver_id,
            'message' => $this->message->message,
            'formatted_time' => $this->message->created_at->format('h:i A'),
            'status' => $this->message->status
        ];
    }
}
