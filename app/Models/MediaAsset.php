<?php

namespace App\Models;

use App\Support\UploadStorage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;

class MediaAsset extends Model
{
    protected $fillable = [
        'path',
        'original_name',
        'mime_type',
        'size',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
        ];
    }

    public static function createFromUpload(UploadedFile $file): self
    {
        $path = UploadStorage::storePublic($file, 'media-library');

        return self::query()->create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize() ?: 0,
        ]);
    }
}
