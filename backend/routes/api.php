<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\CinVerificationController;

// Routes accessibles sans être connecté
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/verify-cin', [CinVerificationController::class, 'verifyCin']);
// Routes protégées (authentification obligatoire avec sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Exemples d'autres routes protégées
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::post('/service-provider/apply', [ServiceProviderController::class, 'store']);

    

    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/users', [UserController::class, 'index']);
    Route::resource('bookings', BookingController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('services', ServiceController::class);
    Route::post('/reviews', [ReviewController::class, 'store']);
    Route::get('/categories-with-services', [CategoryController::class, 'getCategoriesWithServices']);
    // ✅ Custom route to get services by category ID
    Route::get('/categories/{id}/services', [ServiceController::class, 'getByCategory']);
    Route::get('/service-providers/map', [ServiceProviderController::class, 'verifiedProvidersForMap']);

    
    // Admin-only routes
    Route::middleware(['auth:sanctum', 'is_admin'])->group(function () {
    Route::post('/service-provider/verify/{id}', [ServiceProviderController::class, 'verify']);
    });

    // Public route to show verified providers
    Route::get('/service-providers/verified', [ServiceProviderController::class, 'verifiedProviders']);  
    });
