<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-x="html" data-x-toggle="html-overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    <style>
        :root {
            --site-brand-blue: #1e4d6b;
            --site-pricing-muted: #5c6b6d;
            --site-dropdown-bg: #f8f7f4;
            /* Dark overlay on top hero (readability) — matches newsletter feel */
            --site-hero-overlay-gradient: linear-gradient(
                180deg,
                rgba(18, 34, 35, 0.86) 0%,
                rgba(18, 34, 35, 0.36) 48%,
                rgba(18, 34, 35, 0.64) 100%
            );
        }
        /* Section 1 / inner page heroes: gradient sits above image + wipe animation (separate from ::after) */
        .pageHero .pageHero__bg {
            overflow: hidden;
        }
        .pageHero .pageHero__reveal {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
        }
        .pageHero .pageHero__reveal > img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            position: relative;
            z-index: 1;
        }
        .flatpickr-calendar {
            border-radius: 14px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            box-shadow: 0 14px 36px rgba(18, 34, 35, 0.12);
            font-family: inherit;
            overflow: hidden;
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
        }
        .flatpickr-months {
            background: linear-gradient(120deg, rgba(30, 77, 107, 0.12) 0%, rgba(0, 153, 204, 0.1) 50%, rgba(18, 34, 35, 0.08) 100%) !important;
            border-bottom: 1px solid rgba(226, 232, 240, 0.9);
        }
        .flatpickr-months .flatpickr-month { height: 46px; }
        .flatpickr-current-month { font-size: 1rem; padding-top: 8px; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px !important; height: 14px !important; }
        .flatpickr-day { border-radius: 8px; }
        .flatpickr-day.today { border-color: #0f172a; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: linear-gradient(135deg, #1e4d6b 0%, #1d4ed8 100%) !important;
            border-color: transparent !important;
            color: #fff !important;
        }
        html[data-site-theme="light"] .site-main-with-fixed-header,
        html[data-site-theme="light"] body {
            background-color: #fff !important;
            color: inherit !important;
        }
        html[data-site-theme="light"] .header,
        html[data-site-theme="light"] .header.-h-90,
        html[data-site-theme="light"] .site-header-stack {
            background-color: #fff !important;
        }
        html[data-site-theme="light"] .header-circle-icon,
        html[data-site-theme="light"] .hamburg-mobile-btn {
            background: #fff !important;
            color: #122223 !important;
            border-color: rgba(0,0,0,0.1) !important;
        }
        html[data-site-theme="light"] .desktop-nav-links a,
        html[data-site-theme="light"] .header-logo-wrap span,
        html[data-site-theme="light"] .header-hotel-name {
            color: #122223 !important;
        }
        @media (prefers-color-scheme: light) {
            html[data-site-theme="system"] .site-main-with-fixed-header,
            html[data-site-theme="system"] body {
                background-color: #fff !important;
                color: inherit !important;
            }
            html[data-site-theme="system"] .header,
            html[data-site-theme="system"] .header.-h-90,
            html[data-site-theme="system"] .site-header-stack {
                background-color: #fff !important;
            }
            html[data-site-theme="system"] .header-circle-icon,
            html[data-site-theme="system"] .hamburg-mobile-btn {
                background: #fff !important;
                color: #122223 !important;
                border-color: rgba(0,0,0,0.1) !important;
            }
            html[data-site-theme="system"] .desktop-nav-links a,
            html[data-site-theme="system"] .header-logo-wrap span,
            html[data-site-theme="system"] .header-hotel-name {
                color: #122223 !important;
            }
        }
        /* Site theme: dark + system (prefers dark) */
        html[data-site-theme="dark"] .site-main-with-fixed-header,
        html[data-site-theme="dark"] body {
            background-color: #0f172a !important;
            color: #e2e8f0 !important;
        }
        html[data-site-theme="dark"] .header,
        html[data-site-theme="dark"] .header.-h-90,
        html[data-site-theme="dark"] .site-header-stack {
            background-color: #0f172a !important;
            border-color: rgba(148, 163, 184, 0.2) !important;
        }
        html[data-site-theme="dark"] .header-circle-icon,
        html[data-site-theme="dark"] .hamburg-mobile-btn {
            border-color: rgba(148, 163, 184, 0.35) !important;
            color: #e2e8f0 !important;
            background: rgba(15, 23, 42, 0.5) !important;
        }
        html[data-site-theme="dark"] .desktop-nav-links a,
        html[data-site-theme="dark"] .header-logo-wrap span,
        html[data-site-theme="dark"] .header-hotel-name {
            color: #f1f5f9 !important;
        }
        html[data-site-theme="dark"] .footer,
        html[data-site-theme="dark"] .footer.-type-2 {
            background: #020617 !important;
            color: #cbd5e1 !important;
        }
        @media (prefers-color-scheme: dark) {
            html[data-site-theme="system"] .site-main-with-fixed-header,
            html[data-site-theme="system"] body {
                background-color: #0f172a !important;
                color: #e2e8f0 !important;
            }
            html[data-site-theme="system"] .header,
            html[data-site-theme="system"] .header.-h-90,
            html[data-site-theme="system"] .site-header-stack {
                background-color: #0f172a !important;
                border-color: rgba(148, 163, 184, 0.2) !important;
            }
            html[data-site-theme="system"] .header-circle-icon,
            html[data-site-theme="system"] .hamburg-mobile-btn {
                border-color: rgba(148, 163, 184, 0.35) !important;
                color: #e2e8f0 !important;
                background: rgba(15, 23, 42, 0.5) !important;
            }
            html[data-site-theme="system"] .desktop-nav-links a,
            html[data-site-theme="system"] .header-logo-wrap span,
            html[data-site-theme="system"] .header-hotel-name {
                color: #f1f5f9 !important;
            }
            html[data-site-theme="system"] .footer,
            html[data-site-theme="system"] .footer.-type-2 {
                background: #020617 !important;
                color: #cbd5e1 !important;
            }
        }
        .pageHero .pageHero__image-overlay {
            position: absolute;
            inset: 0;
            z-index: 25;
            pointer-events: none;
            background: var(--site-hero-overlay-gradient);
        }
        /* Home hero (slider) — same overlay on each slide background */
        .hero.-type-8 .hero__bg {
            overflow: hidden;
        }
        .hero.-type-8 .hero__bg::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 2;
            pointer-events: none;
            background: var(--site-hero-overlay-gradient);
        }
        .hero.-type-8 .hero__bg > img {
            position: relative;
            z-index: 1;
        }
        /* Dropdown panels (nav + hero search) — cream background */
        .header .desktopNav .desktopNavSubnav__content {
            background-color: var(--site-dropdown-bg) !important;
            border: 1px solid rgba(18, 34, 35, 0.08);
            box-shadow: 0 16px 48px rgba(18, 34, 35, 0.12);
        }
        .header .desktopNav .desktopNavSubnav a {
            color: var(--color-accent-1) !important;
        }
        .header .desktopNav .desktopNavSubnav a:hover {
            color: var(--site-brand-blue) !important;
        }
        .searchForm.-type-1 .searchFormItemDropdown__container {
            background-color: var(--site-dropdown-bg) !important;
            border: 1px solid rgba(18, 34, 35, 0.06);
        }
        .site-newsletter {
            position: relative;
            overflow: hidden;
            background-color: #122223;
            background-size: cover;
            background-position: center;
        }
        .site-newsletter__overlay {
            position: absolute;
            inset: 0;
            background: var(--site-hero-overlay-gradient);
            pointer-events: none;
        }
        .site-newsletter .container {
            position: relative;
            z-index: 1;
        }
        .site-newsletter__icon {
            color: rgba(255, 255, 255, 0.95);
        }
        .site-newsletter__title {
            color: #fff;
        }
        .site-newsletter__form {
            max-width: 32rem;
            margin-left: auto;
            margin-right: auto;
        }
        .site-newsletter__form .site-newsletter__input-wrap {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            gap: 0.65rem;
            background: rgba(255, 255, 255, 0.12);
            border: 1px solid rgba(255, 255, 255, 0.28);
            border-radius: 16px;
            padding: 0.35rem 0.35rem 0.35rem 1rem;
            backdrop-filter: blur(8px);
        }
        .site-newsletter__form input[type="email"] {
            flex: 1;
            min-width: 200px;
            border: none !important;
            background: transparent !important;
            color: #fff !important;
            font-size: 1rem;
            padding: 0.75rem 0.25rem;
            outline: none;
        }
        .site-newsletter__form input[type="email"]::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }
        .site-newsletter__submit {
            flex-shrink: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 52px;
            height: 52px;
            border: none;
            border-radius: 12px;
            background: var(--color-accent-2);
            color: var(--color-accent-1);
            cursor: pointer;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .site-newsletter__submit:hover {
            transform: scale(1.04);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
        }
        .site-newsletter__flash {
            font-size: 0.9375rem;
            margin: 0 0 1rem;
            padding: 0.65rem 1rem;
            border-radius: 12px;
            text-align: center;
        }
        .site-newsletter__flash--ok {
            background: rgba(255, 255, 255, 0.18);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, 0.25);
        }
        .site-newsletter__flash--err {
            background: rgba(198, 40, 40, 0.25);
            color: #fff;
            border: 1px solid rgba(255, 200, 200, 0.35);
        }
        /* Top bar + nav row: .header is flex-row by default — stack vertically */
        .site-header-stack.header {
            flex-direction: column !important;
            align-items: stretch !important;
            height: auto !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            margin: 0 !important;
            z-index: 10050 !important;
        }
        .site-header-stack.header.-h-90 {
            height: auto !important;
        }
        /* Theme hides header on scroll down — keep ours visible */
        .site-header-stack.header.is-hidden-on-scroll {
            transform: none !important;
        }
        .site-header-stack.header.is-sticky {
            opacity: 1 !important;
            -webkit-backdrop-filter: none !important;
            backdrop-filter: none !important;
        }
        .site-header-stack.header.is-sticky .header__container {
            padding-top: 0 !important;
            padding-bottom: 0 !important;
        }
        .site-header-stack > .header__container {
            position: relative !important;
            width: 100% !important;
            min-height: 90px;
        }
        @media (max-width: 575px) {
            .site-header-stack > .header__container {
                min-height: 74px;
            }
        }
        .site-header-stack .header__center {
            position: absolute !important;
            left: 50% !important;
            top: 50% !important;
            transform: translate(-50%, -50%) !important;
            z-index: 2;
        }
        .site-header-stack .header__center a.header__brand {
            display: flex;
        }
        .site-header-left-cluster {
            display: flex;
            align-items: center;
            flex-wrap: nowrap;
        }
        .site-header-search-left {
            flex-shrink: 0;
        }
        .header__brand-line {
            display: block;
            line-height: 1.15;
        }
        .header__brand-line--primary {
            font-family: 'Cormorant Garamond', Georgia, 'Times New Roman', serif;
            font-weight: 600;
            font-size: clamp(1.35rem, 2.8vw, 2.15rem);
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }
        .header__brand-line--muted {
            font-family: 'Jost', system-ui, sans-serif;
            font-size: 0.8125rem;
            font-weight: 500;
            color: #6b7280;
            margin-top: 0.2rem;
            letter-spacing: 0.06em;
        }
        .header__brand-line--accent {
            font-family: 'Jost', system-ui, sans-serif;
            font-size: clamp(0.95rem, 1.6vw, 1.125rem);
            font-weight: 700;
            color: #1e4d6b;
            margin-top: 0.25rem;
            letter-spacing: 0.02em;
        }
        .header__brand-line--primary:only-of-type {
            text-transform: none;
            font-size: clamp(1.25rem, 2.5vw, 1.85rem);
            letter-spacing: 0.02em;
        }
        /* Logo Styling */
