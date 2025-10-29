<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use App\Events\NotificationCountChanged;


class NotificationController extends Controller
{
    /**
     * Display all notifications for the authenticated user
     */
    public function index()
    {
        $user = Auth::user();
        
        return view('member.notifications', [
            'unreadNotifications' => $user->unreadNotifications,
            'readNotifications' => $user->readNotifications
        ]);
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->unreadNotifications()->find($id);
        
        if ($notification) {
            $notification->markAsRead();
            
            // Broadcast updated count
            $newCount = Auth::user()->unreadNotifications->count();
            broadcast(new NotificationCountChanged(Auth::id(), $newCount));
            
            return back()->with('success', 'Notification marked as read.');
        }
        
        return back()->with('error', 'Notification not found.');
    }

    /**
     * Delete a specific notification
     */
    public function destroy($id)
    {
        $user = Auth::user();
        
        // Try to find in both read and unread notifications
        $notification = $user->notifications()->find($id);
        
        if ($notification) {
            $notification->delete();
            return back()->with('success', 'Notification deleted successfully.');
        }
        
        return back()->with('error', 'Notification not found.');
    }

    /**
     * Mark notification as read and redirect to the appropriate page
     */
    public function markAsReadAndRedirect($id)
    {
        $notification = Auth::user()->unreadNotifications()->find($id);
        
        if (!$notification) {
            // If not found in unread, check if it's already read
            $notification = Auth::user()->readNotifications()->find($id);
        }
        
        if (!$notification) {
            return redirect()->route('notifications')->with('error', 'Notification not found.');
        }
        
        // Mark as read if it's unread
        if ($notification->read_at === null) {
            $notification->markAsRead();
        }
        
        // Determine redirect URL based on notification type
        $redirectUrl = $this->getRedirectUrl($notification);
        
        return redirect($redirectUrl);
    }

    /**
     * Get the appropriate redirect URL based on notification type and data
     */
    private function getRedirectUrl($notification)
    {
        $data = $notification->data;
        $type = $data['type'] ?? 'general';
        
        switch ($type) {
            case 'event':
                if (isset($data['event_id'])) {
                    return route('member.events.show', $data['event_id']);
                }
                return route('member.events.index');
                
            case 'sermon':
                if (isset($data['sermon_id'])) {
                    return route('member.sermons.show', $data['sermon_id']);
                }
                return route('member.sermons.index');
                
            case 'announcement':
                if (isset($data['announcement_id'])) {
                    return route('announcements.public') . '#announcement-' . $data['announcement_id'];
                }
                return route('announcements.public');
                
            case 'livestream':
                return route('livestream');
                
            case 'chat':
                if (isset($data['sender_id'])) {
                    return route('chat.index') . '?user=' . $data['sender_id'];
                }
                return route('chat.index');
                
            case 'donation':
                return route('home') . '#donations';
                
            case 'ministry':
                return route('home') . '#ministries';
                
            default:
                return route('home');
        }
    }

    /**
     * Mark all notifications as read for the authenticated user
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        // Broadcast updated count (0)
        broadcast(new NotificationCountChanged(Auth::id(), 0));
        
        return back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Get notification count for AJAX requests with caching
     */
    public function getCount()
    {
        try {
            $userId = Auth::id();
            $cacheKey = "notification_count_{$userId}";
            
            // Cache for 2 minutes
            $count = \Cache::remember($cacheKey, 120, function () {
                return Auth::user()->unreadNotifications->count();
            });
            
            return response()->json(['count' => $count]);
        } catch (\Exception $e) {
            // Return 0 if database tables don't exist
            return response()->json(['count' => 0]);
        }
    }

    /**
     * Get recent notifications for dropdown/sidebar with caching
     */
    public function getRecent(Request $request)
    {
        try {
            $limit = $request->get('limit', 5);
            $userId = Auth::id();
            $cacheKey = "recent_notifications_{$userId}_{$limit}";
            
            // Cache for 1 minute
            $data = \Cache::remember($cacheKey, 60, function () use ($limit) {
                $notifications = Auth::user()->unreadNotifications()->take($limit)->get();
                return [
                    'notifications' => $notifications,
                    'total_unread' => Auth::user()->unreadNotifications->count()
                ];
            });
            
            return response()->json($data);
        } catch (\Exception $e) {
            // Return empty data if database tables don't exist
            return response()->json([
                'notifications' => [],
                'total_unread' => 0
            ]);
        }
    }
}