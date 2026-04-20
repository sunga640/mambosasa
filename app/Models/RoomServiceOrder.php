<?php

namespace App\Models;

use App\Enums\RoomServiceOrderStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RoomServiceOrder extends Model
{
    protected $fillable = [
        'user_id',
        'booking_id',
        'room_id',
        'hotel_branch_id',
        'status',
        'estimated_ready_at',
        'preparation_minutes',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'estimated_ready_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'preparation_minutes' => 'integer',
        ];
    }

    public function statusEnum(): RoomServiceOrderStatus
    {
        return RoomServiceOrderStatus::from((string) $this->status);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RoomServiceOrderItem::class);
    }
}
