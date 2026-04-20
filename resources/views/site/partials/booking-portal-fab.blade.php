@php
    $portalBookingId = session('site_booking_portal_booking_id');
    $portalBooking = $portalBookingId ? \App\Models\Booking::query()->find($portalBookingId) : null;
@endphp
@if ($portalBooking && \Illuminate\Support\Facades\Cache::has(\App\Http\Controllers\Site\BookingPortalController::cacheKey($portalBooking)))
    <a
        href="{{ \Illuminate\Support\Facades\URL::temporarySignedRoute('site.booking.portal', now()->addHours(48), ['reference' => $portalBooking->public_reference]) }}"
        class="site-booking-portal-fab"
        title="{{ __('Open your booking account') }}"
        aria-label="{{ __('Open your booking account') }}"
    >
        <span class="site-booking-portal-fab__inner" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </span>
        <span class="site-booking-portal-fab__badge" aria-hidden="true">1</span>
    </a>
@endif
