<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CinVerificationController;

/*
|--------------------------------------------------------------------------
| Public Routes (No auth required)
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
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

    // User Management
    Route::get('/users', [UserController::class, 'index']);

    // Service Provider Application
    Route::post('/service-provider/apply', [ServiceProviderController::class, 'store']);

    // Bookings, Categories, Services
    Route::resource('bookings', BookingController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('services', ServiceController::class);

    // Additional category-service routes
    Route::get('/categories-with-services', [CategoryController::class, 'getCategoriesWithServices']);
    Route::get('/categories/{id}/services', [ServiceController::class, 'getByCategory']);
    Route::post('/categories/{id}/services', [ServiceController::class, 'storeByCategory']);

    // Reviews
    Route::post('/reviews', [ReviewController::class, 'store']);

    // Verified service providers for map (authenticated)
    Route::get('/service-providers/map', [ServiceProviderController::class, 'verifiedProvidersForMap']);

    /*
    |--------------------------------------------------------------------------
    | Admin-only Routes
    |--------------------------------------------------------------------------
    */
    Route::middleware('is_admin')->group(function () {
        Route::post('/service-provider/verify/{id}', [ServiceProviderController::class, 'verify']);
        Route::get('/service-providers/verified', [ServiceProviderController::class, 'verifiedProviders']);
    });
});

