<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'service_provider_id' => 'required|exists:service_providers,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string',
        ]);

        $user = Auth::user();

        $existingReview = Review::where('user_id', $user->id)
            ->where('service_provider_id', $request->service_provider_id)
            ->first();

        if ($existingReview) {
            return response()->json(['message' => 'You already reviewed this provider.'], 400);
        }

        $review = Review::create([
            'service_provider_id' => $request->service_provider_id,
            'user_id' => $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json(['message' => 'Review submitted successfully', 'review' => $review]);
    }
}
