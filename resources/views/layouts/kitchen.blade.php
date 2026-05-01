<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <title>@yield('title', __('Kitchen')) - {{ $dashboardSettings->hotelDisplayName() }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    @include('partials.footer-theme-overrides')
    <style>
        html { scroll-behavior: smooth; scrollbar-color: rgba(56,189,248,.65) var(--brand-theme-surface); }
        body.kitchen-body { margin:0; height:100vh; overflow:hidden; background:var(--brand-theme-bg); color:var(--brand-theme-text); font-family:'Manrope', 'Segoe UI', sans-serif; font-size:14px; }
        .k-shell { height:100vh; display:grid; grid-template-columns:280px minmax(0, 1fr); overflow:hidden; }
        .k-mobile-bar,
        .k-sidebar-backdrop { display:none; }
        .k-sidebar { position:sticky; top:0; height:100vh; overflow-y:auto; background:var(--brand-theme-surface); border-right:1px solid var(--brand-theme-border); padding:.75rem; display:flex; flex-direction:column; gap:.55rem; }
        .k-brand { padding:.72rem .8rem; border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface-soft); border-radius:10px; }
        .k-brand-wrap { display:flex; align-items:center; gap:.75rem; min-width:0; }
        .k-brand-wrap img { width:42px; height:42px; object-fit:contain; flex-shrink:0; }
        .k-brand-copy { min-width:0; }
        .k-brand h1 { margin:0; color:var(--brand-theme-heading); font-size:1.35rem; }
        .k-brand p { margin:.35rem 0 0; color:var(--brand-theme-muted); font-size:.9rem; }
        .k-nav { display:grid; gap:.35rem; }
        .k-nav a { text-decoration:none; color:var(--brand-theme-text); padding:.62rem .72rem; min-height:40px; border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface-soft); transition:background .2s ease, border-color .2s ease, transform .2s ease; font-size:.82rem; border-radius:8px; }
        .k-nav a:hover { background:var(--brand-theme-highlight); border-color:rgba(125,211,252,.32); transform:translateY(-1px); }
        .k-nav a.is-active { color:var(--brand-theme-heading); background:var(--brand-theme-highlight); }
        .k-nav form { margin:0; }
        .k-main { overflow:auto; overflow-x:hidden; padding:1.4rem; min-width:0; scrollbar-width:thin; }
        .dash-btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:42px;
            padding:.72rem 1rem;
            border-radius:12px;
            border:1px solid rgba(125, 211, 252, 0.24);
            background:linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(56, 189, 248, 0.08));
            color:#e0f2fe;
            font:inherit;
            font-size:.84rem;
            font-weight:700;
            text-decoration:none;
            cursor:pointer;
            transition:transform .18s ease, border-color .18s ease, background .18s ease, color .18s ease;
        }
        .dash-btn:hover {
            transform:translateY(-1px);
            border-color:rgba(125, 211, 252, 0.42);
            background:linear-gradient(135deg, rgba(56, 189, 248, 0.26), rgba(56, 189, 248, 0.12));
            color:#f0f9ff;
        }
        .dash-btn--primary {
            background:linear-gradient(135deg, rgba(56, 189, 248, 0.24), rgba(56, 189, 248, 0.1));
            color:#f0f9ff;
        }
        .dash-btn--ghost {
            background:rgba(255,255,255,.03);
            color:var(--brand-theme-text);
        }
        .dash-btn--ghost:hover {
            background:rgba(56, 189, 248, 0.12);
            color:#f0f9ff;
        }
        .k-top { display:flex; justify-content:space-between; gap:1rem; align-items:center; margin-bottom:1.4rem; }
        .k-top-right { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; justify-content:flex-end; }
        .k-top-card { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface); padding:.95rem 1rem; border-radius:14px; }
        .k-notify { position:relative; }
        .k-notify-btn {
            width:50px;
            height:50px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border:1px solid var(--brand-theme-border);
            background:var(--brand-theme-surface);
            color:var(--brand-theme-text);
            border-radius:14px;
            cursor:pointer;
        }
        .k-logout-btn {
            width:50px;
            height:50px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border:1px solid var(--brand-theme-border);
            background:var(--brand-theme-surface);
            color:var(--brand-theme-text);
            border-radius:14px;
            cursor:pointer;
            padding:0;
        }
        .k-notify-badge {
            position:absolute;
            top:-6px;
            right:-6px;
            min-width:22px;
            height:22px;
            padding:0 6px;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            border-radius:999px;
            background:#22c55e;
            color:#06220f;
            font-size:.72rem;
            font-weight:800;
            border:2px solid var(--brand-theme-bg);
        }
        .k-notify-panel {
            position:absolute;
            top:calc(100% + .7rem);
            right:0;
            width:min(420px, 88vw);
            border:1px solid var(--brand-theme-border);
            background:#222833;
            border-radius:18px;
            box-shadow:0 24px 60px rgba(0,0,0,.32);
            padding:1rem;
            display:none;
            z-index:200;
        }
        .k-notify.is-open .k-notify-panel { display:block; }
        .k-notify-list { display:grid; gap:.75rem; max-height:420px; overflow:auto; }
        .k-notify-item {
            border:1px solid rgba(125,211,252,.14);
            background:rgba(255,255,255,.02);
            border-radius:14px;
            padding:.85rem .9rem;
            display:grid;
            gap:.45rem;
        }
        .k-notify-item--unread {
            border-color:rgba(34,197,94,.28);
            background:rgba(34,197,94,.08);
        }
        .k-notify-meta {
            display:flex;
            justify-content:space-between;
            gap:.75rem;
            flex-wrap:wrap;
            align-items:center;
        }
        .k-grid { display:grid; gap:1rem; }
        .k-card { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface); padding:1rem; border-radius:16px; }
        .k-card h2, .k-card h3, .k-card h4 { margin-top:0; color:var(--brand-theme-heading); }
        .k-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1rem; }
        .k-stat { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface-card); padding:1rem; border-radius:14px; }
        .k-stat small { display:block; color:var(--brand-theme-muted); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.35rem; }
        .k-stat strong { font-size:1.65rem; color:var(--brand-theme-text); }
        .k-form-grid { display:grid; gap:.85rem; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); }
        .k-form-section { display:grid; gap:1rem; }
        .k-form-section__title { margin:0; color:var(--brand-theme-heading); font-size:1.65rem; }
        .k-form-section__copy { color:var(--brand-theme-muted); font-size:.95rem; }
        .k-field { display:grid; gap:.45rem; min-width:0; }
        .k-field--span { grid-column:1 / -1; }
        .k-field label,
        .k-card label { display:block; color:#dbeafe; font-size:.86rem; font-weight:700; letter-spacing:.04em; }
        .k-card input[type="text"],
        .k-card input[type="number"],
        .k-card input[type="time"],
        .k-card input[type="search"],
        .k-card input[type="file"],
        .k-card select,
        .k-card textarea {
            width:100%;
            min-height:42px;
            border:1px solid rgba(125,211,252,.24);
            background:#181b1f;
            color:var(--brand-theme-text);
            padding:.68rem .85rem;
            font:inherit;
            border-radius:12px;
        }
        .k-card textarea { min-height:112px; resize:vertical; }
        .k-card input[type="file"] { padding:.5rem .7rem; }
        .k-card input[type="number"]::-webkit-outer-spin-button,
        .k-card input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .k-card input[type="number"] {
            -moz-appearance: textfield;
            appearance: textfield;
        }
        .k-card input[type="file"]::file-selector-button {
            margin-right:.9rem;
            border:1px solid rgba(125,211,252,.24);
            background:var(--brand-theme-surface-soft);
            color:var(--brand-theme-text);
            padding:.5rem .85rem;
            font:inherit;
            cursor:pointer;
            border-radius:10px;
        }
        .k-card input::placeholder,
        .k-card textarea::placeholder { color:var(--brand-theme-muted); }
        .k-card input:focus,
        .k-card select:focus,
        .k-card textarea:focus { outline:none; border-color:var(--brand-theme-heading); box-shadow:0 0 0 1px rgba(125,211,252,.18); }
        .k-checkbox { display:flex; align-items:center; gap:.7rem; min-height:48px; }
        .k-checkbox input[type="checkbox"] { width:18px; height:18px; accent-color:#38bdf8; }
        .k-toolbar { display:flex; gap:.75rem; justify-content:space-between; align-items:end; flex-wrap:wrap; }
        .k-search { display:flex; gap:.75rem; flex-wrap:wrap; align-items:end; }
        .k-chart-bars { display:grid; grid-template-columns:repeat(auto-fit,minmax(70px,1fr)); gap:.8rem; align-items:end; min-height:220px; }
        .k-bar-col { display:grid; gap:.45rem; justify-items:center; }
        .k-bar { width:100%; max-width:52px; background:linear-gradient(180deg, rgba(56,189,248,.92), rgba(56,189,248,.18)); border:1px solid rgba(125,211,252,.22); min-height:14px; border-radius:12px 12px 0 0; }
        .k-muted { color:var(--brand-theme-muted); }
        .k-actions { display:flex; gap:.6rem; flex-wrap:wrap; align-items:center; }
        .k-sidebar::-webkit-scrollbar,
        .k-main::-webkit-scrollbar { width:12px; }
        .k-sidebar::-webkit-scrollbar-track,
        .k-main::-webkit-scrollbar-track { background:var(--brand-theme-surface); }
        .k-sidebar::-webkit-scrollbar-thumb,
        .k-main::-webkit-scrollbar-thumb { background:rgba(56,189,248,.55); border:2px solid var(--brand-theme-surface); }
        .k-sidebar::-webkit-scrollbar-thumb,
        .k-main::-webkit-scrollbar-thumb,
        body::-webkit-scrollbar-thumb { border-radius:999px; }
        body::-webkit-scrollbar,
        .dash-content-scroll::-webkit-scrollbar,
        .dash-sidebar-nav-scroll::-webkit-scrollbar { width:12px; }
        body::-webkit-scrollbar-track,
        .dash-content-scroll::-webkit-scrollbar-track,
        .dash-sidebar-nav-scroll::-webkit-scrollbar-track { background:var(--brand-theme-surface); }
        body::-webkit-scrollbar-thumb,
        .dash-content-scroll::-webkit-scrollbar-thumb,
        .dash-sidebar-nav-scroll::-webkit-scrollbar-thumb { background:rgba(56,189,248,.55); border:2px solid var(--brand-theme-surface); border-radius:999px; }
        body.kitchen-body { font-size: 13px !important; }
        .k-brand h1 { font-size: 1.2rem !important; }
        .k-brand p,
        .k-nav a,
        .k-muted,
        .k-card,
        .k-card p,
        .k-card li,
        .k-card td,
        .k-card th { font-size: 0.82rem !important; }
        .k-card h2 { font-size: 1.15rem !important; }
        .k-card h3 { font-size: 1rem !important; }
        .k-card h4 { font-size: 0.92rem !important; }
        .k-stat small { font-size: 0.68rem !important; }
        .k-stat strong { font-size: 1.35rem !important; }
        .k-form-section__title { font-size: 1.3rem !important; }
        .k-form-section__copy { font-size: 0.82rem !important; }
        .k-field label,
        .k-card label { font-size: 0.78rem !important; }
        .k-card input[type="text"],
        .k-card input[type="number"],
        .k-card input[type="time"],
        .k-card input[type="search"],
        .k-card input[type="file"],
        .k-card select,
        .k-card textarea { font-size: 0.82rem !important; min-height: 40px !important; padding: .58rem .75rem !important; }
        .k-panel-btn,
        .dash-btn,
        .k-tab { font-size: 0.78rem !important; }
        .k-panel-btn,
        .dash-btn { min-height: 38px !important; padding: .62rem .85rem !important; }
        .k-tab { padding: .58rem .85rem !important; }
        .k-top-card,
        .k-card,
        .k-stat,
        .k-brand { padding: .72rem .8rem !important; }
        @media (max-width: 1023px) {
            html, body.kitchen-body { height:auto; }
            body.kitchen-body { overflow-x:hidden; overflow-y:auto; }
            .k-shell { height:auto; display:block; }
            .k-mobile-bar {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                z-index: 10090;
                display:flex;
                align-items:center;
                justify-content:space-between;
                gap:1rem;
                padding:.9rem 1rem;
                background:rgba(26, 28, 31, 0.96);
                border-bottom:1px solid var(--brand-theme-border);
                backdrop-filter:blur(10px);
            }
            .k-mobile-toggle {
                width:44px;
                height:44px;
                display:inline-flex;
                align-items:center;
                justify-content:center;
                border:1px solid var(--brand-theme-border);
                background:var(--brand-theme-surface-soft);
                color:var(--brand-theme-text);
                border-radius:12px;
                cursor:pointer;
            }
        .k-mobile-title { display:flex; align-items:center; gap:.6rem; color:var(--brand-theme-heading); font-weight:700; font-size:1rem; min-width:0; }
        .k-mobile-title img { width:34px; height:34px; object-fit:contain; flex-shrink:0; }
        .k-mobile-title span { white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            .k-sidebar-backdrop {
                position:fixed;
                inset:0;
                z-index:10070;
                background:rgba(2, 6, 23, 0.58);
            }
            .k-shell.is-nav-open .k-sidebar-backdrop { display:block; }
            .k-sidebar {
                position:fixed;
                top:0;
                left:0;
                bottom:0;
                width:min(84vw, 320px);
                height:100vh;
                z-index:10080;
                transform:translateX(-108%);
                transition:transform .24s ease;
                overflow-y:auto;
                box-shadow:20px 0 40px rgba(0,0,0,.34);
            }
            .k-shell.is-nav-open .k-sidebar { transform:translateX(0); }
            .k-main {
                overflow:visible;
                width:100%;
                min-width:0;
                padding:5.6rem .85rem 1.2rem;
            }
            .k-top { margin-top:.2rem; }
        }
    </style>
    @stack('styles')
</head>
<body class="kitchen-body">
    @php
        $kitchenNotifications = \App\Models\DashboardNotification::query()
            ->where('recipient_user_id', auth()->id())
            ->whereNull('resolved_at')
            ->where('kind', 'like', 'kitchen-%')
            ->latest()
            ->limit(8)
            ->get();
        $kitchenUnreadNotifications = $kitchenNotifications->whereNull('read_at')->count();
    @endphp
    <div class="k-shell" id="kShell">
        <div class="k-mobile-bar">
            <button type="button" class="k-mobile-toggle" id="kSidebarOpen" aria-label="{{ __('Open kitchen menu') }}">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            </button>
            <div class="k-mobile-title">
                <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $dashboardSettings->hotelDisplayName() }}">
                <span>{{ $dashboardSettings->hotelDisplayName() }}</span>
            </div>
            <div style="width:44px;height:44px;"></div>
        </div>
        <div class="k-sidebar-backdrop" id="kSidebarBackdrop"></div>
        <aside class="k-sidebar">
            <div class="k-brand">
                <div class="k-brand-wrap">
                    <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $dashboardSettings->hotelDisplayName() }}">
                    <div class="k-brand-copy">
                        <h1>{{ __('Kitchen Ops') }}</h1>
                        <p>{{ $dashboardSettings->hotelDisplayName() }}</p>
                    </div>
                </div>
            </div>
            <nav class="k-nav">
                <a href="{{ route('kitchen.dashboard') }}" class="{{ request()->routeIs('kitchen.dashboard') ? 'is-active' : '' }}">{{ __('Dashboard') }}</a>
                <a href="{{ route('kitchen.orders.index') }}" class="{{ request()->routeIs('kitchen.orders.*') ? 'is-active' : '' }}">{{ __('Kitchen orders') }}</a>
                @if (auth()->user()?->canAssignKitchenOrders())
                    <a href="{{ route('kitchen.assignments.index') }}" class="{{ request()->routeIs('kitchen.assignments.*') ? 'is-active' : '' }}">{{ __('Assign orders') }}</a>
                @endif
                <a href="{{ route('kitchen.orders.index', ['payment' => 'unpaid', 'billed' => 1]) }}" class="{{ request()->routeIs('kitchen.orders.*') && request('billed') ? 'is-active' : '' }}">{{ __('Kitchen bills') }}</a>
                <a href="{{ route('kitchen.menu.index') }}" class="{{ request()->routeIs('kitchen.menu.*') ? 'is-active' : '' }}">{{ __('Menu catalog') }}</a>
                @if (auth()->user()?->role?->slug === \App\Models\Role::KITCHEN_SLUG || auth()->user()?->isSuperAdmin() || auth()->user()?->isManager())
                    <a href="{{ route('kitchen.settings.edit') }}" class="{{ request()->routeIs('kitchen.settings.*') ? 'is-active' : '' }}">{{ __('Service settings') }}</a>
                @endif
                <a href="{{ route('kitchen.qr.index') }}" class="{{ request()->routeIs('kitchen.qr.*') ? 'is-active' : '' }}">{{ __('Room QR codes') }}</a>
                @if (auth()->user()?->hasPermission('view-kitchen-reports'))
                    <a href="{{ route('kitchen.reports.index') }}" class="{{ request()->routeIs('kitchen.reports.*') ? 'is-active' : '' }}">{{ __('Kitchen reports') }}</a>
                @endif
                @if (auth()->user()?->canManageKitchenStaff())
                    <a href="{{ route('kitchen.staff.index') }}" class="{{ request()->routeIs('kitchen.staff.*') ? 'is-active' : '' }}">{{ __('Kitchen staff') }}</a>
                @endif
                @if (auth()->user()?->canManageKitchenRoles())
                    <a href="{{ route('kitchen.roles.index') }}" class="{{ request()->routeIs('kitchen.roles.*') ? 'is-active' : '' }}">{{ __('Kitchen roles') }}</a>
                @endif
                @if (auth()->user()?->hasAdminPanelAccess())
                    <a href="{{ route('admin.dashboard') }}">{{ __('Admin dashboard') }}</a>
                @endif
                @if (auth()->user()?->hasPermission('access-reception-panel'))
                    <a href="{{ route('reception.dashboard') }}">{{ __('Reception panel') }}</a>
                @endif
            </nav>
        </aside>
        <main class="k-main">
            <div class="k-top">
                <div class="k-top-card">
                    <div class="text-13 k-muted">{{ __('Kitchen workspace') }}</div>
                    <div class="fw-600">{{ auth()->user()?->name }}</div>
                </div>
                <div class="k-top-right">
                    <div class="k-notify" id="kNotify">
                        <button type="button" class="k-notify-btn" id="kNotifyToggle" aria-label="{{ __('Open kitchen notifications') }}">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 1 1-6 0m6 0H9"/></svg>
                        </button>
                        @if ($kitchenUnreadNotifications > 0)
                            <span class="k-notify-badge">{{ $kitchenUnreadNotifications }}</span>
                        @endif
                        <div class="k-notify-panel">
                            <div class="k-actions-between" style="margin-bottom:.85rem;">
                                <div>
                                    <div class="fw-700">{{ __('Kitchen alerts') }}</div>
                                    <div class="text-13 k-muted mt-5">{{ __('New orders for kitchen master and assigned task alerts for staff.') }}</div>
                                </div>
                                <span class="k-chip">{{ $kitchenUnreadNotifications }} {{ __('unread') }}</span>
                            </div>
                            <div class="k-notify-list">
                                @forelse ($kitchenNotifications as $notification)
                                    <article class="k-notify-item {{ $notification->read_at ? '' : 'k-notify-item--unread' }}">
                                        <div class="fw-700">{{ $notification->title }}</div>
                                        <div class="text-13 k-muted">{{ $notification->body }}</div>
                                        <div class="k-notify-meta">
                                            <span class="text-12 k-muted">{{ $notification->created_at?->format('Y-m-d H:i') }}</span>
                                            <div class="k-actions">
                                                <a href="{{ route('kitchen.orders.index') }}" class="dash-btn dash-btn--ghost">{{ __('Open queue') }}</a>
                                                @if (! $notification->read_at)
                                                    <form method="POST" action="{{ route('kitchen.notifications.read', $notification) }}">
                                                        @csrf
                                                        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Mark read') }}</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </div>
                                    </article>
                                @empty
                                    <div class="k-card" style="padding:.9rem;">{{ __('No kitchen alerts yet.') }}</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" style="margin:0;">
                        @csrf
                        <button type="submit" class="k-logout-btn" title="{{ __('Log out') }}" aria-label="{{ __('Log out') }}">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9"/><path stroke-width="2" d="M13 20H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
                        </button>
                    </form>
                    <div class="k-top-card">
                        <div class="text-13 k-muted">{{ __('Today') }}</div>
                        <div class="fw-600">{{ now()->format('Y-m-d H:i') }}</div>
                    </div>
                </div>
            </div>
            @if (session('status'))
                <div class="k-card" style="margin-bottom:1rem;color:#9ff0b8;border-color:rgba(34,197,94,.34);background:rgba(34,197,94,.09);">
                    {{ session('status') }}
                </div>
            @endif
            @yield('content')
        </main>
    </div>
    @stack('scripts')
    <script>
        (function () {
            var shell = document.getElementById('kShell');
            var openBtn = document.getElementById('kSidebarOpen');
            var backdrop = document.getElementById('kSidebarBackdrop');
            if (!shell || !openBtn || !backdrop) return;
            function closeNav() { shell.classList.remove('is-nav-open'); }
            function openNav() { shell.classList.add('is-nav-open'); }
            openBtn.addEventListener('click', function () {
                if (shell.classList.contains('is-nav-open')) closeNav(); else openNav();
            });
            backdrop.addEventListener('click', closeNav);
            document.querySelectorAll('.k-nav a').forEach(function (link) {
                link.addEventListener('click', closeNav);
            });
            window.addEventListener('resize', function () {
                if (window.innerWidth > 1023) closeNav();
            });
            var notifyWrap = document.getElementById('kNotify');
            var notifyToggle = document.getElementById('kNotifyToggle');
            if (notifyWrap && notifyToggle) {
                notifyToggle.addEventListener('click', function (event) {
                    event.stopPropagation();
                    notifyWrap.classList.toggle('is-open');
                });
                document.addEventListener('click', function (event) {
                    if (!notifyWrap.contains(event.target)) {
                        notifyWrap.classList.remove('is-open');
                    }
                });
            }
        })();
        document.querySelectorAll('.k-card input[type="number"]').forEach(function (input) {
            input.addEventListener('wheel', function (event) {
                event.preventDefault();
            }, { passive: false });
        });
    </script>
</body>
</html>
