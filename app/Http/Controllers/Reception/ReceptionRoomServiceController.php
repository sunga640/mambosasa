<?php

namespace App\Http\Controllers\Reception;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\RoomServiceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReceptionRoomServiceController extends Controller
{
    use InteractsWithStaffScope;

    public function index(): View
    {
        $q = RoomServiceOrder::query()
            ->with(['user', 'room.branch', 'items'])
            ->latest();

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $q->whereRaw('0=1');
            } else {
                $q->whereIn('hotel_branch_id', $ids);
            }
        }

        $orders = $q->paginate(7)->withQueryString();

        return view('reception.room-service.index', [
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, RoomServiceOrder $roomServiceOrder): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($roomServiceOrder);

        $data = $request->validate([
            'status' => ['required', 'in:pending,preparing,delivered,cancelled'],
        ]);

        $status = RoomServiceOrderStatus::from($data['status']);
        $updates = ['status' => $status->value];

        if ($status === RoomServiceOrderStatus::Preparing && ! $roomServiceOrder->estimated_ready_at) {
            $m = max(15, (int) $roomServiceOrder->preparation_minutes);
            $updates['estimated_ready_at'] = now()->addMinutes($m);
        }

        if ($status === RoomServiceOrderStatus::Delivered) {
            $updates['estimated_ready_at'] = now();
        }

        $roomServiceOrder->update($updates);

        return back()->with('status', __('Order updated.'));
    }
}
