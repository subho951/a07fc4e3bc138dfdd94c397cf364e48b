<?php

namespace Tests\Feature;

use App\Services\FirebaseService;
use Google\Client;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mockery;
use Tests\TestCase;

class FirebaseServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('setAuthConfig')->once();
        $client->shouldReceive('addScope')->once();
        $client->shouldReceive('fetchAccessTokenWithAssertion')->once()
            ->andReturn(['access_token' => 'test-access-token']);
        $this->app->instance(Client::class, $client);
        Http::preventStrayRequests();
    }

    public function test_alert_payload_supports_apple_and_android_without_empty_data_array(): void
    {
        Http::fake(['fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/1'])]);

        FirebaseService::sendNotification('test-token', 'Title', 'Body');

        Http::assertSent(function ($request) {
            $message = $request['message'];
            return $message['android']['priority'] === 'HIGH'
                && $message['apns']['headers']['apns-push-type'] === 'alert'
                && $message['apns']['headers']['apns-priority'] === '10'
                && $message['apns']['payload']['aps']['alert'] === ['title' => 'Title', 'body' => 'Body']
                && $message['apns']['payload']['aps']['sound'] === 'default'
                && !isset($message['data']);
        });
    }

    public function test_image_and_custom_data_are_included(): void
    {
        Http::fake(['fcm.googleapis.com/*' => Http::response(['name' => 'projects/test/messages/1'])]);
        FirebaseService::sendNotification('test-token', 'Title', 'Body', ['profile_id' => 12], 'https://example.com/photo.jpg');

        Http::assertSent(fn ($request) => $request['message']['data']['profile_id'] === '12'
            && $request['message']['notification']['image'] === 'https://example.com/photo.jpg'
            && $request['message']['apns']['fcm_options']['image'] === 'https://example.com/photo.jpg'
            && $request['message']['apns']['payload']['aps']['mutable-content'] === 1);
    }

    public function test_apns_authentication_error_is_returned_and_logged_without_token(): void
    {
        $error = ['error' => ['status' => 'UNAUTHENTICATED', 'details' => [
            ['errorCode' => 'THIRD_PARTY_AUTH_ERROR'],
        ]]];
        Http::fake(['fcm.googleapis.com/*' => Http::response($error, 401)]);
        Log::shouldReceive('warning')->once()->with('Firebase rejected push notification.', [
            'http_status' => 401,
            'status' => 'UNAUTHENTICATED',
            'error_codes' => ['THIRD_PARTY_AUTH_ERROR'],
        ]);

        $this->assertSame($error, FirebaseService::sendNotification('private-token', 'Title', 'Body'));
    }
}
