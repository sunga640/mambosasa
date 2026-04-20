<?php

namespace App\Enums;

enum RoomStatus: string
{
    case Available = 'available';
    case Booked = 'booked';
    case UnderMaintenance = 'under_maintenance';

    public function label(): string
    {
        return match ($this) {
            self::Available => __('Available'),
            self::Booked => __('Booked'),
            self::UnderMaintenance => __('Under maintenance'),
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
