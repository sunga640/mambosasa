@extends('layouts.site')

@section('title', __('Book your stay'))

@push('head')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/css/intlTelInput.css"/>
<script src="https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/intlTelInput.min.js"></script>
@endpush

@section('content')
@php
    $type = $selectedType;
    $mainImage = $type?->heroImageUrl() ?? asset('img/roomsSingle/3/1.png');
    $typeThumbs = $type ? $type->thumbnailUrls() : [];
    $price = $type ? (float) $type->price : 0;
    $defaultCheckIn = old('check_in', \Carbon\Carbon::today()->format('Y-m-d'));
    $defaultCheckOut = old('check_out', \Carbon\Carbon::tomorrow()->format('Y-m-d'));
    $rooms = $type?->rooms ?? collect();
    $firstRoom = $rooms->first();
    $firstRoomId = (int) old('room_id', $firstRoom?->id ?? 0);
    $fallbackRoomDescription = $type?->description ?: __('Room amenities, details, and policies');
@endphp

<section data-anim-wrap class="pageHero -type-1 -items-center">
    <div class="pageHero__bg">
        @include('site.partials.page-hero-image', ['fallback' => 'img/pageHero/7.png', 'heroUrl' => $heroUrl ?? null])
    </div>
    <div class="container">
        <div class="row justify-center">
            <div class="col-xl-8 col-lg-10">
                <div class="pageHero__content text-center">
                    <span class="site-kicker justify-center">{{ __('Direct Booking Experience') }}</span>
                    <h1 class="pageHero__title text-white mt-20">{{ __('Book Your Stay') }}</h1>
                    <p class="pageHero__text text-white mt-15">{{ __('Browse available rooms, open the stay calendar in a modal, and complete your reservation without leaving the page.') }}</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="site-booking-modern layout-pt-md layout-pb-lg">
    <div class="container">
        <div class="site-booking-modern__head">
            <div>
                <span class="site-kicker">{{ __('Available Rooms') }}</span>
                <h2>{{ __('Choose your room and check availability') }}</h2>
            </div>
            <p>{{ __('Browse the room list below, then open the availability modal for the exact room you want to reserve.') }}</p>
        </div>

        @if ($errors->any())
            <div class="site-booking-modern__errors">
                <strong>{{ __('Please fix these booking details:') }}</strong>
                <ul>
                    @foreach ($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="site-booking-modern__room-grid">
            @forelse($rooms as $roomOption)
                @php
                    $roomStatus = (string) ($roomOption->status->value ?? $roomOption->status);
                    $roomPrice = (int) ($roomPrices[$roomOption->id] ?? round($price));
                @endphp
                <article class="site-booking-room-card">
                    <div class="site-booking-room-card__media">
                        <img src="{{ $mainImage }}" alt="{{ $roomOption->name }}">
                        <div class="site-booking-room-card__overlay"></div>
                    </div>
                    <div class="site-booking-room-card__body">
                        <div class="site-booking-room-card__top">
                            <div>
                                <h3>{{ $roomOption->name }}</h3>
                                <span class="site-booking-room-card__tag">{{ __('Room :number', ['number' => $roomOption->room_number]) }}</span>
                            </div>
                            <div class="site-booking-room-card__ratebox">
                                <button
                                    type="button"
                                    class="site-booking-room-card__button"
                                    data-booking-open
                                    data-room-id="{{ $roomOption->id }}"
                                    data-room-name="{{ $roomOption->name }}"
                                    data-room-number="{{ $roomOption->room_number }}"
                                    data-room-price="{{ $roomPrice }}"
                                    data-room-image="{{ $mainImage }}"
                                >
                                    {{ __('Check availability') }}
                                </button>
                                {{-- <span class="site-booking-room-card__lowrate">{{ __("Today's low rate") }}</span> --}}
                            </div>
                        </div>
                        <div class="site-booking-room-card__benefits">
                            <span>{{ __('With your stay:') }}</span>
                            <strong>{{ __('Free Internet') }}</strong>
                        </div>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($roomOption->description ?: $fallbackRoomDescription), 120) }}</p>
                        <div class="site-booking-room-card__footer">
                            <span class="site-booking-room-card__status {{ $roomStatus === 'available' ? 'is-available' : 'is-busy' }}">
                                {{ $roomStatus === 'available' ? __('Available to check') : __('Status: :status', ['status' => ucfirst($roomStatus)]) }}
                            </span>
                            <strong class="site-booking-room-card__price">TZS {{ number_format($roomPrice, 0) }}</strong>
                        </div>
                    </div>
                </article>
            @empty
                <div class="site-booking-modern__empty site-floating-card">
                    {{ __('No rooms are currently available under this room type.') }}
                </div>
            @endforelse
        </div>
    </div>
