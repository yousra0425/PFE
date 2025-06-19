<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\FirebaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    protected $firebase;

    public function __construct(FirebaseService $firebase)
    {
        $this->firebase = $firebase;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $bookings = $user->role === 'tutor'
    ? Booking::where('tutor_id', $user->id)->with('user')->get() // ✅ student should be "user"
    : Booking::where('user_id', $user->id)->with('tutor')->get(); // ✅ tutor here is fine


        return response()->json($bookings);
    }

    public function adminIndex()
    {
        if (Auth::user()->role !== 'admin') {
            return response()->json(['error' => 'Forbidden'], 403);
        }

        $bookings = Booking::with(['user', 'tutor'])->latest()->get();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'tutor_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'message' => 'nullable|string|max:1000',
        ]);

        // Fixed tutor working hours: 09:00 to 18:00
        $start = Carbon::createFromFormat('H:i', $validated['start_time']);
        $end = Carbon::createFromFormat('H:i', $validated['end_time']);

        $workStart = Carbon::createFromFormat('H:i', '09:00');
        $workEnd = Carbon::createFromFormat('H:i', '18:00');

        if ($start->lt($workStart) || $end->gt($workEnd)) {
            return response()->json(['message' => 'Booking must be within working hours: 09:00 to 18:00'], 422);
        }

        // Check for overlapping bookings
        $hasConflict = Booking::where('tutor_id', $validated['tutor_id'])
            ->where('date', $validated['date'])
            ->where(function ($query) use ($validated) {
                $query->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhere(function ($q) use ($validated) {
                          $q->where('start_time', '<=', $validated['start_time'])
                            ->where('end_time', '>=', $validated['end_time']);
                      });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json(['message' => 'Tutor already has a booking during this time.'], 422);
        }

        // Create the booking
        $booking = Booking::create([
            'user_id' => $user->id,
            'tutor_id' => $validated['tutor_id'],
            'date' => $validated['date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        // Notify tutor
        $tutor = $booking->tutor;
        if ($tutor && $tutor->fcm_token) {
            $this->firebase->sendNotification(
                $tutor->fcm_token,
                'New Booking Request',
                "You have a new booking request from {$user->name}.",
                ['booking_id' => $booking->id]
            );
        }

        return response()->json($booking->load(['user', 'tutor']), 201);

    }

    public function reschedule(Request $request, $id)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'date' => 'required|date|after:now',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $booking = Booking::findOrFail($id);

        if ($user->id !== $booking->user_id && $user->id !== $booking->tutor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->date = $request->date;
        $booking->start_time = $request->start_time;
        $booking->end_time = $request->end_time;
        $booking->save();

        return response()->json(['message' => 'Booking rescheduled successfully', 'booking' => $booking]);
    }

    public function respond(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:accepted,refused',
        ]);

        $booking = Booking::findOrFail($id);

        if ($request->user()->id !== $booking->tutor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->status = $request->status;
        $booking->save();

        return response()->json(['message' => 'Booking status updated successfully.', 'booking' => $booking]);
    }

    public function accept(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($request->user()->id !== $booking->tutor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $booking->status = 'accepted';
        $booking->save();

        $student = $booking->user;
        if ($student && $student->fcm_token) {
            $this->firebase->sendNotification(
                $student->fcm_token,
                'Booking Accepted',
                "Your booking with tutor {$booking->tutor->name} has been accepted.",
                ['booking_id' => $booking->id]
            );
        }

        return response()->json(['message' => 'Booking accepted', 'booking' => $booking]);
    }

    public function decline(Request $request, $id)
    {
        $booking = Booking::findOrFail($id);

        if ($request->user()->id !== $booking->tutor_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $student = $booking->user;
        if ($student && $student->fcm_token) {
            $this->firebase->sendNotification(
                $student->fcm_token,
                'Booking Declined',
                "Your booking with tutor {$booking->tutor->name} was declined.",
                ['booking_id' => $booking->id]
            );
        }

        $booking->delete();

        return response()->json(['message' => 'Booking declined and deleted']);
    }
}
