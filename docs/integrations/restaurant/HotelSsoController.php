<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Support\Integrations\Restaurant\HotelGuestSsoVerifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use RuntimeException;

/**
 * COPY THIS FILE INTO THE RESTAURANT SYSTEM, for example:
 *   app/Http/Controllers/HotelSsoController.php
 *
 * REQUIRED COMPANION FILE:
 *   app/Support/Integrations/HotelGuestSsoVerifier.php
 *
 * REQUIRED CONFIG:
 *   config('services.hotel_guest_sso.shared_secret')
 */
class HotelSsoController extends Controller
{
    public function hotelGuestEntry(Request $request, HotelGuestSsoVerifier $verifier): RedirectResponse
    {
        $sharedSecret = (string) config('services.hotel_guest_sso.shared_secret', '');
        $payload = $verifier->verifyFromRequest($request, $sharedSecret);

        $this->guardAgainstReplay($payload);

        $profile = $verifier->mapToRestaurantGuestProfile($payload);

        /**
         * TODO 1:
         * Find or create the restaurant-side guest/customer record.
         *
         * Example:
         * $guest = RestaurantGuest::updateOrCreate(
         *     ['email' => $profile['email']],
         *     [
         *         'name' => $profile['name'],
         *         'phone' => $profile['phone'],
         *         'source' => 'hotel-sso',
         *         'source_reference' => $profile['source_reference'],
         *     ]
         * );
         */

        /**
         * TODO 2:
         * Store booking/room context in session so the restaurant app knows
         * which room or branch this guest belongs to.
         */
        session([
            'hotel_guest_sso' => [
                'booking_reference' => $profile['source_reference'],
                'room_number' => $profile['room_number'],
                'room_name' => $profile['room_name'],
                'branch_id' => $profile['branch_id'],
                'branch_name' => $profile['branch_name'],
                'guest_email' => $profile['email'],
            ],
        ]);

        /**
         * TODO 3:
         * Log the user into the restaurant system if you have authentication.
         *
         * Example:
         * Auth::login($guest->user);
         */

        return redirect()->route('restaurant.dashboard');
    }

    /**
     * Prevent the same token ID (jti) from being reused repeatedly.
     *
     * @param  array<string, mixed>  $payload
     */
    private function guardAgainstReplay(array $payload): void
    {
        $jti = (string) ($payload['jti'] ?? '');
        $exp = (int) ($payload['exp'] ?? 0);

        if ($jti === '' || $exp <= 0) {
            throw new RuntimeException('Replay guard data is missing.');
        }

        $ttlSeconds = max(60, $exp - now()->timestamp);
        $cacheKey = 'hotel_guest_sso:jti:'.$jti;

        if (Cache::has($cacheKey)) {
            throw new RuntimeException('This hotel token was already used.');
        }

        Cache::put($cacheKey, true, now()->addSeconds($ttlSeconds));
    }
}
