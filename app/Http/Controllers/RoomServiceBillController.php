<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\RoomServiceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomServiceBillController extends Controller
{
    use InteractsWithStaffScope;

    public function show(RoomServiceOrder $order): View
    {
        $this->ensureRoomServiceOrderInScope($order);

        abort_unless($order->hasGeneratedBill(), 404);

        $order->loadMissing(['room.branch', 'booking', 'items', 'bookingMethod', 'billGeneratedBy']);

        return view('room-service.bill', [
            'order' => $order,
        ]);
    }

    public function generate(Request $request, RoomServiceOrder $order): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($order);

        if (! $order->canGenerateBill()) {
            return back()->withErrors([
                'bill' => __('Deliver the order first, then generate the bill while payment is still pending.'),
            ]);
        }

        if (! $order->hasGeneratedBill()) {
            $order->forceFill([
                'bill_generated_at' => now(),
                'bill_generated_by_user_id' => $request->user()?->id,
            ])->save();
        }

        return redirect()->route($request->routeIs('kitchen.*') ? 'kitchen.orders.bill.show' : 'reception.room-service.bill.show', $order);
    }
}
