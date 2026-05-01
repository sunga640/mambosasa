<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\SystemSetting;
use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class RestaurantIntegrationService
{
    public function __construct(
        private ?SystemSetting $settings = null,
    ) {
        $this->settings ??= SystemSetting::current();
    }

    public function isReady(): bool
    {
        return $this->settings->restaurantIntegrationConfigured();
    }

    public function activeBookingForUser(User $user): ?Booking
    {
        return Booking::query()
            ->where('user_id', $user->id)
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('check_in', '<=', now()->toDateString())
            ->whereDate('check_out', '>', now()->toDateString())
            ->with(['room.branch'])
            ->latest('check_in')
            ->first();
    }

    public function guestCanAccessFromBooking(Booking $booking): bool
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            return false;
        }

        return $booking->check_in
            && $booking->check_out
            && now()->toDateString() >= $booking->check_in->toDateString()
            && now()->toDateString() < $booking->check_out->toDateString();
    }

    public function launchUrlForBooking(Booking $booking, ?User $user = null, array $extra = []): string
    {
        if (! $this->isReady()) {
            throw new RuntimeException('Restaurant integration is not configured.');
        }

        $entryUrl = $this->settings->restaurantEntryUrl();
        if (! $entryUrl) {
            throw new RuntimeException('Restaurant entry URL is invalid.');
        }

        $token = $this->issueGuestHandoffToken($booking, $user, $extra);

        return $entryUrl.'?token='.rawurlencode($token);
    }

    public function issueGuestHandoffToken(Booking $booking, ?User $user = null, array $extra = []): string
    {
        $secret = (string) $this->settings->restaurant_sso_shared_secret;
        if ($secret === '') {
            throw new RuntimeException('Restaurant SSO shared secret is missing.');
        }

        $issuedAt = CarbonImmutable::now();
        $expiresAt = $issuedAt->addMinutes($this->settings->restaurantTokenTtlMinutes());
        $user ??= $booking->user;

        $payload = array_merge([
            'iss' => config('app.url'),
            'aud' => $this->settings->restaurant_api_base_url,
            'iat' => $issuedAt->timestamp,
            'exp' => $expiresAt->timestamp,
            'jti' => (string) Str::uuid(),
            'sub' => 'hotel-guest:'.($user?->id ?? $booking->id),
            'booking_reference' => $booking->public_reference,
            'booking_id' => $booking->id,
            'guest_name' => trim(($booking->first_name ?? '').' '.($booking->last_name ?? '')),
            'guest_email' => $booking->email,
            'guest_phone' => $booking->phone,
            'room_id' => $booking->room_id,
            'room_name' => $booking->room?->name,
            'room_number' => $booking->room?->room_number,
            'branch_id' => $booking->room?->hotel_branch_id,
            'branch_name' => $booking->room?->branch?->name,
            'check_in' => $booking->check_in?->format('Y-m-d'),
            'check_out' => $booking->check_out?->format('Y-m-d'),
            'hotel_app_key' => $this->settings->restaurant_api_key ?: null,
            'source' => 'hotel-guest-portal',
        ], $extra);

        $header = [
            'alg' => 'HS256',
            'typ' => 'JWT',
        ];

        $segments = [
            $this->base64UrlEncode(json_encode($header, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
            $this->base64UrlEncode(json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)),
        ];

        $signature = hash_hmac('sha256', implode('.', $segments), $secret, true);
        $segments[] = $this->base64UrlEncode($signature);

        return implode('.', $segments);
    }

    public function pingHealth(): array
    {
        if (! $this->isReady()) {
            return [
                'ok' => false,
                'message' => 'Integration is not fully configured yet.',
            ];
        }

        $base = rtrim((string) $this->settings->restaurant_api_base_url, '/');
        $timeout = $this->settings->restaurantApiTimeoutSeconds();

        try {
            $response = Http::timeout($timeout)
                ->acceptJson()
                ->withHeaders($this->serverHeaders())
                ->get($base.'/health');

            return [
                'ok' => $response->successful(),
                'status' => $response->status(),
                'message' => $response->successful() ? 'Restaurant API reachable.' : 'Restaurant API returned an unexpected response.',
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function serverHeaders(): array
    {
        $headers = [
            'X-Hotel-Client' => 'mambosasa-hotel-app',
        ];

        if (filled($this->settings->restaurant_api_key)) {
            $headers['X-Api-Key'] = (string) $this->settings->restaurant_api_key;
        }

        if (filled($this->settings->restaurant_api_secret)) {
            $headers['X-Api-Secret'] = (string) $this->settings->restaurant_api_secret;
        }

        return $headers;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }
}
