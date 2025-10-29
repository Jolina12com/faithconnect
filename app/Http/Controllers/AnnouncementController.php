<?php

namespace App\Http\Controllers;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\Request;
use App\Events\AnnouncementPosted;
use App\Notifications\AnnouncementNotification;
class AnnouncementController extends Controller

{
    public function index()
    {
        $announcements = Announcement::latest()->get();
        return view('admin.announcements')->with('announcements', $announcements);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $announcement  = Announcement::create([
            'title' => $request->title,
            'message' => $request->message,
            'published_at' => now(),
            'is_pinned' => $request->has('is_pinned') ? 1 : 0
        ]);

        broadcast(new AnnouncementPosted($announcement))->toOthers();

        $users = User::where('is_admin', 0)->get(); // exclude admin
        foreach ($users as $user) {
            $user->notify(new AnnouncementNotification($announcement));
        }

        return redirect()->back()->with('success', 'Announcement posted and users notified.');
    }
    public function destroy($id)
    {
        $announcement = Announcement::findOrFail($id);
        $announcement->delete();

        return redirect()->back()->with('success', 'Announcement deleted successfully.');
    }
}

