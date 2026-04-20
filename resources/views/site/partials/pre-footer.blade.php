@php
    use App\Support\HomeHeroSlides;
    $pfHotel = $siteSettings->hotelDisplayName();
    $pfAccent = $siteSettings->company_name ?? config('app.name');
    $pfHeroBg = HomeHeroSlides::urlForSlideNumber(2);
@endphp

<section class="prefooter-v2" style="margin-top:-8px;background-image: linear-gradient(145deg, rgba(15,23,42,0.78) 0%, rgba(30,58,95,0.68) 45%, rgba(18,34,35,0.76) 100%), url('{{ $pfHeroBg }}'); background-size: cover; background-position: center; background-repeat: no-repeat;" aria-labelledby="prefooter-v2-title">
    <div class="prefooter-v2__inner">
        <div class="container">
            <div class="prefooter-v2__top row y-gap-30 justify-between items-end">
                <div class="col-lg-7">
                    <p class="prefooter-v2__eyebrow">{{ __('Direct reservations') }}</p>
                    <h2 id="prefooter-v2-title" class="prefooter-v2__title">
                        {{ __('Experience :hotel without the friction.', ['hotel' => $pfHotel]) }}
                    </h2>
                    <p class="prefooter-v2__lead">
                        {{ __('Real-time availability, clear rates in TZS, and a single place to manage your stay from booking to checkout.') }}
                    </p>
                </div>
                <div class="col-lg-auto">
                    <a href="{{ route('site.booking') }}" class="prefooter-v2__cta-main">
                        {{ __('Book your stay') }}
                        <i class="fa fa-arrow-right ml-10"></i>
                    </a>
                </div>
            </div>

            <div class="prefooter-v2__cards row y-gap-24 pt-40">
                <div class="col-md-4">
                    <div class="prefooter-v2__card">
                        <div class="prefooter-v2__card-icon" aria-hidden="true">
                            <i class="fa fa-bed"></i>
                        </div>
                        <h3 class="prefooter-v2__card-title">{{ __('Rooms & rates') }}</h3>
                        <p class="prefooter-v2__card-text">{{ __('Browse categories, compare nights, and pick the room that fits your trip.') }}</p>
                        <a href="{{ route('site.page', ['slug' => 'pricing']) }}" class="prefooter-v2__card-link">{{ __('View pricing') }} →</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="prefooter-v2__card">
                        <div class="prefooter-v2__card-icon" aria-hidden="true">
                            <i class="fa fa-map-marker-alt"></i>
                        </div>
                        <h3 class="prefooter-v2__card-title">{{ __('Our properties') }}</h3>
                        <p class="prefooter-v2__card-text">{{ __('See branches, locations, and what makes each property distinct.') }}</p>
                        <a href="{{ route('site.branches') }}" class="prefooter-v2__card-link">{{ __('Explore locations') }} →</a>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="prefooter-v2__card">
                        <div class="prefooter-v2__card-icon" aria-hidden="true">
                            <i class="fa fa-concierge-bell"></i>
                        </div>
                        <h3 class="prefooter-v2__card-title">{{ __('We are here to help') }}</h3>
                        <p class="prefooter-v2__card-text">{{ __('Questions about payment, groups, or special requests? Reach the team anytime.') }}</p>
                        <a href="{{ route('site.page', ['slug' => 'contact']) }}" class="prefooter-v2__card-link">{{ __('Contact us') }} →</a>
                    </div>
                </div>
            </div>

            <div class="prefooter-v2__strip row y-gap-20 justify-between items-center pt-50 mt-40">
                <div class="col-auto">
                    <span class="prefooter-v2__strip-label">{{ $pfAccent }}</span>
                </div>
                <div class="col-lg text-lg-right">
                    <span class="prefooter-v2__strip-note">{{ __('Member dashboard, guest portal, and secure payment options — all in one system.') }}</span>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .prefooter-v2 {
        position: relative;
        margin-top: 0;
        color: #f8fafc;
        overflow: hidden;
    }
    .prefooter-v2__inner {
        position: relative;
        padding: clamp(3rem, 5vw, 4.5rem) 0 clamp(2.5rem, 4vw, 3.25rem);
    }
    .prefooter-v2__inner::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0.12;
        background:
            radial-gradient(ellipse 50% 40% at 15% 20%, rgba(37, 99, 235, 0.22) 0%, transparent 55%),
            radial-gradient(ellipse 45% 35% at 90% 80%, rgba(249, 218, 186, 0.12) 0%, transparent 50%);
        pointer-events: none;
    }
    .prefooter-v2 .container {
        position: relative;
        z-index: 1;
    }
    .prefooter-v2__eyebrow {
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.65);
        margin: 0 0 0.75rem;
    }
    .prefooter-v2__title {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(1.85rem, 3.8vw, 2.75rem);
        font-weight: 600;
        line-height: 1.15;
        margin: 0 0 1rem;
        color: #fff;
        max-width: 20ch;
    }
    .prefooter-v2__lead {
        font-size: 1.0625rem;
        line-height: 1.65;
        color: rgba(248, 250, 252, 0.82);
        margin: 0;
        max-width: 42ch;
    }
    .prefooter-v2__cta-main {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.95rem 1.75rem;
        border-radius: 999px;
        font-family: 'Jost', system-ui, sans-serif;
        font-size: 0.9375rem;
        font-weight: 700;
        letter-spacing: 0.03em;
        text-decoration: none;
        color: #0f172a !important;
        background: #fff;
        box-shadow: 0 12px 32px rgba(0, 0, 0, 0.2);
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }
    .prefooter-v2__cta-main:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 40px rgba(0, 0, 0, 0.28);
        color: #0f172a !important;
    }
    .prefooter-v2__card {
        height: 100%;
        padding: 1.5rem 1.35rem;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.06);
        border: 1px solid rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(8px);
        transition: border-color 0.2s ease, background 0.2s ease;
    }
    .prefooter-v2__card:hover {
        background: rgba(255, 255, 255, 0.09);
        border-color: rgba(255, 255, 255, 0.16);
    }
    .prefooter-v2__card-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(37, 99, 235, 0.25);
        color: #93c5fd;
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }
    .prefooter-v2__card-title {
        font-family: 'Jost', system-ui, sans-serif;
        font-size: 1.1rem;
        font-weight: 700;
        margin: 0 0 0.5rem;
        color: #fff;
    }
    .prefooter-v2__card-text {
        font-size: 0.9rem;
        line-height: 1.55;
        color: rgba(248, 250, 252, 0.78);
        margin: 0 0 1rem;
    }
    .prefooter-v2__card-link {
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        color: #93c5fd !important;
        text-decoration: none;
        border-bottom: 1px solid rgba(147, 197, 253, 0.45);
        padding-bottom: 2px;
    }
    .prefooter-v2__card-link:hover {
        color: #bfdbfe !important;
        border-bottom-color: #bfdbfe;
    }
    .prefooter-v2__strip {
        border-top: 1px solid rgba(255, 255, 255, 0.12);
    }
    .prefooter-v2__strip-label {
        font-size: 0.8125rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: rgba(248, 250, 252, 0.55);
    }
    .prefooter-v2__strip-note {
        font-size: 0.875rem;
        color: rgba(248, 250, 252, 0.65);
        line-height: 1.5;
    }
    @media (max-width: 991px) {
        .prefooter-v2__title { max-width: none; }
        .prefooter-v2__lead { max-width: none; }
        .prefooter-v2__top .col-lg-auto { width: 100%; }
        .prefooter-v2__cta-main { width: 100%; }
        .prefooter-v2__strip .col-lg { text-align: left !important; }
    }
</style>
