<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Booking;

class BookingController extends Controller
{
    // Get all bookings for the authenticated user
    public function index()
    {
        $bookings = Booking::where('user_id', Auth::id())
                         ->orderBy('date')
                         ->orderBy('time')
                         ->get();

        return response()->json($bookings);
    }

    // Store a new booking
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_name' => 'required|string|max:255',
            'provider_name' => 'required|string|max:255',
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        $booking = new Booking($validated);
        $booking->user_id = Auth::id();
        $booking->save();

        return response()->json($booking, 201);
    }

    // Show a single booking
    public function show($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($booking);
    }

    // Update a booking
    public function update(Request $request, Booking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'service_name' => 'sometimes|required|string|max:255',
            'provider_name' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date|after_or_equal:today',
            'time' => 'sometimes|required|date_format:H:i',
        ]);

        $booking->update($validated);

        return response()->json($booking);
    }

    // Delete a booking
    public function destroy($id)
    {
        $booking = Booking::findOrFail($id);

        if ($booking->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();

        return response()->json(['message' => 'Booking deleted successfully']);
    }

    // Get bookings by date
    public function getByDate(Request $request)
    {
        $request->validate([
            'date' => 'required|date'
        ]);

        $bookings = Booking::where('user_id', Auth::id())
                         ->where('date', $request->date)
                         ->orderBy('time')
                         ->get();

        return response()->json($bookings);
    }

    // Reschedule a booking (single time field)
    public function reschedule(Request $request, $id)
    {
        $booking = Booking::where('user_id', $request->user()->id)->findOrFail($id);

        $validated = $request->validate([
            'date' => 'required|date|after_or_equal:today',
            'time' => 'required|date_format:H:i',
        ]);

        $booking->date = $validated['date'];
        $booking->time = $validated['time'];
        $booking->save();

        return response()->json([
            'message' => 'Booking rescheduled successfully',
            'booking' => $booking
        ]);
    }
}
