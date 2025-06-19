<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // List all users (admin)
    public function index()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }
        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json(User::all());
    }

    // Show single user
    public function show($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->id !== $targetUser->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($targetUser);
    }

    // Create new user
    public function store(Request $request)
    {
        // You might want to restrict this route to admins only with middleware
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'telephone'  => 'required|string|max:20',
            'email'      => 'required|string|email|unique:users',
            'password'   => 'required|string|min:8',
            'date_of_birth' => 'sometimes|date',
            'city' => 'nullable|string|max:255',
            'address'    => 'required|string|max:255',
            'role'       => 'required|in:admin,provider,client',
        ]);

        $user = User::create([
            'first_name'    => $request->first_name,
            'last_name'     => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'telephone'     => $request->telephone,
            'city'          => $request->city,
            'address'       => $request->address,
            'role'          => $request->role,
        ]);

        $user->makeHidden(['password']);

        return response()->json(['message' => 'User created', 'user' => $user], 201);
    }

    // Update user
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->id !== $targetUser->id && $user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'telephone'  => 'sometimes|string|max:20',
            'email'      => 'sometimes|string|email|unique:users,email,' . $targetUser->id,
            'password'   => 'nullable|string|min:8',
            'date_of_birth' => 'sometimes|date',
            'address'    => 'sometimes|string|max:255',
            'city'       => 'nullable|string|max:255',
            'role'       => 'required|in:admin,provider,client',
        ]);

        $targetUser->update([
            'first_name'    => $request->first_name ?? $targetUser->first_name,
            'last_name'     => $request->last_name ?? $targetUser->last_name,
            'email'         => $request->email ?? $targetUser->email,
            'password'      => $request->password ? Hash::make($request->password) : $targetUser->password,
            'telephone'     => $request->telephone ?? $targetUser->telephone,
            'date_of_birth' => $request->date_of_birth ?? $targetUser->date_of_birth,
            'address'       => $request->address ?? $targetUser->address,
            'city'          => $request->city ?? $targetUser->city,
            'role'          => $request->role,
        ]);

        return response()->json(['message' => 'User updated', 'user' => $targetUser]);
    }

    // Delete user (admin only)
    public function destroy($id)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $targetUser = User::find($id);
        if (!$targetUser) {
            return response()->json(['message' => 'User not found'], 404);
        }

        $targetUser->delete();

        return response()->json(['message' => 'User deleted']);
    }

    // Verify service provider status
    public function verifytutor(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if ($user->role !== 'tutor') {
            return response()->json(['error' => 'User is not a tutor'], 400);
        }

        $user->is_verified = $request->input('is_verified', true); // default to true if not provided
        $user->save();

        return response()->json(['message' => 'Tutor verification status updated.']);
    }

    // Update authenticated user location
    public function updateLocation(Request $request)
    {
        $user = \App\Models\User::find(Auth::id());

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $request->validate([
            'latitude'  => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user->latitude  = $request->latitude;
        $user->longitude = $request->longitude;
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Location updated successfully.',
        ]);
    }

    public function getTutorsWithLocation()
{
    $tutors = User::where('role', 'tutor')
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->get([
            'id',
            'first_name',
            'last_name',
            'latitude',
            'longitude',
            'city',       // optional: add fields as needed
            'address'     // optional: add fields as needed
        ]);

    return response()->json([
        'status' => 'success',
        'data' => $tutors
    ]);
}

public function getTutorZones()
{
    $tutors = User::where('role', 'tutor')
        ->whereNotNull('working_latitude')
        ->whereNotNull('working_longitude')
        ->whereNotNull('working_radius')
        ->get([
            'id',
            'first_name',
            'last_name',
            'working_latitude',
            'working_longitude',
            'working_radius'
        ]);

    return response()->json([
        'status' => 'success',
        'data' => $tutors
    ]);
}

public function updateFcmToken(Request $request)
{
    $request->validate([
        'fcm_token' => 'required|string',
    ]);

    $user = $request->user();
    $user->fcm_token = $request->fcm_token;
    $user->save();

    return response()->json(['message' => 'FCM token updated']);
}



}
