<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            // Unauthenticated → redirect or return 401
            return $request->expectsJson()
                ? response()->json(['message' => 'Unauthenticated.'], 401)
                : redirect('/login')->with('error', 'Please login to access this page.');
        }

        if (Auth::user()->is_admin) {
            // Admins blocked from member routes
            return $request->expectsJson()
                ? response()->json(['message' => 'Admins cannot access member routes.'], 403)
                : redirect('/admin/main')->with('error', 'Access Denied! Members only.');
        }

        // ✅ Passed all checks → allow member
        return $next($request);
    }
}
