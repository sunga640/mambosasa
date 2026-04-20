<?php

namespace App\Http\Controllers;

use App\Http\Requests\Member\UpdateBookingDatesRequest;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Booking;
use App\Models\HotelBranch;
use App\Models\HotelService;
use App\Models\DashboardNotification;
use App\Services\InvoiceResendService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class MemberBookingController extends Controller
{
    private function memberBookingScope(Builder $query, Request $request): void
    {
        $u = $request->user();
        $query->where(function ($q) use ($u) {
            $q->where('user_id', $u->id)->orWhere('email', $u->email);
        });
    }

    private function ensureMemberOwnsBooking(Request $request, Booking $booking): void
    {
        $u = $request->user();
        abort_unless(
            $booking->user_id === $u->id || $booking->email === $u->email,
            403
        );
    }

    public function index(Request $request): View
    {
        Artisan::call('bookings:expire-pending');

        $bookings = Booking::query();
        $this->memberBookingScope($bookings, $request);
        if ($bid = session('member_booking_branch_id')) {
            $bookings->whereHas('room', fn ($r) => $r->where('hotel_branch_id', (int) $bid));
        }
        $bookings = $bookings
            ->with(['room.branch', 'method', 'invoice'])
            ->latest()
            ->paginate(15);

        return view('member.bookings.index', [
            'bookings' => $bookings,
            'memberBranchFilterId' => session('member_booking_branch_id'),
            'memberBranchesForFilter' => HotelBranch::query()->orderBy('name')->get(),
        ]);
    }

    public function show(Request $request, Booking $booking): View
{
    $this->ensureMemberOwnsBooking($request, $booking);

    // 1. Pakia data zote muhimu (Eager Loading)
    $booking->load(['room.branch', 'room.images', 'method', 'invoice', 'hotelServiceRequests.service']);

    // 2. HAPA NDIYO FIX: Pata tarehe ambazo chumba hiki hakipatikani (Booked au Maintenance)
    // Hakikisha umeongeza function ya getUnavailableDates() kwenye Model ya Room kwanza!
    $disabledDates = $booking->room->getUnavailableDates();

    $branchId = (int) $booking->room->hotel_branch_id;
    $catalogServices = HotelService::query()
        ->listedForGuests($branchId)
        ->orderBy('category')
        ->orderBy('sort_order')
        ->orderBy('name')
        ->get();

    // 3. Pitisha 'disabledDates' kwenda kwenye View
    return view('member.bookings.show', [
        'booking' => $booking,
        'catalogServices' => $catalogServices,
        'disabledDates' => $disabledDates, // Ongeza hii hapa
    ]);
}

    public function requestExtend(Request $request, Booking $booking): RedirectResponse
    {
        $this->ensureMemberOwnsBooking($request, $booking);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1500'],
        ]);

        $booking->loadMissing('room.branch');

        DashboardNotification::query()->create([
            'booking_id' => $booking->id,
            'room_id' => $booking->room_id,
            'kind' => 'extend_request',
            'title' => __('Extend stay request — :ref', ['ref' => $booking->public_reference]),
            'body' => $data['message'],
            'meta' => [
                'customer_email' => $booking->email,
                'customer_phone' => $booking->phone,
            ],
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', __('Your extension request was sent to the hotel.'));
    }

    public function resendInvoice(Request $request, Booking $booking, InvoiceResendService $resend): RedirectResponse
    {
        $this->ensureMemberOwnsBooking($request, $booking);

        $resend->resend($booking);

        return redirect()->route('bookings.show', $booking)->with('status', __('Invoice sent again to your email and phone.'));
    }

    // Ndani ya MemberBookingController.php

public function updateDates(UpdateBookingDatesRequest $request, Booking $booking): RedirectResponse
{
    $this->ensureMemberOwnsBooking($request, $booking);

    $checkIn = Carbon::parse($request->validated('check_in'))->startOfDay();
    $checkOut = Carbon::parse($request->validated('check_out'))->startOfDay();

    $booking->loadMissing('room');
    $room = $booking->room;

    // --- HAPA NDIYO FIX ---
    // Angalia kama kuna booking nyingine inayopishana na tarehe hizi mpya
    $overlap = Booking::where('room_id', $room->id)
        ->where('id', '!=', $booking->id) // Usijilinganishe na hii booking yenyewe
        ->whereIn('status', [\App\Enums\BookingStatus::Confirmed, \App\Enums\BookingStatus::PendingPayment])
        ->where(function ($query) use ($checkIn, $checkOut) {
            $query->whereBetween('check_in', [$checkIn, $checkOut->subMinute()])
                  ->orWhereBetween('check_out', [$checkIn->addMinute(), $checkOut])
                  ->orWhere(function ($q) use ($checkIn, $checkOut) {
                      $q->where('check_in', '<=', $checkIn)
                        ->where('check_out', '>=', $checkOut);
                  });
        })->exists();

    if ($overlap) {
        return back()->withErrors(['check_in' => __('The room is already booked for the selected dates.')]);
    }

    // Angalia kama kuna Maintenance inayopishana
    $maintenanceOverlap = $room->maintenances()
        ->where('status', '!=', \App\Enums\MaintenanceStatus::Completed)
        ->where(function ($q) use ($checkIn, $checkOut) {
            $q->whereBetween('started_at', [$checkIn, $checkOut])
              ->orWhereBetween('due_at', [$checkIn, $checkOut]);
        })->exists();

    if ($maintenanceOverlap) {
        return back()->withErrors(['check_in' => __('The room will be under maintenance during these dates.')]);
    }
    // --- MWISHO WA FIX ---

    $nights = max(1, $checkIn->diffInDays($checkOut));
    $total = (float) $room->price * $nights * (int) $booking->rooms_count;

    $booking->update([
        'check_in' => $checkIn,
        'check_out' => $checkOut,
        'nights' => $nights,
        'total_amount' => $total,
    ]);

    return redirect()->route('bookings.show', $booking)->with('status', __('Stay dates updated successfully.'));
}
}
