<?php

namespace App\Http\Controllers\Site;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\KitchenRoomQr;
use App\Models\RestaurantMenuItem;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\KitchenNotificationService;
use App\Services\RoomServiceOrderPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;

class KitchenQrMenuController extends Controller
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
        abort_unless(Schema::hasTable('kitchen_room_qrs'), 404);

        $qr = KitchenRoomQr::query()
            ->with(['room.branch'])
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $qr->forceFill(['last_scanned_at' => now()])->save();
        $setting = SystemSetting::current();
        $availability = $setting->kitchenServiceAvailability(now());
        $menu = ($availability['is_available'] ?? true)
            ? RestaurantMenuItem::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get()
            : collect();

        return view('site.kitchen-menu', [
            'qr' => $qr,
            'serviceAvailability' => $availability,
            'menu' => $menu,
            'recentOrders' => RoomServiceOrder::query()
                ->where('room_id', $qr->room_id)
                ->where('request_source', 'qr')
                ->when(RoomServiceOrder::supportsPaymentTracking(), fn ($query) => $query->where('payment_status', '!=', 'paid'))
                ->with(['items', 'bookingMethod'])
                ->latest()
                ->limit(5)
                ->get(),
            'paymentMethods' => $this->payments->onlineMethods(),
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        abort_unless(Schema::hasTable('kitchen_room_qrs'), 404);

        $qr = KitchenRoomQr::query()
            ->with('room')
            ->where('token', $token)
            ->where('is_active', true)
            ->firstOrFail();

        $availability = SystemSetting::current()->kitchenServiceAvailability(now());
        if (! ($availability['is_available'] ?? true)) {
            throw ValidationException::withMessages([
                'service' => $availability['message'] ?? __('Service is not available right now.'),
            ]);
        }

        $data = $request->validate([
            'guest_name' => ['required', 'string', 'max:120'],
            'guest_phone' => ['nullable', 'string', 'max:40'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'menu_item_id' => ['nullable', 'integer', 'exists:restaurant_menu_items,id'],
            'card_quantities' => ['nullable', 'array'],
            'card_quantities.*' => ['nullable', 'integer', 'min:1', 'max:20'],
            'items' => ['nullable', 'array', 'min:1'],
            'items.*.menu_item_id' => ['nullable', 'integer', 'exists:restaurant_menu_items,id'],
            'items.*.quantity' => ['nullable', 'integer', 'min:1', 'max:20'],
        ]);

        $rows = collect($data['items'] ?? [])
            ->map(fn (array $row) => [
                'menu_item_id' => (int) ($row['menu_item_id'] ?? 0),
                'quantity' => (int) ($row['quantity'] ?? 0),
            ])
            ->filter(fn (array $row) => $row['menu_item_id'] > 0 && $row['quantity'] > 0);

        if ($rows->isEmpty() && ! empty($data['menu_item_id'])) {
            $selectedItemId = (int) $data['menu_item_id'];
            $selectedQuantity = (int) data_get($data, 'card_quantities.'.$selectedItemId, 1);

            if ($selectedQuantity < 1 || $selectedQuantity > 20) {
                throw ValidationException::withMessages([
                    'menu_item_id' => __('Choose a valid quantity before sending the kitchen order.'),
                ]);
            }

            $rows = collect([[
                'menu_item_id' => $selectedItemId,
                'quantity' => $selectedQuantity,
            ]]);
        }

        $rows = $rows->values()->all();

        if ($rows === []) {
            throw ValidationException::withMessages([
                'items' => __('Select at least one dish before sending the kitchen order.'),
            ]);
        }

        $menuRows = RestaurantMenuItem::query()
            ->whereIn('id', array_column($rows, 'menu_item_id'))
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        $total = 0.0;
        $maxPrep = 10;
        foreach ($rows as $row) {
            $item = $menuRows->get($row['menu_item_id']);
            if (! $item) {
                throw ValidationException::withMessages([
                    'items' => __('One or more selected dishes are no longer available.'),
                ]);
            }

            $total += ((float) $item->price * $row['quantity']);
            $maxPrep = max($maxPrep, (int) $item->preparation_minutes);
        }

        $activeBooking = Booking::query()
            ->where('room_id', $qr->room_id)
            ->where('status', 'confirmed')
            ->whereDate('check_in', '<=', now())
            ->whereDate('check_out', '>', now())
            ->first(['id', 'user_id']);

        $bookingId = $activeBooking?->id;

        $userId = User::guestPortalUserId();
        abort_if(! $userId, 503);

        $createdOrder = null;

        DB::transaction(function () use ($userId, $activeBooking, $bookingId, $qr, $rows, $menuRows, $total, $maxPrep, $data, &$createdOrder): void {
            $order = RoomServiceOrder::query()->create($this->orderPayload([
                'user_id' => $activeBooking?->user_id ?: $userId,
                'booking_id' => $bookingId,
                'room_id' => $qr->room_id,
                'hotel_branch_id' => $qr->hotel_branch_id,
                'request_source' => 'qr',
                'guest_name' => $data['guest_name'],
                'guest_phone' => $data['guest_phone'] ?? null,
                'status' => RoomServiceOrderStatus::Pending->value,
                'payment_status' => 'unpaid',
                'estimated_ready_at' => now()->addMinutes($maxPrep),
                'preparation_minutes' => $maxPrep,
                'total_amount' => $total,
                'notes' => $data['notes'] ?? null,
            ]));

            foreach ($rows as $row) {
                $item = $menuRows->get($row['menu_item_id']);
                RoomServiceOrderItem::query()->create([
                    'room_service_order_id' => $order->id,
                    'restaurant_menu_item_id' => $item->id,
                    'item_name' => $item->name,
                    'quantity' => $row['quantity'],
                    'unit_price' => (float) $item->price,
                    'line_total' => (float) $item->price * $row['quantity'],
                ]);
            }

            $createdOrder = $order;
        });

        if ($createdOrder) {
            $this->notifications->notifyNewOrder($createdOrder->fresh(['room']));
        }

        return back()->with('status', __('Kitchen received your order. Estimated preparation time: :minutes minutes.', ['minutes' => $maxPrep]));
    }
}
