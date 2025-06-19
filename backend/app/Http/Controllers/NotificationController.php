<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Kreait\Firebase\Factory;

class NotificationController extends Controller
{
    public function sendNotification(Request $request)
    {
        // Validate incoming request
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',       // The user to receive notification
            'tutor_id' => 'required|integer|exists:users,id',      // The tutor who accepted
            'booking_id' => 'required|integer',
        ]);

        $user = User::find($request->user_id);
        $tutor = User::find($request->tutor_id);

        if (!$user->fcm_token) {
            return response()->json(['error' => 'User does not have a valid FCM token'], 400);
        }

        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase-service-account.json'));
        $messaging = $factory->createMessaging();

        $tutorFullName = trim($tutor->first_name . ' ' . $tutor->last_name);

        $message = [
            'token' => $user->fcm_token,
            'notification' => [
                'title' => 'Booking Update',
                'body' => "Tutor {$tutorFullName} has accepted your request",
            ],
            'data' => [
                'booking_id' => (string)$request->booking_id,
                'status' => 'accepted',
            ],
        ];

        try {
            $messaging->send($message);
            return response()->json(['message' => 'Notification sent successfully']);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
