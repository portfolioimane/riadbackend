<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RoomsController extends Controller
{
    public function index()
    {
        $rooms = Room::all();
        Log::info('Fetched all rooms', ['count' => $rooms->count()]);
        return response()->json($rooms);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'room_name' => 'required|string|max:255',
            'room_type' => 'required|string|max:255',
            'main_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'max_adults' => 'required|integer|min:1',
            'max_children' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for room store', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->except('main_photo');

        if ($request->hasFile('main_photo')) {
            $path = $request->file('main_photo')->store('images/rooms', 'public');
            $data['main_photo'] = 'storage/' . $path;
            Log::info('Room main photo uploaded', ['path' => $path]);
        }

        $room = Room::create($data);

        Log::info('Room created', ['room_id' => $room->id]);
        return response()->json($room, 201);
    }

    public function show($id)
    {
        $room = Room::findOrFail($id);
        Log::info('Fetched room by ID', ['room_id' => $id]);
        return response()->json($room);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'room_name' => 'sometimes|required|string|max:255',
            'room_type' => 'sometimes|required|string|max:255',
            'main_photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'max_adults' => 'sometimes|required|integer|min:1',
            'max_children' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'description' => 'nullable|string',
            'featured' => 'boolean',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for room update', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $room = Room::findOrFail($id);
        $data = $request->except('main_photo');

        if ($request->hasFile('main_photo')) {
            if ($room->main_photo) {
                Storage::delete('public/' . ltrim($room->main_photo, '/storage/'));
            }
            $path = $request->file('main_photo')->store('images/rooms', 'public');
            $data['main_photo'] = 'storage/' . $path;
            Log::info('Room main photo updated', ['path' => $path]);
        }

        $room->update($data);

        Log::info('Room updated', ['room_id' => $room->id]);
        return response()->json($room);
    }

    public function destroy($id)
    {
        $room = Room::findOrFail($id);

        if ($room->main_photo) {
            Storage::delete('public/' . ltrim($room->main_photo, '/storage/'));
        }

        $room->delete();

        Log::info('Room deleted', ['room_id' => $id]);
        return response()->json(null, 204);
    }

    public function toggleFeatured($id)
    {
        $room = Room::findOrFail($id);
        $room->featured = !$room->featured;
        $room->save();

        Log::info('Toggled featured for room', ['room_id' => $id, 'featured' => $room->featured]);
        return response()->json($room);
    }
}
