<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServicesController extends Controller
{
    // Fetch all services
    public function getAllServices()
    {
        $services = Service::all();

        Log::info('All services fetched: ', $services->toArray());

        return response()->json($services, 200);
    }

    // Fetch a specific service by ID
    public function show($id)
    {
        $service = Service::findOrFail($id);

        Log::info('Service fetched: ', $service->toArray());

        return response()->json($service, 200);
    }

    // Fetch featured services
    public function getFeaturedServices()
    {
        $featuredServices = Service::where('featured', true)
            ->latest()
            ->take(4)
            ->get();

        Log::info('Featured services fetched: ', $featuredServices->toArray());

        return response()->json($featuredServices, 200);
    }

    // Fetch popular services (e.g., most booked)
public function getPopularServices()
{
    $popularServices = Service::latest()
        ->limit(8)
        ->get();

    Log::info('Latest 8 services fetched:', $popularServices->toArray());

    return response()->json($popularServices, 200);
}

}
