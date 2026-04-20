<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardNotification;
use App\Services\BookingLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardNotificationController extends Controller
{
    public function index(): View
    {
        $notifications = DashboardNotification::query()
            ->with(['booking', 'room.branch'])
            ->latest()
            ->paginate(7)
            ->withQueryString();

        $unreadCount = DashboardNotification::query()
            ->whereNull('read_at')
            ->whereNull('resolved_at')
            ->count();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function read(DashboardNotification $notification): RedirectResponse
    {
        $notification->markRead();

        return back()->with('status', __('Marked as read.'));
    }

    public function resolveSignOut(DashboardNotification $notification, BookingLifecycleService $lifecycle): RedirectResponse
    {
        $booking = $notification->booking;
        abort_if(! $booking, 404);

        $notification->forceFill([
            'read_at' => $notification->read_at ?? now(),
            'resolved_at' => now(),
        ])->save();

        $lifecycle->notifyGuestSignedOut($booking->loadMissing(['room.branch']));

        return back()->with('status', __('Guest notified by email and SMS.'));
    }
}
