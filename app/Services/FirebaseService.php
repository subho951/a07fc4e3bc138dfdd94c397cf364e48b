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
                'invalid_registration' => self::hasInvalidRegistration($result ?? []),
                'invalid_fields' => self::invalidFields($result ?? []),
                'error_message' => self::redactDiagnostic($result['error']['message'] ?? '', [$token, $title, $body, $image]),
            ]);
            return $result ?: ['error' => ['status' => 'UNKNOWN']];
        }

        return $result;
    }

    public static function hasInvalidRegistration(array $result): bool
    {
        $error = $result['error'] ?? [];
        $codes = array_column($error['details'] ?? [], 'errorCode');
        if (in_array('UNREGISTERED', $codes, true)) {
            return true;
        }

        // INVALID_ARGUMENT can also describe a bad payload. Only retire a token
        // when Firebase explicitly identifies the registration token as invalid.
        $message = rtrim(strtolower(trim($error['message'] ?? '')), '.');
        return ($error['status'] ?? '') === 'INVALID_ARGUMENT'
            && $message === 'the registration token is not a valid fcm registration token';
    }

    private static function redactDiagnostic(string $message, array $privateValues): string
    {
        foreach ($privateValues as $value) {
            if (is_string($value) && $value !== '') {
                $message = str_replace($value, '[REDACTED]', $message);
            }
        }
        // Firebase may include a bad data value in its error message. Keep its
        // field/type explanation, but drop the value following that explanation.
        $message = preg_replace('/(Invalid value at .*?\([^)]*\)),[^\r\n]*/i', '$1 [REDACTED]', $message);
        return mb_substr(preg_replace('/[\r\n\x00-\x1F]+/', ' ', $message), 0, 1000);
    }

    private static function invalidFields(array $result): array
    {
        $fields = [];
        foreach ($result['error']['details'] ?? [] as $detail) {
            foreach ($detail['fieldViolations'] ?? [] as $violation) {
                // Field paths help diagnose payload errors without logging values.
                $field = $violation['field'] ?? '';
                if (preg_match('/^message(?:\.[a-zA-Z_]+|\[\d+\])*$/', $field)) {
                    $fields[] = $field;
                }
            }
        }
        return array_values(array_unique($fields));
    }
}
