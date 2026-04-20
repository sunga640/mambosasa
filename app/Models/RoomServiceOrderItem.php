<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomServiceOrderItem extends Model
{
    protected $fillable = [
        'room_service_order_id',
        'restaurant_menu_item_id',
        'item_name',
        'quantity',
        'unit_price',
        'line_total',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'line_total' => 'decimal:2',
            'quantity' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(RoomServiceOrder::class, 'room_service_order_id');
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(RestaurantMenuItem::class, 'restaurant_menu_item_id');
    }
}
