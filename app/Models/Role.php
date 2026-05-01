<?php

namespace App\Models;

use App\Support\PermissionCatalog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN_SLUG = 'super-admin';

    public const GUEST_SLUG = 'guest';

    public const RECEPTION_SLUG = 'reception';

    public const DIRECTOR_SLUG = 'director';

    public const MANAGER_SLUG = 'manager';

    public const KITCHEN_SLUG = 'kitchen';

    protected $fillable = [
        'name',
        'slug',
        'is_system',
        'context',
        'created_by_user_id',
        'hotel_branch_id',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function hotelBranch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function isKitchenRole(): bool
    {
        return $this->slug === self::KITCHEN_SLUG || $this->context === 'kitchen';
    }

    public function hasPermission(string $slug): bool
    {
        if ($this->slug === self::SUPER_ADMIN_SLUG) {
            return true;
        }

        $normalized = PermissionCatalog::normalizeSlug($slug);

        return $this->permissions()->where('slug', $normalized)->exists();
    }
}
