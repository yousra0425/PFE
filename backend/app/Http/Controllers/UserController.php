<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
     
    //List all users (admin)
    public function index() {
        return response()->json(User::all());
    }

    //show single user
    public function show($id) {
        $user = User::find($id); 
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        return response()->json($user);
    }

    //create new user
    public function store(Request $request) {

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'role'     => 'required|in:admin,instructor,user',
        ]);

        $user = User::create([
            'full_name'  => $request->input('full_name'),
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'country' => $request->country,
            'city' => $request->city,
            'role'     => $request->role,

        ]);

        return response()->json(['message' => 'User created', 'user' => $user], 201);

    }

    //update user
    public function update (Request $request, $id) {
        $user = User::find($id); 

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        if (Auth::user()->id !== $user->id && Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' .$id,
            'password' => 'required|string|min:8',
            'country' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'role'     => 'required|in:admin,instructor,user',
        ]);

        $user->update([
            'full_name' => $request->input('full_name'),
            'email'    => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'country' => $request->country ?? $user->country,
            'city' => $request->city ?? $user->city,
            'role' => $request->role,
        ]);

        return response()->json(['message' => 'User updated', 'user' => $user]);
    }

    //delete user (admin)
    public function destroy($id) {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'User deleted']);


    }


}
