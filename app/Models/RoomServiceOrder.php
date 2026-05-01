<?php

namespace App\Models;

use App\Enums\RoomServiceOrderStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class RoomServiceOrder extends Model
{
    protected $fillable = [
        'public_reference',
        'user_id',
        'booking_id',
        'room_id',
        'hotel_branch_id',
        'booking_method_id',
        'request_source',
        'guest_name',
        'guest_phone',
        'status',
        'payment_status',
        'payment_reference',
        'paid_at',
        'bill_generated_at',
        'bill_generated_by_user_id',
        'assigned_to_user_id',
        'assigned_by_user_id',
        'assigned_at',
        'completed_at',
        'last_status_updated_by_user_id',
        'estimated_ready_at',
        'preparation_minutes',
        'total_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'public_reference' => 'string',
            'estimated_ready_at' => 'datetime',
            'paid_at' => 'datetime',
            'bill_generated_at' => 'datetime',
            'assigned_at' => 'datetime',
            'completed_at' => 'datetime',
            'total_amount' => 'decimal:2',
            'preparation_minutes' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $order): void {
            if (self::supportsPaymentTracking() && blank($order->public_reference)) {
                $order->public_reference = self::nextReference();
            }

            if (self::supportsPaymentTracking() && blank($order->payment_status)) {
                $order->payment_status = 'unpaid';
            }
        });
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

    public function bookingMethod(): BelongsTo
    {
        return $this->belongsTo(BookingMethod::class, 'booking_method_id');
    }

    public function billGeneratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'bill_generated_by_user_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_user_id');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by_user_id');
    }

    public function lastStatusUpdatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_status_updated_by_user_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RoomServiceOrderItem::class);
    }

    public function isPaid(): bool
    {
        if (! self::supportsPaymentTracking()) {
            return false;
        }

        return $this->payment_status === 'paid' && $this->paid_at !== null;
    }

    public function paymentStatusLabel(): string
    {
        if (! self::supportsPaymentTracking()) {
            return __('Payment tracking unavailable');
        }

        return match ($this->payment_status) {
            'paid' => __('Paid'),
            'cash_pending' => __('Cash pending confirmation'),
            'processing' => __('Waiting for online payment'),
            'bill_later' => __('Keep bill for checkout'),
            default => __('Unpaid'),
        };
    }

    public function hasPendingBalance(): bool
    {
        if (! self::supportsPaymentTracking()) {
            return true;
        }

        return $this->payment_status !== 'paid';
    }

    public function hasGeneratedBill(): bool
    {
        return $this->bill_generated_at !== null;
    }

    public function canGenerateBill(): bool
    {
        return $this->status === RoomServiceOrderStatus::Delivered->value && $this->hasPendingBalance();
    }

    public function isAssignedTo(?User $user): bool
    {
        return $user !== null && (int) $this->assigned_to_user_id === (int) $user->id;
    }

    public function billReference(): string
    {
        return 'KBL-'.($this->public_reference ?: str_pad((string) $this->id, 6, '0', STR_PAD_LEFT));
    }

    public function paymentReturnUrl(): string
    {
        if ($this->booking) {
            return $this->booking->guestPortalUrl();
        }

        $qr = KitchenRoomQr::query()
            ->where('room_id', $this->room_id)
            ->where('is_active', true)
            ->latest('id')
            ->first();

        if ($qr) {
            return route('site.kitchen-menu.show', $qr->token);
        }

        return route('site.home');
    }

    public static function supportsPaymentTracking(): bool
    {
        static $supports = null;

        if ($supports !== null) {
            return $supports;
        }

        return $supports = Schema::hasColumns('room_service_orders', [
            'public_reference',
            'booking_method_id',
            'payment_status',
            'payment_reference',
            'paid_at',
        ]);
    }

    public static function nextReference(): string
    {
        do {
            $candidate = 'RSO-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
        } while (self::query()->where('public_reference', $candidate)->exists());

        return $candidate;
    }
}
