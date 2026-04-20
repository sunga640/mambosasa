<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class RoomType extends Model
{
    protected $fillable = [
        'hotel_branch_id',
        'name',
        'slug',
        'description',
        'price',
        'max_rooms',
        'hero_image_path',
        'thumbnail_paths',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'max_rooms' => 'integer',
            'thumbnail_paths' => 'array',
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (RoomType $type): void {
            if (! $type->slug) {
                $type->slug = Str::slug($type->name).'-'.Str::lower(Str::random(4));
            }
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    public function heroImageUrl(): ?string
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

    public function thumbnailUrls(): array
    {
        $paths = array_values(array_filter((array) ($this->thumbnail_paths ?? [])));
        $urls = [];
        foreach ($paths as $path) {
            if (PublicDisk::exists($path)) {
                $urls[] = PublicDisk::url($path);
            } elseif (is_file(public_path('storage/'.$path))) {
                $urls[] = asset('storage/'.$path);
            }
        }

        return $urls;
    }
}
