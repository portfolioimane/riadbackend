<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Campaign;
use Illuminate\Http\Request;

class CampaignController extends Controller {
    public function index() {
        return Campaign::all();
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'active' => 'required|boolean',
        ]);
        return Campaign::create($validated);
    }

    public function show(Campaign $campaign) {
        return $campaign;
    }

    public function update(Request $request, Campaign $campaign) {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'active' => 'sometimes|boolean',
        ]);
        $campaign->update($validated);
        return $campaign;
    }

    public function destroy(Campaign $campaign) {
        $campaign->delete();
        return response()->json(null, 204);
    }
}
