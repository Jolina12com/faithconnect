<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;
use App\Models\Sermon;
use Illuminate\Support\Str;

class NewSermonNotification extends Notification 
{
    use Queueable;

    public $sermon;

    public function __construct(Sermon $sermon)
    {
        $this->sermon = $sermon;
        $this->afterCommit = true; // Only send notification after database transaction is committed
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    protected function getData()
    {
        return [
            'type' => 'sermon',
            'title' => $this->sermon->title,
            'message' => 'New sermon: ' . $this->sermon->title,
            'description' => Str::limit($this->sermon->description, 100),
            'date_preached' => $this->sermon->date_preached ? $this->sermon->date_preached->toDateTimeString() : null,
            'speaker_name' => $this->sermon->speaker_name,
            'duration' => $this->sermon->duration,
            'scripture_reference' => $this->sermon->scripture_reference,
            'series_id' => $this->sermon->series_id,
            'id' => $this->sermon->id,
            'thumbnail_path' => $this->sermon->thumbnail_path,
            'created_at' => now()->toDateTimeString()
        ];
    }

    public function toDatabase($notifiable)
    {
        return $this->getData();
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage($this->getData());
    }



    public function toArray($notifiable)
    {
        return $this->getData();
    }
}

