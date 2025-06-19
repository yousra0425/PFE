<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CinVerificationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SubcategoryController;
use App\Http\Controllers\TutorController;
use App\Http\Controllers\ZoomTestController;
use App\Http\Controllers\TeachingLevelController;
use App\Services\FirebaseService;
use App\Http\Controllers\NotificationController;



/*
|--------------------------------------------------------------------------
| Public Routes (No auth required)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/register-tutor', [AuthController::class, 'registerTutor']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-cin', [CinVerificationController::class, 'verifyCin']);


/*
|--------------------------------------------------------------------------
| Authenticated Routes (auth:sanctum middleware)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Logout & Profile
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', fn(Request $request) => $request->user());
    Route::get('/profile', fn(Request $request) => response()->json($request->user()));
    Route::post('/fcm-token', [UserController::class, 'updateFcmToken']);
    Route::get('/test-notification', function (Request $request, FirebaseService $firebaseService) {
        // Replace this with the FCM token you got from the frontend
        $deviceToken = 'eN_qwyGFXoX6zktsJ3ByLU:APA91bGUiVB6EaUkbJLtdJWckVM2NJMzhOXY6gCljnA3yOvST3-BEplsJv1q17FIGGphjJ-MEFMXXyMwSNS6MscUDF1aEpZGiPH2VNFt7F1-u4QB73ROIDc';
    
        $title = "Test Notification";
    $body = "This is a test notification sent from Laravel backend!";
    $data = ['key1' => 'value1', 'key2' => 'value2'];

    try {
        $sent = $firebaseService->sendNotification($deviceToken, $title, $body, $data);
        if ($sent) {
            return response()->json(['message' => 'Notification sent successfully']);
        } else {
            return response()->json(['message' => 'Failed to send notification'], 500);
        }
    } catch (\Exception $e) {
        Log::error('Test notification error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 500);
    }
});
Route::post('/send-notification', [NotificationController::class, 'sendNotification']);
    // User Management
    
    Route::post('/update-location', [UserController::class, 'updateLocation']);
       
    Route::post('/tutor/apply', [TutorController::class, 'store']);
    Route::get('/tutors/map', [UserController::class, 'getTutorsWithLocation']);
    Route::get('/tutors/zones', [UserController::class, 'getTutorZones']);
    Route::get('/teaching-levels', [TeachingLevelController::class, 'index']);
    Route::post('/teaching-levels', [TeachingLevelController::class, 'store']);
  
    Route::patch('/tutors/{tutor}/categories', [TutorController::class, 'updateCategories']);
    Route::patch('/tutors/{id}', [TutorController::class, 'updateAvailability']);
    Route::get('/tutor/{tutorId}/available-slots', [TutorController::class, 'getAvailabilityWithBookedSlots']);
    Route::get('/tutor/{tutorId}/booked-dates', [TutorController::class, 'getBookedDates']);
    Route::get('/tutor/{tutorId}/availability-with-booked', [TutorController::class, 'getAvailabilityWithBookedSlots']);

    // Bookings
    Route::resource('bookings', BookingController::class);
    Route::get('/tutor/{tutor}/bookings', [BookingController::class, 'getTutorBookings']);
    Route::put('bookings/{booking}/reschedule', [BookingController::class, 'reschedule']);
    Route::post('/bookings/{id}/respond', [BookingController::class, 'respond']);
    Route::post('/bookings/{id}/accept', [BookingController::class, 'accept']);
    Route::post('/bookings/{id}/decline', [BookingController::class, 'decline']);

    //Messages
    Route::get('/messages/{userId}', [MessageController::class, 'index']);
    Route::post('/messages', [MessageController::class, 'store']);
    Route::patch('/messages/{id}/read', [MessageController::class, 'markAsRead']);


    Route::resource('categories', CategoryController::class);
    Route::resource('services', ServiceController::class);
    // Subcategories
    Route::get('/filtered-subcategories', [SubcategoryController::class, 'getFilteredSubcategories']);
    Route::apiResource('subcategories', SubcategoryController::class);
    // Additional category-service routes
    Route::get('/categories-with-subcategories', [CategoryController::class, 'getCategoriesWithSubcategories']);
    Route::get('/categories/{id}/services', [ServiceController::class, 'getByCategory']);
    Route::post('/categories/{id}/services', [ServiceController::class, 'storeByCategory']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);
// Example route in routes/api.php
    Route::get('/tutors/filter', [TutorController::class, 'getTutorsByFilters']);

    // Verified service providers for map (authenticated)
    Route::get('/tutors/verified-online', [TutorController::class, 'verifiedOnlineTutors']);
    Route::apiResource('tutors', TutorController::class); 
    Route::get('tutors-by-category', [TutorController::class, 'getTutorsByCategory']);
    //Online tutors
    Route::get('/zoom/test', [ZoomTestController::class, 'test']);
    Route::get('/zoom/create-meeting', [ZoomTestController::class, 'createMeeting']);
    Route::post('/zoom/send-link', [ZoomTestController::class, 'createMeetingAndSendLink']);
    Route::post('/tutor/upload-intro-video', [TutorController::class, 'uploadIntroVideo']);

    /*
    |--------------------------------------------------------------------------
    | Admin-only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {
        Route::post('/tutor/verify/{id}', [TutorController::class, 'verify']);
        Route::get('/tutors/verified', [TutorController::class, 'verifiedTutors']);
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/admin/bookings', [BookingController::class, 'adminIndex']);
    });

    Route::middleware(['auth:sanctum', 'is_tutor'])->group(function () {
        Route::get('/tutor/dashboard', [TutorController::class, 'dashboard'])->name('tutor.dashboard');
        // add other tutor-only routes here
    });
    
});

