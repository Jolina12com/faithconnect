<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordChangeController extends Controller
{
    /**
     * Show the change password form.
     */
    public function showChangeForm()
    {
        return view('auth.passwords.change');
    }

    /**
     * Update the user's password.
     */
    public function update(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = Auth::user();
        $user->password = Hash::make($request->password);
        $user->password_changed = true;
        $user->save();

        return redirect()->intended('/daily-verse')->with('success', '<strong>Password Changed Successfully!</strong> Your account is now secure with your new password. You can now access all features of the application.');
    }
} 