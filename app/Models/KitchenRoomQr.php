<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KitchenRoomQr extends Model
{
    protected $fillable = [
        'room_id',
        'hotel_branch_id',
        'token',
        'is_active',
        'last_scanned_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_scanned_at' => 'datetime',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }
}
