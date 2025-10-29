<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Unauthenticated
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect('/login')->with('error', 'Please login to access this page.');
        }

        if (!Auth::user()->is_admin) {
            // Non-admins blocked from admin routes
            return $request->expectsJson()
                ? response()->json(['message' => 'Access Denied! Admins only.'], 403)
                : redirect('/daily-verse')->with('error', 'Access Denied! Admins only.');
        }

        // ✅ Passed check → allow admin
        return $next($request);
    }
}
