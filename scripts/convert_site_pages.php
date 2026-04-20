<?php

/**
 * Convert template *.html under resources/views into resources/views/site/*.blade.php
 * home-8.html -> site/home.blade.php (main site home). Other files use basename without .html.
 */
$root = dirname(__DIR__);
$views = $root.'/resources/views';
$outDir = $views.'/site';

if (! is_dir($outDir)) {
    mkdir($outDir, 0777, true);
}

function transformContent(string $body): string
{
    $body = preg_replace_callback(
        '/href="([^"]+\.html)"/',
        static fn (array $m) => 'href="{{ site_url(\''.$m[1].'\') }}"',
        $body
    );

    $body = preg_replace('/src="img\/([^"]+)"/', 'src="{{ asset(\'img/$1\') }}"', $body);

    return $body;
}

function extractMainBody(string $html): ?string
{
    if (preg_match('/<main[^>]*>\s*[\s\S]+?<\/header>\s*([\s\S]+?)<footer class="footer/s', $html, $m)) {
        return trim($m[1]);
    }

    if (preg_match('/<main[^>]*>\s*[\s\S]+?<\/header>\s*([\s\S]+?)<\/main>/s', $html, $m)) {
        return trim($m[1]);
    }

    if (preg_match('/<main[^>]*>\s*([\s\S]+?)<\/main>/s', $html, $m)) {
        return trim($m[1]);
    }

    return null;
}

function extractTitle(string $html): string
{
    if (preg_match('/<title>([^<]+)<\/title>/', $html, $m)) {
        return trim(html_entity_decode($m[1], ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    }

    return 'Hotel';
}

$files = glob($views.'/*.html') ?: [];

foreach ($files as $path) {
    $base = basename($path, '.html');
    $html = file_get_contents($path);
    $body = extractMainBody($html);

    if ($body === null) {
        fwrite(STDERR, "skip (no main/header/footer pattern): $path\n");
        continue;
    }

    $title = extractTitle($html);
    $blade = transformContent($body);

    $targetName = $base === 'home-8' ? 'home' : $base;
    $target = $outDir.'/'.$targetName.'.blade.php';

    $wrapped = "@extends('layouts.site')\n\n@section('title')\n{$title}\n@endsection\n\n@section('content')\n{$blade}\n@endsection\n";

    file_put_contents($target, $wrapped);
    echo "Wrote $target\n";
}
