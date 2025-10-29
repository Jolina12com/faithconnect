<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LiveStreamController;
use App\Http\Controllers\ChatbotAnalyticsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Add Chatbot Analytics route
Route::get('/chatbot-analytics', [ChatbotAnalyticsController::class, 'getAnalytics']);

// LiveKit routes - protected by auth:sanctum middleware
Route::middleware(['auth:sanctum'])->prefix('livekit')->group(function () {
    Route::get('/url', [LiveStreamController::class, 'getLivekitUrl']);
    Route::get('/room', [LiveStreamController::class, 'getRoomName']);
    Route::post('/token', [LiveStreamController::class, 'getToken']);
});

