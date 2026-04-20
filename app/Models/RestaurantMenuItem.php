<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RestaurantMenuItem extends Model
{
    protected $fillable = [
        'name',
        'description',
        'price',
        'preparation_minutes',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'is_active' => 'boolean',
            'preparation_minutes' => 'integer',
            'sort_order' => 'integer',
        ];
    }
}
