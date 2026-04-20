<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\DashboardNotification;

final class StayEndNotificationService
{
    public function notifyStayEnded(Booking $booking): void
    {
        if ($booking->checkout_notified_at !== null) {
            return;
        }

        $room = $booking->room;
        if (! $room) {
            return;
        }

        $roomNo = $room->room_number ? (string) $room->room_number : __('N/A');
        $floor = (string) $room->floor_number;
        $branch = $room->branch?->name ?? __('Unknown branch');
        $guest = trim($booking->first_name.' '.$booking->last_name);

        $title = __('Room :num is now available', ['num' => $roomNo]);
        $body = __('Stay for :guest ended (:ref). Branch: :branch · Floor :floor · Room: :name.', [
            'guest' => $guest !== '' ? $guest : $booking->email,
            'ref' => $booking->public_reference,
            'branch' => $branch,
            'floor' => $floor,
            'name' => $room->name,
        ]);

        DashboardNotification::query()->create([
            'booking_id' => $booking->id,
            'room_id' => $room->id,
            'kind' => 'stay_ended_room_available',
            'title' => $title,
            'body' => $body,
            'meta' => [
                'public_reference' => $booking->public_reference,
                'guest_email' => $booking->email,
                'guest_phone' => $booking->phone,
            ],
        ]);

        $booking->forceFill(['checkout_notified_at' => now()])->save();
    }
}
