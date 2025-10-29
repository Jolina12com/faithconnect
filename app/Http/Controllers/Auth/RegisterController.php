<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Models\Member;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login'; // Redirect to login page after registration

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:12'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'password_changed' => true,
            'created_by_admin' => false,
        ]);

        Member::create([
            'user_id' => $user->id,
            'membership_status' => 'new_member',
            'date_of_membership' => now(),
        ]);

        return $user;
    }

    public function sendVerificationCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'password' => 'required|string|min:12',
            'terms' => 'required|accepted'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $email = $request->email;
        
        // Store verification data in cache for 10 minutes
        Cache::put("verification_{$email}", [
            'code' => $code,
            'data' => $request->all()
        ], 600);

        try {
            Mail::send('emails.verification-code', ['code' => $code], function($message) use ($email) {
                $message->to($email)
                       ->subject('Email Verification Code');
            });

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send verification email'
            ], 500);
        }
    }

    public function verifyAndRegister(Request $request)
    {
        $email = $request->email;
        $inputCode = $request->verification_code;
        
        $cached = Cache::get("verification_{$email}");
        
        if (!$cached || $cached['code'] !== $inputCode) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired verification code'
            ], 400);
        }

        try {
            $data = $cached['data'];
            
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'password_changed' => true,
                'created_by_admin' => false,
            ]);

            Member::create([
                'user_id' => $user->id,
                'membership_status' => 'new_member',
                'date_of_membership' => now(),
            ]);

            Cache::forget("verification_{$email}");

            return response()->json([
                'success' => true,
                'user' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Registration failed'
            ], 500);
        }
    }
}
