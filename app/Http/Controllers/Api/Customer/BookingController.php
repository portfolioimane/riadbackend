<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BookingController extends Controller
{
    /**
     * Get unavailable dates for a specific room within a given date range.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
 public function getUnavailableDates(Request $request)
{
    $roomId = $request->query('room_id');

    if (!$roomId) {
        return response()->json(['message' => 'room_id is required'], 400);
    }

    // Fetch all bookings for this room that are not cancelled
    $bookings = Booking::where('room_id', $roomId)
        ->where('status', '!=', 'Cancelled')
        ->get();

    $unavailableDates = [];
    foreach ($bookings as $booking) {
        $bookingStart = Carbon::parse($booking->check_in);
        $bookingEnd = Carbon::parse($booking->check_out);

        // Add all dates from check_in to check_out (inclusive)
        for ($date = $bookingStart->copy(); $date->lte($bookingEnd); $date->addDay()) {
            $unavailableDates[] = $date->toDateString();
        }
    }

    // Remove duplicates if any
    $unavailableDates = array_values(array_unique($unavailableDates));

    Log::info("Unavailable dates for room {$roomId}: " . json_encode($unavailableDates));

    return response()->json($unavailableDates);
}


    /**
     * Create a booking.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createBooking(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:15',
            'room_id' => 'required|exists:rooms,id',
            'check_in' => 'required|date|before:check_out',
            'check_out' => 'required|date|after:check_in',
            'adults' => 'integer|min:1',
            'children' => 'integer|min:0',
            'payment_method' => 'required|string',
            'total' => 'required|numeric|min:0',
        ]);

        $checkIn = Carbon::parse($validated['check_in']);
        $checkOut = Carbon::parse($validated['check_out']);

        // Check if there is an overlapping booking for this room
        $conflict = Booking::where('room_id', $validated['room_id'])
            ->where('status', '!=', 'Cancelled')
            ->where(function ($query) use ($checkIn, $checkOut) {
                $query->whereBetween('check_in', [$checkIn, $checkOut])
                      ->orWhereBetween('check_out', [$checkIn, $checkOut])
                      ->orWhere(function ($query) use ($checkIn, $checkOut) {
                          $query->where('check_in', '<=', $checkIn)
                                ->where('check_out', '>=', $checkOut);
                      });
            })
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Room is already booked for the selected dates'], 409);
        }

        $status = 'Pending';
        $paidAmount = 0;

        if (in_array(strtolower($validated['payment_method']), ['stripe', 'paypal'])) {
            $status = 'Confirmed';
            $paidAmount = $validated['total'];
        }

        $booking = Booking::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'room_id' => $validated['room_id'],
            'check_in' => $validated['check_in'],
            'check_out' => $validated['check_out'],
            'adults' => $validated['adults'] ?? 1,
            'children' => $validated['children'] ?? 0,
            'payment_method' => $validated['payment_method'],
            'total' => $validated['total'],
            'status' => $status,
            'paid_amount' => $paidAmount,
        ]);

        return response()->json([
            'message' => 'Booking created successfully!',
            'booking' => $booking,
        ], 201);
    }
}
