<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    protected $fillable = [
        'public_reference',
        'guest_access_token',
        'guest_access_token_expires_at',
        'user_id',
        'room_id',
        'booking_method_id',
        'status',
        'confirmed_at',
        'checkout_notified_at',
        'payment_deadline_at',
        'check_in',
        'check_out',
        'first_name',
        'last_name',
        'email',
        'phone',
        'adults',
        'children',
        'rooms_count',
        'nights',
        'total_amount',
        'terms_accepted',
        'special_requests',
    ];

    protected function casts(): array
    {
        return [
            'status' => BookingStatus::class,
            'confirmed_at' => 'datetime',
            'checkout_notified_at' => 'datetime',
            'guest_access_token_expires_at' => 'datetime',
            'payment_deadline_at' => 'datetime',
            'check_in' => 'date',
            'check_out' => 'date',
            'adults' => 'integer',
            'children' => 'integer',
            'rooms_count' => 'integer',
            'nights' => 'integer',
            'total_amount' => 'decimal:2',
            'terms_accepted' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function method(): BelongsTo
    {
        return $this->belongsTo(BookingMethod::class, 'booking_method_id');
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }

    public function dashboardNotifications(): HasMany
    {
        return $this->hasMany(DashboardNotification::class);
    }

    public function hotelServiceRequests(): HasMany
    {
        return $this->hasMany(BookingHotelServiceRequest::class);
    }

    public function secondsUntilPaymentDeadline(): int
    {
        if (! $this->payment_deadline_at || $this->status !== BookingStatus::PendingPayment) {
            return 0;
        }

        return max(0, $this->payment_deadline_at->getTimestamp() - now()->getTimestamp());
    }

    public function guestPortalUrl(): string
    {
        return route('site.guest-stay.show', ['token' => $this->guest_access_token]);
    }

    public function ensureGuestAccessToken(): void
    {
        $expires = $this->payment_deadline_at
            ? $this->payment_deadline_at->copy()->addDays(14)
            : now()->addDays(90);

        if (
            $this->guest_access_token
            && $this->guest_access_token_expires_at
            && $this->guest_access_token_expires_at->isFuture()
        ) {
            return;
        }

        $this->forceFill([
            'guest_access_token' => Str::lower(Str::random(48)),
            'guest_access_token_expires_at' => $expires,
        ])->saveQuietly();
    }

    public function extendGuestTokenAfterPaymentConfirmed(): void
    {
        $until = $this->check_out
            ? $this->check_out->copy()->addDays(3)->endOfDay()
            : now()->addDays(30);

        $this->forceFill([
            'guest_access_token_expires_at' => $until,
        ])->saveQuietly();
    }

    public static function findByValidGuestToken(string $token): ?self
    {
        if ($token === '') {
            return null;
        }

        return self::query()
            ->where('guest_access_token', $token)
            ->where(function ($q): void {
                $q->whereNull('guest_access_token_expires_at')
                    ->orWhere('guest_access_token_expires_at', '>', now());
            })
            ->first();
    }
}
