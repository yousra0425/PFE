<?php

namespace App\Http\Controllers;

use App\Models\TeachingLevel;
use Illuminate\Http\Request;

class TeachingLevelController extends Controller
{
    // Get all teaching levels
    public function index()
    {
        $levels = TeachingLevel::all();

        return response()->json([
            'success' => true,
            'data' => $levels
        ]);
    }

    // Create a new teaching level
    public function store(Request $request)
    {
        // Validate input
        $request->validate([
            'name' => 'required|string|unique:teaching_levels,name|max:255',
        ]);

        // Create new teaching level
        $level = TeachingLevel::create([
            'name' => $request->name,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Teaching level created successfully.',
            'data' => $level
        ], 201);
    }
}
