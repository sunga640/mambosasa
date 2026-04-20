<?php

namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

/**
 * Saves uploads with PHP copy() directly under storage/app/public.
 * Avoids Laravel Storage / Flysystem / League MIME detection (requires ext-fileinfo).
 */
final class UploadStorage
{
    public static function storePublic(UploadedFile $file, string $directory): string
    {
        $ext = strtolower($file->getClientOriginalExtension() ?: '');
        if ($ext === '') {
            $ext = 'bin';
        }
        $name = Str::random(40).'.'.$ext;
        $relativePath = trim(str_replace('\\', '/', $directory), '/').'/'.$name;

        $root = storage_path('app/public');
        $fullPath = $root.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relativePath);
        $dir = dirname($fullPath);
        if (! is_dir($dir)) {
            if (! @mkdir($dir, 0755, true) && ! is_dir($dir)) {
                throw new \RuntimeException('Could not create storage directory.');
            }
        }

        $tmp = $file->getRealPath();
        if ($tmp === false || ! is_readable($tmp)) {
            throw new \RuntimeException('Upload file is not readable.');
        }

        if (! @copy($tmp, $fullPath)) {
            throw new \RuntimeException('Failed to save upload.');
        }

        return $relativePath;
    }
}
