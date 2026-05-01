<?php

declare(strict_types=1);

namespace App\Support\Integrations\Restaurant;

use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Restaurant-side verifier for hotel-issued guest SSO tokens.
 *
 * COPY THIS FILE INTO THE RESTAURANT SYSTEM, for example:
 *   app/Support/Integrations/HotelGuestSsoVerifier.php
 *
 * Then call it from the restaurant SSO entry route/controller that receives:
 *   GET /restaurant/sso/hotel?token=...
 *
 * EXPECTED SHARED SECRET:
 *   The same value entered in the hotel admin dashboard under:
 *   System Settings -> Integrations -> Shared secret for signed guest access
 *
 * WHAT THIS FILE DOES:
 *   1. Verifies HMAC SHA-256 signature
 *   2. Checks token expiry
 *   3. Returns the decoded hotel guest payload
 *
 * AFTER VERIFYING:
 *   - create/find the restaurant-side guest session
 *   - optionally create a temporary customer record
 *   - redirect the guest into your restaurant ordering dashboard
 */
final class HotelGuestSsoVerifier
{
    /**
     * @return array<string, mixed>
     */
    public function verifyFromRequest(Request $request, string $sharedSecret): array
    {
        $token = trim((string) $request->query('token', ''));

        if ($token === '') {
            throw new RuntimeException('Missing hotel SSO token.');
        }

        return $this->verify($token, $sharedSecret);
    }

    /**
     * @return array<string, mixed>
     */
    public function verify(string $token, string $sharedSecret): array
    {
        if ($sharedSecret === '') {
            throw new RuntimeException('Shared secret is missing.');
        }

        $parts = explode('.', $token);
        if (count($parts) !== 3) {
            throw new RuntimeException('Invalid token format.');
        }

        [$encodedHeader, $encodedPayload, $encodedSignature] = $parts;

        $headerJson = $this->base64UrlDecode($encodedHeader);
        $payloadJson = $this->base64UrlDecode($encodedPayload);

        $header = json_decode($headerJson, true);
        $payload = json_decode($payloadJson, true);

        if (! is_array($header) || ! is_array($payload)) {
            throw new RuntimeException('Token payload is invalid.');
        }

        if (($header['alg'] ?? null) !== 'HS256') {
            throw new RuntimeException('Unexpected token algorithm.');
        }

        $expectedSignature = $this->base64UrlEncode(
            hash_hmac('sha256', $encodedHeader.'.'.$encodedPayload, $sharedSecret, true)
        );

        if (! hash_equals($expectedSignature, $encodedSignature)) {
            throw new RuntimeException('Token signature verification failed.');
        }

        $now = CarbonImmutable::now()->timestamp;
        $exp = isset($payload['exp']) ? (int) $payload['exp'] : 0;
        $iat = isset($payload['iat']) ? (int) $payload['iat'] : 0;

        if ($exp <= 0 || $exp < $now) {
            throw new RuntimeException('Token has expired.');
        }

        if ($iat > 0 && $iat > ($now + 60)) {
            throw new RuntimeException('Token issue time is invalid.');
        }

        if (! isset($payload['booking_reference']) || ! is_string($payload['booking_reference'])) {
            throw new RuntimeException('Booking reference is missing from token.');
        }

        if (! isset($payload['guest_email']) || ! is_string($payload['guest_email'])) {
            throw new RuntimeException('Guest email is missing from token.');
        }

        if (! isset($payload['jti']) || ! is_string($payload['jti']) || ! Str::isUuid($payload['jti'])) {
            throw new RuntimeException('Token identifier is invalid.');
        }

        return $payload;
    }

    /**
     * Example helper:
     * Turn the decoded payload into a compact customer/session array that your
     * restaurant app can use when creating a local login/session.
     *
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function mapToRestaurantGuestProfile(array $payload): array
    {
        return [
            'source' => 'hotel-sso',
            'source_reference' => $payload['booking_reference'] ?? null,
            'name' => trim((string) ($payload['guest_name'] ?? 'Hotel Guest')),
            'email' => (string) ($payload['guest_email'] ?? ''),
            'phone' => (string) ($payload['guest_phone'] ?? ''),
            'room_name' => (string) ($payload['room_name'] ?? ''),
            'room_number' => (string) ($payload['room_number'] ?? ''),
            'branch_id' => $payload['branch_id'] ?? null,
            'branch_name' => (string) ($payload['branch_name'] ?? ''),
            'check_in' => (string) ($payload['check_in'] ?? ''),
            'check_out' => (string) ($payload['check_out'] ?? ''),
            'booking_id' => $payload['booking_id'] ?? null,
            'hotel_app_key' => $payload['hotel_app_key'] ?? null,
        ];
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): string
    {
        $remainder = strlen($value) % 4;
        if ($remainder > 0) {
            $value .= str_repeat('=', 4 - $remainder);
        }

        $decoded = base64_decode(strtr($value, '-_', '+/'), true);
        if ($decoded === false) {
            throw new RuntimeException('Base64 decode failed.');
        }

        return $decoded;
    }
}

/**
 * QUICK LARAVEL USAGE INSIDE THE RESTAURANT SYSTEM
 * -----------------------------------------------
 *
 * 1. Place this file at:
 *    app/Support/Integrations/HotelGuestSsoVerifier.php
 *
 * 2. Create a controller action similar to this:
 *
 *    use App\Support\Integrations\Restaurant\HotelGuestSsoVerifier;
 *    use Illuminate\Http\Request;
 *    use Illuminate\Support\Facades\Auth;
 *
 *    public function hotelGuestEntry(Request $request, HotelGuestSsoVerifier $verifier)
 *    {
 *        $payload = $verifier->verifyFromRequest(
 *            $request,
 *            config('services.hotel_guest_sso.shared_secret', '')
 *        );
 *
 *        $profile = $verifier->mapToRestaurantGuestProfile($payload);
 *
 *        // TODO:
 *        // - find or create restaurant-side guest/customer
 *        // - log them into the restaurant app
 *        // - attach booking reference / room / branch context
 *        // - redirect to restaurant ordering dashboard
 *
 *        return redirect()->route('restaurant.dashboard');
 *    }
 *
 * 3. Create a route in the restaurant system:
 *
 *    Route::get('/restaurant/sso/hotel', [HotelSsoController::class, 'hotelGuestEntry']);
 *
 * 4. Put the SAME shared secret in the restaurant system config or .env:
 *
 *    HOTEL_GUEST_SSO_SHARED_SECRET=the-same-secret-from-hotel-admin
 *
 * 5. Optional recommended protections:
 *    - store used jti values for a few minutes to prevent replay
 *    - require HTTPS only
 *    - check allowed issuer (iss) / audience (aud)
 *    - log failed verification attempts
 */
