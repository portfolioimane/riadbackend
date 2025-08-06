<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function summary(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        // Use created_at instead of 'date'
        $totalAppointments = Booking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $totalRevenue = Booking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->sum('total');

        $newCustomers = Booking::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->distinct('email')
            ->count('email');

        // Adjust max capacity if needed (e.g., 100 bookings per month)
        $maxCapacity = 100;
        $occupancyRate = $maxCapacity > 0
            ? round(($totalAppointments / $maxCapacity) * 100, 2)
            : 0;

        return response()->json([
            'totalAppointments' => $totalAppointments,
            'totalRevenue' => $totalRevenue,
            'newCustomers' => $newCustomers,
            'occupancyRate' => $occupancyRate,
        ]);
    }

    public function popularRooms(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        $rooms = Booking::select('room_id', DB::raw('COUNT(*) as timesBooked'), DB::raw('SUM(total) as revenue'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('room_id')
            ->with('room:id,room_name') // Load room name
            ->orderByDesc('timesBooked')
            ->get()
            ->map(function ($booking) {
                return [
                    'roomName' => $booking->room ? $booking->room->room_name : 'Unknown',
                    'timesBooked' => $booking->timesBooked,
                    'revenue' => $booking->revenue,
                ];
            });

        return response()->json($rooms);
    }

    public function topClients(Request $request)
    {
        $year = $request->query('year', date('Y'));
        $month = $request->query('month', date('m'));

        $clients = Booking::select('email', DB::raw('COUNT(*) as visits'))
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->groupBy('email')
            ->orderByDesc('visits')
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'clientName' => $booking->email ?? 'Unknown',
                    'visits' => $booking->visits,
                ];
            });

        return response()->json($clients);
    }
}
