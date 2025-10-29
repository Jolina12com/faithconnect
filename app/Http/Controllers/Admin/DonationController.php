<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DonationController extends Controller
{
    /**
     * Display a listing of all donations.
     */
    public function index()
    {
        // Fetch all donations with optional member information
        $donations = DB::table('donations')
            ->leftJoin('users', 'donations.user_id', '=', 'users.id')
            ->select('donations.*', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email')
            ->orderBy('donations.created_at', 'desc')
            ->paginate(15);

        return view('admin.donations.index', compact('donations'));
    }

    /**
     * Display monthly donation reports.
     */
    public function monthly()
    {
        // Get donations grouped by month for the current year
        $currentYear = Carbon::now()->year;
        $monthlyDonations = DB::table('donations')
            ->select(
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as donation_count')
            )
            ->whereYear('created_at', $currentYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy('month')
            ->get();

        // Format the data for display
        $months = [];
        $totals = [];
        $counts = [];

        foreach ($monthlyDonations as $donation) {
            $monthName = Carbon::create()->month($donation->month)->format('F');
            $months[] = $monthName;
            $totals[] = $donation->total_amount;
            $counts[] = $donation->donation_count;
        }

        return view('admin.donations.monthly', compact('months', 'totals', 'counts', 'currentYear'));
    }

    /**
     * Display donation transparency information for members.
     */
    public function transparency()
    {
        // Get monthly totals for member transparency
        $monthlyTotals = DB::table('donations')
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('COUNT(*) as donation_count')
            )
            ->where('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->get();

        // Format data for display
        $formattedData = $monthlyTotals->map(function($item) {
            $date = Carbon::createFromDate($item->year, $item->month, 1);
            return [
                'month_year' => $date->format('F Y'),
                'total_amount' => $item->total_amount,
                'donation_count' => $item->donation_count,
                'date_for_sorting' => $date->format('Y-m')
            ];
        });

        return view('admin.donations.transparency', compact('formattedData'));
    }

    /**
     * Show the form for creating a new donation.
     */
    public function create()
    {
        return view('admin.donations.create');
    }

    /**
     * Store a newly created donation in storage.
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'donor_name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'donation_date' => 'required|date',
            'donation_type' => 'required|in:monetary,food,materials,medical,other',
            'notes' => 'nullable|string|max:500',
            'receipt_number' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
            'is_recurring' => 'nullable|boolean',
        ];

        // Conditional validation based on donation type
        if ($request->donation_type === 'monetary') {
            $rules['amount'] = 'required|numeric|min:0.01';
            $rules['payment_method'] = 'required|string';
        } else {
            // For physical donations (food, materials, medical)
            $rules['item_name'] = 'required|string|max:100';
            $rules['quantity'] = 'required|numeric|min:0.01';
            $rules['unit'] = 'required|string|max:20';
            
            if (in_array($request->donation_type, ['food', 'medical'])) {
                $rules['expiry_date'] = 'nullable|date|after:today';
            }
            
            if ($request->donation_type === 'materials') {
                $rules['condition'] = 'nullable|in:new,used,good,fair';
            }
        }

        $validated = $request->validate($rules);

        // Prepare data for insertion
        $data = [
            'donor_name' => $validated['donor_name'],
            'user_id' => $validated['user_id'] ?? null,
            'donation_date' => $validated['donation_date'],
            'donation_type' => $validated['donation_type'],
            'notes' => $validated['notes'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_recurring' => isset($validated['is_recurring']) ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Add type-specific fields
        if ($validated['donation_type'] === 'monetary') {
            $data['amount'] = $validated['amount'];
            $data['payment_method'] = $validated['payment_method'];
        } else {
            $data['item_name'] = $validated['item_name'];
            $data['quantity'] = $validated['quantity'];
            $data['unit'] = $validated['unit'];
            $data['condition'] = $validated['condition'] ?? null;
            $data['expiry_date'] = $validated['expiry_date'] ?? null;
        }

        DB::table('donations')->insert($data);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation record created successfully.');
    }

    /**
     * Show the specified donation.
     */
    public function show($id)
    {
        $donation = DB::table('donations')
            ->leftJoin('users', 'donations.user_id', '=', 'users.id')
            ->select('donations.*', 'users.first_name', 'users.middle_name', 'users.last_name', 'users.email')
            ->where('donations.id', $id)
            ->first();

        if (!$donation) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Donation not found.');
        }

        return view('admin.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation.
     */
    public function edit($id)
    {
        $donation = DB::table('donations')->find($id);

        if (!$donation) {
            return redirect()->route('admin.donations.index')
                ->with('error', 'Donation not found.');
        }

        return view('admin.donations.edit', compact('donation'));
    }

    /**
     * Update the specified donation in storage.
     */
        public function update(Request $request, $id)
    {
        // Base validation rules
        $rules = [
            'donor_name' => 'required|string|max:255',
            'user_id' => 'nullable|exists:users,id',
            'donation_date' => 'required|date',
            'donation_type' => 'required|in:monetary,food,materials,medical,other',
            'notes' => 'nullable|string|max:500',
            'receipt_number' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:50',
            'is_recurring' => 'nullable|boolean',
        ];

        // Conditional validation
        if ($request->donation_type === 'monetary') {
            $rules['amount'] = 'required|numeric|min:0.01';
            $rules['payment_method'] = 'required|string';
        } else {
            $rules['item_name'] = 'required|string|max:100';
            $rules['quantity'] = 'required|numeric|min:0.01';
            $rules['unit'] = 'required|string|max:20';
            
            if (in_array($request->donation_type, ['food', 'medical'])) {
                $rules['expiry_date'] = 'nullable|date|after:today';
            }
            
            if ($request->donation_type === 'materials') {
                $rules['condition'] = 'nullable|in:new,used,good,fair';
            }
        }

        $validated = $request->validate($rules);

        // Prepare data for update
        $data = [
            'donor_name' => $validated['donor_name'],
            'user_id' => $validated['user_id'] ?? null,
            'donation_date' => $validated['donation_date'],
            'donation_type' => $validated['donation_type'],
            'notes' => $validated['notes'] ?? null,
            'receipt_number' => $validated['receipt_number'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_recurring' => isset($validated['is_recurring']) ? 1 : 0,
            'updated_at' => now(),
        ];

        // Clear opposite type fields and add relevant ones
        if ($validated['donation_type'] === 'monetary') {
            $data['amount'] = $validated['amount'];
            $data['payment_method'] = $validated['payment_method'];
            $data['item_name'] = null;
            $data['quantity'] = null;
            $data['unit'] = null;
            $data['condition'] = null;
            $data['expiry_date'] = null;
        } else {
            $data['item_name'] = $validated['item_name'];
            $data['quantity'] = $validated['quantity'];
            $data['unit'] = $validated['unit'];
            $data['condition'] = $validated['condition'] ?? null;
            $data['expiry_date'] = $validated['expiry_date'] ?? null;
            $data['amount'] = null;
            $data['payment_method'] = null;
        }

        DB::table('donations')->where('id', $id)->update($data);

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation record updated successfully.');
    }


    /**
     * Remove the specified donation from storage.
     */
    public function destroy($id)
    {
        DB::table('donations')->where('id', $id)->delete();

        return redirect()->route('admin.donations.index')
            ->with('success', 'Donation record deleted successfully.');
    }

    /**
     * Get autocomplete suggestions for categories.
     */
    public function getCategories(Request $request)
    {
        $search = $request->get('term', '');
        
        $categories = DB::table('donations')
            ->select('category')
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->where('category', 'LIKE', "%{$search}%")
            ->groupBy('category')
            ->limit(10)
            ->pluck('category');

        return response()->json($categories);
    }

    /**
     * Get autocomplete suggestions for item names.
     */
    public function getItems(Request $request)
    {
        $search = $request->get('term', '');
        $type = $request->get('type', '');
        
        $query = DB::table('donations')
            ->select('item_name')
            ->whereNotNull('item_name')
            ->where('item_name', '!=', '')
            ->where('item_name', 'LIKE', "%{$search}%");
        
        if ($type) {
            $query->where('donation_type', $type);
        }
        
        $items = $query->groupBy('item_name')
            ->limit(10)
            ->pluck('item_name');

        return response()->json($items);
    }

    /**
     * Get autocomplete suggestions for donor names.
     */
    public function getDonors(Request $request)
    {
        $search = $request->get('term', '');
        
        $users = DB::table('users')
            ->select('id', DB::raw("CONCAT(first_name, ' ', last_name) as name"))
            ->where(DB::raw("CONCAT(first_name, ' ', last_name)"), 'LIKE', "%{$search}%")
            ->limit(10)
            ->get()
            ->map(function($user) {
                return [
                    'value' => $user->id,
                    'label' => $user->name
                ];
            });

        return response()->json($users);
    }

    /**
     * Get donation analytics for dashboard
     */
    public function analytics()
    {
        // Total donations
        $totalDonations = DB::table('donations')->count();
        
        // Total monetary amount
        $totalAmount = DB::table('donations')
            ->where('donation_type', 'monetary')
            ->sum('amount');
        
        // This month's donations
        $thisMonth = DB::table('donations')
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->count();
        
        // Monthly donation amounts for chart
        $monthlyAmounts = DB::table('donations')
            ->select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN donation_type = "monetary" THEN amount ELSE 0 END) as total_amount')
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_amount', 'month')
            ->toArray();
        
        // Donation types breakdown
        $donationTypes = DB::table('donations')
            ->select('donation_type', DB::raw('COUNT(*) as count'))
            ->groupBy('donation_type')
            ->pluck('count', 'donation_type')
            ->toArray();
        
        // Recent donations
        $recentDonations = DB::table('donations')
            ->leftJoin('users', 'donations.user_id', '=', 'users.id')
            ->select('donations.*', 'users.first_name', 'users.last_name')
            ->orderBy('donations.created_at', 'desc')
            ->limit(5)
            ->get();
        
        return response()->json([
            'totalDonations' => $totalDonations,
            'totalAmount' => number_format($totalAmount, 2),
            'thisMonth' => $thisMonth,
            'monthlyAmounts' => $monthlyAmounts,
            'donationTypes' => $donationTypes,
            'recentDonations' => $recentDonations
        ]);
    }
} 