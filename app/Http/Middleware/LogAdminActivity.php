<?php

namespace App\Http\Middleware;

use App\Http\Controllers\LogController;
use Closure;
use Illuminate\Http\Request;

class LogAdminActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (auth()->check() && auth()->user()->is_admin) {
            $action = $this->getActionFromRequest($request);
            
            if ($action) {
                LogController::log($action, $this->getDetails($request));
            }
        }

        return $response;
    }

    private function getActionFromRequest($request)
    {
        $method = $request->method();
        $path = $request->path();

        if ($method === 'POST' && str_contains($path, 'sermon')) return 'Created Sermon';
        if ($method === 'PUT' && str_contains($path, 'sermon')) return 'Updated Sermon';
        if ($method === 'DELETE' && str_contains($path, 'sermon')) return 'Deleted Sermon';
        
        if ($method === 'POST' && str_contains($path, 'donation')) return 'Created Donation';
        if ($method === 'PUT' && str_contains($path, 'donation')) return 'Updated Donation';
        if ($method === 'DELETE' && str_contains($path, 'donation')) return 'Deleted Donation';
        
        if ($method === 'POST' && str_contains($path, 'member')) return 'Created Member';
        if ($method === 'PUT' && str_contains($path, 'member')) return 'Updated Member';
        if ($method === 'DELETE' && str_contains($path, 'member')) return 'Deleted Member';
        
        if ($method === 'POST' && str_contains($path, 'event')) return 'Created Event';
        if ($method === 'PUT' && str_contains($path, 'event')) return 'Updated Event';
        if ($method === 'DELETE' && str_contains($path, 'event')) return 'Deleted Event';

        return null;
    }

    private function getDetails($request)
    {
        return json_encode([
            'url' => $request->fullUrl(),
            'method' => $request->method(),
            'data' => $request->except(['password', '_token'])
        ]);
    }
}