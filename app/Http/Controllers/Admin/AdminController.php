<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message;

class AdminController extends Controller
{
    /**
     * Apply middleware to restrict access to admins only.
     */
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    /**
     * Show the Admin Dashboard.
     */
    public function index()
    {
        return view('admin.dashboard');
    }


    /**
     * Show Families Management Page.
     */
    public function families()
    {
        return view('admin.families');
    }

    /**
     * Show Ministries & Departments Page.
     */
    public function ministries()
    {
        return view('admin.ministries');
    }

    /**
     * Show Attendance Tracking Page.
     */

    /**
     * Show Sermons & Live Stream Page.
     */


    /**
     * Show Events & Calendar Page.
     */

    /**
     * Show Volunteer Management Page.
     */
    public function volunteers()
    {
        return view('admin.volunteers');
    }


    /**
     * Show Announcements Page.
     */

     public function main_dashboard()
     {
         $memberCount = User::where('is_admin', 0)->count();
         $adminCount = User::where('is_admin', 1)->count();
         $messageCount = Message::count();


         return view('admin.main_dashboard', compact(
             'memberCount',
             'adminCount',
             'messageCount',

         ));
     }
    /**
     * Show Messaging & Chat Page.
     */
    public function messaging()
    {
        return view('admin.messaging');
    }

    /**
     * Show Notifications (Email & SMS Alerts) Page.
     */


    /**
     * Show Library & Digital Resources Page.
     */
    public function library()
    {
        return view('admin.library');
    }

    /**
     * Show Facility & Equipment Booking Page.
     */
    public function booking()
    {
        return view('admin.booking');
    }

    /**
     * Show User Roles & Permissions Page.
     */
    public function roles()
    {
        return view('admin.roles');
    }

    /**
     * Show Audit Logs Page.
     */
    public function logs()
    {
        return view('admin.logs');
    }

    /**
     * Show Settings Page.
     */
    public function settings()
    {
        return view('admin.settings');
    }

}
