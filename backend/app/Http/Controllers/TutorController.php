<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\Tutor;
use Illuminate\Support\Str;

use Carbon\Carbon; 
use App\Models\Booking;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class TutorController extends Controller
{
    public function index()
{
    $tutors = Tutor::with(['category', 'subcategory', 'user'])->get()->map(function ($tutor) {
        return [
            'id' => $tutor->id,
            'first_name' => $tutor->first_name,
            'last_name' => $tutor->last_name,
            'email' => $tutor->user->email ?? null,    
            'telephone' => $tutor->user->telephone ?? null,    
            'bio' => $tutor->bio,
            'hourly_rate' => number_format((float) $tutor->hourly_rate, 2),
            'experience_years' => (int) $tutor->experience_years,
            'profile_picture' => $tutor->profile_picture ? asset('storage/' . $tutor->profile_picture) : null,
            'location' => $tutor->location,
            'latitude' => $tutor->latitude,
            'longitude' => $tutor->longitude,
            'category' => $tutor->category->label ?? null,
            'subcategories' => $tutor->subcategory ? [$tutor->subcategory->name] : [],
            'rating' => $tutor->rating ? round($tutor->rating, 1) : null,
            'is_verified' => $tutor->is_verified,
            'working_radius' => $tutor->working_radius,
            "intro_video" => $tutor->intro_video ? asset('storage/' . $tutor->intro_video) : null,
            'teaching_level' => $tutor->teaching_level ?? null,


        ];
    });

    return response()->json(['status' => 'success', 'tutors' => $tutors]);
}


    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'category_id' => 'required|exists:categories,id',
            'subcategory_id' => 'required|exists:subcategories,id',
            'bio' => 'required|string',
            'hourly_rate' => 'required|numeric',
            'experience_years' => 'required|numeric',
            'location' => 'required|string',
            'teaching_level' => 'required|string',
        ]);

        $coords = $this->getCoordinatesFromLocation($request->location);
        if (!$coords) {
            return response()->json(['message' => 'Invalid location. Unable to get coordinates.'], 422);
        }

        $tutor = new Tutor();
        $tutor->user_id = $request->user_id;
        $tutor->category_id = $request->category_id;
        $tutor->subcategory_id = $request->subcategory_id;
        $tutor->bio = $request->bio;
        $tutor->hourly_rate = $request->hourly_rate;
        $tutor->experience_years = $request->experience_years;
        $tutor->latitude = $coords['latitude'];
        $tutor->longitude = $coords['longitude'];
        $tutor->location = $request->location;
        $tutor->teaching_level = $request->teaching_level;
        $tutor->profile_picture = $request->profile_picture;
        $tutor->save();

        return response()->json(['status' => 'success', 'data' => $tutor]);
    }

    public function verify($id)
    {
        $tutor = Tutor::findOrFail($id);
        $tutor->is_verified = true;
        $tutor->save();

        return response()->json(['message' => 'Tutor verified.']);
    }

    public function verifiedTutors()
    {
        $tutors = Tutor::with(['category', 'subcategories', 'user'])
        ->where('is_verified', true)
        ->get()
        ->map(function ($tutor) {
            return [
                'id' => $tutor->id,
                'first_name' => $tutor->first_name,
                'last_name' => $tutor->last_name,
                'email' => $tutor->user->email ?? null,
                'telephone' => $tutor->user->telephone ?? null,
                'bio' => $tutor->bio,
                'hourly_rate' => number_format((float) $tutor->hourly_rate, 2),
                'experience_years' => (int) $tutor->experience_years,
                'profile_picture' => $tutor->profile_picture ? asset('storage/' . $tutor->profile_picture) : null,
                'location' => $tutor->location,
                'teaching_level' => $tutor->teaching_level ?? null,
                'latitude' => $tutor->latitude,
                'longitude' => $tutor->longitude,
                'category' => $tutor->category->label ?? null,
                'rating' => $tutor->rating ? round($tutor->rating, 1) : null,
            ];
        });
    

        return response()->json($tutors);
    }

    public function verifiedTutorsForMap(Request $request)
    {
        $query = Tutor::with('category', 'reviews')->where('is_verified', true);

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->has('lat') && $request->has('lng')) {
            $lat = $request->input('lat');
            $lng = $request->input('lng');
            $radius = 10;

            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance", [$lat, $lng, $lat])
                ->having("distance", "<", $radius)
                ->orderBy("distance");
        }

        $tutors = $query->get([
            'id',
            'first_name',
            'last_name',
            'rating',
            'category_id',
        ]);

        $result = $tutors->map(function ($tutor) {
            return [
                'id' => $tutor->id,
                'first_name' => $tutor->first_name,
                'last_name' => $tutor->last_name,
                'rating' => $tutor->rating ? round($tutor->rating, 1) : null,
                'reviews_count' => $tutor->reviews->count(),
                'subject' => $tutor->category->label ?? null,
            ];
        });

        return response()->json($result);
    }

    private function getCoordinatesFromLocation($location)
    {
        $apiKey = env('GOOGLE_MAPS_API_KEY');
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

        return null;
    }

    public function updateCategories(Request $request, $tutorId)
    {
        $request->validate([
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
        ]);

        $tutor = Tutor::findOrFail($tutorId);
        $tutor->categories()->sync($request->category_ids);

        return response()->json([
            'message' => 'Categories updated successfully.',
            'categories' => $tutor->categories,
        ]);
    }

    public function updateSubcategories(Request $request, $tutorId)
    {
        $request->validate([
            'subcategory_ids' => 'required|array',
            'subcategory_ids.*' => 'exists:subcategories,id',
        ]);

        $tutor = Tutor::findOrFail($tutorId);
        $tutor->subcategories()->sync($request->subcategory_ids);

        return response()->json([
            'message' => 'Subcategories updated successfully.',
            'subcategories' => $tutor->subcategories,
        ]);
    }

    public function show($tutorId)
    {
        $tutor = Tutor::with(['category', 'subcategories'])->findOrFail($tutorId);
    
        return response()->json([
            'id' => $tutor->id,
            'first_name' => $tutor->first_name,
            'last_name' => $tutor->last_name,
            'email' => $tutor->user->email ?? null,
            'telephone' => $tutor->user->telephone ?? null,
            'bio' => $tutor->bio,
            'hourly_rate' => number_format((float) $tutor->hourly_rate, 2),
            'experience_years' => (int) $tutor->experience_years,
            'profile_picture' => $tutor->profile_picture ? asset('storage/' . $tutor->profile_picture) : null,
            'location' => $tutor->location,
            'latitude' => $tutor->latitude,
            'longitude' => $tutor->longitude,
            'category' => $tutor->category->label ?? null,
            'subcategories' => $tutor->subcategories->pluck('name'),
            'rating' => $tutor->rating ? round($tutor->rating, 1) : null,
            'is_verified' => $tutor->is_verified,
            'working_radius' => $tutor->working_radius,
        ]);
    }
    

    public function getTutorsByCategory(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'mode' => 'sometimes|string'
        ]);
    
        $radius = 50; // km
    
        $query = Tutor::where('category_id', $request->category_id)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude');
    
        if ($request->input('mode') === 'around') {
            $query->selectRaw("*, (6371 * acos(cos(radians(?)) * cos(radians(latitude)) 
                     * cos(radians(longitude) - radians(?)) 
                     + sin(radians(?)) * sin(radians(latitude)))) AS distance", 
                [$request->lat, $request->lng, $request->lat])
                ->having("distance", "<", $radius)
                ->orderBy("distance", 'asc');
        }
    
        $tutors = $query->get();
    
        return response()->json([
            'status' => 'success',
            'tutors' => $tutors
        ]);
    }
    
    public function verifiedOnlineTutors(Request $request)
    {
        $categoryId = $request->query('category_id');
        $subcategoryId = $request->query('subcategory_id');
        $levelId = $request->query('level_id'); // if needed
    
        $query = Tutor::with(['user', 'category', 'subcategory'])
            ->where('is_verified', true);
    
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
    
        if ($subcategoryId) {
            $query->where('subcategory_id', $subcategoryId);
        }
    
        if ($levelId) {
            $query->where('teaching_level_id', $levelId);
        }
    
        $tutors = $query->get()->map(function ($tutor) {
            $fullName = null;
            if ($tutor->user) {
                $firstName = $tutor->user->first_name ?? '';
                $lastName = $tutor->user->last_name ?? '';
                $fullName = trim("$firstName $lastName");
            }
    
            return [
                'id' => $tutor->id,
                'profile_picture' => Str::startsWith($tutor->profile_picture, ['http://', 'https://'])
                    ? $tutor->profile_picture
                    : asset('storage/profilePics/' . $tutor->profile_picture),
                'name' => $fullName ?: 'No Name',
                'category' => $tutor->category->label ?? null,
                // since single subcategory, wrap it in array if exists
                'subcategories' => $tutor->subcategory ? [$tutor->subcategory->name] : [],
                'teaching_level' => $tutor->teaching_level ?? null,
                'bio' => $tutor->bio,
                'hourly_rate' => number_format((float) $tutor->hourly_rate, 2),
                'rating' => $tutor->rating ? round($tutor->rating, 1) : null,
                'experience_years' => (int) $tutor->experience_years,
                'is_online' => $tutor->available == 1,
                'intro_video' => Str::startsWith($tutor->intro_video, ['http://', 'https://'])
                    ? $tutor->intro_video
                    : asset('storage/' . $tutor->intro_video),
            ];
        });
    
        return response()->json($tutors);
    }
    
    

    public function dashboard()
{
    $user = Auth::user();  // Use Auth facade here

    $tutor = Tutor::where('user_id', $user->id)->first();

    if (!$tutor) {
        abort(404, 'Tutor profile not found.');
    }

    return view('tutor.dashboard', compact('tutor'));
}

