@extends('layouts.site')

@section('meta_description')
{{ __('Luxury hotel rooms, online booking, and guest services at :name.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}
@endsection


@section('content')
<!-- SECTION 1: HERO SLIDER -->
<section style="position: relative; overflow: visible; background-color: #ffffff; display: flex; flex-direction: column;">
  <!-- Swiper Slider Background -->
  <div class="hero__slider js-section-slider"
       data-gap="0"
       data-slider-cols="xl-1 lg-1 md-1 sm-1 base-1"
       data-nav-prev="js-sliderHero-prev"
       data-nav-next="js-sliderHero-next"
       data-loop
       style="height: clamp(500px, 75vh, 850px); width: 100%; position: relative;">

    <div class="swiper-wrapper" style="height: 100%;">

@foreach (array_slice($homeHeroSlideUrls ?? [], 0, 2) as $idx => $slideSrc)
      <div class="swiper-slide" style="height: 100%; width: 100%;">
        <div style="position: relative; height: 100%; width: 100%;">

          <!-- Background Image with Modern Gradient Overlay -->
          <div style="position: absolute; inset: 0; background-image: url('{{ $slideSrc }}'); background-size: cover; background-position: center; z-index: 1;">
            <!-- OVERLAY IMEBORESHWA HAPA: Inatumia gradient kuzuia maandishi yasipotee kwenye picha angavu -->
            <div style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(0,0,0,0.65) 0%, rgba(0,0,0,0.3) 50%, rgba(0,0,0,0.7) 100%); z-index: 2;"></div>
          </div>

          <!-- Main Headline -->
          <div style="position: relative; z-index: 3; display: flex; align-items: flex-start; justify-content: center; height: 100%; text-align: center; padding-top: clamp(60px, 12vh, 150px);">
            <div class="container" style="padding: 0 20px;">
              <!-- Nimeongeza text-shadow nzito kidogo kwa ajili ya usalama wa ziada -->
              <h1 style="font-size: clamp(38px, 9vw, 100px); font-weight: 900; color: #ffffff; line-height: 0.9; text-shadow: 0 10px 30px rgba(0,0,0,0.8); text-transform: uppercase; letter-spacing: -1px; margin: 0;">
                 Enjoy a Luxury <span style="color: #0099cc;">Experience</span><br>
                <span style="font-size: clamp(14px, 3.5vw, 30px); font-weight: 300; opacity: 0.95; letter-spacing: clamp(4px, 1vw, 12px); display: block; margin-top: 20px; text-transform: uppercase; text-shadow: 0 4px 10px rgba(0,0,0,0.5);">
                   Your home away from home.
                </span>
              </h1>
              <div style="width: 80px; height: 4px; background: #0099cc; margin: 30px auto 0;"></div>
            </div>
          </div>

        </div>
      </div>
      @endforeach
    </div>

    <!-- Navigation Arrows -->
    <div class="d-none d-md-flex" style="position: absolute; top: 45%; width: 100%; justify-content: space-between; padding: 0 50px; transform: translateY(-50%); z-index: 10; pointer-events: none;">
      <button class="js-sliderHero-prev" style="pointer-events: auto; width: 55px; height: 55px; border-radius: 50%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
        <i class="icon-arrow-left text-20"></i>
      </button>
      <button class="js-sliderHero-next" style="pointer-events: auto; width: 55px; height: 55px; border-radius: 50%; background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.3); color: white; cursor: pointer; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px);">
        <i class="icon-arrow-right text-20"></i>
      </button>
    </div>
  </div>

  <!-- FLOATING SEARCH CARD -->
  <div style="position: relative; z-index: 25; margin: -100px auto 40px; width: 94%; max-width: 1200px; background: #ffffff; border-radius: clamp(20px, 4vw, 40px); padding: clamp(25px, 5vw, 50px); box-shadow: 0 30px 70px rgba(0,0,0,0.15);">

    @php
      $hour = date('H');
      $greeting = ($hour < 12) ? 'Good Morning!' : (($hour < 17) ? 'Good Afternoon!' : 'Good Evening!');
    @endphp

    <div style="margin-bottom: clamp(20px, 4vw, 35px); text-align: center;">
      <span style="text-transform: uppercase; letter-spacing: 3px; font-size: clamp(10px, 2vw, 13px); color: #0099cc; font-weight: 700; display: block; margin-bottom: 10px;">{{ $greeting }} Welcome Home</span>
      <h2 style="font-size: clamp(24px, 4vw, 38px); font-weight: 800; color: #1a2b48; margin: 0; line-height: 1.2;">Experience Pure Serenity in Safe Hands</h2>
      <p class="d-none d-sm-block" style="font-size: 17px; color: #64748b; margin: 10px 0 0 0; font-style: italic;">"More than a Hotel, A Place for Your Precious Memories"</p>
    </div>

    <!-- Hapa kodi ya fomu yako inaendelea... -->
  </div>
</section>

<!-- SPACER FOR NEXT SECTION (Imepunguzwa kuwa responsive) -->
<div style="height: clamp(50px, 10vh, 150px);"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  var btn = document.querySelector('.js-sliderHero-next');
  if (btn) {
    setInterval(function () {
      btn.click();
    }, 7000);
  }
});
</script>
@endpush

    <!-- Nimeongeza margin-top: -50px na kupunguza padding ya juu kuwa 30px tu -->
