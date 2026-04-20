@extends('layouts.site')

@section('title', __('About Us'))

@section('content')
@php
    $aboutHeroSlides = $homeHeroSlideUrls ?? [];
    if ($aboutHeroSlides === []) {
        $aboutHeroSlides = [asset('img/pageHero/4.png'), asset('img/pageHero/1.png')];
    }
    $aboutSlide = fn (int $i): string => $aboutHeroSlides[$i % count($aboutHeroSlides)];
@endphp

    <!-- SECTION 1: HERO INTRO (Long Titles & Advanced Typography) -->
    <section class="layout-pt-lg layout-pb-md bg-white">
      <div data-anim-wrap class="container">
        <div class="row justify-center text-center">
          <div class="col-xl-10 col-lg-11">

            <!-- Subtitle with Decorative Lines -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 20px;">
                <div style="width: 40px; height: 1px; background: #2563eb;"></div>
                <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: #2563eb;">
                    {{ __('Our Legacy & Vision') }}
                </span>
                <div style="width: 40px; height: 1px; background: #2563eb;"></div>
            </div>

            <div class="row justify-center">
                <div class="col-lg-9">
                    <p style="font-size: 19px; line-height: 1.8; color: #4b5563;">
                        {{ __(':name welcomes travellers who seek well-appointed rooms, transparent pricing, and simple online booking. Whether you are here for business or a coastal getaway, we focus on seamless check-ins and high-quality service you can rely on.', ['name' => $siteSettings->hotelDisplayName()]) }}
                    </p>
                </div>
            </div>

            <!-- STATS SECTION (Compact & Premium) -->
            <div class="row y-gap-30 justify-between pt-80 md:pt-50">
                @php
                    $statItems = [
                        ['num' => $stats['customers_display'], 'label' => __('Happy Guests')],
                        ['num' => $stats['rooms_count'], 'label' => __('Luxury Rooms')],
                        ['num' => $stats['pools_count'], 'label' => __('Wellness Spots')],
                        ['num' => $stats['restaurants_count'], 'label' => __('Dining Venues')]
                    ];
                @endphp

                @foreach($statItems as $item)
                <div class="col-lg-3 col-6">
                    <div style="padding: 10px;">
                        <h3 style="font-size: 64px; font-weight: 800; color: #051039; margin-bottom: 5px; letter-spacing: -3px; line-height: 1;">{{ $item['num'] }}</h3>
                        <div style="width: 25px; height: 3px; background: #2563eb; margin: 0 auto 15px;"></div>
                        <div style="font-size: 11px; font-weight: 700; text-transform: uppercase; color: #697488; letter-spacing: 1px;">{{ $item['label'] }}</div>
                    </div>
                </div>
                @endforeach
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: ALTERNATING CONTENT BLOCKS -->
    <section class="layout-pt-md layout-pb-lg bg-white">
      <div class="container">

        <!-- Block 1: Image Left, Text Right -->
        <div data-anim-wrap class="row y-gap-40 justify-between items-center pb-100 sm:pb-50">
          <div data-anim="img-right cover-white delay-1" class="col-lg-6">
            <div style="border-radius: 30px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
              <img src="{{ $aboutSlide(0) }}" alt="Luxury Rooms" style="width: 100%; height: 500px; object-fit: cover;">
            </div>
          </div>

          <div class="col-lg-5">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div style="width: 30px; height: 2px; background: #2563eb;"></div>
                <span style="font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase;">{{ __('Modern Living') }}</span>
            </div>
            <h3 style="font-size: 38px; font-weight: 800; color: #051039; line-height: 1.2; margin-bottom: 25px;">
              {{ __('Thoughtful Rooms & Calm Personal Spaces') }}
            </h3>
            <p style="font-size: 16px; line-height: 1.8; color: #4b5563;">
              {{ __('Every room at :name is prepared for rest: premium bedding, transparent nightly rates in TZS, and housekeeping standards that match global hospitality expectations.', ['name' => $siteSettings->hotelDisplayName()]) }}
            </p>
            <div class="pt-30">
                <a href="{{ route('site.booking') }}" class="btn-advanced-navy">
                    {{ __('View Availability') }}
                    <div class="icon-circle"><i class="icon-arrow-top-right"></i></div>
                </a>
            </div>
          </div>
        </div>

        <!-- Block 2: Text Left, Image Right -->
        <div data-anim-wrap class="row y-gap-40 justify-between items-center pt-60 sm:pt-40">
          <div class="col-lg-5 order-2 order-lg-1">
            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 15px;">
                <div style="width: 30px; height: 2px; background: #2563eb;"></div>
                <span style="font-size: 12px; font-weight: 700; color: #2563eb; text-transform: uppercase;">{{ __('Guest Portal') }}</span>
            </div>
            <h3 style="font-size: 38px; font-weight: 800; color: #051039; line-height: 1.2; margin-bottom: 25px;">
              {{ __('Seamless Dining & Professional Guest Services') }}
            </h3>
            <p style="font-size: 16px; line-height: 1.8; color: #4b5563;">
                {{ __('After your stay is confirmed, gain access to our digital guest portal. Request room service, view property maps, or contact our 24/7 concierge for any specialized needs during your visit.', ['name' => $siteSettings->hotelDisplayName()]) }}
            </p>
            <div class="pt-30">
                <a href="{{ route('site.page', ['slug' => 'contact']) }}" class="btn-advanced-navy">
                    {{ __('Get In Touch') }}
                </a>
            </div>
          </div>

          <div data-anim="img-right cover-white delay-1" class="col-lg-6 order-1 order-lg-2">
            <div style="border-radius: 30px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.08);">
              <img src="{{ $aboutSlide(1) }}" alt="Dining Services" style="width: 100%; height: 500px; object-fit: cover;">
            </div>
          </div>
        </div>

      </div>
    </section>



    <!-- SECTION 5: HOTEL SERVICES LIST -->
