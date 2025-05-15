<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\UserController;
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

    

    Route::get('/profile', function (Request $request) {
        return response()->json($request->user());
    });
    Route::get('/users', [UserController::class, 'index']);
    Route::resource('bookings', BookingController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('services', ServiceController::class);
    
    // ✅ Custom route to get services by category ID
    Route::get('/categories/{id}/services', [ServiceController::class, 'getByCategory']);

    

});
