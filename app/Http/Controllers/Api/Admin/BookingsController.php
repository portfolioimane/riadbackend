<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingsController extends Controller
{
    /**
     * Get all bookings.
     */
    public function index()
    {
        Log::info('Fetching all bookings');

        try {
            $bookings = Booking::with('room')->get(); // Make sure Booking model has a `room()` relationship
            Log::info('Bookings fetched successfully', ['bookings_count' => $bookings]);
        } catch (\Exception $e) {
            Log::error('Error fetching bookings: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch bookings'], 500);
        }

        return response()->json(['bookings' => $bookings], 200);
    }

    /**
     * Get a specific booking.
     */
    public function show($bookingId)
    {
        Log::info('Fetching booking details', ['booking_id' => $bookingId]);

        try {
            $booking = Booking::with('room')->find($bookingId);

            if (!$booking) {
                Log::warning('Booking not found', ['booking_id' => $bookingId]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            Log::info('Booking found', ['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            Log::error('Error fetching booking: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to fetch booking'], 500);
        }

        return response()->json(['booking' => $booking], 200);
    }

    /**
     * Update a booking.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',
            'room_id' => 'nullable|exists:rooms,id',
            'payment_method' => 'nullable|string',
            'status' => 'nullable|in:Pending,Confirmed,Cancelled,Completed',
            'check_in' => 'nullable|date',
            'check_out' => 'nullable|date',
            'adults' => 'nullable|integer|min:1',
            'children' => 'nullable|integer|min:0',
            'total' => 'nullable|numeric',
            'paid_amount' => 'nullable|numeric',
        ]);

        $booking = Booking::find($id);

        if (!$booking) {
            return response()->json(['error' => 'Booking not found'], 404);
        }

        $booking->update($request->all());
        $booking->load('room'); // eager load room relationship

        return response()->json(['booking' => $booking]);
    }

    /**
     * Delete a booking.
     */
    public function destroy($bookingId)
    {
        Log::info('Deleting booking', ['booking_id' => $bookingId]);

        try {
            $booking = Booking::find($bookingId);

            if (!$booking) {
                Log::warning('Booking not found for deletion', ['booking_id' => $bookingId]);
                return response()->json(['error' => 'Booking not found'], 404);
            }

            $booking->delete();
            Log::info('Booking deleted successfully', ['booking_id' => $bookingId]);
        } catch (\Exception $e) {
            Log::error('Error deleting booking: ' . $e->getMessage());
            return response()->json(['error' => 'Unable to delete booking'], 500);
        }

        return response()->json(['message' => 'Booking deleted successfully'], 200);
    }
}
