<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Invoice extends Model
{
    protected $fillable = [
        'booking_id',
        'number',
        'token',
        'total_amount',
        'currency',
        'line_items',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'line_items' => 'array',
            'issued_at' => 'datetime',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function publicUrl(): string
    {
        return route('site.invoice.show', ['token' => $this->token]);
    }

    public static function createForBooking(Booking $booking): self
    {
        $existing = self::query()->where('booking_id', $booking->id)->first();
        if ($existing) {
            return $existing;
        }

        $number = self::nextNumber();
        $token = Str::random(48);
        $lineItems = [
            [
                'description' => $booking->room?->name ?? __('Room'),
                'reference' => $booking->public_reference,
                'quantity' => $booking->rooms_count,
                'unit_price' => (float) $booking->total_amount / max(1, $booking->rooms_count),
                'total' => (float) $booking->total_amount,
            ],
        ];

        return self::query()->create([
            'booking_id' => $booking->id,
            'number' => $number,
            'token' => $token,
            'total_amount' => $booking->total_amount,
            'currency' => 'USD',
            'line_items' => $lineItems,
            'issued_at' => now(),
        ]);
    }

    public static function nextNumber(): string
    {
        $year = (int) now()->format('Y');
        $prefix = 'INV-'.$year.'-';
        $last = self::query()
            ->where('number', 'like', $prefix.'%')
            ->orderByDesc('id')
            ->value('number');
        $seq = 1;
        if ($last && preg_match('/-(\d+)$/', $last, $m)) {
            $seq = (int) $m[1] + 1;
        }

        return $prefix.str_pad((string) $seq, 5, '0', STR_PAD_LEFT);
    }
}
