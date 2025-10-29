<?php

namespace App\Observers;

use Illuminate\Notifications\DatabaseNotification;
use App\Events\NotificationCountChanged;
use Illuminate\Support\Facades\Cache;

class NotificationObserver
{
    public function created(DatabaseNotification $notification)
    {
        $this->clearCacheAndBroadcast($notification->notifiable_id);
    }

    public function updated(DatabaseNotification $notification)
    {
        $this->clearCacheAndBroadcast($notification->notifiable_id);
    }

    public function deleted(DatabaseNotification $notification)
    {
        $this->clearCacheAndBroadcast($notification->notifiable_id);
    }

    private function clearCacheAndBroadcast($userId)
    {
        // Clear notification caches
        Cache::forget("notification_count_{$userId}");
        Cache::forget("recent_notifications_{$userId}_5");
        
        // Get fresh count and broadcast
        $user = \App\Models\User::find($userId);
        if ($user) {
            $count = $user->unreadNotifications->count();
            broadcast(new NotificationCountChanged($userId, $count));
        }
    }
}