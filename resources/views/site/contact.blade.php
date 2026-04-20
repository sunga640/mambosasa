@extends('layouts.site')

@section('title', __('Contact'))

@section('content')

    <section class="layout-pt-lg layout-pb-lg">
      <div class="container">
        @if (session('status'))
          <div class="row justify-center mb-40">
            <div class="col-xl-8 col-lg-10">
              <div class="p-20 bg-light-1 rounded-8 text-15 text-center" role="status">{{ session('status') }}</div>
            </div>
          </div>
        @endif
        <div class="row justify-center text-center">
          <div class="col-xl-8 col-lg-10">
            <div class="mb-30">
              <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#clip0_632_5288)">
                  <path d="M47.9511 0.57683C45.9935 -0.55357 43.4816 0.119689 42.3516 2.07726L36.7643 11.7548H24.7149C24.1755 11.7548 23.7383 12.1921 23.7383 12.7313C23.7383 13.2706 24.1755 13.7079 24.7149 13.7079H35.6366L31.5821 20.7308H8.78948C8.25011 20.7308 7.81289 21.1681 7.81289 21.7073C7.81289 22.2467 8.25011 22.6839 8.78948 22.6839H30.4543L28.199 26.5903H8.78948C8.25011 26.5903 7.81289 27.0276 7.81289 27.5669C7.81289 28.1061 8.25011 28.5435 8.78948 28.5435H27.795L27.3742 32.4498H8.78948C8.25011 32.4498 7.81289 32.8871 7.81289 33.4264C7.81289 33.9657 8.25011 34.403 8.78948 34.403H27.1638L27.1118 34.8853C27.0704 35.2697 27.2597 35.6424 27.5944 35.8356C27.7461 35.9232 27.9146 35.9665 28.0826 35.9665C28.2857 35.9665 28.488 35.9033 28.6588 35.7785L34.8944 31.2232C35.0043 31.1429 35.0961 31.0407 35.1641 30.9229L42.7745 17.7414V36.6703C42.7745 38.8585 40.9943 40.6388 38.8061 40.6388H16.1736C15.8246 40.6388 15.5022 40.8249 15.3278 41.1271L11.913 47.0418L8.49817 41.1271C8.32375 40.8249 8.00138 40.6388 7.65244 40.6388H5.92164C3.7334 40.6388 1.95317 38.8585 1.95317 36.6703V17.6764C1.95317 15.4882 3.7334 13.7079 5.92164 13.7079H15.9257C16.4651 13.7079 16.9023 13.2706 16.9023 12.7313C16.9023 12.192 16.4651 11.7548 15.9257 11.7548H5.92164C2.65642 11.7548 0 14.4112 0 17.6764V36.6703C0 39.9355 2.65642 42.592 5.92164 42.592H7.08856L11.0673 49.4832C11.2417 49.7854 11.5641 49.9715 11.913 49.9715C12.2619 49.9715 12.5843 49.7854 12.7587 49.4832L16.7374 42.592H38.8062C42.0714 42.592 44.7278 39.9355 44.7278 36.6703V17.6764C44.7278 16.8393 44.5503 16.0123 44.2107 15.2541L49.4516 6.17648C50.5818 4.21901 49.9087 1.70703 47.9511 0.57683ZM46.9745 2.26828C47.9994 2.85999 48.3517 4.17507 47.76 5.1999L47.202 6.16643L43.485 4.02037L44.043 3.05385C44.6347 2.02911 45.9497 1.67686 46.9745 2.26828ZM29.7203 28.9075L32.5315 30.5306L29.2904 32.8984L29.7203 28.9075ZM33.9609 29.1006L30.2439 26.9545L42.5084 5.71182L46.2254 7.85787L33.9609 29.1006Z" fill="#122223" />
                  <path d="M20.3203 13.708C20.5771 13.708 20.8291 13.6035 21.0117 13.4219C21.1934 13.2402 21.2979 12.9883 21.2979 12.7314C21.2979 12.4746 21.1934 12.2227 21.0117 12.041C20.8291 11.8595 20.5781 11.7549 20.3203 11.7549C20.0635 11.7549 19.8115 11.8594 19.6299 12.041C19.4482 12.2227 19.3447 12.4746 19.3447 12.7314C19.3447 12.9883 19.4481 13.2402 19.6299 13.4219C19.8125 13.6035 20.0635 13.708 20.3203 13.708Z" fill="#122223" />
                </g>
                <defs>
                  <clipPath id="clip0_632_5288">
                    <rect width="50" height="50" fill="white" />
                  </clipPath>
                </defs>
              </svg>
            </div>

            <!-- Advanced Contact Title Section -->
