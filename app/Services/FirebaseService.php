<?php

namespace App\Services;

use Google\Client;
use Illuminate\Support\Facades\Http;

class FirebaseService
{
    public static function sendNotification($token, $title, $body, $data = [], $image = null)
    {
        $client = new Client();
        $client->setAuthConfig(storage_path('app/firebase/firebase_credentials.json'));
        $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

        $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

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

        // $payload = [
        //     "message" => [
        //         "token" => $token,
        //         "notification" => $notification,
        //         "data" => array_map('strval', $data)
        //     ]
        // ];

        $payload = [
            "message" => [
                "token" => $token,
                "notification" => [
                    "title" => $title,
                    "body" => $body
                ],
                "data" => array_map('strval', $data),
                "android" => [
                    "priority" => "HIGH"
                ],
                "apns" => [
                    "headers" => [
                        "apns-priority" => "10"
                    ]
                ]
            ]
        ];

        $response = Http::withHeaders([
            "Authorization" => "Bearer " . $accessToken,
            "Content-Type" => "application/json"
        ])->post($url, $payload);

        return $response->json();
    }
}