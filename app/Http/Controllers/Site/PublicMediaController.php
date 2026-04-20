<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Support\PublicDisk;
use Illuminate\Http\Response;

class PublicMediaController extends Controller
{
    public function show(string $path): Response
    {
        $full = PublicDisk::fullPath($path);
        abort_unless($full && is_file($full), 404);

        $mime = $this->mimeFromExtension((string) pathinfo($full, PATHINFO_EXTENSION));

        return response(file_get_contents($full) ?: '', 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }

    private function mimeFromExtension(string $ext): string
    {
        return match (strtolower($ext)) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'svg' => 'image/svg+xml',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'mov' => 'video/quicktime',
            default => 'application/octet-stream',
        };
    }
}

