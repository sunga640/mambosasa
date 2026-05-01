<?php

namespace App\Http\Controllers\Reception;

use App\Enums\BookingStatus;
use App\Enums\RoomStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Http\Requests\Admin\UpdateAdminBookingRequest;
use App\Http\Requests\Reception\StoreManualBookingRequest;
use App\Models\Booking;
use App\Models\BookingMethod;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Room;
use App\Models\SystemSetting;
use App\Services\ActivityLogger;
use App\Services\BookingLifecycleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ReceptionBookingController extends Controller
{
    use InteractsWithStaffScope;

    public function index(Request $request): View
    {
        Artisan::call('bookings:expire-pending');

        $statusFilter = $request->query('status');
        $q = trim((string) $request->query('q', ''));

        $bookings = Booking::query()
            ->with(['room.branch', 'method', 'user', 'invoice'])
            ->when($statusFilter && in_array($statusFilter, BookingStatus::values(), true), fn ($b) => $b->where('status', $statusFilter))
            ->when($q !== '', function ($b) use ($q) {
                $b->where(function ($b) use ($q) {
                    $b->where('public_reference', 'like', '%'.$q.'%')
                        ->orWhere('email', 'like', '%'.$q.'%')
                        ->orWhere('phone', 'like', '%'.$q.'%')
                        ->orWhere('first_name', 'like', '%'.$q.'%')
                        ->orWhere('last_name', 'like', '%'.$q.'%');
                });
            });

        $this->scope()->filterBookingsByBranch($bookings);

        $bookings = $bookings->latest()->paginate(7)->withQueryString();

        return view('reception.bookings.index', [
            'bookings' => $bookings,
            'statusFilter' => $statusFilter,
            'q' => $q,
            'statuses' => BookingStatus::cases(),
        ]);
    }


    public function create(): View
    {
        Artisan::call('bookings:expire-pending');

        $roomsQuery = Room::query()
            ->with([
                'branch',
                'activeMaintenances',
                'bookings' => function ($query): void {
                    $query->whereIn('status', [BookingStatus::Confirmed, BookingStatus::PendingPayment])
                        ->where(function ($rangeQuery): void {
                            $rangeQuery->whereNull('check_out')
                                ->orWhereDate('check_out', '>', now()->toDateString());
                        });
                },
            ]);

        $this->scope()->filterRoomsByBranch($roomsQuery);

        $methods = BookingMethod::query()->where('is_active', true)->orderBy('sort_order')->get();
        $rooms = $roomsQuery->orderBy('name')->get()->map(function (Room $room) {
            $room->setAttribute('is_currently_available', ! $room->isEffectivelyInUse() && $room->status === RoomStatus::Available);

            return $room;
        });

        return view('reception.bookings.create', [
            'rooms' => $rooms,
            'methods' => $methods,
        ]);
    }

    /**
     * Method ya kutoa tarehe ambazo chumba kimeshachukuliwa
     */
    public function getBookedDates(Room $room)
    {
        $this->ensureRoomInScope($room);

        // Tafuta bookings zote ambazo zipo hai (Confirmed au Pending)
        $bookings = Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::PendingPayment])
            ->where('check_out', '>=', now()->startOfDay())
            ->get(['check_in', 'check_out']);

        $disabledDates = [];
        foreach ($bookings as $b) {
            $disabledDates[] = [
                'from' => $b->check_in->format('Y-m-d'),
                'to' => $b->check_out->subDay()->format('Y-m-d')
            ];
        }

        $isUnderMaintenance = $room->status === RoomStatus::UnderMaintenance;
        if ($isUnderMaintenance) {
            $disabledDates[] = [
                'from' => now()->toDateString(),
                'to' => now()->addYears(5)->toDateString(),
            ];
        }

        return response()->json([
            'disabled_ranges' => $disabledDates,
            'under_maintenance' => $isUnderMaintenance,
        ]);
    }

    public function store(StoreManualBookingRequest $request, BookingLifecycleService $lifecycle): RedirectResponse
    {
        Artisan::call('bookings:expire-pending');

        $data = $request->validated();

        $method = BookingMethod::query()
            ->where('id', $data['booking_method_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $room = Room::query()->findOrFail($data['room_id']);
        $this->ensureRoomInScope($room);

        // Kuchukua tarehe kutoka kwenye Flatpickr hidden inputs
        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = max(1, $checkIn->diffInDays($checkOut));
        $total = (float) $room->price * $nights * (int) $data['rooms_count'];

        // Ukaguzi wa mwisho wa upatikanaji (Conflict check)
        $exists = Booking::query()
            ->where('room_id', $room->id)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::PendingPayment])
            ->where(function ($q) use ($checkIn, $checkOut) {
                $q->whereBetween('check_in', [$checkIn, $checkOut->copy()->subDay()])
                  ->orWhereBetween('check_out', [$checkIn->copy()->addDay(), $checkOut]);
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['booking_dates_raw' => __('The room is already booked for these dates.')])->withInput();
        }

        $isCash = $method->slug === 'cash';
        $confirmPaid = $request->boolean('confirm_paid');
        $timeout = (int) SystemSetting::current()->booking_payment_timeout_minutes ?: 30;

        $bookingData = [
            'public_reference' => 'BK-'.strtoupper(Str::random(8)),
            'user_id' => null,
            'room_id' => $room->id,
            'booking_method_id' => $method->id,
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'adults' => 1,
            'children' => 0,
            'rooms_count' => $data['rooms_count'],
            'nights' => $nights,
            'total_amount' => $total,
            'terms_accepted' => true,
            'special_requests' => $data['special_requests'] ?? null,
        ];

        if ($isCash && $confirmPaid) {
            $bookingData['status'] = BookingStatus::Confirmed;
            $bookingData['confirmed_at'] = now();
            $booking = Booking::query()->create($bookingData);

            Customer::syncFromBooking($booking->fresh());
            Invoice::createForBooking($booking->fresh());
            $lifecycle->handlePaymentConfirmed($booking->fresh());
        } else {
            $bookingData['status'] = BookingStatus::PendingPayment;
            $bookingData['payment_deadline_at'] = now()->addMinutes($timeout);
            $booking = Booking::query()->create($bookingData);
            $lifecycle->handleNewBooking($booking->fresh());
        }

        return redirect()->route('reception.bookings.show', $booking)->with('status', __('Booking created successfully.'));
    }

    // Method zingine za show, update, confirmCash zinabaki vilevile...
    public function show(Booking $booking): View
    {
        $this->ensureBookingInScope($booking);
        $booking->load(['room.branch', 'room.images', 'method', 'user', 'invoice']);
        return view('reception.bookings.show', ['booking' => $booking, 'statuses' => BookingStatus::cases()]);
    }

    public function update(UpdateAdminBookingRequest $request, Booking $booking, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $this->ensureBookingInScope($booking);
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
        return redirect()->route('reception.bookings.show', $booking)->with('status', __('Booking updated.'));
    }

    public function confirmCash(Request $request, Booking $booking, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $this->ensureBookingInScope($booking);
        $booking->loadMissing('method');
        abort_unless($booking->method?->slug === 'cash', 400);
        abort_unless($booking->status === BookingStatus::PendingPayment, 400);
        $booking->update(['status' => BookingStatus::Confirmed, 'confirmed_at' => now(), 'payment_deadline_at' => null]);
        Customer::syncFromBooking($booking->fresh());
        Invoice::createForBooking($booking->fresh());
        $lifecycle->handlePaymentConfirmed($booking->fresh());
        return back()->with('status', __('Cash payment confirmed.'));
    }

    public function destroy(Request $request, Booking $booking): RedirectResponse
    {
        abort_unless($request->user()?->isSuperAdmin() || $request->user()?->isManager(), 403);

        $this->ensureBookingInScope($booking);

        if ($booking->status === BookingStatus::Confirmed) {
            return back()->withErrors(['booking' => __('Confirmed bookings cannot be deleted. Change status first or keep the record for accounting.')]);
        }

        DB::transaction(function () use ($booking): void {
            $booking->invoice?->delete();
            $booking->delete();
        });

        return redirect()
            ->route('reception.bookings.index')
            ->with('status', __('Booking deleted.'));
    }
}
