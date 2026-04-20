<?php

namespace App\Enums;

enum RoomServiceOrderStatus: string
{
    case Pending = 'pending';
    case Preparing = 'preparing';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Pending => __('Pending'),
            self::Preparing => __('Preparing'),
            self::Delivered => __('Delivered'),
            self::Cancelled => __('Cancelled'),
        };
    }
}
