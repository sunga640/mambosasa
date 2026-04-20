@php
    $s = match (true) {
        isset($setting) && $setting instanceof \App\Models\SystemSetting => $setting,
        ($siteSettings ?? null) instanceof \App\Models\SystemSetting => $siteSettings,
        ($dashboardSettings ?? null) instanceof \App\Models\SystemSetting => $dashboardSettings,
        default => \App\Models\SystemSetting::current(),
    };

    $iconUrl = $s->headerLogoUrl();
    $iconType = $s->faviconMimeType();
    $base64Image = '';

    try {
        // Tunapata file path kutoka kwenye URL
        $pathPart = last(explode('/storage/', $iconUrl));
        $disk = \Storage::disk('public');

        if ($disk->exists($pathPart)) {
            $imageData = $disk->get($pathPart);
            $base64Image = 'data:' . $iconType . ';base64,' . base64_encode($imageData);
        }
    } catch (\Exception $e) {}

    if ($base64Image) {
        // THAMANI YA KUREKEBISHA:
        // rx='15' ndio inayoweka border radius.
        // Ukitaka iwe "round" zaidi, ongeza namba (mfano 20).
        // Ukitaka iwe "square" zaidi, punguza namba (mfano 8).
        $borderRadius = "15";

        $roundedSvg = "<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 64 64'>"
            ."<defs>"
                ."<clipPath id='roundCorner'>"
                    ."<rect x='0' y='0' width='64' height='64' rx='{$borderRadius}' ry='{$borderRadius}'/>"
                ."</clipPath>"
            ."</defs>"
            ."<rect width='64' height='64' rx='{$borderRadius}' ry='{$borderRadius}' fill='#122223'/>"
            ."<image href='{$base64Image}' x='0' y='0' width='64' height='64' preserveAspectRatio='xMidYMid slice' clip-path='url(#roundCorner)'/>"
            ."</svg>";

        $faviconHref = 'data:image/svg+xml;base64,'.base64_encode($roundedSvg);
    } else {
        $faviconHref = $iconUrl;
    }
@endphp

<link rel="icon" href="{{ $faviconHref }}" type="image/svg+xml">
<link rel="shortcut icon" href="{{ $iconUrl }}" type="{{ $iconType }}">
<link rel="apple-touch-icon" sizes="180x180" href="{{ $iconUrl }}">
<meta name="theme-color" content="#122223">
