@php
    $loginUrl = auth()->check() ? auth()->user()->accountHomeUrl() : route('login');
    $siteHomeUrl = auth()->check() ? auth()->user()->accountHomeUrl() : route('site.home');
    $telHref = $siteSettings->phone ? 'tel:'.preg_replace('/\s+/', '', $siteSettings->phone) : '#';
@endphp

<!-- HEADER START -->
<header class="header -h-90 bg-white js-header site-header-stack">
    {{-- Top Bar (Desktop Only) --}}
    <div class="d-flex items-center bg-light-1 h-40 md:d-none w-full border-bottom-light">
        <div class="container">
            <div class="row justify-between">
                <div class="col-auto">
                    @if ($siteSettings->address_line)
                        <span class="text-13 opacity-70"><i class="icon-map mr-10"></i>{{ $siteSettings->address_line }}</span>
                    @endif
                </div>
                <div class="col-auto d-flex">
                    @if ($siteSettings->phone) <a href="{{ $telHref }}" class="text-13 mr-20"><i class="icon-phone mr-10"></i>{{ $siteSettings->phone }}</a> @endif
                    @if ($siteSettings->email) <a href="mailto:{{ $siteSettings->email }}" class="text-13"><i class="icon-email mr-10"></i>{{ $siteSettings->email }}</a> @endif
                </div>
            </div>
        </div>
    </div>

    <div class="header__container container">
        {{-- LEFT SIDE --}}
        <div class="d-flex items-center" style="flex: 1; gap: 12px;">
            <a href="{{ route('site.search') }}" class="header-circle-icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
            </a>

            {{-- Button ya kufungua Menu ya Simu --}}
            <button type="button" onclick="toggleMobileMenu()" class="hamburg-mobile-btn">
                <i class="icon-menu text-15"></i>
            </button>

            {{-- Links za Desktop pekee --}}
            <nav class="desktop-nav-links">
                <a href="{{ $siteHomeUrl }}">Home</a>
                <a href="{{ route('site.page', ['slug' => 'about']) }}">About</a>
                <a href="{{ route('site.page', ['slug' => 'contact']) }}">Contact</a>

            </nav>
        </div>

       <div class="header__center">
    <a href="{{ $siteHomeUrl }}" class="header-logo-wrap">
        @if ($dashboardSettings->headerLogoUrl())
            <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="Logo">
        @endif
        <span>{{ $siteSettings->company_name ?? 'Mambo Sasa Hotel' }}</span>
    </a>
</div>

        {{-- RIGHT SIDE --}}
        <div class="d-flex justify-end items-center" style="flex: 1; gap: 10px;">
            <a href="{{ $loginUrl }}" class="header-circle-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg>
            </a>
        </div>
    </div>
</header>

<!-- MOBILE SIDE DRAWER MENU -->
<div id="mobileDrawer" class="mobile-side-drawer">
    <div class="drawer-header">
        <span class="drawer-title">MENU</span>
        <button onclick="toggleMobileMenu()" class="drawer-close-btn">&times;</button>
    </div>
    <div class="drawer-body">
        <nav class="mobile-nav-list">
            <a href="{{ $siteHomeUrl }}"><i class="icon-home mr-10"></i> Home</a>
            <a href="{{ route('site.page', ['slug' => 'about']) }}"><i class="icon-notification mr-10"></i> About Us</a>
            <a href="{{ route('site.page', ['slug' => 'contact']) }}"><i class="icon-newsletter mr-10"></i> Contact</a>
            <a href="{{ route('site.page', ['slug' => 'faq']) }}"><i class="icon-clock mr-10"></i> FAQ</a>
            <a href="{{ route('site.page', ['slug' => 'terms']) }}"><i class="icon-clock mr-10"></i> Terms & Conditions</a>

        </nav>
    </div>
</div>
<!-- Overlay background inatokea menu ikifunguka -->
<div id="drawerOverlay" class="drawer-overlay" onclick="toggleMobileMenu()"></div>

