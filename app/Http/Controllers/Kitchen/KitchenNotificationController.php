<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\DashboardNotification;
use Illuminate\Http\RedirectResponse;

class KitchenNotificationController extends Controller
{
    public function read(DashboardNotification $notification): RedirectResponse
    {
        abort_unless(
            (int) $notification->recipient_user_id === (int) auth()->id()
            && str_starts_with((string) $notification->kind, 'kitchen-'),
            404
        );

        $notification->markRead();

        return back()->with('status', __('Kitchen notification marked as read.'));
    }
}
