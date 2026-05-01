<?php

namespace App\Services;

use App\Models\DashboardNotification;
use App\Models\RoomServiceOrder;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Throwable;

class KitchenNotificationService
{
    public function __construct(
        private readonly SmsService $sms,
    ) {}

    public function notifyNewOrder(RoomServiceOrder $order): void
    {
        $order->loadMissing(['room', 'assignedTo']);

        $this->kitchenMasterRecipients((int) $order->hotel_branch_id)->each(function (User $user) use ($order): void {
            DashboardNotification::query()->create([
                'booking_id' => $order->booking_id,
                'room_id' => $order->room_id,
                'recipient_user_id' => $user->id,
                'kind' => 'kitchen-new-order',
                'title' => __('New kitchen order received'),
                'body' => __(':guest placed a new order for :room. Reference: :reference.', [
                    'guest' => $order->guest_name ?: __('Guest'),
                    'room' => $order->room?->name ?: __('Room'),
                    'reference' => $order->public_reference ?: '#'.$order->id,
                ]),
                'meta' => [
                    'order_id' => $order->id,
                    'reference' => $order->public_reference,
                    'request_source' => $order->request_source,
                ],
            ]);
        });

        $this->notifyConfiguredRecipients($order);
    }

    public function notifyAssignedTask(RoomServiceOrder $order, User $staffUser): void
    {
        DashboardNotification::query()->create([
            'booking_id' => $order->booking_id,
            'room_id' => $order->room_id,
            'recipient_user_id' => $staffUser->id,
            'kind' => 'kitchen-task-assigned',
            'title' => __('New assigned kitchen task'),
            'body' => __('You have been assigned order :reference for :guest in :room.', [
                'reference' => $order->public_reference ?: '#'.$order->id,
                'guest' => $order->guest_name ?: __('Guest'),
                'room' => $order->room?->name ?: __('Room'),
            ]),
            'meta' => [
                'order_id' => $order->id,
                'reference' => $order->public_reference,
                'assigned_to_user_id' => $staffUser->id,
            ],
        ]);
    }

    private function kitchenMasterRecipients(int $branchId)
    {
        return User::query()
            ->with('role')
            ->where('is_active', true)
            ->where('hotel_branch_id', $branchId)
            ->whereHas('role', function ($query): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere('context', 'kitchen');
            })
            ->get()
            ->filter(function (User $user): bool {
                return $user->canAssignKitchenOrders()
                    || $user->canManageKitchenStaff()
                    || $user->hasPermission('manage-kitchen-orders');
            })
            ->unique('id')
            ->values();
    }

    private function notifyConfiguredRecipients(RoomServiceOrder $order): void
    {
        $settings = SystemSetting::current();
        $emails = $settings->kitchenAlertEmails();
        $phones = $settings->kitchenAlertPhones();

        if ($emails === [] && $phones === []) {
            return;
        }

        $reference = $order->public_reference ?: '#'.$order->id;
        $roomName = $order->room?->name ?: __('Room');
        $guestName = $order->guest_name ?: __('Guest');
        $itemsText = $order->items()
            ->get()
            ->map(fn ($item) => $item->item_name.' x '.$item->quantity)
            ->implode(', ');

        $emailSubject = __('New kitchen order: :reference', ['reference' => $reference]);
        $emailBody = implode("\n", array_filter([
            __('A new kitchen order has been placed.'),
            __('Reference: :reference', ['reference' => $reference]),
            __('Guest: :guest', ['guest' => $guestName]),
            __('Room: :room', ['room' => $roomName]),
            $itemsText !== '' ? __('Items: :items', ['items' => $itemsText]) : null,
            __('Please open the kitchen dashboard to review and act on it.'),
        ]));

        foreach ($emails as $email) {
            try {
                Mail::raw($emailBody, function ($mail) use ($email, $emailSubject): void {
                    $mail->to($email)->subject($emailSubject);
                });
            } catch (Throwable $exception) {
                report($exception);
            }
        }

        $smsMessage = __('New kitchen order :reference for :room by :guest.', [
            'reference' => $reference,
            'room' => $roomName,
            'guest' => $guestName,
        ]);

        foreach ($phones as $phone) {
            try {
                $this->sms->send($phone, $smsMessage);
            } catch (Throwable $exception) {
                report($exception);
            }
        }
    }
}