public function updateAvailability(Request $request, $id)
{
    $request->validate([
        'available' => 'required|boolean',
    ]);

    $tutor = Tutor::findOrFail($id);
    $tutor->available = $request->available;
    $tutor->save();

    return response()->json(['message' => 'Availability updated.', 'available' => $tutor->available]);
}


public function getAvailabilityWithBookedSlots($tutorId)
{
    $workingHours = [
        'start' => '09:00',
        'end' => '18:00',
        'interval' => 30 // minutes
    ];

    $availability = [];
    $today = Carbon::today();

    for ($i = 0; $i < 14; $i++) {
        $date = $today->copy()->addDays($i)->toDateString();

        // Generate all possible time slots for the date
        $slots = $this->generateTimeSlots($workingHours['start'], $workingHours['end'], $workingHours['interval']);

        // Get bookings for tutor on this date
        $bookings = Booking::where('tutor_id', $tutorId)
            ->where('date', $date)
            ->get();

        // Filter out slots that overlap with existing bookings
        foreach ($bookings as $booking) {
            $slots = array_filter($slots, function ($slot) use ($booking) {
                $slotTime = Carbon::createFromFormat('H:i', $slot);
        
                // Adjust parsing depending on DB time format
                $start = Carbon::createFromFormat('H:i:s', $booking->start_time);
                $end = Carbon::createFromFormat('H:i:s', $booking->end_time);
        
                $endAdjusted = $end->copy()->subMinute();
        
                return !$slotTime->between($start, $endAdjusted);
            });
        }
        

        if (!empty($slots)) {
            $availability[$date] = array_values($slots); // reset keys to 0-based
        }
    }

    return response()->json([
        'available_slots' => $availability
    ]);
}

