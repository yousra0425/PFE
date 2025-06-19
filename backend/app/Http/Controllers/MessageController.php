<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Events\MessageSent;

class MessageController extends Controller
{
    // Get messages between authenticated user and another user
    public function index($userId)
    {
        $authUserId = Auth::id();

        $messages = Message::where(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $authUserId)
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($authUserId, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $authUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages);
    }

    // Send a new message
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message, 201);
    }
}
