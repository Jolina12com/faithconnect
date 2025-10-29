<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ConfigureUploadSettings
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only set temp directory - other settings should be in php.ini
        $tmpDir = storage_path('app/tmp');
        if (!file_exists($tmpDir)) {
            @mkdir($tmpDir, 0755, true);
        }
        ini_set('upload_tmp_dir', $tmpDir);

        return $next($request);
    }
} 