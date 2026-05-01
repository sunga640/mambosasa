<?php

namespace App\Http\Controllers\Site;

use App\Enums\BookingStatus;
use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RestaurantMenuItem;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\User;
use App\Services\KitchenNotificationService;
use App\Services\RoomServiceOrderPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class GuestStayController extends Controller
{
    public function __construct(
        private readonly RoomServiceOrderPaymentService $payments,
        private readonly KitchenNotificationService $notifications,
    ) {}

    private function orderPayload(array $attributes): array
    {
        if (! RoomServiceOrder::supportsPaymentTracking()) {
            unset(
                $attributes['public_reference'],
                $attributes['booking_method_id'],
                $attributes['payment_status'],
                $attributes['payment_reference'],
                $attributes['paid_at'],
            );
        }

        return $attributes;
    }

    public function show(string $token): View
    {
        $booking = Booking::findByValidGuestToken($token);
        abort_if(! $booking, 404);

        $booking->load(['room.branch', 'room.images', 'room.rank', 'method', 'invoice']);

        $menu = RestaurantMenuItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $canOrderService = $this->canOrderRoomService($booking);

        $recentOrders = RoomServiceOrder::query()
            ->where('booking_id', $booking->id)
            ->when(RoomServiceOrder::supportsPaymentTracking(), fn ($query) => $query->where('payment_status', '!=', 'paid'))
            ->with(['room', 'items', 'bookingMethod'])
            ->latest()
            ->limit(10)
            ->get();

        return view('site.guest-stay', [
            'token' => $token,
            'booking' => $booking,
            'menu' => $menu,
            'canOrderService' => $canOrderService,
            'recentOrders' => $recentOrders,
            'paymentMethods' => $this->payments->onlineMethods(),
        ]);
    }

    public function storeRoomService(Request $request, string $token): RedirectResponse
    {
        $booking = Booking::findByValidGuestToken($token);
        abort_if(! $booking, 404);

        if (! $this->canOrderRoomService($booking)) {
            throw ValidationException::withMessages([
                'items' => __('Room service opens after your stay is paid and active.'),
            ]);
        }

        $data = $request->validate([
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_item_id' => ['required', 'integer', 'exists:restaurant_menu_items,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $rows = [];
        foreach ($data['items'] as $row) {
            $mid = (int) $row['menu_item_id'];
            $qty = (int) $row['quantity'];
            if ($mid > 0 && $qty > 0) {
                $rows[] = ['menu_item_id' => $mid, 'quantity' => $qty];
            }
        }
        if ($rows === []) {
            throw ValidationException::withMessages([
                'items' => __('Select at least one item with quantity greater than zero.'),
            ]);
        }

        $booking->loadMissing('room');
        $roomId = (int) $booking->room_id;
        $menuRows = RestaurantMenuItem::query()
            ->whereIn('id', array_column($rows, 'menu_item_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $total = 0.0;
        $maxPrep = 15;
        foreach ($rows as $row) {
            $item = $menuRows->get($row['menu_item_id']);
            if (! $item) {
                throw ValidationException::withMessages([
                    'items' => __('One or more menu items are unavailable.'),
                ]);
            }
            $line = (float) $item->price * $row['quantity'];
            $total += $line;
            $maxPrep = max($maxPrep, (int) $item->preparation_minutes);
        }

        $branchId = (int) $booking->room->hotel_branch_id;
        $userId = $booking->user_id ?? User::guestPortalUserId();
        abort_if(! $userId, 503);

        $createdOrder = null;

        DB::transaction(function () use ($userId, $booking, $roomId, $branchId, $rows, $menuRows, $total, $maxPrep, $data, &$createdOrder): void {
            $eta = now()->addMinutes($maxPrep);
            $order = RoomServiceOrder::query()->create($this->orderPayload([
                'user_id' => $booking->user_id ?: $userId,
                'booking_id' => $booking->id,
                'room_id' => $roomId,
                'hotel_branch_id' => $branchId,
                'request_source' => 'portal',
                'guest_name' => trim($booking->first_name.' '.$booking->last_name),
                'guest_phone' => $booking->phone,
                'status' => RoomServiceOrderStatus::Pending->value,
                'payment_status' => 'unpaid',
                'estimated_ready_at' => $eta,
                'preparation_minutes' => $maxPrep,
                'total_amount' => $total,
                'notes' => $data['notes'] ?? null,
            ]));

            foreach ($rows as $row) {
                $item = $menuRows->get($row['menu_item_id']);
                $qty = $row['quantity'];
                $unit = (float) $item->price;
                RoomServiceOrderItem::query()->create([
                    'room_service_order_id' => $order->id,
                    'restaurant_menu_item_id' => $item->id,
                    'item_name' => $item->name,
                    'quantity' => $qty,
                    'unit_price' => $unit,
                    'line_total' => $unit * $qty,
                ]);
            }

            $createdOrder = $order;
        });

        if ($createdOrder) {
            $this->notifications->notifyNewOrder($createdOrder->fresh(['room']));
        }

        return redirect()
            ->route('site.guest-stay.show', ['token' => $token])
            ->with('status', __('Order placed. Estimated ready around :t.', ['t' => now()->addMinutes($maxPrep)->format('H:i')]));
    }

    private function canOrderRoomService(Booking $booking): bool
    {
        if ($booking->status !== BookingStatus::Confirmed) {
            return false;
        }

        return $booking->check_in
            && $booking->check_out
            && now()->toDateString() >= $booking->check_in->toDateString()
            && now()->toDateString() < $booking->check_out->toDateString();
    }
}
