<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Models\Event;
use App\Models\Announcement;
use App\Models\Sermon;
use App\Models\Member;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('member.dashboard_member');
    }

    public function edit()
    {
        // Get the current user with their member information
        $user = Auth::user();
        $member = Member::where('user_id', $user->id)->first();

        return view('member.profile', compact('user', 'member'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        // Validate the request data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => 'nullable|string|max:20',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:male,female,other,prefer_not_to_say',
            'address' => 'nullable|string|max:255',
            'marital_status' => 'nullable|string|in:single,married,divorced,widowed,other',
            'emergency_contact' => 'nullable|string|max:255',
        ]);

        // Start a database transaction
        DB::beginTransaction();

        try {
            // Update user table data (split name fields)
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name ?? null;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->save();

            // Find or create the member record
            $member = Member::firstOrNew(['user_id' => $user->id]);

            // Update member table data
            $member->phone_number = $request->phone_number;
            $member->date_of_birth = $request->date_of_birth;
            $member->gender = $request->gender;
            $member->address = $request->address;
            $member->marital_status = $request->marital_status;
            $member->emergency_contact = $request->emergency_contact;
            $member->save();

            DB::commit();

            return redirect()->route('member.profile')->with('success', 'Profile updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error updating profile: ' . $e->getMessage());
        }
    }

    public function upload(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete old profile picture if exists
        if ($user->profile_picture && Storage::disk('public')->exists($user->profile_picture)) {
            Storage::disk('public')->delete($user->profile_picture);
        }

        // Store new image
        $path = $request->file('profile_picture')->store('profile_pictures', 'public');
        $user->profile_picture = $path;
        $user->save();

        return back()->with('success', 'Profile picture uploaded successfully.');
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'new_password'          => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return back()->with('success', 'Password changed successfully.');
    }

    
    public function chatbot()
    {
        return view('member.chatbot');
    }



    public function public()
    {
        $announcements = Announcement::where('published_at', '<=', now())
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->get();

        return view('member.view_announcement', compact('announcements'));
    }

    public function dailyVerse()
    {
        return view('member.daily_verse');
    }
}
