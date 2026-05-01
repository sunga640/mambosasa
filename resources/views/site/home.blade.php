@extends('layouts.site')

@section('meta_description')
{{ __('Luxury hotel rooms, online booking, and guest services at :name.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}
@endsection

@section('content')
@php
    $heroSlides = array_slice($homeHeroSlideUrls ?? [], 0, 3);
    $heroSlides = $heroSlides !== [] ? $heroSlides : [asset('img/hero/1/1.png')];
    $heroTitle = strtoupper($siteSettings->hotelDisplayName());
    $heroSubtitle = __('- YOUR PREMIUM ACCOMMODATION CHOICE IN DAR ES SALAAM -');
@endphp

<section class="site-home-hero">
    <div class="hero__slider js-section-slider"
         data-gap="0"
         data-slider-cols="xl-1 lg-1 md-1 sm-1 base-1"
         data-nav-prev="js-sliderHero-prev"
         data-nav-next="js-sliderHero-next"
         data-loop
         style="height: clamp(860px, 100vh, 1120px); width: 100%; position: relative;">
        <div class="swiper-wrapper" style="height: 100%;">
            @foreach ($heroSlides as $slideSrc)
                <div class="swiper-slide" style="height: 100%;">
                    <div class="site-home-hero__slide">
                        <img
                            src="{{ $slideSrc }}"
                            alt="{{ $siteSettings->hotelDisplayName() }}"
                            sizes="100vw"
                            referrerpolicy="no-referrer"
                            @if($loop->first)
                                fetchpriority="high"
                                loading="eager"
                                decoding="sync"
                            @else
                                loading="lazy"
                                decoding="async"
                            @endif
                        >
                        <div class="site-home-hero__overlay"></div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="site-home-hero__content">
            <div class="container">
                <div class="row justify-center text-center">
                    <div class="col-xl-12 col-lg-12">
                        <span class="site-kicker">{{ __('Guest reservations') }}</span>
                        @php
                            $heroTitleParts = preg_split('/\s+/', trim($heroTitle), 2) ?: [$heroTitle, ''];
                        @endphp
                        <h1 class="site-home-hero__title">
                            <span class="site-home-hero__title-line site-home-hero__title-line--primary">{{ $heroTitleParts[0] ?? $heroTitle }}</span>
                            <span class="site-home-hero__title-line site-home-hero__title-line--secondary">{{ $heroTitleParts[1] ?? '' }}</span>
                        </h1>
                        <p class="site-home-hero__text">{{ $heroSubtitle }}</p>
                        <div class="site-home-hero__actions">
                            <a href="{{ route('site.page', ['slug' => 'pricing']) }}" class="site-home-hero__primary">{{ __('Explore Rooms') }}</a>
                            <a href="{{ route('site.page', ['slug' => 'about']) }}" class="site-home-hero__secondary">{{ __('Our Story') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    var heroSlider = document.querySelector('.site-home-hero .js-section-slider');
    if (!heroSlider) return;

    var started = false;

    var startAutoSlide = function () {
        if (started || !heroSlider.swiper) return false;
        started = true;
        setInterval(function () {
            if (heroSlider.swiper && !document.hidden) {
                heroSlider.swiper.slideNext();
            }
        }, 5000);
        return true;
    };

    if (!startAutoSlide()) {
        var retries = 0;
        var timer = setInterval(function () {
            retries += 1;
            if (startAutoSlide() || retries > 20) {
                clearInterval(timer);
            }
        }, 350);
    }
});
</script>
@endpush

<section class="site-home-intro">
    <div class="container">
        <div class="row justify-between items-end y-gap-24">
            <div class="col-xl-8 col-lg-9">
                <span class="site-kicker">{{ $siteSettings->company_name ?? config('app.name') }}</span>
                <h2 class="site-home-intro__title">
                    {{ $siteSettings->home_section1_heading ?: __('Elegant comfort by the coast') }}
                </h2>
                <p class="site-home-intro__text">
                    @if ($siteSettings->home_section1_body)
                        {!! nl2br(e(\Illuminate\Support\Str::limit($siteSettings->home_section1_body, 420))) !!}
                    @else
                        {{ __(':name offers refined rooms, warm hospitality, and a smooth stay from arrival to departure.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}
                    @endif
                </p>
            </div>
            <div class="col-xl-3 col-lg-3">
                <a href="{{ route('site.page', ['slug' => 'about']) }}" class="site-home-intro__cta">{{ __('Discover') }}</a>
            </div>
        </div>

        <div class="site-home-stats">
            <article>
                <strong>{{ $stats['customers_display'] }}</strong>
                <span>{{ $stats['caption_guests'] ?? __('Happy Guests') }}</span>
            </article>
            <article>
                <strong>{{ $stats['rooms_count'] }}</strong>
                <span>{{ $stats['caption_rooms'] ?? __('Luxury Rooms') }}</span>
            </article>
            <article>
                <strong>{{ $stats['pools_count'] }}</strong>
                <span>{{ $stats['caption_pools'] ?? __('Wellness') }}</span>
            </article>
            <article>
                <strong>{{ $stats['restaurants_count'] }}</strong>
                <span>{{ $stats['caption_dining'] ?? __('Dining') }}</span>
            </article>
        </div>
    </div>
</section>

@include('site.partials.hotel-views-gallery')

@if(($homeBranches ?? collect())->isNotEmpty())
<section class="site-home-branches">
    <div class="container">
        <div class="site-home-split site-home-split--branch-reverse">
            <div class="site-home-split__intro">
                <span class="site-kicker">{{ __('Our Branches') }}</span>
                <h2>{{ __('Stay with us wherever your journey takes you.') }}</h2>
                <p>{{ __('Each Mambosasa location is prepared to offer attentive hospitality, comfortable rooms, and easy access to the neighborhoods our guests visit most.') }}</p>
                <a href="{{ route('site.branches') }}" class="site-home-split__button">{{ __('View all branches') }}</a>
            </div>

            <div class="site-home-split__cards">
                @foreach($homeBranches->take(3) as $branch)
                    @php
                        $branchImage = collect($branch->preview_images ?? [])->filter()->first() ?: $branch->logo_path;
                        $branchLocation = collect([$branch->location_address, $branch->city, $branch->country])->filter()->implode(', ');
                        $branchImageUrl = $branchImage
                            ? (str_starts_with((string) $branchImage, 'http')
                                ? $branchImage
                                : \App\Support\PublicDisk::url((string) $branchImage))
                            : asset('img/pageHero/4.png');
                    @endphp
                    <article class="site-branch-card">
                        <div class="site-branch-card__media" style="background-image:url('{{ $branchImageUrl }}');"></div>
                        <div class="site-branch-card__body">
                            <span class="site-branch-card__tag">{{ $branch->city ?: __('Branch') }}</span>
                            <h3>{{ $branch->name }}</h3>
                            <p>{{ \Illuminate\Support\Str::limit($branch->extra_notes ?: ($branchLocation ?: __('Active hotel branch in our portfolio.')), 120) }}</p>
                            <div class="site-branch-card__meta">
                                <span>{{ trans_choice(':count room|:count rooms', $branch->rooms_count, ['count' => $branch->rooms_count]) }}</span>
                                @if ($branch->contact_phone)
                                    <span>{{ $branch->contact_phone }}</span>
                                @endif
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </div>
</section>
@endif

<section id="rooms" class="site-home-rooms">
    <div class="container">
        <div class="site-home-split">
            <div class="site-home-split__intro">
                <span class="site-kicker">{{ __('Room Types') }}</span>
                <h2>{{ __('Rooms designed for rest, work, and longer stays') }}</h2>
                <p>{{ __('Choose from carefully prepared room categories with inviting interiors, practical amenities, and the calm atmosphere guests expect after a full day in the city.') }}</p>
            </div>

            <div class="site-home-split__cards">
                <div class="js-section-slider"
                     data-gap="18"
                     data-slider-cols="xl-2 lg-2 md-1 sm-1 base-1"
                     data-nav-prev="js-rooms-prev"
                     data-nav-next="js-rooms-next"
                     data-loop>
                    <div class="swiper-wrapper">
                        @forelse ($homeRoomTypes as $roomType)
                            @php
                                $sampleRoom = $roomType->rooms->first();
                                $bgImage = $roomType->heroImageUrl() ?? $sampleRoom?->cardImageUrl() ?? asset('img/cards/rooms/3/1.png');
                            @endphp
                            <div class="swiper-slide">
                                <article class="site-hover-tile">
                                    <div class="site-hover-tile__media" style="background-image:url('{{ $bgImage }}');"></div>
                                    <div class="site-hover-tile__pointer-glow"></div>
                                    <div class="site-hover-tile__overlay">
                                        <span class="site-hover-tile__tag">{{ $roomType->branch?->name ?? __('Room type') }}</span>
                                        <h3>{{ $roomType->name }}</h3>
                                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($roomType->description ?? __('Elegant comfort with a restful atmosphere.')), 95) }}</p>
                                        <div class="site-hover-tile__footer">
                                            <strong>{{ number_format((int) round((float) $roomType->price), 0) }} TZS</strong>
                                            <a href="{{ route('site.booking', ['type' => $roomType->id]) }}">{{ __('Book now') }}</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="site-hover-tile site-hover-tile--empty">{{ __('No rooms available.') }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="site-slider-arrows">
                    <button type="button" class="js-rooms-prev"><i class="icon-arrow-left text-18"></i></button>
                    <button type="button" class="js-rooms-next"><i class="icon-arrow-right text-18"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>

@if(($homeHotelServices ?? collect())->isNotEmpty())
@include('site.partials.services-zigzag', [
    'services' => $homeHotelServices->take(5),
    'sectionClass' => 'site-home-services',
    'title' => __('Guest services that feel elevated'),
    'description' => __('From dining and in-room comfort to thoughtful stay support, our guest services are arranged to make every visit feel smooth, personal, and well cared for.'),
])
@endif

<style>
    .site-home-hero {
        position: relative;
        background: #102523;
        padding-bottom: 0;
        margin-top: 0 !important;
    }
    .site-home-hero::before {
        content: "";
        position: absolute;
        inset: 0;
        z-index: 2;
        pointer-events: none;
        background:
            linear-gradient(180deg, rgba(9, 21, 24, 0.24) 0%, rgba(9, 21, 24, 0.05) 16%, rgba(9, 21, 24, 0.02) 36%, rgba(9, 21, 24, 0.18) 100%),
            radial-gradient(circle at center, rgba(255, 213, 51, 0.04) 0%, rgba(255, 213, 51, 0) 58%);
    }
    .site-home-hero__slide {
        position: relative;
        height: 100%;
        background: #102523;
        overflow: hidden;
    }
    .site-home-hero__slide img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        display: block;
        object-fit: cover;
        object-position: center;
        image-rendering: auto;
        filter: contrast(1.08) saturate(1.04);
    }
    .site-home-hero .swiper-wrapper,
    .site-home-hero .swiper-slide {
        will-change: transform;
    }
    .site-home-hero__overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(115deg, rgba(10, 24, 30, 0.16) 0%, rgba(10, 24, 30, 0.06) 45%, rgba(10, 24, 30, 0.22) 100%),
            linear-gradient(180deg, rgba(12, 31, 36, 0.1) 0%, rgba(12, 31, 36, 0.2) 36%, rgba(12, 31, 36, 0.84) 100%);
        opacity: 0.98;
        transition: background 0.35s ease, opacity 0.35s ease;
    }
    .site-home-hero__slide:hover .site-home-hero__overlay,
    .site-home-hero__slide:focus-within .site-home-hero__overlay {
        background:
            linear-gradient(115deg, rgba(10, 24, 30, 0.14) 0%, rgba(10, 24, 30, 0.06) 42%, rgba(10, 24, 30, 0.24) 100%),
            linear-gradient(180deg, rgba(12, 31, 36, 0.08) 0%, rgba(12, 31, 36, 0.24) 32%, rgba(12, 31, 36, 0.92) 100%);
        opacity: 1;
    }
    .site-home-hero__content {
        position: absolute;
        inset: 0;
        z-index: 4;
        display: flex;
        align-items: center;
        text-align: center;
        padding-top: 5.75rem;
    }
    .site-home-hero__content .row {
        width: 100%;
        transform: translateY(-2.2rem);
    }
    .site-home-hero__title {
        margin: 0.8rem 0;
        color: #fff;
        font-size: clamp(3.1rem, 7.1vw, 6.15rem);
        font-weight: 700;
        line-height: 0.95;
        max-width: min(100%, 22ch);
        margin-left: auto;
        margin-right: auto;
        letter-spacing: 0.02em;
        text-shadow: 0 10px 28px rgba(0, 0, 0, 0.42);
    }
    .site-home-hero__title-line {
        display: block;
        margin-left: auto;
        margin-right: auto;
    }
    .site-home-hero__title-line--primary {
        width: fit-content;
        max-width: 100%;
    }
    .site-home-hero__title-line--secondary {
        width: fit-content;
        max-width: 82%;
    }
    .site-home-hero__text {
        max-width: min(100%, 72rem);
        color: #ffd533;
        font-size: clamp(1.1rem, 2vw, 1.7rem);
        margin-bottom: 1.4rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.45;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-shadow: 0 8px 20px rgba(0, 0, 0, 0.32);
    }
    .site-home-hero__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.8rem;
        justify-content: center;
    }
    .site-home-hero__primary,
    .site-home-hero__secondary,
    .site-home-intro__cta,
    .site-home-split__button,
    .site-home-hero__welcome-actions a,
    .site-hover-tile__footer a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.8rem;
        padding: 0.82rem 1.2rem;
        border-radius: 999px;
        text-decoration: none;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .site-home-hero__primary {
        background: linear-gradient(135deg, #b8955d 0%, #d5b47a 100%);
        color: #13211e !important;
    }
    .site-home-hero__secondary {
        border: 1px solid rgba(255, 255, 255, 0.28);
        background: rgba(255, 255, 255, 0.08);
        color: #fff !important;
    }
    .site-home-hero__welcome-card {
        position: relative;
        z-index: 5;
        margin: -5.2rem auto 0;
        width: min(92%, 68rem);
        background: rgba(255, 250, 244, 0.96);
        border: 1px solid rgba(184, 149, 93, 0.18);
        border-radius: 1.35rem;
        padding: 1rem 1.15rem;
        box-shadow: 0 18px 34px rgba(20, 33, 37, 0.1);
    }
    .site-home-intro {
        padding-top: 8.8rem;
    }
    .site-home-hero__welcome-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: 1rem;
        align-items: center;
    }
    .site-home-hero__welcome-label,
    .site-branch-card__tag,
    .site-hover-tile__tag,
    .site-service-panel__eyebrow {
        display: inline-flex;
        width: fit-content;
        padding: 0.42rem 0.68rem;
        border-radius: 999px;
        background: rgba(184, 149, 93, 0.12);
        color: #9a7641;
        font-size: 0.64rem;
        letter-spacing: 0.14em;
        font-weight: 700;
        text-transform: uppercase;
    }
    .site-home-hero__welcome-grid h2 {
        margin: 0.45rem 0 0;
        font-size: clamp(1.2rem, 2.2vw, 1.6rem);
        line-height: 1.05;
        color: #17352f;
    }
    .site-home-hero__welcome-grid p,
    .site-home-hero__welcome-actions span,
    .site-home-intro__text,
    .site-home-split__intro p,
    .site-branch-card p,
    .site-hover-tile__overlay p,
    .site-service-panel__body p {
        margin: 0;
        color: #5c6b6d;
        font-size: 0.84rem;
        line-height: 1.7;
    }
    .site-home-hero__welcome-actions {
        display: grid;
        gap: 0.45rem;
        justify-items: end;
        text-align: right;
    }
    .site-home-hero__welcome-actions a,
    .site-home-intro__cta,
    .site-home-split__button,
    .site-hover-tile__footer a {
        background: #17352f;
        color: #fff !important;
    }
    .site-home-intro,
    .site-home-branches,
    .site-home-rooms,
    .site-home-services {
        padding: 4.6rem 0;
    }
    .site-home-intro__title,
    .site-home-split__intro h2,
    .site-service-panel__body h3,
    .site-branch-card h3,
    .site-hover-tile__overlay h3 {
        font-size: clamp(2rem, 3.2vw, 3rem);
        line-height: 0.98;
        color: #17352f;
        margin: 0.85rem 0;
    }
    .site-home-stats {
        margin-top: 2rem;
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 0.9rem;
    }
    .site-home-stats article {
        background: linear-gradient(180deg, #fffdf8 0%, #f6f0e5 100%);
        border: 1px solid rgba(184, 149, 93, 0.16);
        border-radius: 0;
        padding: 1rem;
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }
    .site-home-stats article:hover {
        transform: translateY(-6px);
        border-color: rgba(23, 53, 47, 0.18);
        box-shadow: 0 18px 36px rgba(23, 53, 47, 0.08);
    }
    .site-home-stats strong {
        display: block;
        font-size: clamp(1.45rem, 3vw, 2.1rem);
        line-height: 1;
        color: #17352f;
        font-family: 'Cormorant Garamond', Georgia, serif;
    }
    .site-home-stats span {
        display: block;
        margin-top: 0.45rem;
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        color: #8a7656;
        font-weight: 700;
    }
    .site-home-branches {
        background: linear-gradient(180deg, #fff 0%, #faf7f1 100%);
    }
    .site-home-rooms {
        background:
            radial-gradient(circle at top right, rgba(184, 149, 93, 0.13), transparent 28%),
            linear-gradient(180deg, #f8f3eb 0%, #f3ece1 100%);
    }
    .site-home-services {
        background: #fff;
    }
    .site-home-split {
        display: grid;
        grid-template-columns: minmax(260px, 0.88fr) minmax(0, 1.12fr);
        gap: 2rem;
        align-items: start;
    }
    .site-home-split__intro {
        position: sticky;
        top: 8rem;
    }
    .site-home-split__cards {
        min-width: 0;
    }
    .site-home-rooms .site-home-split {
        grid-template-columns: minmax(320px, 0.98fr) minmax(0, 1.02fr);
        gap: clamp(1.25rem, 3vw, 2.8rem);
    }
    .site-home-rooms .site-home-split__intro {
        max-width: 34rem;
    }
    .site-home-rooms .site-home-split__intro h2 {
        font-size: clamp(2.6rem, 4.6vw, 4.2rem);
        line-height: 0.92;
        margin-bottom: 1rem;
    }
    .site-home-rooms .site-home-split__intro p {
        font-size: 1.03rem;
        line-height: 1.9;
        max-width: 32rem;
    }
    .site-home-rooms .site-home-split__cards {
        position: relative;
        overflow: hidden;
        padding-left: 0.15rem;
    }
    .site-home-split--branch-reverse {
        grid-template-columns: minmax(0, 1.12fr) minmax(260px, 0.88fr);
    }
    .site-home-split--branch-reverse .site-home-split__cards {
        order: 1;
    }
    .site-home-split--branch-reverse .site-home-split__intro {
        order: 2;
        text-align: left;
    }
    .site-home-split--branch-reverse .site-home-split__button {
        margin-left: 0;
    }
    .site-home-rooms .site-home-split__cards .js-section-slider,
    .site-home-rooms .site-home-split__cards .swiper-wrapper,
    .site-home-rooms .site-home-split__cards .swiper-slide {
        max-width: 100%;
    }
    .site-home-split__button {
        margin-top: 1.2rem;
    }
    .site-branch-card {
        display: grid;
        grid-template-columns: minmax(220px, 250px) minmax(0, 1fr);
        gap: 1rem;
        padding: 1rem;
        border-radius: 0;
        background: #fff;
        border: 1px solid rgba(23, 53, 47, 0.08);
        box-shadow: 0 14px 30px rgba(23, 53, 47, 0.08);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }
    .site-branch-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 42px rgba(23, 53, 47, 0.11);
        border-color: rgba(23, 53, 47, 0.16);
    }
    .site-branch-card + .site-branch-card {
        margin-top: 1rem;
    }
    .site-branch-card__media {
        min-height: 170px;
        border-radius: 0;
        background-size: cover;
        background-position: center;
    }
    .site-branch-card__body {
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
        justify-content: center;
    }
    .site-branch-card__body h3 {
        font-size: clamp(1.35rem, 2vw, 1.85rem);
        margin: 0;
    }
    .site-branch-card__meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    .site-branch-card__meta span {
        padding: 0.45rem 0.7rem;
        border-radius: 999px;
        background: #f7f2e8;
        font-size: 0.68rem;
        color: #6b5a41;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .site-hover-tile {
        position: relative;
        min-height: 30rem;
        border-radius: 0;
        overflow: hidden;
        background: #d9d2c7;
        width: 100%;
    }
    .site-hover-tile__media {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        transform: scale(1.02);
        transition: transform 0.35s ease;
    }
    .site-hover-tile__pointer-glow {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(30, 77, 107, 0.02) 0%, rgba(30, 77, 107, 0.18) 42%, rgba(23, 53, 47, 0.42) 100%);
        opacity: 0;
        transition: opacity 0.32s ease;
        z-index: 1;
        pointer-events: none;
    }
    .site-hover-tile__overlay {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 0.8rem;
        padding: 1.4rem;
        color: #fff;
        background: linear-gradient(180deg, rgba(19, 33, 30, 0.08) 0%, rgba(19, 33, 30, 0.28) 45%, rgba(19, 33, 30, 0.82) 100%);
    }
    .site-hover-tile__overlay h3,
    .site-hover-tile__overlay p,
    .site-hover-tile__footer strong {
        color: #fff;
    }
    .site-hover-tile__overlay p {
        max-width: 22rem;
    }
    .site-hover-tile__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.8rem;
    }
    .site-hover-tile__footer strong {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.4rem;
        line-height: 1;
    }
    .site-hover-tile:hover .site-hover-tile__media {
        transform: scale(1.08);
    }
    .site-hover-tile:hover .site-hover-tile__pointer-glow {
        opacity: 1;
    }
    .site-hover-tile--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 20rem;
    }
    .site-slider-arrows {
        display: flex;
        justify-content: flex-start;
        gap: 0.8rem;
        margin-top: 1rem;
    }
    .site-slider-arrows button {
        width: 3rem;
        height: 3rem;
        border-radius: 999px;
        border: 1px solid rgba(23, 53, 47, 0.14);
        background: rgba(255, 255, 255, 0.88);
        color: #17352f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }
    @media (max-width: 1199px) {
        .site-home-split,
        .site-home-hero__welcome-grid,
        .site-home-stats,
        .site-branch-card {
            grid-template-columns: 1fr;
        }
        .site-home-split--branch-reverse .site-home-split__cards,
        .site-home-split--branch-reverse .site-home-split__intro {
            order: initial;
        }
        .site-home-split__intro {
            position: static;
        }
        .site-home-rooms .site-home-split__intro {
            max-width: none;
        }
        .site-home-rooms .site-home-split {
            grid-template-columns: 1fr;
        }
        .site-home-rooms .site-home-split__cards {
            padding-left: 0;
        }
    }
    @media (max-width: 767px) {
        .site-home-hero .hero__slider {
            height: clamp(700px, 94vh, 860px) !important;
        }
        .site-home-hero__content {
            padding-top: 5.4rem;
        }
        .site-home-hero__content .row {
            transform: translateY(-1rem);
        }
        .site-home-intro,
        .site-home-branches,
        .site-home-rooms,
        .site-home-services {
            padding: 3.6rem 0;
        }
        .site-home-intro {
            padding-top: 6.1rem;
        }
        .site-home-hero__title {
            font-size: clamp(2.6rem, 11vw, 4rem);
            max-width: min(100%, 13ch);
        }
        .site-home-hero__text {
            font-size: 0.98rem;
        }
        .site-home-hero__welcome-card {
            width: calc(100% - 1rem);
            padding: 0.9rem;
            margin-top: -3.4rem;
        }
        .site-home-hero__welcome-actions {
            justify-items: start;
            text-align: left;
        }
        .site-home-rooms .site-home-split {
            gap: 1.35rem;
        }
        .site-home-rooms .site-home-split__intro h2 {
            font-size: clamp(2rem, 10vw, 3rem);
            line-height: 0.96;
        }
        .site-home-rooms .site-home-split__intro p {
            font-size: 0.95rem;
            line-height: 1.75;
            max-width: none;
        }
        .site-home-rooms .site-home-split__cards,
        .site-home-rooms .site-home-split__cards .js-section-slider,
        .site-home-rooms .site-home-split__cards .swiper-wrapper,
        .site-home-rooms .site-home-split__cards .swiper-slide {
            width: 100%;
        }
        .site-hover-tile {
            min-height: 22rem;
        }
        .site-hover-tile__footer,
        .site-service-zigzag-card__meta {
            flex-direction: column;
            align-items: flex-start;
        }
        .site-hover-tile__footer a {
            width: 100%;
        }
        .site-branch-card__media {
            min-height: 13rem;
        }
        .site-slider-arrows {
            justify-content: flex-end;
        }
    }
    @media (max-width: 575px) {
        .site-home-rooms .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        .site-home-rooms .site-home-split__cards {
            overflow: visible;
        }
        .site-home-rooms .swiper-slide {
            display: block;
        }
        .site-hover-tile__overlay {
            padding: 1.1rem;
        }
        .site-hover-tile__overlay h3 {
            font-size: 2rem;
        }
    }
</style>
@endsection
