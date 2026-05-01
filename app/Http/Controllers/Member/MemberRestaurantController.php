<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Services\RestaurantIntegrationService;
use Illuminate\Http\RedirectResponse;

class MemberRestaurantController extends Controller
{
    public function __invoke(RestaurantIntegrationService $restaurant): RedirectResponse
    {
        abort_unless($restaurant->isReady(), 503);

        $booking = $restaurant->activeBookingForUser(auth()->user());
        if (! $booking) {
            return redirect()
                ->route('dashboard')
                ->with('status', __('Restaurant ordering opens for guests with an active confirmed stay.'));
        }

        return redirect()->away($restaurant->launchUrlForBooking($booking, auth()->user()));
    }
}
