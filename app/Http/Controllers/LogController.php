<?php

namespace App\Http\Controllers;

use App\Models\UserLog;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = UserLog::with('user')
            ->whereHas('user', function($query) {
                $query->where('is_admin', true);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.logs', compact('logs'));
    }

    public function destroy($id)
    {
        UserLog::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Log deleted successfully');
    }

    public static function log($action, $details = null)
    {
        if (auth()->check() && auth()->user()->is_admin) {
            UserLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'details' => $details,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
    }
}