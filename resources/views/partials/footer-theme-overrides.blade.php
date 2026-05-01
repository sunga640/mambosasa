@php
    $dashboardThemeMode = $dashboardSettings->dashboard_theme_mode ?? 'system';
    if (! in_array($dashboardThemeMode, ['light', 'dark', 'system'], true)) {
        $dashboardThemeMode = 'system';
    }
@endphp
<script>
    (function () {
        var mode = @json($dashboardThemeMode);
        var root = document.documentElement;
        var media = window.matchMedia ? window.matchMedia('(prefers-color-scheme: dark)') : null;

        function resolveTheme() {
            if (mode === 'light' || mode === 'dark') {
                return mode;
            }

            return media && media.matches ? 'dark' : 'light';
        }

        function applyTheme() {
            root.setAttribute('data-dashboard-theme', resolveTheme());
            root.setAttribute('data-dashboard-theme-mode', mode);
        }

        applyTheme();

        if (mode === 'system' && media && media.addEventListener) {
            media.addEventListener('change', applyTheme);
        } else if (mode === 'system' && media && media.addListener) {
            media.addListener(applyTheme);
        }
    })();
</script>
<style>
    html[data-dashboard-theme="dark"] {
        --brand-theme-bg: #101827;
        --brand-theme-bg-soft: #162133;
        --brand-theme-surface: #23262b;
        --brand-theme-surface-soft: #2c3138;
        --brand-theme-surface-card: #30353d;
        --brand-theme-heading: #7dd3fc;
        --brand-theme-text: #d9e1ea;
        --brand-theme-muted: #a3b0c2;
        --brand-theme-border: rgba(125, 211, 252, 0.18);
        --brand-theme-highlight: rgba(56, 189, 248, 0.14);
        --brand-theme-accent: #38bdf8;
        --brand-theme-topbar: rgba(32, 36, 42, 0.94);
        --brand-theme-shadow: 0 18px 40px rgba(2, 6, 23, 0.32);
        color-scheme: dark;
    }

    html[data-dashboard-theme="light"] {
        --brand-theme-bg: #e9eff5;
        --brand-theme-bg-soft: #dfe8f1;
        --brand-theme-surface: #eef3f8;
        --brand-theme-surface-soft: #e3ebf3;
        --brand-theme-surface-card: #f2f6fa;
        --brand-theme-heading: #19506a;
        --brand-theme-text: #243746;
        --brand-theme-muted: #6d7f8c;
        --brand-theme-border: rgba(27, 79, 113, 0.14);
        --brand-theme-highlight: rgba(56, 189, 248, 0.08);
        --brand-theme-accent: #1f7ea4;
        --brand-theme-topbar: rgba(238, 243, 248, 0.96);
        --brand-theme-shadow: 0 14px 32px rgba(15, 23, 42, 0.08);
        color-scheme: light;
    }

    :root {
        --color-accent-1: #1f5f74;
        --color-accent-2: #a5f3fc;
    }

    body.dash-body,
    body.auth-theme-body,
    body.kitchen-body {
        background:
            linear-gradient(180deg, var(--brand-theme-bg) 0%, var(--brand-theme-bg-soft) 100%) !important;
        color: var(--brand-theme-text) !important;
    }

    html,
    body,
    .dash-content-scroll,
    .dash-sidebar-nav-scroll {
        scrollbar-width: thin;
        scrollbar-color: var(--brand-theme-accent) var(--brand-theme-surface);
    }

    .auth-theme-body .auth-panel,
    .auth-theme-body .auth-panel__card,
    .auth-theme-body .auth-copy p,
    .auth-theme-body .account-link {
        color: var(--brand-theme-text) !important;
    }

    .auth-theme-body .auth-panel,
    .auth-theme-body .auth-visual {
        background: var(--brand-theme-bg) !important;
    }

    .auth-theme-body .auth-visual::before {
        display: none !important;
    }

    .auth-theme-body .auth-copy h1,
    .auth-theme-body .auth-brand__name,
    .auth-theme-body label {
        color: var(--brand-theme-heading) !important;
    }

    .auth-theme-body .auth-copy p,
    .auth-theme-body .account-link,
    .auth-theme-body .password-toggle-btn {
        color: var(--brand-theme-muted) !important;
    }

    .auth-theme-body .account-field-input,
    .auth-theme-body input[type="text"],
    .auth-theme-body input[type="email"],
    .auth-theme-body input[type="password"] {
        border-bottom-color: var(--brand-theme-border) !important;
        color: var(--brand-theme-text) !important;
    }

    .auth-theme-body .btn-account-primary,
    .auth-theme-body button[type="submit"],
    .auth-theme-body .dash-btn--primary {
        background: linear-gradient(135deg, #1f5f74, #2f7f98) !important;
        color: #ecfeff !important;
        box-shadow: none !important;
    }

    body.dash-body {
        --dash-crumb-h: 0px !important;
        --dash-footer-h: 0px !important;
        --dash-sidebar-w: 280px !important;
        --dash-header-h: 64px !important;
        --dash-brand-forest: var(--brand-theme-accent) !important;
        --dash-brand-gold: var(--brand-theme-heading) !important;
        --dash-brand-cream: var(--brand-theme-surface-soft) !important;
    }

    .dash-breadcrumb-bar,
    .dash-site-footer,
    .dash-theme-btn {
        display: none !important;
    }

    .dash-header-bar {
        position: fixed !important;
        top: 0 !important;
        left: var(--dash-sidebar-w) !important;
        right: 0 !important;
        height: var(--dash-header-h) !important;
        background: var(--brand-theme-bg) !important;
        color: var(--brand-theme-text) !important;
        border: 0 !important;
        border-bottom: 1px solid var(--brand-theme-border) !important;
        border-radius: 0 !important;
        box-shadow: none !important;
        backdrop-filter: none !important;
        z-index: 1005 !important;
        padding: 0 1.15rem !important;
    }

    .dash-header-bar__brand {
        max-width: min(50vw, 820px) !important;
    }

    .dash-header-brand-text {
        color: var(--brand-theme-heading) !important;
    }

    .dash-workspace {
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        display: flex !important;
    }

    .dash-sidebar-backdrop {
        top: 0 !important;
        bottom: 0 !important;
    }

    .dash-sidebar-outer {
        padding: .75rem !important;
        background: var(--brand-theme-bg) !important;
        border-right: 1px solid var(--brand-theme-border) !important;
        width: var(--dash-sidebar-w) !important;
        height: 100vh !important;
    }

    .dash-sidebar-card {
        background: transparent !important;
        border: 0 !important;
        border-right: 0 !important;
        border-radius: 0 !important;
        box-shadow: none !important;
    }

    .dash-sidebar-nav-scroll {
        background: transparent !important;
        padding: .25rem 0 .55rem !important;
        scrollbar-color: var(--brand-theme-accent) var(--brand-theme-bg);
    }

    .dash-nav-heading {
        display: none !important;
    }

    .dash-nav-link {
        color: var(--brand-theme-text) !important;
        border: 1px solid transparent !important;
        background: transparent !important;
        border-radius: 8px !important;
        margin: 0 0 .35rem !important;
        padding: .62rem .72rem !important;
        min-height: 40px !important;
        font-size: .82rem !important;
        box-shadow: none !important;
    }

    .dash-nav-link:hover {
        background: var(--brand-theme-surface-soft) !important;
        border-color: color-mix(in srgb, var(--brand-theme-accent) 25%, transparent) !important;
    }

    .dash-nav-link.is-active {
        color: var(--brand-theme-heading) !important;
        background: var(--brand-theme-highlight) !important;
        border-color: color-mix(in srgb, var(--brand-theme-accent) 32%, transparent) !important;
        border-left: 3px solid var(--brand-theme-accent) !important;
        padding-left: calc(.72rem - 3px) !important;
    }

    .dash-nav-ico {
        width: 15px !important;
        height: 15px !important;
    }

    .dash-sidebar-logout {
        margin: auto .9rem .95rem !important;
        padding: .8rem 0 0 !important;
        background: transparent !important;
        border-top: 1px solid var(--brand-theme-border) !important;
    }

    .dash-sidebar-logout button,
    .dash-btn--primary,
    .dash-header-search button {
        background: linear-gradient(135deg, #1f5f74, #2f7f98) !important;
        color: #ecfeff !important;
        border-color: rgba(125, 211, 252, 0.2) !important;
        border-radius: 12px !important;
        box-shadow: none !important;
    }

    .dash-content-outer {
        background: transparent !important;
        padding: calc(var(--dash-header-h) + 1rem) 1.4rem 1.4rem !important;
        min-width: 0 !important;
    }

    .dash-content-scroll {
        overflow-y: auto !important;
    }

    .dash-content-card {
        background: var(--brand-theme-surface) !important;
        color: var(--brand-theme-text) !important;
        border: 1px solid var(--brand-theme-border) !important;
        border-radius: 16px !important;
        padding: 1.2rem 1.2rem !important;
        box-shadow: none !important;
        min-height: calc(100vh - var(--dash-header-h) - 2.3rem) !important;
    }

    .dash-page-preloader,
    .dash-user-menu__panel,
    .dash-header-search,
    .dash-header-alert-btn,
    .dash-header-logout-btn,
    .dash-mobile-search-btn,
    .dash-menu-btn,
    .dash-user-menu__trigger,
    .report-kpi,
    .report-filters,
    .report-chart-wrap,
    .report-hub-card,
    .admin-table thead th,
    .admin-table tbody tr:nth-child(odd),
    .admin-table tbody tr:nth-child(even),
    .dash-user-menu__panel form button,
    .dash-btn--ghost {
        background: var(--brand-theme-surface-soft) !important;
        color: var(--brand-theme-text) !important;
        border-color: var(--brand-theme-border) !important;
    }

    .dash-header-search input[type="search"],
    .dash-header-branch-select,
    .dash-content-card input[type="search"],
    .dash-content-card input[type="text"],
    .dash-content-card input[type="email"],
    .dash-content-card input[type="password"],
    .dash-content-card input[type="url"],
    .dash-content-card input[type="number"],
    .dash-content-card input[type="date"],
    .dash-content-card input[type="month"],
    .dash-content-card input[type="time"],
    .dash-content-card select,
    .dash-content-card textarea,
    .form-row input[type="search"],
    .form-row input[type="text"],
    .form-row input[type="email"],
    .form-row input[type="password"],
    .form-row input[type="url"],
    .form-row input[type="number"],
    .form-row input[type="date"],
    .form-row input[type="month"],
    .form-row input[type="time"],
    .form-row select,
    .form-row textarea,
    .report-filters input,
    .report-filters select {
        background: var(--brand-theme-surface-soft) !important;
        color: var(--brand-theme-text) !important;
        border: 1px solid var(--brand-theme-border) !important;
    }

    .dash-header-branch-select option,
    .dash-content-card select option,
    .report-filters select option {
        background: var(--brand-theme-surface-soft) !important;
        color: var(--brand-theme-text) !important;
    }

    .dash-header-bar .dash-header-branch-select,
    .dash-header-bar select,
    .dash-header-bar button,
    .dash-header-bar .dash-menu-btn,
    .dash-header-bar .dash-header-alert-btn,
    .dash-header-bar .dash-header-logout-btn,
    .dash-header-bar .dash-user-menu__trigger,
    .dash-header-bar .dash-mobile-search-btn {
        background: var(--brand-theme-surface-soft) !important;
        color: var(--brand-theme-text) !important;
        border-color: var(--brand-theme-border) !important;
    }

    .hotel-branches-card {
        background: var(--brand-theme-surface-card) !important;
        border-color: var(--brand-theme-border) !important;
        color: var(--brand-theme-text) !important;
        box-shadow: none !important;
    }

    .hotel-branches-card [style*="color:#64748b"],
    .hotel-branches-card [style*="color: #64748b"] {
        color: var(--brand-theme-muted) !important;
    }

    .dash-content-card input[type="time"],
    .form-row input[type="time"] {
        color-scheme: auto;
        appearance: none;
        min-height: 42px;
    }

    .dash-content-card input[type="file"],
    .form-row input[type="file"] {
        background: var(--brand-theme-surface-soft) !important;
        color: var(--brand-theme-text) !important;
        border: 1px solid var(--brand-theme-border) !important;
        border-radius: 12px !important;
        padding: 0.62rem 0.75rem !important;
    }

    .dash-content-card input::placeholder,
    .dash-header-search input::placeholder,
    .dash-content-card textarea::placeholder {
        color: var(--brand-theme-muted) !important;
        opacity: 1 !important;
    }

    .dash-header-bar__brand,
    .dash-page-preloader__label,
    .dash-user-menu__who strong,
    .report-kpi__val,
    .dash-content-card h1,
    .dash-content-card h2,
    .dash-content-card h3,
    .dash-content-card h4,
    .dash-content-card h5,
    .dash-content-card h6 {
        color: var(--brand-theme-heading) !important;
    }

    .dash-nav-link,
    .dash-user-menu__panel a,
    .dash-user-menu__who,
    .report-kpi__lab,
    .dash-content-card,
    .dash-content-card p,
    .dash-content-card li,
    .dash-content-card label,
    .dash-content-card th,
    .dash-content-card td,
    .dash-content-card small,
    .dash-content-card strong {
        color: var(--brand-theme-text) !important;
    }

    .dash-content-card .text-light-1,
    .dash-content-card [style*="opacity:.7"],
    .dash-content-card [style*="opacity: .7"],
    .dash-content-card [style*="opacity:.75"],
    .dash-content-card [style*="opacity: .75"],
    .dash-content-card [style*="opacity:.8"],
    .dash-content-card [style*="opacity: .8"] {
        color: var(--brand-theme-muted) !important;
    }

    .dash-content-card [style*="background:#fff"],
    .dash-content-card [style*="background:rgb(255,255,255)"],
    .dash-content-card [style*="background: rgb(255,255,255)"],
    .dash-content-card [style*="background: #fff"],
    .dash-content-card [style*="background:#ffffff"],
    .dash-content-card [style*="background: #ffffff"],
    .dash-content-card [style*="background:#f8fafc"],
    .dash-content-card [style*="background: #f8fafc"],
    .dash-content-card [style*="background:#eff6ff"],
    .dash-content-card [style*="background: #eff6ff"],
    .dash-content-card [style*="background:#f0fdf4"],
    .dash-content-card [style*="background: #f0fdf4"],
    .dash-content-card [style*="background:#fffbeb"],
    .dash-content-card [style*="background: #fffbeb"],
    .dash-content-card [style*="background:#fef2f2"],
    .dash-content-card [style*="background: #fef2f2"],
    .dash-content-card [style*="background:#fff1f2"],
    .dash-content-card [style*="background: #fff1f2"],
    .dash-content-card [style*="background:#ecfdf5"],
    .dash-content-card [style*="background: #ecfdf5"],
    .dash-content-card [style*="background:#fff5f5"],
    .dash-content-card [style*="background: #fff5f5"],
    .dash-content-card [style*="background:#fafafa"],
    .dash-content-card [style*="background: #fafafa"],
    .dash-content-card .bg-white,
    .dash-content-card .bg-light-1,
    .dash-content-card .bg-light-2,
    .dash-content-card .content-box,
    .dash-content-card .stat-card-work,
    .dash-content-card .settings-preview-card,
    .dash-content-card .report-card,
    .dash-content-card .booking-days-modal-panel {
        background: var(--brand-theme-surface-card) !important;
        border-color: var(--brand-theme-border) !important;
        color: var(--brand-theme-text) !important;
    }

    .dash-content-card [style*="background:#fff7ed"],
    .dash-content-card [style*="background: #fff7ed"],
    .dash-content-card [style*="background:#fff5f5"],
    .dash-content-card [style*="background: #fff5f5"],
    .dash-content-card [style*="background:#fffbeb"],
    .dash-content-card [style*="background: #fffbeb"],
    .dash-content-card [style*="background:#fff1f2"],
    .dash-content-card [style*="background: #fff1f2"] {
        color: var(--brand-theme-text) !important;
    }

    .dash-content-card [style*="box-shadow: 0 1px 2px rgba(0,0,0,0.05)"],
    .dash-content-card [style*="box-shadow:0 1px 2px rgba(0,0,0,0.05)"] {
        box-shadow: none !important;
    }

    .dash-content-card [style*="Hotel branches"],
    .dash-content-card .hotel-branches-card {
        background: var(--brand-theme-surface-card) !important;
    }

    .media-picker-shell {
        border: 1px solid var(--brand-theme-border) !important;
        border-radius: 12px;
        background: var(--brand-theme-surface-soft) !important;
        padding: 0.75rem 0.85rem;
    }

    .media-picker-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 10px;
        max-height: 300px;
        overflow: auto;
        padding: 10px 0 0;
    }

    .media-picker-option {
        border: 1px solid var(--brand-theme-border);
        border-radius: 10px;
        padding: 8px;
        background: var(--brand-theme-surface-card);
        cursor: pointer;
    }

    .media-picker-option.is-selected {
        border-color: var(--brand-theme-accent);
        box-shadow: inset 0 0 0 1px color-mix(in srgb, var(--brand-theme-accent) 32%, transparent);
    }

    .dash-page-preloader__spinner {
        border-color: color-mix(in srgb, var(--brand-theme-accent) 18%, transparent) !important;
        border-top-color: var(--brand-theme-heading) !important;
    }

    .admin-table tbody tr:hover td {
        background: var(--brand-theme-highlight) !important;
    }

    .k-sidebar {
        background: linear-gradient(180deg, var(--brand-theme-bg) 0%, var(--brand-theme-bg-soft) 100%) !important;
        border-right-color: transparent !important;
    }

    .k-nav a {
        background: transparent !important;
        border: 0 !important;
        box-shadow: none !important;
        color: var(--brand-theme-text) !important;
        border-radius: 8px !important;
    }

    .k-nav a:hover {
        background: var(--brand-theme-highlight) !important;
        border-color: transparent !important;
        transform: none !important;
    }

    .k-nav a.is-active {
        background: var(--brand-theme-highlight) !important;
        color: var(--brand-theme-heading) !important;
        border-left: 3px solid var(--brand-theme-accent) !important;
        padding-left: calc(.72rem - 3px) !important;
    }

    .k-logout-btn,
    .k-top-card,
    .k-notify-btn {
        background: var(--brand-theme-surface) !important;
        color: var(--brand-theme-text) !important;
        border-color: var(--brand-theme-border) !important;
    }

    @media (max-width: 1023px) {
        .dash-header-bar {
            left: .75rem !important;
            right: .75rem !important;
            top: .75rem !important;
            border: 1px solid var(--brand-theme-border) !important;
            border-radius: 14px !important;
            background: var(--brand-theme-topbar) !important;
        }

        .dash-sidebar-outer {
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            bottom: 0 !important;
            width: min(84vw, 320px) !important;
            transform: translateX(-100%);
            z-index: 1200 !important;
            padding: .75rem !important;
            background: var(--brand-theme-bg) !important;
        }

        .dash-sidebar-outer.is-open {
            transform: translateX(0);
        }

        .dash-content-outer {
            padding: calc(var(--dash-header-h) + 1.35rem) .75rem .75rem !important;
        }

        .dash-content-card {
            min-height: calc(100vh - var(--dash-header-h) - 1.9rem) !important;
            padding: .95rem !important;
        }
    }

    @media (max-width: 767px) {
        .dash-header-bar {
            gap: .45rem !important;
            padding: 0 .7rem !important;
        }

        .dash-header-brand-wrap {
            gap: .45rem !important;
        }

        .dash-header-brand-text {
            display: none !important;
        }

        .dash-header-bar__brand {
            max-width: 52px !important;
            flex: 0 0 auto !important;
        }

        .dash-header-brand-wrap img {
            width: 34px !important;
            height: 34px !important;
        }

        .dash-header-actions {
            gap: .35rem !important;
        }

        .dash-header-branch-form {
            margin-left: 0 !important;
            min-width: 0 !important;
            flex: 1 1 auto !important;
        }

        .dash-header-branch-select {
            min-width: 0 !important;
            width: 100% !important;
            max-width: 110px !important;
            padding: 7px 26px 7px 9px !important;
            font-size: 12px !important;
        }

        .dash-header-alert-btn,
        .dash-header-logout-btn,
        .dash-user-menu__trigger,
        .dash-menu-btn,
        .dash-mobile-search-btn {
            width: 38px !important;
            height: 38px !important;
        }

        .hotel-branches-card {
            display: flex !important;
            width: 100% !important;
            justify-content: space-between !important;
            flex-wrap: wrap !important;
        }
    }
</style>
