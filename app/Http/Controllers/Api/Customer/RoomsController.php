<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    // Fetch all rooms
    public function getAllRooms()
    {
        $rooms = Room::all();

        Log::info('All rooms fetched: ', $rooms->toArray());

        return response()->json($rooms, 200);
    }

    // Fetch a specific room by ID
    public function show($id)
    {
        $room = Room::findOrFail($id);

        Log::info('Room fetched: ', $room->toArray());

        return response()->json($room, 200);
    }

    // Fetch featured rooms
    public function getFeaturedRooms()
    {
        $featuredRooms = Room::where('featured', true)
            ->latest()
            ->take(4)
            ->get();

        Log::info('Featured rooms fetched: ', $featuredRooms->toArray());

        return response()->json($featuredRooms, 200);
    }

    // Fetch popular rooms (e.g., latest 8)
    public function getPopularRooms()
    {
        $popularRooms = Room::latest()
            ->limit(8)
            ->get();

        Log::info('Latest 8 rooms fetched:', $popularRooms->toArray());

        return response()->json($popularRooms, 200);
    }
}
