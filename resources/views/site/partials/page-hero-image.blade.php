@props(['fallback' => 'img/pageHero/4.png', 'heroUrl' => null, 'anim' => 'img-right cover-white delay-1'])
@php
    $src = $heroUrl ?? $siteSettings->resolvedInnerPageHero($fallback);
@endphp
{{-- img-right reveal uses ::after on this node — gradient is a sibling so it stays visible after the wipe --}}
<div class="pageHero__reveal" data-anim-child="{{ $anim }}">
    <img src="{{ $src }}" alt="" class="img-ratio" loading="lazy" decoding="async">
</div>
<div class="pageHero__image-overlay" aria-hidden="true"></div>