private function generateTimeSlots($start, $end, $interval)
{
    $slots = [];
    $startTime = Carbon::createFromFormat('H:i', $start);
    $endTime = Carbon::createFromFormat('H:i', $end);

    while ($startTime < $endTime) {
        $slots[] = $startTime->format('H:i');
        $startTime->addMinutes($interval);
    }

    return $slots;
}

public function uploadIntroVideo(Request $request)
{
    $request->validate([
        'video' => 'required|mimes:mp4,mov,avi,webm|max:102400', // max 20MB
    ]);

    $tutor = Auth::user()->tutor;

    $path = $request->file('video')->store('intro_videos', 'public');
    $tutor->intro_video = $path;
    $tutor->save();

    return response()->json(['message' => 'Video uploaded successfully', 'video_url' => asset('storage/' . $path)]);
}

public function updateTeachingLevel(Request $request, $id)
{
    $request->validate([
        'teaching_level' => 'required|string',
    ]);

    $tutor = Tutor::findOrFail($id);
    $tutor->teaching_level = $request->teaching_level;
    $tutor->save();

    return response()->json(['message' => 'Teaching level updated.', 'teaching_level' => $tutor->teaching_level]);
}

public function getTutorsByFilters(Request $request)
{
    $request->validate([
        'category_id' => 'required|exists:categories,id',
        'teaching_level_id' => 'nullable|exists:teaching_levels,id',
        'subcategory_id' => 'nullable|exists:subcategories,id',
        'price' => 'nullable|numeric|min:0',
        'distance' => 'nullable|numeric|min:1',
        'latitude' => 'nullable|numeric',
        'longitude' => 'nullable|numeric',
    ]);

    $query = Tutor::query()
        ->where('category_id', $request->category_id);

    if ($request->filled('teaching_level_id')) {
        $query->where('teaching_level_id', $request->teaching_level_id);
    }

    if ($request->filled('subcategory_id')) {
        $query->where('subcategory_id', $request->subcategory_id);
    }

    if ($request->filled('price')) {
        $query->where('price_per_hour', '<=', $request->price);
    }

    // Distance filtering using Haversine formula
    if ($request->filled('distance') && $request->filled('latitude') && $request->filled('longitude')) {
        $distance = $request->distance;
        $lat = $request->latitude;
        $lng = $request->longitude;

        $query->selectRaw("*, (6371 * acos(
            cos(radians(?)) *
            cos(radians(latitude)) *
            cos(radians(longitude) - radians(?)) +
            sin(radians(?)) *
            sin(radians(latitude))
        )) AS distance", [$lat, $lng, $lat])
        ->having('distance', '<=', $distance)
        ->orderBy('distance');
    }

    $tutors = $query->with(['category', 'subcategory', 'teachingLevel'])->get();

    return response()->json([
        'status' => 'success',
        'tutors' => $tutors,
    ]);
}

}








