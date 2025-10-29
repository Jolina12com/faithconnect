<?php

namespace App\Observers;

use App\Models\Message;
use Illuminate\Support\Facades\Cache;

class MessageObserver
{
    public function created(Message $message)
    {
        $this->clearChatCaches($message->receiver_id);
    }

    public function updated(Message $message)
    {
        $this->clearChatCaches($message->receiver_id);
        $this->clearChatCaches($message->sender_id);
    }

    public function deleted(Message $message)
    {
        $this->clearChatCaches($message->receiver_id);
        $this->clearChatCaches($message->sender_id);
    }

    private function clearChatCaches($userId)
    {
        Cache::forget("chat_unread_count_{$userId}");
        Cache::forget("chat_unread_counts_{$userId}");
    }
}