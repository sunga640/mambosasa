<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminBookingRequest;
use App\Models\Booking;
use App\Services\BookingLifecycleService;
use App\Support\StaffScope;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status');
        $q = trim((string) $request->query('q', ''));
        $from = $request->query('from');
        $to = $request->query('to');

        $bookings = Booking::query()
            ->with(['room.branch', 'method', 'user'])
            ->when($statusFilter && in_array($statusFilter, BookingStatus::values(), true), fn ($b) => $b->where('status', $statusFilter))
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($b) use ($q) {
                    $b->where('public_reference', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                });
            })
            ->when($from, fn ($b) => $b->whereDate('created_at', '>=', $from))
            ->when($to, fn ($b) => $b->whereDate('created_at', '<=', $to))
            ->latest()
            ->paginate(7)
            ->withQueryString();

        return view('admin.bookings.index', [
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'q' => $q,
            'from' => $from,
            'to' => $to,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function show(Booking $booking): View
    {
        $booking->load(['room.branch', 'method', 'user', 'invoice']);

        return view('admin.bookings.show', [
            'booking' => $booking,
            'statuses' => BookingStatus::cases(),
        ]);
    }

    public function update(UpdateAdminBookingRequest $request, Booking $booking, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $newStatus = $request->validated('status');
        if (! $newStatus instanceof BookingStatus) {
            $newStatus = BookingStatus::from((string) $newStatus);
        }
        $prev = $booking->status;

        $data = ['status' => $newStatus];
        if ($newStatus === BookingStatus::Confirmed && $prev !== BookingStatus::Confirmed) {
            $data['confirmed_at'] = now();
        }

        $booking->update($data);

        if ($newStatus === BookingStatus::Confirmed && $prev !== BookingStatus::Confirmed) {
            $lifecycle->handlePaymentConfirmed($booking->fresh());
        }

        return redirect()
            ->route('admin.bookings.show', $booking)
            ->with('status', __('Booking updated.'));
    }

    public function destroy(Booking $booking, StaffScope $scope): RedirectResponse
    {
        $booking->loadMissing('room');
        $ids = $scope->branchIds();
        if ($ids !== null) {
            $bid = (int) $booking->room->hotel_branch_id;
            if ($ids === [] || ! in_array($bid, $ids, true)) {
                abort(404);
            }
        }

        if ($booking->status === BookingStatus::Confirmed) {
            return back()->withErrors(['booking' => __('Confirmed bookings cannot be deleted. Change status first or keep the record for accounting.')]);
        }

        DB::transaction(function () use ($booking): void {
            $booking->invoice?->delete();
            $booking->delete();
        });

        return redirect()
            ->route('admin.bookings.index')
            ->with('status', __('Booking deleted.'));
    }
}
