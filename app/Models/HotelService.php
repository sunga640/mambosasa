<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HotelService extends Model
{
    protected $fillable = [
        'hotel_branch_id',
        'name',
        'slug',
        'description',
        'price',
        'category',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'sort_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (HotelService $m): void {
            if ($m->slug === '' || $m->slug === null) {
                $m->slug = Str::slug($m->name).'-'.Str::random(4);
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function requests(): HasMany
    {
        return $this->hasMany(BookingHotelServiceRequest::class);
    }

    public function imageUrl(): ?string
    {
        if (! $this->image_path) {
            return null;
        }
        if (PublicDisk::exists($this->image_path)) {
            return PublicDisk::url($this->image_path);
        }
        if (is_file(public_path('storage/'.$this->image_path))) {
            return asset('storage/'.$this->image_path);
        }

        return null;
    }

    /**
     * Active services; when $branchId is set, include global (null) or that branch.
     */
    public function scopeListedForGuests(Builder $query, ?int $branchId = null): void
    {
        $query->where('is_active', true);
        if ($branchId !== null) {
            $query->where(function (Builder $q) use ($branchId): void {
                $q->whereNull('hotel_branch_id')->orWhere('hotel_branch_id', $branchId);
            });
        }
    }
}
