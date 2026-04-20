<?php

namespace App\Http\Controllers\Member;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingHotelServiceRequest;
use App\Models\HotelService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MemberHotelServiceRequestController extends Controller
{
    public function store(Request $request, Booking $booking): RedirectResponse
    {
        $u = $request->user();
        abort_unless($booking->user_id === $u->id || $booking->email === $u->email, 403);

        abort_unless(
            in_array($booking->status, [BookingStatus::Confirmed, BookingStatus::PendingPayment], true),
            400
        );

        $data = $request->validate([
            'hotel_service_id' => ['required', 'integer', 'exists:hotel_services,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:99'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking->loadMissing('room');
        $branchId = (int) $booking->room->hotel_branch_id;

        $service = HotelService::query()->whereKey($data['hotel_service_id'])->firstOrFail();
        abort_unless($service->is_active, 400);

        $ok = $service->hotel_branch_id === null || (int) $service->hotel_branch_id === $branchId;
        abort_unless($ok, 400);

        BookingHotelServiceRequest::query()->create([
            'booking_id' => $booking->id,
            'hotel_service_id' => $service->id,
            'quantity' => max(1, (int) ($data['quantity'] ?? 1)),
            'status' => 'pending',
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('bookings.show', $booking)
            ->with('status', __('Service request submitted. The hotel will follow up.'));
    }
}
