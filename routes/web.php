<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\SermonController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\AdminChatController;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\LogController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\LiveStreamController;
use App\Http\Controllers\SermonMemberController;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\LivestreamInteractionController;
use App\Http\Controllers\EventPollController;
use App\Http\Controllers\MemberEventController;
use App\Http\Controllers\ChatbotAnalyticsController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\PollResponseController;
use App\Http\Controllers\Auth\PasswordChangeController;




// Default landing page
Route::get('/', function () {
    return view('layouts.app'); // Change this if you have a specific homepage
});

// Authentication routes (login, register, logout)
Auth::routes();

// Email verification routes
Route::post('/send-verification', [RegisterController::class, 'sendVerificationCode'])->name('send-verification');
Route::post('/verify-registration', [RegisterController::class, 'verifyAndRegister'])->name('verify-registration');

// User Dashboard (Authenticated users)
Route::middleware(['auth'])->group(function () {
    // Routes accessible even without changing password
    Route::get('/password/change-required', [PasswordChangeController::class, 'showChangeForm'])
        ->name('password.change.required');
    Route::post('/password/change-required', [PasswordChangeController::class, 'update'])
        ->name('password.change.update');

    // Routes that require password to be changed
    Route::middleware(['auth','member',\App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
        Route::get('/home', [HomeController::class, 'index'])->name('home');
        Route::get('/livestream', [LiveStreamController::class,'showViewerPage'])->name('member.view_stream');
        Route::get('profile', [HomeController::class, 'edit'])->name('member.profile');
        Route::put('/profile/update', [HomeController::class, 'update'])->name('profile.update');
        Route::post('/profile/upload', [HomeController::class, 'upload'])->name('profile.upload');
        Route::put('/password/change', [HomeController::class, 'changePassword'])->name('password.change');
        Route::get('/member/events', [MemberEventController::class, 'index'])->name('member.events.index');

    // New enhanced notification routes
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/{id}/redirect', [NotificationController::class, 'markAsReadAndRedirect'])->name('notifications.redirect');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::get('/notifications/count', [NotificationController::class, 'getCount'])->name('notifications.count');
    Route::get('/notifications/recent', [NotificationController::class, 'getRecent'])->name('notifications.recent');

        // Show a specific event with poll and responses (show)
        Route::get('/member/events/{id}', [MemberEventController::class, 'show'])->name('member.events.show');
        Route::get('/member/events/{id}/poll', [MemberEventController::class, 'getPollDetails'])->name('member.events.poll');

        // Submit a response to an event poll (submitPollResponse)
        Route::post('/member/events/{id}/poll-response', [MemberEventController::class, 'submitPollResponse'])->name('member.events.poll.submit');

        Route::get('/chatbot', [HomeController::class, 'chatbot'])->name('member.chatbot');
        Route::get('/daily-verse', [HomeController::class, 'dailyVerse'])->name('member.daily_verse');

        // Sermon routes for members
        Route::get('/sermons', [SermonMemberController::class, 'index'])->name('member.sermons.index');
        Route::get('/sermons/{sermon}', [SermonMemberController::class, 'show'])->name('member.sermons.show');
        Route::get('/sermons/series/{series}', [SermonMemberController::class, 'series'])->name('member.sermons.series');
        Route::get('/sermons/topics/{topic}', [SermonMemberController::class, 'topics'])->name('member.sermons.topics');
        Route::get('/sermons/favorites', [SermonMemberController::class, 'favorites'])->name('member.sermons.favorites');
        Route::get('/sermons/favorites/filter', [SermonMemberController::class, 'filterFavorites'])->name('member.sermons.filter-favorites');
        Route::post('/sermons/toggle-favorite', [SermonMemberController::class, 'toggleFavorite'])->name('member.sermons.toggle-favorite');
        Route::get('/sermons/{sermon}/download/{type}', [SermonMemberController::class, 'download'])->name('member.sermons.download');

        Route::get('/content', [HomeController::class, 'content'])->name('member.content');

        Route::get('/announcements', [HomeController::class, 'public'])->name('announcements.public');

        // Member donation routes
        Route::get('/donations', [App\Http\Controllers\Member\DonationController::class, 'index'])->name('member.donations.index');
        Route::get('/donations/create', [App\Http\Controllers\Member\DonationController::class, 'create'])->name('member.donations.create');
        Route::post('/donations', [App\Http\Controllers\Member\DonationController::class, 'store'])->name('member.donations.store');
        Route::get('/donations/categories/autocomplete', [App\Http\Controllers\Member\DonationController::class, 'getCategories'])->name('member.donations.categories.autocomplete');
        Route::get('/donations/items/autocomplete', [App\Http\Controllers\Member\DonationController::class, 'getItems'])->name('member.donations.items.autocomplete');

        // Add the chatbot analytics route here
        Route::post('/chatbot-analytics/store-message', [ChatbotAnalyticsController::class, 'storeMessage'])->name('chatbot.analytics.store');

        // Chatbot routes
        Route::post('/chatbot/send', [ChatbotController::class, 'sendMessage'])->name('chatbot.send');
        Route::post('/chatbot/more-verses', [ChatbotController::class, 'getMoreVerses'])->name('chatbot.more.verses');
        Route::post('/chatbot/filter-profanity', [ChatbotController::class, 'filterProfanity'])->name('chatbot.filter.profanity');

    });

    // Admin Routes (Only for Admins) - Separate from member routes
    Route::middleware(['auth', 'admin', 'log.admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/main', [AdminController::class, 'main_dashboard'])->name('main_dashboard');

        // Donation routes
        Route::get('/donations', [App\Http\Controllers\Admin\DonationController::class, 'index'])->name('donations.index');
        Route::get('/donations/create', [App\Http\Controllers\Admin\DonationController::class, 'create'])->name('donations.create');
        Route::post('/donations', [App\Http\Controllers\Admin\DonationController::class, 'store'])->name('donations.store');
        Route::get('/donations/monthly', [App\Http\Controllers\Admin\DonationController::class, 'monthly'])->name('donations.monthly');
        Route::get('/donations/transparency', [App\Http\Controllers\Admin\DonationController::class, 'transparency'])->name('donations.transparency');
        Route::get('/donations/{id}', [App\Http\Controllers\Admin\DonationController::class, 'show'])->name('donations.show');
        Route::get('/donations/{id}/edit', [App\Http\Controllers\Admin\DonationController::class, 'edit'])->name('donations.edit');
        Route::put('/donations/{id}', [App\Http\Controllers\Admin\DonationController::class, 'update'])->name('donations.update');
        Route::delete('/donations/{id}', [App\Http\Controllers\Admin\DonationController::class, 'destroy'])->name('donations.destroy');

        // Autocomplete for donation categories and items
        Route::get('/donations/categories/autocomplete', [App\Http\Controllers\Admin\DonationController::class, 'getCategories'])->name('donations.categories.autocomplete');
        Route::get('/donations/items/autocomplete', [App\Http\Controllers\Admin\DonationController::class, 'getItems'])->name('donations.items.autocomplete');
        Route::get('/donations/donors/autocomplete', [App\Http\Controllers\Admin\DonationController::class, 'getDonors'])->name('donations.donors.autocomplete');
        Route::get('/donation-analytics', [App\Http\Controllers\Admin\DonationController::class, 'analytics'])->name('donations.analytics');



        Route::get('/ministries', [AdminController::class, 'ministries'])->name('ministries');


        Route::get('/messaging', [AdminController::class, 'messaging'])->name('messaging');
        Route::get('/notifications', [AdminController::class, 'notifications'])->name('notifications');

        Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
        Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
        Route::resource('/announcements', AnnouncementController::class);
        Route::resource('/members', MemberController::class);
        Route::get('/member-analytics', [MemberController::class, 'getMemberAnalytics'])->name('members.analytics');
        Route::resource('/events', EventController::class);
        Route::get('/events/{id}/details', [App\Http\Controllers\EventController::class, 'ajaxDetails'])->name('events.ajaxDetails');
        Route::get('/event-analytics', [EventController::class, 'getEventAnalytics'])->name('events.analytics');
        Route::get('/events-range', [EventController::class, 'getEventsForRange'])->name('events.range');

        // Wedding and Baptism convenience routes (these map to the events resource with type parameter)
        Route::get('/weddings', [EventController::class, 'index'])
            ->defaults('type', 'wedding')
            ->name('weddings.index');

        Route::get('/weddings/create', [EventController::class, 'create'])
            ->defaults('type', 'wedding')
            ->name('weddings.create');

        Route::get('/baptisms', [EventController::class, 'index'])
            ->defaults('type', 'baptism')
            ->name('baptisms.index');

        Route::get('/baptisms/create', [EventController::class, 'create'])
            ->defaults('type', 'baptism')
            ->name('baptisms.create');

        Route::resource('/sermons', SermonController::class);
        // Chunked upload endpoints for large sermon videos
        Route::post('/sermons/upload-chunk', [SermonController::class, 'uploadChunk'])->name('sermons.upload_chunk');
        Route::post('/sermons/finalize-upload', [SermonController::class, 'finalizeUpload'])->name('sermons.finalize_upload');
        Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat');

        Route::post('/chat/send', [AdminChatController::class, 'send']);
        Route::get('/chat/messages/{receiverId}', [AdminChatController::class, 'fetchMessages']);
        Route::post('/chat/bible-verses', [AdminChatController::class, 'getBibleVerses']);
        Route::post('/chat/search-verses', [AdminChatController::class, 'searchVerses']);
        Route::post('/chat/filter-message', [AdminChatController::class, 'filterMessage']);
        Route::post('/chat/mark-read', [AdminChatController::class, 'markAsRead']);
        Route::post('chat/mark-as-unread/{receiverId}', [AdminChatController::class, 'markAsUnread']);
        Route::post('/chat/typing', [AdminChatController::class, 'userTyping']);
        Route::post('/chat/delete-message', [AdminChatController::class, 'deleteMessage']);
        Route::get('profile', [AdminProfileController::class, 'edit'])->name('admin.profile');
        Route::put('/profile/update', [AdminProfileController::class, 'update'])->name('profile.update');
        Route::post('/profile/upload', [AdminProfileController::class, 'upload'])->name('profile.upload');
        Route::put('/password/change', [AdminProfileController::class, 'changePassword'])->name('password.change');

        // Poll Response Routes
        Route::get('/events/{eventId}/responses', [PollResponseController::class, 'showResponses'])->name('events.responses');
        Route::get('/events/{eventId}/responses/export', [PollResponseController::class, 'exportResponses'])->name('events.responses.export');

        // Chatbot Analytics for Admin Dashboard
        Route::get('/chatbot-analytics', [ChatbotAnalyticsController::class, 'getAnalytics'])->name('chatbot.analytics');
    });

    // Member Routes (Only for Members) - Separate from admin routes
    Route::middleware(['auth', 'admin'])->group(function () {
        // Broadcaster view
        Route::get('/broadcaster', function () {
            return view('broadcaster');
        })->name('broadcaster');

        // API endpoints for the live stream
        Route::post('/check-allowed', [LiveStreamController::class, 'checkAllowed']);
        Route::post('/get-token', [LiveStreamController::class, 'getToken']);

        // Broadcasting authentication
        Route::post('/broadcasting/auth', [LiveStreamController::class, 'auth'])
            ->name('broadcasting.auth');
    });

    // Admin-only routes
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/admin/logs', [LogController::class, 'index'])->name('admin.logs');
        Route::delete('/logs/{id}', [LogController::class, 'destroy'])->name('logs.destroy');
    });

    // Member-only routes
    Route::middleware(['auth', 'member'])->group(function () {
        // Show all notifications
        Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications');

        // Mark a single notification as read
        Route::patch('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
        Route::delete('/notifications/{id}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    });
});

// Notifications count - accessible by both admins and members
Route::middleware(['auth'])->get('/notifications/count', function () {
    return response()->json([
        'count' => Auth::user()->unreadNotifications->count(),
    ]);
})->name('notifications.count');

// Chat routes - accessible by both admins and members
Route::middleware(['auth'])->group(function() {
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/chat/users', [ChatController::class, 'getUsers']);
    Route::get('/chat/messages/{receiver_id}', [ChatController::class, 'fetchMessages']);
    Route::post('/chat/send', [ChatController::class, 'send']);
    Route::get('/chat/unread-count', [ChatController::class, 'getUnreadCount']);
    Route::post('/chat/typing', [ChatController::class, 'userTyping']);
    Route::post('/chat/mark-read', [ChatController::class, 'markAsRead']);
    Route::post('/chat/delete-message', [ChatController::class, 'deleteMessage']);

    // Chat test routes (removed)


    // Pusher test routes (removed)


    // Chat diagnostic routes (removed)

    Route::get('/check-auth-endpoint', function() {
        return response()->json([
            'success' => true,
            'message' => 'Auth endpoint is accessible',
            'csrf_token' => csrf_token(),
            'user_id' => auth()->id()
        ]);
    });
});

Route::middleware(['auth'])->get('/chat/unread-count', function (Request $request) {
    $user = Auth::user();
    if (!$user) {
        return response()->json(['unread' => 0]);
    }

    $unreadCount = Message::where('receiver_id', $user->id)
                          ->where('status', 'sent')
                          ->count();

    return response()->json(['unread' => $unreadCount]);
});

Broadcast::routes(['middleware' => ['auth']]);

Broadcast::channel('chat.{receiverId}', function ($user, $receiverId) {
    return (int) $user->id === (int) $receiverId;
});

// Add this for debugging
Route::any('/check-allowed-debug', function (Request $request) {
    return response()->json([
        'method' => $request->method(),
        'headers' => $request->headers->all(),
        'data' => $request->all(),
        'is_ajax' => $request->ajax()
    ]);
});

// Test POST route (removed)

// In web.php - Keep only this group
Route::middleware(['auth' , \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    Route::get('/livekit/url', [LiveStreamController::class, 'getLivekitUrl']);
    Route::get('/livekit/room', [LiveStreamController::class, 'getRoomName']);
    Route::post('/livekit/token', [LiveStreamController::class, 'getToken']);
    Route::post('/livekit/reactions', [LiveStreamController::class, 'storeReaction']);
    Route::get('/livekit/reactions', [LiveStreamController::class, 'getReactions']);
    Route::get('/livekit/active-stream', [LiveStreamController::class, 'getActiveStream']);

    // Viewer tracking
    Route::post('/livekit/viewer-joined', [LiveStreamController::class, 'viewerJoined']);
    Route::post('/livekit/viewer-left', [LiveStreamController::class, 'viewerLeft']);
    Route::get('/livekit/stream/{streamId}/viewers', [LiveStreamController::class, 'getStreamViewers']);

    // Recording routes for admins
        Route::post('/livekit/start-recording', [LiveStreamController::class, 'startRecording']);
        Route::post('/livekit/stop-recording', [LiveStreamController::class, 'stopRecording']);
        Route::post('/livekit/upload-recording', [LiveStreamController::class, 'uploadRecording']);
        Route::post('/livekit/upload-recording-file', [LiveStreamController::class, 'uploadRecordingFile']);
});
    Route::get('/livekit/token', [LiveStreamController::class, 'getToken']);

Route::middleware(['auth', 'member', \App\Http\Middleware\CheckPasswordChanged::class])->get('/livestream', function () {
    return view('viewer');
})->name('livestream');

// Livestream interaction routes - Member only
Route::prefix('livestream')->middleware(['auth', \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    Route::get('/comments', [LivestreamInteractionController::class, 'getComments']);
    Route::post('/comments', [LivestreamInteractionController::class, 'postComment']);
    Route::get('/reactions', [LivestreamInteractionController::class, 'getReactions']);
    Route::post('/reactions', [LivestreamInteractionController::class, 'postReaction']);
});

// Event Poll Routes - Member only
Route::middleware(['auth', 'member', \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    Route::post('/events/{event}/polls', [EventPollController::class, 'store'])->name('event.polls.store');
    Route::post('/polls/{poll}/vote', [EventPollController::class, 'vote'])->name('event.polls.vote');
    Route::patch('/polls/{poll}/toggle', [EventPollController::class, 'toggleStatus'])->name('event.polls.toggle');
    Route::delete('/polls/{poll}', [EventPollController::class, 'destroy'])->name('event.polls.destroy');
});

// Chatbot Analytics routes - Member only
Route::middleware(['auth' , \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    Route::get('/chatbot-analytics', [ChatbotAnalyticsController::class, 'getAnalytics'])->name('chatbot.analytics');
});



// Chat unread counts - accessible by both admins and members
Route::middleware(['auth'])->get('/chat/unread-counts', [App\Http\Controllers\ChatController::class, 'getUnreadCounts']);
Route::middleware(['auth'])->get('/chat/users-with-messages', [App\Http\Controllers\ChatController::class, 'getUsersWithLastMessages']);


// Add these routes in your authenticated middleware group
Route::middleware(['auth'])->group(function() {
    // Bible verse API
    Route::post('/chat/bible-verses', [ChatController::class, 'getBibleVerses']);
    Route::post('/chat/search-verses', [ChatController::class, 'searchVerses']);
    Route::post('/chat/more-verses', [ChatController::class, 'getMoreVerses']);

    // Profanity filter
    Route::post('/chat/filter-message', [ChatController::class, 'filterMessage']);

    // Prayer request tagging
    Route::post('/chat/tag-prayer-request', [ChatController::class, 'tagPrayerRequest']);
});


// VOD/Replay routes (Member access)
// Remove the member-only restriction, keep auth only
Route::middleware(['auth', \App\Http\Middleware\CheckPasswordChanged::class])->group(function () {
    Route::get('/recordings', [LiveStreamController::class, 'recordings'])->name('member.recordings');
    Route::get('/recordings/{id}/watch', [LiveStreamController::class, 'watchRecording'])->name('member.recordings.watch');
    Route::get('/api/past-streams', [LiveStreamController::class, 'getPastStreams'])->name('api.past-streams');
});
// Stream lifecycle
Route::post('/livestream/end', [LiveStreamController::class, 'endStream']);
Route::post('/livestream/handle-failure', [LiveStreamController::class, 'handleStreamFailure']);
Route::get('/livestream/status/{id}', [LiveStreamController::class, 'checkStreamStatus']);
Route::get('/livestream/live', [LiveStreamController::class, 'getLiveStreams']);

// Cleanup (protect with admin middleware)
Route::post('/admin/livestream/cleanup', [LiveStreamController::class, 'cleanupStaleStreams'])
    ->middleware('admin');

// Emergency end stream (no auth required for sendBeacon)
Route::post('/livekit/emergency-end-stream', [LiveStreamController::class, 'emergencyEndStream']);

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function() {
    Route::get('/livestreams', [LiveStreamController::class, 'index'])
        ->name('admin.livestreams.index');

    Route::delete('/livestreams/{id}', [LiveStreamController::class, 'destroy'])
        ->name('admin.livestreams.destroy');
});
Route::get('/seed', function() {
    Artisan::call('db:seed', ['--class' => 'AdminSeeder', '--force' => true]);
    return 'Done: ' . Artisan::output();
});