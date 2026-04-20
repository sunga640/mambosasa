<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MemberNotificationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $u = $request->user();
        $alerts = Booking::query()
            ->where(function ($q) use ($u): void {
                $q->where('user_id', $u->id)->orWhere('email', $u->email);
            })
            ->where('status', BookingStatus::PendingPayment)
            ->with('room')
            ->orderBy('payment_deadline_at')
            ->get();

        return view('member.notifications.index', [
            'alerts' => $alerts,
        ]);
    }
}
