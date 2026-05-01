<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\RestaurantIntegrationService;
use Illuminate\Http\RedirectResponse;

class GuestRestaurantAccessController extends Controller
{
    public function __invoke(string $token, RestaurantIntegrationService $restaurant): RedirectResponse
    {
        $booking = Booking::findByValidGuestToken($token);
        abort_if(! $booking, 404);
        abort_unless($restaurant->isReady(), 503);

        if (! $restaurant->guestCanAccessFromBooking($booking)) {
            return redirect()
                ->route('site.guest-stay.show', ['token' => $token])
                ->with('status', __('Restaurant ordering opens after check-in and while your stay is active.'));
        }

        $booking->loadMissing(['room.branch', 'user']);

        return redirect()->away($restaurant->launchUrlForBooking($booking, $booking->user, [
            'guest_access_token' => $booking->guest_access_token,
        ]));
    }
}
