@extends('layouts.site')

@section('title', __('Checkout'))

@section('content')
<section data-anim-wrap class="pageHero -type-1 -items-center">
      <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/1.png'])
      </div>

      <div class="container">
        <div class="row justify-center">
          <div class="col-auto">
            <div data-split='lines' data-anim-child="split-lines delay-3" class="pageHero__content text-center">
              <h1 class="pageHero__title text-white">{{ __('Checkout') }}</h1>
              <p class="pageHero__text text-white">{{ __('Complete your details to confirm your stay. For live reservations, use “Book your stay” from the main menu.') }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="layout-pt-lg layout-pb-lg">
      <div class="container">
        <div class="row y-gap-40 justify-between">
          <div class="col-xl-7 col-lg-7">
            <h2 class="text-30 mb-50">Billing details</h2>

            <div class="contactForm row y-gap-30">
              <div class="col-md-6">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">First Name</label>
                </div>

              </div>

              <div class="col-md-6">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Last Name</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Company name (optional)</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Country / Region *</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">House number and street name</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Apartment, suite, unit, etc. (optional)</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">State *</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">ZIP *</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Phone *</label>
                </div>

              </div>

              <div class="col-12">

                <div class="form-input ">
                  <input type="text" required class="">
                  <label class="lh-1 text-16 text-light-1">Email Address *</label>
                </div>

              </div>
            </div>


            <h2 class="text-30 mb-50 pt-100 sm:pt-50">Additional information</h2>

            <div class="contactForm row y-gap-30">
              <div class="col-12">

                <div class="form-input ">
                  <textarea required class="border-1" rows="10"></textarea>
                  <label class="lh-1 ">Order notes (optional)</label>
                </div>

              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-5">
            <div class="sidebar">
              <div class="px-40 py-40 bg-light-1">
                <h3 class="text-30">Your order</h3>

                <div class="border-table-1 y-gap-40 pt-30">
                  <div class="d-flex justify-between">
                    <div class="fw-500">PRODUCT</div>
                    <div class="fw-500">SUBTOTAL</div>
                  </div>

                  <div class="d-flex justify-between">
                    <div class="">
                      Luxury Villa Suite × 1

                      <div class="mt-25">
                        <div><span class="fw-500">Date:</span> 2023-11-08 - 2023-11-09</div>
                        <div><span class="fw-500">Details:</span> Rooms: 1, Adults: 1,</div>
                      </div>
                    </div>

                    <div class="">$4.321.89</div>
                  </div>

                  <div class="d-flex justify-between">
                    <div class="fw-500">Subtotal</div>
                    <div class="fw-500">$12,345.65</div>
                  </div>

                  <div class="d-flex justify-between">
                    <div class="fw-500">Total</div>
                    <div class="fw-500">$122.00</div>
                  </div>
                </div>
              </div>

              <div class="px-40 py-40 bg-light-1 mt-30">
                <h3 class="text-30">Payment</h3>

                <div class="y-gap-30 pt-30">
                  <div>

                    <div class="form-radio d-flex items-center ">
                      <div class="radio">
                        <input type="radio" name="name">
                        <div class="radio__mark">
                          <div class="radio__icon"></div>
                        </div>
                      </div>
                      <div class="fw-500 lh-1 ml-10">Direct bank transfer</div>
                    </div>

                    <div class="mt-20">
                      Make your payment directly into our bank account. Please use your Order ID as the payment reference.Your order will not be shipped until the funds have cleared in our account.
                    </div>
                  </div>


                  <div class="form-radio d-flex items-center ">
                    <div class="radio">
                      <input type="radio" name="name">
                      <div class="radio__mark">
                        <div class="radio__icon"></div>
                      </div>
                    </div>
                    <div class="fw-500 lh-1 ml-10">Check payments</div>
                  </div>


                  <div class="form-radio d-flex items-center ">
                    <div class="radio">
                      <input type="radio" name="name">
                      <div class="radio__mark">
                        <div class="radio__icon"></div>
                      </div>
                    </div>
                    <div class="fw-500 lh-1 ml-10">Cash on delivery</div>
                  </div>


                  <div class="form-radio d-flex items-center ">
                    <div class="radio">
                      <input type="radio" name="name">
                      <div class="radio__mark">
                        <div class="radio__icon"></div>
                      </div>
                    </div>
                    <div class="fw-500 lh-1 ml-10">PayPal</div>
                  </div>

                </div>
              </div>

              <button class="button -md bg-accent-1 -accent-2 text-white w-1/1 mt-30">PLACE ORDER</button>
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