.header-logo-img {
    max-height: 30px;
    margin-bottom: 2px;
}

.header-hotel-name {
    color: #122223;
    font-weight: 700;
    font-size: 0.95rem;
    white-space: nowrap;
}

/* Fix for Mobile Overlap */
@media (max-width: 1023px) {
    /* Ficha nav links kabisa kwenye mobile */
    nav.lg:d-flex {
        display: none !important;
    }

    /* Hakikisha button ya Hamburg inaonekana */
    .js-menuFullScreen-toggle {
        display: flex !important;
    }

    /* Punguza ukubwa wa maandishi ya logo ili yasigongane na icons */
    .header-hotel-name {
        font-size: 0.85rem;
    }
    .header-logo-img {
        max-height: 25px;
    }
}

/* Desktop Fix */
@media (min-width: 1024px) {
    .js-menuFullScreen-toggle {
        display: none !important;
    }
    nav.lg:d-flex {
        display: flex !important;
    }
}

/* Kuhakikisha logo inabaki katikati bila kusukuma vingine */
.site-header-stack .header__center {
    position: absolute !important;
    left: 50% !important;
    top: 50% !important;
    transform: translate(-50%, -50%) !important;
    z-index: 2;
    width: auto;
}/* --- HEADER STYLES --- */
.header-circle-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 50%;
    border: 1px solid rgba(0,0,0,0.1);
    color: #122223;
}

