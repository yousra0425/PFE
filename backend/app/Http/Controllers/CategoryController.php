<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Category;

class CategoryController extends Controller
{
    public function index() {
        $categories = Category::all();
        return response()->json($categories);
    }
    
    public function store(Request $request)
    {
        // Validate the request data
        $validatedData = $request->validate([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Create a new category
        $category = Category::create($validatedData);

        return response()->json($category, 201);
    }

    public function show($id) {
        $category = Category::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id) {
        $category = Category::findOrFail($id);

        $data = request()-> validated([
            'label' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
        ]);

        $category->update($data);
        return response()->json($category);
    }

    public function destroy($id) {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