<div style="margin-bottom: 40px;">



    <!-- Long Advanced Title (Line 1 longer than Line 2) -->
    <h2 style="font-size: clamp(30px, 4.5vw, 52px); font-weight: 800; color: #051039; line-height: 1.1; letter-spacing: -1.5px;">

        <span style="display: block; font-size: 0.7em; color: #2563eb; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">
            {{ __('Your Journey to Absolute Comfort Starts Here') }}
        </span>
    </h2>


</div>
            <p class="lh-17 mt-30">
              {{ __('Reach the team at') }} {{ $siteSettings->company_name ?? config('app.name') }}. {{ __('We read every message and reply during business hours.') }}
            </p>

            @if ($errors->any())
              <div class="mt-30 p-20 bg-light-1 rounded-8 text-accent-1 text-15 text-left" role="alert">
                <ul class="mb-0">
                  @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="post" action="{{ route('site.contact.submit') }}" class="contactForm row y-gap-30 pt-60 text-left">
              @csrf
              <div class="col-md-6">
                <div class="form-input ">
                  <input type="text" name="first_name" value="{{ old('first_name') }}" required class="">
                  <label class="lh-1 text-16 text-light-1">First Name</label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="form-input ">
                  <input type="text" name="last_name" value="{{ old('last_name') }}" required class="">
                  <label class="lh-1 text-16 text-light-1">Last Name</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-input ">
                  <input type="email" name="email" value="{{ old('email') }}" required class="">
                  <label class="lh-1 text-16 text-light-1">Email</label>
                </div>
              </div>
              <div class="col-12">
                <div class="form-input">
                    <input type="text" name="phone" value="{{ old('phone') }}" placeholder="">
                    <label class="lh-1 text-16 text-light-1">Phone Number</label>
                </div>
                </div>
              <div class="col-12">
                <div class="form-input ">
                  <textarea name="body" required class="border-1" rows="8">{{ old('body') }}</textarea>
                  <label class="lh-1 ">Message</label>
                </div>
              </div>
              <div class="col-12">
                <button type="submit" class="button -md -type-2 w-1/1 bg-accent-2 -accent-1">SEND YOUR MESSAGE</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>

   <!-- SECTION 1: MAP & LOCATION (Map on Left) -->
