<?php

namespace App\Events;

use App\Models\LiveStream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamFailed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;

    public function __construct(LiveStream $stream)
    {
        $this->stream = $stream;
    }

    public function broadcastOn()
    {
        return new Channel('livestream.' . $this->stream->room_name);
    }

    public function broadcastAs()
    {
        return 'stream.failed';
    }

    public function broadcastWith()
    {
        return [
            'stream_id' => $this->stream->id,
            'message' => 'Stream connection lost'
        ];
    }
}