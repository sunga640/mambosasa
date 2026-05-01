<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DashboardNotification;
use App\Services\BookingLifecycleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Support\StaffScope;
use Illuminate\Http\Request;

class DashboardNotificationController extends Controller
{
    public function index(StaffScope $scope): View
    {
        $notifications = DashboardNotification::query()
            ->with(['booking', 'room.branch']);
        $scope->filterNotificationsByBranch($notifications);
        $notifications = $notifications->latest()->paginate(7)->withQueryString();

        $unreadCount = DashboardNotification::query();
        $scope->filterNotificationsByBranch($unreadCount);
        $unreadCount = $unreadCount
            ->whereNull('read_at')
            ->whereNull('resolved_at')
            ->count();

        return view('admin.notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function read(Request $request, DashboardNotification $notification, StaffScope $scope): RedirectResponse
    {
        $this->ensureNotificationInScope($notification, $request, $scope);
        $notification->markRead();

        return back()->with('status', __('Marked as read.'));
    }

    public function resolveSignOut(Request $request, DashboardNotification $notification, BookingLifecycleService $lifecycle, StaffScope $scope): RedirectResponse
    {
        $this->ensureNotificationInScope($notification, $request, $scope);
        $booking = $notification->booking;
        abort_if(! $booking, 404);

        $notification->forceFill([
            'read_at' => $notification->read_at ?? now(),
            'resolved_at' => now(),
        ])->save();

        $lifecycle->notifyGuestSignedOut($booking->loadMissing(['room.branch']));

        return back()->with('status', __('Guest notified by email and SMS.'));
    }

    private function ensureNotificationInScope(DashboardNotification $notification, Request $request, StaffScope $scope): void
    {
        $user = $request->user();
        $ids = $scope->branchIds($user);

        if ($notification->recipient_user_id !== null && (int) $notification->recipient_user_id !== (int) $user?->id) {
            abort(404);
        }

        if ($ids === null) {
            return;
        }

        $branchId = (int) ($notification->room?->hotel_branch_id ?? 0);
        if ($ids === [] || ! in_array($branchId, $ids, true)) {
            abort(404);
        }
    }
}
