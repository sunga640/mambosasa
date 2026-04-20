<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BookingPortalController extends Controller
{
    public function show(string $reference): View|RedirectResponse
    {
        $booking = Booking::query()->where('public_reference', $reference)->firstOrFail();
        $plain = Cache::get($this->cacheKey($booking));

        if ($plain === null || $plain === '') {
            return redirect()->route('login')->with('status', __('Use your email and password to log in, or reset your password if needed.'));
        }

        session(['booking_portal_ref' => $reference]);

        return view('site.booking-portal', [
            'booking' => $booking,
            'plainPassword' => $plain,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $ref = session('booking_portal_ref');
        $request->validate([
            'public_reference' => ['required', 'string'],
            'full_name' => ['required', 'string', 'max:255'],
        ]);

        abort_unless($ref && $ref === $request->string('public_reference')->toString(), 403);

        $booking = Booking::query()->where('public_reference', $ref)->firstOrFail();
        $plain = Cache::get($this->cacheKey($booking));

        if ($plain === null || $plain === '') {
            return redirect()->route('login')->withErrors(['email' => __('This sign-in link has expired. Please log in normally or book again.')]);
        }

        $expected = mb_strtolower(preg_replace('/\s+/u', ' ', trim($booking->first_name.' '.$booking->last_name)));
        $got = mb_strtolower(preg_replace('/\s+/u', ' ', trim($request->string('full_name')->toString())));
        if ($expected !== $got) {
            return back()->withInput($request->only('full_name'))->withErrors(['full_name' => __('The full name does not match this booking.')]);
        }

        if (! Auth::attempt(['email' => $booking->email, 'password' => $plain])) {
            return back()->withInput($request->only('full_name'))->withErrors(['full_name' => __('Could not sign you in. Please contact support.')]);
        }

        $request->session()->regenerate();
        Cache::forget($this->cacheKey($booking));
        session()->forget(['booking_portal_ref', 'site_booking_portal_booking_id']);

        return redirect()->intended(route('dashboard'));
    }

    public static function cacheKey(Booking $booking): string
    {
        return 'booking-portal-pwd:'.$booking->id;
    }

    /**
     * Guest / member user for booking email; returns plain password for new or rotated guest accounts.
     */
    public static function provisionUserForBooking(Booking $booking): ?string
    {
        $booking->loadMissing('room');
        $guestRole = Role::query()->where('slug', Role::GUEST_SLUG)->first();
        if (! $guestRole) {
            return null;
        }

        $email = mb_strtolower(trim($booking->email));
        $name = trim($booking->first_name.' '.$booking->last_name);
        $plain = Str::password(14);

        $user = User::query()->where('email', $email)->first();

        if ($user) {
            if (! $user->isGuest()) {
                $booking->forceFill(['user_id' => $user->id])->save();

                return null;
            }
            $user->forceFill([
                'name' => $name,
                'password' => $plain,
                'is_active' => true,
                'uses_system_password' => true,
                'system_password_plain' => $plain,
            ])->save();
        } else {
            $user = User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $plain,
                'uses_system_password' => true,
                'system_password_plain' => $plain,
                'role_id' => $guestRole->id,
                'is_active' => true,
            ]);
        }

        $booking->forceFill(['user_id' => $user->id])->save();

        return $plain;
    }
}
