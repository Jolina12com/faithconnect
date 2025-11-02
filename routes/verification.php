<?php
// routes/web.php or verification.php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

Route::post('/send-verification', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'password' => 'required|string|min:12'
        ]);

        $email = $request->email;
        $firstName = $request->first_name;
        $lastName = $request->last_name;
        
        // Generate 6-digit verification code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store verification code and user data for 10 minutes (600 seconds)
        Cache::put("verification_code_{$email}", $code, 600);
        Cache::put("registration_data_{$email}", $request->all(), 600);
        
        // Send email using Blade template
        Mail::send('emails.verification-code', ['code' => $code, 'name' => $firstName], function($message) use ($email, $firstName) {
            $message->to($email, $firstName)
                    ->subject('Email Verification Code - FaithConnect');
        });

        // Check if email was sent successfully
        if (Mail::failures()) {
            throw new \Exception('Failed to send email');
        }

        return response()->json([
            'success' => true,
            'message' => 'Verification code sent successfully. Please check your email.'
        ]);

    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Verification email failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to send verification email: ' . $e->getMessage()
        ], 500);
    }
});

Route::post('/verify-registration', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email',
            'verification_code' => 'required|string|size:6'
        ]);
        
        $email = $request->email;
        $code = $request->verification_code;
        
        // Get stored code and user data
        $storedCode = Cache::get("verification_code_{$email}");
        $userData = Cache::get("registration_data_{$email}");
        
        // Validate verification code
        if (!$storedCode || !$userData) {
            return response()->json([
                'success' => false,
                'message' => 'Verification code has expired. Please request a new one.'
            ], 400);
        }
        
        if ($storedCode !== $code) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid verification code. Please try again.'
            ], 400);
        }
        
        // Check if user already exists
        if (\App\Models\User::where('email', $email)->exists()) {
            Cache::forget("verification_code_{$email}");
            Cache::forget("registration_data_{$email}");
            
            return response()->json([
                'success' => false,
                'message' => 'This email is already registered.'
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
        
        // Optional: Auto-login the user
        // Auth::login($user);
        
        return response()->json([
            'success' => true,
            'message' => 'Registration completed successfully! You can now log in.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return response()->json([
            'success' => false,
            'message' => 'Validation failed',
            'errors' => $e->errors()
        ], 422);
        
    } catch (\Exception $e) {
        Log::error('Registration verification failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Registration failed: ' . $e->getMessage()
        ], 500);
    }
});

// Optional: Resend verification code
Route::post('/resend-verification', function (Request $request) {
    try {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->email;
        
        // Check if there's pending registration data
        $userData = Cache::get("registration_data_{$email}");
        
        if (!$userData) {
            return response()->json([
                'success' => false,
                'message' => 'No pending registration found for this email.'
            ], 400);
        }
        
        // Generate new code
        $code = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Store new code (10 minutes)
        Cache::put("verification_code_{$email}", $code, 600);
        
        // Send email
        Mail::send('emails.verification-code', [
            'code' => $code, 
            'name' => $userData['first_name']
        ], function($message) use ($email, $userData) {
            $message->to($email, $userData['first_name'])
                    ->subject('Email Verification Code - FaithConnect');
        });

        return response()->json([
            'success' => true,
            'message' => 'Verification code resent successfully.'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to resend code: ' . $e->getMessage()
        ], 500);
    }
});