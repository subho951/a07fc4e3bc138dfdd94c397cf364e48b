<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseService
{
    public static function sendNotification($token, $title, $body, $data = [], $image = null)
    {
        $client = app(Client::class);
        $client->setAuthConfig(storage_path('app/firebase/firebase_credentials.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $auth = $client->fetchAccessTokenWithAssertion();
        if (empty($auth['access_token'])) {
            Log::error('Firebase authentication failed.');
            return ['error' => ['status' => 'UNAUTHENTICATED']];
        }
        $accessToken = $auth['access_token'];

        $projectId = 'alfa-network-c0ca9';

        $url = "https://fcm.googleapis.com/v1/projects/$projectId/messages:send";

        $notification = [
            "title" => $title,
            "body"  => $body
        ];

        // Add image if provided
        if ($image) {
            $notification['image'] = $image;
        }

        $payload = [
            "message" => [
                "token" => $token,
                "notification" => $notification,
                "android" => [
                    "priority" => "HIGH"
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10",
                        "apns-push-type" => "alert"
                    ],
                    "payload" => [
                        "aps" => [
                            "alert" => ["title" => $title, "body" => $body],
                            "sound" => "default"
                        ]
                    ]
                ]
            ]
        ];

        // FCM requires data to be a JSON object, including when it is empty.
        if ($data) {
            $payload['message']['data'] = array_map('strval', $data);
        }
        if ($image) {
            $payload['message']['apns']['payload']['aps']['mutable-content'] = 1;
            $payload['message']['apns']['fcm_options']['image'] = $image;
        }

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $accessToken,
            "Content-Type" => "application/json"
        ])->post($url, $payload);

        $result = $response->json();
        if (!$response->successful() || empty($result['name'])) {
            // Do not log registration tokens, credentials, or notification content.
            Log::warning('Firebase rejected push notification.', [
                'http_status' => $response->status(),
                'status' => $result['error']['status'] ?? 'UNKNOWN',
                'error_codes' => array_column($result['error']['details'] ?? [], 'errorCode'),
            ]);
            return $result ?: ['error' => ['status' => 'UNKNOWN']];
        }

        return $result;
    }
}