</section>

<div id="bookingRoomModal" class="site-booking-modal" aria-hidden="true">
    <div class="site-booking-modal__backdrop" data-booking-close></div>
    <div class="site-booking-modal__panel">
        <button type="button" class="site-booking-modal__close" data-booking-close aria-label="{{ __('Close booking modal') }}">&times;</button>

        <form id="booking-form" method="POST" action="{{ route('site.booking.store') }}" class="site-booking-modal__grid">
            @csrf
            <input type="hidden" name="room_type_id" value="{{ $type?->id }}">
            <input type="hidden" name="room_id" id="selected_room_id" value="{{ $firstRoomId }}">
            <input type="hidden" name="check_in" id="check_in" value="{{ $defaultCheckIn }}">
            <input type="hidden" name="check_out" id="check_out" value="{{ $defaultCheckOut }}">

            <div class="site-booking-modal__calendar">
                <div class="site-booking-modal__hero">
                    <img id="booking-modal-image" src="{{ $mainImage }}" alt="{{ $type?->name }}">
                    <div class="site-booking-modal__hero-overlay"></div>
                    <div class="site-booking-modal__hero-copy">
                        <span class="site-booking-modal__hero-tag">{{ __('Selected room') }}</span>
                        <h3 id="booking-modal-room-name">{{ $firstRoom?->name ?? $type?->name }}</h3>
                        <p id="booking-modal-room-meta">{{ __('Room :number', ['number' => $firstRoom?->room_number ?? '--']) }}</p>
                    </div>
                </div>
                <div class="site-booking-modal__thumbs" id="booking-modal-thumbs"></div>

                <div class="site-booking-modal__calendar-card">
                    <div class="site-booking-modal__calendar-head">
                        <div>
                            <strong>{{ __('Choose your stay dates') }}</strong>
                            <p>{{ __('Booked dates are blocked directly from live room bookings and maintenance entries.') }}</p>
                        </div>
                        <span id="booking-price-night">TZS {{ number_format((int) round($price), 0) }}</span>
                    </div>

                    <input type="text" id="booking_date_range" class="site-booking-modal__date-input" placeholder="{{ __('Select stay dates') }}" readonly>

                    <div class="site-booking-modal__summary">
                        <div>
                            <span>{{ __('Total amount') }}</span>
                            <strong id="booking-total">-</strong>
                        </div>
                        <p>{{ __('The nightly price updates automatically when you switch to another room.') }}</p>
                    </div>
                </div>
            </div>

            <div class="site-booking-modal__form">
                <h3>{{ __('Complete your booking') }}</h3>
                <p>{{ __('Keep the same booking functionality, but finish the reservation inside this room-specific modal.') }}</p>

                <div class="site-booking-modal__field">
                    <label>{{ __('First name') }}</label>
                    <input type="text" name="first_name" required value="{{ old('first_name') }}">
                </div>
                <div class="site-booking-modal__field">
                    <label>{{ __('Last name') }}</label>
                    <input type="text" name="last_name" required value="{{ old('last_name') }}">
                </div>
                <div class="site-booking-modal__field">
                    <label>{{ __('Email address') }}</label>
                    <input type="email" name="email" required value="{{ old('email', auth()->user()?->email) }}">
                </div>
                <div class="site-booking-modal__field">
                    <label>{{ __('Phone number') }}</label>
                    <input type="tel" id="phone" name="phone" required value="{{ old('phone') }}">
                </div>

                <div class="site-booking-modal__payments">
                    <label class="site-booking-modal__payments-label">{{ __('Payment method') }}</label>
                    @foreach ($methods as $m)
                        <label class="site-booking-modal__payment-item">
                            <input type="radio" name="booking_method_id" value="{{ $m->id }}" @checked((int) old('booking_method_id', $methods->first()?->id) === $m->id)>
                            <span>
                                <strong>{{ $m->name }}</strong>
                                @if ($m->instructions)
                                    <small>{{ $m->instructions }}</small>
                                @endif
                            </span>
                        </label>
                    @endforeach
                </div>

                <label class="site-booking-modal__terms">
                    <input type="checkbox" name="terms" required class="mr-10" style="width:17px; height:17px;">
                    <span>{{ __('I agree to the terms and conditions') }}</span>
                </label>

                <button type="submit" class="site-pill-btn site-booking-modal__submit">{{ __('Complete booking') }}</button>
            </div>
        </form>
    </div>
