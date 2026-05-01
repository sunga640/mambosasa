@extends('layouts.site')

@section('title', __('Kitchen Menu'))
<br><br>
@section('content')
    <section class="layout-pt-lg layout-pb-lg qr-menu-shell" style="padding-top:clamp(10.75rem, 16vw, 12.5rem);">
        <div class="container">
            <style>
                .qr-menu-shell {
                    padding-top: clamp(10.75rem, 16vw, 12.5rem) !important;
                }
                .qr-page {
                    display: grid;
                    gap: 1.5rem;
                }
                .qr-menu-hero {
                    padding-top: .85rem;
                }
                .qr-menu-room-pill {
                    display: inline-flex;
                    align-items: center;
                    gap: .55rem;
                    width: fit-content;
                    max-width: 100%;
                    padding: .6rem .9rem;
                    border: 1px solid rgba(213, 172, 66, .34);
                    background: rgba(213, 172, 66, .08);
                    color: #7a5b15;
                    font-size: .82rem;
                    font-weight: 700;
                    letter-spacing: .08em;
                    text-transform: uppercase;
                    line-height: 1.35;
                    flex-wrap: wrap;
                }
                .qr-menu-card {
                    padding: 1rem;
                    border: 1px solid rgba(18,34,35,.12);
                    background: #fff;
                    display: grid;
                    gap: .85rem;
                }
                .qr-menu-card__footer {
                    display: flex;
                    justify-content: space-between;
                    gap: .85rem;
                    align-items: end;
                    flex-wrap: wrap;
                }
                .qr-menu-card__quantity {
                    display: grid;
                    gap: .35rem;
                    min-width: 120px;
                }
                .qr-menu-room-title {
                    line-height: 1.15;
                    word-break: break-word;
                    margin-top: .9rem !important;
                }
                .qr-unpaid-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                    gap: 1rem;
                }
                .qr-unpaid-card {
                    border: 1px solid rgba(18,34,35,.12);
                    background: #fff;
                    padding: 1rem;
                    display: grid;
                    gap: .75rem;
                }
                .qr-unpaid-card__top {
                    display: flex;
                    justify-content: space-between;
                    gap: .75rem;
                    flex-wrap: wrap;
                }
                .qr-unpaid-card__methods {
                    display: grid;
                    gap: .55rem;
                }
                .qr-unpaid-card__method-select {
                    display: grid;
                    gap: .4rem;
                }
                .qr-unpaid-card__choices {
                    display: grid;
                    gap: .7rem;
                    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                    font-size: .92rem;
                }
                .qr-unpaid-card__choices label {
                    display: grid;
                    gap: .45rem;
                    margin: 0;
                    padding: .75rem;
                    border: 1px solid rgba(18,34,35,.12);
                    border-radius: 10px;
                    background: #fafafa;
                    justify-items: start;
                    text-align: left;
                }
                .qr-unpaid-card__choices label span:first-of-type {
                    display: flex;
                    align-items: center;
                    gap: .45rem;
                    font-weight: 600;
                    justify-content: flex-start;
                }
                .qr-unpaid-card__hint {
                    font-size: .82rem;
                    opacity: .72;
                    line-height: 1.45;
                }
                .qr-closed-panel {
                    position: relative;
                    overflow: hidden;
                    padding: clamp(1.5rem, 3vw, 2.4rem);
                    border: 1px solid rgba(184, 149, 93, .28);
                    background:
                        radial-gradient(circle at top right, rgba(184, 149, 93, .2), transparent 32%),
                        radial-gradient(circle at bottom left, rgba(30, 77, 107, .12), transparent 34%),
                        linear-gradient(135deg, #fffaf2 0%, #ffffff 55%, #f8fbfd 100%);
                    box-shadow: 0 24px 60px rgba(18, 34, 35, .08);
                    display: grid;
                    gap: 1.4rem;
                }
                .qr-closed-panel::after {
                    content: "";
                    position: absolute;
                    inset: 0;
                    background:
                        linear-gradient(120deg, rgba(255,255,255,.54), transparent 35%),
                        repeating-linear-gradient(135deg, rgba(184, 149, 93, .05) 0 10px, transparent 10px 20px);
                    pointer-events: none;
                }
                .qr-closed-panel > * {
                    position: relative;
                    z-index: 1;
                }
                .qr-closed-kicker {
                    display: inline-flex;
                    align-items: center;
                    gap: .55rem;
                    width: fit-content;
                    padding: .48rem .82rem;
                    border-radius: 999px;
                    background: rgba(154, 52, 18, .08);
                    border: 1px solid rgba(154, 52, 18, .14);
                    color: #9a3412;
                    font-size: .78rem;
                    font-weight: 700;
                    letter-spacing: .08em;
                    text-transform: uppercase;
                }
                .qr-closed-kicker span {
                    width: .55rem;
                    height: .55rem;
                    border-radius: 999px;
                    background: #f59e0b;
                    box-shadow: 0 0 0 6px rgba(245, 158, 11, .12);
                }
                .qr-closed-head {
                    display: grid;
                    gap: .75rem;
                    max-width: 44rem;
                }
                .qr-closed-head h2 {
                    margin: 0;
                    color: #1b2c31;
                    font-size: clamp(2rem, 4vw, 3.15rem);
                    line-height: .95;
                    letter-spacing: -.03em;
                }
                .qr-closed-copy {
                    margin: 0;
                    color: #485c60;
                    font-size: 1.02rem;
                    line-height: 1.75;
                }
                .qr-closed-highlight {
                    display: inline-flex;
                    align-items: center;
                    gap: .65rem;
                    width: fit-content;
                    padding: .85rem 1rem;
                    border-radius: 16px;
                    background: #17352f;
                    color: #f5efe2;
                    box-shadow: 0 14px 34px rgba(23, 53, 47, .18);
                }
                .qr-closed-highlight strong {
                    display: block;
                    font-size: .76rem;
                    letter-spacing: .12em;
                    text-transform: uppercase;
                    color: rgba(245, 239, 226, .74);
                }
                .qr-closed-highlight span:last-child {
                    display: block;
                    font-size: 1.18rem;
                    font-weight: 700;
                    color: #fff;
                }
                .qr-closed-grid {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
                    gap: 1rem;
                }
                .qr-closed-card {
                    padding: 1rem 1.05rem;
                    border-radius: 18px;
                    border: 1px solid rgba(18, 34, 35, .09);
                    background: rgba(255, 255, 255, .82);
                    backdrop-filter: blur(6px);
                    box-shadow: inset 0 1px 0 rgba(255,255,255,.5);
                    display: grid;
                    gap: .35rem;
                }
                .qr-closed-card small {
                    color: #8a6c33;
                    font-size: .74rem;
                    font-weight: 700;
                    letter-spacing: .12em;
                    text-transform: uppercase;
                }
                .qr-closed-card strong {
                    color: #14282d;
                    font-size: 1.05rem;
                    line-height: 1.4;
                }
                .qr-closed-note {
                    color: #6a7e82;
                    font-size: .92rem;
                    line-height: 1.65;
                }
                @media (max-width: 767px) {
                    .qr-menu-shell {
                        padding-top: 13rem !important;
                    }
                    .qr-menu-hero {
                        padding-top: 1.15rem;
                    }
                    .qr-menu-room-pill {
                        font-size: .74rem;
                        letter-spacing: .06em;
                    }
                    .qr-menu-room-title {
                        font-size: 1.58rem;
                        line-height: 1.14;
                        margin-top: 0 !important;
                    }
                }
            </style>

            <div class="qr-page">
                <div class="qr-menu-hero" style="padding:1.5rem;border:1px solid rgba(18,34,35,.12);background:#fff;">
                    <br>
                    <h1 class="text-30 mt-10 qr-menu-room-title">{{ $qr->room?->name }} - {{ __('Kitchen menu') }}</h1>
                    <p class="text-15 mt-10" style="opacity:.8;max-width:48rem;">{{ __('Scan, choose dishes, and send the order directly to the hotel kitchen. The team will prepare and deliver it to your room.') }}</p>
                    @if (($serviceAvailability['is_configured'] ?? false) && ! ($serviceAvailability['is_available'] ?? true))
                        <div class="mt-15" style="padding:1rem;border:1px solid #f59e0b;background:#fff7ed;color:#9a3412;">
                            {{ $serviceAvailability['message'] }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="mt-15" style="padding:1rem;border:1px solid #86efac;background:#ecfdf5;color:#166534;">{{ session('status') }}</div>
                    @endif
                    @if ($errors->any())
                        <div class="mt-15" style="padding:1rem;border:1px solid #fca5a5;background:#fff5f5;color:#991b1b;">
                            {{ $errors->first() }}
                        </div>
                    @endif
                </div>

                @if ($recentOrders->count())
                    <section style="padding:1.5rem;border:1px solid rgba(18,34,35,.12);background:#fff;">
                        <div style="display:flex;justify-content:space-between;gap:1rem;align-items:end;flex-wrap:wrap;">
                            <div>
                                <h2 class="text-22" style="margin:0;">{{ __('Orders waiting payment') }}</h2>
                                <p class="text-14 mt-10" style="opacity:.75;margin-bottom:0;">{{ __('Each order stays on its own card so the guest can choose the right payment method for that exact order.') }}</p>
                            </div>
                        </div>
                        <div class="qr-unpaid-grid mt-20">
                            @foreach ($recentOrders as $order)
                                <article class="qr-unpaid-card">
                                    <div class="qr-unpaid-card__top">
                                        <div>
                                            <strong>{{ $order->statusEnum()->label() }}</strong>
                                            <div class="text-13" style="opacity:.72;">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
                                        </div>
                                        <div style="text-align:right;">
                                            <strong>{{ number_format((float) $order->total_amount, 0) }} TZS</strong>
                                            <div class="text-13" style="opacity:.72;">{{ $order->paymentStatusLabel() }}</div>
                                        </div>
                                    </div>

                                    <div class="text-14">
                                        @foreach ($order->items as $item)
                                            <div>{{ $item->item_name }} x {{ $item->quantity }}</div>
                                        @endforeach
                                    </div>

                                    @if (\App\Models\RoomServiceOrder::supportsPaymentTracking() && ! $order->isPaid())
                                        <form method="POST" action="{{ route('site.room-service-orders.payment', $order->public_reference) }}" class="qr-unpaid-card__methods">
                                            @csrf
                                            <div class="qr-unpaid-card__method-select">
                                                <label for="order-method-{{ $order->id }}">{{ __('Online payment methods from our system') }}</label>
                                                <select id="order-method-{{ $order->id }}" name="booking_method_id" style="padding:.55rem .7rem;border:1px solid rgba(18,34,35,.18);border-radius:8px;width:100%;">
                                                    <option value="">{{ __('Choose online payment method') }}</option>
                                                    @foreach ($paymentMethods as $method)
                                                        <option value="{{ $method->id }}" @selected($order->booking_method_id === $method->id)>{{ $method->name }}</option>
                                                    @endforeach
                                                </select>
                                                <div class="qr-unpaid-card__hint">{{ __('Cash and keep-bill stay below, while online methods come directly from the active system payment list.') }}</div>
                                            </div>
                                            <div class="qr-unpaid-card__choices">
                                                <label>
                                                    <span><input type="radio" name="payment_choice" value="online" checked> {{ __('Use selected method') }}</span>
                                                    <span class="qr-unpaid-card__hint">{{ __('Use one of the online methods listed above.') }}</span>
                                                </label>
                                                <label>
                                                    <span><input type="radio" name="payment_choice" value="cash"> {{ __('Pay cash') }}</span>
                                                    <span class="qr-unpaid-card__hint">{{ __('Kitchen or reception confirms this after receiving cash.') }}</span>
                                                </label>
                                                <label>
                                                    <span><input type="radio" name="payment_choice" value="bill_later"> {{ __('Keep bill') }}</span>
                                                    <span class="qr-unpaid-card__hint">{{ __('Add this order to checkout settlement at reception.') }}</span>
                                                </label>
                                            </div>
                                            <button type="submit" class="button -sm -accent-1 bg-accent-1 text-white" style="border:none;padding:.75rem 1rem;width:max-content;">
                                                {{ __('Save payment choice') }}
                                            </button>
                                        </form>
                                    @endif
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif

                @if (($serviceAvailability['is_configured'] ?? false) && ! ($serviceAvailability['is_available'] ?? true))
                    <section class="qr-closed-panel">
                        <div class="qr-closed-kicker">
                            <span></span>
                            {{ __('Kitchen service paused') }}
                        </div>
                        <div class="qr-closed-head">
                            <h2>{{ __('The menu is currently unavailable') }}</h2>
                            <p class="qr-closed-copy">
                                {{ $serviceAvailability['message'] ?? __('Please wait until the next kitchen service time.') }}
                            </p>
                        </div>
                        @if (! empty($serviceAvailability['next_start_at']))
                            <div class="qr-closed-highlight">
                                <div>
                                    <strong>{{ __('Next opening time') }}</strong>
                                    <span>{{ $serviceAvailability['next_start_at']->format('l, g:i A') }}</span>
                                </div>
                            </div>
                        @endif
                        <div class="qr-closed-grid">
                            @if (! empty($serviceAvailability['schedule_label']))
                                <article class="qr-closed-card">
                                    <small>{{ __('Active schedule') }}</small>
                                    <strong>{{ $serviceAvailability['schedule_label'] }}</strong>
                                    <div class="qr-closed-note">{{ __('This QR menu follows the service timetable set for this day group.') }}</div>
                                </article>
                            @endif
                            <article class="qr-closed-card">
                                <small>{{ __('Service window') }}</small>
                                <strong>{{ $serviceAvailability['start_at']?->format('g:i A') }} - {{ $serviceAvailability['end_at']?->format('g:i A') }}</strong>
                                <div class="qr-closed-note">{{ __('Orders automatically reopen once the kitchen is back within service hours.') }}</div>
                            </article>
                            <article class="qr-closed-card">
                                <small>{{ __('Helpful note') }}</small>
                                <strong>{{ __('Please scan again later') }}</strong>
                                <div class="qr-closed-note">{{ __('Come back during the next available service window to view dishes and place your order.') }}</div>
                            </article>
                        </div>
                    </section>
                @else
                    <form method="POST" action="{{ route('site.kitchen-menu.store', $qr->token) }}" style="display:grid;gap:1.5rem;">
                        @csrf
                        <div style="padding:1.5rem;border:1px solid rgba(18,34,35,.12);background:#fff;">
                            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
                                <div><label>{{ __('Guest name') }}</label><input type="text" name="guest_name" value="{{ old('guest_name') }}" required></div>
                                <div><label>{{ __('Phone (optional)') }}</label><input type="text" name="guest_phone" value="{{ old('guest_phone') }}"></div>
                            </div>
                            <div class="mt-15"><label>{{ __('Special notes') }}</label><textarea name="notes" rows="3">{{ old('notes') }}</textarea></div>
                        </div>

                        <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1rem;">
                            @foreach ($menu as $item)
                                <article class="qr-menu-card">
                                    @if ($item->image_path)
                                        <img src="{{ \App\Support\PublicDisk::url($item->image_path) }}" alt="{{ $item->name }}" loading="lazy" style="width:100%;height:180px;object-fit:cover;">
                                    @endif
                                    <h3 class="text-18 mt-15">{{ $item->name }}</h3>
                                    <p class="text-14 mt-10" style="opacity:.78;">{{ $item->description }}</p>
                                    <div class="mt-10" style="display:flex;justify-content:space-between;gap:1rem;">
                                        <strong>{{ number_format((float) $item->price, 0) }} TZS</strong>
                                        <span>{{ $item->preparation_minutes }} {{ __('mins') }}</span>
                                    </div>
                                    <div class="qr-menu-card__footer">
                                        <div class="qr-menu-card__quantity">
                                            <label for="card-qty-{{ $item->id }}">{{ __('Quantity') }}</label>
                                            <input id="card-qty-{{ $item->id }}" type="number" name="card_quantities[{{ $item->id }}]" min="1" max="20" value="{{ old('card_quantities.'.$item->id, 1) }}">
                                        </div>
                                        <button
                                            type="submit"
                                            name="menu_item_id"
                                            value="{{ $item->id }}"
                                            class="button -md -accent-1 bg-accent-1 text-white"
                                            style="border:none;padding:.9rem 1.35rem;align-self:end;"
                                        >
                                            {{ __('Order now') }}
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </section>
@endsection
