<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookingMethod extends Model
{
    public const VIS_PUBLIC = 'public';

    public const VIS_INTERNAL = 'internal';

    protected $fillable = [
        'name',
        'code',
        'slug',
        'method_type',
        'is_active',
        'sort_order',
        'visibility',
        'show_on_booking_page',
        'account_number',
        'account_holder',
        'instructions',
        'gateway_public_key',
        'gateway_secret_key',
        'gateway_base_url',
        'gateway_ipn_id',

    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'show_on_booking_page' => 'boolean',
        ];
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Builder<static>  $query
     * @return \Illuminate\Database\Eloquent\Builder<static>
     */
    public function scopeVisibleOnPublicSite(\Illuminate\Database\Eloquent\Builder $query): \Illuminate\Database\Eloquent\Builder
    {
        return $query
            ->where('visibility', self::VIS_PUBLIC)
            ->where('show_on_booking_page', true);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
