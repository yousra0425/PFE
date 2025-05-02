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
        if (Auth::user()->role !== 'admin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
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
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'telephone'  => 'required|string|max:20',
            'email'      => 'required|string|email|unique:users',
            'password' => 'required|string|min:8',
            'date_of_birth' => 'sometimes|date',
            'city' => 'nullable|string|max:255',
            'address'    => 'required|string|max:255',
            'role'     => 'required|in:admin,provider,client',
        ]);

        $user = User::create([
            'first_name'  => $request->first_name,
            'last_name'  => $request->last_name,
            'date_of_birth' => $request->date_of_birth,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'telephone' => $request->telephone,
            'city' => $request->city,
            'address' => $request->address,
            'role'     => $request->role,

        ]);

        $user->makeHidden(['password']);

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
            'first_name' => 'sometimes|string|max:255',
            'last_name'  => 'sometimes|string|max:255',
            'telephone'  => 'sometimes|string|max:20',
            'email'      => 'sometimes|string|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'date_of_birth' => 'sometimes|date',
            'address'    => 'sometimes|string|max:255',
            'city' => 'nullable|string|max:255',
            'role'     => 'required|in:admin,provider,client',
        ]);

        $user->update([
            'first_name'  => $request->first_name,
            'last_name'  => $request->last_name,
            'email'    => $request->email,
            'password' => $request->password ? Hash::make($request->password) : $user->password,
            'telephone' => $request->telephone ?? $user->telephone,
            'date_of_birth' => $request->date_of_birth ?? $user->date_of_birth,
            'address' => $request->address ?? $user->address,
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