<section style="position: relative; padding: 60px 0; background-color: #ffffff; border-top: 1px solid #eee; overflow: hidden;">

    <!-- MAP SIDE (LEFT) - Njia ya Embed isiyohitaji API Key -->
    <div style="position: absolute; top: 0; left: 0; width: 50%; height: 100%;" class="md:relative md:w-full">
        <iframe
            width="100%"
            height="100%"
            frameborder="0"
            style="border:0; filter: grayscale(100%) contrast(1.2) opacity(0.8); min-height: 450px;"
            src="https://maps.google.com/maps?q={{ urlencode($siteSettings->address_line ?? 'Mbezi Beach, Dar es Salaam') }}&t=&z=14&ie=UTF8&iwloc=&output=embed"
            allowfullscreen>
        </iframe>
    </div>

    <div class="container">
        <div class="row justify-end">
            <div class="col-lg-6 col-md-6">
                <div style="padding: 40px 0; position: relative; z-index: 2; background: white;">

                    <!-- Subtitle -->
                    <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
                        <div style="width: 40px; height: 1px; background: #2563eb;"></div>
                        <span style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: #2563eb;">
                            {{ __('Get in Touch') }}
                        </span>
                    </div>

                    <!-- Advanced Title (Line 1 longer than Line 2) -->
                    <h2 style="font-size: clamp(28px, 4vw, 44px); font-weight: 800; color: #051039; line-height: 1.1; letter-spacing: -1.5px; margin-bottom: 30px;">
                        <span style="display: block; margin-bottom: 8px;">{{ __('Find Your Way to the Most Pristine and Serene Coastal Destination') }}</span>
                        <span style="display: block; font-size: 0.7em; color: #2563eb; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">{{ __('Perfectly Situated for Your Convenience') }}</span>
                    </h2>

                    <!-- Contact Details -->
                    <div style="margin-top: 30px; color: #4b5563; font-size: 16px; line-height: 1.8;">
                        <div style="display: flex; align-items: flex-start; gap: 15px; margin-bottom: 25px;">
                            <i class="icon-location text-24 text-accent-1"></i>
                            <div style="font-weight: 500;">
                                {!! nl2br(e($siteSettings->address_line ?? 'Mbezi Beach, Victoria 8007 Tanzania')) !!}
                            </div>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                            <i class="icon-email text-24 text-accent-1"></i>
                            <a href="mailto:{{ $siteSettings->email }}" style="color: inherit; text-decoration: none; font-weight: 600; border-bottom: 1px dashed #ccc;">
                                {{ $siteSettings->email ?? 'info@swisshotel.co.tz' }}
                            </a>
                        </div>

                        <div style="display: flex; align-items: center; gap: 15px;">
                            <i class="icon-phone text-24 text-accent-1"></i>
                            <a href="tel:{{ preg_replace('/\s+/', '', (string)($siteSettings->phone ?? '')) }}" style="color: inherit; text-decoration: none; font-weight: 600;">
                                {{ $siteSettings->phone }}
                            </a>
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div style="margin-top: 45px;">
                        <a href="https://maps.google.com/?q={{ urlencode($siteSettings->address_line ?? 'Mbezi Beach, Victoria 8007 Tanzania') }}" target="_blank"
                           style="display: inline-flex; align-items: center; padding: 10px 10px 10px 30px; background: #051039; color: white !important; border-radius: 100px; font-weight: 700; text-decoration: none; font-size: 14px; transition: 0.3s; box-shadow: 0 10px 25px rgba(5,16,57,0.15);">
                            {{ __('Open in Google Maps') }}
                            <div style="width: 40px; height: 40px; background: rgba(255,255,255,0.15); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 20px;">
                                <i class="icon-arrow-top-right"></i>
                            </div>
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

<style>
    /* Fix for mobile view stacking */
    @media (max-width: 991px) {
        div[style*="position: absolute; top: 0; left: 0; width: 50%"] {
            position: relative !important;
            width: 100% !important;
            height: 350px !important;
        }
        .col-lg-6 {
            width: 100% !important;
        }
    }
</style>

<style>
    /* Button Style consistency */
    .btn-advanced-pill {
        display: inline-flex;
        align-items: center;
        padding: 8px 8px 8px 25px;
        background: #051039;
        color: white !important;
        border-radius: 100px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    .btn-advanced-pill:hover { background: #2563eb; transform: translateY(-3px); }
    .btn-advanced-pill .icon-circle {
        width: 40px; height: 40px; background: rgba(255,255,255,0.1);
        border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: 15px;
    }

    /* Grid Hover Effect */
    .imageGrid__item:hover img {
        transform: scale(1.1);
    }
    .imageGrid__item:hover div {
        background: rgba(37, 99, 235, 0.4) !important;
    }
    .imageGrid__item:hover i {
        opacity: 1 !important;
    }
</style>
@endsection