</div>

<style>
    .site-booking-modern {
        background: linear-gradient(180deg, #fff 0%, #f7f2e8 100%);
    }
    .site-booking-modern__head {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(280px, 0.72fr);
        gap: 1.4rem;
        align-items: end;
        margin-bottom: 1.8rem;
    }
    .site-booking-modern__head h2 {
        margin: 0.8rem 0 0;
        font-size: clamp(2.2rem, 4vw, 3.3rem);
        line-height: 0.94;
        color: #17352f;
    }
    .site-booking-modern__head p {
        margin: 0;
        color: #5c6b6d;
        line-height: 1.8;
        font-size: 0.95rem;
    }
    .site-booking-modern__errors {
        margin-bottom: 1.2rem;
        padding: 1rem 1.1rem;
        border: 1px solid #fecaca;
        border-radius: 0;
        background: #fff1f2;
        color: #9f1239;
    }
    .site-booking-modern__errors ul {
        margin: 0.4rem 0 0;
        padding-left: 1.2rem;
    }
    .site-booking-modern__summary-tag,
    .site-booking-room-card__tag,
    .site-booking-modal__hero-tag {
        display: inline-flex;
        width: fit-content;
        padding: 0.42rem 0.72rem;
        background: #f8f3eb;
        color: #8a6a39;
        font-size: 0.66rem;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
    }
    .site-booking-modern__room-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.1rem;
        margin-top: 1.7rem;
    }
    .site-booking-room-card {
        display: grid;
        grid-template-columns: minmax(150px, 190px) minmax(0, 1fr);
        align-items: stretch;
        border: 1px solid rgba(23, 53, 47, 0.12);
        background: #fff;
        overflow: hidden;
        border-radius: 0;
        box-shadow: 0 12px 34px rgba(17, 35, 38, 0.06);
        min-height: 14.75rem;
    }
    .site-booking-room-card__media {
        position: relative;
        min-height: 100%;
        overflow: hidden;
    }
    .site-booking-room-card__media img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
        transition: transform 0.38s ease;
    }
    .site-booking-room-card__overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(17, 35, 38, 0.06) 0%, rgba(17, 35, 38, 0.28) 48%, rgba(17, 35, 38, 0.82) 100%);
        opacity: 0.7;
        transition: opacity 0.3s ease;
    }
    .site-booking-room-card:hover .site-booking-room-card__media img {
        transform: scale(1.06);
    }
    .site-booking-room-card:hover .site-booking-room-card__overlay {
        opacity: 1;
    }
    .site-booking-room-card__body {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(145px, 180px);
        gap: 0.9rem;
        padding: 0.9rem 1rem;
        align-items: start;
    }
    .site-booking-room-card__top {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.9rem;
    }
    .site-booking-room-card__ratebox,
    .site-booking-room-card__footer {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        justify-content: center;
        gap: 0.8rem;
    }
    .site-booking-room-card__top > div:first-child {
        display: flex;
        flex-direction: column;
        gap: 0.8rem;
    }
    .site-booking-room-card h3 {
        margin: 0;
        font-size: clamp(1.25rem, 1.8vw, 1.75rem);
        line-height: 0.96;
        color: #17352f;
    }
    .site-booking-room-card p {
        margin: 0;
        color: #5c6b6d;
        line-height: 1.55;
        font-size: 0.9rem;
    }
    .site-booking-room-card__benefits {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        color: #5c6b6d;
    }
    .site-booking-room-card__benefits span {
        font-size: 0.9rem;
    }
    .site-booking-room-card__benefits strong {
        color: #17352f;
        font-size: 0.9rem;
        font-weight: 600;
    }
    .site-booking-room-card__status {
        font-size: 0.92rem;
        color: #0f766e;
        font-weight: 500;
    }
    .site-booking-room-card__status.is-available { color: #166534; }
    .site-booking-room-card__status.is-busy { color: #b91c1c; }
    .site-booking-room-card__button {
        border: none;
        padding: 0.9rem 1.15rem;
        min-width: 0;
        background: #163d72;
        color: #fff;
        font-size: 0.86rem;
        font-weight: 700;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        cursor: pointer;
        margin-left: auto;
    }
    .site-booking-room-card__lowrate {
        color: #4b5563;
        font-size: 0.96rem;
        text-align: right;
    }
    .site-booking-room-card__price {
        color: #17352f;
        font-size: 1.2rem;
        line-height: 1;
    }
    .site-booking-modern__empty {
        padding: 1.4rem;
    }
    .site-booking-modal {
        position: fixed;
        inset: 0;
        display: none;
        z-index: 10150;
    }
    .site-booking-modal.is-open {
        display: block;
    }
    .site-booking-modal__backdrop {
        position: absolute;
        inset: 0;
        background: rgba(7, 17, 20, 0.58);
        backdrop-filter: blur(6px);
    }
    .site-booking-modal__panel {
        position: relative;
        z-index: 1;
        width: min(96vw, 88rem);
        max-height: calc(100vh - 2rem);
        margin: 1rem auto;
        overflow-y: auto;
        overflow-x: hidden;
        overscroll-behavior: contain;
        border-radius: 0;
        background: #fff;
        box-shadow: 0 26px 60px rgba(17, 35, 38, 0.24);
    }
    .site-booking-modal__close {
        position: absolute;
        top: 1rem;
        right: 1rem;
        z-index: 3;
        width: 2.6rem;
        height: 2.6rem;
        border-radius: 999px;
        border: 1px solid rgba(23, 53, 47, 0.12);
        background: rgba(255, 255, 255, 0.92);
        cursor: pointer;
        font-size: 1.3rem;
        color: #17352f;
    }
    .site-booking-modal__grid {
        display: grid;
        grid-template-columns: minmax(320px, 1fr) minmax(340px, 1fr);
    }
    .site-booking-modal__calendar,
    .site-booking-modal__form {
        padding: 1.2rem;
    }
    .site-booking-modal__calendar {
        display: flex;
        flex-direction: column;
    }
    .site-booking-modal__hero {
        position: relative;
        height: 17.25rem;
        border-radius: 0;
        overflow: hidden;
        margin-bottom: 1rem;
        flex-shrink: 0;
    }
    .site-booking-modal__hero img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .site-booking-modal__thumbs {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(78px, 78px));
        gap: .6rem;
        margin-bottom: 1rem;
    }
    .site-booking-modal__thumb {
        border: 1px solid rgba(23, 53, 47, 0.14);
        background: #fff;
        padding: .2rem;
        cursor: pointer;
        height: 64px;
        width: 78px;
        overflow: hidden;
    }
    .site-booking-modal__thumb.is-active {
        border-color: #17352f;
        box-shadow: 0 0 0 1px rgba(23,53,47,.18);
    }
    .site-booking-modal__thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }
    .site-booking-modal__hero-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(17, 35, 38, 0.08) 0%, rgba(17, 35, 38, 0.34) 46%, rgba(17, 35, 38, 0.88) 100%);
    }
    .site-booking-modal__hero-copy {
        position: absolute;
        left: 1rem;
        right: 1rem;
        bottom: 1rem;
        color: #fff;
    }
    .site-booking-modal__hero-copy h3 {
        margin: 0.75rem 0 0.35rem;
        font-size: clamp(1.45rem, 2.6vw, 2.2rem);
        line-height: 0.98;
        color: #fff;
    }
    .site-booking-modal__hero-copy p {
        margin: 0;
        color: rgba(255, 255, 255, 0.8);
    }
    .site-booking-modal__calendar-card,
    .site-booking-modal__form {
        border-radius: 0;
        border: 1px solid rgba(23, 53, 47, 0.08);
        background: #fff;
        padding-bottom: 1.5rem;
    }
    .site-booking-modal__calendar-card {
        flex: 1;
    }
    .site-booking-modal__calendar-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }
    .site-booking-modal__calendar-head strong,
    .site-booking-modal__form h3 {
        display: block;
        color: #17352f;
        font-size: 1.2rem;
    }
    .site-booking-modal__calendar-head p,
    .site-booking-modal__form p {
        margin: 0.3rem 0 0;
        color: #5c6b6d;
        line-height: 1.7;
        font-size: 0.88rem;
    }
    .site-booking-modal__date-input,
    .site-booking-modal__field input,
    .site-booking-modal__field textarea,
    .iti input {
        width: 100%;
        border: 1px solid rgba(23, 53, 47, 0.14);
        border-radius: 0;
        min-height: 3.7rem;
        padding: 0.92rem 1rem;
        background: #fff;
        font-size: 1rem;
        color: #17352f;
    }
    .site-booking-modal__date-input {
        font-weight: 600;
    }
    .site-booking-modal__summary {
        margin-top: 1rem;
        padding: 1rem;
        border-radius: 0;
        background: linear-gradient(180deg, #f8f3eb 0%, #fff 100%);
    }
    .site-booking-modal__summary span,
    .site-booking-modal__summary p {
        margin: 0;
        color: #5c6b6d;
    }
    .site-booking-modal__summary strong {
        display: block;
        margin-top: 0.45rem;
        color: #17352f;
        font-size: 1.8rem;
        line-height: 1;
    }
    .site-booking-modal__field {
        margin-bottom: 1rem;
    }
    .site-booking-modal__field label,
    .site-booking-modal__payments-label {
        display: block;
        margin-bottom: 0.45rem;
        color: #17352f;
        font-size: 0.76rem;
        font-weight: 700;
        letter-spacing: 0.12em;
        text-transform: uppercase;
    }
    .site-booking-modal__payments {
        margin-top: 1.4rem;
    }
    .site-booking-modal__payment-item {
        display: grid;
        grid-template-columns: 1.5rem minmax(0, 1fr);
        gap: 1rem;
        align-items: start;
        justify-items: start;
        padding: 1rem 1rem;
        margin-bottom: 0.7rem;
        border-radius: 0;
        border: 1px solid rgba(23, 53, 47, 0.08);
        background: #fff;
        cursor: pointer;
        text-align: left;
    }
    .site-booking-modal__payment-item input[type="radio"] {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0.2rem;
    }
    .site-booking-modal__payment-item strong {
        display: block;
        color: #17352f;
    }
    .site-booking-modal__payment-item small {
        display: block;
        margin-top: 0.2rem;
        color: #5c6b6d;
        line-height: 1.6;
    }
    .site-booking-modal__terms {
        display: flex;
        gap: 0.65rem;
        align-items: center;
        margin-top: 1rem;
        color: #17352f;
    }
    .site-booking-modal__submit {
        width: 100%;
        border: none;
        margin-top: 1.35rem;
        cursor: pointer;
        border-radius: 0;
        background: #17352f;
        min-height: 3.5rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    .iti {
        width: 100%;
    }
    .iti input {
        height: 3.7rem;
        padding-left: 5.8rem !important;
    }
    .iti__selected-flag {
        height: 100%;
        border-radius: 0;
    }
    .flatpickr-day.booking-day-available {
        background: #dcfce7 !important;
        border-color: #dcfce7 !important;
        color: #166534 !important;
    }
    .flatpickr-day.booking-day-past {
        background: #f8fafc !important;
        border-color: #eef2f7 !important;
        color: #a0aec0 !important;
        cursor: not-allowed;
    }
    .flatpickr-day.booking-day-booked {
        background: #fee2e2 !important;
        border-color: #fecaca !important;
        color: #b91c1c !important;
        opacity: 1 !important;
        cursor: not-allowed !important;
    }
    @media (max-width: 1100px) {
        .site-booking-modern__head,
        .site-booking-modal__grid {
            grid-template-columns: 1fr;
        }
        .site-booking-modern__room-grid {
            grid-template-columns: 1fr;
        }
        .site-booking-room-card,
        .site-booking-room-card__body {
            grid-template-columns: 1fr;
        }
        .site-booking-room-card__ratebox,
        .site-booking-room-card__footer {
            justify-content: flex-start;
            align-items: flex-start;
        }
    }
    @media (max-width: 767px) {
        .site-booking-modal__hero {
            height: 14rem;
        }
        .site-booking-modal__panel {
            width: calc(100vw - 1rem);
            margin: 0.5rem;
            max-height: calc(100dvh - 1rem);
        }
        .site-booking-modal__form {
            padding: 1rem 1rem calc(5.8rem + env(safe-area-inset-bottom, 0px));
        }
        .site-booking-modal__submit {
            position: sticky;
            bottom: 0.35rem;
            z-index: 3;
            margin-top: 1rem;
            background: #17352f;
            box-shadow: 0 -10px 24px rgba(255, 255, 255, 0.96);
        }
        .site-booking-room-card__footer,
        .site-booking-room-card__top,
        .site-booking-modal__calendar-head {
            flex-direction: column;
            align-items: flex-start;
        }
        .site-booking-room-card__button {
            width: 100%;
            min-width: 0;
        }
        .site-booking-room-card__body {
            padding: 1.2rem;
        }
    }
</style>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
(function () {
    var roomPrices = @json($roomPrices ?? []);
    var defaultNightPrice = {{ (int) round($price) }};
    var selectedRoomEl = document.getElementById('selected_room_id');
    var ci = document.getElementById('check_in');
    var co = document.getElementById('check_out');
    var totalEl = document.getElementById('booking-total');
    var priceNightLabel = document.getElementById('booking-price-night');
    var modal = document.getElementById('bookingRoomModal');
    var roomNameEl = document.getElementById('booking-modal-room-name');
    var roomMetaEl = document.getElementById('booking-modal-room-meta');
    var roomImageEl = document.getElementById('booking-modal-image');
    var roomThumbsEl = document.getElementById('booking-modal-thumbs');
    var bookedRanges = [];
    var currentNightPrice = defaultNightPrice;
    var gallerySources = @json(array_values(array_filter($typeThumbs)));
    var galleryTimer = null;
    var activeGalleryIndex = 0;

    function formatDate(date) {
        var d = new Date(date);
        var month = '' + (d.getMonth() + 1);
        var day = '' + d.getDate();
        var year = d.getFullYear();
        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;
        return [year, month, day].join('-');
    }

    function nightPriceForRoom(roomId) {
        if (!roomId) return defaultNightPrice;
        return roomPrices[String(roomId)] ?? roomPrices[roomId] ?? defaultNightPrice;
    }

    function setMainImage(src) {
        if (!src) return;
        roomImageEl.src = src;
    }

    function renderThumbs(images, activeSrc) {
        if (!roomThumbsEl) return;
        roomThumbsEl.innerHTML = '';
        images.forEach(function (src, index) {
            var button = document.createElement('button');
            button.type = 'button';
            button.className = 'site-booking-modal__thumb' + (src === activeSrc ? ' is-active' : '');
            button.innerHTML = '<img src="' + src + '" alt="Room preview">';
            button.addEventListener('click', function () {
                activeGalleryIndex = index;
                setMainImage(src);
                renderThumbs(images, src);
                restartGallery(images);
            });
            roomThumbsEl.appendChild(button);
        });
    }

    function restartGallery(images) {
        if (galleryTimer) {
            clearInterval(galleryTimer);
        }
        if (!images.length) return;
        galleryTimer = setInterval(function () {
            activeGalleryIndex = (activeGalleryIndex + 1) % images.length;
            var nextSrc = images[activeGalleryIndex];
            setMainImage(nextSrc);
            renderThumbs(images, nextSrc);
        }, 3200);
    }

    function recalc() {
        if (!ci.value || !co.value) return;
        var dateIn = new Date(ci.value);
        var dateOut = new Date(co.value);
        dateIn.setHours(12, 0, 0, 0);
        dateOut.setHours(12, 0, 0, 0);
        var diffTime = dateOut.getTime() - dateIn.getTime();
        var nights = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        if (nights <= 0) nights = 1;
        var totalAmount = currentNightPrice * nights;
        totalEl.textContent = 'TZS ' + Math.round(totalAmount).toLocaleString(undefined, { maximumFractionDigits: 0 });
        priceNightLabel.textContent = 'TZS ' + Math.round(currentNightPrice).toLocaleString(undefined, { maximumFractionDigits: 0 });
    }

    var today = new Date();
    today.setHours(0, 0, 0, 0);

    var fp = flatpickr('#booking_date_range', {
        mode: 'range',
        dateFormat: 'Y-m-d',
        minDate: 'today',
        defaultDate: (ci.value && co.value) ? [ci.value, co.value] : undefined,
        disable: bookedRanges,
        onDayCreate: function(dObj, dStr, instance, dayElem) {
            var dStrLocal = formatDate(dayElem.dateObj);
            var isBlocked = bookedRanges.some(function(r) {
                return dStrLocal >= r.from && dStrLocal <= r.to;
            });
            if (isBlocked) {
                dayElem.classList.add('booking-day-booked');
            } else if (dayElem.dateObj < today) {
                dayElem.classList.add('booking-day-past');
            } else if (dayElem.dateObj >= today) {
                dayElem.classList.add('booking-day-available');
            }
        },
        onChange: function(dates) {
            if (dates.length === 2) {
                ci.value = formatDate(dates[0]);
                co.value = formatDate(dates[1]);
                recalc();
            }
        }
    });

    async function loadRoomCalendar(roomId) {
        if (!roomId) return;
        try {
            let res = await fetch(`{{ route('site.booking.room-calendar') }}?room_id=${roomId}`);
            let data = await res.json();
            bookedRanges = data.booked_ranges || [];
            fp.set('disable', bookedRanges);
            fp.redraw();
        } catch (e) {
            console.error('Calendar Error:', e);
        }
    }

    function openModal(button) {
        var roomId = button.getAttribute('data-room-id');
        var roomName = button.getAttribute('data-room-name');
        var roomNumber = button.getAttribute('data-room-number');
        var roomPrice = Number(button.getAttribute('data-room-price') || defaultNightPrice);
        var roomImage = button.getAttribute('data-room-image');

        selectedRoomEl.value = roomId;
        currentNightPrice = roomPrice;
        roomNameEl.textContent = roomName;
        roomMetaEl.textContent = 'Room ' + roomNumber;
        roomImageEl.src = roomImage;
        var images = [roomImage].concat(gallerySources).filter(function (src, idx, arr) {
            return !!src && arr.indexOf(src) === idx;
        });
        activeGalleryIndex = 0;
        renderThumbs(images, roomImage);
        restartGallery(images);
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';

        loadRoomCalendar(roomId);
        recalc();
    }

    function closeModal() {
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
        if (galleryTimer) {
            clearInterval(galleryTimer);
            galleryTimer = null;
        }
    }

    document.querySelectorAll('[data-booking-open]').forEach(function(button) {
        button.addEventListener('click', function() {
            openModal(button);
        });
    });

    document.querySelectorAll('[data-booking-close]').forEach(function(button) {
        button.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });

    @if ($errors->any())
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
    @endif

    if (selectedRoomEl.value) {
        currentNightPrice = nightPriceForRoom(selectedRoomEl.value);
        renderThumbs([roomImageEl.src].concat(gallerySources).filter(function (src, idx, arr) {
            return !!src && arr.indexOf(src) === idx;
        }), roomImageEl.src);
        loadRoomCalendar(selectedRoomEl.value);
    }
    recalc();
})();
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const input = document.querySelector('#phone');
    if (!input) return;

    const iti = window.intlTelInput(input, {
        initialCountry: 'tz',
        separateDialCode: true,
        preferredCountries: ['tz', 'ke', 'ug'],
        nationalMode: false,
        autoPlaceholder: 'polite',
        formatOnDisplay: true,
        utilsScript: 'https://cdn.jsdelivr.net/npm/intl-tel-input@18.2.1/build/js/utils.js'
    });

    input.addEventListener('input', function () {
        if (input.value.startsWith('0')) {
            input.value = input.value.substring(1);
        }
    });

    if (input.form) {
        input.form.addEventListener('submit', function () {
            if (input.value.trim() !== '') {
                input.value = iti.getNumber();
            }
        });
    }
});
</script>
@endpush
@endsection
