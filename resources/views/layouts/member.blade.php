<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account-bridge.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>@yield('title', __('Account')) — {{ $dashboardSettings->hotelDisplayName() }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    @include('partials.footer-theme-overrides')
    {{-- CSS Styles zako zote zilizopita zibaki hapa --}}
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --dash-body-bg: #f5efe5;
            --dash-header-h: 68px;
            --dash-crumb-h: 46px;
            --dash-footer-h: 56px;
            --dash-sidebar-w: 372px;
            --dash-brand-forest: #17352f;
            --dash-brand-gold: #b8955d;
            --dash-brand-cream: #f7f2e8;
        }
        .dash-body { margin: 0; min-height: 100vh; height: 100vh; overflow: hidden; background: var(--dash-body-bg); font-family: 'Manrope', system-ui, sans-serif; font-size: 14px; }
        .dash-header-bar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h);
            background: rgba(255,255,255,0.96); border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 300;
            display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box;
        }
        .dash-menu-btn {
            display: none; align-items: center; justify-content: center; width: 40px; height: 40px;
            border: 1px solid #ddd; border-radius: 10px; background: #fff; cursor: pointer; flex-shrink: 0;
        }
        .dash-header-bar__brand { font-size: 1.125rem; font-weight: 700; color: #111; text-decoration: none; flex-shrink: 0; }
        .dash-header-bar__brand:hover { color: #c41e3a; }
        .dash-header-brand-wrap { display:inline-flex; align-items:center; gap:.75rem; min-width:0; }
        .dash-header-brand-wrap img { width:42px; height:42px; object-fit:contain; flex-shrink:0; }
        .dash-header-brand-text { min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .dash-header-spacer { display: none; flex: 1; min-width: 0; }
        .dash-header-search {
            flex: 1; min-width: 0; max-width: 420px; margin: 0 auto;
            display: flex; align-items: center; gap: 0.35rem;
            border: 1px solid #d8d8d8; border-radius: 10px; padding: 0 0.5rem 0 0.65rem; background: #f8f9fa;
        }
        .dash-header-search input[type="search"] {
            flex: 1; min-width: 0; border: none; background: transparent; padding: 0.55rem 0; font: inherit; outline: none;
        }
        .dash-header-search button {
            border: none; background: #111; color: #fff; width: 36px; height: 32px; border-radius: 8px;
            cursor: pointer; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .dash-mobile-search-btn {
            display: none; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; flex-shrink: 0;
        }
        .dash-header-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; margin-left: auto; }
        .dash-header-alert-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; position: relative; text-decoration: none;
            background: #fff;
        }
        .dash-header-alert-btn:hover { border-color: #bbb; background: #fafafa; }
        .dash-header-logout-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; text-decoration: none; background: #fff;
            cursor: pointer; padding: 0;
        }
        .dash-header-logout-btn:hover { border-color: #bbb; background: #fafafa; }
        .dash-theme-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; text-decoration: none; background: #fff;
            cursor: pointer;
        }
        .dash-theme-btn:hover { border-color: #bbb; background: #fafafa; }
        .dash-user-menu { position: relative; }
        .dash-user-menu__trigger {
            width: 42px; height: 42px; border-radius: 50%; border: 1px solid #ddd; background: #111; color: #fff;
            font-weight: 700; cursor: pointer; display: flex; align-items: center; justify-content: center;
            font-size: 0.95rem; padding: 0;
        }
        .dash-user-menu__panel {
            position: absolute; right: 0; top: calc(100% + 8px); min-width: 220px;
            background: #f8f7f4; border: 1px solid #e5e5e5; border-radius: 12px; box-shadow: 0 8px 28px rgba(0,0,0,.12);
            padding: 0.5rem 0; z-index: 320;
        }
        .dash-user-menu__who { padding: 0.65rem 1rem; font-size: 0.85rem; color: #444; border-bottom: 1px solid #eee; line-height: 1.4; }
        .dash-user-menu__who strong { display: block; color: #111; font-size: 0.95rem; }
        .dash-user-menu__panel a { display: block; padding: 0.55rem 1rem; color: #333; text-decoration: none; font-size: 0.9rem; }
        .dash-user-menu__panel a:hover { background: #f5f5f5; }
        .flatpickr-calendar {
            border-radius: 0;
            border: 1px solid rgba(214, 221, 231, 0.98);
            box-shadow: 0 18px 42px rgba(18, 34, 35, 0.14);
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
        .flatpickr-day { border-radius: 0; }
        .flatpickr-day.today { border-color: #0f172a; }
        .flatpickr-day.flatpickr-disabled,
        .flatpickr-day.flatpickr-disabled:hover {
            background: #fee2e2;
            border-color: #fecaca;
            color: #b91c1c;
            opacity: 1;
            cursor: not-allowed;
        }
        .flatpickr-day:not(.flatpickr-disabled):not(.prevMonthDay):not(.nextMonthDay):not(.selected):not(.startRange):not(.endRange):not(.inRange) {
            background: #dcfce7;
            border-color: #dcfce7;
            color: #166534;
        }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #1d4ed8; border-color: #1d4ed8; }
        .dash-user-menu__panel form { margin: 0; padding: 0.35rem 0.75rem 0.5rem; border-top: 1px solid #eee; margin-top: 0.25rem; }
        .dash-user-menu__panel form button {
            width: 100%; margin-top: 0.35rem; padding: 0.45rem; border-radius: 8px; border: 1px solid #ccc;
            background: #fff; cursor: pointer; font-family: inherit; font-size: 0.9rem;
        }
        .dash-breadcrumb-bar {
            position: fixed; top: var(--dash-header-h); left: 0; right: 0; height: var(--dash-crumb-h);
            background: #efe7d8; border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 290;
            display: flex; align-items: center; padding: 0 1.25rem; font-size: 0.875rem; color: #444; box-sizing: border-box;
        }
        .dash-breadcrumb-bar a { color: #333; text-decoration: none; }
        .dash-breadcrumb-bar .sep { margin: 0 0.35rem; color: #888; user-select: none; }
        .dash-breadcrumb-bar .current { color: #111; font-weight: 500; }
        .dash-sidebar-backdrop {
            display: none; position: fixed;
            top: calc(var(--dash-header-h) + var(--dash-crumb-h)); left: 0; right: 0; bottom: var(--dash-footer-h);
            background: rgba(0,0,0,.38); z-index: 250; opacity: 0; pointer-events: none;
        }
        .dash-workspace {
            position: fixed;
            top: calc(var(--dash-header-h) + var(--dash-crumb-h)); left: 0; right: 0; bottom: var(--dash-footer-h);
            display: flex; min-height: 0;
        }
        .dash-sidebar-outer {
            width: var(--dash-sidebar-w); flex-shrink: 0; background: var(--dash-body-bg);
            padding: 1rem; box-sizing: border-box; min-height: 0; display: flex; flex-direction: column;
        }
        .dash-sidebar-card {
            background: rgba(255,255,255,0.92); border-radius: 12px; border: 1px solid rgba(23,53,47,0.08);
            box-shadow: 0 1px 2px rgba(0,0,0,.04); flex: 1; min-height: 0; display: flex; flex-direction: column;
        }
        .dash-nav-heading {
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; color: #777;
            padding: 0.65rem 1rem 0.3rem; background: #f3f4f6; border-bottom: 1px solid #ececec; text-transform: uppercase;
        }
        .dash-nav-heading:first-child { border-radius: 12px 12px 0 0; }
        .dash-sidebar-nav-scroll { flex: 1; overflow-y: auto; min-height: 0; padding: 0.35rem 0 0.85rem; }
        .dash-nav-link {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 1rem; margin: 0.12rem 0.5rem;
            color: #333; text-decoration: none; font-size: 0.9rem; border-radius: 8px;
        }
        .dash-nav-link:hover { background: #f4f4f5; }
        .dash-nav-link.is-active { color: var(--dash-brand-forest); font-weight: 600; background: rgba(184,149,93,0.18); }
        .dash-nav-ico { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.75; }
        .dash-nav-link.is-active .dash-nav-ico { opacity: 1; stroke: #c41e3a; }
        .dash-content-outer { flex: 1; min-width: 0; min-height: 0; display: flex; flex-direction: column; background: var(--dash-body-bg); padding: 1rem 1rem 0; box-sizing: border-box; position: relative; }
        .dash-content-scroll { flex: 1; overflow-y: auto; min-height: 0; -webkit-overflow-scrolling: touch; }
        .dash-content-card {
            background: rgba(255,255,255,0.94); border-radius: 12px; border: 1px solid rgba(23,53,47,0.08);
            padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(0,0,0,.04); min-height: min-content; overflow-x: auto;
        }
        .dash-page-preloader {
            position: absolute; inset: 1rem 1rem 0 0; z-index: 40; display: flex; align-items: center; justify-content: center;
            background: rgba(247, 242, 232, 0.92); backdrop-filter: blur(4px); transition: opacity 0.28s ease, visibility 0.28s ease;
        }
        .dash-page-preloader.is-hidden { opacity: 0; visibility: hidden; pointer-events: none; }
        .dash-page-preloader__panel { display: grid; gap: .85rem; justify-items: center; text-align: center; }
        .dash-page-preloader__spinner { width: 2.8rem; height: 2.8rem; border: 2px solid rgba(23,53,47,.14); border-top-color: #17352f; animation: dash-preloader-spin .9s linear infinite; }
        .dash-page-preloader__label { color: #17352f; font-size: .76rem; font-weight: 700; letter-spacing: .18em; text-transform: uppercase; }
        @keyframes dash-preloader-spin { to { transform: rotate(360deg); } }
        .dash-site-footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: var(--dash-footer-h);
            background: #fff; border-top: 1px solid #dadce0; display: flex; align-items: center; justify-content: center;
            font-size: 0.875rem; color: #666; z-index: 280; padding: 0 1rem; box-sizing: border-box;
        }
        html[data-dashboard-theme="dark"] .dash-body { background: #0f172a !important; color: #e2e8f0; }
        html[data-dashboard-theme="dark"] .dash-header-bar,
        html[data-dashboard-theme="dark"] .dash-breadcrumb-bar,
        html[data-dashboard-theme="dark"] .dash-site-footer,
        html[data-dashboard-theme="dark"] .dash-content-card,
        html[data-dashboard-theme="dark"] .dash-sidebar-card { background: #111827 !important; color: #e5e7eb !important; border-color: #334155 !important; }
        html[data-dashboard-theme="dark"] .dash-nav-link { color: #e5e7eb; }
        html[data-dashboard-theme="dark"] .dash-nav-link:hover { background: #1f2937; }
        html[data-dashboard-theme="dark"] .dash-nav-link.is-active { background: #312e81; color: #eef2ff; }
        html[data-dashboard-theme="dark"] .dash-theme-btn,
        html[data-dashboard-theme="dark"] .dash-header-alert-btn,
        html[data-dashboard-theme="dark"] .dash-mobile-search-btn { background: #1f2937 !important; color: #e5e7eb !important; border-color: #475569 !important; }
        .dash-content-card [style*="font-size:22px; font-weight:600"],
        .dash-content-card [style*="font-size:24px; font-weight:700"],
        .dash-content-card [style*="font-size:24px; font-weight:800"],
        .dash-content-card [style*="font-size:26px; font-weight:800"],
        .dash-content-card [style*="font-size:28px; font-weight:600"],
        .dash-content-card .work-value,
        .dash-content-card .k-stat strong { color: #ffffff !important; }
        .dash-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; font-family: inherit; cursor: pointer; text-decoration: none; border: none; }
        .dash-btn--primary { background: #111; color: #fff; }
        .dash-btn--primary:hover { background: #333; color: #fff; }
        .dash-body,
        .dash-content-card { font-size: 13px !important; }
        .dash-header-bar__brand { font-size: 1rem !important; }
        .dash-breadcrumb-bar { font-size: 0.8rem !important; }
        .dash-nav-link,
        .dash-user-menu__panel a,
        .dash-user-menu__panel form button,
        .dash-header-search input[type="search"] { font-size: 0.82rem !important; }
        .dash-btn { font-size: 0.82rem !important; padding: 0.45rem 0.85rem !important; }
        .dash-content-card { padding: 1.2rem 1.35rem !important; }
        .admin-table th, .admin-table td { font-size: 0.84rem !important; padding: 0.58rem 0.45rem !important; }
        .dash-content-card .text-30 { font-size: 1.55rem !important; }
        .dash-content-card .text-24 { font-size: 1.25rem !important; }
        .dash-content-card .text-20 { font-size: 1.08rem !important; }
        .dash-content-card .text-18 { font-size: 0.98rem !important; }
        .dash-content-card .text-15 { font-size: 0.84rem !important; }
        .dash-content-card .text-14 { font-size: 0.8rem !important; }
        .dash-content-card .text-13,
        .dash-content-card .text-12 { font-size: 0.75rem !important; }
        .dash-content-card [style*="font-size:28px"] { font-size: 1.45rem !important; }
        .dash-content-card [style*="font-size:26px"] { font-size: 1.32rem !important; }
        .dash-content-card [style*="font-size:24px"] { font-size: 1.2rem !important; }
        .dash-content-card [style*="font-size:22px"] { font-size: 1.08rem !important; }
        .dash-content-card [style*="font-size:16px"] { font-size: 0.88rem !important; }
        .dash-content-card [style*="font-size:15px"] { font-size: 0.84rem !important; }
        .dash-content-card [style*="font-size:14px"] { font-size: 0.8rem !important; }
        .dash-content-card div[style*="border-radius"],
        .dash-content-card a[style*="border-radius"],
        .dash-content-card button[style*="border-radius"],
        .dash-content-card img[style*="border-radius"],
        .dash-content-card video[style*="border-radius"],
        .dash-content-card label[style*="border-radius"],
        .dash-content-card span[style*="border-radius"] {
            border-radius: 10px !important;
        }
        @media (max-width: 1023px) {
            .dash-menu-btn { display: flex; }
            .dash-header-spacer { display: block; }
            .dash-header-search { display: none; }
            .dash-mobile-search-btn { display: flex; }
            .dash-sidebar-backdrop { display: block; }
            .dash-sidebar-outer {
                position: fixed; top: calc(var(--dash-header-h) + var(--dash-crumb-h)); left: 0; bottom: var(--dash-footer-h);
                width: min(var(--dash-sidebar-w), 92vw); z-index: 260; transform: translateX(-100%); transition: transform 0.22s ease;
                padding: 0.75rem;
            }
            .dash-sidebar-outer.is-open { transform: translateX(0); box-shadow: 6px 0 32px rgba(0,0,0,.15); }
            .dash-content-outer { width: 100%; }
        }
        @media (max-width: 767px) {
            .dash-header-bar { padding: 0 .6rem; gap: .45rem; }
            .dash-header-actions { gap: .35rem; }
            .dash-header-branch-picker { display: none !important; }
            .dash-content-outer { padding: .65rem .65rem 0; }
            .dash-content-card { padding: 1rem; }
            .admin-table { display: block; overflow-x: auto; white-space: nowrap; }
        }
        @media (min-width: 1024px) {
            .dash-sidebar-backdrop { display: none !important; }
        }

        /* ... CSS yako ya awali ... */
        [x-cloak] { display: none !important; }
        :root { --dash-body-bg: #eef0f3; --dash-header-h: 68px; --dash-crumb-h: 46px; --dash-footer-h: 56px; --dash-sidebar-w: 340px; }
        .dash-body { margin: 0; min-height: 100vh; height: 100vh; overflow: hidden; background: var(--dash-body-bg); font-family: 'Manrope', system-ui, sans-serif; font-size: 14px; }
        .dash-header-bar { position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h); background: #fff; border-bottom: 1px solid #e0e0e0; z-index: 300; display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box; }
        .dash-nav-link.is-active { color: var(--dash-brand-forest); font-weight: 600; background: rgba(184,149,93,0.18); }
        /* Nimeongeza hii kwa ajili ya kutofautisha maeneo */
        .role-badge { font-size: 10px; background: #eee; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 700; color: #555; }
        /* CSS zingine zote zilizobaki... */
        :root {
            --dash-body-bg: #1a1c1f !important;
            --dash-crumb-h: 0px !important;
            --dash-footer-h: 0px !important;
            --dash-sidebar-w: 280px !important;
            --dash-brand-forest: #38bdf8 !important;
            --dash-brand-gold: #7dd3fc !important;
            --dash-brand-cream: #23262b !important;
        }
        .dash-breadcrumb-bar { display:none !important; }
        .dash-header-bar,
        .dash-breadcrumb-bar { background:#20242a !important; border-color:rgba(125,211,252,.12) !important; color:#cbd5e1 !important; }
        .dash-header-bar__brand,
        .dash-breadcrumb-bar a { color:#7dd3fc !important; }
        .dash-menu-btn,
        .dash-mobile-search-btn,
        .dash-header-alert-btn,
        .dash-theme-btn { background:#23262b !important; color:#e5e7eb !important; border-color:rgba(125,211,252,.18) !important; border-radius:12px !important; }
        .dash-header-search { background:#23262b !important; border-color:rgba(125,211,252,.18) !important; border-radius:12px !important; }
        .dash-header-search input[type="search"] { color:#e5e7eb !important; }
        .dash-sidebar-card,
        .dash-content-card { background:#23262b !important; border:1px solid rgba(125,211,252,.14) !important; border-radius:12px !important; color:#e5e7eb !important; }
        .dash-sidebar-outer { padding:1.1rem !important; }
        .dash-nav-heading { display:none !important; }
        .dash-sidebar-nav-scroll { padding:.25rem 0 .45rem !important; }
        .dash-nav-link { color:#e5e7eb !important; border:1px solid rgba(125,211,252,.12) !important; background:#2b3038 !important; border-radius:8px !important; margin:0 .55rem .32rem !important; padding:.52rem .62rem !important; min-height:40px !important; }
        .dash-nav-link:hover { background:rgba(56,189,248,.1) !important; border-color:rgba(125,211,252,.24) !important; }
        .dash-nav-link.is-active { color:#7dd3fc !important; background:rgba(56,189,248,.12) !important; border-color:rgba(125,211,252,.28) !important; }
        .dash-nav-ico { width:15px !important; height:15px !important; }
        .dash-site-footer { display:none !important; }
        .member-dash-hero,
        .guest-surface-card,
        .stat-card-work { border-radius:12px !important; }
        @media (max-width: 1023px) {
            .dash-sidebar-outer { width:min(84vw, 320px) !important; padding:0 !important; }
            .dash-sidebar-card { border-radius:0 !important; }
            .dash-nav-link { margin:0 .5rem .32rem !important; padding:.52rem .62rem !important; }
        }
    </style>
</head>
<body class="dash-body" data-skip-settings-preloader="{{ request()->routeIs('admin.settings.*') ? '1' : '0' }}">
@php
    $dashCompany = $dashboardSettings->hotelDisplayName();
    $u = auth()->user();
    $userInitial = mb_strtoupper(mb_substr($u->name, 0, 1));

    /**
     * 1. KUREKEBISHA JSON ERROR:
     * Tunahakikisha tunapata jina la Role kama String pekee.
     */
    $roleRaw = $u->role;
    $roleName = strtolower(is_string($roleRaw) ? $roleRaw : ($roleRaw->name ?? 'guest'));

    /**
     * 2. LOGIC ZA MENU (Mamlaka)
     */
    $isAdmin = in_array($roleName, ['admin', 'director']);
    $isMaintenance = in_array($roleName, ['maintainance', 'maintenance', 'housekeeping', 'admin']);
    $isReception = in_array($roleName, ['receptionist', 'manager', 'admin']);
    // Guest ni yule ambaye sio staff wa aina yoyote au ni mteja tu
    $isGuest = in_array($roleName, ['guest', 'customer', 'member']) || (!$isAdmin && !$isMaintenance && !$isReception);

    // Counter ya payments za mteja (Kwa Guest pekee)
    $memberPendingCount = 0;
    if ($isGuest || $isAdmin) {
        $memberPendingCount = \App\Models\Booking::query()
            ->where(function ($q) use ($u) { $q->where('user_id', $u->id)->orWhere('email', $u->email); })
            ->where('status', \App\Enums\BookingStatus::PendingPayment)->count();
    }
@endphp

<div class="dash-root" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <header class="dash-header-bar">
        <button type="button" class="dash-menu-btn" @click="sidebarOpen = !sidebarOpen">
            <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('dashboard') }}" class="dash-header-bar__brand">
            <span class="dash-header-brand-wrap">
                <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $dashCompany }}">
                <span class="dash-header-brand-text">{{ $dashCompany }}</span>
            </span>
        </a>
        <div class="dash-header-spacer"></div>

        {{-- SEARCH: Inatokea kwa Staff pekee --}}
        @if(!$isGuest)
        <form method="GET" action="{{ route('admin.search') }}" class="dash-header-search">
            <input type="search" name="q" placeholder="{{ __('Search...') }}">
            <button type="submit"><svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg></button>
        </form>
        @endif

        <div class="dash-header-actions">
            @if($isGuest || $isAdmin)
                <a href="{{ route('member.notifications.index') }}" class="dash-header-alert-btn" style="display:inline-flex;text-decoration:none;" title="{{ __('Notifications') }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($memberPendingCount > 0)
                        <span style="position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;padding:0 5px;border-radius:999px;background:#c41e3a;color:#fff;font-size:10px;font-weight:800;line-height:18px;text-align:center;">{{ $memberPendingCount > 99 ? '99+' : $memberPendingCount }}</span>
                    @endif
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="dash-header-logout-btn" title="{{ __('Log out') }}" aria-label="{{ __('Log out') }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9"/><path stroke-width="2" d="M13 20H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
                </button>
            </form>
            <div class="dash-user-menu" x-data="{ open: false }">
                <button type="button" class="dash-user-menu__trigger" @click="open = !open">{{ $userInitial }}</button>
                <div class="dash-user-menu__panel" x-show="open" x-cloak @click.outside="open = false">
                    <div class="dash-user-menu__who">
                        <strong>{{ $u->name }}</strong>
                        <span style="font-size:10px; text-transform:uppercase; font-weight:700; color:#c41e3a;">{{ $roleName }}</span>
                    </div>
                    <a href="{{ route('profile.edit') }}">{{ __('My Profile') }}</a>
                    @if(!$isGuest)
                        <a href="{{ route('admin.dashboard') }}">{{ __('Admin Panel') }}</a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <div class="dash-breadcrumb-bar">
        <a href="{{ $u->accountHomeUrl() }}">{{ __('Home') }}</a>
        <span class="sep">/</span>
        <span class="current">@yield('title')</span>
    </div>
    <div class="dash-sidebar-backdrop" x-show="sidebarOpen" x-cloak x-transition.opacity @click="sidebarOpen = false"></div>

    <div class="dash-workspace">
        <aside class="dash-sidebar-outer" :class="{ 'is-open': sidebarOpen }">
            <div class="dash-sidebar-card">
                {{-- Side Profile --}}
                <div style="padding:1.25rem 1rem; border-bottom:1px solid var(--brand-theme-border, rgba(125,211,252,0.18)); background:linear-gradient(180deg, rgba(18,23,32,0.96) 0%, rgba(28,35,46,0.94) 100%); border-radius:12px 12px 0 0;">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div style="width:44px;height:44px;border-radius:50%;background:linear-gradient(135deg, #0f172a, #1e3a5f);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;box-shadow:0 10px 24px rgba(14,165,233,.18);">{{ $userInitial }}</div>
                        <div style="min-width:0;">
                            <div style="font-weight:600;font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;color:#eef2ff;">{{ $u->name }}</div>
                            <div style="font-size:11px; color:var(--brand-theme-heading, #7dd3fc); font-weight:700; text-transform:uppercase; letter-spacing:.08em;">{{ $roleName }}</div>
                        </div>
                    </div>
                </div>

                <div class="dash-sidebar-nav-scroll">
                    <div class="dash-nav-heading">{{ __('Main') }}</div>
                    <a href="{{ route('dashboard') }}" class="dash-nav-link {{ request()->routeIs('dashboard') ? 'is-active' : '' }}">
                        <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        {{ __('Dashboard') }}
                    </a>

                    {{-- MENU YA MTEJA (GUEST) TU --}}
                    @if($isGuest || $isAdmin)
                        <div class="dash-nav-heading">{{ __('My Personal') }}</div>
                        <a href="{{ route('bookings.index') }}" class="dash-nav-link {{ request()->routeIs('bookings.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14"/></svg>
                            {{ __('My Bookings') }}
                            @if($memberPendingCount > 0) <span style="background:#c41e3a;color:#fff;font-size:10px;padding:2px 6px;border-radius:10px;margin-left:auto;">{{ $memberPendingCount }}</span> @endif
                        </a>
                        <a href="{{ route('member.invoices.index') }}" class="dash-nav-link {{ request()->routeIs('member.invoices.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>
                            {{ __('My Invoices') }}
                        </a>
                        @if($dashboardSettings->restaurantIntegrationConfigured())
                            <a href="{{ route('member.restaurant.launch') }}" class="dash-nav-link {{ request()->routeIs('member.restaurant.launch') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M8 3v8M16 3v8M5 21h14M7 11h10M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4"/></svg>
                                {{ __('Restaurant') }}
                            </a>
                        @endif
                    @endif

                    {{-- MENU YA MAINTENANCE TU --}}
                    @if($isMaintenance)
                        <div class="dash-nav-heading">{{ __('Operations') }}</div>
                        <a href="{{ route('admin.maintenance.index') }}" class="dash-nav-link {{ request()->routeIs('admin.maintenance.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                            {{ __('Maintenance Jobs') }}
                        </a>
                        <a href="{{ route('admin.rooms.index') }}" class="dash-nav-link {{ request()->routeIs('admin.rooms.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ __('Room Status') }}
                        </a>
                    @endif

                    {{-- MENU YA ADMIN/MANAGEMENT --}}
                    @if($isAdmin)
                        <div class="dash-nav-heading">{{ __('Administration') }}</div>
                        <a href="{{ route('admin.reports.index') }}" class="dash-nav-link">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 19V5M8 19v-6m4 6V9"/></svg>
                            {{ __('Financial Reports') }}
                        </a>
                        <a href="{{ route('admin.settings.edit') }}" class="dash-nav-link">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M12 15a3 3 0 100-6"/></svg>
                            {{ __('System Settings') }}
                        </a>
                        <a href="{{ route('admin.users.index') }}" class="dash-nav-link">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5"/></svg>
                            {{ __('User Accounts') }}
                        </a>
                    @endif
                </div>
            </div>
        </aside>

        <div class="dash-content-outer">
            @include('partials.dashboard-preloader')
            <div class="dash-content-scroll">
                <div class="dash-content-card">
                    @hasSection('header')
                        <div style="margin-bottom:1.5rem; padding-bottom:1rem; border-bottom:1px solid #eee;">
                            @yield('header')
                        </div>
                    @endif
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="dash-site-footer">
    {{ $dashboardSettings->copyright_text ?? ('© '.date('Y').' '.$dashCompany) }}
</footer>

@include('partials.swal')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@stack('scripts')
<script>
    (function () {
        var preloader = document.querySelector('[data-dash-preloader]');
        if (!preloader) return;
        function hidePreloader() { preloader.classList.add('is-hidden'); }
        hidePreloader();
        document.addEventListener('DOMContentLoaded', function () { setTimeout(hidePreloader, 350); });
        window.addEventListener('load', function () { setTimeout(hidePreloader, 180); });
        setTimeout(hidePreloader, 2500);
    })();

    (function () {
        document.querySelectorAll('.dash-sidebar-nav-scroll a').forEach(function (link) {
            link.addEventListener('click', function () {
                var root = document.querySelector('.dash-root');
                if (window.innerWidth <= 1023 && root && root.__x) {
                    root.__x.$data.sidebarOpen = false;
                }
            });
        });
    })();
</script>
</body>
</html>
