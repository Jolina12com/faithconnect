<?php

namespace App\Events;

use App\Models\LiveStream;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StreamEnded implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $stream;
    public $roomName;

    public function __construct(LiveStream $stream, $roomName)
    {
        $this->stream = $stream;
        $this->roomName = $roomName;
    }

    public function broadcastOn()
    {
        return new Channel('livestream.' . $this->roomName);
    }

    public function broadcastAs()
    {
        return 'stream.ended';
    }

    public function broadcastWith()
    {
        return [
            'stream_id' => $this->stream->id,
            'message' => 'Stream has ended',
            'ended_at' => $this->stream->ended_at,
            'duration' => $this->stream->ended_at->diffInSeconds($this->stream->started_at)
        ];
    }
}