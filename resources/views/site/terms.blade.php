@extends('layouts.site')

@section('title', __('Terms and Conditions'))

@section('content')

    <!-- SECTION 1: HERO (Advanced Typography) -->
    <section data-anim-wrap class="pageHero -type-1 -items-center" style="background-color: #051039; padding: 120px 0;">
      <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/6.png', 'heroUrl' => $heroUrl ?? null])
        <div style="position: absolute; inset: 0; background: rgba(5,16,57,0.75);"></div>
      </div>

      <div class="container">
        <div class="row justify-center">
          <div class="col-xl-10">
            <div data-split='lines' data-anim-child="split-lines delay-3" class="pageHero__content text-center">

              <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 25px;">
                  <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.5);"></div>
                  <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: #ffffff;">
                      {{ __('Guest Guidelines') }}
                  </span>
                  <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.5);"></div>
              </div>

              <h1 style="font-size: clamp(32px, 5vw, 56px); font-weight: 800; color: #ffffff; line-height: 1.1; letter-spacing: -1.5px;">
                <span style="display: block; margin-bottom: 10px;">{{ __('Legal Terms and Conditions Designed to Ensure Your Absolute Comfort and Safety') }}</span>

              </h1>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: TABS & CONTENT -->
    <section class="layout-pt-md layout-pb-lg bg-light-2">
      <div class="container">
        <div class="tabs -type-1 js-tabs">
          <div class="row justify-center">
            <div class="col-xl-10 col-lg-11">

              <!-- Tab Controls (Pill Style) -->
              <div class="tabs__controls d-flex justify-center js-tabs-controls custom-tabs-wrapper">

                <button class="tabs__button js-tabs-button is-tab-el-active" data-tab-target=".-tab-item-1">
                    <i class="icon-credit-card text-20 mr-10"></i>
                    {{ __('Booking & Payments') }}
                </button>

                <button class="tabs__button js-tabs-button" data-tab-target=".-tab-item-2">
                    <i class="icon-home text-20 mr-10"></i>
                    {{ __('Property Rules') }}
                </button>

                <button class="tabs__button js-tabs-button" data-tab-target=".-tab-item-3">
                    <i class="icon-clock text-20 mr-10"></i>
                    {{ __('Refunds & Cancellations') }}
                </button>

                <button class="tabs__button js-tabs-button" data-tab-target=".-tab-item-4">
                    <i class="icon-shield text-20 mr-10"></i>
                    {{ __('Health & Safety') }}
                </button>

              </div>

              <!-- Content Area (Card Style) -->
              <div class="tabs__content mt-40 js-tabs-content">
                <div class="bg-white rounded-24 shadow-sm p-40 sm:p-25 border-light">

                    <!-- Tab 1: Booking & Payments -->
                    <div class="tabs__pane -tab-item-1 is-tab-el-active">
                      <div class="row y-gap-30">
                        <div class="col-12">
                          <h2 class="text-26 fw-700 color-dark-1">{{ __('Guaranteed Reservations & Payment Terms') }}</h2>
                          <div class="mt-20 lh-18 color-light-1">
                            {{ __('All reservations made through our portal are subject to availability and confirmation. To secure your stay, a valid form of identification and a deposit or full payment in Tanzanian Shillings (TZS) is required at the time of booking or upon arrival as per the selected rate plan.') }}
                          </div>

                          <div class="mt-40">
                              <h3 class="text-20 fw-700 color-dark-1 mb-20">{{ __('Check-in Requirements') }}</h3>
                              <div class="row y-gap-15">
                                  <div class="col-md-6 d-flex">
                                      <i class="icon-check text-blue-1 mt-5 mr-15"></i>
                                      <p class="text-15">{{ __('Standard check-in time is 2:00 PM.') }}</p>
                                  </div>
                                  <div class="col-md-6 d-flex">
                                      <i class="icon-check text-blue-1 mt-5 mr-15"></i>
                                      <p class="text-15">{{ __('Valid government-issued ID is mandatory.') }}</p>
                                  </div>
                                  <div class="col-md-6 d-flex">
                                      <i class="icon-check text-blue-1 mt-5 mr-15"></i>
                                      <p class="text-15">{{ __('Guests must be 18 years or older.') }}</p>
                                  </div>
                                  <div class="col-md-6 d-flex">
                                      <i class="icon-check text-blue-1 mt-5 mr-15"></i>
                                      <p class="text-15">{{ __('Refundable security deposit may apply.') }}</p>
                                  </div>
                              </div>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- Tab 2: Property Rules -->
                    <div class="tabs__pane -tab-item-2">
                      <h2 class="text-26 fw-700 color-dark-1">{{ __('Guest Conduct & Property Integrity') }}</h2>
                      <p class="mt-20 color-light-1">{{ __('We strive to maintain a peaceful sanctuary for all our visitors.') }}</p>
                      <div class="mt-30">
                          <div class="p-20 rounded-12 bg-light-2 mb-10 d-flex items-center">
                              <i class="icon-no-smoking text-20 mr-15 text-blue-1"></i>
                              <span class="fw-500">{{ __('Strict Non-Smoking policy inside all rooms.') }}</span>
                          </div>
                          <div class="p-20 rounded-12 bg-light-2 mb-10 d-flex items-center">
                              <i class="icon-moon text-20 mr-15 text-blue-1"></i>
                              <span class="fw-500">{{ __('Quiet hours: 10:00 PM – 7:00 AM.') }}</span>
                          </div>
                      </div>
                    </div>

                    <!-- Tab 3 & 4 (Zinafuata muundo huohuo wa hapo juu) -->
                    <div class="tabs__pane -tab-item-3">
                        <h2 class="text-26 fw-700 color-dark-1">{{ __('Cancellation Policy') }}</h2>
                        <p class="mt-20 color-light-1">{{ __('Refunds are processed within 7-14 business days to the original payment method.') }}</p>
                    </div>

                    <div class="tabs__pane -tab-item-4">
                        <h2 class="text-26 fw-700 color-dark-1">{{ __('Your Well-being') }}</h2>
                        <p class="mt-20 color-light-1">{{ __('Your safety is our priority. 24-hour security surveillance is active.') }}</p>
                    </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- CUSTOM STYLES -->
    <style>
        .tabs__button {
            transition: 0.3s;
            color: #697488;
            border-bottom: 2px solid transparent;
            padding-bottom: 5px;
        }
        .tabs__button.is-tab-el-active {
            color: #2563eb !important;
            border-color: #2563eb;
        }
        .tabs__button:hover {
            color: #051039;
        }
        .ulList.-type-1 li {
            position: relative;
            padding-left: 25px;
            list-style: none;
            color: #4b5563;
        }
        .ulList.-type-1 li::before {
            content: "\e918"; /* Check icon or dot */
            font-family: 'icomoon';
            position: absolute;
            left: 0;
            top: 2px;
            color: #2563eb;
            font-weight: 700;
        }
        .color-dark-1 { color: #051039 !important; }
        .color-light-1 { color: #697488 !important; }
    </style>
@endsection
