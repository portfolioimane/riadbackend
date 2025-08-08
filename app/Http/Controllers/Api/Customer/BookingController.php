<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Booking;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

use App\Mail\BookingConfirmed;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    /**
     * Get unavailable dates for a specific room.
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

        // Get all bookings for the room that are not cancelled
        $bookings = Booking::where('room_id', $roomId)
            ->where('status', '!=', 'Cancelled')
            ->get();

        $unavailableDates = [];

        foreach ($bookings as $booking) {
            $bookingStart = Carbon::parse($booking->check_in);
            $bookingEnd = Carbon::parse($booking->check_out); // include checkout date

            // Add all dates from check_in to check_out (inclusive)
            for ($date = $bookingStart->copy(); $date->lte($bookingEnd); $date->addDay()) {
                $unavailableDates[] = $date->toDateString();
            }
        }

        // Remove duplicates and reindex
        $unavailableDates = array_values(array_unique($unavailableDates));

        Log::info("Unavailable dates for room {$roomId}: " . json_encode($unavailableDates));

        return response()->json($unavailableDates);
    }

    /**
     * Create a new booking.
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

    // Log check-in and check-out dates
    Log::info('Received booking dates:', [
        'check_in' => $checkIn->toDateString(),
        'check_out' => $checkOut->toDateString(),
    ]);

        // Conflict check with inclusive date ranges (checkout date included)
$conflict = Booking::where('room_id', $validated['room_id'])
    ->where('status', '!=', 'Cancelled')
    ->where(function ($query) use ($checkIn, $checkOut) {
        $query->where('check_in', '<=', $checkOut)
              ->where('check_out', '>=', $checkIn);
    })
    ->exists();



        if ($conflict) {
            return response()->json(['message' => 'Room is already booked for the selected dates'], 409);
        }

        // Determine booking status and paid amount based on payment method
        $status = 'Pending';
        $paidAmount = 0;

     

        // Create the booking
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




public function updatePaymentStatus(Request $request, $id)
{
    Log::info("UpdatePaymentStatus called for booking ID: {$id}", [
        'request_data' => $request->all(),
    ]);

    $booking = Booking::with('room')->findOrFail($id);
    Log::info("Booking loaded", ['booking' => $booking->toArray()]);
    Log::info("Room relation loaded", ['room' => $booking->room ? $booking->room->toArray() : null]);

    $method = strtolower($booking->payment_method);

    if ($method !== 'cash') {
        if ($request->has('payment_fee_status')) {
            $booking->payment_fee_status = $request->payment_fee_status;
            Log::info("Payment fee status updated to: {$request->payment_fee_status}");
        }

        if ($request->has('paid_fee')) {
            $booking->paid_amount = $request->paid_fee;
            Log::info("Paid amount updated to: {$request->paid_fee}");
        }

        $booking->save();

        // Refresh and reload room after update
        $booking->refresh();
        $booking->load('room');

        Log::info("Booking after update", ['booking' => $booking->toArray()]);
        Log::info("Room after update", ['room' => $booking->room ? $booking->room->toArray() : null]);
    }

    try {
        Mail::to($booking->email)->queue(new BookingConfirmed($booking));
        Log::info("Booking notification email queued for {$booking->email}");
    } catch (\Exception $e) {
        Log::error("Failed to queue booking notification email: " . $e->getMessage());
    }

    return response()->json([
        'message' => 'Booking updated and email sent successfully.',
        'booking' => $booking,
    ]);
}





}
