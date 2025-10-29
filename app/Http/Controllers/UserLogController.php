<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserLog;
class UserLogController extends Controller
{

    public function index()
{
    $logs = UserLog::latest()->paginate(10); // Simplified pagination
    return view('admin.logs', compact('logs'));
}

    public function destroy($id)
    {
        $log = UserLog::findOrFail($id);
        $log->delete();
        return redirect()->back()->with('success', 'Log deleted successfully.');
    }

}
