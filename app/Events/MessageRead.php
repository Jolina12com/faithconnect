<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageRead implements ShouldBroadcast
{
    use SerializesModels;

    public $receiver_id;
    public $sender_id;

    public function __construct($receiver_id, $sender_id)
    {
        $this->receiver_id = $receiver_id;
        $this->sender_id = $sender_id;
    }

    public function broadcastOn()
    {
        return new Channel("chat.{$this->sender_id}");
    }

    public function broadcastAs()
    {
        return 'MessageRead';
    }
}
