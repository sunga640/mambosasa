<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

final class SmsService
{
    public function send(string $to, string $message): void
    {
        // 1. Tunalazimisha kusoma env moja kwa moja kwa ajili ya majaribio
        $driver = env('SMS_DRIVER', 'log');

        Log::info("SMS System: Inajaribu kutuma kwa kutumia driver ya [$driver]");

        if ($driver === 'beem') {
            $this->sendBeem($to, $message);
            return;
        }

        // Kama si beem, iandike kwenye log tu
        Log::info('[SMS LOG ONLY]', ['to' => $to, 'message' => $message]);
    }

    private function sendBeem(string $to, string $message): void
    {
        $apiKey = env('BEEM_API_KEY');
        $secretKey = env('BEEM_SECRET_KEY');
        $senderId = mb_substr(preg_replace('/\s+/', '', (string) env('BEEM_SENDER_ID', 'INFO')), 0, 11);

        if (!$apiKey || !$secretKey) {
            Log::error('Beem SMS Error: API Credentials hazipo kwenye .env');
            return;
        }

        // 1. REKEBISHA NAMBA: Geuza 0... kwenda 255... na toa +
        $formattedNumber = preg_replace('/[^0-9]/', '', $to); // Toa alama zote zisizo namba (kama +)

        if (str_starts_with($formattedNumber, '255')) {
            // tayari kimataifa
        } elseif (str_starts_with($formattedNumber, '0')) {
            $formattedNumber = '255'.substr($formattedNumber, 1);
        } elseif (strlen($formattedNumber) === 9) {
            $formattedNumber = '255'.$formattedNumber;
        }

        Log::info("Beem SMS: Inatuma kwenda $formattedNumber kwa Sender ID: $senderId");

        $endpoint = rtrim((string) env('BEEM_SMS_URL', 'https://apisms.beem.africa/v1/send'), '/');

        // 2. TUMA KWA BEEM API (apisms.beem.africa ndiyo host rasmi ya SMS)
        try {
            $response = Http::withBasicAuth($apiKey, $secretKey)
                ->connectTimeout(10) // Subiri sekunde 10 kuunganisha
                ->timeout(30)        // Subiri sekunde 30 kupata jibu
                ->withoutVerifying() // MUHIMU: Inazuia matatizo ya SSL ya XAMPP
                ->acceptJson()
                ->asJson()
                ->post($endpoint, [
                    'source_addr' => $senderId,
                    'schedule_time' => '',
                    'encoding' => 0,
                    'message' => $message,
                    'recipients' => [
                        ['recipient_id' => 1, 'dest_addr' => $formattedNumber],
                    ],
                ]);

            $body = $response->body();
            if ($response->successful()) {
                $json = $response->json();
                $code = is_array($json) ? ($json['code'] ?? $json['status'] ?? null) : null;
                if (is_array($json) && isset($json['message']) && stripos((string) $json['message'], 'fail') !== false) {
                    Log::error('Beem SMS API reported failure', ['to' => $formattedNumber, 'response' => $json]);
                } elseif ($code !== null && (int) $code !== 200 && (int) $code !== 100) {
                    Log::error('Beem SMS unexpected code', ['to' => $formattedNumber, 'code' => $code, 'response' => $json]);
                } else {
                    Log::info("Beem SMS Success: $formattedNumber | ".$body);
                }
            } else {
                Log::error("Beem SMS HTTP {$response->status()}: ".$body);
            }
        } catch (\Throwable $e) {
            Log::error("Beem Connection Error (Kuna tatizo la Internet au DNS): " . $e->getMessage());
        }
    }
}
