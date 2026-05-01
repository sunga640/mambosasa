@extends('layouts.site')

@section('title', __('Room rates'))

@section('content')
<br>

<section class="site-pricing-room-types">
    <div class="container">
        <div class="site-pricing-room-types__head">
            <span class="site-kicker">{{ __('Room Types') }}</span>
            <h2>{{ __('Explore our stay collection') }}</h2>
            <p>{{ __('Browse room categories in a cleaner full-width slider area so every card stays inside its own side without crossing into the intro content.') }}</p>
        </div>
        <div class="site-pricing-room-types__split">
            <div class="site-pricing-room-types__intro site-pricing-room-types__intro-card">
                <span class="site-kicker">{{ __('Room Types') }}</span>
                <h2>{{ __('Modern room types') }}</h2>
                <p>{{ __('Explore your room categories with the same room-type storytelling used on the home page, while keeping booking actions close to each card.') }}</p>
            </div>

            <div class="site-pricing-room-types__cards">
                <div class="js-section-slider"
                     data-gap="18"
                     data-slider-cols="xl-2 lg-2 md-1 sm-1 base-1"
                     data-nav-prev="js-pricing-rooms-prev"
                     data-nav-next="js-pricing-rooms-next"
                     data-loop>
                    <div class="swiper-wrapper">
                        @forelse ($roomTypes ?? [] as $type)
                            @php
                                $sampleRoom = $type->rooms->first();
                                $typeImage = $type->heroImageUrl() ?? $sampleRoom?->cardImageUrl() ?? asset('img/cards/rooms/3/1.png');
                            @endphp
                            <div class="swiper-slide">
                                <article class="site-pricing-hover-tile">
                                    <div class="site-pricing-hover-tile__media" style="background-image:url('{{ $typeImage }}');"></div>
                                    <div class="site-pricing-hover-tile__pointer-glow"></div>
                                    <div class="site-pricing-hover-tile__overlay">
                                        <span class="site-pricing-hover-tile__tag">{{ $type->branch?->name ?? __('Room type') }}</span>
                                        <h3>{{ $type->name }}</h3>
                                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($type->description ?? __('Elegant comfort with a restful atmosphere.')), 95) }}</p>
                                        <div class="site-pricing-hover-tile__footer">
                                            <strong>{{ number_format((int) round((float) $type->price), 0) }} TZS</strong>
                                            <a href="{{ route('site.booking', ['type' => $type->id]) }}">{{ __('Reserve') }}</a>
                                        </div>
                                    </div>
                                </article>
                            </div>
                        @empty
                            <div class="swiper-slide">
                                <div class="site-pricing-hover-tile site-pricing-hover-tile--empty">{{ __('No rooms available.') }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="site-pricing-room-types__arrows">
                    <button type="button" class="js-pricing-rooms-prev"><i class="icon-arrow-left text-18"></i></button>
                    <button type="button" class="js-pricing-rooms-next"><i class="icon-arrow-right text-18"></i></button>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .site-pricing-hero-slide {
        height: 100%;
        min-height: 100%;
        background-size: cover;
        background-position: center;
        position: relative;
    }
    .site-pricing-hero-slide::after {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(10, 24, 30, 0.65) 0%, rgba(10, 24, 30, 0.74) 100%);
    }
    .site-pricing-room-types {
        padding: 4rem 0;
        background: linear-gradient(180deg, #fff 0%, #f8f3eb 100%);
    }
    .site-pricing-room-types__head {
        max-width: 52rem;
        margin: 0 auto 2rem;
        text-align: center;
    }
    .site-pricing-room-types__head h2 {
        margin: 0.85rem 0 1rem;
        font-size: clamp(2.6rem, 4.8vw, 4.5rem);
        line-height: 0.92;
        color: #17352f;
    }
    .site-pricing-room-types__head p {
        margin: 0 auto;
        max-width: 40rem;
        color: #5c6b6d;
        font-size: 1rem;
        line-height: 1.85;
    }
    .site-pricing-room-types__split {
        display: grid;
        grid-template-columns: minmax(280px, 0.78fr) minmax(0, 1.22fr);
        gap: clamp(1.25rem, 2.6vw, 2.4rem);
        align-items: start;
    }
    .site-pricing-room-types__intro,
    .site-pricing-ranks__head {
        max-width: 34rem;
    }
    .site-pricing-room-types__intro h2,
    .site-pricing-ranks__head h2 {
        margin: 0.8rem 0 1rem;
        font-size: clamp(2.6rem, 4.6vw, 4.2rem);
        line-height: 0.92;
        color: #17352f;
    }
    .site-pricing-room-types__intro p {
        margin: 0;
        color: #5c6b6d;
        font-size: 1.03rem;
        line-height: 1.9;
        max-width: 32rem;
    }
    .site-pricing-room-types__intro-card {
        padding: 1.6rem;
        background: #fff;
        border: 1px solid rgba(23, 53, 47, 0.08);
        box-shadow: 0 12px 30px rgba(23, 53, 47, 0.06);
    }
    .site-pricing-room-types__cards {
        position: relative;
        overflow: hidden;
        min-width: 0;
        padding-left: 0;
    }
    .site-pricing-room-types__cards .js-section-slider,
    .site-pricing-room-types__cards .swiper-wrapper,
    .site-pricing-room-types__cards .swiper-slide {
        max-width: 100%;
    }
    .site-pricing-room-types__cards .swiper-slide {
        display: block;
    }
    .site-pricing-hover-tile {
        position: relative;
        min-height: 31rem;
        overflow: hidden;
        background: #102523;
        border-radius: 0;
        isolation: isolate;
    }
    .site-pricing-hover-tile__media,
    .site-pricing-hover-tile__pointer-glow,
    .site-pricing-hover-tile__overlay {
        position: absolute;
        inset: 0;
    }
    .site-pricing-hover-tile__media {
        background-size: cover;
        background-position: center;
        transform: scale(1.02);
        transition: transform 0.45s ease;
    }
    .site-pricing-hover-tile__pointer-glow {
        background: radial-gradient(circle at var(--px, 50%) var(--py, 50%), rgba(255, 255, 255, 0.24), transparent 36%);
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 1;
        pointer-events: none;
    }
    .site-pricing-hover-tile__overlay {
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        gap: 0.8rem;
        padding: 1.2rem;
        color: #fff;
        z-index: 2;
        background: linear-gradient(180deg, rgba(7, 18, 21, 0.08) 0%, rgba(7, 18, 21, 0.3) 35%, rgba(7, 18, 21, 0.92) 100%);
    }
    .site-pricing-hover-tile__tag,
    .site-pricing-rank-card__tag {
        display: inline-flex;
        width: fit-content;
        padding: 0.42rem 0.68rem;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        font-size: 0.67rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .site-pricing-hover-tile h3 {
        margin: 0;
        font-size: clamp(2rem, 3vw, 2.75rem);
        line-height: 0.95;
        color: #fff;
    }
    .site-pricing-hover-tile p {
        margin: 0;
        color: rgba(255, 255, 255, 0.86);
        font-size: 0.92rem;
        line-height: 1.75;
    }
    .site-pricing-hover-tile__footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.9rem;
        margin-top: 0.5rem;
    }
    .site-pricing-hover-tile__footer strong {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(1.85rem, 3vw, 2.4rem);
        line-height: 1;
        color: #fff;
    }
    .site-pricing-rank-card__count {
        display: block;
        margin-top: 0.25rem;
        color: #8a7656;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
    }
    .site-pricing-hover-tile__footer a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.7rem;
        padding: 0.78rem 1.1rem;
        border-radius: 999px;
        background: #17352f;
        color: #fff !important;
        text-decoration: none;
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .site-pricing-hover-tile:hover .site-pricing-hover-tile__media {
        transform: scale(1.08);
    }
    .site-pricing-hover-tile:hover .site-pricing-hover-tile__pointer-glow {
        opacity: 1;
    }
    .site-pricing-hover-tile--empty {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: 20rem;
        background: #fff;
        color: #17352f;
        border: 1px solid rgba(23, 53, 47, 0.08);
    }
    .site-pricing-room-types__arrows {
        display: flex;
        justify-content: flex-end;
        gap: 0.8rem;
        margin-top: 1rem;
    }
    .site-pricing-room-types__arrows button {
        width: 3.55rem;
        height: 3.55rem;
        border-radius: 50%;
        border: 1px solid rgba(23, 53, 47, 0.12);
        background: #fff;
        color: #17352f;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    @media (max-width: 991px) {
        .site-pricing-room-types__split {
            grid-template-columns: 1fr;
        }
        .site-pricing-room-types__intro {
            max-width: none;
        }
    }
    @media (max-width: 767px) {
        .site-pricing-room-types { padding: 3.3rem 0; }
        .site-pricing-room-types__head {
            margin-bottom: 1.35rem;
        }
        .site-pricing-room-types__head h2 {
            font-size: clamp(2.2rem, 10vw, 3.4rem);
        }
        .site-pricing-room-types__split {
            gap: 1.35rem;
        }
        .site-pricing-room-types__intro h2 {
            font-size: clamp(2rem, 10vw, 3rem);
            line-height: 0.96;
        }
        .site-pricing-room-types__intro p {
            font-size: 0.95rem;
            line-height: 1.75;
            max-width: none;
        }
        .site-pricing-room-types__cards,
        .site-pricing-room-types__cards .js-section-slider,
        .site-pricing-room-types__cards .swiper-wrapper,
        .site-pricing-room-types__cards .swiper-slide {
            width: 100%;
        }
        .site-pricing-hover-tile {
            min-height: 22rem;
        }
        .site-pricing-hover-tile__footer {
            flex-direction: column;
            align-items: flex-start;
        }
        .site-pricing-hover-tile__footer a {
            width: 100%;
        }
    }
</style>
@endsection