<section style="padding: 90px 0; background: #f8fafc;">
    <div class="container">
        <div class="text-center mb-40">
            <span style="font-size: 12px; font-weight: 700; color: #2563eb; letter-spacing: 0.14em; text-transform: uppercase;">
                {{ __('What We Offer') }}
            </span>
            <h2 style="font-size: clamp(30px, 4vw, 44px); font-weight: 800; color: #051039; margin-top: 14px; line-height: 1.2;">
                {{ __('Hotel Services Available') }}
            </h2>
            <p style="max-width: 720px; margin: 12px auto 0; font-size: 16px; color: #64748b; line-height: 1.7;">
                {{ __('Below is the current list of services uploaded in the system and available for guests.') }}
            </p>
        </div>

        @if(($aboutHotelServices ?? collect())->isEmpty())
            <div style="padding: 1rem 1.2rem; border: 1px dashed #94a3b8; border-radius: 12px; background: #fff; text-align: center; color: #475569;">
                {{ __('No hotel services uploaded yet.') }}
            </div>
        @else
            <div class="row y-gap-20">
                @foreach($aboutHotelServices as $svc)
                    <div class="col-lg-4 col-md-6">
                        <article style="height:100%;display:flex;flex-direction:column;border:1px solid #e2e8f0;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.06);">
                            @if($svc->imageUrl())
                                <div style="height:180px;overflow:hidden;">
                                    <img src="{{ $svc->imageUrl() }}" alt="{{ $svc->name }}" style="width:100%;height:100%;object-fit:cover;">
                                </div>
                            @endif
                            <div style="padding:1rem 1rem 1.15rem;">
                                <h3 style="font-size:1.08rem;font-weight:700;color:#0f172a;margin:0 0 .4rem;">{{ $svc->name }}</h3>
                                <p style="margin:0 0 .55rem;font-size:.78rem;letter-spacing:.08em;text-transform:uppercase;color:#64748b;font-weight:700;">
                                    {{ $svc->category ?: __('General service') }}
                                    @if($svc->branch)
                                        · {{ $svc->branch->name }}
                                    @endif
                                </p>
                                <p style="font-size:.92rem;line-height:1.6;color:#475569;margin:0 0 .8rem;">
                                    {{ $svc->description ?: __('Service details available at booking time.') }}
                                </p>
                                @if((float)$svc->price > 0)
                                    <div style="font-weight:800;color:#0f766e;">TZS {{ number_format((float)$svc->price, 0) }}</div>
                                @endif
                            </div>
                        </article>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
    /* Hii inafanya picha iwe na zoom nzuri mteja akigusa (hover) */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&display=swap');
</style>


    <!-- STYLES -->
    <style>
        .btn-advanced-navy {
            display: inline-flex;
            align-items: center;
            background: #051039;
            color: white !important;
            padding: 10px 10px 10px 30px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 15px;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-advanced-navy:hover { background: #2563eb; transform: translateY(-3px); }
        .btn-advanced-navy .icon-circle {
            width: 40px; height: 40px; background: rgba(255,255,255,0.15);
            border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 20px;
        }
        .circle-card-advanced {
            position: relative;
            border-radius: 50%;
            overflow: hidden;
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 8px solid white;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            text-decoration: none;
            transition: 0.5s;
        }
        .circle-card-advanced:hover { transform: scale(1.03) translateY(-10px); }
        .circle-overlay-gradient {
            position: absolute; inset: 0;
            background: radial-gradient(circle, rgba(5,16,57,0.3) 0%, rgba(5,16,57,0.85) 100%);
        }
        .circle-content-inner { position: relative; z-index: 2; text-align: center; }
        .direction-btn-circle {
            width: 70px; height: 70px; background: #2563eb; color: white;
            border-radius: 50%; display: flex; align-items: center; justify-content: center;
            font-size: 24px; margin: 0 auto; box-shadow: 0 10px 20px rgba(37,99,235,0.3); transition: 0.3s;
        }
        .direction-btn-circle.sm { width: 55px; height: 55px; font-size: 18px; }
        .circle-card-advanced:hover .direction-btn-circle { background: white; color: #2563eb; transform: rotate(45deg); }
        .badge-blue { background: #2563eb; color: white; padding: 4px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; }
        .badge-white { background: white; color: #051039; padding: 4px 12px; border-radius: 50px; font-size: 10px; font-weight: 800; }
        .hover-main:hover { color: #2563eb !important; }
        .imageGrid__item:hover img { transform: scale(1.1); }
        .imageGrid__item:hover .hover-overlay { opacity: 1 !important; }
    </style>

@endsection
