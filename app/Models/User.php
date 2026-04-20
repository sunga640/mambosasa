<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role?->slug === Role::SUPER_ADMIN_SLUG;
    }

    public function isReceptionStaff(): bool
    {
        return in_array($this->role?->slug, [Role::RECEPTION_SLUG, Role::DIRECTOR_SLUG], true);
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
            return null;
        }

        return self::query()->where('email', $email)->value('id');
    }

    public function accountHomeUrl(): string
    {
         // Kama ni Super Admin, Manager, au Director - wote waende Admin Dashboard
    if ($this->isSuperAdmin() || $this->isManager() || $this->isDirector()) {
        return route('admin.dashboard');
    }
        if ($this->role?->slug === Role::RECEPTION_SLUG) {
            return route('reception.dashboard');
        }
        if ($this->isDirector()) {
            return route('admin.dashboard');
        }

        return route('dashboard');
    }

    public function hasPermission(string $slug): bool
    {
        return $this->role?->hasPermission($slug) ?? false;
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