.hamburg-mobile-btn {
    display: none; /* Default hide */
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border: 1px solid rgba(0,0,0,0.1);
    border-radius: 8px;
    background: #fff;
    cursor: pointer;
    color: #122223;
}

.desktop-nav-links {
    display: flex;
    align-items: center;
    gap: 20px;
}

.desktop-nav-links a {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: #122223 !important; /* Inahakikisha rangi inaonekana */
    text-decoration: none;
}

/* Dropdown styling */
.desktop-nav-dropdown { position: relative; }
.desktop-nav-dropdown-content {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background: #fff;
    min-width: 150px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    padding: 10px 0;
    z-index: 100;
}
.desktop-nav-dropdown:hover .desktop-nav-dropdown-content { display: block; }
.desktop-nav-dropdown-content a {
    display: block;
    padding: 8px 20px;
    text-transform: none;
}

/* Logo Fix */
.header-logo-wrap {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-decoration: none;
}
.header-logo-wrap img { max-height: 30px; margin-bottom: 2px; }
.header-logo-wrap span {
    font-weight: 700;
    font-size: 1.1rem;
    color: #122223;
    white-space: nowrap;
}

/* --- FULLSCREEN MENU FIX (Kwa Simu) --- */
/* Hii inahakikisha links za kwenye menu ya simu zinaonekana */
.menuFullScreen .menuFullScreen__nav a {
    color: #122223 !important; /* Rangi nyeusi badala ya nyeupe */
    opacity: 1 !important;
    visibility: visible !important;
}

