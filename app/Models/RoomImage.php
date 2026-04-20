<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RoomImage extends Model
{
    protected $fillable = [
        'room_id',
        'path',
        'caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function url(): string
    {
        if ($this->path && PublicDisk::exists($this->path)) {
            return PublicDisk::url($this->path);
        }

        if ($this->path && is_file(public_path('storage/'.$this->path))) {
            return asset('storage/'.$this->path);
        }

        return asset('img/cards/rooms/3/1.png');
    }
}