<section style="margin-top: -50px; padding: 30px 0 60px; background-color: #ffffff; position: relative; z-index: 10; overflow: hidden;">
    <div class="container">

        <!-- TOP CONTENT SECTION -->
        <div class="row justify-center text-center">
            <div class="col-xl-9 col-lg-11">

                <!-- Subtitle -->
                <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 15px;">
                    <div style="width: 30px; height: 1px; background: #2563eb;"></div>
                    <div style="font-size: 11px; text-transform: uppercase; font-weight: 700; letter-spacing: 2px; color: #2563eb;">
                        {{ $siteSettings->company_name ?? config('app.name') }}
                    </div>
                    <div style="width: 30px; height: 1px; background: #2563eb;"></div>
                </div>

                <!-- Long Heading (Reduced margin-bottom) -->
                <h2 style="font-size: clamp(26px, 3.5vw, 42px); font-weight: 800; line-height: 1.1; color: #051039; margin-bottom: 15px; letter-spacing: -1px;">
                    {{ $siteSettings->home_section1_heading ?: __('Experience the Pinnacle of Coastal Luxury and Thoughtful Tanzanian Hospitality') }}
                </h2>

                <!-- Body Text (Compact) -->
                <div class="row justify-center">
                    <div class="col-lg-8">
                        <p style="font-size: 15px; line-height: 1.6; color: #4b5563; margin-bottom: 20px;">
                            @if ($siteSettings->home_section1_body)
                                {!! nl2br(e($siteSettings->home_section1_body)) !!}
                            @else
                                {{ __(':name invites you to refined elegance where every detail is curated for your comfort. From well-appointed rooms to our dedicated team, we ensure your stay is seamless and memorable.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Advanced Pill Button (More Compact) -->
                <div style="margin-top: 5px;">
                    <a href="{{ route('site.page', ['slug' => 'about']) }}"
                       style="display: inline-flex; align-items: center; padding: 6px 6px 6px 20px; background: #051039; color: #ffffff; border-radius: 50px; font-weight: 700; text-decoration: none; font-size: 13px; transition: 0.3s;">
                        {{ __('Discover Our Story') }}
                        <div style="width: 30px; height: 30px; background: rgba(255,255,255,0.1); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 12px;">
                            <i class="icon-arrow-top-right" style="font-size: 10px;"></i>
                        </div>
                    </a>
                </div>

            </div>
        </div>

        <!-- STATS SECTION (Inline Styled & Tight Spacing) -->
        <div class="row justify-center text-center" style="margin-top: 40px;">
            <div class="col-xl-10">
                <div class="row y-gap-20 justify-between items-center">

                    <!-- Stat Item 1 -->
                    <div class="col-md-3 col-6">
                        <div style="padding: 5px;">
                            <h3 style="font-size: 48px; font-weight: 800; color: #051039; margin-bottom: 2px; letter-spacing: -2px; line-height: 1;">
                                {{ $stats['customers_display'] }}
                            </h3>
                            <div style="width: 15px; height: 2px; background: #2563eb; margin: 0 auto 8px;"></div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #697488; letter-spacing: 1px;">
                                {{ $stats['caption_guests'] ?? __('Happy Guests') }}
                            </div>
                        </div>
                    </div>

                    <!-- Stat Item 2 -->
                    <div class="col-md-3 col-6">
                        <div style="padding: 5px;">
                            <h3 style="font-size: 48px; font-weight: 800; color: #051039; margin-bottom: 2px; letter-spacing: -2px; line-height: 1;">
                                {{ $stats['rooms_count'] }}
                            </h3>
                            <div style="width: 15px; height: 2px; background: #2563eb; margin: 0 auto 8px;"></div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #697488; letter-spacing: 1px;">
                                {{ $stats['caption_rooms'] ?? __('Luxury Rooms') }}
                            </div>
                        </div>
                    </div>

                    <!-- Stat Item 3 -->
                    <div class="col-md-3 col-6">
                        <div style="padding: 5px;">
                            <h3 style="font-size: 48px; font-weight: 800; color: #051039; margin-bottom: 2px; letter-spacing: -2px; line-height: 1;">
                                {{ $stats['pools_count'] }}
                            </h3>
                            <div style="width: 15px; height: 2px; background: #2563eb; margin: 0 auto 8px;"></div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #697488; letter-spacing: 1px;">
                                {{ $stats['caption_pools'] ?? __('Wellness Spots') }}
                            </div>
                        </div>
                    </div>

                    <!-- Stat Item 4 -->
                    <div class="col-md-3 col-6">
                        <div style="padding: 5px;">
                            <h3 style="font-size: 48px; font-weight: 800; color: #051039; margin-bottom: 2px; letter-spacing: -2px; line-height: 1;">
                                {{ $stats['restaurants_count'] }}
                            </h3>
                            <div style="width: 15px; height: 2px; background: #2563eb; margin: 0 auto 8px;"></div>
                            <div style="font-size: 10px; font-weight: 700; text-transform: uppercase; color: #697488; letter-spacing: 1px;">
                                {{ $stats['caption_dining'] ?? __('Dining Experiences') }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

   <section id="rooms" class="home-rooms-band" style="scroll-margin-top:120px;padding:72px 0 96px;background:linear-gradient(165deg,#f4e8d4 0%,#e9d0a8 40%,#deb892 100%);position:relative;overflow:hidden;">
  <div class="container" style="position:relative;z-index:1;">
    <div style="text-align:center;margin-bottom:42px;max-width:1000px;margin-left:auto;margin-right:auto;padding:0 16px;">
        <div style="display:flex;align-items:center;justify-content:center;gap:15px;margin-bottom:12px;">
            <div style="width:40px;height:1px;background:#8b2942;"></div>
            <span style="font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:3px;color:#8b2942;">{{ __('World-Class Accommodation') }}</span>
            <div style="width:40px;height:1px;background:#8b2942;"></div>
        </div>
        <h2 style="font-size:clamp(28px,4vw,46px);font-weight:800;color:#1a0f0a;line-height:1.12;letter-spacing:-1px;margin:0;">
            {{ __('Experience Unmatched Coastal Luxury and Refined Comfort in Our Sanctuary') }}
        </h2>
        <p style="font-size:16px;color:#4a3728;line-height:1.6;max-width:650px;margin:18px auto 0;">
            {{ __('From ocean-view suites to cozy garden retreats, explore our collection of rooms designed to provide an unforgettable experience.') }}
        </p>
    </div>

    <div class="js-section-slider"
         data-gap="16"
         data-slider-cols="xl-4 lg-3 md-2 sm-1 base-1"
         data-nav-prev="js-rooms-prev"
         data-nav-next="js-rooms-next"
         style="position:relative;">

      <div class="swiper-wrapper">
        @forelse ($homeRoomTypes as $roomType)
          @php
            $sampleRoom = $roomType->rooms->first();
            $bgImage = $roomType->heroImageUrl() ?? $sampleRoom?->cardImageUrl() ?? asset('img/cards/rooms/3/1.png');
          @endphp
          <div class="swiper-slide">
            <article class="home-rtc-card" style="max-width:340px;margin:0 auto;background:#fff;border-radius:22px;overflow:hidden;box-shadow:0 22px 48px rgba(26,15,10,0.18);border:1px solid rgba(255,255,255,0.65);">
              <div style="position:relative;height:220px;background-image:url('{{ $bgImage }}');background-size:cover;background-position:center;">
                <span style="position:absolute;top:14px;left:-6px;background:#8b2942;color:#fff;font-size:11px;font-weight:800;padding:6px 28px 6px 18px;clip-path:polygon(0 0,100% 0,92% 100%,0 100%);letter-spacing:.06em;">{{ __('Featured') }}</span>
              </div>
              <div style="position:relative;padding:1.35rem 1.35rem 0;min-height:210px;">
                <h3 style="font-size:1.35rem;font-weight:800;color:#111827;line-height:1.25;margin:0 0 6px;">{{ $roomType->name }}</h3>
                <p style="font-size:13px;color:#6b7280;margin:0;">{{ $roomType->branch?->name ?? __('Our hotel') }} · {{ __('Room type') }}</p>
                <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-top:1.25rem;padding-top:1.1rem;border-top:1px solid #f3f4f6;">
                  <div style="margin-left:-1.35rem;margin-bottom:-2px;align-self:flex-end;">
                    <a href="{{ route('site.booking', ['type' => $roomType->id]) }}" class="home-rtc-card__book" title="{{ __('Book now') }}">{{ __('Book') }}</a>
                  </div>
                  <div style="text-align:right;padding-bottom:4px;">
                    <div style="font-size:1.65rem;font-weight:800;color:#c41e3a;line-height:1;">{{ number_format((int) round((float) $roomType->price), 0) }} <span style="font-size:.95rem;font-weight:600;color:#374151;">TZS</span></div>
                    <div style="font-size:11px;font-weight:600;color:#6b7280;text-transform:uppercase;letter-spacing:.05em;">{{ __('per night') }}</div>
                  </div>
                </div>
              </div>
            </article>
          </div>
        @empty
          <div class="swiper-slide" style="text-align:center;padding:50px;">{{ __('No rooms available.') }}</div>
        @endforelse
      </div>

      <div style="display:flex;justify-content:center;gap:20px;margin-top:36px;">
        <button type="button" class="js-rooms-prev" style="width:56px;height:56px;border-radius:50%;border:2px solid #1a0f0a;background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;">
          <i class="icon-arrow-left text-20"></i>
        </button>
        <button type="button" class="js-rooms-next" style="width:56px;height:56px;border-radius:50%;border:none;background:#1a0f0a;color:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;">
          <i class="icon-arrow-right text-20"></i>
        </button>
      </div>
    </div>
  </div>
</section>
<style>
  .home-rtc-card__book {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 5.5rem;
    padding: 0.65rem 1.25rem;
    border-radius: 0 16px 0 0;
    font-size: 0.72rem;
    font-weight: 800;
    letter-spacing: 0.12em;
    text-transform: uppercase;
    text-decoration: none;
    color: #fff !important;
    background: linear-gradient(135deg, #1e4d6b 0%, #122223 48%, #8b2942 100%);
    box-shadow: 0 10px 26px rgba(18, 34, 35, 0.28);
    border: none;
    transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
  }
  .home-rtc-card__book:hover {
    transform: translateY(-2px);
    box-shadow: 0 14px 34px rgba(18, 34, 35, 0.35);
    filter: brightness(1.06);
  }
</style>

@if(($homeHotelServices ?? collect())->isNotEmpty())
<section style="padding:72px 0;background:#f8fafc;">
  <div class="container">
    <div style="text-align:center;margin-bottom:28px;">
      <span style="font-size:11px;font-weight:700;letter-spacing:.12em;text-transform:uppercase;color:#2563eb;">{{ __('Hotel Services') }}</span>
      <h2 style="font-size:clamp(26px,3.8vw,40px);font-weight:800;color:#051039;margin:10px 0 0;">{{ __('Services You Can Request') }}</h2>
    </div>
    <div class="row y-gap-20">
      @foreach($homeHotelServices as $svc)
        <div class="col-lg-4 col-md-6">
          <article class="site-card-hover" style="height:100%;display:flex;flex-direction:column;border:1px solid #e2e8f0;background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.06);">
            @if($svc->imageUrl())
              <div style="height:170px;overflow:hidden;"><img src="{{ $svc->imageUrl() }}" alt="{{ $svc->name }}" style="width:100%;height:100%;object-fit:cover;"></div>
            @endif
            <div style="padding:1rem 1rem 1.15rem;">
              <h3 style="font-size:1.05rem;font-weight:700;color:#0f172a;margin:0 0 .45rem;">{{ $svc->name }}</h3>
              <p style="font-size:.84rem;color:#64748b;margin:0 0 .6rem;text-transform:uppercase;letter-spacing:.06em;">{{ $svc->category ?: __('Service') }}</p>
              <p style="font-size:.9rem;line-height:1.55;color:#475569;margin:0;">{{ \Illuminate\Support\Str::limit($svc->description ?: __('Service details available during booking.'), 120) }}</p>
            </div>
          </article>
        </div>
      @endforeach
    </div>
  </div>
</section>
@endif

    @php
        $newsletterSlides = $homeHeroSlideUrls ?? [];
        if ($newsletterSlides === []) {
            $newsletterSlides = [asset('img/hero/8/2.png')];
        }
        // Kuchukua picha ya tatu au ya kwanza kama background
        $newsletterBgUrl = $newsletterSlides[(3 - 1) % count($newsletterSlides)];
    @endphp

    <section class="home-newsletter-section" style="position: relative; padding: 140px 0 120px; background-image: url('{{ $newsletterBgUrl }}'); background-size: cover; background-position: center; background-attachment: fixed; display: flex; align-items: center; overflow: hidden;">

      <!-- Dark Overlay kwa ajili ya usomaji mzuri wa maandishi -->
      <div style="position: absolute; inset: 0; background: rgba(0, 0, 0, 0.65); z-index: 1;"></div>

      <div class="container" style="position: relative; z-index: 2;">
        <div class="row justify-center text-center">
          <div class="col-xl-7 col-lg-9 col-md-11">

            <!-- Icon -->
            <div class="icon-email" style="font-size: 70px; color: #ffffff; margin-bottom: 35px; opacity: 0.9; display: flex; justify-content: center;"></div>

            <!-- Heading -->
            <div style="margin-bottom: 40px;">
              <h2 style="font-size: 46px; font-weight: 800; color: #ffffff; line-height: 1.2; text-shadow: 0 2px 10px rgba(0,0,0,0.3);">
                {{ __('Would you like to receive hotel news and special offers?') }}
              </h2>
              <p style="color: rgba(255,255,255,0.8); font-size: 18px; margin-top: 15px;">{{ __('Join our community and stay updated with the latest luxury deals.') }}</p>
            </div>

            <!-- Flash Messages -->
            @if (session('newsletter_ok'))
              <p style="background: #22c55e; color: white; padding: 12px 25px; border-radius: 50px; margin-bottom: 20px; font-weight: 600;">{{ session('newsletter_ok') }}</p>
            @endif
            @error('email')
              <p style="background: #ef4444; color: white; padding: 12px 25px; border-radius: 50px; margin-bottom: 20px; font-weight: 600;">{{ $message }}</p>
            @enderror

            <!-- Form -->
            <form method="post" action="{{ route('site.newsletter.subscribe') }}" style="max-width: 550px; margin: 0 auto; position: relative;">
              @csrf
              <div style="position: relative; display: flex; align-items: center; background: #ffffff; border-radius: 60px; padding: 6px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">

                <label class="sr-only" for="site-newsletter-email">{{ __('Your email') }}</label>

                <input id="site-newsletter-email"
                       type="email"
                       name="email"
                       value="{{ old('email') }}"
                       required
                       autocomplete="email"
                       placeholder="{{ __('Enter your email address') }}"
                       style="width: 100%; border: none; padding: 15px 30px; font-size: 16px; border-radius: 60px; outline: none; color: #111827;">

                <button type="submit"
                        aria-label="{{ __('Subscribe') }}"
                        style="background: #111827; color: #ffffff; width: 55px; height: 55px; border-radius: 50%; border: none; display: flex; align-items: center; justify-content: center; transition: 0.3s; cursor: pointer; flex-shrink: 0; margin-left: 10px;">
                  <i class="icon-arrow-right text-20"></i>
                </button>

              </div>
            </form>

            <p style="color: rgba(255,255,255,0.6); font-size: 13px; margin-top: 20px;">{{ __('Your privacy is safe with us. Unsubscribe at any time.') }}</p>

          </div>
        </div>
      </div>
    </section>

@endsection
