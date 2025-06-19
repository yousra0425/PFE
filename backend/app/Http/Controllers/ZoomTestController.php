<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ZoomService;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;


class ZoomTestController extends Controller
{
    protected $zoomService;

    public function __construct(ZoomService $zoomService)
    {
        $this->zoomService = $zoomService;
    }

    // Generate Zoom access token (for testing)
    public function test()
    {
        try {
            $token = $this->zoomService->generateAccessToken();
            return response()->json(['token' => $token]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Just create a Zoom meeting (no message sending)
    public function createMeeting()
    {
        try {
            $meeting = $this->zoomService->createMeeting('Math Tutoring with Student');
            return response()->json([
                'join_url' => $meeting['join_url'],
                'start_url' => $meeting['start_url'],
                'meeting_id' => $meeting['id']
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Create a Zoom meeting AND send the join link in a chat message
    public function createMeetingAndSendLink(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);
    
        $studentId = $request->input('user_id');  // use input() to be sure
        $tutorId = Auth::id();
    
        if (!$studentId) {
            return response()->json(['error' => 'student user_id is missing'], 400);
        }
    
        try {
            $meeting = $this->zoomService->createMeeting('Tutoring Session with Student');
    
            Message::create([
                'user_id' => $studentId,
                'tutor_id' => $tutorId,
                'text' => 'Here is your Zoom link for our tutoring session: ' . $meeting['join_url'],
                'sender' => optional(Auth::user())->name ?? 'Unknown',

            ]);
    
            return response()->json([
                'content' => 'Zoom meeting created and link sent to student.',
                'join_url' => $meeting['join_url'],
                'start_url' => $meeting['start_url'],
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    

}
