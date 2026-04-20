<?php

namespace App\Models;

use App\Enums\BookingStatus;
use App\Enums\MaintenanceStatus;
use App\Enums\RoomStatus;
use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    protected $fillable = [
        'hotel_branch_id',
        'room_type_id',
        'room_rank_id',
        'room_number',
        'floor_number',
        'name',
        'slug',
        'status',
        'price',
        'description',
        'video_path',
        'hero_image_path',
        'card_primary',
        'force_in_use',
    ];

    protected function casts(): array
    {
        return [
            'floor_number' => 'integer',
            'price' => 'decimal:2',
            'status' => RoomStatus::class,
            'force_in_use' => 'boolean',
        ];
    }
    // Ndani ya App\Models\Room.php

public function getUnavailableDates()
{
    $dates = [];

    // 1. Chukua tarehe za Booking zilizothibitishwa (Confirmed/Paid)
    $bookings = $this->bookings()
        ->whereIn('status', [\App\Enums\BookingStatus::Confirmed, \App\Enums\BookingStatus::PendingPayment])
        ->get(['check_in', 'check_out']);

    foreach ($bookings as $b) {
        $dates[] = [
            'from' => $b->check_in->format('Y-m-d'),
            'to' => $b->check_out->subDay()->format('Y-m-d') // subDay kwa sababu check-out day chumba kinaweza kuwa wazi mchana
        ];
    }

    // 2. Chukua tarehe za Maintenance ambazo hazijakamilika
    $maintenances = $this->maintenances()
        ->where('status', '!=', \App\Enums\MaintenanceStatus::Completed)
        ->get(['started_at', 'due_at']);

    foreach ($maintenances as $m) {
        $dates[] = [
            'from' => $m->started_at->format('Y-m-d'),
            'to' => $m->due_at->format('Y-m-d')
        ];
    }

    return $dates;
}

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function rank(): BelongsTo
    {
        return $this->belongsTo(RoomRank::class, 'room_rank_id');
    }

    public function type(): BelongsTo
    {
        return $this->belongsTo(RoomType::class, 'room_type_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(RoomImage::class)->orderBy('sort_order');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(RoomMaintenance::class);
    }

    public function activeMaintenances(): HasMany
    {
        return $this->maintenances()->where('status', MaintenanceStatus::Active);
    }

    /**
     * Room is shown as “in use”: forced flag, active maintenance, active reservation, or stay in progress.
     */
    public function isEffectivelyInUse(): bool
    {
        if ($this->force_in_use) {
            return true;
        }

        if ($this->activeMaintenances()->exists()) {
            return true;
        }

        $today = now()->toDateString();

        $confirmedStay = $this->bookings()
            ->where('status', BookingStatus::Confirmed)
            ->where(function (Builder $q) use ($today): void {
                $q->where(function (Builder $q2) use ($today): void {
                    $q2->whereNotNull('check_in')
                        ->whereNotNull('check_out')
                        ->whereDate('check_in', '<=', $today)
                        ->whereDate('check_out', '>=', $today);
                })->orWhere(function (Builder $q2): void {
                    $q2->whereNull('check_in')->whereNull('check_out');
                });
            })
            ->exists();

        if ($confirmedStay) {
            return true;
        }

        return $this->bookings()
            ->where('status', BookingStatus::PendingPayment)
            ->where('payment_deadline_at', '>', now())
            ->exists();
    }

    /**
     * Rooms that may appear on the public site (home, pricing, search, cart): not booked,
     * not forced in use, no active maintenance, no active reservation / stay window.
     */
    public function scopeListedOnPublicSite(Builder $query): void
    {
        $today = now()->toDateString();
        $query->where('status', RoomStatus::Available)
            ->where(function (Builder $q): void {
                $q->whereNull('force_in_use')->orWhere('force_in_use', false);
            })
            ->whereDoesntHave('maintenances', function (Builder $q): void {
                $q->where('status', MaintenanceStatus::Active);
            })
            ->whereDoesntHave('bookings', function (Builder $q): void {
                $q->where('status', BookingStatus::PendingPayment)
                    ->where('payment_deadline_at', '>', now());
            })
            ->whereDoesntHave('bookings', function (Builder $q) use ($today): void {
                $q->where('status', BookingStatus::Confirmed)
                    ->where(function (Builder $q2) use ($today): void {
                        $q2->where(function (Builder $q3) use ($today): void {
                            $q3->whereNotNull('check_in')->whereNotNull('check_out')
                                ->whereDate('check_in', '<=', $today)
                                ->whereDate('check_out', '>=', $today);
                        })->orWhere(function (Builder $q3): void {
                            $q3->whereNull('check_in')->whereNull('check_out');
                        });
                    });
            });
    }

    public function scopeAvailableForBooking(Builder $query): void
    {
        $query->listedOnPublicSite();
    }

    public function heroImagePublicUrl(): ?string
    {
        if (! $this->hero_image_path) {
            return null;
        }
        if (PublicDisk::exists($this->hero_image_path)) {
            return PublicDisk::url($this->hero_image_path);
        }
        if (is_file(public_path('storage/'.$this->hero_image_path))) {
            return asset('storage/'.$this->hero_image_path);
        }

        return null;
    }

    public function videoPublicUrl(): ?string
    {
        if (! $this->video_path) {
            return null;
        }
        if (PublicDisk::exists($this->video_path)) {
            return PublicDisk::url($this->video_path);
        }
        if (is_file(public_path('storage/'.$this->video_path))) {
            return asset('storage/'.$this->video_path);
        }

        return null;
    }

    /**
     * Image URL for cards when primary is not video, or as video poster fallback.
     */
    public function cardImageUrl(): string
    {
        $hero = $this->heroImagePublicUrl();
        if ($hero) {
            return $hero;
        }

        return $this->images->first()?->url() ?? asset('img/cards/rooms/3/1.png');
    }

    public function usesVideoOnCard(): bool
    {
        return $this->card_primary === 'video' && $this->video_path !== null;
    }
}
