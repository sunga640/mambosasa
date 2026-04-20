<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'user_id',
        'is_active',
        'notes',
        'last_booking_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_booking_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'email', 'email');
    }

    public static function syncFromBooking(Booking $booking): self
    {
        $existing = self::query()->where('email', $booking->email)->first();
        if ($existing && ! $existing->is_active) {
            return $existing;
        }

        return self::query()->updateOrCreate(
            ['email' => $booking->email],
            [
                'first_name' => $booking->first_name,
                'last_name' => $booking->last_name,
                'phone' => $booking->phone,
                'user_id' => $booking->user_id,
                'last_booking_at' => now(),
            ]
        );
    }
}
