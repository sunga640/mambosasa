<?php

namespace App\Support;

/**
 * Public disk paths without Laravel Flysystem (avoids League MIME / finfo when ext-fileinfo is missing).
 */
final class PublicDisk
{
    public static function normalize(string $path): string
    {
        $path = trim($path);
        if ($path === '') {
            return '';
        }

        if (preg_match('#^https?://#i', $path) === 1) {
            $parsedPath = parse_url($path, PHP_URL_PATH);
            $path = is_string($parsedPath) ? $parsedPath : '';
        }

        $path = urldecode($path);
        $path = str_replace('\\', '/', $path);
        $path = ltrim($path, '/');
        $path = preg_replace('#^(storage|public|media)/#i', '', $path) ?? $path;
        if ($path === '' || str_contains($path, '..')) {
            return '';
        }

        return $path;
    }

    public static function fullPath(string $relativePath): ?string
    {
        $rel = self::normalize($relativePath);
        if ($rel === '') {
            return null;
        }

        $storagePath = storage_path('app/public/'.$rel);
        if (is_file($storagePath)) {
            return $storagePath;
        }

        $publicPath = public_path($rel);
        if (is_file($publicPath)) {
            return $publicPath;
        }

        return $storagePath;
    }

    public static function exists(string $relativePath): bool
    {
        $full = self::fullPath($relativePath);

        return $full !== null && is_file($full);
    }

    public static function url(string $relativePath): string
    {
        $rel = self::normalize($relativePath);
        if ($rel === '') {
            return '';
        }

        return route('site.media.show', ['path' => $rel]);
    }

    public static function delete(?string $relativePath): void
    {
        if ($relativePath === null || $relativePath === '') {
            return;
        }
        $full = self::fullPath($relativePath);
        if ($full !== null && is_file($full)) {
            @unlink($full);
        }
    }
}
