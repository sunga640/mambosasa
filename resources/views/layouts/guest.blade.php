<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/vendors.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/account-bridge.css') }}">
    @include('partials.footer-theme-overrides')
    <title>@isset($layoutTitle){{ $layoutTitle }} - @endisset{{ $dashboardSettings->hotelDisplayName() }}</title>
    <style>
        :root {
            --auth-ink: #122223;
            --auth-muted: #6b7280;
            --auth-gold: #d5ac42;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Jost', sans-serif;
            color: #d9e1ea;
            background: #23262b;
        }
        .auth-shell {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        .auth-panel {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: clamp(1.5rem, 4vw, 4.5rem);
            background: transparent;
            width: 100%;
        }
        .auth-panel__card {
            width: min(100%, 31.5rem);
        }
        .auth-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            text-decoration: none;
            color: inherit;
        }
        .auth-brand img {
            max-height: 4.7rem;
            width: auto;
            object-fit: contain;
            margin-bottom: 0.8rem;
        }
        .auth-brand__name {
            display: block;
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(2rem, 3.4vw, 3rem);
            line-height: 0.98;
            font-weight: 700;
        }
        .auth-copy {
            margin: 2rem 0 2.1rem;
        }
        .auth-copy h1 {
            margin: 0 0 0.6rem;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.16em;
            text-transform: uppercase;
            color: var(--auth-gold);
        }
        .auth-copy p {
            margin: 0;
            color: #c7d2fe;
            line-height: 1.75;
        }
        .account-field-stack {
            margin-bottom: 1.7rem;
        }
        label {
            display: inline-block;
            margin-bottom: 0.7rem;
            font-size: 0.86rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--auth-gold);
        }
        .account-field-input,
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.9rem 0.05rem !important;
            border: none !important;
            border-bottom: 1px solid rgba(213, 172, 66, 0.4) !important;
            border-radius: 0 !important;
            background: transparent !important;
            color: #f8fafc !important;
            font-size: 1rem !important;
            transition: border-color 0.22s ease;
        }
        .account-field-input:focus,
        input:focus {
            outline: none !important;
            border-bottom-color: rgba(213, 172, 66, 0.9) !important;
            box-shadow: none !important;
        }
        .password-wrapper {
            position: relative;
            display: flex;
            align-items: center;
        }
        .password-wrapper .account-field-input {
            padding-right: 2.8rem !important;
        }
        .password-toggle-btn {
            position: absolute;
            right: 0;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #cbd5e1;
            cursor: pointer;
            padding: 0.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .btn-account-primary,
        button[type="submit"],
        .dash-btn--primary {
            width: 100%;
            margin-top: 1.6rem;
            padding: 1rem 1.2rem !important;
            background: #d5ac42 !important;
            color: #111827 !important;
            border: none !important;
            border-radius: 999px !important;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            font-weight: 700;
            cursor: pointer;
            transition: transform 0.22s ease, box-shadow 0.22s ease;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.2);
        }
        .btn-account-primary:hover,
        button[type="submit"]:hover,
        .dash-btn--primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 18px 40px rgba(0, 0, 0, 0.24);
        }
        .account-link {
            color: #cbd5e1;
            text-decoration: none;
        }
        .account-link:hover {
            color: #ffffff;
        }
        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus,
        input:-webkit-autofill:active {
            -webkit-box-shadow: 0 0 0 1000px #23262b inset !important;
            -webkit-text-fill-color: #f8fafc !important;
            transition: background-color 5000s ease-in-out 0s;
        }
        @media (max-width: 1023px) {
            .auth-shell { padding: 1rem; }
        }
        @media (max-width: 767px) {
            .auth-panel {
                padding: 1.25rem;
            }
            .auth-brand__name {
                font-size: clamp(1.8rem, 9vw, 2.45rem);
            }
        }
    </style>
</head>
<body class="auth-theme-body">
    @php
        $authCompanyName = trim((string) ($dashboardSettings->company_name ?? ''));
        if ($authCompanyName === '') {
            $authCompanyName = $dashboardSettings->hotelDisplayName();
        }
    @endphp

    <div class="auth-shell">
        <main class="auth-panel">
            <div class="auth-panel__card">

                <div class="auth-copy">
                    <h1>@isset($layoutTitle){{ $layoutTitle }}@else{{ __('Welcome back') }}@endisset</h1>
                    <p>{{ __('Use your account details below to continue into the guest area.') }}</p>
                </div>

                {{ $slot }}
            </div>
        </main>
    </div>

    @include('partials.swal')
    @include('partials.client-security-deter')
</body>
</html>
