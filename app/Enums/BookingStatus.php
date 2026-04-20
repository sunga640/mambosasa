<?php

namespace App\Enums;

enum BookingStatus: string
{
    case PendingPayment = 'pending_payment';
    case Expired = 'expired';
    case Confirmed = 'confirmed';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::PendingPayment => __('Waiting for payment'),
            self::Expired => __('Expired'),
            self::Confirmed => __('Confirmed'),
            self::Cancelled => __('Cancelled'),
        };
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
