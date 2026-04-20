<?php

/**
 * One-off: build site partials from resources/views/home-8.html
 */
$root = dirname(__DIR__);
$src = $root.'/resources/views/home-8.html';
$html = file_get_contents($src);

function bladeAssetImg(string $inner): string
{
    return preg_replace('/src="img\/([^"]+)"/', 'src="{{ asset(\'img/$1\') }}"', $inner);
}

function bladeSiteUrls(string $inner): string
{
    return preg_replace_callback(
        '/href="([^"]+\.html)"/',
        static fn (array $m) => 'href="{{ site_url(\''.$m[1].'\') }}"',
        $inner
    );
}

function transform(string $chunk): string
{
    $chunk = bladeSiteUrls($chunk);
    $chunk = bladeAssetImg($chunk);

    return $chunk;
}

// menu: first menuFullScreen div through its closing (before cursor comment)
if (! preg_match('/(<div class="menuFullScreen js-menuFullScreen">[\s\S]+?<\/div>\s*)(?=<!-- cursor start -->)/', $html, $m)) {
    fwrite(STDERR, "menu block not found\n");
    exit(1);
}
$menu = trim($m[1]);

// cursor block
if (! preg_match('/<!-- cursor start -->[\s\S]+?<!-- cursor end -->/', $html, $m)) {
    fwrite(STDERR, "cursor block not found\n");
    exit(1);
}
$cursor = trim($m[0]);

// top bar + header inside main (after <main> until first <section after </header>)
if (! preg_match('/<main>\s*([\s\S]+?<\/header>\s*)/', $html, $m)) {
    fwrite(STDERR, "main/header block not found\n");
    exit(1);
}
$topHeader = trim($m[1]);

// footer
if (! preg_match('/(<footer class="footer[^"]*"[\s\S]+?<\/footer>\s*)/', $html, $m)) {
    fwrite(STDERR, "footer block not found\n");
    exit(1);
}
$footer = trim($m[0]);

$out = $root.'/resources/views/site/partials';
if (! is_dir($out)) {
    mkdir($out, 0777, true);
}

file_put_contents($out.'/menu-fullscreen.blade.php', transform($menu)."\n");
file_put_contents($out.'/cursor.blade.php', transform($cursor)."\n");
file_put_contents($out.'/top-header.blade.php', transform($topHeader)."\n");
file_put_contents($out.'/footer.blade.php', transform($footer)."\n");

echo "Wrote partials to $out\n";