/* --- RESPONSIVE LOGIC --- */
@media (max-width: 1023px) {
    .desktop-nav-links {
        display: none !important; /* Ficha links kwenye simu */
    }
    .hamburg-mobile-btn {
        display: flex !important; /* Onyesha Hamburg kwenye simu */
    }
    .header-logo-wrap span {
        font-size: 0.9rem;
    }
}

@media (min-width: 1024px) {
    .hamburg-mobile-btn {
        display: none !important;
    }
    .desktop-nav-links {
        display: flex !important;
    }
}
        /* Booking search bar — pill / capsule */
        .site-booking-search-pill.searchForm {
            background: rgba(18, 34, 35, 0.94) !important;
            border-radius: 999px !important;
            box-shadow: 0 10px 36px rgba(0, 0, 0, 0.22);
            border: 1px solid rgba(255, 255, 255, 0.08);
            padding: 0.2rem 0.35rem 0.2rem 0.85rem;
            backdrop-filter: blur(8px);
        }
        .site-booking-search-pill .searchForm__form {
            align-items: stretch;
        }
        .site-booking-search-pill .searchFormItem {
            border-right: 1px solid rgba(255, 255, 255, 0.18);
        }
        .site-booking-search-pill .searchFormItem:last-of-type {
            border-right: none;
        }
        .site-booking-search-pill .searchFormItem__button {
            background: transparent !important;
            color: #fff !important;
            min-height: 52px;
        }
        .site-booking-search-pill .searchFormItem__button i {
            color: rgba(255, 255, 255, 0.85);
        }
        .site-booking-search-pill .searchForm__button {
            padding-left: 0.35rem;
            padding-right: 0.35rem;
        }
        .site-booking-search-pill .searchForm__button .button {
            font-weight: 700;
            letter-spacing: 0.08em;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.15);
        }
        /* Home hero compact + keep search card from covering text */
        body .hero.-type-8 {
            height: 360px !important;
            min-height: 360px !important;
        }
        body .hero.-type-8 .hero__slider,
        body .hero.-type-8 .swiper-wrapper,
        body .hero.-type-8 .swiper-slide,
        body .hero.-type-8 .hero__slide,
        body .hero.-type-8 .hero__bg {
            height: 100% !important;
            min-height: 100% !important;
        }
        body .hero.-type-8 .hero__title {
            font-size: clamp(2.1rem, 5.6vw, 4rem) !important;
            line-height: 1.08 !important;
            font-family: 'Jost', system-ui, sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: 0.015em !important;
        }
        body .hero.-type-8 .hero__content {
            padding-top: 56px !important;
            padding-bottom: 30px !important;
        }
        body .hero.-type-8 .hero__filter {
            display: none !important;
        }
        @media (max-width: 991px) {
            body .hero.-type-8 {
                height: 330px !important;
                min-height: 330px !important;
            }
            body .hero.-type-8 .hero__content {
                padding-top: 52px !important;
                padding-bottom: 24px !important;
            }
            .site-booking-search-pill.searchForm {
                max-width: calc(100vw - 24px) !important;
            }
        }
        @media (max-width: 767px) {
            body .hero.-type-8 {
                height: 300px !important;
                min-height: 300px !important;
            }
            body .hero.-type-8 .hero__title {
                font-size: clamp(1.8rem, 8.5vw, 2.8rem) !important;
                font-family: 'Jost', system-ui, sans-serif !important;
                font-weight: 700 !important;
                letter-spacing: 0.015em !important;
            }
            body .hero.-type-8 .hero__content {
                padding-top: 44px !important;
                padding-bottom: 18px !important;
            }
            .site-booking-search-pill.searchForm {
                padding: 0.2rem 0.25rem 0.2rem 0.75rem;
            }
            .site-booking-search-pill .searchForm__form {
                width: calc(100% - 56px);
            }
        }
        .site-inline-search-pill {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
            gap: 0;
            background: rgba(18, 34, 35, 0.94);
            border-radius: 999px;
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 28px rgba(0, 0, 0, 0.12);
            padding: 0.25rem 0.35rem 0.25rem 1rem;
            max-width: 100%;
        }
        .site-inline-search-pill input[type="search"] {
            flex: 1;
            min-width: 160px;
            border: none !important;
            background: transparent !important;
            color: #fff !important;
            padding: 0.65rem 0.5rem 0.65rem 0 !important;
        }
        .site-inline-search-pill input[type="search"]::placeholder {
            color: rgba(255, 255, 255, 0.55);
        }
        .site-inline-search-pill .site-inline-search-pill__go {
            flex-shrink: 0;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: none;
            background: #fff;
            color: #122223;
            font-weight: 700;
            font-size: 0.8rem;
            letter-spacing: 0.06em;
            cursor: pointer;
            align-self: center;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .site-inline-search-pill .site-inline-search-pill__go:hover {
            transform: scale(1.04);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
        }
        a.btn-site-book,
        button.btn-site-book {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.35rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            text-decoration: none;
            background: #122223;
            color: #fff !important;
            border: 1px solid #122223;
            cursor: pointer;
            font-family: inherit;
            transition: background 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        a.btn-site-book:hover,
        button.btn-site-book:hover {
            background: #1a3334;
            transform: translateY(-1px);
            box-shadow: 0 6px 18px rgba(18, 34, 35, 0.28);
        }
        a.btn-site-cart,
        button.btn-site-cart {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.55rem 1.2rem;
            border-radius: 999px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.07em;
            text-transform: uppercase;
            text-decoration: none;
            background: #fff;
            color: #122223 !important;
            border: 1px solid rgba(18, 34, 35, 0.35);
            cursor: pointer;
            font-family: inherit;
            transition: border-color 0.15s ease, transform 0.15s ease, box-shadow 0.15s ease;
        }
        a.btn-site-cart:hover,
        button.btn-site-cart:hover {
            border-color: #122223;
            transform: translateY(-1px);
            box-shadow: 0 4px 14px rgba(18, 34, 35, 0.12);
        }
        /*
         * Top info bar: visible from 768px up (md:d-none hides it on smaller viewports).
         * Offset main so hero clears the fixed header.
         */
        main.site-main-with-fixed-header {
            padding-top: 74px;
        }
        @media (min-width: 576px) {
            main.site-main-with-fixed-header {
                padding-top: 90px;
            }
        }
        @media (min-width: 768px) {
            main.site-main-with-fixed-header {
                padding-top: calc(40px + 90px);
            }
        }
        .site-pricing-card {
            border-radius: 20px;
            overflow: hidden;
            background: #fff;
            border: 1px solid rgba(18, 34, 35, 0.08);
            box-shadow: 0 8px 28px rgba(18, 34, 35, 0.06);
            height: 100%;
            display: flex;
            flex-direction: column;
            text-align: left;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
        }
        .site-pricing-card,
        .site-pricing-category-card,
        .home-rtc-card,
        .prefooter-v2__card,
        .site-card-hover {
            position: relative;
            overflow: hidden;
        }
        .site-pricing-card::after,
        .site-pricing-category-card::after,
        .home-rtc-card::after,
        .prefooter-v2__card::after,
        .site-card-hover::after {
            content: "";
            position: absolute;
            inset: 0;
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.2s ease;
            background: linear-gradient(130deg, rgba(30, 77, 107, 0.09), rgba(0, 153, 204, 0.06));
        }
        .site-pricing-card:hover::after,
        .site-pricing-category-card:hover::after,
        .home-rtc-card:hover::after,
        .prefooter-v2__card:hover::after,
        .site-card-hover:hover::after {
            opacity: 1;
        }
        .site-pricing-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 40px rgba(18, 34, 35, 0.1);
        }
        .site-pricing-category-card .site-pricing-card {
            box-shadow: none;
        }
        .site-pricing-category-card .site-pricing-card:hover {
            box-shadow: 0 8px 24px rgba(18, 34, 35, 0.08);
        }
        .site-pricing-category-card {
            background: #fff;
            border: 1px solid rgba(18, 34, 35, 0.08);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 14px 44px rgba(18, 34, 35, 0.07);
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .site-pricing-category-card__body {
            padding: 1.5rem 1.5rem 1.75rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            text-align: left;
        }
        .site-pricing-category-card__label {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: var(--site-pricing-muted);
            opacity: 0.85;
            margin: 0 0 0.5rem;
        }
        .site-pricing-category-card__title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.75rem, 3vw, 2.25rem);
            font-weight: 600;
            line-height: 1.15;
            color: var(--color-accent-1);
            margin: 0 0 0.75rem;
        }
        .site-pricing-category-card__desc {
            font-size: 0.9375rem;
            line-height: 1.55;
            color: var(--site-pricing-muted);
            margin: 0 0 1.25rem;
        }
        .site-pricing-category-card__rooms {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            margin-top: auto;
        }
        .site-pricing-category-card__empty {
            font-size: 0.9375rem;
            line-height: 1.5;
            color: var(--site-pricing-muted);
            opacity: 0.88;
            margin: 0;
        }
        .site-pricing-unranked-row {
            margin-top: 3rem !important;
        }
        .site-pricing-card__media {
            aspect-ratio: 16 / 11;
            overflow: hidden;
            background: #eef2f2;
        }
        .site-pricing-card__media img,
        .site-pricing-card__media video {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .site-pricing-card__body {
            padding: 1.5rem 1.5rem 1.35rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        .site-pricing-card__rank {
            font-size: 0.6875rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            padding: 0.35rem 0.65rem;
            border-radius: 999px;
            background: rgba(18, 34, 35, 0.06);
            color: var(--color-accent-1);
            margin-bottom: 0.75rem;
        }
        .site-pricing-card__title {
            font-family: 'Jost', system-ui, sans-serif;
            font-size: 1.25rem;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 0.5rem;
            color: var(--color-accent-1);
        }
        .site-pricing-card__title a {
            color: inherit;
            text-decoration: none;
        }
        .site-pricing-card__title a:hover {
            opacity: 0.88;
        }
        .site-pricing-card__excerpt {
            font-size: 0.875rem;
            line-height: 1.55;
            color: var(--site-pricing-muted);
            margin: 0 0 0.35rem;
        }
        .site-pricing-card__meta {
            font-size: 0.8125rem;
            color: rgba(92, 107, 109, 0.85);
            margin: 0 0 1rem;
        }
        .site-pricing-card__price-row {
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            gap: 0.35rem 0.75rem;
            margin-top: auto;
            padding-top: 1rem;
            margin-bottom: 1rem;
            border-top: 1px solid rgba(18, 34, 35, 0.08);
        }
        .site-pricing-card__amount {
            font-size: 1.65rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            color: var(--color-accent-1);
        }
        .site-pricing-card__per {
            font-size: 0.9375rem;
            font-weight: 600;
            color: var(--site-pricing-muted);
        }
        .site-pricing-card__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            padding: 0.65rem 1.25rem;
            border-radius: 999px;
            font-family: 'Jost', system-ui, sans-serif;
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.04em;
            text-decoration: none;
            border: 1px solid rgba(18, 34, 35, 0.18);
            color: var(--color-accent-1);
            background: #fff;
            transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }
        .site-pricing-card__cta:hover {
            border-color: var(--color-accent-1);
            transform: translateY(-1px);
        }
        /* Pre-footer: white band + cards */
        .site-pre-footer {
            position: relative;
            overflow: hidden;
            padding: clamp(3rem, 6vw, 5rem) 0;
            color: var(--color-accent-1);
            background: #fff;
            border-top: 1px solid rgba(18, 34, 35, 0.06);
        }
        .site-pre-footer__waves {
            pointer-events: none;
            position: absolute;
            inset: 0;
            opacity: 0.35;
            background:
                radial-gradient(ellipse 70% 55% at 8% 90%, rgba(30, 77, 107, 0.06) 0%, transparent 55%),
                radial-gradient(ellipse 60% 45% at 95% 5%, rgba(249, 218, 186, 0.25) 0%, transparent 50%);
        }
        .site-pre-footer .container {
            z-index: 1;
        }
        .site-pre-footer__eyebrow {
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            text-transform: uppercase;
            opacity: 0.65;
            margin-bottom: 0.75rem;
            color: var(--site-pricing-muted);
        }
        .site-pre-footer__title {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(2rem, 4.5vw, 3.25rem);
            font-weight: 600;
            line-height: 1.12;
            margin: 0 0 1.25rem;
            max-width: 22ch;
            color: var(--color-accent-1);
        }
        .site-pre-footer__lead {
            font-size: 1.0625rem;
            line-height: 1.65;
            opacity: 0.92;
            max-width: 36ch;
            margin: 0 0 1.75rem;
            color: var(--site-pricing-muted);
        }
        .site-pre-footer__accent {
            color: var(--site-brand-blue);
            font-weight: 700;
        }
        .site-pre-footer__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.85rem 1.85rem;
            border-radius: 999px;
            font-family: 'Jost', system-ui, sans-serif;
            font-size: 0.9375rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-decoration: none;
            background: var(--color-accent-1);
            color: #fff !important;
            box-shadow: 0 8px 28px rgba(18, 34, 35, 0.18);
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .site-pre-footer__cta:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 36px rgba(18, 34, 35, 0.22);
        }
        .site-pre-footer__glass {
            position: relative;
            height: 100%;
            padding: 1.35rem 1.35rem 1.5rem;
            border-radius: 22px;
            background: #fff;
            border: 1px solid rgba(18, 34, 35, 0.08);
            box-shadow: 0 12px 40px rgba(18, 34, 35, 0.07);
        }
        .site-pre-footer__glass-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1rem;
        }
        .site-pre-footer__glass-thumb {
            width: 4.5rem;
            height: 4.5rem;
            border-radius: 14px;
            overflow: hidden;
            flex-shrink: 0;
            border: 2px solid rgba(18, 34, 35, 0.08);
            box-shadow: 0 6px 20px rgba(18, 34, 35, 0.08);
        }
        .site-pre-footer__glass-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }
        .site-pre-footer__badge {
            flex-shrink: 0;
            font-size: 0.625rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            padding: 0.45rem 0.65rem;
            border-radius: 999px;
            background: var(--color-accent-1);
            color: #fff;
            line-height: 1.2;
            max-width: 7.5rem;
            text-align: center;
        }
        .site-pre-footer__glass-title {
            font-family: 'Jost', system-ui, sans-serif;
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0 0 0.5rem;
            color: var(--color-accent-1);
        }
        .site-pre-footer__glass-text {
            font-size: 0.875rem;
            line-height: 1.55;
            margin: 0 0 1.15rem;
            color: var(--site-pricing-muted);
        }
        .site-pre-footer__glass-btn {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 0;
            font-size: 0.8125rem;
            font-weight: 700;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--site-brand-blue) !important;
            text-decoration: none;
            border-bottom: 2px solid rgba(30, 77, 107, 0.35);
            transition: border-color 0.15s ease, opacity 0.15s ease;
        }
        .site-pre-footer__glass-btn:hover {
            border-bottom-color: var(--color-accent-1);
            opacity: 1;
        }
        .site-about-hero-img {
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 16px 48px rgba(18, 34, 35, 0.12);
        }
        .site-about-hero-img img {
            width: 100%;
            display: block;
            object-fit: cover;
            min-height: 280px;
            max-height: 420px;
        }
        .site-cta-hero-bg {
            position: absolute;
            inset: 0;
            background-size: cover;
            background-position: center;
        }
        .site-cta-hero-bg::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 1;
            background: linear-gradient(0deg, rgba(18, 34, 35, 0.75), rgba(18, 34, 35, 0.45));
        }
        .cta.-type-1 .cta__bg .site-cta-hero-bg {
            z-index: 0;
        }
        [x-cloak] { display: none !important; }
        .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }
        /* Inner pages: shorter first-section heroes */
        body .pageHero.-type-1 {
            height: 380px !important;
            padding-top: 130px !important;
        }
        body .pageHero.-type-2 {
            height: 380px !important;
            padding-top: 130px !important;
        }
        @media (max-width: 767px) {
            body .pageHero.-type-1 {
                padding-top: 110px !important;
                padding-bottom: 36px !important;
            }
            body .pageHero.-type-2 {
                height: 300px !important;
                padding-top: 110px !important;
                padding-bottom: 36px !important;
            }
        }
        /* Global section spacing compact mode (all pages) */
        body .layout-pt-lg { padding-top: 30px !important; }
        body .layout-pb-lg { padding-bottom: 30px !important; }
        body .layout-pt-md { padding-top: 22px !important; }
        body .layout-pb-md { padding-bottom: 22px !important; }
        body .layout-pt-sm { padding-top: 16px !important; }
        body .layout-pb-sm { padding-bottom: 16px !important; }
        /* Reduce big gap after hero on all pages */
        body .pageHero + section.layout-pt-lg,
        body .pageHero + section.layout-pt-md,
        body .pageHero + section[class*="layout-pt-"] {
            padding-top: 24px !important;
        }
        @media (max-width: 767px) {
            body .layout-pt-lg { padding-top: 22px !important; }
            body .layout-pb-lg { padding-bottom: 22px !important; }
            body .layout-pt-md { padding-top: 16px !important; }
            body .layout-pb-md { padding-bottom: 16px !important; }
            body .layout-pt-sm { padding-top: 12px !important; }
            body .layout-pb-sm { padding-bottom: 12px !important; }
            body .pageHero + section.layout-pt-lg,
            body .pageHero + section.layout-pt-md,
            body .pageHero + section[class*="layout-pt-"] {
                padding-top: 16px !important;
            }
        }
        .site-booking-portal-fab {
            position: fixed;
            right: 1.25rem;
            bottom: 1.25rem;
            z-index: 10060;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 58px;
            height: 58px;
            border-radius: 50%;
            background: linear-gradient(145deg, #1e4d6b 0%, #122223 100%);
            color: #fff;
            box-shadow: 0 10px 28px rgba(18, 34, 35, 0.35);
            text-decoration: none;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .site-booking-portal-fab:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 32px rgba(18, 34, 35, 0.42);
            color: #fff;
        }
        .site-booking-portal-fab__inner {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .site-booking-portal-fab__badge {
            position: absolute;
            top: -4px;
            right: -4px;
            min-width: 20px;
            height: 20px;
            padding: 0 6px;
            border-radius: 999px;
            background: #c41e3a;
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            line-height: 20px;
            text-align: center;
            pointer-events: none;
        }
        .menuFullScreen {
            visibility: hidden;
        }
        .menuFullScreen.is-active {
            visibility: visible;
        }

    /* Inahakikisha header inakaa juu ya kila kitu */
    .site-header-stack {
        z-index: 9999 !important;
    }

    /* Inahakikisha button ya Hamburg inabonyezeka */
    .js-menuFullScreen-toggle {
        cursor: pointer !important;
        pointer-events: auto !important;
        position: relative;
        z-index: 10000 !important;
    }

    /* Full screen menu lazima iwe na z-index ya juu zaidi */
    .menuFullScreen {
        z-index: 10001 !important;
        visibility: hidden;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .menuFullScreen.is-active {
        visibility: visible !important;
        opacity: 1 !important;
    }

    /* Inazuia kuscroll ukiwa ndani ya menu */
    .html-overflow-hidden, .html-overflow-hidden body {
        overflow: hidden !important;
        height: 100vh !important;
    }
    </style>

    <title>@hasSection('title')
@yield('title') — {{ $siteSettings->hotelDisplayName() }}
@else
{{ $siteSettings->hotelDisplayName() }}
@endif</title>
    @include('site.partials.seo')
    @stack('head')
</head>
<body>
    @include('partials.page-progress')
    @include('site.partials.menu-fullscreen')
    @include('site.partials.cursor')
    <main class="site-main-with-fixed-header">
        @include('site.partials.top-header')
        @yield('content')
        @include('site.partials.pre-footer')
        @include('site.partials.footer')
    </main>
    @include('site.partials.booking-portal-fab')

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('js/vendors.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>

    <script>
        (function () {
            const menu = document.querySelector('.js-menuFullScreen');
            const toggles = document.querySelectorAll('.js-menuFullScreen-toggle, .js-menuFullScreen-close');
            const html = document.documentElement;
            if (!menu) return;

            const toggle = (e) => {
                if(e) { e.preventDefault(); e.stopPropagation(); }
                const active = menu.classList.toggle('is-active');
                html.classList.toggle('html-overflow-hidden', active);
            };

            toggles.forEach(t => t.addEventListener('click', toggle));
            document.addEventListener('keydown', (e) => { if(e.key === 'Escape' && menu.classList.contains('is-active')) toggle(); });
            menu.querySelectorAll('a[href]').forEach(a => a.addEventListener('click', () => { if(a.getAttribute('href')!=='#') { menu.classList.remove('is-active'); html.classList.remove('html-overflow-hidden'); } }));
        })();
    </script>
    @stack('scripts')
</body>
</html>
