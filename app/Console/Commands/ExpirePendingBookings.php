<?php

namespace App\Console\Commands;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class ExpirePendingBookings extends Command
{
    protected $signature = 'bookings:expire-pending';

    protected $description = 'Expire bookings that stayed in waiting-for-payment past the deadline';

    public function handle(): int
    {
        $updated = Booking::query()
            ->where('status', BookingStatus::PendingPayment)
            ->where('payment_deadline_at', '<=', now())
            ->update(['status' => BookingStatus::Expired]);

        if ($updated > 0) {
            $this->info("Expired {$updated} booking(s).");
        }

        $deleted = Booking::query()
            ->where('status', BookingStatus::Expired)
            ->where('payment_deadline_at', '<=', now())
            ->delete();

        if ($deleted > 0) {
            $this->info("Deleted {$deleted} expired booking(s).");
        }

        $guestRoleId = Role::query()->where('slug', Role::GUEST_SLUG)->value('id');
        if ($guestRoleId) {
            $deletedGuests = User::query()
                ->where('role_id', $guestRoleId)
                ->doesntHave('bookings')
                ->delete();
            if ($deletedGuests > 0) {
                $this->info("Deleted {$deletedGuests} orphan guest account(s).");
            }
        }

        return self::SUCCESS;
    }
}
