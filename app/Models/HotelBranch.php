<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelBranch extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'location_address',
        'city',
        'country',
        'is_ground_floor_only',
        'floors_count',
        'contact_phone',
        'contact_email',
        'contact_whatsapp',
        'extra_notes',
        'is_active',
        'logo_path',
        'preview_images',
    ];

    protected function casts(): array
    {
        return [
            'is_ground_floor_only' => 'boolean',
            'is_active' => 'boolean',
            'floors_count' => 'integer',
            'preview_images' => 'array',
        ];
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function maxFloorIndex(): int
    {
        if ($this->is_ground_floor_only) {
            return 0;
        }

        return max(0, (int) $this->floors_count - 1);
    }
}
