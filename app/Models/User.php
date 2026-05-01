<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Support\PermissionCatalog;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uses_system_password',
        'system_password_plain',
        'role_id',
        'hotel_branch_id',
        'created_by_user_id',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
            'uses_system_password' => 'boolean',
            'system_password_plain' => 'encrypted',
        ];
    }

    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    public function hotelBranch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(self::class, 'created_by_user_id');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function roomServiceOrders(): HasMany
    {
        return $this->hasMany(RoomServiceOrder::class);
    }

    public function createdUsers(): HasMany
    {
        return $this->hasMany(self::class, 'created_by_user_id');
    }

    public function assignedRoomServiceOrders(): HasMany
    {
        return $this->hasMany(RoomServiceOrder::class, 'assigned_to_user_id');
    }

    public function kitchenRoles(): HasMany
    {
        return $this->hasMany(Role::class, 'created_by_user_id');
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === Role::SUPER_ADMIN_SLUG;
    }

    public function isReceptionStaff(): bool
    {
        return in_array($this->role?->slug, [Role::RECEPTION_SLUG, Role::DIRECTOR_SLUG], true);
    }

    public function isKitchenStaff(): bool
    {
        return in_array($this->role?->slug, [Role::KITCHEN_SLUG, Role::DIRECTOR_SLUG, Role::MANAGER_SLUG], true);
    }

    public function hasStaffPanelAccess(): bool
    {
        if ($this->hasAdminPanelAccess()) {
            return true;
        }

        return $this->isDirector()
            || $this->role?->slug === Role::RECEPTION_SLUG
            || $this->role?->slug === Role::KITCHEN_SLUG
            || $this->hasAnyPermission(PermissionCatalog::receptionPanelPermissionSlugs());
    }

    public function isDirector(): bool
    {
        return $this->role?->slug === Role::DIRECTOR_SLUG;
    }

    public function isGuest(): bool
    {
        return $this->role?->slug === Role::GUEST_SLUG;
    }
    public function isManager(): bool
{
    return $this->role?->slug === Role::MANAGER_SLUG;
}

    /**
     * Primary dashboard URL for this user (used for "Home" while logged in).
     */
    public static function guestPortalUserId(): ?int
    {
        $email = config('hotel.guest_portal_user_email');
        if (! is_string($email) || $email === '') {
            return self::query()
                ->whereHas('role', fn ($query) => $query->where('slug', Role::GUEST_SLUG))
                ->orderBy('id')
                ->value('id');
        }

        $existingId = self::query()->where('email', $email)->value('id');
        if ($existingId) {
            return $existingId;
        }

        $guestRole = Role::query()->where('slug', Role::GUEST_SLUG)->first();
        if (! $guestRole) {
            return null;
        }

        return self::query()->firstOrCreate(
            ['email' => $email],
            [
                'name' => 'Guest portal (system)',
                'password' => Hash::make(Str::random(48)),
                'role_id' => $guestRole->id,
                'is_active' => false,
            ]
        )->id;
    }

    public function accountHomeUrl(): string
    {
        if ($this->role?->slug === Role::KITCHEN_SLUG) {
            return route('kitchen.dashboard');
        }
        if ($this->role?->slug === Role::RECEPTION_SLUG) {
            return route('reception.dashboard');
        }
        if ($this->hasAdminPanelAccess()) {
            return route('admin.dashboard');
        }
        if ($this->isKitchenStaff() || $this->hasAnyPermission(PermissionCatalog::kitchenPanelPermissionSlugs())) {
            return route('kitchen.dashboard');
        }
        if ($this->hasStaffPanelAccess()) {
            return route('reception.dashboard');
        }

        return route('dashboard');
    }

    public function hasPermission(string $slug): bool
    {
        $normalized = PermissionCatalog::normalizeSlug($slug);

        if ($this->role?->hasPermission($normalized)) {
            return true;
        }

        if ($this->hasAdminPanelAccess() && (
            in_array($normalized, PermissionCatalog::receptionPanelPermissionSlugs(), true)
            || in_array($normalized, PermissionCatalog::kitchenPanelPermissionSlugs(), true)
        )) {
            return true;
        }

        return false;
    }

    public function hasAnyPermission(array $slugs): bool
    {
        foreach ($slugs as $slug) {
            if ($this->hasPermission($slug)) {
                return true;
            }
        }

        return false;
    }

    public function roleHasAnyPermission(array $slugs): bool
    {
        $role = $this->role;
        if (! $role) {
            return false;
        }

        if ($role->slug === Role::SUPER_ADMIN_SLUG) {
            return true;
        }

        $normalized = array_values(array_unique(array_map(
            fn (string $slug): string => PermissionCatalog::normalizeSlug($slug),
            $slugs
        )));

        if ($normalized === []) {
            return false;
        }

        return $role->permissions()->whereIn('slug', $normalized)->exists();
    }

    public function hasAdminPanelAccess(): bool
    {
        if ($this->role?->slug === Role::KITCHEN_SLUG) {
            return false;
        }

        if ($this->isSuperAdmin() || $this->isManager()) {
            return true;
        }

        return $this->roleHasAnyPermission(PermissionCatalog::adminPanelPermissionSlugs());
    }

    public function canManageKitchenStaff(): bool
    {
        return $this->hasPermission('manage-kitchen-staff') || $this->isSuperAdmin() || $this->isManager();
    }

    public function canManageKitchenRoles(): bool
    {
        return $this->hasPermission('manage-kitchen-roles') || $this->isSuperAdmin() || $this->isManager();
    }

    public function canAssignKitchenOrders(): bool
    {
        return $this->hasPermission('assign-kitchen-orders') || $this->canManageKitchenStaff();
    }

    public static function normalizeLoginIdentity(string $value): string
    {
        return Str::lower(trim((string) preg_replace('/\s+/u', ' ', $value)));
    }

    public static function findForLogin(string $login): ?self
    {
        $normalized = self::normalizeLoginIdentity($login);
        if ($normalized === '') {
            return null;
        }

        $user = self::query()
            ->whereRaw('LOWER(email) = ?', [$normalized])
            ->first();
        if ($user) {
            return $user;
        }

        return self::query()
            ->get()
            ->first(function (self $candidate) use ($normalized): bool {
                return self::normalizeLoginIdentity((string) $candidate->name) === $normalized;
            });
    }
}
