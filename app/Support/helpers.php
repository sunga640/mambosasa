<?php

use Illuminate\Support\Facades\Route;

if (! function_exists('site_url')) {
    /**
     * Map template .html links to Laravel routes (home-8 is the site home).
     */
    function site_url(string $htmlFile): string
    {
        $slug = preg_replace('/\.html$/i', '', $htmlFile);

        return match ($slug) {
            'home-8' => route('site.home'),
            default => route('site.page', ['slug' => $slug]),
        };
    }
}
