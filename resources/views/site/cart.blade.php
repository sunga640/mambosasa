@extends('layouts.site')

@section('title', __('Cart'))

@section('content')
<section data-anim-wrap class="pageHero -type-1 -items-center">
      <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/1.png', 'heroUrl' => $heroUrl ?? null])
      </div>

      <div class="container">
        <div class="row justify-center">
          <div class="col-auto">
            <div data-split='lines' data-anim-child="split-lines delay-3" class="pageHero__content text-center">
              <h1 class="pageHero__title text-white">{{ __('Cart') }}</h1>
              <p class="pageHero__text text-white">{{ __('Review room selections before you continue to booking at :name.', ['name' => $siteSettings->company_name ?? config('app.name')]) }}</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="layout-pt-lg layout-pb-lg">
      <div class="container">
        @if (session('status'))
          <div class="mb-30 p-20 bg-light-1 rounded-8 text-15 text-center" role="status">{{ session('status') }}</div>
        @endif

        <div class="row y-gap-40 justify-between">
          <div class="col-xl-7 col-lg-7">
            <div class="tableWrap">
              <table class="table -type-1">
                <tr class="table__header bg-light-1">
                  <th>PRODUCT</th>
                  <th>PRICE</th>
                  <th>QUANTITY</th>
                  <th>SUBTOTAL</th>
                </tr>

                @forelse ($cartLines as $line)
                  @php
                    $room = $line['room'];
                    $qty = $line['qty'];
                  @endphp
                  <tr>
                    <td>
                      <div class="d-flex items-center">
                        <img src="{{ $room->cardImageUrl() }}" alt="{{ $room->name }}" style="width:120px;height:auto;object-fit:cover;">

                        <div class="ml-30">
                          <h2 class="text-24 fw-500">{{ $room->name }}</h2>
                          <div class="text-15 mt-15"><span class="fw-500">{{ __('Branch') }}:</span> {{ $room->branch?->name ?? '—' }}</div>
                          <div class="text-15 mt-5"><span class="fw-500">{{ __('Status') }}:</span> {{ $room->status->label() }}</div>
                          <form method="post" action="{{ route('site.cart.remove') }}" class="mt-15">
                            @csrf
                            <input type="hidden" name="room_id" value="{{ $room->id }}">
                            <button type="submit" class="text-14 text-accent-1 underline">{{ __('Remove') }}</button>
                          </form>
                        </div>
                      </div>
                    </td>

                    <td>TZS {{ number_format((float) $room->price, 0) }}</td>
                    <td>{{ $qty }}</td>
                    <td>TZS {{ number_format((float) $line['line_total'], 0) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center py-40">
                      {{ __('Your cart is empty.') }}
                      <a href="{{ route('site.home') }}" class="d-block mt-20 text-accent-1">{{ __('Browse rooms') }}</a>
                    </td>
                  </tr>
                @endforelse
              </table>
            </div>

            <div class="row y-gap-30 justify-between pt-30">
              <div class="col-auto">
                <div class="row y-gap-30">
                  <div class="col-auto">
                    <div class="contactForm">
                      <div class="form-input">
                        <div class="form-input -h-55">
                          <input type="text" disabled class="" placeholder="{{ __('Not available yet') }}">
                          <label class="lh-1 text-16 text-light-1">Coupon code</label>
                        </div>
                      </div>
                    </div>
                  </div>

                  <div class="col-auto">
                    <button type="button" class="button -md -type-2 bg-accent-2 -accent-1" disabled>{{ __('Apply Coupon') }}</button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-4 col-lg-5">
            <div class="sidebar px-40 py-40 bg-light-1">
              <h3 class="text-30">Cart totals</h3>

              <div class="row y-gap-15 pt-30">
                <div class="col-12">
                  <div class="d-flex justify-between">
                    <div class="fw-500">Subtotal</div>
                    <div class="fw-500">TZS {{ number_format($subtotal, 0) }}</div>
                  </div>
                </div>

                <div class="col-12">
                  <div class="line -horizontal bg-border"></div>
                </div>

                <div class="col-12">
                  <div class="d-flex justify-between">
                    <div class="fw-500">Total</div>
                    <div class="fw-500">TZS {{ number_format($subtotal, 0) }}</div>
                  </div>
                </div>
              </div>

              @if (count($cartLines) > 0)
                <a href="{{ route('site.booking', ['room' => $cartLines[0]['room']->id]) }}" class="button -md bg-accent-1 -accent-2 text-white w-1/1 mt-30 d-block text-center">{{ __('Proceed to checkout') }}</a>
              @else
                <a href="{{ route('site.home') }}" class="button -md bg-accent-1 -accent-2 text-white w-1/1 mt-30 d-block text-center">{{ __('Browse rooms') }}</a>
              @endif
            </div>
          </div>
        </div>
      </div>
    </section>
@endsection