<style>
    /* 1. Header & Desktop Links */
    .header-circle-icon {
        display: flex; align-items: center; justify-content: center;
        width: 38px; height: 38px; border-radius: 50%; border: 1px solid rgba(0,0,0,0.1); color: #122223;
    }
    .hamburg-mobile-btn {
        display: none; align-items: center; justify-content: center;
        width: 38px; height: 38px; border: 1px solid rgba(0,0,0,0.1); border-radius: 8px; background: #fff; cursor: pointer;
    }
    .desktop-nav-links { display: flex; align-items: center; gap: 20px; }
    .desktop-nav-links a { font-size: 13px; font-weight: 600; text-transform: uppercase; color: #122223 !important; text-decoration: none; }

    /* 2. Side Drawer (The Menu) */
    .mobile-side-drawer {
        position: fixed; top: 0; left: -300px; /* Hidden by default */
        width: 280px; height: 100%; background: #122223; /* Dark Professional Background */
        z-index: 10000; transition: 0.4s ease;
        box-shadow: 5px 0 15px rgba(0,0,0,0.3);
        display: flex; flex-direction: column;
    }
    .mobile-side-drawer.active { left: 0; } /* Show when active */

    .drawer-header { padding: 20px; display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid rgba(255,255,255,0.1); }
    .drawer-title { color: #fff; font-weight: 700; letter-spacing: 2px; }
    .drawer-close-btn { background: none; border: none; color: #fff; font-size: 30px; cursor: pointer; }

    .drawer-body { padding: 20px; }
    .mobile-nav-list a {
        display: block; color: #fff !important; font-size: 16px; font-weight: 500;
        padding: 15px 0; border-bottom: 1px solid rgba(255,255,255,0.05); text-decoration: none;
    }
    .drawer-login-btn { background: #fff; color: #122223 !important; text-align: center; border-radius: 8px; margin-top: 10px; font-weight: 700 !important; }

    /* 3. Overlay */
    .drawer-overlay {
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 9999; display: none;
    }
    .drawer-overlay.active { display: block; }

    /* 4. Logo Positioning */
    .header__center { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); }
    .header-logo-wrap { display: flex; flex-direction: column; align-items: center; text-decoration: none; }
    .header-logo-wrap img { max-height: 25px; }
    .header-logo-wrap span { font-weight: 700; font-size: 0.95rem; color: #122223; white-space: nowrap; }

    /* Responsive */
    @media (max-width: 1023px) {
        .desktop-nav-links { display: none !important; }
        .hamburg-mobile-btn { display: flex !important; }
    }
    /* Muonekano wa Logo na Jina la Hoteli */
.header-logo-wrap {
    display: flex;
    align-items: center;
    gap: 12px; /* Nafasi kati ya picha ya logo na maandishi */
    text-decoration: none;
    color: #111; /* Rangi ya herufi */
    transition: opacity 0.2s;
}

.header-logo-wrap:hover {
    opacity: 0.8;
}

/* Hapa ndipo tunakuza maandishi na kuweka font */
.header-logo-wrap span {
    font-family: 'Cormorant Garamond', serif; /* Font uliyotaka */
    font-size: 28px; /* Ukubwa wa maandishi (unaweza kuongeza hapa) */
    font-weight: 600; /* Unene wa maandishi */
    line-height: 1.2;
    letter-spacing: 0.02em; /* Nafasi kidogo kati ya herufi ili iwe classic */
    white-space: nowrap; /* Kuzuia jina lisiende mstari wa pili */
}

/* Ukubwa wa picha ya logo (kama ipo) */
.header-logo-wrap img {
    max-height: 45px; /* Inarekebisha picha iendane na maandishi makubwa */
    width: auto;
    object-fit: contain;
}

/* Kurekebisha kwenye simu (Mobile) ili jina lisizidi kioo */
@media (max-width: 600px) {
    .header-logo-wrap span {
        font-size: 20px; /* Ukubwa unapungua kidogo kwenye simu */
    }
    .header-logo-wrap img {
        max-height: 35px;
    }
}
</style>

<script>
    function toggleMobileMenu() {
        const drawer = document.getElementById('mobileDrawer');
        const overlay = document.getElementById('drawerOverlay');
        drawer.classList.toggle('active');
        overlay.classList.toggle('active');

        // Zuia kuscroll huku menu ipo wazi
        if (drawer.classList.contains('active')) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'auto';
        }
    }
</script>
