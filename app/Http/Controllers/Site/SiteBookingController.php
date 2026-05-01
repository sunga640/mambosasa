<?php

namespace App\Http\Controllers\Site;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreSiteBookingRequest;
use App\Models\Booking;
use App\Models\BookingMethod;
use App\Models\HotelService;
use App\Models\Room;
use App\Models\RoomType;
use App\Models\SystemSetting;
use App\Services\BookingLifecycleService;
use App\Support\HomeHeroSlides;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SiteBookingController extends Controller
{
    /**
     * Hii ndio method iliyokuwa inaleta error (potea)
     */
    public function show(Request $request): View
    {
        Artisan::call('bookings:expire-pending');

        $roomTypes = RoomType::query()
            ->where('is_active', true)
            ->with(['branch', 'rooms.images'])
            ->orderBy('name')
            ->get();

        $methods = BookingMethod::query()
            ->where('is_active', true)
            ->visibleOnPublicSite()
            ->orderBy('sort_order')
            ->get();

        $typeId = (int) $request->query('type');
        $selectedType = $typeId ? $roomTypes->firstWhere('id', $typeId) : $roomTypes->first();
        $bookingHeroUrl = $selectedType?->heroImageUrl() ?: HomeHeroSlides::urlForSlideNumber(1);

        $roomPrices = [];
        if ($selectedType) {
            $base = (float) $selectedType->price;
            foreach ($selectedType->rooms as $r) {
                $rp = $r->price !== null && (float) $r->price > 0 ? (float) $r->price : $base;
                $roomPrices[$r->id] = (int) round($rp);
            }
        }

        return view('site.booking', [
            'roomTypes' => $roomTypes,
            'methods' => $methods,
            'selectedType' => $selectedType,
            'heroUrl' => $bookingHeroUrl,
            'roomPrices' => $roomPrices,
            'bookingHotelServices' => HotelService::query()
                ->listedForGuests($selectedType?->hotel_branch_id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ]);
    }

    public function store(StoreSiteBookingRequest $request): RedirectResponse
    {
        Artisan::call('bookings:expire-pending');

        $data = $request->validated();

        $method = BookingMethod::query()
            ->where('id', $data['booking_method_id'])
            ->where('is_active', true)
            ->visibleOnPublicSite()
            ->first();

        if (! $method) {
            return back()->withInput()->withErrors(['booking_method_id' => __('Invalid payment method.')]);
        }

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $nights = max(1, $checkIn->diffInDays($checkOut));

        $roomType = RoomType::query()->findOrFail($data['room_type_id']);
        $roomId = (int) $data['room_id'];
        $room = Room::query()->where('room_type_id', $roomType->id)->find($roomId);

        // Inakagua upatikanaji (Bookings + Maintenance)
        if (! $room || ! $this->isRoomAvailableForDates($room, $checkIn, $checkOut)) {
            return back()->withInput()->withErrors(['room_id' => __('This room is not available on selected dates.')]);
        }

        $nightPrice = (float) ($room->price && (float) $room->price > 0 ? $room->price : $roomType->price);
        $total = (float) round($nightPrice * $nights);
        $timeout = (int) SystemSetting::current()->booking_payment_timeout_minutes;
        $timeout = $timeout > 0 ? $timeout : 30;

        $booking = Booking::query()->create([
            'public_reference' => 'BK-'.strtoupper(Str::random(8)),
            'user_id' => auth()->id(),
            'room_id' => $room->id,
            'booking_method_id' => $method->id,
            'status' => BookingStatus::PendingPayment,
            'payment_deadline_at' => now()->addMinutes($timeout),
            'check_in' => $checkIn,
            'check_out' => $checkOut,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
            'adults' => 1,
            'children' => 0,
            'rooms_count' => 1,
            'nights' => $nights,
            'total_amount' => $total,
            'terms_accepted' => true,
            'special_requests' => $data['special_requests'] ?? null,
        ]);

        $booking->refresh();
        try {
            app(BookingLifecycleService::class)->handleNewBooking($booking);
        } catch (\Throwable $e) {
            report($e);
            // Booking already exists; keep guest flow moving to waiting/confirmation page.
        }

        return redirect()->route('site.booking.confirmation', ['reference' => $booking->public_reference]);
    }

    public function confirmation(string $reference): View
    {
        $booking = Booking::query()
            ->where('public_reference', $reference)
            ->with(['room.branch', 'room.images', 'method', 'invoice'])
            ->firstOrFail();

        return view('site.booking-confirmation', ['booking' => $booking]);
    }

    public function availability(Request $request)
    {
        $data = $request->validate([
            'room_type_id' => ['required', 'integer', 'exists:room_types,id'],
            'check_in' => ['required', 'date'],
            'check_out' => ['required', 'date', 'after:check_in'],
            'room_id' => ['nullable', 'integer', 'exists:rooms,id'],
        ]);

        $checkIn = Carbon::parse($data['check_in'])->startOfDay();
        $checkOut = Carbon::parse($data['check_out'])->startOfDay();
        $rooms = $this->roomsForType((int) $data['room_type_id']);
        $availableCount = 0;

        $roomsPayload = $rooms->map(function (Room $room) use ($checkIn, $checkOut, &$availableCount) {
            $isAvailable = $this->isRoomAvailableForDates($room, $checkIn, $checkOut);
            if ($isAvailable) {
                $availableCount++;
            }

            return [
                'id' => $room->id,
                'name' => $room->name,
                'room_number' => $room->room_number,
                'is_available' => $isAvailable,
            ];
        })->values();

        $selectedRoomAvailable = null;
        if (! empty($data['room_id'])) {
            $picked = $roomsPayload->firstWhere('id', (int) $data['room_id']);
            $selectedRoomAvailable = (bool) ($picked['is_available'] ?? false);
        }

        return response()->json([
            'available' => $availableCount > 0,
            'available_count' => $availableCount,
            'selected_room_available' => $selectedRoomAvailable,
            'rooms' => $roomsPayload,
        ]);
    }

    /**
     * Inapata tarehe za Kalenda (Bookings + Maintenance)
     */
    public function roomCalendar(Request $request)
    {
        $data = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
        ]);

        $room = Room::query()->findOrFail((int) $data['room_id']);
        $ranges = collect();

        // 1. Bookings zilizopo
        $bookings = $room->bookings()
            ->whereIn('status', [BookingStatus::PendingPayment, BookingStatus::Confirmed])
            ->whereDate('check_out', '>=', now()->toDateString())
            ->get();

        foreach ($bookings as $b) {
            $ranges->push([
                'from' => Carbon::parse($b->check_in)->toDateString(),
                'to' => Carbon::parse($b->check_out)->subDay()->toDateString(),
            ]);
        }

        // 2. MAREKEBISHO: Inachukua tarehe halisi za Maintenance
        $maintenances = $room->maintenances()
            ->where('status', MaintenanceStatus::Active)
            ->get();

        foreach ($maintenances as $m) {
            $ranges->push([
                'from' => Carbon::parse($m->started_at)->toDateString(),
                'to' => $m->due_at ? Carbon::parse($m->due_at)->toDateString() : now()->addMonths(2)->toDateString(),
            ]);
        }

        return response()->json([
            'booked_ranges' => $ranges->values(),
            'under_maintenance' => $room->isEffectivelyInUse(),
        ]);
    }
