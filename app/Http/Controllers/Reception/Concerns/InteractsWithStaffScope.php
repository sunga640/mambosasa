<?php

namespace App\Http\Controllers\Reception\Concerns;

use App\Models\Booking;
use App\Models\DashboardNotification;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomServiceOrder;
use App\Support\StaffScope;

trait InteractsWithStaffScope
{
    protected function scope(): StaffScope
    {
        return app(StaffScope::class);
    }

    protected function ensureBookingInScope(Booking $booking): void
    {
        $booking->loadMissing('room');
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($ids === [] || ! in_array((int) $booking->room->hotel_branch_id, $ids, true)) {
            abort(404);
        }
    }

    protected function ensureRoomInScope(Room $room): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($ids === [] || ! in_array((int) $room->hotel_branch_id, $ids, true)) {
            abort(404);
        }
    }

    protected function ensureNotificationInScope(DashboardNotification $notification): void
    {
        $notification->loadMissing('room');
        $this->ensureRoomInScope($notification->room);
    }

    protected function ensureMaintenanceInScope(RoomMaintenance $maintenance): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($ids === [] || ! in_array((int) $maintenance->hotel_branch_id, $ids, true)) {
            abort(404);
        }
    }

    protected function ensureRoomServiceOrderInScope(RoomServiceOrder $order): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($ids === [] || ! in_array((int) $order->hotel_branch_id, $ids, true)) {
            abort(404);
        }
    }
}
