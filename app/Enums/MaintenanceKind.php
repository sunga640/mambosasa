<?php

namespace App\Enums;

enum MaintenanceKind: string
{
    case Repairment = 'repairment';
    case Cleaning = 'cleaning';
    case Inspection = 'inspection';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Repairment => __('Repairment'),
            self::Cleaning => __('Cleaning'),
            self::Inspection => __('Inspection'),
            self::Other => __('Other'),
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
