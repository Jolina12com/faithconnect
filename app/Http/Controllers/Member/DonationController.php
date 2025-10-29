<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function index()
    {
        $donations = DB::table('donations')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('member.donations.index', compact('donations'));
    }

    public function create()
    {
        return view('member.donations.create');
    }

    public function store(Request $request)
    {
        $rules = [
            'donation_date' => 'required|date',
            'donation_type' => 'required|in:monetary,food,materials,medical,other',
            'notes' => 'nullable|string|max:500',
            'category' => 'nullable|string|max:50',
            'is_recurring' => 'nullable|boolean',
        ];

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

        $user = Auth::user();
        $data = [
            'donor_name' => $user->first_name . ' ' . $user->last_name,
            'user_id' => $user->id,
            'donation_date' => $validated['donation_date'],
            'donation_type' => $validated['donation_type'],
            'notes' => $validated['notes'] ?? null,
            'category' => $validated['category'] ?? null,
            'is_recurring' => isset($validated['is_recurring']) ? 1 : 0,
            'created_at' => now(),
            'updated_at' => now(),
        ];

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

        return redirect()->route('member.donations.index')
            ->with('success', 'Thank you! Your donation has been recorded successfully.');
    }

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
}
