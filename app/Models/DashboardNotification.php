<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DashboardNotification extends Model
{
    protected $fillable = [
        'booking_id',
        'room_id',
        'kind',
        'title',
        'body',
        'meta',
        'read_at',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'read_at' => 'datetime',
            'resolved_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function markRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
