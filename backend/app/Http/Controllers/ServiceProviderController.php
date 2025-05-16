<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ServiceProviderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_type' => 'required|string',
            'experience' => 'required|integer',
            'description' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'location' => 'required|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'rating' => 'nullable|numeric|min:0|max:5',
            'cin_image' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        $path = $request->file('cin_image')->store('cin_images', 'public');

        ServiceProvider::create([
            'user_id' => Auth::id(),
            'service_type' => $request->service_type,
            'experience' => $request->experience,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'rating' => $request->rating,
            'cin_image' => $path,
            'is_verified' => false,
        ]);

        return response()->json(['message' => 'Application submitted. Await admin approval.'], 201);
    }

    //admin approving provider
    public function verify($id)
    {
        $provider = ServiceProvider::findOrFail($id);
        $provider->is_verified = true;
        $provider->save();

        return response()->json(['message' => 'Provider verified.']);
    }

    //get verified providers on map
    public function verifiedProvidersForMap()
{
    $providers = ServiceProvider::with('user:id,name') // eager load user name only
        ->where('is_verified', true)
        ->get(['id', 'user_id', 'service_type', 'phone', 'email', 'location', 'latitude', 'longitude', 'rating']);

    $result = $providers->map(function($provider) {
        return [
            'id' => $provider->id,
            'name' => $provider->user->name,
            'service_type' => $provider->service_type,
            'phone' => $provider->phone,
            'email' => $provider->email,
            'location' => $provider->location,
            'latitude' => $provider->latitude,
            'longitude' => $provider->longitude,
            'rating' => $provider->rating,
        ];
    });

    return response()->json($result);
}
}

