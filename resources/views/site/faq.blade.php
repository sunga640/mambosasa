@extends('layouts.site')

@section('title', __('Frequently Asked Questions'))

@section('content')
    <!-- SECTION 1: HERO (Advanced Typography) -->
    <section data-anim-wrap class="pageHero -type-1 -items-center" style="background-color: #051039; padding: 120px 0;">
      <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/8/1.png', 'heroUrl' => $heroUrl ?? null])
        <div style="position: absolute; inset: 0; background: rgba(5,16,57,0.7);"></div>
      </div>

      <div class="container">
        <div class="row justify-center">
          <div class="col-xl-10">
            <div data-split='lines' data-anim-child="split-lines delay-3" class="pageHero__content text-center">

              <div style="display: flex; align-items: center; justify-content: center; gap: 15px; margin-bottom: 25px;">
                  <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.5);"></div>
                  <span style="font-size: 13px; font-weight: 700; text-transform: uppercase; letter-spacing: 3px; color: #ffffff;">
                      {{ __('Information Desk') }}
                  </span>
                  <div style="width: 40px; height: 1px; background: rgba(255,255,255,0.5);"></div>
              </div>

              <h1 style="font-size: clamp(32px, 5vw, 56px); font-weight: 800; color: #ffffff; line-height: 1.1; letter-spacing: -1.5px;">
                <span style="display: block; margin-bottom: 10px;">{{ __('Everything You Need to Know Before Planning a Comfortable Stay at :hotel', ['hotel' => $siteSettings->hotelDisplayName()]) }}</span>
                <span style="display: block; font-size: 0.6em; color: rgba(255,255,255,0.8); font-weight: 600; text-transform: uppercase; letter-spacing: 1px;">
                    {{ __('Frequently Asked Questions & Travel Guidelines') }}
                </span>
              </h1>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- SECTION 2: FAQ ACCORDIONS -->
    <section class="layout-pt-lg layout-pb-lg bg-white">
      <div class="container">
        <div class="row justify-center">
          <div class="col-xl-8 col-lg-10">

            <!-- Category: Payments & Pricing -->
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px;">
                <div style="width: 30px; height: 2px; background: #2563eb;"></div>
                <h2 style="font-size: 32px; font-weight: 800; color: #051039;">{{ __('Payments & Pricing') }}</h2>
            </div>

            <div class="accordion -type-1 row y-gap-15 js-accordion">

              <div class="col-12">
                <div class="accordion__item border-light rounded-12" style="border: 1px solid #eee; overflow: hidden;">
                  <div class="accordion__button d-flex items-center justify-between px-30 py-25 bg-white" style="cursor: pointer;">
                    <div class="text-20 fw-700 color-dark-1">{{ __('Are there any hidden booking fees?') }}</div>
                    <div class="accordion__icon size-30 flex-center bg-light-2 rounded-full">
                      <i class="icon-plus text-12"></i>
                      <i class="icon-minus text-12"></i>
                    </div>
                  </div>

                  <div class="accordion__content">
                    <div class="px-30 pb-30">
                      <p class="text-16 lh-17 color-light-1">
                        {{ __('No, we believe in complete transparency. The nightly rate you see in Tanzanian Shillings (TZS) is the final price for your accommodation. Local taxes are included in the quoted price unless stated otherwise during the final step of booking.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="accordion__item border-light rounded-12" style="border: 1px solid #eee; overflow: hidden;">
                  <div class="accordion__button d-flex items-center justify-between px-30 py-25 bg-white" style="cursor: pointer;">
                    <div class="text-20 fw-700 color-dark-1">{{ __('What payment methods do you accept?') }}</div>
                    <div class="accordion__icon size-30 flex-center bg-light-2 rounded-full">
                      <i class="icon-plus text-12"></i>
                      <i class="icon-minus text-12"></i>
                    </div>
                  </div>

                  <div class="accordion__content">
                    <div class="px-30 pb-30">
                      <p class="text-16 lh-17 color-light-1">
                        {{ __('We accept major credit cards (Visa, Mastercard), mobile money transfers popular in Tanzania, and direct bank transfers. For walk-in guests, we also accept cash payments at the reception.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

            </div>


            <!-- Category: Reservations & Cancellations -->
            <div style="display: flex; align-items: center; gap: 15px; margin-bottom: 20px; margin-top: 80px;">
                <div style="width: 30px; height: 2px; background: #2563eb;"></div>
                <h2 style="font-size: 32px; font-weight: 800; color: #051039;">{{ __('Reservations & Cancellations') }}</h2>
            </div>

            <div class="accordion -type-1 row y-gap-15 js-accordion">

              <div class="col-12">
                <div class="accordion__item border-light rounded-12" style="border: 1px solid #eee; overflow: hidden;">
                  <div class="accordion__button d-flex items-center justify-between px-30 py-25 bg-white" style="cursor: pointer;">
                    <div class="text-20 fw-700 color-dark-1">{{ __('Can I modify my reservation dates?') }}</div>
                    <div class="accordion__icon size-30 flex-center bg-light-2 rounded-full">
                      <i class="icon-plus text-12"></i>
                      <i class="icon-minus text-12"></i>
                    </div>
                  </div>

                  <div class="accordion__content">
                    <div class="px-30 pb-30">
                      <p class="text-16 lh-17 color-light-1">
                        {{ __('Yes, modifications are subject to availability. If you booked directly through our website, you can use the guest portal or contact our team to request a date change. Please note that nightly rates may vary based on your new selected dates.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="accordion__item border-light rounded-12" style="border: 1px solid #eee; overflow: hidden;">
                  <div class="accordion__button d-flex items-center justify-between px-30 py-25 bg-white" style="cursor: pointer;">
                    <div class="text-20 fw-700 color-dark-1">{{ __('What is your refund policy for cancellations?') }}</div>
                    <div class="accordion__icon size-30 flex-center bg-light-2 rounded-full">
                      <i class="icon-plus text-12"></i>
                      <i class="icon-minus text-12"></i>
                    </div>
                  </div>

                  <div class="accordion__content">
                    <div class="px-30 pb-30">
                      <p class="text-16 lh-17 color-light-1">
                        {{ __('Refunds depend on the room category and the notice period provided. Generally, cancellations made 48 hours prior to check-in are eligible for a full refund. Specific "Non-refundable" rates are excluded from this policy.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="accordion__item border-light rounded-12" style="border: 1px solid #eee; overflow: hidden;">
                  <div class="accordion__button d-flex items-center justify-between px-30 py-25 bg-white" style="cursor: pointer;">
                    <div class="text-20 fw-700 color-dark-1">{{ __('What time is check-in and check-out?') }}</div>
                    <div class="accordion__icon size-30 flex-center bg-light-2 rounded-full">
                      <i class="icon-plus text-12"></i>
                      <i class="icon-minus text-12"></i>
                    </div>
                  </div>

                  <div class="accordion__content">
                    <div class="px-30 pb-30">
                      <p class="text-16 lh-17 color-light-1">
                        {{ __('Standard check-in time is at 2:00 PM and check-out is at 11:00 AM. Early check-in or late check-out can be requested and is subject to room availability on that day.') }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>

            </div>

            <!-- Contact Link -->
            <div class="text-center mt-60">
                <p class="text-18 color-light-1">{{ __('Still have questions?') }}</p>
                <a href="{{ route('site.page', ['slug' => 'contact']) }}" class="btn-advanced-navy mt-20">
                    {{ __('Contact Our Concierge') }}
                    <i class="icon-arrow-top-right ml-15"></i>
                </a>
            </div>

          </div>
        </div>
      </div>
    </section>

    <!-- STYLES -->
    <style>
        .btn-advanced-navy {
            display: inline-flex;
            align-items: center;
            background: #051039;
            color: white !important;
            padding: 18px 45px;
            border-radius: 100px;
            font-weight: 700;
            text-decoration: none;
            transition: 0.3s;
        }
        .btn-advanced-navy:hover {
            background: #2563eb;
            transform: translateY(-3px);
        }
        .accordion__item {
            transition: 0.3s;
            background: #ffffff;
        }
        .accordion__item:hover {
            border-color: #2563eb !important;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }
        .accordion__button .text-20 {
            transition: 0.3s;
        }
        .accordion__item.is-active .accordion__button .text-20 {
            color: #2563eb;
        }
        .bg-light-2 { background-color: #f8fafc !important; }
        .color-dark-1 { color: #051039 !important; }
        .color-light-1 { color: #697488 !important; }
    </style>
@endsection
