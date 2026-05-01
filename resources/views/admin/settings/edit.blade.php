@extends('layouts.admin')

@section('title', __('System settings'))

@section('content')
    @php
        $headerLogoAssetId = old('logo_header_media_asset_id', $mediaAssets->firstWhere('path', $setting->logo_header)?->id);
        $footerLogoAssetId = old('logo_footer_media_asset_id', $mediaAssets->firstWhere('path', $setting->logo_footer)?->id);
        $heroSlideOneAssetId = old('hero_home_media_asset_id', $mediaAssets->firstWhere('path', $setting->hero_home_background)?->id);
        $heroSlideTwoAssetId = old('hero_home_slide_two_media_asset_id', $mediaAssets->firstWhere('path', $setting->hero_home_slide_two)?->id);
        $heroSlideThreeAssetId = old('hero_home_slide_three_media_asset_id', $mediaAssets->firstWhere('path', $setting->hero_home_slide_three)?->id);
        $homeHeroGalleryAssetIds = old(
            'home_hero_gallery_media_asset_ids',
            $mediaAssets->whereIn('path', (array) ($setting->home_hero_gallery_paths ?? []))->pluck('id')->values()->all()
        );
        $homeViewsGalleryAssetIds = old(
            'home_views_gallery_media_asset_ids',
            $mediaAssets->whereIn('path', (array) ($setting->home_views_gallery_paths ?? []))->pluck('id')->values()->all()
        );
        $aboutGalleryAssetIds = old(
            'about_gallery_media_asset_ids',
            $mediaAssets->whereIn('path', (array) ($setting->about_gallery_paths ?? []))->pluck('id')->values()->all()
        );
        $emailTemplates = collect($emailTemplateDefinitions)->mapWithKeys(function ($definition, $key) use ($emailTemplates) {
            $defaults = $definition['defaults'] ?? [];
            $saved = old('email_templates.'.$key, $emailTemplates[$key] ?? []);

            return [$key => array_merge($defaults, is_array($saved) ? $saved : [])];
        })->all();
    @endphp
    <style>
        .settings-tabs { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:1rem; }
        .settings-tabs button { border:1px solid rgba(213, 172, 66, 0.22); background:rgba(245,239,226,0.05); color:var(--brand-theme-text, #f5efe2); border-radius:999px; padding:.4rem .85rem; font:inherit; cursor:pointer; }
        .settings-tabs button.is-active { background:var(--brand-theme-accent, #d5ac42); color:#1f232a; border-color:var(--brand-theme-accent, #d5ac42); }
        .settings-collapsible-title { user-select:none; }
        .settings-preview-card { display:grid; gap:.9rem; padding:1rem 1rem 1.1rem; border:1px solid rgba(213, 172, 66, 0.18); background:var(--brand-theme-surface, #2e333b); margin-bottom:1rem; }
        .settings-preview-card img { width:100%; max-width:360px; max-height:220px; object-fit:cover; display:block; }
        .settings-grid-form { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:.8rem 1rem; align-items:start; }
        .settings-grid-form > h2,
        .settings-grid-form > p,
        .settings-grid-form > button,
        .settings-grid-form > .email-template-wrap { grid-column:1 / -1; }
        .settings-grid-form > .form-row { margin-bottom:0; }
        .settings-grid-form > .form-row:has(textarea),
        .settings-grid-form > .form-row:has(input[type="file"]),
        .settings-grid-form > .form-row:has(details.media-picker-shell) { grid-column:1 / -1; }
        .settings-grid-form .form-row label { margin-bottom:.28rem; font-size:.78rem; letter-spacing:.03em; }
        .settings-grid-form .form-row input[type="text"],
        .settings-grid-form .form-row input[type="email"],
        .settings-grid-form .form-row input[type="password"],
        .settings-grid-form .form-row input[type="url"],
        .settings-grid-form .form-row input[type="number"],
        .settings-grid-form .form-row input[type="date"],
        .settings-grid-form .form-row input[type="time"],
        .settings-grid-form .form-row select,
        .settings-grid-form .form-row textarea {
            max-width:none;
            min-height:40px;
            padding:.48rem .68rem;
            font-size:.82rem;
            border-radius:10px;
        }
        .settings-grid-form .form-row textarea { min-height:104px; }
        .settings-grid-form .form-row input[type="time"] { max-width:none; }
        .settings-grid-form .form-row input[type="file"] { max-width:none; font-size:.8rem; }
        .email-template-wrap { width:100%; max-width:none; }
        .email-template-card { margin-top:1.1rem; padding:1.15rem 1.2rem; border:1px solid rgba(213, 172, 66, 0.18); border-radius:16px; background:rgba(255,255,255,0.03); width:100%; }
        .email-template-card:first-of-type { margin-top:0; }
        .email-template-head { display:flex; align-items:flex-start; justify-content:space-between; gap:1rem; margin-bottom:.9rem; }
        .email-template-grid { display:grid; grid-template-columns:repeat(2, minmax(0, 1fr)); gap:.85rem; margin-top:.95rem; }
        .email-template-tall { grid-column:1 / -1; }
        .email-template-body textarea { min-height:120px; resize:vertical; }
        .email-template-chiplist { display:flex; flex-wrap:wrap; gap:.5rem; margin-top:.85rem; }
        .email-template-chip { border:1px solid rgba(213, 172, 66, 0.2); background:rgba(255,255,255,0.04); color:var(--brand-theme-text, #f5efe2); border-radius:999px; padding:.3rem .65rem; font-size:.78rem; cursor:pointer; }
        .email-template-inline-toggle { display:flex; align-items:center; gap:1rem; flex-wrap:wrap; }
        .email-template-toggle-btn { border:1px solid rgba(213, 172, 66, 0.22); background:rgba(255,255,255,0.04); color:var(--brand-theme-text, #f5efe2); border-radius:999px; padding:.38rem .8rem; font:inherit; cursor:pointer; }
        .email-template-panel { display:none; }
        .email-template-card.is-expanded .email-template-panel { display:block; }
        .email-template-field-hint { margin-top:.55rem; font-size:.8rem; opacity:.68; }
        .settings-inline-checkbox {
            display: inline-flex !important;
            align-items: center;
            gap: .7rem;
            margin-bottom: 0 !important;
            font-weight: 700 !important;
            line-height: 1.3;
            text-transform: uppercase;
        }
        .settings-inline-checkbox input[type="checkbox"] {
            margin: 0;
            width: 1rem;
            height: 1rem;
            flex-shrink: 0;
        }
        @media (max-width: 900px) {
            .settings-grid-form { grid-template-columns:1fr; }
            .settings-grid-form > .form-row,
            .settings-grid-form > .form-row:has(textarea),
            .settings-grid-form > .form-row:has(input[type="file"]),
            .settings-grid-form > .form-row:has(details.media-picker-shell) { grid-column:1 / -1; }
        }
        @media (max-width: 900px) {
            .email-template-head { flex-direction:column; }
            .email-template-grid { grid-template-columns:1fr; }
        }
    </style>
    <h1 class="text-30">{{ __('System settings') }}</h1>
    <p class="text-15 mt-15" style="opacity:.85;max-width:40rem;line-height:1.5;">
        {{ __('These details appear in the public site header, footer, and fullscreen menu. Upload a hero image to replace the first slide background on the home page.') }}
    </p>
    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:1rem;margin-top:1.25rem;">
        <div class="settings-preview-card" style="margin-bottom:0;">
            <div class="text-14" style="font-weight:700;">{{ __('Email setup status') }}</div>
            <p class="text-13" style="opacity:.72;margin:0;">
                {{ $setting->smtp_host && $setting->smtp_port ? __('SMTP is configured from admin settings.') : __('SMTP is not fully configured yet.') }}
            </p>
            <div class="text-12" style="opacity:.65;">
                {{ __('Host') }}: {{ $setting->smtp_host ?: '—' }}<br>
                {{ __('From') }}: {{ $setting->mail_from_address ?: '—' }}
            </div>
        </div>
        <div class="settings-preview-card" style="margin-bottom:0;">
            <div class="text-14" style="font-weight:700;">{{ __('Payment setup') }}</div>
            <p class="text-13" style="opacity:.72;margin:0;">
                {{ __('Gateway keys and provider setup are managed from the payment methods page.') }}
            </p>
            <div>
                <a href="{{ route('admin.payment-methods.index') }}" class="dash-btn dash-btn--ghost">{{ __('Open payment methods') }}</a>
            </div>
        </div>
    </div>
    <div class="settings-tabs">
        <button type="button" data-settings-tab="branding">{{ __('Business settings') }}</button>
        <button type="button" data-settings-tab="booking">{{ __('Booking settings') }}</button>
        <button type="button" data-settings-tab="email">{{ __('Email settings') }}</button>
        <button type="button" data-settings-tab="integrations">{{ __('Integrations') }}</button>
        <button type="button" data-settings-tab="home">{{ __('Home/hero settings') }}</button>
        <button type="button" data-settings-tab="advanced">{{ __('Advanced settings') }}</button>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-30 settings-grid-form" data-autosave-key="admin-settings-edit">
        @csrf
        @method('PUT')

        <h2 class="text-20 mt-30 mb-15" data-settings-group="branding">{{ __('Branding') }}</h2>
        <div class="form-row">
            <label for="company_name">{{ __('Company / site name') }}</label>
            <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $setting->company_name) }}">
            @error('company_name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="header_brand_lines">{{ __('Header center — display lines (optional)') }}</label>
            <textarea id="header_brand_lines" name="header_brand_lines" rows="4" placeholder="SWISS RESORT&#10;CITY HOTEL&#10;Swiss Seaside Hotel">{{ old('header_brand_lines', $setting->header_brand_lines) }}</textarea>
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Up to three lines: line 1 large (serif), line 2 smaller (grey), line 3 accent. Leave empty to show only the company name as one large line.') }}</p>
            @error('header_brand_lines')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="logo_header">{{ __('Header logo') }} <span class="text-13" style="opacity:.65;">(PNG/SVG/JPG)</span></label>
            @if ($setting->logo_header)
                <div class="mb-10"><img src="{{ $setting->headerLogoUrl() }}" alt="" style="max-height:48px;background:#f5f5f5;padding:4px 8px;border-radius:6px;"></div>
                <label class="text-13" style="font-weight:400;"><input type="checkbox" name="remove_logo_header" value="1" @checked(old('remove_logo_header'))> {{ __('Remove uploaded header logo') }}</label>
            @endif
            <input id="logo_header" type="file" name="logo_header" accept="image/*">
            @error('logo_header')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'logo_header_media_asset_id',
            'label' => __('Or choose header logo from system gallery'),
            'selected' => $headerLogoAssetId,
        ])
        <div class="form-row">
            <label for="logo_footer">{{ __('Footer logo (often light / white)') }}</label>
            @if ($setting->logo_footer)
                <div class="mb-10"><img src="{{ $setting->footerLogoUrl() }}" alt="" style="max-height:48px;background:#122223;padding:4px 8px;border-radius:6px;"></div>
                <label class="text-13" style="font-weight:400;"><input type="checkbox" name="remove_logo_footer" value="1" @checked(old('remove_logo_footer'))> {{ __('Remove uploaded footer logo') }}</label>
            @endif
            <input id="logo_footer" type="file" name="logo_footer" accept="image/*">
            @error('logo_footer')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'logo_footer_media_asset_id',
            'label' => __('Or choose footer logo from system gallery'),
            'selected' => $footerLogoAssetId,
        ])

        <h2 class="text-20 mt-30 mb-15" data-settings-group="branding">{{ __('Contact (header top bar & footer)') }}</h2>
        <div class="form-row">
            <label for="address_line">{{ __('Address') }}</label>
            <textarea id="address_line" name="address_line" rows="3">{{ old('address_line', $setting->address_line) }}</textarea>
            @error('address_line')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="phone">{{ __('Phone') }}</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone', $setting->phone) }}">
            @error('phone')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email', $setting->email) }}">
            @error('email')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="branding">{{ __('Social links') }}</h2>
        <div class="form-row">
            <label for="facebook_url">Facebook URL</label>
            <input id="facebook_url" type="text" name="facebook_url" value="{{ old('facebook_url', $setting->facebook_url) }}" placeholder="https://">
            @error('facebook_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="twitter_url">X / Twitter URL</label>
            <input id="twitter_url" type="text" name="twitter_url" value="{{ old('twitter_url', $setting->twitter_url) }}" placeholder="https://">
            @error('twitter_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="instagram_url">Instagram URL</label>
            <input id="instagram_url" type="text" name="instagram_url" value="{{ old('instagram_url', $setting->instagram_url) }}" placeholder="https://">
            @error('instagram_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="linkedin_url">LinkedIn URL</label>
            <input id="linkedin_url" type="text" name="linkedin_url" value="{{ old('linkedin_url', $setting->linkedin_url) }}" placeholder="https://">
            @error('linkedin_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="booking">{{ __('Bookings & home stats') }}</h2>
        <div class="form-row">
            <label for="booking_payment_timeout_minutes">{{ __('Payment window (minutes)') }}</label>
            <input id="booking_payment_timeout_minutes" type="number" name="booking_payment_timeout_minutes" min="5" max="10080" value="{{ old('booking_payment_timeout_minutes', $setting->booking_payment_timeout_minutes ?? 30) }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('After a guest completes an order, they have this long to pay before the hold is released.') }}</p>
            @error('booking_payment_timeout_minutes')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="booking_checkout_time">{{ __('Checkout time — weekdays (Mon–Fri), 24h') }}</label>
            <input id="booking_checkout_time" type="time" name="booking_checkout_time" value="{{ old('booking_checkout_time', $setting->bookingCheckoutTime()) }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Used on guest calendar for checkout day and for stay-ended notifications when checkout falls on a weekday.') }}</p>
            @error('booking_checkout_time')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="booking_checkout_weekend_time">{{ __('Checkout time — weekend (Sat–Sun), 24h') }}</label>
            <input id="booking_checkout_weekend_time" type="time" name="booking_checkout_weekend_time" value="{{ old('booking_checkout_weekend_time', $setting->bookingCheckoutWeekendTime()) }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Example: 04:30 for a slightly later Sunday / Saturday checkout. Guest dashboard calendar shows this on the last stay day.') }}</p>
            @error('booking_checkout_weekend_time')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="stat_pools_count">{{ __('Home stat: private pools (number)') }}</label>
            <input id="stat_pools_count" type="number" name="stat_pools_count" min="0" value="{{ old('stat_pools_count', $setting->stat_pools_count) }}">
            @error('stat_pools_count')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="stat_restaurants_count">{{ __('Home stat: restaurants (number)') }}</label>
            <input id="stat_restaurants_count" type="number" name="stat_restaurants_count" min="0" value="{{ old('stat_restaurants_count', $setting->stat_restaurants_count) }}">
            @error('stat_restaurants_count')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_stat_customers_label">{{ __('Home stat: happy customers (override text)') }}</label>
            <input id="home_stat_customers_label" type="text" name="home_stat_customers_label" maxlength="32" value="{{ old('home_stat_customers_label', $setting->home_stat_customers_label) }}" placeholder="{{ __('Leave empty to use booking/user counts from the database') }}">
            @error('home_stat_customers_label')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_stat_caption_guests">{{ __('Home stat caption: guests column') }}</label>
            <input id="home_stat_caption_guests" type="text" name="home_stat_caption_guests" maxlength="80" value="{{ old('home_stat_caption_guests', $setting->home_stat_caption_guests) }}" placeholder="{{ __('e.g. Happy guests hosted') }}">
            @error('home_stat_caption_guests')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_stat_caption_rooms">{{ __('Home stat caption: rooms column') }}</label>
            <input id="home_stat_caption_rooms" type="text" name="home_stat_caption_rooms" maxlength="80" value="{{ old('home_stat_caption_rooms', $setting->home_stat_caption_rooms) }}" placeholder="{{ __('e.g. Rooms ready to book') }}">
            @error('home_stat_caption_rooms')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_stat_caption_pools">{{ __('Home stat caption: pools column') }}</label>
            <input id="home_stat_caption_pools" type="text" name="home_stat_caption_pools" maxlength="80" value="{{ old('home_stat_caption_pools', $setting->home_stat_caption_pools) }}" placeholder="{{ __('e.g. Pool & wellness') }}">
            @error('home_stat_caption_pools')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_stat_caption_dining">{{ __('Home stat caption: dining column') }}</label>
            <input id="home_stat_caption_dining" type="text" name="home_stat_caption_dining" maxlength="80" value="{{ old('home_stat_caption_dining', $setting->home_stat_caption_dining) }}" placeholder="{{ __('e.g. Dining experiences') }}">
            @error('home_stat_caption_dining')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="email">{{ __('Email delivery (SMTP)') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Fill these once and only credentials can be changed later. Used by booking, invoice, and notification emails.') }}</p>
        <div class="form-row">
            <label for="smtp_host">{{ __('SMTP host') }}</label>
            <input id="smtp_host" type="text" name="smtp_host" value="{{ old('smtp_host', $setting->smtp_host) }}" placeholder="smtp.mailprovider.com">
            @error('smtp_host')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="smtp_port">{{ __('SMTP port') }}</label>
            <input id="smtp_port" type="number" name="smtp_port" min="1" max="65535" value="{{ old('smtp_port', $setting->smtp_port) }}" placeholder="587">
            @error('smtp_port')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="smtp_encryption">{{ __('SMTP encryption') }}</label>
            <select id="smtp_encryption" name="smtp_encryption">
                <option value="">{{ __('None') }}</option>
                <option value="tls" @selected(old('smtp_encryption', $setting->smtp_encryption) === 'tls')>TLS</option>
                <option value="ssl" @selected(old('smtp_encryption', $setting->smtp_encryption) === 'ssl')>SSL</option>
            </select>
            @error('smtp_encryption')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="smtp_username">{{ __('SMTP username') }}</label>
            <input id="smtp_username" type="text" name="smtp_username" value="{{ old('smtp_username', $setting->smtp_username) }}">
            @error('smtp_username')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="smtp_password">{{ __('SMTP password / app password') }}</label>
            <input id="smtp_password" type="password" name="smtp_password" value="{{ old('smtp_password', $setting->smtp_password) }}">
            @error('smtp_password')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="mail_from_address">{{ __('Mail from address') }}</label>
            <input id="mail_from_address" type="email" name="mail_from_address" value="{{ old('mail_from_address', $setting->mail_from_address) }}" placeholder="noreply@yourdomain.com">
            @error('mail_from_address')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="mail_from_name">{{ __('Mail from name') }}</label>
            <input id="mail_from_name" type="text" name="mail_from_name" value="{{ old('mail_from_name', $setting->mail_from_name) }}" placeholder="{{ $setting->hotelDisplayName() }}">
            @error('mail_from_name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="dashboard_theme_mode">{{ __('Dashboard theme mode') }}</label>
            <select id="dashboard_theme_mode" name="dashboard_theme_mode">
                <option value="system" @selected(old('dashboard_theme_mode', $setting->dashboard_theme_mode ?? 'system') === 'system')>{{ __('System default') }}</option>
                <option value="dark" @selected(old('dashboard_theme_mode', $setting->dashboard_theme_mode ?? 'system') === 'dark')>{{ __('Dark') }}</option>
                <option value="light" @selected(old('dashboard_theme_mode', $setting->dashboard_theme_mode ?? 'system') === 'light')>{{ __('Light') }}</option>
            </select>
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Controls all internal dashboards and settings surfaces.') }}</p>
            @error('dashboard_theme_mode')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <h2 class="text-20 mt-30 mb-15" data-settings-group="email">{{ __('Guest email templates') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:48rem;">{{ __('Customize the subject, arrangement, call-to-action buttons, and guest-facing message content for each booking email flow. Click any variable below to insert it into the field you are editing.') }}</p>
        <div class="email-template-wrap">
        @foreach ($emailTemplateDefinitions as $templateKey => $templateDefinition)
            @php($template = $emailTemplates[$templateKey] ?? ($templateDefinition['defaults'] ?? []))
            <div class="email-template-card" data-email-template-card>
                <div class="email-template-head">
                    <div>
                        <div class="text-18" style="font-weight:700;">{{ $templateDefinition['label'] }}</div>
                        <p class="text-13 mt-5" style="opacity:.72;margin:0;max-width:46rem;">{{ $templateDefinition['description'] }}</p>
                    </div>
                    <div class="email-template-inline-toggle">
                        <label class="text-13" style="font-weight:600;">
                            <input type="hidden" name="email_templates[{{ $templateKey }}][enabled]" value="0">
                            <input type="checkbox" name="email_templates[{{ $templateKey }}][enabled]" value="1" @checked((bool) ($template['enabled'] ?? false))>
                            {{ __('Enabled') }}
                        </label>
                        <button type="button" class="email-template-toggle-btn" data-email-template-toggle>{{ __('More options') }}</button>
                    </div>
                </div>

                <div class="form-row email-template-body">
                    <label for="email_templates_{{ $templateKey }}_body">{{ __('Message content') }}</label>
                    <textarea id="email_templates_{{ $templateKey }}_body" name="email_templates[{{ $templateKey }}][body]" rows="4" data-email-template-input>{{ $template['body'] ?? '' }}</textarea>
                    <div class="email-template-field-hint">{{ __('This is the main guest message. Use the variables below to personalize it.') }}</div>
                </div>

                <div class="email-template-panel">
                    <div class="email-template-grid">
                        <div class="form-row email-template-tall">
                            <label for="email_templates_{{ $templateKey }}_subject">{{ __('Email subject') }}</label>
                            <input id="email_templates_{{ $templateKey }}_subject" type="text" name="email_templates[{{ $templateKey }}][subject]" value="{{ $template['subject'] ?? '' }}" data-email-template-input>
                        </div>
                        <div class="form-row">
                            <label for="email_templates_{{ $templateKey }}_title">{{ __('Banner title') }}</label>
                            <input id="email_templates_{{ $templateKey }}_title" type="text" name="email_templates[{{ $templateKey }}][title]" value="{{ $template['title'] ?? '' }}" data-email-template-input>
                        </div>
                        <div class="form-row">
                            <label for="email_templates_{{ $templateKey }}_accent_color">{{ __('Accent color') }}</label>
                            <input id="email_templates_{{ $templateKey }}_accent_color" type="text" name="email_templates[{{ $templateKey }}][accent_color]" value="{{ $template['accent_color'] ?? '#1f7ae0' }}" placeholder="#1f7ae0">
                        </div>
                        <div class="form-row email-template-tall">
                            <label for="email_templates_{{ $templateKey }}_intro">{{ __('Opening line / greeting') }}</label>
                            <textarea id="email_templates_{{ $templateKey }}_intro" name="email_templates[{{ $templateKey }}][intro]" rows="2" data-email-template-input>{{ $template['intro'] ?? '' }}</textarea>
                        </div>
                        <div class="form-row email-template-tall">
                            <label for="email_templates_{{ $templateKey }}_highlight">{{ __('Highlight box content') }}</label>
                            <textarea id="email_templates_{{ $templateKey }}_highlight" name="email_templates[{{ $templateKey }}][highlight]" rows="3" data-email-template-input>{{ $template['highlight'] ?? '' }}</textarea>
                        </div>
                        <div class="form-row">
                            <label for="email_templates_{{ $templateKey }}_primary_button_label">{{ __('Primary button label') }}</label>
                            <input id="email_templates_{{ $templateKey }}_primary_button_label" type="text" name="email_templates[{{ $templateKey }}][primary_button_label]" value="{{ $template['primary_button_label'] ?? '' }}" data-email-template-input>
                        </div>
                        <div class="form-row">
                            <label for="email_templates_{{ $templateKey }}_secondary_button_label">{{ __('Secondary button label') }}</label>
                            <input id="email_templates_{{ $templateKey }}_secondary_button_label" type="text" name="email_templates[{{ $templateKey }}][secondary_button_label]" value="{{ $template['secondary_button_label'] ?? '' }}" data-email-template-input>
                        </div>
                        <div class="form-row email-template-tall">
                            <label for="email_templates_{{ $templateKey }}_footer_note">{{ __('Footer note') }}</label>
                            <textarea id="email_templates_{{ $templateKey }}_footer_note" name="email_templates[{{ $templateKey }}][footer_note]" rows="3" data-email-template-input>{{ $template['footer_note'] ?? '' }}</textarea>
                        </div>
                        <div class="form-row email-template-tall">
                            <label class="text-13" style="font-weight:600;">
                                <input type="hidden" name="email_templates[{{ $templateKey }}][details_enabled]" value="0">
                                <input type="checkbox" name="email_templates[{{ $templateKey }}][details_enabled]" value="1" @checked((bool) ($template['details_enabled'] ?? false))>
                                {{ __('Show booking detail box') }}
                            </label>
                        </div>
                    </div>
                </div>

                <div class="email-template-chiplist">
                    @foreach ($templateDefinition['placeholders'] as $placeholder)
                        <button type="button" class="email-template-chip" data-insert-variable="{{ $placeholder }}">{{ $placeholder }}</button>
                    @endforeach
                </div>
            </div>
        @endforeach
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="integrations">{{ __('Restaurant system integration') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:50rem;">
            {{ __('This connects the guest portal to your separate restaurant system. Guests never receive raw API keys in the browser. Instead, this system creates a short-lived signed token and redirects them to the restaurant platform.') }}
        </p>
        <div class="form-row">
            <label class="settings-inline-checkbox"><input type="checkbox" name="restaurant_integration_enabled" value="1" @checked(old('restaurant_integration_enabled', $setting->restaurant_integration_enabled))> <span>{{ __('Enable restaurant integration') }}</span></label>
            @error('restaurant_integration_enabled')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_api_base_url">{{ __('Restaurant system base URL') }}</label>
            <input id="restaurant_api_base_url" type="url" name="restaurant_api_base_url" value="{{ old('restaurant_api_base_url', $setting->restaurant_api_base_url) }}" placeholder="https://restaurant.example.com">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Use the main HTTPS URL of the restaurant system. The launch path below will be appended to it.') }}</p>
            @error('restaurant_api_base_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_sso_entry_path">{{ __('Restaurant SSO / launch path') }}</label>
            <input id="restaurant_sso_entry_path" type="text" name="restaurant_sso_entry_path" value="{{ old('restaurant_sso_entry_path', $setting->restaurant_sso_entry_path ?: 'restaurant/sso/hotel') }}" placeholder="restaurant/sso/hotel">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Example: restaurant/sso/hotel or api/hotel/sso-entry. This path must exist on the restaurant system and verify the token signature.') }}</p>
            @error('restaurant_sso_entry_path')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_api_key">{{ __('Server API key (optional)') }}</label>
            <input id="restaurant_api_key" type="text" name="restaurant_api_key" value="{{ old('restaurant_api_key') }}" placeholder="{{ $setting->restaurant_api_key ? __('Saved - leave blank to keep current key') : __('Paste API key') }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Used only for server-to-server calls from this hotel system to the restaurant API. It is stored encrypted and never exposed to guests.') }}</p>
            @error('restaurant_api_key')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_api_secret">{{ __('Server API secret (optional)') }}</label>
            <input id="restaurant_api_secret" type="password" name="restaurant_api_secret" value="{{ old('restaurant_api_secret') }}" placeholder="{{ $setting->restaurant_api_secret ? __('Saved - leave blank to keep current secret') : __('Paste API secret') }}">
            @error('restaurant_api_secret')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_sso_shared_secret">{{ __('Shared secret for signed guest access') }}</label>
            <input id="restaurant_sso_shared_secret" type="password" name="restaurant_sso_shared_secret" value="{{ old('restaurant_sso_shared_secret') }}" placeholder="{{ $setting->restaurant_sso_shared_secret ? __('Saved - leave blank to keep current secret') : __('Paste shared secret') }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('This is the most important field. The restaurant system must have the same secret so it can verify hotel-issued guest tokens.') }}</p>
            @error('restaurant_sso_shared_secret')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_api_timeout_seconds">{{ __('API timeout in seconds') }}</label>
            <input id="restaurant_api_timeout_seconds" type="number" name="restaurant_api_timeout_seconds" min="5" max="120" value="{{ old('restaurant_api_timeout_seconds', $setting->restaurantApiTimeoutSeconds()) }}">
            @error('restaurant_api_timeout_seconds')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="restaurant_token_ttl_minutes">{{ __('Guest access token lifetime (minutes)') }}</label>
            <input id="restaurant_token_ttl_minutes" type="number" name="restaurant_token_ttl_minutes" min="1" max="120" value="{{ old('restaurant_token_ttl_minutes', $setting->restaurantTokenTtlMinutes()) }}">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Recommended 3-10 minutes. The shorter the lifetime, the safer the handoff token becomes.') }}</p>
            @error('restaurant_token_ttl_minutes')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="home">{{ __('Home page — first content block (trust)') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Leave fields empty to use the default English text on the public home page.') }}</p>
        <div class="form-row">
            <label for="home_hero_eyebrow">{{ __('Hero — small line above main title') }}</label>
            <input id="home_hero_eyebrow" type="text" name="home_hero_eyebrow" maxlength="160" value="{{ old('home_hero_eyebrow', $setting->home_hero_eyebrow) }}">
            @error('home_hero_eyebrow')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_hero_headline_suffix">{{ __('Hero — second line of headline (after hotel name)') }}</label>
            <input id="home_hero_headline_suffix" type="text" name="home_hero_headline_suffix" maxlength="255" value="{{ old('home_hero_headline_suffix', $setting->home_hero_headline_suffix) }}">
            @error('home_hero_headline_suffix')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_section1_heading">{{ __('Section below hero — main heading') }}</label>
            <input id="home_section1_heading" type="text" name="home_section1_heading" maxlength="255" value="{{ old('home_section1_heading', $setting->home_section1_heading) }}">
            @error('home_section1_heading')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="home_section1_body">{{ __('Section below hero — intro paragraph') }}</label>
            <textarea id="home_section1_body" name="home_section1_body" rows="4">{{ old('home_section1_body', $setting->home_section1_body) }}</textarea>
            @error('home_section1_body')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="home">{{ __('Home page hero') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Each home hero slide can now come from upload, direct HTTPS image URL, or the built-in media library below. If an HTTPS URL is set for slide 1, it takes priority over file and media-library selection.') }}</p>
        <div class="form-row">
            <label for="hero_home_image_url">{{ __('Home first slide — image URL (HTTPS)') }}</label>
            <input id="hero_home_image_url" type="url" name="hero_home_image_url" value="{{ old('hero_home_image_url', $setting->hero_home_image_url) }}" placeholder="https://images.example.com/hero.jpg">
            @error('hero_home_image_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="inner_page_hero_image_url">{{ __('Inner pages hero — image URL (HTTPS)') }}</label>
            <input id="inner_page_hero_image_url" type="url" name="inner_page_hero_image_url" value="{{ old('inner_page_hero_image_url', $setting->inner_page_hero_image_url) }}" placeholder="https://…">
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Used on About, Contact, Cart, Checkout, Booking, Terms, and Search headers when set.') }}</p>
            @error('inner_page_hero_image_url')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="hero_home_background">{{ __('First section background image (upload)') }}</label>
            @if ($setting->hero_home_background)
                <div class="mb-10"><img src="{{ $setting->heroHomeBackgroundUrl() }}" alt="" style="max-width:280px;border-radius:8px;"></div>
                <label class="settings-inline-checkbox" style="font-weight:400 !important;text-transform:none;"><input type="checkbox" name="remove_hero_background" value="1" @checked(old('remove_hero_background'))> <span>{{ __('Remove hero image (use default template)') }}</span></label>
            @endif
            <input id="hero_home_background" type="file" name="hero_home_background" accept="image/*">
            @error('hero_home_background')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'hero_home_media_asset_id',
            'label' => __('Or choose slide 1 image from system media library'),
            'selected' => $heroSlideOneAssetId,
        ])
        <div class="form-row">
            <label for="hero_home_slide_two">{{ __('Home slide 2 image') }}</label>
            @if ($setting->hero_home_slide_two)
                <div class="mb-10"><img src="{{ $setting->heroHomeSlideTwoUrl() }}" alt="" style="max-width:280px;border-radius:8px;"></div>
                <label class="settings-inline-checkbox" style="font-weight:400 !important;text-transform:none;"><input type="checkbox" name="remove_hero_slide_two" value="1" @checked(old('remove_hero_slide_two'))> <span>{{ __('Remove slide 2 image') }}</span></label>
            @endif
            <input id="hero_home_slide_two" type="file" name="hero_home_slide_two" accept="image/*">
            @error('hero_home_slide_two')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'hero_home_slide_two_media_asset_id',
            'label' => __('Or choose slide 2 image from system media library'),
            'selected' => $heroSlideTwoAssetId,
        ])
        <div class="form-row">
            <label for="hero_home_slide_three">{{ __('Home slide 3 image') }}</label>
            @if ($setting->hero_home_slide_three)
                <div class="mb-10"><img src="{{ $setting->heroHomeSlideThreeUrl() }}" alt="" style="max-width:280px;border-radius:8px;"></div>
                <label class="settings-inline-checkbox" style="font-weight:400 !important;text-transform:none;"><input type="checkbox" name="remove_hero_slide_three" value="1" @checked(old('remove_hero_slide_three'))> <span>{{ __('Remove slide 3 image') }}</span></label>
            @endif
            <input id="hero_home_slide_three" type="file" name="hero_home_slide_three" accept="image/*">
            @error('hero_home_slide_three')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'hero_home_slide_three_media_asset_id',
            'label' => __('Or choose slide 3 image from system media library'),
            'selected' => $heroSlideThreeAssetId,
        ])
        <div class="form-row">
            <label>{{ __('Home hero slider gallery (multi select from media library)') }}</label>
            <p class="text-13 mt-5" style="opacity:.7;">{{ __('Select multiple gallery images for the homepage slider. When this list has images, it becomes the main source for the home slider.') }}</p>
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'home_hero_gallery_media_asset_ids',
            'label' => __('Choose multiple slider images from system media library'),
            'selected' => $homeHeroGalleryAssetIds,
            'multiple' => true,
        ])

        <h2 class="text-20 mt-30 mb-15" data-settings-group="home">{{ __('Hotel views gallery section') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Select multiple images for the modern hotel views section on the homepage. These will be shown in a styled gallery section.') }}</p>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'home_views_gallery_media_asset_ids',
            'label' => __('Choose multiple hotel view images from system media library'),
            'selected' => $homeViewsGalleryAssetIds,
            'multiple' => true,
        ])
        <h2 class="text-20 mt-30 mb-15" data-settings-group="home">{{ __('About page gallery') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Select the images that should appear inside the About page content blocks. These will replace the current default about images.') }}</p>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'about_gallery_media_asset_ids',
            'label' => __('Choose multiple About page images from system media library'),
            'selected' => $aboutGalleryAssetIds,
            'multiple' => true,
        ])
        <h2 class="text-20 mt-30 mb-15" data-settings-group="advanced">{{ __('UI customization (per-page heroes)') }}</h2>
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;line-height:1.5;">
            {{ __('Optional JSON: page slug (URL segment) → HTTPS image URL or storage path. Overrides the global inner hero for that page. Keys examples: about, contact, terms, pricing, faq, search, branches.') }}
        </p>
        <div class="form-row">
            <label for="ui_page_heroes_json">{{ __('Page hero map (JSON)') }}</label>
            <textarea id="ui_page_heroes_json" name="ui_page_heroes_json" rows="7" style="font-family:ui-monospace,monospace;font-size:0.85rem;">{{ old('ui_page_heroes_json', $setting->ui_page_heroes ? json_encode($setting->ui_page_heroes, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '') }}</textarea>
            @error('ui_page_heroes_json')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15" data-settings-group="branding">{{ __('Footer') }}</h2>
        <div class="form-row">
            <label for="copyright_text">{{ __('Copyright line') }}</label>
            <input id="copyright_text" type="text" name="copyright_text" value="{{ old('copyright_text', $setting->copyright_text) }}">
            @error('copyright_text')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-30" style="border:none;cursor:pointer;padding:.65rem 1.4rem;border-radius:8px;">
            {{ __('Save settings') }}
        </button>
    </form>
@endsection
@push('scripts')
@push('scripts')
<script>
    (function () {
        var form = document.querySelector('form[data-autosave-key="admin-settings-edit"]');
        if (!form) return;
        var activeTemplateInput = null;

        var headings = Array.prototype.slice.call(form.querySelectorAll('h2[data-settings-group]'));
        if (!headings.length) return;

        // Tunatengeneza list ya element zote zilizo chini ya kila heading
        headings.forEach(function (h) {
            h.classList.add('settings-collapsible-title');
            var nodes = [];
            var cur = h.nextElementSibling;

            // Inakusanya element zote mpaka itakapokutana na H2 nyingine
            while (cur && cur.tagName !== 'H2' && cur.tagName !== 'BUTTON') {
                nodes.push(cur);
                cur = cur.nextElementSibling;
            }
            h.__nodes = nodes;
        });

        var openGroup = function (group) {
            headings.forEach(function (h) {
                // Angalia kama heading hii inahusika na tab iliyobonyezwa
                var show = h.dataset.settingsGroup === group;

                // 1. Ficha au onyesha Heading yenyewe (H2)
                h.style.display = show ? 'block' : 'none';

                // 2. Ficha au onyesha maudhui (inputs/fields) yaliyo chini ya heading hiyo
                (h.__nodes || []).forEach(function (n) {
                    n.style.display = show ? '' : 'none';
                });
            });

            // Badilisha rangi ya button iwe "active"
            document.querySelectorAll('[data-settings-tab]').forEach(function (btn) {
                btn.classList.toggle('is-active', btn.dataset.settingsTab === group);
            });
        };

        // Ongeza click event kwenye tabs
        document.querySelectorAll('[data-settings-tab]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                openGroup(btn.dataset.settingsTab);
            });
        });

        form.querySelectorAll('[data-email-template-input]').forEach(function (field) {
            field.addEventListener('focus', function () {
                activeTemplateInput = field;
            });
            field.addEventListener('click', function () {
                activeTemplateInput = field;
            });
        });

        form.querySelectorAll('[data-insert-variable]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                if (!activeTemplateInput) return;

                var token = btn.getAttribute('data-insert-variable') || '';
                var start = activeTemplateInput.selectionStart || 0;
                var end = activeTemplateInput.selectionEnd || 0;
                var value = activeTemplateInput.value || '';

                activeTemplateInput.value = value.slice(0, start) + token + value.slice(end);
                activeTemplateInput.focus();
                activeTemplateInput.selectionStart = activeTemplateInput.selectionEnd = start + token.length;
            });
        });

        form.querySelectorAll('[data-email-template-toggle]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var card = btn.closest('[data-email-template-card]');
                if (!card) return;

                var expanded = card.classList.toggle('is-expanded');
                btn.textContent = expanded ? '{{ __('Hide options') }}' : '{{ __('More options') }}';
            });
        });

        // Fungua tab ya kwanza (Branding) kama default unapoingia
        openGroup('branding');
    })();
</script>
@endpush
@endpush
