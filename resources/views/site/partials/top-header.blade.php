@php
    $loginUrl = auth()->check() ? auth()->user()->accountHomeUrl() : route('login');
    $siteHomeUrl = auth()->check() ? auth()->user()->accountHomeUrl() : route('site.home');
    $brandLines = $siteSettings->headerBrandLines();
    $primaryBrand = $brandLines[0] ?? $siteSettings->hotelDisplayName();
    $secondaryBrand = $brandLines[1] ?? __('Guest reservations');
    $guestNavItems = [
        ['label' => __('Home'), 'url' => $siteHomeUrl],
        ['label' => __('Travel Guides'), 'url' => route('site.page', ['slug' => 'about'])],
        ['label' => __('Contact'), 'url' => route('site.page', ['slug' => 'contact'])],
    ];
@endphp

<header class="header bg-white js-header site-header-stack site-guest-header {{ request()->routeIs('site.home') ? 'site-guest-header--overlay' : '' }}">
    <div class="site-guest-header__top">
        <div class="container">
            <div class="site-guest-header__topbar">
                <a href="{{ $siteHomeUrl }}" class="site-guest-header__identity">
                    @if ($dashboardSettings->headerLogoUrl())
                        <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $siteSettings->hotelDisplayName() }}">
                    @endif
                    <span>
                        <strong>{{ $primaryBrand }}</strong>
                        <small>{{ $secondaryBrand }}</small>
                    </span>
                </a>

                <nav class="site-guest-header__desktop-nav">
                    @foreach($guestNavItems as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach
                </nav>

                <div class="site-guest-header__actions">
                    @if($siteSettings->phone)
                        <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="site-guest-header__phone">
                            <i class="icon-phone text-14"></i>
                            <span>{{ $siteSettings->phone }}</span>
                        </a>
                    @endif
                    <a href="{{ $loginUrl }}" class="site-guest-header__account">{{ __('Admin') }}</a>
                </div>
            </div>
        </div>
    </div>

    <div class="site-guest-header__mobile">
        <div class="container">
            <div class="site-guest-header__mobile-row">
                <button type="button" onclick="toggleMobileMenu()" class="site-guest-header__mobile-btn" aria-label="{{ __('Open menu') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2.2"/>
                    </svg>
                </button>
                <a href="{{ $siteHomeUrl }}" class="site-guest-header__mobile-brand">
                    @if ($dashboardSettings->headerLogoUrl())
                        <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $siteSettings->hotelDisplayName() }}">
                    @endif
                    <span>{{ $primaryBrand }}</span>
                </a>
                <a href="{{ $loginUrl }}" class="site-guest-header__mobile-btn" aria-label="{{ __('Admin login') }}">
                    <svg viewBox="0 0 24 24" aria-hidden="true">
                        <path d="M12 12a4 4 0 1 0 0-8 4 4 0 0 0 0 8Z" fill="none" stroke="currentColor" stroke-width="2"/>
                        <path d="M4 20a8 8 0 0 1 16 0" fill="none" stroke="currentColor" stroke-linecap="round" stroke-width="2"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</header>

<div id="mobileDrawer" class="site-guest-drawer">
    <div class="site-guest-drawer__panel">
        <div class="site-guest-drawer__head">
            <span>{{ $siteSettings->hotelDisplayName() }}</span>
            <button onclick="toggleMobileMenu()" class="site-guest-drawer__close" aria-label="{{ __('Close menu') }}">&times;</button>
        </div>
        <nav class="site-guest-drawer__links">
            @foreach($guestNavItems as $item)
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endforeach
            <a href="{{ route('site.page', ['slug' => 'faq']) }}">{{ __('FAQ') }}</a>
            <a href="{{ $loginUrl }}">{{ __('Admin') }}</a>
        </nav>
    </div>
</div>
<div id="drawerOverlay" class="site-guest-drawer__overlay" onclick="toggleMobileMenu()"></div>

