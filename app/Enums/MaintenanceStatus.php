<?php

namespace App\Enums;

enum MaintenanceStatus: string
{
    case Active = 'active';
    case Completed = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::Active => __('Active'),
            self::Completed => __('Completed'),
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
