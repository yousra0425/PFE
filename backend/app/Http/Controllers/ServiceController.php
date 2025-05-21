<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Service;

class ServiceController extends Controller {

    public function index(){

        $services = Service::with('category')->get();
        return response()->json($services);
    }

    public function store(Request $request){

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $service = Service::create($validatedData);
        return response()->json($service, 201);
    }

    public function storeByCategory(Request $request, $id)
{
    $validatedData = $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'nullable|numeric|min:0',
    ]);

    $validatedData['category_id'] = $id;

    $service = Service::create($validatedData);

    return response()->json($service, 201);
}


    public function show($id) {
        $service = Service::with('category')->findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id){
        $service = Service::findOrFail($id);

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
        ]);

        $service->update($validatedData);
        return response()->json($service);
    }

    public function getByCategory($id)
    {
        $services = Service::with('category')->where('category_id', $id)->get();
        return response()->json($services);
    }
       


    public function destroy($id){
        $service = Service::findOrFail($id);
        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }

} 