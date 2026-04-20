<!-- Footer -->
<footer class="footer -type-1 bg-white border-top-light">
    <div class="footer__main pt-60 pb-40">
        <div class="container">
            <div class="row y-gap-40 justify-between">

                <!-- Column 1: Brand/Logo -->
                <div class="col-xl-3 col-lg-4 col-md-6">
                    <div class="footer-logo">
                        <img src="{{ $siteSettings->footerLogoUrl() }}" alt="{{ $siteSettings->hotelDisplayName() }}" style="max-height: 60px;">
                    </div>
                    <p class="text-16 fw-500 mt-20 text-dark-1">{{ $siteSettings->hotelDisplayName() }}</p>
                    <p class="text-14 text-light-1 mt-10">
                       We provide high-quality accommodation and hospitality services that meet international standards. You are warmly welcome to join us.
                    </p>

                    <!-- Social Icons Moved Here for Better Flow -->
                    <div class="d-flex x-gap-20 items-center pt-20">
                        @if ($siteSettings->facebook_url)
                            <a href="{{ $siteSettings->facebook_url }}" target="_blank" class="text-dark-1"><i class="icon-facebook text-16"></i></a>
                        @endif
                        @if ($siteSettings->twitter_url)
                            <a href="{{ $siteSettings->twitter_url }}" target="_blank" class="text-dark-1"><i class="icon-twitter text-16"></i></a>
                        @endif
                        @if ($siteSettings->instagram_url)
                            <a href="{{ $siteSettings->instagram_url }}" target="_blank" class="text-dark-1"><i class="icon-instagram text-16"></i></a>
                        @endif
                        @if ($siteSettings->linkedin_url)
                            <a href="{{ $siteSettings->linkedin_url }}" target="_blank" class="text-dark-1"><i class="icon-linkedin text-16"></i></a>
                        @endif
                    </div>
                </div>

                <!-- Column 2: Quick Links -->
                <div class="col-xl-2 col-lg-2 col-md-6">
                    <h5 class="text-16 fw-500 mb-20 text-dark-1 uppercase">{{ __('Quick Links') }}</h5>
                    <div class="d-flex flex-column y-gap-10">
                        <a href="{{ route('site.home') }}" class="text-14 text-light-1 hover-main">{{ __('Home') }}</a>
                        <a href="{{ route('site.home') }}#rooms" class="text-14 text-light-1 hover-main">{{ __('Our Rooms') }}</a>
                        <a href="{{ route('site.page', ['slug' => 'about']) }}" class="text-14 text-light-1 hover-main">{{ __('About Us') }}</a>
                        <a href="{{ route('site.page', ['slug' => 'pricing']) }}" class="text-14 text-light-1 hover-main">{{ __('Pricing') }}</a>
                        <a href="{{ route('site.page', ['slug' => 'contact']) }}" class="text-14 text-light-1 hover-main">{{ __('Contact') }}</a>
                        <a href="{{ route('site.page', ['slug' => 'faq']) }}" class="text-14 text-light-1 hover-main">{{ __('Faq') }}</a>
                        <a href="{{ route('site.page', ['slug' => 'terms']) }}" class="text-14 text-light-1 hover-main">{{ __('Terms') }}</a>
                    </div>
                </div>

                <!-- Column 3: Contact Info -->
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <h5 class="text-16 fw-500 mb-20 text-dark-1 uppercase">{{ __('Contact Us') }}</h5>
                    <div class="d-flex flex-column y-gap-15">
                        @if ($siteSettings->address_line)
                        <div class="d-flex items-center">
                            <i class="icon-location text-20 mr-10 text-accent-1"></i>
                            <div class="text-14 text-light-1">{!! nl2br(e($siteSettings->address_line)) !!}</div>
                        </div>
                        @endif

                        @if ($siteSettings->phone)
                        <div class="d-flex items-center">
                            <i class="icon-phone text-20 mr-10 text-accent-1"></i>
                            <a href="tel:{{ preg_replace('/\s+/', '', $siteSettings->phone) }}" class="text-14 text-light-1">{{ $siteSettings->phone }}</a>
                        </div>
                        @endif

                        @if ($siteSettings->email)
                        <div class="d-flex items-center">
                            <i class="icon-email text-20 mr-10 text-accent-1"></i>
                            <a href="mailto:{{ $siteSettings->email }}" class="text-14 text-light-1">{{ $siteSettings->email }}</a>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Column 4: Newsletter -->
                <div class="col-xl-3 col-lg-3 col-md-6">
                    <h5 class="text-16 fw-500 mb-20 text-dark-1 uppercase">{{ __('Newsletter') }}</h5>
                    <p class="text-14 text-light-1 mb-15">“Join us to receive news and new offers.”</p>
                    <div class="footer-newsletter-form">
                        <div class="single-field d-flex items-center">
                            <input class="bg-light-2 text-dark-1" type="email" placeholder="Your Email" style="width: 100%; padding: 12px; border-radius: 4px; border: 1px solid #eee;">
                            <button class="button -md -dark-1 bg-accent-1 text-white ml-10" style="padding: 12px 20px;">
                                <i class="icon-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="footer__bottom py-20 border-top-light">
        <div class="container">
            <div class="row justify-between items-center y-gap-10">
                <div class="col-md-auto">
                    <div class="text-14 text-light-1">
                        {{ $siteSettings->copyright_text ?: ('© '.date('Y').' '.$siteSettings->hotelDisplayName().'. All Rights Reserved.') }}
                    </div>
                </div>
                <div class="col-md-auto">
                    <div class="d-flex x-gap-20">
                        <a href="#" class="text-13 text-light-1 hover-main">Privacy Policy</a>
                        <a href="#" class="text-13 text-light-1 hover-main">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
    /* Custom style kusaidia muonekano wa white footer */
    .footer.bg-white {
        background-color: #ffffff !important;
        color: #1A1C1F !important;
    }
    .text-light-1 {
        color: #697488 !important; /* Rangi ya kijivu kwa maelezo */
    }
    .text-dark-1 {
        color: #051039 !important; /* Rangi nyeusi kwa vichwa vya habari */
    }
    .border-top-light {
        border-top: 1px solid #EDF2F7 !important;
    }
    .hover-main:hover {
        color: var(--color-accent-1) !important; /* Au weka rangi yako ya brand hapa */
        text-decoration: underline;
    }
    .bg-light-2 {
        background-color: #F5F5F5 !important;
    }
</style>
