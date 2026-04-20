@extends('layouts.site')

@section('title', __('Room rates'))

@section('content')
<style>
    /* Mitindo ya Kadi kama kwenye picha */
    .room-type-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 25px;
        padding: 40px 0;
    }

    .room-type-card {
        position: relative;
        height: 500px;
        border-radius: 30px;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        text-align: center;
        color: #fff;
        transition: transform 0.3s ease;
        background-color: #000;
    }

    .room-type-card:hover {
        transform: translateY(-10px);
    }

    .room-type-card__bg {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.7; /* Inafanya maandishi yaonekane vizuri */
        z-index: 1;
    }

    .room-type-card__content {
        position: relative;
        z-index: 2;
        padding: 20px;
        width: 100%;
    }

    .room-type-card__hotel {
        text-transform: uppercase;
        letter-spacing: 3px;
        font-size: 12px;
        font-weight: 700;
        margin-bottom: 10px;
        opacity: 0.9;
    }

    .room-type-card__title {
        font-family: 'Cormorant Garamond', serif;
        font-size: 42px;
        font-weight: 600;
        margin-bottom: 15px;
        line-height: 1.1;
    }

    .room-type-card__price {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 25px;
    }

    .room-type-card__price span {
        font-size: 16px;
        font-weight: 400;
        opacity: 0.8;
    }

    .room-type-card__btn {
        background: #fff;
        color: #000;
        padding: 12px 40px;
        border-radius: 50px;
        text-transform: uppercase;
        font-weight: 700;
        font-size: 14px;
        text-decoration: none;
        transition: 0.3s;
    }

    .room-type-card__btn:hover {
        background: #2563eb;
        color: #fff;
    }
</style>

<section data-anim-wrap class="pageHero -type-1 -items-center">
    <div class="pageHero__bg">
        <div class="hero__slider js-section-slider" data-gap="0" data-slider-cols="xl-1 lg-1 md-1 sm-1 base-1" data-loop>
            <div class="swiper-wrapper">
                @foreach(array_slice($homeHeroSlideUrls ?? [($heroUrl ?? asset('img/pageHero/7.png'))], 0, 3) as $slide)
                    <div class="swiper-slide">
                        <div style="position:relative;height:100%;min-height:100%;">
                            <div style="position:absolute;inset:0;background-image:url('{{ $slide }}');background-size:cover;background-position:center;">
                                <div style="position:absolute;inset:0;background:linear-gradient(180deg, rgba(5,16,57,0.65), rgba(5,16,57,0.45));"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row justify-center">
            <div class="col-auto">
                <div data-split="lines" data-anim-child="split-lines delay-3" class="pageHero__content text-center">
                    <p class="pageHero__subtitle text-white uppercase mb-15">{{ __('Premium Stays') }}</p>
                    <h1 class="pageHero__title lh-11 capitalize text-white">{{ __('Room Categories') }}</h1>
                    <p class="pageHero__text text-white mt-15">{{ __('Choose your preferred category and book your stay directly.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="layout-pt-lg layout-pb-lg" style="background:#fff;">
    <div class="container">
        <div class="room-type-grid">
            {{-- Tunazunguka kwenye Room Types sasa --}}
            @foreach ($roomTypes ?? [] as $type)
                <article class="room-type-card">
                    {{-- Picha ya aina ya chumba --}}
                    <img class="room-type-card__bg" src="{{ $type->heroImageUrl() }}" alt="{{ $type->name }}">

                    <div class="room-type-card__content">
                        <p class="room-type-card__hotel">{{ $siteSettings->hotelDisplayName() }}</p>
                        <h2 class="room-type-card__title">{{ $type->name }}</h2>

                        <div class="room-type-card__price">
                            {{ number_format($type->price) }} TZS <span>/ {{ __('night') }}</span>
                        </div>

                        <a href="{{ route('site.booking', ['type' => $type->id]) }}" class="room-type-card__btn">
                            {{ __('Book Now') }}
                        </a>
                    </div>
                </article>
            @endforeach
        </div>

        <p class="text-14 mt-60 text-center" style="opacity:.72;max-width:36rem;margin-left:auto;margin-right:auto;">
            {{ __('Rates are subject to availability. Prices may vary based on seasons and special holidays.') }}
        </p>
    </div>
</section>

@if(($roomRanks ?? collect())->isNotEmpty())
<section class="layout-pt-md layout-pb-lg" style="background:#f8fafc;">
    <div class="container">
        <h2 class="text-30 fw-700 mb-25 text-center">{{ __('Room Ranks') }}</h2>
        <div class="row y-gap-20">
            @foreach($roomRanks as $rank)
                @php $rankRooms = $roomsByRankId[$rank->id] ?? collect(); @endphp
                <div class="col-lg-4 col-md-6">
                    <article class="site-card-hover" style="height:100%;padding:1rem 1rem 1.2rem;border:1px solid #e2e8f0;border-radius:14px;background:#fff;">
                        <div class="text-12 uppercase fw-700" style="letter-spacing:.08em;color:#64748b;">{{ __('Rank') }}</div>
                        <h3 class="text-22 fw-700 mt-5 mb-10">{{ $rank->name }}</h3>
                        <p class="text-14" style="opacity:.75;">{{ $rank->description ?: __('Premium class grouping from your database setup.') }}</p>
                        <div class="mt-15 text-13">
                            <strong>{{ __('Rooms') }}:</strong> {{ $rankRooms->count() }}
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
