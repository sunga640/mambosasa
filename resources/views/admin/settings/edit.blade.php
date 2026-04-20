@extends('layouts.admin')

@section('title', __('System settings'))

@section('content')
    <style>
        .settings-tabs { display:flex; gap:.5rem; flex-wrap:wrap; margin-top:1rem; }
        .settings-tabs button { border:1px solid #d1d5db; background:#fff; border-radius:999px; padding:.4rem .85rem; font:inherit; cursor:pointer; }
        .settings-tabs button.is-active { background:#111; color:#fff; border-color:#111; }
        .settings-collapsible-title { user-select:none; }
    </style>
    <h1 class="text-30">{{ __('System settings') }}</h1>
    <p class="text-15 mt-15" style="opacity:.85;max-width:40rem;line-height:1.5;">
        {{ __('These details appear in the public site header, footer, and fullscreen menu. Upload a hero image to replace the first slide background on the home page.') }}
    </p>
    <div class="settings-tabs">
        <button type="button" data-settings-tab="branding">{{ __('Business settings') }}</button>
        <button type="button" data-settings-tab="booking">{{ __('Booking settings') }}</button>
        <button type="button" data-settings-tab="email">{{ __('Email settings') }}</button>
        <button type="button" data-settings-tab="home">{{ __('Home/hero settings') }}</button>
        <button type="button" data-settings-tab="advanced">{{ __('Advanced settings') }}</button>
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data" class="mt-30" data-autosave-key="admin-settings-edit">
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
            'selected' => old('logo_header_media_asset_id'),
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
            'selected' => old('logo_footer_media_asset_id'),
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
        <p class="text-14 mb-15" style="opacity:.75;max-width:44rem;">{{ __('Paste a direct HTTPS image link (e.g. from your CDN or Unsplash) to load backgrounds from the web. If set, the home URL takes priority over the uploaded file for the first slide.') }}</p>
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
                <label class="text-13" style="font-weight:400;"><input type="checkbox" name="remove_hero_background" value="1" @checked(old('remove_hero_background'))> {{ __('Remove hero image (use default template)') }}</label>
            @endif
            <input id="hero_home_background" type="file" name="hero_home_background" accept="image/*">
            @error('hero_home_background')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'hero_home_media_asset_id',
            'label' => __('Or choose hero image from system gallery'),
            'selected' => old('hero_home_media_asset_id'),
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

        // Fungua tab ya kwanza (Branding) kama default unapoingia
        openGroup('branding');
    })();
</script>
@endpush
@endpush
