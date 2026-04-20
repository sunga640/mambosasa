@php
    $siteName = $siteSettings->hotelDisplayName();
    $desc = trim($__env->yieldContent('meta_description', __('Book luxury rooms and manage your stay at :name.', ['name' => $siteName])));
    $canonical = url()->current();
    $ogImage = $siteSettings->headerLogoUrl();
@endphp
<meta name="description" content="{{ \Illuminate\Support\Str::limit($desc, 160) }}">
<link rel="canonical" href="{{ $canonical }}">
<meta property="og:type" content="website">
<meta property="og:title" content="@yield('title', $siteName)">
<meta property="og:description" content="{{ \Illuminate\Support\Str::limit($desc, 200) }}">
<meta property="og:url" content="{{ $canonical }}">
<meta property="og:site_name" content="{{ $siteName }}">
@if ($ogImage)
<meta property="og:image" content="{{ $ogImage }}">
@endif
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="@yield('title', $siteName)">
<meta name="twitter:description" content="{{ \Illuminate\Support\Str::limit($desc, 200) }}">

<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Hotel',
    'name' => $siteName,
    'url' => url('/'),
    'description' => \Illuminate\Support\Str::limit($desc, 300),
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
