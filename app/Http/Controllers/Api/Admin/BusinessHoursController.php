<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessHours;
use Illuminate\Support\Facades\Log;

class BusinessHoursController extends Controller
{
    public function index()
    {
        return BusinessHours::all();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'day' => 'required|string',
            'is_closed' => 'required|boolean',
            'open_time' => $request->input('is_closed') ? 'nullable' : 'required|date_format:H:i',
            'close_time' => $request->input('is_closed') ? 'nullable' : 'required|date_format:H:i',
        ]);

        Log::info('Storing business hour:', $validated);

        return BusinessHours::create($validated);
    }

public function update(Request $request)
{
    $validated = $request->validate([
        'id' => 'required|integer|exists:business_hours,id',
        'day' => 'required|string',
        'is_closed' => 'required|boolean',
        'open_time' => $request->input('is_closed') ? 'nullable' : 'required|date_format:H:i',
        'close_time' => $request->input('is_closed') ? 'nullable' : 'required|date_format:H:i',
    ]);
    
    Log::info('Updating business hour:', $validated);
    
    $businessHours = BusinessHours::findOrFail($validated['id']);
    
    // Remove ID from validated data before updating
    unset($validated['id']);
    
    $businessHours->update($validated);
    return $businessHours;
}

    public function destroy(BusinessHours $businessHours)
    {
        $businessHours->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
