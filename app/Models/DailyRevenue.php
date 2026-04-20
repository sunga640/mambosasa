<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyRevenue extends Model
{
    protected $fillable = [
        'revenue_date',
        'amount_total',
        'bookings_count',
    ];

    protected function casts(): array
    {
        return [
            'revenue_date' => 'date',
            'amount_total' => 'decimal:2',
            'bookings_count' => 'integer',
        ];
    }
}
