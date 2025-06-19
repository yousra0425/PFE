<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Exception\MessagingException;
use Illuminate\Support\Facades\Log;


class FirebaseService
{
    protected $messaging;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(storage_path('app/firebase/firebase-service-account.json'));
        $this->messaging = $factory->createMessaging();
    }

    /**
     * Send a notification to a device token
     *
     * @param string $deviceToken
     * @param string $title
     * @param string $body
     * @param array $data
     * @return bool
     */
    public function sendNotification(string $deviceToken, string $title, string $body, array $data = []): bool
{
    try {
        $message = CloudMessage::fromArray([
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ],
            'data' => $data,
        ]);

        $this->messaging->send($message);

        return true;
    } catch (MessagingException $e) {
        Log::error('FCM send error: ' . $e->getMessage());
        return false;
    }
}

}
