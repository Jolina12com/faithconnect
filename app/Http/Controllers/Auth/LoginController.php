<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Models\UserLog;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    protected $redirectTo = '/home';

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    protected function authenticated(Request $request, $user)
    {
        if ($user->is_admin) {
            return redirect('/admin/main'); // Redirect admins to admin dashboard
        }

        return redirect('/daily-verse'); // Redirect regular users to home
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials)) {
            UserLog::create([
                'user_id' => Auth::id(),
                'action' => 'Login',
                'details' => 'User  logged in successfully.',
                'created_at' => now(), // Store login time
            ]);

            // The redirect will be handled by the authenticated method
            return $this->authenticated($request, Auth::user());
        }

        return back()->withErrors(['email' => 'Invalid credentials']);
    }


public function logout(Request $request)
{
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect('/');
}
}
