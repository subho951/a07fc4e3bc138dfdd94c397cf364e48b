<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class FcmHelper
{
    public static function sendNotification($token, $title, $body, $data = [])
    {
        $serverKey = env('FCM_SERVER_KEY');

        $response = Http::withHeaders([
            'Authorization' => 'key=' . $serverKey,
            'Content-Type' => 'application/json',
        ])->post('https://fcm.googleapis.com/fcm/send', [

            "to" => $token,

            "notification" => [
                "title" => $title,
                "body" => $body,
                "sound" => "default"
            ],

            "data" => $data,

            "priority" => "high"

        ]);

        return $response->json();
    }
}