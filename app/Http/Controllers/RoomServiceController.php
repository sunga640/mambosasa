<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Enums\RoomServiceOrderStatus;
use App\Http\Requests\StoreRoomServiceOrderRequest;
use App\Models\Booking;
use App\Models\RestaurantMenuItem;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class RoomServiceController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();

        $menu = RestaurantMenuItem::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $stayBookings = Booking::query()
            ->where('user_id', $user->id)
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('check_in', '<=', now()->toDateString())
            ->whereDate('check_out', '>', now()->toDateString())
            ->with(['room.branch'])
            ->orderBy('check_in')
            ->get();

        $recentOrders = RoomServiceOrder::query()
            ->where('user_id', $user->id)
            ->with(['room', 'items'])
            ->latest()
            ->limit(10)
            ->get();

        return view('member.room-service.index', [
            'menu' => $menu,
            'stayBookings' => $stayBookings,
            'recentOrders' => $recentOrders,
        ]);
    }

    public function store(StoreRoomServiceOrderRequest $request): RedirectResponse
    {
        $user = $request->user();
        $roomId = (int) $request->validated('room_id');

        $booking = Booking::query()
            ->where('user_id', $user->id)
            ->where('room_id', $roomId)
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('check_in', '<=', now()->toDateString())
            ->whereDate('check_out', '>', now()->toDateString())
            ->with('room')
            ->firstOrFail();

        $rows = $request->validatedItems();
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
                return back()->withErrors(['items' => __('One or more menu items are unavailable.')])->withInput();
            }
            $line = (float) $item->price * $row['quantity'];
            $total += $line;
            $maxPrep = max($maxPrep, (int) $item->preparation_minutes);
        }

        $branchId = (int) $booking->room->hotel_branch_id;

        DB::transaction(function () use ($user, $booking, $roomId, $branchId, $rows, $menuRows, $total, $maxPrep, $request): void {
            $eta = now()->addMinutes($maxPrep);
            $order = RoomServiceOrder::query()->create([
                'user_id' => $user->id,
                'booking_id' => $booking->id,
                'room_id' => $roomId,
                'hotel_branch_id' => $branchId,
                'status' => RoomServiceOrderStatus::Pending->value,
                'estimated_ready_at' => $eta,
                'preparation_minutes' => $maxPrep,
                'total_amount' => $total,
                'notes' => $request->validated('notes'),
            ]);

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
        });

        return redirect()
            ->route('member.room-service.index')
            ->with('status', __('Order placed. Estimated ready around :t.', ['t' => now()->addMinutes($maxPrep)->format('H:i')]));
    }
}
