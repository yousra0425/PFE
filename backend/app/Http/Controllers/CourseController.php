<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Models\Course;

class CourseController extends Controller
{
    public function index()
    {
        return Course::with('category', 'user')->get();
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'title' => 'required|string',
            'description' => 'nullable|string',
            'image' => 'required|url',
            'price' => 'required|numeric',
            'status' => 'required|in:Pending,Approved,Rejected,Published',
            'category_id' => 'required|exists:categories,id',
        ]);

        $data['user_id'] = $user->id;

        return Course::create($data);
    }

    public function show($id)
    {
        return Course::with('category', 'user')->findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $course = Course::findOrFail($id);

         if ($user->id !== $course->user_id) {
        return response()->json(['error' => 'Unauthorized'], 403);
    }


        $data = $request->validate([
            'title' => 'sometimes|string',
            'description' => 'nullable|string',
            'image' => 'sometimes|url',
            'price' => 'sometimes|numeric',
            'status' => 'required|in:Pending,Approved,Rejected,Published',
            'category_id' => 'sometimes|exists:categories,id',
        ]);

        $course->update($data);
        return $course;
    }

    public function destroy($id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        $course = Course::findOrFail($id);

        if ($user->id !== $course->user_id) {
           return response()->json(['error' => 'Unauthorized'], 403);
        }

        $course->delete();
        return response()->json(['message' => 'Course deleted']);
    }
}
