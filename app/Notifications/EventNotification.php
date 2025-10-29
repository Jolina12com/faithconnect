<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class EventNotification extends Notification
{
    use Queueable;

    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'event',
            'title' => $this->event->title,
            'message' => "New event: {$this->event->title} scheduled for {$this->event->event_date}",
            'id' => $this->event->id,
            'event_type' => $this->event->event_type,
            'event_date' => $this->event->event_date,
            'event_time' => $this->event->event_time,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'event',
            'title' => $this->event->title,
            'message' => "New event: {$this->event->title} scheduled for {$this->event->event_date}",
            'id' => $this->event->id,
            'event_type' => $this->event->event_type,
            'event_date' => $this->event->event_date,
            'event_time' => $this->event->event_time,
            'created_at' => now()->toDateTimeString(),
        ]);
    }
}