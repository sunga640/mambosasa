<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    public const SUPER_ADMIN_SLUG = 'super-admin';

    public const GUEST_SLUG = 'guest';

    public const RECEPTION_SLUG = 'reception';

    public const DIRECTOR_SLUG = 'director';

    // 1. Ongeza hii hapa chini
    public const MANAGER_SLUG = 'manager';

    protected $fillable = [
        'name',
        'slug',
        'is_system',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function hasPermission(string $slug): bool
    {
        // 2. Rekebisha hapa ili Manager na Super Admin wawe na nguvu sawa
        if ($this->slug === self::SUPER_ADMIN_SLUG || $this->slug === self::MANAGER_SLUG) {
            return true;
        }

        return $this->permissions()->where('slug', $slug)->exists();
    }
}
