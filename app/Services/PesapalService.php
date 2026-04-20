<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BookingMethod;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PesapalService
{
    /**
     * Omba Token ya miamala kutoka Pesapal
     */
    private function getAuthToken(BookingMethod $method): ?string
    {
        $url = rtrim($method->gateway_base_url, '/') . '/api/Auth/RequestToken';

        $response = Http::post($url, [
            'consumer_key' => $method->gateway_public_key,
            'consumer_secret' => $method->gateway_secret_key,
        ]);

        return $response->json()['token'] ?? null;
    }

    /**
     * Sajili URL ya IPN (Inahitajika mara moja kwa kila merchant key)
     */
    public function registerIpn(BookingMethod $method): ?string
    {
        $token = $this->getAuthToken($method);
        $url = rtrim($method->gateway_base_url, '/') . '/api/URLSetup/RegisterIPN';

        $response = Http::withToken($token)->post($url, [
            'url' => route('api.pesapal.ipn'), // Route hii ni lazima iwe public
            'ipn_notification_type' => 'GET',
        ]);

        return $response->json()['ipn_id'] ?? null;
    }

    /**
     * Tengeneza malipo na pata link ya kumpeleka mteja
     */
    public function createPaymentLink(Booking $booking)
{
    $method = $booking->method;
    if (!$method || !$method->gateway_public_key) {
        return null;
    }

    $token = $this->getAuthToken($method);

    // 1. Angalia kama tayari tunayo IPN ID kwenye database
    $ipnId = $method->gateway_ipn_id;

    // 2. Kama haipo, isajili sasa hivi na uisave kwa baadae
    if (!$ipnId) {
        $ipnId = $this->registerIpn($method);
        if ($ipnId) {
            $method->update(['gateway_ipn_id' => $ipnId]);
        }
    }

    $url = rtrim($method->gateway_base_url, '/') . '/api/Transactions/SubmitOrderRequest';

    $payload = [
        "id" => $booking->public_reference,
        "currency" => "TZS",
        "amount" => (float) round((float) $booking->total_amount),
        "description" => "Room Booking - " . $booking->public_reference,
        "callback_url" => route('site.pesapal.callback'),
        "notification_id" => $ipnId,
        "billing_address" => [
            "email_address" => $booking->email,
            "phone_number" => $booking->phone,
            "first_name" => $booking->first_name,
            "last_name" => $booking->last_name,
        ]
    ];

    $response = Http::withToken($token)->post($url, $payload);
    return $response->json();
}
}
