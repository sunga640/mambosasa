<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\SystemSetting;
use App\Services\StayEndNotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class NotifyStayEndedBookings extends Command
{
    protected $signature = 'bookings:notify-stay-ended';

    protected $description = 'Create dashboard notifications when a confirmed stay checkout date has passed (room available)';

    public function handle(StayEndNotificationService $stayEnds): int
    {
        $now = now();
        $settings = SystemSetting::current();

        Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->whereNotNull('check_out')
            ->whereDate('check_out', '<=', $now->toDateString())
            ->whereNull('checkout_notified_at')
            ->with(['room.branch'])
            ->chunkById(50, function ($bookings) use ($stayEnds, $now, $settings): void {
                foreach ($bookings as $booking) {
                    $clock = $settings->checkoutTimeForDate($booking->check_out);
                    $checkoutAt = Carbon::parse($booking->check_out->toDateString().' '.$clock, config('app.timezone'));
                    if ($checkoutAt->gt($now)) {
                        continue;
                    }

                    $stayEnds->notifyStayEnded($booking);
                }
            });

        return self::SUCCESS;
    }
}