<style>
    .site-guest-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
        z-index: 10040;
        background: #ffffff;
        box-shadow: 0 12px 34px rgba(15, 23, 42, 0.08);
    }
    .site-guest-header.is-sticky,
    .site-guest-header.is-sticky.header,
    .site-guest-header.is-sticky.bg-white {
        background: #000000 !important;
        box-shadow: 0 12px 34px rgba(0, 0, 0, 0.22) !important;
    }
    .site-guest-header.site-guest-header--overlay,
    .site-guest-header.site-guest-header--overlay.header,
    .site-guest-header.site-guest-header--overlay.bg-white {
        background: transparent !important;
        box-shadow: none !important;
    }
    .site-guest-header__top {
        background: #ffffff;
        border-bottom: 1px solid rgba(23, 53, 47, 0.1);
    }
    .site-guest-header.is-sticky .site-guest-header__top {
        background: #000000 !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    }
    .site-guest-header--overlay .site-guest-header__top {
        background: linear-gradient(180deg, rgba(9, 21, 24, 0.26) 0%, rgba(9, 21, 24, 0.08) 72%, rgba(9, 21, 24, 0) 100%) !important;
        border-bottom: none !important;
    }
    .site-guest-header__topbar {
        min-height: 7rem;
        display: grid;
        grid-template-columns: minmax(250px, 1fr) auto auto;
        gap: 1.4rem;
        align-items: center;
    }
    .site-guest-header__identity {
        display: inline-flex;
        align-items: center;
        gap: 0.9rem;
        text-decoration: none;
        color: #17352f;
    }
    .site-guest-header__identity img {
        width: auto;
        max-height: 3.75rem;
        object-fit: contain;
        flex-shrink: 0;
        border-radius: 0.65rem;
    }
    .site-guest-header__identity strong {
        display: block;
        font-size: 1.06rem;
        font-weight: 600;
        line-height: 1.1;
        color: #17352f;
    }
    .site-guest-header__identity small {
        display: block;
        margin-top: 0.3rem;
        color: #64748b;
        font-size: 0.78rem;
        letter-spacing: 0.04em;
    }
    .site-guest-header__desktop-nav {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1.5rem;
    }
    .site-guest-header__desktop-nav a,
    .site-guest-header__account {
        color: #17352f;
        text-decoration: none;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }
    .site-guest-header.is-sticky .site-guest-header__identity,
    .site-guest-header.is-sticky .site-guest-header__desktop-nav a,
    .site-guest-header.is-sticky .site-guest-header__account,
    .site-guest-header.is-sticky .site-guest-header__phone,
    .site-guest-header.is-sticky .site-guest-header__identity strong,
    .site-guest-header.is-sticky .site-guest-header__identity small {
        color: #ffffff !important;
    }
    .site-guest-header--overlay .site-guest-header__identity,
    .site-guest-header--overlay .site-guest-header__desktop-nav a,
    .site-guest-header--overlay .site-guest-header__account,
    .site-guest-header--overlay .site-guest-header__phone,
    .site-guest-header--overlay .site-guest-header__identity strong,
    .site-guest-header--overlay .site-guest-header__identity small {
        color: #ffffff;
        text-shadow: 0 2px 14px rgba(0, 0, 0, 0.26);
    }
    .site-guest-header--overlay .site-guest-header__identity small {
        opacity: 0.82;
    }
    .site-guest-header__actions {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
    }
    .site-guest-header__phone {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        color: #1e3a8a;
        text-decoration: none;
        font-size: 0.88rem;
        font-weight: 600;
    }
    .site-guest-header__account {
        padding: 0.85rem 1rem;
        background: #ffffff;
        border: 1px solid rgba(23, 53, 47, 0.14);
    }
    .site-guest-header.is-sticky .site-guest-header__account {
        background: rgba(255, 255, 255, 0.06) !important;
        border-color: rgba(255, 255, 255, 0.18) !important;
    }
    .site-guest-header--overlay .site-guest-header__account {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.24);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .site-guest-header__mobile {
        display: none;
    }
    .site-guest-drawer {
        position: fixed;
        inset: 0 auto 0 0;
        width: min(19rem, 86vw);
        transform: translateX(-104%);
        transition: transform 0.28s ease;
        z-index: 10060;
    }
    .site-guest-drawer.active { transform: translateX(0); }
    .site-guest-drawer__panel {
        height: 100%;
        background: linear-gradient(180deg, #1f2028 0%, #17352f 100%);
        color: #fff;
        padding: 1.35rem;
        box-shadow: 16px 0 48px rgba(0, 0, 0, 0.28);
    }
    .site-guest-drawer__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.12);
        padding-bottom: 0.9rem;
        margin-bottom: 0.9rem;
    }
    .site-guest-drawer__close {
        border: none;
        background: transparent;
        color: #fff;
        font-size: 1.8rem;
        cursor: pointer;
    }
    .site-guest-drawer__links {
        display: grid;
        gap: 0.2rem;
    }
    .site-guest-drawer__links a {
        color: rgba(255, 255, 255, 0.94);
        text-decoration: none;
        padding: 0.9rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        font-size: 0.84rem;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        font-weight: 700;
    }
    .site-guest-drawer__overlay {
        position: fixed;
        inset: 0;
        background: rgba(7, 17, 20, 0.48);
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.28s ease, visibility 0.28s ease;
        z-index: 10055;
    }
    .site-guest-drawer__overlay.active {
        opacity: 1;
        visibility: visible;
    }
    .site-guest-header__mobile-row {
        min-height: 5.25rem;
        display: grid;
        grid-template-columns: 3.35rem 1fr 3.35rem;
        gap: 0.8rem;
        align-items: center;
    }
    .site-guest-header__mobile-brand {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        text-decoration: none;
        color: #17352f;
        min-width: 0;
    }
    .site-guest-header__mobile-brand img {
        max-height: 2.7rem;
        width: auto;
        object-fit: contain;
        border-radius: 0.55rem;
    }
    .site-guest-header__mobile-brand span {
        font-family: 'Cormorant Garamond', Georgia, serif;
        font-size: clamp(0.98rem, 4vw, 1.24rem);
        font-weight: 700;
        line-height: 1;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .site-guest-header__mobile-btn {
        width: 3.1rem;
        height: 3.1rem;
        border-radius: 999px;
        border: 1px solid rgba(30, 58, 138, 0.24);
        background: #173b74;
        color: #ffffff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        box-shadow: 0 10px 24px rgba(23, 59, 116, 0.18);
        font-size: 1.15rem;
    }
    .site-guest-header__mobile-btn i {
        font-size: 1.15rem !important;
        line-height: 1;
    }
    .site-guest-header__mobile-btn svg {
        width: 1.32rem;
        height: 1.32rem;
        display: block;
    }
    @media (max-width: 1023px) {
        .site-guest-header__top {
            display: none;
        }
        .site-guest-header__mobile {
            display: block;
            background: #fff;
        }
        .site-guest-header.is-sticky .site-guest-header__mobile {
            background: #000000 !important;
        }
        .site-guest-header--overlay .site-guest-header__mobile {
            background: linear-gradient(180deg, rgba(9, 21, 24, 0.34) 0%, rgba(9, 21, 24, 0.12) 72%, rgba(9, 21, 24, 0) 100%) !important;
        }
        .site-guest-header__mobile-brand {
            justify-content: flex-start;
        }
        .site-guest-header--overlay .site-guest-header__mobile-brand,
        .site-guest-header--overlay .site-guest-header__mobile-brand span {
            color: #fff;
        }
        .site-guest-header--overlay .site-guest-header__mobile-btn {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-color: rgba(255, 255, 255, 0.22);
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.18);
        }
        .site-guest-header.is-sticky .site-guest-header__mobile-btn,
        .site-guest-header.is-sticky .site-guest-header__mobile-brand,
        .site-guest-header.is-sticky .site-guest-header__mobile-brand span {
            color: #ffffff !important;
        }
        .site-guest-header.is-sticky .site-guest-header__mobile-btn {
            background: rgba(255, 255, 255, 0.08) !important;
            border-color: rgba(255, 255, 255, 0.18) !important;
            box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24) !important;
        }
    }
    @media (max-width: 575px) {
        .site-guest-header__mobile-row {
            min-height: 5rem;
            grid-template-columns: 3.2rem minmax(0, 1fr) 3.2rem;
            gap: 0.55rem;
        }
        .site-guest-header__mobile-brand img {
            max-height: 2.5rem;
        }
        .site-guest-header__mobile-brand span {
            font-size: clamp(0.9rem, 3.9vw, 1.08rem);
        }
        .site-guest-header__mobile-btn {
            width: 3rem;
            height: 3rem;
        }
    }
</style>

<script>
    function toggleMobileMenu() {
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('drawerOverlay');
        drawer.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = drawer.classList.contains('active') ? 'hidden' : 'auto';
    }
</script>
