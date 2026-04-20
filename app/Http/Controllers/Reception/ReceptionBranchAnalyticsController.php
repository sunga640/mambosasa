<?php

namespace App\Http\Controllers\Reception;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomMaintenance;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReceptionBranchAnalyticsController extends Controller
{
    public function __invoke(Request $request): View
    {
        abort_unless($request->user()?->isDirector(), 403);

        $rows = [];
        foreach (HotelBranch::query()->orderBy('name')->get() as $br) {
            $bookings = Booking::query()->whereHas('room', fn ($r) => $r->where('hotel_branch_id', $br->id));
            $rows[] = [
                'branch' => $br,
                'bookings_total' => (clone $bookings)->count(),
                'bookings_confirmed' => (clone $bookings)->where('status', BookingStatus::Confirmed)->count(),
                'revenue' => (float) (clone $bookings)->where('status', BookingStatus::Confirmed)->sum('total_amount'),
                'rooms' => Room::query()->where('hotel_branch_id', $br->id)->count(),
                'maintenance_active' => RoomMaintenance::query()->where('hotel_branch_id', $br->id)->where('status', \App\Enums\MaintenanceStatus::Active)->count(),
            ];
        }

        return view('reception.analytics.branches', ['rows' => $rows]);
    }
}
