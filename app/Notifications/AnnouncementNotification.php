<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class AnnouncementNotification extends Notification
{
    public $announcement;

    public function __construct($announcement)
    {
        $this->announcement = $announcement;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'announcement',
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'id' => $this->announcement->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'type' => 'announcement',
            'title' => $this->announcement->title,
            'message' => $this->announcement->message,
            'id' => $this->announcement->id,
        ]);
    }
}
