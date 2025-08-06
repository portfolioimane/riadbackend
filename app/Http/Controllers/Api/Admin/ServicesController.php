<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    public function index()
    {
        $services = Service::all();
        Log::info('Fetched all services', ['count' => $services->count()]);
        return response()->json($services);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'duration' => 'required|integer',
            'category' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for service store', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $request->all();

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('images/services', 'public');
            $data['image'] = 'storage/' . $path;
            Log::info('Service image uploaded', ['path' => $path]);
        }

        $service = Service::create($data);

        Log::info('Service created', ['service_id' => $service->id]);
        return response()->json($service, 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        Log::info('Fetched service by ID', ['service_id' => $id]);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric',
            'duration' => 'sometimes|required|integer',
            'category' => 'sometimes|required|string|max:255',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($validator->fails()) {
            Log::error('Validation failed for service update', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $service = Service::findOrFail($id);
        $data = $request->all();

        if ($request->hasFile('image')) {
            if ($service->image) {
                Storage::delete('public/' . ltrim($service->image, '/storage/'));
            }
            $path = $request->file('image')->store('images/services', 'public');
            $data['image'] = 'storage/' . $path;
            Log::info('Service image replaced', ['path' => $path]);
        }

        $service->update($data);

        Log::info('Service updated', ['service_id' => $id]);
        return response()->json($service);
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        if ($service->image) {
            Storage::delete('public/' . ltrim($service->image, '/storage/'));
        }

        $service->delete();

        Log::info('Service deleted', ['service_id' => $id]);
        return response()->json(null, 204);
    }

    public function toggleFeatured($id)
    {
        $service = Service::findOrFail($id);
        $service->featured = !$service->featured;
        $service->save();

        Log::info('Toggled featured for service', ['service_id' => $id, 'featured' => $service->featured]);
        return response()->json($service);
    }
}
