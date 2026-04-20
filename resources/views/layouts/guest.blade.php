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
    <title>@isset($layoutTitle){{ $layoutTitle }} — @endisset{{ $dashboardSettings->hotelDisplayName() }}</title>

    <style>
        /* Minimalist Auth Style */
        body {
            background-color: #ffffff; /* Background nyeupe safi */
            color: #122223;
            font-family: 'Jost', sans-serif;
        }
        .auth-minimal-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.25rem;
        }
        .auth-minimal-content {
            width: 100%;
            max-width: 400px; /* Upana wa fomu */
        }
        /* Kuondoa muonekano wa container/card */
        .auth-minimal-form-container {
            background: transparent;
            box-shadow: none;
            border: none;
            padding: 0;
        }.auth-brand-name {
            font-family: 'Cormorant Garamond', Georgia, serif;
            font-size: clamp(1.4rem, 2.5vw, 1.9rem); /* Imepunguzwa kidogo ili kutosha mstari mmoja */
            font-weight: 600;
            color: #122223;
            margin-top: 1rem;
            display: block;
            white-space: nowrap; /* HII NDIO INAFANYA ISOMEKE MSTARI MMOJA */
        }
        /* Mitindo ya Input ili zionekane vizuri kwenye plain background */
        input[type="text"], input[type="email"], input[type="password"] {
            border: none !important;
            border-bottom: 1px solid #e2e8f0 !important; /* Mstari wa chini tu */
            border-radius: 0 !important;
            padding: 0.8rem 0 !important;
            background: transparent !important;
            transition: border-color 0.3s;
        }
        input:focus {
            border-bottom-color: #122223 !important;
            box-shadow: none !important;
        }
        .account-field-stack {
            margin-bottom: 1.5rem;
        }
        label {
            font-weight: 500;
            font-size: 0.9rem;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

    /* Staili ya Button */
    button[type="submit"], .dash-btn--primary {
        width: 100%;
        margin-top: 2rem;
        padding: 0.9rem !important;
        background-color: #122223 !important; /* Rangi ya giza uliyotumia */
        color: #ffffff !important;
        border: none !important;
        border-radius: 50px !important; /* Inafanya iwe na duara (Pill shape) */
        text-transform: uppercase;
        letter-spacing: 0.15em;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease; /* Inafanya hover iwe laini */
    }

    /* Hover Effect ya Button */
    button[type="submit"]:hover {
        background-color: #2a3d3e !important; /* Rangi inabadilika kidogo ukisogeza mouse */
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        transform: translateY(-1px); /* Inanyanyuka kidogo */
    }

    /* Mpangilio wa Input na Icon ya Jicho */
    .password-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .password-wrapper input {
        width: 100%;
        padding-right: 40px !important; /* Acha nafasi ya icon */
    }

    .password-toggle-btn {
        position: absolute;
        right: 0;
        bottom: 8px;
        background: none;
        border: none;
        color: #64748b;
        cursor: pointer;
        padding: 5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .password-toggle-btn:hover {
        color: #122223;
    }
    /* Inazuia rangi ya bluu/njano wakati browser inajaza email/password yenyewe */
input:-webkit-autofill,
input:-webkit-autofill:hover,
input:-webkit-autofill:focus,
input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0px 1000px white inset !important;
    -webkit-text-fill-color: #122223 !important;
    transition: background-color 5000s ease-in-out 0s;
}
    </style>
</head>
<body>
    @php
        $authCompanyName = trim((string) ($dashboardSettings->company_name ?? ''));
        if ($authCompanyName === '') {
            $authCompanyName = $dashboardSettings->hotelDisplayName();
        }
    @endphp

    <div class="auth-minimal-wrapper">
        <div class="auth-minimal-content">
            <!-- Logo Section -->
            <div style="text-align:center; margin-bottom: 3rem;">
                <a href="{{ route('site.home') }}" style="text-decoration:none;">
                    <img src="{{ $dashboardSettings->headerLogoUrl() }}" alt="" style="max-height:70px; width:auto;">
                    <strong class="auth-brand-name">{{ $authCompanyName }}</strong>
                </a>
            </div>

            <!-- Fomu inaingia hapa -->
            <div class="auth-minimal-form-container">
                {{ $slot }}
            </div>
        </div>
    </div>

    <script src="{{ asset('js/vendors.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    @include('partials.swal')
</body>
</html>
