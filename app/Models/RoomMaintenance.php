<?php

namespace App\Models;

use App\Enums\MaintenanceKind;
use App\Enums\MaintenanceStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomMaintenance extends Model
{
    protected $fillable = [
        'room_id',
        'hotel_branch_id',
        'kind',
        'description',
        'expenses',
        'started_at',
        'due_at',
        'completed_at',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expenses' => 'decimal:2',
            'started_at' => 'datetime',
            'due_at' => 'datetime',
            'completed_at' => 'datetime',
            'status' => MaintenanceStatus::class,
            'kind' => MaintenanceKind::class,
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(HotelBranch::class, 'hotel_branch_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('status', MaintenanceStatus::Active);
    }
}
