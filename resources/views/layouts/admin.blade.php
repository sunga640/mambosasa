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
    <title>@yield('title', __('Admin')) — {{ $dashboardSettings->hotelDisplayName() }}</title>
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
        .dash-root { min-height: 100vh; height: 100vh; }
        .dash-header-bar {
            position: fixed; top: 0; left: 0; right: 0; height: var(--dash-header-h);
            background: rgba(255,255,255,0.96); border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 300;
            display: flex; align-items: center; gap: 0.75rem; padding: 0 1rem; box-sizing: border-box;
            box-shadow: 0 8px 24px rgba(11,29,35,0.04);
        }
        .dash-menu-btn {
            display: none; align-items: center; justify-content: center; width: 40px; height: 40px;
            border: 1px solid #ddd; border-radius: 10px; background: #fff; cursor: pointer; flex-shrink: 0;
        }
        .dash-menu-btn:hover { border-color: #bbb; }
        .dash-header-bar__brand {
            font-size: 1.125rem; font-weight: 700; color: var(--dash-brand-forest); text-decoration: none; flex-shrink: 1; min-width: 0; max-width: min(44vw, 720px);
        }
        .dash-header-bar__brand:hover { color: var(--dash-brand-gold); }
        .dash-header-brand-wrap { display:inline-flex; align-items:center; gap:.75rem; min-width:0; }
        .dash-header-brand-wrap img { width:42px; height:42px; object-fit:contain; flex-shrink:0; }
        .dash-header-brand-text { min-width:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .dash-header-logout-btn {
            display: inline-flex; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; text-decoration: none; background: #fff;
            cursor: pointer; padding: 0;
        }
        .dash-header-logout-btn:hover { border-color: #bbb; background: #fafafa; }
        .dash-header-branch-form { margin-left: .5rem; }
        .dash-header-branch-select {
            min-width: 104px; max-width: 180px; font-size: 12px; padding: 8px 30px 8px 10px; border-radius: 10px;
            border: 1px solid #d7dee7; background: #fff; color: #111827; font-family: inherit;
        }
        .dash-header-search {
            flex: 1; min-width: 0; max-width: 520px; margin: 0 auto;
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
        .dash-header-search button:hover { background: #333; }
        .dash-header-spacer { display: none; flex: 1; min-width: 0; }
        .dash-mobile-search-btn {
            display: none; align-items: center; justify-content: center; width: 42px; height: 42px;
            border: 1px solid #ddd; border-radius: 10px; color: #111; flex-shrink: 0;
        }
        .dash-mobile-search-btn:hover { border-color: #bbb; background: #fafafa; }
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
        .dash-user-menu__trigger:hover { opacity: 0.92; }
        .dash-user-menu__panel {
            position: absolute; right: 0; top: calc(100% + 8px); min-width: 220px;
            background: #f8f7f4; border: 1px solid #e5e5e5; border-radius: 12px; box-shadow: 0 8px 28px rgba(0,0,0,.12);
            padding: 0.5rem 0; z-index: 320;
        }
        .dash-user-menu__who { padding: 0.65rem 1rem; font-size: 0.85rem; color: #444; border-bottom: 1px solid #eee; line-height: 1.4; }
        .dash-user-menu__who strong { display: block; color: #111; font-size: 0.95rem; }
        .dash-user-menu__panel a {
            display: block; padding: 0.55rem 1rem; color: #333; text-decoration: none; font-size: 0.9rem;
        }
        .dash-user-menu__panel a:hover { background: #f5f5f5; }
        .dash-user-menu__panel form { margin: 0; padding: 0.35rem 0.75rem 0.5rem; border-top: 1px solid #eee; margin-top: 0.25rem; }
        .dash-user-menu__panel form button {
            width: 100%; margin-top: 0.35rem; padding: 0.45rem; border-radius: 8px; border: 1px solid #ccc;
            background: #fff; cursor: pointer; font-family: inherit; font-size: 0.9rem;
        }
        .dash-user-menu__panel form button:hover { border-color: #111; }
        .dash-breadcrumb-bar {
            position: fixed; top: var(--dash-header-h); left: 0; right: 0; height: var(--dash-crumb-h);
            background: #efe7d8; border-bottom: 1px solid rgba(23,53,47,0.08); z-index: 290;
            display: flex; align-items: center; padding: 0 1.25rem; font-size: 0.875rem; color: #444; box-sizing: border-box;
        }
        .dash-breadcrumb-bar a { color: #333; text-decoration: none; }
        .dash-breadcrumb-bar a:hover { text-decoration: underline; }
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
            padding: 0.65rem 1rem 0.3rem; background: var(--dash-brand-cream); border-bottom: 1px solid rgba(23,53,47,0.06);
            text-transform: uppercase;
        }
        .dash-nav-heading:first-child { border-radius: 12px 12px 0 0; }
        .dash-sidebar-nav-scroll { flex: 1; overflow-y: auto; min-height: 0; padding: 0.35rem 0 0.85rem; }
        .dash-nav-link {
            display: flex; align-items: center; gap: 0.65rem; padding: 0.55rem 1rem; margin: 0.12rem 0.5rem;
            color: #333; text-decoration: none; font-size: 0.9rem; border-radius: 0;
        }
        .dash-nav-link:hover { background: #f4f4f5; }
        .dash-nav-link.is-active { color: var(--dash-brand-forest); font-weight: 600; background: rgba(184,149,93,0.18); }
        .dash-nav-ico { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.75; }
        .dash-nav-link.is-active .dash-nav-ico { opacity: 1; stroke: #c41e3a; }
        .dash-nav-link--sub { font-size: 0.92rem; padding-left: 2.25rem; opacity: 0.95; }
        .dash-content-outer {
            flex: 1; min-width: 0; min-height: 0; display: flex; flex-direction: column;
            background: var(--dash-body-bg); padding: 1rem 1rem 0; box-sizing: border-box; position: relative;
        }
        .dash-content-scroll { flex: 1; overflow-y: auto; min-height: 0; -webkit-overflow-scrolling: touch; }
        .dash-content-card {
            background: rgba(255,255,255,0.94); border-radius: 12px; border: 1px solid rgba(23,53,47,0.08);
            padding: 1.5rem 1.75rem; box-shadow: 0 1px 2px rgba(0,0,0,.04); min-height: min-content; overflow-x: auto;
        }
        .dash-page-preloader {
            position: absolute;
            inset: 1rem 1rem 0 0;
            z-index: 40;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(247, 242, 232, 0.92);
            backdrop-filter: blur(4px);
            transition: opacity 0.28s ease, visibility 0.28s ease;
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
        .admin-table { width: 100%; border-collapse: collapse; margin-top: 1rem; table-layout: auto; }
        .admin-table thead th {
            background: linear-gradient(120deg, rgba(30, 77, 107, 0.12) 0%, rgba(0, 153, 204, 0.1) 45%, rgba(18, 34, 35, 0.08) 100%);
            border-bottom: 1px solid #dbe3ec;
            font-weight: 600;
        }
        .admin-table tbody tr:nth-child(odd) { background: #fff; }
        .admin-table tbody tr:nth-child(even) { background: linear-gradient(180deg, #fafafa 0%, #f4f4f5 100%); }
        .admin-table tbody tr:hover td { background: rgba(0, 153, 204, 0.06); }
        .admin-table th, .admin-table td { text-align: left; padding: 0.65rem 0.5rem; border-bottom: 1px solid #e8e8e8; font-size: 0.95rem; vertical-align: middle; }
        .admin-actions { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .form-row { margin-bottom: 1rem; }
        .form-row label { display: block; margin-bottom: 0.35rem; font-weight: 500; }
        .form-row input[type="text"], .form-row input[type="email"], .form-row input[type="password"], .form-row input[type="url"], .form-row input[type="number"], .form-row select, .form-row textarea {
            width: 100%; max-width: 520px; padding: 0.5rem 0.65rem; border: 1px solid #ccc; border-radius: 8px; font-family: inherit;
        }
        .form-row input[type="file"] { max-width: 520px; font-size: 0.9rem; }
        .perm-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 0.5rem; margin-top: 0.5rem; }
        .report-kpi-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin: 1.25rem 0; }
        .report-kpi { background: #f8f9fb; border: 1px solid #e5e7eb; border-radius: 10px; padding: 1rem; }
        .report-kpi__val { font-size: 1.35rem; font-weight: 700; color: #111; }
        .report-kpi__lab { font-size: 0.8rem; color: #666; margin-top: 0.25rem; }
        .report-filters { margin: 1rem 0; padding: 1rem; background: #f8f9fb; border-radius: 10px; border: 1px solid #eee; }
        .report-filters__row { display: flex; flex-wrap: wrap; gap: 0.75rem; align-items: flex-end; }
        .report-filters__lab { display: block; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; color: #888; margin-bottom: 0.25rem; }
        .report-filters input, .report-filters select { padding: 0.45rem 0.55rem; border: 1px solid #ccc; border-radius: 8px; font-family: inherit; }
        .report-chart-wrap { margin: 1.25rem 0; padding: 0.85rem 1rem; border: 1px solid #eee; border-radius: 12px; background: #fafafa; }
        .report-chart-canvas { position: relative; width: 100%; height: 200px; max-height: 38vh; }
        .report-chart-canvas--doughnut { height: 200px; max-height: 34vh; max-width: 260px; margin: 0 auto; }
        @media (max-width: 600px) {
            .report-chart-canvas { height: 180px; max-height: 42vh; }
            .report-chart-canvas--doughnut { height: 180px; max-width: 220px; }
        }
        .report-hub-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem; }
        .report-hub-card {
            display: flex; flex-direction: column; gap: 0.35rem; padding: 1.25rem; border: 1px solid #e5e7eb; border-radius: 12px;
            text-decoration: none; color: #111; background: #fff; transition: box-shadow 0.15s, border-color 0.15s;
        }
        .report-hub-card:hover { border-color: #c41e3a; box-shadow: 0 4px 14px rgba(0,0,0,.06); }
        .report-hub-card__title { font-weight: 700; }
        .report-hub-card__meta { font-size: 0.85rem; color: #666; }
        .dash-btn { display: inline-flex; align-items: center; justify-content: center; padding: 0.5rem 1rem; border-radius: 8px; font-size: 0.9rem; font-family: inherit; cursor: pointer; text-decoration: none; border: none; }
        .dash-content-card [style*="font-size:24px; font-weight:800"],
        .dash-content-card [style*="font-size:26px; font-weight:800"],
        .dash-content-card [style*="font-size:28px; font-weight:600"],
        .dash-content-card .work-value,
        .dash-content-card .k-stat strong { color: #ffffff !important; }
        .dash-btn--primary { background: var(--dash-brand-forest); color: #fff; }
        .dash-body,
        .dash-content-card { font-size: 13px !important; }
        .dash-header-bar__brand { font-size: 1rem !important; }
        .dash-breadcrumb-bar { font-size: 0.8rem !important; }
        .dash-nav-link,
        .dash-nav-link--sub,
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
        .dash-btn--primary:hover { background: #214b43; color: #fff; }
        .dash-content-card div[style*="border-radius"],
        .dash-content-card a[style*="border-radius"],
        .dash-content-card button[style*="border-radius"],
        .dash-content-card img[style*="border-radius"],
        .dash-content-card video[style*="border-radius"],
        .dash-content-card label[style*="border-radius"],
        .dash-content-card span[style*="border-radius"] {
            border-radius: 10px !important;
        }
        .dash-btn--ghost { background: #fff; color: #333; border: 1px solid #ccc; }
        .dash-btn--ghost:hover { border-color: #111; }
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
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange {
            background: linear-gradient(135deg, #1e4d6b 0%, #1d4ed8 100%) !important;
            border-color: transparent !important;
            color: #fff !important;
        }
        .booking-days-modal-panel .flatpickr-calendar.inline {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
            margin: 0 auto;
        }@media (max-width: 1023px) {
    /* 1. Ruhusu body na root ziweze kuscroll */
    .dash-body, .dash-root {
        height: auto !important;
        overflow: auto !important;
        min-height: 100vh;
    }

    /* 2. Workspace isikwame kwenye screen moja */
    .dash-workspace {
        display: block !important;
        position: relative !important;
        height: auto !important; /* Muhimu: ili isifuate height ya 100vh */
        top: 0 !important; /* Inaruhusu content kuanza juu vizuri */
        padding-top: calc(var(--dash-header-h) + var(--dash-crumb-h)); /* Nafasi ya header */
    }

    /* 3. Content ijae na iweze kurefuka kulingana na maudhui */
    .dash-content-outer {
        width: 100% !important;
        padding: 15px !important;
        margin-left: 0 !important;
        flex: none !important;
        height: auto !important;
    }

    .dash-content-scroll {
        overflow-y: visible !important; /* Ruhusu body scroll ndio itumike */
        height: auto !important;
    }

    .dash-content-card {
        min-height: min-content;
        margin-bottom: 80px; /* Nafasi kwa ajili ya footer */
    }

    /* 4. Sidebar iendelee kubaki fixed pale inapofunguliwa */
    .dash-sidebar-outer {
        position: fixed !important;
        z-index: 10000 !important;
        top: 0 !important;
        left: 0 !important;
        height: 100vh !important;
        width: 280px !important;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
        overflow-y: auto; /* Sidebar yenyewe iweze kuscroll kama menu ni ndefu */
    }

    .dash-sidebar-outer.is-open {
        transform: translateX(0) !important;
    }

    /* Inaficha search bar ya ndani ya header isisumbue mpangilio */
    .dash-header-search {
        display: none !important;
    }

    /* Hakikisha footer haifuniki content ya mwisho */
    .dash-site-footer {
        position: relative !important;
        margin-top: 20px;
    }
}
@media (max-width: 1023px) {
    /* ... kodi zako zingine ... */

    .dash-sidebar-backdrop {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        width: 100vw !important;
        height: 100vh !important;
        background: rgba(0, 0, 0, 0.5) !important;
        z-index: 9999 !important; /* Chini kidogo ya sidebar (10000) */
        display: block;
    }
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
        .dash-mobile-search-btn,
        .dash-header-alert-btn,
        .dash-theme-btn { background:#23262b !important; color:#e5e7eb !important; border-color:rgba(125,211,252,.18) !important; border-radius:12px !important; }
        .dash-header-search { background:#23262b !important; border-color:rgba(125,211,252,.18) !important; border-radius:12px !important; }
        .dash-header-search input[type="search"] { color:#e5e7eb !important; }
        .dash-sidebar-card,
        .dash-content-card { background:#23262b !important; border:1px solid rgba(125,211,252,.14) !important; border-radius:12px !important; color:#e5e7eb !important; }
        .dash-sidebar-outer { padding:.75rem !important; }
        .dash-nav-heading { display:none !important; }
        .dash-sidebar-nav-scroll { padding:.25rem 0 .45rem !important; }
        .dash-nav-link { color:#e5e7eb !important; border:1px solid rgba(125,211,252,.12) !important; background:#2b3038 !important; border-radius:8px !important; margin:0 .55rem .32rem !important; padding:.52rem .62rem !important; min-height:40px !important; }
        .dash-nav-link:hover { background:rgba(56,189,248,.1) !important; border-color:rgba(125,211,252,.24) !important; }
        .dash-nav-link.is-active { color:#7dd3fc !important; background:rgba(56,189,248,.12) !important; border-color:rgba(125,211,252,.28) !important; }
        .dash-nav-ico { width:15px !important; height:15px !important; }
        .dash-site-footer { display:none !important; }
        .dash-content-card { margin-bottom:0 !important; }
        @media (max-width: 1023px) {
            .dash-sidebar-outer { width:min(84vw, 320px) !important; padding:0 !important; }
            .dash-sidebar-card { border-radius:0 !important; }
            .dash-nav-link { margin:0 .5rem .32rem !important; padding:.52rem .62rem !important; }
        }
    </style>
</head>
<body class="dash-body" data-skip-settings-preloader="{{ request()->routeIs('admin.settings.*') ? '1' : '0' }}">
@php
    $navUser = auth()->user();
    $dashCompany = $dashboardSettings->hotelDisplayName();
    $userInitial = mb_strtoupper(mb_substr($navUser->name, 0, 1));
    $navNotificationQuery = \App\Models\DashboardNotification::query();
    app(\App\Support\StaffScope::class)->filterNotificationsByBranch($navNotificationQuery, $navUser);
    $navN = $navNotificationQuery->whereNull('read_at')->whereNull('resolved_at')->count();
    $canNav = static fn (string ...$permissions): bool => $navUser->isSuperAdmin() || $navUser->hasAnyPermission($permissions);
    $showOverview = $canNav('manage-bookings', 'manage-dashboard-notifications', 'manage-hotel-services');
    $showProperty = $canNav('manage-properties-directory', 'manage-media-library', 'manage-room-categories', 'manage-branches', 'manage-rooms');
    $showOperations = $canNav('manage-bookings', 'manage-payment-methods', 'manage-customers', 'manage-maintenance');
    $showCommunication = $canNav('manage-contacts', 'manage-newsletters');
    $showReports = $canNav('view-reports', 'view-dashboard-analytics', 'export-reports');
    $showConfiguration = $canNav('manage-system-settings');
    $showAccess = $canNav('manage-users', 'manage-staff-users', 'manage-roles', 'manage-permissions');
    $showKitchen = $canNav('access-kitchen-panel', 'manage-kitchen-orders', 'manage-kitchen-menu', 'generate-kitchen-qr', 'view-kitchen-reports');
@endphp

<div class="dash-root" x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false">
    <header class="dash-header-bar" style="z-index: 1001 !important;">
    {{-- Hamburg Button --}}
    <button type="button" class="dash-menu-btn"
        @click="sidebarOpen = !sidebarOpen"
        style="display: flex !important; margin-right: 10px;">
        <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Logo --}}
    <a href="{{ route('admin.dashboard') }}" class="dash-header-bar__brand" style="font-size: 15px;" title="{{ $dashCompany }}">
        <span class="dash-header-brand-wrap">
            <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="{{ $dashCompany }}">
            <span class="dash-header-brand-text">{{ $dashCompany }}</span>
        </span>
    </a>

    {{-- Branch Selector (Mobile version) --}}
    @if ($canNav('switch-branch-scope'))
        <form method="POST" action="{{ route('admin.branch-scope') }}" class="dash-header-branch-form">
            @csrf
            <select name="branch_id" onchange="this.form.submit()" class="dash-header-branch-select">
                <option value="">{{ __('All') }}</option>
                @foreach (\App\Models\HotelBranch::query()->orderBy('name')->get() as $br)
                    <option value="{{ $br->id }}" @selected((string) session('director_branch_id') === (string) $br->id)>
                        {{ $br->name }}
                    </option>
                @endforeach
            </select>
        </form>
    @endif

    <div class="dash-header-spacer" style="flex: 1;"></div>

    {{-- Right Actions --}}
    <div class="dash-header-actions" style="gap: 8px;">
        <a href="{{ route('admin.search') }}" class="dash-mobile-search-btn">
            <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 110-16 8 8 0 010 16z"/></svg>
        </a>

        @if ($canNav('manage-dashboard-notifications'))
            <a href="{{ route('admin.notifications.index') }}" class="dash-header-alert-btn" title="{{ __('Notifications') }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                @if($navN > 0)
                    <span style="position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;padding:0 5px;border-radius:999px;background:#c41e3a;color:#fff;font-size:10px;font-weight:800;line-height:18px;text-align:center;">{{ $navN > 99 ? '99+' : $navN }}</span>
                @endif
            </a>
        @endif
        <form method="POST" action="{{ route('logout') }}" style="margin:0;">
            @csrf
            <button type="submit" class="dash-header-logout-btn" title="{{ __('Log out') }}" aria-label="{{ __('Log out') }}">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H9"/><path stroke-width="2" d="M13 20H6a2 2 0 01-2-2V6a2 2 0 012-2h7"/></svg>
            </button>
        </form>

        {{-- User Menu --}}
        <div class="dash-user-menu" x-data="{ open: false }">
            <button @click="open = !open" class="dash-user-menu__trigger" style="width: 32px; height: 32px; font-size: 13px;">
                {{ $userInitial }}
            </button>
            <div x-show="open" @click.outside="open = false" class="dash-user-menu__panel" style="right: 0; top: 40px;">
                <a href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
            </div>
        </div>
    </div>
</header>

    <div class="dash-breadcrumb-bar" aria-label="{{ __('Breadcrumb') }}">
        <a href="{{ auth()->user()->accountHomeUrl() }}">{{ __('Home') }}</a>
        <span class="sep">/</span>
        <a href="{{ route('admin.dashboard') }}">{{ __('Admin') }}</a>
        <span class="sep">/</span>
        <span class="current">
            @hasSection('breadcrumb')
                @yield('breadcrumb')
            @else
                @yield('title', __('Dashboard'))
            @endif
        </span>
    </div>

    <!-- Backdrop: Inafunga sidebar ukibonyeza popote nje ya menu -->
<div
    class="dash-sidebar-backdrop"
    x-show="sidebarOpen"
    x-cloak
    x-transition.opacity
    @click="sidebarOpen = false"
    style="display: none; cursor: pointer; pointer-events: auto;">
</div>

    <div class="dash-workspace">
        <aside class="dash-sidebar-outer" :class="{ 'is-open': sidebarOpen }">
            <div class="dash-sidebar-card">
                <nav class="dash-sidebar-nav-scroll" aria-label="{{ __('Admin menu') }}">
                    <a href="{{ route('admin.dashboard') }}" class="dash-nav-link {{ request()->routeIs('admin.dashboard') ? 'is-active' : '' }}">
                        <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        {{ __('Dashboard') }}
                    </a>

                    @if ($showOverview)
                        <div class="dash-nav-heading">{{ __('Overview') }}</div>
                        @if ($canNav('manage-bookings'))
                            <a href="{{ route('admin.payments.pending') }}" class="dash-nav-link {{ request()->routeIs('admin.payments.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/></svg>
                                {{ __('PendingPayments') }}
                            </a>
                        @endif
                        @if ($canNav('manage-hotel-services'))
                            <a href="{{ route('admin.hotel-services.index') }}" class="dash-nav-link {{ request()->routeIs('admin.hotel-services.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M4 6h16v4H4V6zm0 6h10v8H4v-8zm12 0h4v8h-4v-8z"/></svg>
                                {{ __('Services') }}
                            </a>
                        @endif
                    @endif

                    @if ($showProperty)
                        <div class="dash-nav-heading">{{ __('Property') }}</div>
                        @if ($canNav('manage-properties-directory'))
                            <a href="{{ route('admin.properties.index') }}" class="dash-nav-link {{ request()->routeIs('admin.properties.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M3 21h18M5 21V7l8-4v18M19 21V11l-6-4"/></svg>
                                {{ __('Properties') }}
                            </a>
                        @endif
                        @if ($canNav('manage-media-library'))
                            <a href="{{ route('admin.media-library.index') }}" class="dash-nav-link {{ request()->routeIs('admin.media-library.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M4 7h16v10H4zM9 7v10M15 7v10"/></svg>
                                {{ __('Media library') }}
                            </a>
                        @endif
                        @if ($canNav('manage-room-categories'))
                            <a href="{{ route('admin.room-types.index') }}" class="dash-nav-link {{ request()->routeIs('admin.room-types.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M4 6h16v4H4zM4 14h16v6H4z"/></svg>
                                {{ __('Room categories') }}
                            </a>
                        @endif
                    @endif

                    @if ($showOperations)
                        <div class="dash-nav-heading">{{ __('Operations') }}</div>
                        @if ($canNav('manage-bookings'))
                            <a href="{{ route('admin.bookings.index') }}" class="dash-nav-link {{ request()->routeIs('admin.bookings.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                {{ __('Bookings') }}
                            </a>
                        @endif
                        @if ($canNav('manage-payment-methods'))
                            <a href="{{ route('admin.payment-methods.index') }}" class="dash-nav-link {{ request()->routeIs('admin.payment-methods.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M3 7h18v10H3zM7 11h2m2 0h6"/></svg>
                                {{ __('Payment methods') }}
                            </a>
                        @endif
                        @if ($canNav('manage-customers'))
                            <a href="{{ route('admin.customers.index') }}" class="dash-nav-link {{ request()->routeIs('admin.customers.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8z"/></svg>
                                {{ __('Guests') }}
                            </a>
                        @endif
                        @if ($canNav('manage-maintenance'))
                            <a href="{{ route('admin.maintenance.index') }}" class="dash-nav-link {{ request()->routeIs('admin.maintenance.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M14.7 6.3a1 1 0 000 1.4l1.6 1.6a1 1 0 001.4 0l3.77-3.77a6 6 0 01-7.94 7.94l-6.91 6.91a2.12 2.12 0 01-3-3l6.91-6.91a6 6 0 017.94-7.94l-3.76 3.76z"/></svg>
                                {{ __('Maintenance') }}
                            </a>
                        @endif
                        @if ($showKitchen)
                            <a href="{{ route('kitchen.dashboard') }}" target="_blank" rel="noopener" class="dash-nav-link {{ request()->routeIs('kitchen.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M8 3v8M16 3v8M5 21h14M7 11h10M8 21v-4a2 2 0 012-2h4a2 2 0 012 2v4"/></svg>
                                {{ __('Kitchen dashboard') }}
                            </a>
                        @endif
                        @if ($canNav('access-reception-panel'))
                            <a href="{{ route('reception.dashboard') }}" target="_blank" rel="noopener" class="dash-nav-link {{ request()->routeIs('reception.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M3 10h18M6 6h12M8 14h8M5 18h14"/></svg>
                                {{ __('Reception dashboard') }}
                            </a>
                        @endif
                        <a href="{{ route('dashboard', ['guest_view' => 1]) }}" target="_blank" rel="noopener" class="dash-nav-link {{ request()->routeIs('dashboard') && request()->boolean('guest_view') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M5 4h14a2 2 0 012 2v12a2 2 0 01-2 2H9l-4 4V6a2 2 0 012-2z"/></svg>
                            {{ __('Guest dashboard') }}
                        </a>
                    @endif

                    @if ($showCommunication)
                        <div class="dash-nav-heading">{{ __('Communication') }}</div>
                        @if ($canNav('manage-contacts'))
                            <a href="{{ route('admin.contacts.index') }}" class="dash-nav-link {{ request()->routeIs('admin.contacts.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M21 15a4 4 0 01-4 4H7l-4 4V7a4 4 0 014-4h10a4 4 0 014 4v8z"/></svg>
                                {{ __('Messages') }}
                            </a>
                        @endif
                        @if ($canNav('manage-newsletters'))
                            <a href="{{ route('admin.emails.index') }}" class="dash-nav-link {{ request()->routeIs('admin.emails.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                {{ __('Emails') }}
                            </a>
                        @endif
                    @endif

                    @if ($showReports)
                        <div class="dash-nav-heading">{{ __('Reports') }}</div>
                        <a href="{{ route('admin.reports.index') }}" class="dash-nav-link {{ request()->routeIs('admin.reports.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M4 19V5M8 19v-6m4 6V9m4 10V5"/></svg>
                            {{ __('Reports') }}
                        </a>
                    @endif

                    @if ($showConfiguration)
                        <div class="dash-nav-heading">{{ __('Configuration') }}</div>
                        <a href="{{ route('admin.settings.edit') }}" class="dash-nav-link {{ request()->routeIs('admin.settings.*') ? 'is-active' : '' }}">
                            <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path stroke-width="2" d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-2 2 2 2 0 01-2-2v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83 0 2 2 0 010-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 01-2-2 2 2 0 012-2h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 010-2.83 2 2 0 012.83 0l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 012-2 2 2 0 012 2v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 0 2 2 0 010 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 012 2 2 2 0 01-2 2h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                            {{ __('System settings') }}
                        </a>
                    @endif

                @if ($showAccess)
                    <div class="dash-nav-heading">{{ __('Access') }}</div>
                        @if ($canNav('manage-users', 'manage-staff-users'))
                            <a href="{{ route('admin.users.index') }}" class="dash-nav-link {{ request()->routeIs('admin.users.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2M9 11a4 4 0 100-8 4 4 0 000 8zM23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                                {{ __('Users') }}
                            </a>
                        @endif
                        @if ($canNav('manage-roles'))
                            <a href="{{ route('admin.roles.index') }}" class="dash-nav-link {{ request()->routeIs('admin.roles.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M12 11c1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3 1.34 3 3 3zM4 20c0-3.31 5.33-5 8-5s8 1.69 8 5"/></svg>
                                {{ __('Roles') }}
                            </a>
                        @endif
                        @if ($canNav('manage-permissions'))
                            <a href="{{ route('admin.permissions.index') }}" class="dash-nav-link {{ request()->routeIs('admin.permissions.*') ? 'is-active' : '' }}">
                                <svg class="dash-nav-ico" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path stroke-width="2" d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                                {{ __('Permissions') }}
                            </a>
                        @endif
                    @endif
                </nav>
            </div>
        </aside>

        <div class="dash-content-outer">
            @include('partials.dashboard-preloader')
            <div class="dash-content-scroll">
                <div class="dash-content-card">
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



    (function (showLabel, hideLabel) {
        document.querySelectorAll('.js-password-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var wrap = btn.closest('.auth-password-wrap');
                if (!wrap) return;
                var input = wrap.querySelector('input');
                if (!input) return;
                var revealed = wrap.classList.toggle('is-revealed');
                input.type = revealed ? 'text' : 'password';
                btn.setAttribute('aria-pressed', revealed ? 'true' : 'false');
                btn.setAttribute('aria-label', revealed ? hideLabel : showLabel);
            });
        });
    })(@json(__('Show password')), @json(__('Hide password')));

    (function () {
        var preloader = document.querySelector('[data-dash-preloader]');
        if (preloader) {
            var hidePreloader = function () { preloader.classList.add('is-hidden'); };
            hidePreloader();
            window.addEventListener('load', function () { setTimeout(hidePreloader, 180); });
        }
    })();

    (function () {
        document.querySelectorAll('.dash-sidebar-nav-scroll a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth <= 1023) {
                    document.querySelectorAll('.dash-sidebar-outer').forEach(function (el) { el.classList.remove('is-open'); });
                }
            });
        });
    })();

    (function () {
        document.querySelectorAll('form[data-autosave-key]').forEach(function (form) {
            var key = 'autosave:' + form.getAttribute('data-autosave-key');
            var load = localStorage.getItem(key);
            if (load) {
                try {
                    var payload = JSON.parse(load);
                    Object.keys(payload).forEach(function (name) {
                        var field = form.querySelector('[name="' + name.replace(/"/g, '\\"') + '"]');
                        if (!field || field.type === 'file' || field.dataset.noAutosave === '1') return;
                        if (field.type === 'checkbox') field.checked = !!payload[name];
                        else field.value = payload[name];
                    });
                } catch (e) {}
            }
            var save = function () {
                var payload = {};
                form.querySelectorAll('input,textarea,select').forEach(function (field) {
                    if (!field.name || field.type === 'file' || field.dataset.noAutosave === '1') return;
                    payload[field.name] = field.type === 'checkbox' ? field.checked : field.value;
                });
                localStorage.setItem(key, JSON.stringify(payload));
            };
            form.addEventListener('input', save);
            form.addEventListener('change', save);
            form.addEventListener('submit', function () { localStorage.removeItem(key); });
        });
    })();

    (function () {
        if (!window.flatpickr) return;
        document.querySelectorAll('input[type="date"]:not([data-native-date="1"])').forEach(function (input) {
            if (input.dataset.flatpickrInitialized === '1') return;
            var minDate = input.getAttribute('min') || null;
            var maxDate = input.getAttribute('max') || null;
            var defaultDate = input.value || null;
            input.dataset.flatpickrInitialized = '1';
            input.type = 'text';
            flatpickr(input, {
                dateFormat: 'Y-m-d',
                defaultDate: defaultDate,
                minDate: minDate,
                maxDate: maxDate,
                allowInput: true,
                disableMobile: true
            });
        });
    })();

    (function () {
        document.querySelectorAll('.admin-table').forEach(function (table) {
            if (table.dataset.enhancedFilter === '1') return;
            table.dataset.enhancedFilter = '1';
            var bodyRows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
            if (!bodyRows.length) return;
            var wrap = document.createElement('div');
            wrap.style.display = 'flex';
            wrap.style.flexWrap = 'wrap';
            wrap.style.gap = '.5rem';
            wrap.style.alignItems = 'center';
            wrap.style.margin = '0 0 .6rem';
            var search = document.createElement('input');
            search.type = 'search';
            search.placeholder = 'Quick filter table...';
            search.style.minWidth = '220px';
            search.style.padding = '.45rem .55rem';
            search.style.border = '1px solid #d1d5db';
            search.style.borderRadius = '8px';
            var clear = document.createElement('button');
            clear.type = 'button';
            clear.textContent = 'Clear';
            clear.className = 'dash-btn dash-btn--ghost';
            var apply = function () {
                var q = (search.value || '').toLowerCase().trim();
                bodyRows.forEach(function (row) {
                    var txt = (row.innerText || '').toLowerCase();
                    row.style.display = (!q || txt.indexOf(q) !== -1) ? '' : 'none';
                });
            };
            search.addEventListener('input', apply);
            clear.addEventListener('click', function () { search.value = ''; apply(); });
            wrap.appendChild(search);
            wrap.appendChild(clear);
            table.parentNode.insertBefore(wrap, table);
        });
    })();
</script>
</body>
</html>
