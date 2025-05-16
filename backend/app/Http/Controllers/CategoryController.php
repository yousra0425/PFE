<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    // Get all categories
    public function index()
    {
        $categories = Category::all();
        return response()->json($categories);
    }

    // Store a new category
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create the category
        $category = Category::create($validatedData);

        return response()->json($category, 201);
    }

    // Show a single category
    public function show($id)
    {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    // Update a category
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($validatedData);

        return response()->json($category);
    }

    // Delete a category
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }

    public function getCategoriesWithServices()
    {
        $categories = Category::with('services')->get();

        return response()->json($categories);
    }
}
