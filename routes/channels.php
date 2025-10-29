<?php
// routes/channels.php
use Illuminate\Support\Facades\Broadcast;
use App\Models\Message;

// Chat channels
Broadcast::channel('chat.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Notification channels
Broadcast::channel('notifications.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// Public channels
Broadcast::channel('events', function ($user) {
    return auth()->check();
});

Broadcast::channel('sermons', function ($user) {
    return auth()->check();
});
