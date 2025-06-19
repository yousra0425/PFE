<?php

namespace App\Http\Controllers;

use App\Models\Subcategory;
use App\Models\Category;
use Illuminate\Http\Request;

class SubcategoryController extends Controller
{
    // List all subcategories with their categories
    public function index()
    {
        $subcategories = Subcategory::with(['category', 'teachingLevel'])->get();
        return response()->json($subcategories);
    }
    

    // Show a specific subcategory with its category
    public function show($id)
    {
        $subcategory = Subcategory::with('category')->findOrFail($id);
        return response()->json($subcategory);
    }

    // Create a new subcategory
    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'teaching_level_id' => 'nullable|exists:teaching_levels,id',
        ]);
        

        $subcategory = Subcategory::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'teaching_level_id' => $request->teaching_level_id,
        ]);
        

        return response()->json([
            'message' => 'Subcategory created successfully',
            'subcategory' => $subcategory
        ], 201);
    }

    // Update a subcategory
    public function update(Request $request, $id)
    {
        $subcategory = Subcategory::findOrFail($id);

        $request->validate([
            'category_id' => 'sometimes|exists:categories,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'teaching_level_id' => 'sometimes|exists:teaching_levels,id',
        ]);

        $subcategory->update($request->only(['category_id', 'name', 'description', 'teaching_level_id']));

        return response()->json([
            'message' => 'Subcategory updated successfully',
            'subcategory' => $subcategory
            
        ]);
    }

    // Delete a subcategory
    public function destroy($id)
    {
        $subcategory = Subcategory::findOrFail($id);
        $subcategory->delete();

        return response()->json([
            'message' => 'Subcategory deleted successfully'
        ]);
    }

    public function getFilteredSubcategories(Request $request)
    {
        $request->validate([
            'categoryId' => 'required|exists:categories,id',
            'levelId' => 'required|exists:teaching_levels,id',
        ]);
    
        $subcategories = Subcategory::with(['category', 'teachingLevel'])
            ->where('category_id', $request->categoryId)
            ->where('teaching_level_id', $request->levelId)
            ->get();
    
        return response()->json($subcategories);
    }
    
    

}

