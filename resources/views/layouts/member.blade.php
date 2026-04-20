<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account-bridge.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>@yield('title', __('Account')) — {{ $dashboardSettings->hotelDisplayName() }}</title>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.14.3/dist/cdn.min.js"></script>
    {{-- CSS Styles zako zote zilizopita zibaki hapa --}}
    <style>
        [x-cloak] { display: none !important; }
        :root {
            --dash-body-bg: #eef0f3;
            --dash-header-h: 68px;
            --dash-crumb-h: 46px;
            --dash-footer-h: 56px;
            --dash-sidebar-w: 340px;
        }
        .dash-body { margin: 0; min-height: 100vh; height: 100vh; overflow: hidden; background: var(--dash-body-bg); font-family: 'Jost', system-ui, sans-serif; }
        .dash-header-bar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h);
            background: #fff; border-bottom: 1px solid #e0e0e0; z-index: 300;
            display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box;
        }
        .dash-menu-btn {
            display: none; align-items: center; justify-content: center; width: 40px; height: 40px;
            border: 1px solid #ddd; border-radius: 10px; background: #fff; cursor: pointer; flex-shrink: 0;
        }
        .dash-header-bar__brand { font-size: 1.125rem; font-weight: 700; color: #111; text-decoration: none; flex-shrink: 0; }
        .dash-header-bar__brand:hover { color: #c41e3a; }
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
            background: #e3e5e8; border-bottom: 1px solid #d0d4d8; z-index: 290;
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
            background: #fff; border-radius: 12px; border: 1px solid #e5e5e5;
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
            color: #333; text-decoration: none; font-size: 0.98rem; border-radius: 8px;
        }
        .dash-nav-link:hover { background: #f4f4f5; }
        .dash-nav-link.is-active { color: #c41e3a; font-weight: 600; background: #fde8ec; }
        .dash-nav-ico { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.75; }
        .dash-nav-link.is-active .dash-nav-ico { opacity: 1; stroke: #c41e3a; }
        .dash-content-outer { flex: 1; min-width: 0; min-height: 0; display: flex; flex-direction: column; background: var(--dash-body-bg); padding: 1rem 1rem 0; box-sizing: border-box; }
        .dash-content-scroll { flex: 1; overflow-y: auto; min-height: 0; -webkit-overflow-scrolling: touch; }
        .dash-content-card {
            background: #fff; border-radius: 12px; border: 1px solid #e5e5e5;
            padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(0,0,0,.04); min-height: min-content; overflow-x: auto;
        }
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
        .dash-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; font-family: inherit; cursor: pointer; text-decoration: none; border: none; }
        .dash-btn--primary { background: #111; color: #fff; }
        .dash-btn--primary:hover { background: #333; color: #fff; }
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
        .dash-body { margin: 0; min-height: 100vh; height: 100vh; overflow: hidden; background: var(--dash-body-bg); font-family: 'Jost', system-ui, sans-serif; }
        .dash-header-bar { position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h); background: #fff; border-bottom: 1px solid #e0e0e0; z-index: 300; display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box; }
        .dash-nav-link.is-active { color: #c41e3a; font-weight: 600; background: #fde8ec; }
        /* Nimeongeza hii kwa ajili ya kutofautisha maeneo */
        .role-badge { font-size: 10px; background: #eee; padding: 2px 6px; border-radius: 4px; text-transform: uppercase; font-weight: 700; color: #555; }
        /* CSS zingine zote zilizobaki... */
    </style>
</head>
<body class="dash-body">
@include('partials.page-progress')
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
        <a href="{{ route('dashboard') }}" class="dash-header-bar__brand">{{ $dashCompany }}</a>
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
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit">{{ __('Log out') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <div class="dash-breadcrumb-bar">
        <a href="{{ $u->accountHomeUrl() }}">{{ __('Home') }}</a>
        <span class="sep">/</span>
        <span class="current">@yield('title')</span>
    </div>

    <div class="dash-workspace">
        <aside class="dash-sidebar-outer" :class="{ 'is-open': sidebarOpen }">
            <div class="dash-sidebar-card">
                {{-- Side Profile --}}
                <div style="padding:1.25rem 1rem; border-bottom:1px solid #eee; background:#f9fafb; border-radius:12px 12px 0 0;">
                    <div style="display:flex;align-items:center;gap:0.75rem;">
                        <div style="width:44px;height:44px;border-radius:50%;background:#111;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;">{{ $userInitial }}</div>
                        <div style="min-width:0;">
                            <div style="font-weight:600;font-size:0.9rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $u->name }}</div>
                            <div style="font-size:11px; color:#c41e3a; font-weight:700; text-transform:uppercase;">{{ $roleName }}</div>
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
        var root = document.querySelector('.dash-root');
        if (!root) return;
        var sidebar = root.querySelector('.dash-sidebar-outer');
        var backdrop = root.querySelector('.dash-sidebar-backdrop');
        var openBtn = root.querySelector('[data-sidebar-open="1"]');
        if (!sidebar || !backdrop || !openBtn) return;
        var apply = function (open) {
            sidebar.classList.toggle('is-open', open);
            backdrop.style.display = open ? 'block' : 'none';
            backdrop.style.opacity = open ? '1' : '0';
        };
        openBtn.addEventListener('click', function () { apply(!sidebar.classList.contains('is-open')); });
        backdrop.addEventListener('click', function () { apply(false); });
    })();
</script>
</body>
</html>
