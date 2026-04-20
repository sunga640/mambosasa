@php
    $siteHomeUrl = auth()->check() ? auth()->user()->accountHomeUrl() : route('site.home');
    $sliderImages = $homeHeroSlideUrls ?? [];
    $menuBgImage = (count($sliderImages) > 0) ? $sliderImages[0] : asset('img/menu/bg.png');
@endphp

<div class="menuFullScreen js-menuFullScreen">
    {{-- 1. BACKGROUND IMAGE (Inaonekana upande wa kushoto pekee sasa) --}}
    <div class="menuFullScreen__bg">
        <img src="{{ $menuBgImage }}" alt="bg" style="width:100%; height:100%; object-fit:cover;">
        <div style="position:absolute; inset:0; background: rgba(18, 34, 35, 0.85);"></div>
    </div>

   {{-- 2. CLOSE BUTTON (Sasa ni Nyekundu na Bold) --}}
    <div class="menuFullScreen__close js-menuFullScreen-toggle" style="position: absolute; top: 40px; right: 40px; z-index: 1001; cursor: pointer; display: flex; align-items: center; transition: 0.3s;">
        <span class="text-12 fw-800 letter-2 uppercase mr-10" style="color: #c41e3a;">CLOSE</span>
        <div style="width: 40px; height: 40px; border-radius: 50%; border: 2px solid #c41e3a; display: flex; align-items: center; justify-content: center;">
            <i class="icon-close text-20" style="color: #c41e3a; font-weight: 900;"></i>
        </div>
    </div>

    <div class="split-menu-container">

        {{-- 3. UPANDE WA KUSHOTO (LINKS) --}}
        <div class="menu-side-links">
            <nav class="menuFullScreen-links">
                <div class="menuFullScreen-links__item"><a href="{{ $siteHomeUrl }}">HOME</a></div>
                {{-- <div class="menuFullScreen-links__item"><a href="{{ route('site.pricing') }}">ROOM TYPES</a></div> --}}
                <div class="menuFullScreen-links__item"><a href="{{ route('site.page', ['slug' => 'about']) }}">ABOUT US</a></div>
                <div class="menuFullScreen-links__item"><a href="{{ route('site.page', ['slug' => 'contact']) }}">CONTACT</a></div>
                <div class="menuFullScreen-links__item"><a href="{{ route('site.page', ['slug' => 'faq']) }}">FAQ</a></div>
                <div class="menuFullScreen-links__item"><a href="{{ route('site.page', ['slug' => 'terms']) }}">TERMS</a></div>

                <div class="menuFullScreen-links__item mt-30">
                    <a href="{{ auth()->check() ? auth()->user()->accountHomeUrl() : route('login') }}" style="color: #0099cc !important; font-size: 28px !important;">
                        {{ auth()->check() ? 'DASHBOARD' : 'GUEST LOGIN' }}
                    </a>
                </div>
            </nav>
        </div>

        {{-- 4. UPANDE WA KULIA (TAARIFA - HALF PAGE) --}}
        <div class="menu-side-info">
            <div class="info-content-wrap">
                {{-- Logo & Hotel Name --}}
                <div class="text-center mb-60">
                    @if($siteSettings->headerLogoUrl())
                        <img src="{{ $siteSettings->headerLogoUrl() }}" alt="Logo" style="max-height: 100px; width: auto;">
                    @endif
                    <h2 class="text-32 fw-600 mt-25" style="font-family: 'Cormorant Garamond', serif; color: #1a2b48;">
                        {{ $siteSettings->company_name }}
                    </h2>
                </div>

                {{-- Details --}}
                <div class="mb-40">
                    <div class="info-tag">LOCATION</div>
                    <p class="info-val">{!! nl2br(e($siteSettings->address_line)) !!}</p>
                </div>

                <div class="mb-40">
                    <div class="info-tag">PHONE SUPPORT</div>
                    <p class="info-val fw-600" style="font-size: 24px; color: #1a2b48;">{{ $siteSettings->phone }}</p>
                </div>

                <div class="mb-40">
                    <div class="info-tag">EMAIL ADDRESS</div>
                    <p class="info-val">{{ $siteSettings->email }}</p>
                </div>

                {{-- Socials --}}
                <div class="d-flex x-gap-25 justify-center mt-20">
                    @if($siteSettings->facebook_url) <a href="{{ $siteSettings->facebook_url }}" style="color: #1a2b48;"><i class="icon-facebook text-22"></i></a> @endif
                    @if($siteSettings->instagram_url) <a href="{{ $siteSettings->instagram_url }}" style="color: #1a2b48;"><i class="icon-instagram text-22"></i></a> @endif
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    /* BASIC SETUP */
    .menuFullScreen { position: fixed; inset: 0; z-index: 10000; visibility: hidden; opacity: 0; transition: 0.4s ease; background: #fff; }
    .menuFullScreen.is-active { visibility: visible; opacity: 1; }

    /* SPLIT SCREEN WRAPPER */
    .split-menu-container {
        display: flex;
        width: 100%;
        height: 100vh;
    }

    /* LEFT SIDE (50% - Image Background) */
    .menu-side-links {
        flex: 1;
        display: flex;
        align-items: center;
        padding-left: 8%;
        position: relative;
        z-index: 2;
    }

    /* RIGHT SIDE (50% - Solid Info) */
    .menu-side-info {
        flex: 1;
        background: rgba(248, 249, 250, 0.98); /* Rangi ya cream/nyeupe safi */
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px;
        position: relative;
        z-index: 2;
        border-left: 1px solid rgba(0,0,0,0.05);
    }

    .info-content-wrap { width: 100%; max-width: 500px; }

    /* IMAGE BG LOGIC */
    .menuFullScreen__bg { position: absolute; left: 0; top: 0; width: 50%; height: 100%; z-index: 1; overflow: hidden; }

    /* NAVIGATION STYLE */
    .menuFullScreen-links__item { margin-bottom: 20px; }
    .menuFullScreen-links__item a {
        font-family: 'Cormorant Garamond', serif;
        font-size: clamp(32px, 4vw, 52px);
        font-weight: 600;
        color: #fff !important;
        text-transform: uppercase;
        line-height: 1.1;
        transition: 0.3s;
    }
    .menuFullScreen-links__item a:hover { color: #0099cc !important; padding-left: 15px; }

    /* INFO TAGS STYLE */
    .info-tag { font-size: 12px; font-weight: 800; letter-spacing: 3px; color: #94a3b8; margin-bottom: 8px; text-transform: uppercase; }
    .info-val { font-size: 18px; color: #1a2b48; line-height: 1.6; margin: 0; }

    /* RESPONSIVE (Kwenye Simu inarudi kuwa full info chini ya links) */
    @media (max-width: 1024px) {
        .split-menu-container { flex-direction: column; overflow-y: auto; }
        .menu-side-links { padding: 120px 30px 60px; flex: none; width: 100%; }
        .menu-side-info { flex: none; width: 100%; padding: 60px 30px; }
        .menuFullScreen__bg { width: 100%; height: 60vh; }
        .menuFullScreen__close { background: #fff; padding: 10px 20px; border-radius: 50px; top: 20px; right: 20px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
    }
     /* Close Button Hover Effect */
    .menuFullScreen__close:hover {
        transform: scale(1.1);
        opacity: 0.8;
    }

    /* Inahakikisha neno GUEST LOGIN halipotei */
    .menuFullScreen-links__item a[style*="color: #0099cc"] {
        font-family: 'Jost', sans-serif !important;
        letter-spacing: 1px;
    }
</style>
