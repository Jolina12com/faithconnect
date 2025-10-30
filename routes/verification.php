<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

Route::post('/send-verification', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        $code = rand(100000, 999999);
        
        // Store verification code for 10 minutes
        Cache::put("verification_code_{$email}", $code, 600);
        
        // Send email
        Mail::send('emails.verification-code', ['code' => $code], function($message) use ($email) {
            $message->to($email)
                    ->subject('Email Verification Code - FaithConnect');
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to send verification email: ' . $e->getMessage()
        ], 500);
    }
});