<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\TemporaryPasswordMail;

class MemberController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = Member::join('users', 'users.id', '=', 'members.user_id')
            ->select(
                'members.*',
                DB::raw("CONCAT(users.first_name, ' ', COALESCE(users.middle_name, ''), ' ', users.last_name) as user_name"),
                'users.email as user_email'
            )
            ->where('users.is_admin', false); // Exclude admin users using the is_admin column

        // Search by name or email
        if (request()->filled('search')) {
            $search = request('search');
            $query->where(function($q) use ($search) {
                $q->where('users.first_name', 'like', "%$search%")
                  ->orWhere('users.middle_name', 'like', "%$search%")
                  ->orWhere('users.last_name', 'like', "%$search%")
                  ->orWhere('users.email', 'like', "%$search%");
            });
        }

        // Filter by status via header buttons
        if (request()->filled('status')) {
            $status = request('status');
            if ($status === 'member') {
                $query->whereIn('members.membership_status', [
                    'active_member', 'baptized_member'
                ]);
            } elseif ($status === 'new_member') {
                $query->where('members.membership_status', 'new_member');
            }
        }

        $members = $query->orderBy('users.last_name')
                         ->orderBy('users.first_name')
                         ->paginate(10)
                         ->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Generate an initial password for the form
        $generatedPassword = $this->generateRandomPassword();
        return view('admin.members.create', compact('generatedPassword'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'generated_password' => 'required|string|min:8',
        ]);

        DB::beginTransaction();
        try {
            // Use the password from the form
            $defaultPassword = $validatedData['generated_password'];
            
            // Create user first
            $user = User::create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
                'password' => bcrypt($defaultPassword), // Set the password
                'password_changed' => false, // Set password_changed to false
                'created_by_admin' => true, // Set created_by_admin to true
            ]);

            // Create member linked to user
            $member = new Member([
                'phone_number' => $validatedData['phone_number'] ?? null,
                'date_of_birth' => $validatedData['date_of_birth'] ?? null,
                'address' => $validatedData['address'] ?? null,
                'gender' => $validatedData['gender'] ?? null,
                'marital_status' => $validatedData['marital_status'] ?? null,
                'emergency_contact' => $validatedData['emergency_contact'] ?? null,
                'membership_status' => 'new_member',
                'date_of_membership' => now(),
                'baptism_date' => $validatedData['baptism_date'] ?? null,
            ]);

            $user->member()->save($member);

            // Send email with temporary password
            try {
                Mail::send('emails.temporary-password', [
                    'memberName' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
                    'email' => $validatedData['email'],
                    'password' => $defaultPassword
                ], function($message) use ($validatedData) {
                    $message->to($validatedData['email'])
                            ->subject('Welcome to Our Church - Your Login Details')
                            ->from('gio646526@gmail.com', 'FaithConnect');
                });
                $emailSent = true;
            } catch (\Exception $e) {
                $emailSent = false;
                \Log::error('Failed to send welcome email: ' . $e->getMessage());
            }

            DB::commit();
            
            // Return with success message
            $message = "Member created successfully! ";
            $message .= $emailSent 
                ? "Welcome email with login details has been sent to {$validatedData['email']}."
                : "However, the welcome email could not be sent. Please provide the login details manually.";
                
            return redirect()->route('admin.members.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to create member: ' . $e->getMessage()]);
        }
    }

    /**
     * Generate a random password
     * 
     * @return string
     */
    private function generateRandomPassword()
    {
        // Generate a more user-friendly 8 character password
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers = '0123456789';
        
        $password = '';
        
        // Ensure at least one character from each category
        $password .= $uppercase[rand(0, strlen($uppercase) - 1)];
        $password .= $lowercase[rand(0, strlen($lowercase) - 1)];
        $password .= $numbers[rand(0, strlen($numbers) - 1)];
        
        // Fill the rest with random characters
        $allChars = $uppercase . $lowercase . $numbers;
        for ($i = 3; $i < 8; $i++) {
            $password .= $allChars[rand(0, strlen($allChars) - 1)];
        }
        
        // Shuffle the password
        return str_shuffle($password);
    }

    /**
     * Static method to generate random password for Blade templates
     * 
     * @return string
     */
    public static function generateRandomPasswordStatic()
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        
        for ($i = 0; $i < 8; $i++) {
            $index = rand(0, strlen($chars) - 1);
            $password .= $chars[$index];
        }
        
        return $password;
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $member = Member::findOrFail($id);
        $member->load('user');
        return view('admin.members.show', compact('member'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Member $member)
    {
        $member->load('user');
        return view('admin.members.edit', compact('member'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $member = Member::findOrFail($id);

        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $member->user_id,
            'phone_number' => 'nullable|string|max:15',
            'address' => 'nullable|string|max:255',
            'date_of_birth' => 'nullable|date',
            'gender' => 'nullable|string|in:Male,Female,Other,Prefer not to say',
            'marital_status' => 'required|string|in:Single,Married,Divorced,Widowed',
            'emergency_contact' => 'nullable|string|max:255',
            'membership_status' => 'required|string|in:new_member,active_member',
            'date_of_membership' => 'date',
            'baptism_date' => 'date',
        ]);

        DB::beginTransaction();
        try {
            // Update the user information
            $user = User::findOrFail($member->user_id);
            $user->update([
                'first_name' => $validatedData['first_name'],
                'middle_name' => $validatedData['middle_name'] ?? null,
                'last_name' => $validatedData['last_name'],
                'email' => $validatedData['email'],
            ]);

            // Update the member information
            $member->update([
                'phone_number' => $validatedData['phone_number'] ?? $member->phone_number,
                'date_of_birth' => $validatedData['date_of_birth'] ?? $member->date_of_birth,
                'address' => $validatedData['address'] ?? $member->address,
                'gender' => $validatedData['gender'] ?? $member->gender,
                'marital_status' => $validatedData['marital_status'] ?? $member->marital_status,
                'emergency_contact' => $validatedData['emergency_contact'] ?? $member->emergency_contact,
                'membership_status' => $validatedData['membership_status'] ?? $member->membership_status,
                'date_of_membership' => $validatedData['date_of_membership'] ?? $member->date_of_membership,
                'baptism_date' => $validatedData['baptism_date'] ?? $member->baptism_date,
            ]);

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Member updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Failed to update member: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $member = Member::findOrFail($id);

        DB::beginTransaction();
        try {
            // Find the associated user
            $user = User::findOrFail($member->user_id);

            // Delete the member first (since it has a foreign key to user)
            $member->delete();

            // Then delete the user
            $user->delete();

            DB::commit();
            return redirect()->route('admin.members.index')->with('success', 'Member deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to delete member: ' . $e->getMessage()]);
        }
    }

    /**
     * Get member analytics data for dashboard
     */
    public function getMemberAnalytics()
    {
        // Get total members count
        $totalMembers = Member::count();
        
        // Get new members count (this month)
        $newMembers = Member::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Get membership status distribution
        $membershipStatus = Member::select('membership_status', DB::raw('count(*) as count'))
            ->groupBy('membership_status')
            ->get()
            ->pluck('count', 'membership_status')
            ->toArray();
        
        // Get monthly new members (for the last 6 months)
        $sixMonthsAgo = now()->subMonths(6)->startOfMonth();
        $monthlyNewMembers = Member::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('count(*) as count')
            )
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();
            
        // Format the monthly data for charts
        $monthlyData = [];
        foreach ($monthlyNewMembers as $data) {
            $monthName = date('M', mktime(0, 0, 0, $data->month, 1));
            $monthlyData[$monthName] = $data->count;
        }
        
        // Get recent members (last 5)
        $recentMembers = Member::with('user')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
            
        return response()->json([
            'totalMembers' => $totalMembers,
            'newMembers' => $newMembers,
            'membershipStatus' => $membershipStatus,
            'monthlyNewMembers' => $monthlyData,
            'recentMembers' => $recentMembers
        ]);
    }
}
