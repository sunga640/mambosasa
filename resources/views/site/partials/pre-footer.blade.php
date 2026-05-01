@php
    use App\Support\HomeHeroSlides;

    $pfHotel = $siteSettings->hotelDisplayName();
    $pfAccent = $siteSettings->company_name ?? config('app.name');
    $pfHeroBg = HomeHeroSlides::urlForSlideNumber(2);
    $mapQuery = urlencode('Mambosasa Hotel Sinza Dar es Salaam');
@endphp

<section class="site-prefooter-modern" aria-labelledby="site-prefooter-modern-title">
    <div class="site-prefooter-modern__texture"></div>
    <div class="container">
        <div class="site-prefooter-modern__grid">
            <div class="site-prefooter-modern__copy">
                <span class="site-kicker">{{ __('Direct reservations') }}</span>
                <h2 id="site-prefooter-modern-title">{{ __('Plan your stay with a smoother, warmer experience at :hotel.', ['hotel' => $pfHotel]) }}</h2>
                <p>{{ __('Plan your visit with confidence, compare room options, and reach our team directly for reservations, arrival planning, or special guest requests.') }}</p>

                <div class="site-prefooter-modern__actions">
                    <a href="{{ route('site.booking') }}">{{ __('Book your stay') }}</a>
                    <span>{{ $pfAccent }}</span>
                </div>

                <div class="site-prefooter-modern__feature-grid">
                    <article>
                        <strong>{{ __('Rooms & rates') }}</strong>
                        <p>{{ __('Browse categories, compare rates, and reserve directly.') }}</p>
                    </article>
                    <article>
                        <strong>{{ __('Branches') }}</strong>
                        <p>{{ __('Explore our locations, contact details, and stay options before you arrive.') }}</p>
                    </article>
                    <article>
                        <strong>{{ __('Guest support') }}</strong>
                        <p>{{ __('Reach the team anytime for payment or special requests.') }}</p>
                    </article>
                </div>
            </div>

            <div class="site-prefooter-modern__side">
                <div class="site-prefooter-modern__image-card" style="background-image:linear-gradient(180deg, rgba(19,33,30,0.08), rgba(19,33,30,0.7)), url('{{ $pfHeroBg }}');">
                    <div class="site-prefooter-modern__image-card-copy">
                        <span>{{ __('Sinza, Dar es Salaam') }}</span>
                        <h3>{{ __('Find :hotel easily and plan your arrival with confidence.', ['hotel' => $pfHotel]) }}</h3>
                    </div>
                </div>

                <div class="site-prefooter-modern__map-card">
                    <div class="site-prefooter-modern__map-head">
                        <strong>{{ __('Location map') }}</strong>
                        <span>{{ __('Mambosasa Hotel, Sinza') }}</span>
                    </div>
                    <iframe
                        title="{{ __('Mambosasa Hotel map') }}"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps?q={{ $mapQuery }}&output=embed"></iframe>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .site-prefooter-modern {
        position: relative;
        overflow: hidden;
        padding: 4.8rem 0;
        background: linear-gradient(180deg, #f8f3eb 0%, #fff 100%);
    }
    .site-prefooter-modern__texture {
        position: absolute;
        inset: 0;
        opacity: 0.4;
        background:
            radial-gradient(circle at 8% 12%, rgba(184, 149, 93, 0.12), transparent 22%),
            radial-gradient(circle at 92% 80%, rgba(23, 53, 47, 0.08), transparent 24%);
        pointer-events: none;
    }
    .site-prefooter-modern__grid {
        position: relative;
        z-index: 1;
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 430px);
        gap: 1.5rem;
        align-items: stretch;
    }
    .site-prefooter-modern__copy,
    .site-prefooter-modern__map-card,
    .site-prefooter-modern__image-card {
        border-radius: 0;
        border: 1px solid rgba(23, 53, 47, 0.08);
        box-shadow: 0 16px 34px rgba(23, 53, 47, 0.06);
    }
    .site-prefooter-modern__copy {
        background: rgba(255, 255, 255, 0.86);
        padding: 1.5rem;
    }
    .site-prefooter-modern__copy h2 {
        margin: 0.9rem 0 1rem;
        font-size: clamp(2rem, 3.6vw, 3.2rem);
        line-height: 0.98;
        color: #17352f;
    }
    .site-prefooter-modern__copy p {
        max-width: 42rem;
        font-size: 0.9rem;
        color: #5c6b6d;
        line-height: 1.7;
    }
    .site-prefooter-modern__actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 1.2rem;
    }
    .site-prefooter-modern__actions a {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 2.9rem;
        padding: 0.85rem 1.2rem;
        border-radius: 999px;
        background: #17352f;
        color: #fff !important;
        text-decoration: none;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .site-prefooter-modern__actions span {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #8a7656;
    }
    .site-prefooter-modern__feature-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }
    .site-prefooter-modern__feature-grid article {
        padding: 1rem;
        border-radius: 0;
        background: linear-gradient(180deg, #fff 0%, #f7f2e8 100%);
        border: 1px solid rgba(23, 53, 47, 0.07);
        transition: transform 0.28s ease, box-shadow 0.28s ease, border-color 0.28s ease;
    }
    .site-prefooter-modern__feature-grid article:hover {
        transform: translateY(-5px);
        box-shadow: 0 16px 32px rgba(23, 53, 47, 0.08);
        border-color: rgba(23, 53, 47, 0.14);
    }
    .site-prefooter-modern__feature-grid strong {
        display: block;
        margin-bottom: 0.45rem;
        color: #17352f;
        font-size: 0.95rem;
    }
    .site-prefooter-modern__feature-grid p {
        font-size: 0.8rem;
        margin: 0;
    }
    .site-prefooter-modern__side {
        display: grid;
        gap: 1rem;
    }
    .site-prefooter-modern__image-card {
        min-height: 220px;
        background-size: cover;
        background-position: center;
        position: relative;
        overflow: hidden;
    }
    .site-prefooter-modern__image-card-copy {
        position: absolute;
        inset: auto 0 0 0;
        padding: 1.2rem;
        color: #fff;
    }
    .site-prefooter-modern__image-card-copy span {
        display: inline-flex;
        padding: 0.4rem 0.65rem;
        border-radius: 0;
        background: rgba(255,255,255,0.12);
        font-size: 0.62rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .site-prefooter-modern__image-card-copy h3 {
        margin: 0.8rem 0 0;
        font-size: clamp(1.35rem, 2.2vw, 1.9rem);
        line-height: 1.05;
        color: #fff;
    }
    .site-prefooter-modern__map-card {
        background: #fff;
        padding: 1rem;
    }
    .site-prefooter-modern__map-head {
        display: flex;
        flex-direction: column;
        gap: 0.3rem;
        margin-bottom: 0.8rem;
    }
    .site-prefooter-modern__map-head strong {
        color: #17352f;
        font-size: 1rem;
    }
    .site-prefooter-modern__map-head span {
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #8a7656;
    }
    .site-prefooter-modern__map-card iframe {
        width: 100%;
        height: 250px;
        border: none;
        border-radius: 0;
    }
    @media (max-width: 1199px) {
        .site-prefooter-modern__grid,
        .site-prefooter-modern__feature-grid {
            grid-template-columns: 1fr;
        }
    }
    @media (max-width: 767px) {
        .site-prefooter-modern {
            padding: 3.6rem 0;
        }
    }
</style>
