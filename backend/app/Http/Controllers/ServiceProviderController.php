<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class ServiceProviderController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'service_type' => 'required|string',
            'experience' => 'required|integer',
            'description' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'location' => 'required|string',
            'rating' => 'nullable|numeric|min:0|max:5',
            'cin_image' => 'required|file|mimes:pdf,jpg,jpeg,png',
        ]);

        // Geocode location to get coordinates
        $coordinates = $this->getCoordinatesFromLocation($request->location);

        $path = $request->file('cin_image')->store('cin_images', 'public');

        $provider = ServiceProvider::create([
            'user_id' => Auth::id(),
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'experience' => $request->experience,
            'description' => $request->description,
            'phone' => $request->phone,
            'email' => $request->email,
            'location' => $request->location,
            'latitude' => $coordinates['latitude'] ?? null,
            'longitude' => $coordinates['longitude'] ?? null,
            'rating' => $request->rating,
            'cin_image' => $path,
            'is_verified' => false,
        ]);
        
        $provider->services()->attach($request->services);
        

        return response()->json(['message' => 'Application submitted. Await admin approval.'], 201);
    }

    // Admin approves provider
    public function verify($id)
    {
        $provider = ServiceProvider::findOrFail($id);
        $provider->is_verified = true;
        $provider->save();

        return response()->json(['message' => 'Provider verified.']);
    }

    // Get all verified providers with full details (for /verified route)
    public function verifiedProviders()
    {
        $providers = ServiceProvider::with('user:id,first_name,last_name')
            ->where('is_verified', true)
            ->get();

        return response()->json($providers);
    }

    // Get verified providers with minimal info for map
    public function verifiedProvidersForMap(Request $request){
    $query = ServiceProvider::with('user:id,first_name,last_name')
                ->where('is_verified', true);

    // If service_type param exists, filter providers by it
    if ($request->has('service_type')) {
        $serviceType = $request->input('service_type');
        $query->where('service_type', $serviceType);
    }

    $providers = $query->get(['id', 'user_id', 'service_type', 'phone', 'email', 'location', 'latitude', 'longitude', 'rating']);

    $result = $providers->map(function($provider) {
        return [
            'id' => $provider->id,
            'name' => $provider->user->first_name . ' ' . $provider->user->last_name,
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
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


    // Helper to get coordinates from Google Maps API
    private function getCoordinatesFromLocation($location) 
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');  // Store your API key in .env file
        $response = Http::withoutVerifying()->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'address' => $location,
            'key' => $apiKey,
        ]);

        if ($response->successful() && !empty($response['results'])) {
            $coords = $response['results'][0]['geometry']['location'];
            return [
                'latitude' => $coords['lat'],
                'longitude' => $coords['lng'],
            ];
        }

        return null;  // Could not geocode
    }
}
