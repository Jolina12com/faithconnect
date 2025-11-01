<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Resend\Resend;

Route::post('/send-verification', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:12'
        ]);

        $email = $request->email;
        $code = rand(100000, 999999);
        
        // Store verification code and user data for 10 minutes
        Cache::put("verification_code_{$email}", $code, 600);
        Cache::put("registration_data_{$email}", $request->all(), 600);
        
        // Send email via Resend
        $resend = new Resend(env('RESEND_API_KEY'));
        
        $resend->emails->send([
            'from' => 'FaithConnect <onboarding@resend.dev>',
            'to' => [$email],
            'subject' => 'Email Verification Code - FaithConnect',
            'html' => '<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px;"><div style="background: #4a6fa5; color: white; padding: 30px; text-align: center; border-radius: 8px 8px 0 0;"><h1>Email Verification</h1></div><div style="background: white; padding: 40px; border: 1px solid #ddd;"><h2>Welcome!</h2><p>Your verification code is:</p><div style="background: #f8f9fa; border: 2px solid #4a6fa5; border-radius: 8px; padding: 20px; text-align: center; margin: 20px 0; font-size: 24px; font-weight: bold; color: #4a6fa5;">' . $code . '</div><p><strong>Important:</strong> This code expires in 10 minutes.</p></div></div>'
        ]);

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

Route::post('/verify-registration', function (Request $request) {
    try {
        $email = $request->email;
        $code = $request->verification_code;
        
        $storedCode = Cache::get("verification_code_{$email}");
        $userData = Cache::get("registration_data_{$email}");
        
        if (!$storedCode || $storedCode != $code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }
        
        // Create user
        $user = \App\Models\User::create([
            'name' => $userData['first_name'] . ' ' . $userData['last_name'],
            'email' => $userData['email'],
            'password' => bcrypt($userData['password']),
            'email_verified_at' => now()
        ]);
        
        // Clear cache
        Cache::forget("verification_code_{$email}");
        Cache::forget("registration_data_{$email}");
        
        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ], 500);
    }
});