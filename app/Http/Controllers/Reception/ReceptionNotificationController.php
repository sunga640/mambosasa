<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\DashboardNotification;
use App\Models\RoomServiceOrder;
use App\Services\BookingLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReceptionNotificationController extends Controller
{
    use InteractsWithStaffScope;

    public function index(): View
    {
        $base = DashboardNotification::query()
            ->with(['booking', 'room.branch']);

        $this->scope()->filterNotificationsByBranch($base);

        $notifications = $base->latest()->paginate(7)->withQueryString();

        $unread = DashboardNotification::query()
            ->whereNull('read_at')
            ->whereNull('resolved_at');
        $this->scope()->filterNotificationsByBranch($unread);
        $unreadCount = $unread->count();

        return view('reception.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function read(DashboardNotification $notification): RedirectResponse
    {
        $this->ensureNotificationInScope($notification);
        $notification->markRead();

        return back()->with('status', __('Marked as read.'));
    }

    public function resolveSignOut(DashboardNotification $notification, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $this->ensureNotificationInScope($notification);

        $booking = $notification->booking;
        abort_if(! $booking, 404);

        if (RoomServiceOrder::supportsPaymentTracking()) {
            $unpaidKitchenOrders = RoomServiceOrder::query()
                ->where('booking_id', $booking->id)
                ->where('payment_status', '!=', 'paid')
                ->count();

            if ($unpaidKitchenOrders > 0) {
                return back()->withErrors([
                    'signout' => __('This guest still has :count unpaid kitchen order(s). Clear them before sign-out.', ['count' => $unpaidKitchenOrders]),
                ]);
            }
        }

        $notification->forceFill([
            'read_at' => $notification->read_at ?? now(),
            'resolved_at' => now(),
        ])->save();

        $lifecycle->notifyGuestSignedOut($booking->loadMissing(['room.branch']));

        return back()->with('status', __('Guest notified by email and SMS.'));
    }
}