private function roomsForType(int $roomTypeId)
{
    return Room::query()
        ->where('room_type_id', $roomTypeId)
        ->where('status', 'available')
        ->where(function ($q): void {
            $q->whereNull('force_in_use')->orWhere('force_in_use', false);
        })
        /**
         * REKEBISHO:
         * Tunazuia (exclude) vyumba ambavyo vina maintenance INAYOENDELEA LEO.
         * Hii itaficha chumba kabisa kwenye list kama fundi yupo kazini sasa hivi.
         */
        ->whereDoesntHave('maintenances', function ($q) {
            $q->where('status', \App\Enums\MaintenanceStatus::Active)
              ->whereDate('started_at', '<=', now())
              ->where(function ($q2) {
                  $q2->whereNull('due_at') // Ikiwa haina tarehe ya kuisha, chumba kifungwe
                    ->orWhereDate('due_at', '>=', now()); // Au ikiwa tarehe ya kuisha bado haijafika
              });
        })
        ->orderBy('room_number')
        ->get();
}
    private function isRoomAvailableForDates(Room $room, Carbon $checkIn, Carbon $checkOut): bool
    {
        // Kagua Bookings
        $hasBooking = $room->bookings()
            ->whereIn('status', [BookingStatus::PendingPayment, BookingStatus::Confirmed])
            ->whereDate('check_in', '<', $checkOut)
            ->whereDate('check_out', '>', $checkIn)
            ->exists();

        if ($hasBooking) return false;

        // Kagua Maintenance
        $hasMaintenance = $room->maintenances()
            ->where('status', MaintenanceStatus::Active)
            ->whereDate('started_at', '<', $checkOut)
            ->where(function ($q) use ($checkIn) {
                $q->whereNull('due_at')
                  ->orWhereDate('due_at', '>', $checkIn);
            })
            ->exists();

        return ! $hasMaintenance;
    }
}
