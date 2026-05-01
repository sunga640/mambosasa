<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    @include('partials.footer-theme-overrides')
    <title>@yield('title', __('Reception')) — {{ $dashboardSettings->hotelDisplayName() }}</title>
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
        .dash-root { min-height: 100vh; height: 100vh; position: relative; }

        /* Header Bar */
        .dash-header-bar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h);
            background: rgba(255,255,255,0.96); border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 1000;
            display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box;
        }
        .dash-menu-btn {
            display: none; align-items: center; justify-content: center; width: 40px; height: 40px;
            border: 1px solid #ddd; border-radius: 10px; background: #fff; cursor: pointer; flex-shrink: 0;
        }
        .dash-header-bar__brand {
            font-size: 1.125rem; font-weight: 700; color: #111; text-decoration: none; flex-shrink: 0;
        }
        .dash-header-brand-wrap { display:inline-flex; align-items:center; gap:.75rem; min-width:0; }
        .dash-header-brand-wrap img { width:42px; height:42px; object-fit:contain; flex-shrink:0; }
        .dash-header-brand-text { min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .dash-header-actions { display: flex; align-items: center; gap: 0.5rem; flex-shrink: 0; margin-left: auto; }
        .dash-header-alert-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; position: relative; text-decoration: none;
            background: #fff;
        }
        .dash-header-logout-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; text-decoration: none; background: #fff;
            cursor: pointer; padding: 0;
        }
        .dash-theme-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; text-decoration: none; background: #fff;
            cursor: pointer;
        }
        .dash-theme-btn:hover { border-color: #bbb; background: #fafafa; }

        /* Breadcrumb Bar */
        .dash-breadcrumb-bar {
            position: fixed; top: var(--dash-header-h); left: 0; right: 0; height: var(--dash-crumb-h);
            background: #efe7d8; border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 900;
            display: flex; align-items: center; padding: 0 1.25rem; font-size: 0.875rem; color: #444; box-sizing: border-box;
        }

        /* Workspace Structure */
        .dash-workspace {
            position: fixed;
            top: calc(var(--dash-header-h) + var(--dash-crumb-h)); left: 0; right: 0; bottom: var(--dash-footer-h);
            display: flex; min-height: 0;
        }

        .dash-sidebar-backdrop {
            position: fixed;
            top: calc(var(--dash-header-h) + var(--dash-crumb-h));
            left: 0;
            right: 0;
            bottom: var(--dash-footer-h);
            background: rgba(15, 23, 42, .22);
            z-index: 1180;
            pointer-events: auto;
        }

        /* Sidebar Container */
        .dash-sidebar-outer {
            width: var(--dash-sidebar-w); flex-shrink: 0; background: var(--dash-body-bg);
            padding: 1rem; box-sizing: border-box; min-height: 0; display: flex; flex-direction: column;
        }
        .dash-sidebar-card {
            background: rgba(255,255,255,0.92); border-radius: 12px; border: 1px solid rgba(23,53,47,0.08);
            box-shadow: 0 1px 2px rgba(0,0,0,.04); flex: 1; min-height: 0; display: flex; flex-direction: column;
        }
        .dash-sidebar-nav-scroll { flex: 1; overflow-y: auto; padding: 0.35rem 0 0.85rem; }
        .dash-nav-heading {
            font-size: 0.68rem; font-weight: 700; letter-spacing: 0.1em; color: #777;
            padding: 0.65rem 1rem 0.3rem; background: var(--dash-brand-cream); border-bottom: 1px solid rgba(23,53,47,0.06);
            text-transform: uppercase;
        }
        .dash-nav-link {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 1rem; margin: 0.12rem 0.5rem;
            color: #333; text-decoration: none; font-size: 0.9rem; border-radius: 0;
        }
        .dash-nav-link:hover { background: #f4f4f5; }
        .dash-nav-link.is-active { color: var(--dash-brand-forest); font-weight: 600; background: rgba(184,149,93,0.18); }
        .dash-nav-ico { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.75; }

        /* Content Area */
        .dash-content-outer {
            flex: 1; min-width: 0; min-height: 0; display: flex; flex-direction: column;
            background: var(--dash-body-bg); padding: 1rem 1rem 0; box-sizing: border-box; position: relative;
        }
        .dash-content-scroll { flex: 1; overflow-y: auto; min-height: 0; }
        .dash-content-card {
            background: rgba(255,255,255,0.94); border-radius: 12px; border: 1px solid rgba(23,53,47,0.08);
            padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(0,0,0,.04);
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

        /* Footer */
        .dash-site-footer {
            position: fixed; bottom: 0; left: 0; right: 0; height: var(--dash-footer-h);
            background: #fff; border-top: 1px solid #dadce0; display: flex; align-items: center; justify-content: center;
            font-size: 0.875rem; color: #666; z-index: 900;
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
        html[data-dashboard-theme="dark"] .dash-header-alert-btn { background: #1f2937 !important; color: #e5e7eb !important; border-color: #475569 !important; }
        .dash-content-card [style*="font-size:22px; font-weight:600"],
        .dash-content-card [style*="font-size:24px; font-weight:800"],
        .dash-content-card [style*="font-size:26px; font-weight:800"],
        .dash-content-card [style*="font-size:28px; font-weight:600"],
        .dash-content-card .work-value,
        .dash-content-card .k-stat strong,
        .dash-content-card .text-22.fw-600,
        .dash-content-card .text-28.fw-600,
        .dash-content-card .text-30.fw-600 { color: #ffffff !important; }

        /* Mobile Specifics */
        @media (max-width: 1023px) {
            .dash-menu-btn { display: flex !important; }
            .dash-sidebar-outer {
                position: fixed;
                top: calc(var(--dash-header-h) + var(--dash-crumb-h));
                left: 0;
                bottom: var(--dash-footer-h);
                width: min(320px, 84vw);
                z-index: 1200;
                transform: translateX(-100%); transition: transform 0.3s ease;
                padding: 0;
            }
            .dash-sidebar-outer.is-open { transform: translateX(0); }
            .dash-sidebar-card { border-radius: 0; height: 100%; }
            .dash-content-outer { padding: 0.5rem 0.5rem 0; }
        }

        /* Table & Admin UI - Unchanged */
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 1rem; }
        .admin-table th, .admin-table td { text-align: left; padding: 0.65rem 0.5rem; border-bottom: 1px solid #e8e8e8; font-size: 0.95rem; }
        .dash-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; border-radius: 8px; cursor: pointer; text-decoration: none; border: none; }
        .dash-btn--ghost { background: #fff; color: #333; border: 1px solid #ccc; }
        .dash-user-menu__panel { position: absolute; right: 0; top: calc(100% + 8px); min-width: 220px; background: #fff; border: 1px solid #e5e5e5; border-radius: 12px; box-shadow: 0 8px 28px rgba(0,0,0,.12); z-index: 2000; }
        .dash-content-card div[style*="border-radius"],
        .dash-content-card a[style*="border-radius"],
        .dash-content-card button[style*="border-radius"],
        .dash-content-card img[style*="border-radius"],
        .dash-content-card video[style*="border-radius"],
        .dash-content-card label[style*="border-radius"],
        .dash-content-card span[style*="border-radius"] {
            border-radius: 10px !important;
        }
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
        .dash-header-alert-btn,
        .dash-theme-btn { background:#23262b !important; color:#e5e7eb !important; border-color:rgba(125,211,252,.18) !important; border-radius:12px !important; }
        .dash-sidebar-card,
        .dash-content-card { background:#23262b !important; border:1px solid rgba(125,211,252,.14) !important; border-radius:12px !important; color:#e5e7eb !important; }
        .dash-sidebar-outer { padding:.75rem !important; }
        .dash-nav-heading { display:none !important; }
        .dash-sidebar-nav-scroll { padding:.25rem 0 .45rem !important; }
        .dash-nav-link { color:#e5e7eb !important; border:1px solid rgba(125,211,252,.12) !important; background:#2b3038 !important; border-radius:8px !important; margin:0 .55rem .32rem !important; padding:.52rem .62rem !important; min-height:40px !important; }
        .dash-nav-link:hover { background:rgba(56,189,248,.1) !important; border-color:rgba(125,211,252,.24) !important; }
        .dash-nav-ico { width:15px !important; height:15px !important; }
        .dash-body,
        .dash-content-card { font-size: 13px !important; }
        .dash-header-bar__brand { font-size: 1rem !important; }
        .dash-breadcrumb-bar { font-size: 0.8rem !important; }
        .dash-nav-link,
        .dash-user-menu__panel a,
        .dash-user-menu__panel form button { font-size: 0.82rem !important; }
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
        .dash-nav-link.is-active { color:#7dd3fc !important; background:rgba(56,189,248,.12) !important; border-color:rgba(125,211,252,.28) !important; }
        .dash-site-footer { display:none !important; }
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
    $userInitial = mb_strtoupper(mb_substr(auth()->user()->name, 0, 1));
    $navUser = auth()->user();
    $canNav = static fn (string $perm): bool => $navUser->isSuperAdmin() || $navUser->hasPermission($perm);
    $showOperations = $canNav('manage-bookings') || $canNav('manage-customers') || $canNav('manage-room-service-reception');
    $showProperty = $canNav('manage-bookings') || $canNav('manage-maintenance') || $canNav('manage-room-service-reception');
    $showReports = $canNav('view-reception-reports');
    $staffScope = app(\App\Support\StaffScope::class);
    $recNavN = $staffScope->filterNotificationsByBranch(
        \App\Models\DashboardNotification::query()->whereNull('read_at')->whereNull('resolved_at')
    )->count();
@endphp

<div class="dash-root" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">

    <!-- Header -->
    <header class="dash-header-bar">
        <button type="button" class="dash-menu-btn" @click="sidebarOpen = true">
            <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <a href="{{ route('reception.dashboard') }}" class="dash-header-bar__brand">
            <span class="dash-header-brand-wrap">
                <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $dashCompany }}">
                <span class="dash-header-brand-text">{{ $dashCompany }}</span>
            </span>
        </a>

        <div class="dash-header-actions">
            @if ($canNav('manage-bookings'))
                <a href="{{ route('reception.notifications.index') }}" class="dash-header-alert-btn">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    @if($recNavN > 0)
                        <span style="position:absolute;top:-5px;right:-5px;background:#c41e3a;color:#fff;font-size:.68rem;padding:.15rem .35rem;border-radius:999px;">{{ $recNavN }}</span>
                    @endif
                </a>
            @endif
            <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                @csrf
                <button type="submit" class="dash-header-logout-btn" title="{{ __('Log out') }}" aria-label="{{ __('Log out') }}">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 16l4-4m0 0l-4-4m4 4H9"/><path d="M13 20H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
                </button>
            </form>
            <div class="dash-user-menu" x-data="{ open: false }">
                <button type="button" class="dash-user-menu__trigger" @click="open = !open">{{ $userInitial }}</button>
                <div class="dash-user-menu__panel" x-show="open" x-cloak @click.outside="open = false" x-transition>
                    <div style="padding: 10px 15px; border-bottom: 1px solid #eee;">
                        <strong>{{ auth()->user()->name }}</strong><br><small>{{ auth()->user()->email }}</small>
                    </div>
                    <a href="{{ route('profile.edit') }}" style="display:block; padding:10px 15px; text-decoration:none; color:#333;">{{ __('Profile') }}</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Breadcrumb -->
    <div class="dash-breadcrumb-bar">
        <a href="{{ auth()->user()->accountHomeUrl() }}">{{ __('Home') }}</a>
        <span style="margin: 0 8px; opacity: 0.5;">/</span>
        <span style="font-weight: 600;">@yield('title', __('Dashboard'))</span>
    </div>

    <!-- Backdrop (Only visible on Mobile when sidebar is open) -->
    <template x-if="sidebarOpen">
        <div class="dash-sidebar-backdrop" @click="sidebarOpen = false" x-transition.opacity></div>
    </template>

    <div class="dash-workspace">
        <!-- Sidebar -->
        <aside class="dash-sidebar-outer" :class="{ 'is-open': sidebarOpen }">
            <div class="dash-sidebar-card">
                <div class="dash-nav-heading">{{ __('Overview') }}</div>
                <nav class="dash-sidebar-nav-scroll">
                    <a href="{{ route('reception.dashboard') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.dashboard') ? 'is-active' : '' }}">
                        <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M4 6h16M4 12h16M4 18h7"/></svg>
                        {{ __('Dashboard') }}
                    </a>

                    @if ($showOperations)
                        <div class="dash-nav-heading">{{ __('Operations') }}</div>
                        @if ($canNav('manage-bookings'))
                            <a href="{{ route('reception.bookings.create') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.bookings.create') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M12 4v16m8-8H4"/></svg>
                                {{ __('New Check-in') }}
                            </a>
                            <a href="{{ route('reception.bookings.index') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.bookings.index') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('Bookings') }}
                            </a>
                        @endif
                        @if ($canNav('manage-customers'))
                            <a href="{{ route('reception.customers.index') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.customers.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                {{ __('Guests') }}
                            </a>
                        @endif
                    @endif

                    @if ($showProperty)
                        <div class="dash-nav-heading">{{ __('Property') }}</div>
                        <a href="{{ route('reception.rooms.index') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.rooms.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 10.5V20a1 1 0 001 1h4v-9H3zM10 4v17h4V4h-4zm7 7v10h4a1 1 0 001-1v-9h-5z"/></svg>
                            {{ __('Rooms') }}
                        </a>
                        <a href="{{ route('reception.maintenance.index') }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.maintenance.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                            {{ __('Maintenance') }}
                        </a>
                        @if ($canNav('manage-room-service-reception'))
                            <a href="{{ route('reception.room-service.index', ['payment' => 'unpaid', 'billed' => 1]) }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('reception.room-service.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M3 4h18M8 11h8M7 20h10a2 2 0 002-2V8H5v10a2 2 0 002 2z"/></svg>
                                {{ __('Kitchen bills') }}
                            </a>
                        @endif
                        <a href="{{ route('dashboard', ['guest_view' => 1]) }}" @click="sidebarOpen = false" class="dash-nav-link {{ request()->routeIs('dashboard') && request()->boolean('guest_view') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path d="M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H9l-4 4V6a2 2 0 012-2z"/></svg>
                            {{ __('Guest dashboard') }}
                        </a>
                    @endif
                </nav>
            </div>
        </aside>

        <!-- Content Area -->
        <main class="dash-content-outer">
            @include('partials.dashboard-preloader')
            <div class="dash-content-scroll">
                <div class="dash-content-card">
                    @yield('content')
                </div>
            </div>
        </main>
    </div>

    <!-- Footer -->
    <footer class="dash-site-footer">
        {{ $dashboardSettings->copyright_text ?? ('© '.date('Y').' '.$dashCompany) }}
    </footer>
</div>

@include('partials.swal')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
@stack('scripts')

<script>
    (function () {
        var preloader = document.querySelector('[data-dash-preloader]');
        if (!preloader) return;
        function hidePreloader() { preloader.classList.add('is-hidden'); }
        hidePreloader();
        window.addEventListener('load', function () { setTimeout(hidePreloader, 180); });
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

    /* Table Filter Tool */
    (function () {
        document.querySelectorAll('.admin-table').forEach(function (table) {
            if (table.dataset.enhancedFilter === '1') return;
            table.dataset.enhancedFilter = '1';
            var bodyRows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!bodyRows.length) return;
            var wrap = document.createElement('div');
            wrap.style.display = 'flex'; wrap.style.gap = '.5rem'; wrap.style.marginBottom = '.6rem';
            var search = document.createElement('input');
            search.placeholder = 'Quick filter...'; search.style.padding = '.45rem'; search.style.borderRadius = '8px'; search.style.border = '1px solid #ccc';
            search.addEventListener('input', function () {
                var q = search.value.toLowerCase();
                bodyRows.forEach(function (row) {
                    row.style.display = row.innerText.toLowerCase().indexOf(q) !== -1 ? '' : 'none';
                });
            });
            wrap.appendChild(search);
            table.parentNode.insertBefore(wrap, table);
        });
    })();

    /* Flatpickr Initialization */
    (function () {
        document.querySelectorAll('input[type="date"]').forEach(function (input) {
            flatpickr(input, { dateFormat: 'Y-m-d', allowInput: true });
        });
    })();
</script>
</body>
</html>
