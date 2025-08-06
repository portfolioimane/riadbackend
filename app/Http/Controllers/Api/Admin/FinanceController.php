<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\Booking; // Include the Booking model
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    /**
     * Display a listing of the finances along with booking revenue.
     */
    public function index()
    {
        $finances = Finance::all();

        // Calculate total paid booking revenue
        $bookingRevenue = Booking::sum('paid_amount');

        // Optional logging for debugging
        \Log::info('All finance records:', $finances->toArray());
        \Log::info('Total booking revenue:', ['bookingRevenue' => $bookingRevenue]);

        return response()->json([
            'finances' => $finances,
            'bookingRevenue' => $bookingRevenue,
            'totalRevenue' => $finances->where('type', 'revenue')->sum('amount') + $bookingRevenue,
            'totalExpense' => $finances->where('type', 'expense')->sum('amount'),
        ]);
    }

    /**
     * Store a newly created finance record.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:revenue,expense',
            'title' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        return Finance::create($data);
    }

    /**
     * Display the specified finance record.
     */
    public function show(Finance $finance)
    {
        return $finance;
    }

    /**
     * Update the specified finance record.
     */
    public function update(Request $request, Finance $finance)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:revenue,expense',
            'title' => 'sometimes|string|max:255',
            'amount' => 'sometimes|numeric',
            'date' => 'sometimes|date',
        ]);

        $finance->update($data);

        return $finance;
    }

    /**
     * Remove the specified finance record.
     */
    public function destroy(Finance $finance)
    {
        $finance->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
