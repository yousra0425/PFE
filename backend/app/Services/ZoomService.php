<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ZoomService
{
    public function generateAccessToken()
    {
        $clientId = config('services.zoom.client_id');
        $clientSecret = config('services.zoom.client_secret');
        $accountId = config('services.zoom.account_id');

        $response = Http::asForm()->withBasicAuth($clientId, $clientSecret)
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $accountId,
            ]);

        if ($response->failed()) {
            throw new \Exception('Failed to get Zoom access token: ' . $response->body());
        }

        return $response->json()['access_token'];
    }

    public function createMeeting($topic = 'Tutoring Session', $startTime = null)
{
    $token = $this->generateAccessToken();

    $response = Http::withToken($token)
        ->post('https://api.zoom.us/v2/users/me/meetings', [
            'topic' => $topic,
            'type' => 1, // 1 = Instant meeting, use 2 for scheduled
            'start_time' => $startTime, // Optional: '2025-06-11T10:00:00Z'
            'timezone' => 'UTC',
            'settings' => [
                'join_before_host' => true,
                'approval_type' => 0,
                'registration_type' => 1,
                'audio' => 'voip',
                'auto_recording' => 'none',
            ],
        ]);

    if ($response->failed()) {
        throw new \Exception('Failed to create Zoom meeting: ' . $response->body());
    }

    return $response->json();
}

}
