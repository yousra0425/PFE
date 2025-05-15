<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CinVerificationController extends Controller
{
    public function verifyCin(Request $request)
    {
        // Validate the uploaded image
        $request->validate([
            'cin_image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if (!$request->hasFile('cin_image')) {
            return response()->json(['error' => 'No image uploaded'], 400);
        }

        $file = $request->file('cin_image');

        if ($file && $file->isValid()) {
            $path = $file->getRealPath();

            try {
                // Send image to Flask API
                $response = Http::attach(
                    'cin_image',
                    file_get_contents($path),
                    $file->getClientOriginalName()
                )->post('http://127.0.0.1:5000/verify-cin');
                
                // Log the response data
                Log::debug('Flask Response Body:', ['body' => $response->body()]);
                
                if ($response->successful()) {
                    $data = $response->json();
                
                    return response()->json([
                        'cin' => $data['cin'] ?? null,
                        'first_name' => $data['first_name'] ?? null,
                        'last_name' => $data['last_name'] ?? null,
                        'ocr_text' => $data['ocr_text'] ?? null,
                    ]);
                }
                
                return response()->json(['error' => 'CIN verification failed from AI service.'], 400);
                

            } catch (\Exception $e) {
                Log::error('Failed to connect to Flask API', ['error' => $e->getMessage()]);
                return response()->json(['error' => 'Failed to connect to the Python API.'], 500);
            }

        } else {
            return response()->json(['error' => 'Invalid file uploaded.'], 400);
        }
    }
}
