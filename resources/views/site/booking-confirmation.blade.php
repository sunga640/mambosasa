@extends('layouts.site')

@php use App\Enums\BookingStatus; @endphp

@section('title', __('Booking Confirmation'))

@section('content')
<style>
    :root {
        --primary-color: #2563eb;
        --success-bg: #f0fdf4;
        --success-text: #166534;
        --pending-bg: #fffbeb;
        --pending-text: #92400e;
        --error-bg: #fef2f2;
        --error-text: #991b1b;
    }

    .booking-main-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
        border: 1px solid #f1f5f9;
        padding: 40px;
    }

    .status-badge {
        padding: 6px 16px;
        border-radius: 50px;
        font-size: 14px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-top: 30px;
    }

    .info-item {
        background: #f8fafc;
        padding: 15px 20px;
        border-radius: 12px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }

    .info-item:hover {
        background: #f1f5f9;
        transform: translateY(-2px);
    }

    .info-label {
        font-size: 13px;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 4px;
        display: block;
    }

    .info-value {
        font-size: 16px;
        font-weight: 600;
        color: #0f172a;
    }

    .countdown-timer {
        font-family: 'Monaco', 'Consolas', monospace;
        font-size: 36px;
        color: var(--pending-text);
        letter-spacing: 2px;
    }

    .action-button {
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    @media (max-width: 768px) {
        .info-grid { grid-template-columns: 1fr; }
        .booking-main-card { padding: 25px; }
    }

</style>

<section class="layout-pt-md layout-pb-lg bg-light-2">
    <div class="container">
        <div class="row justify-center">
            <div class="col-xl-8 col-lg-10">

                <div class="booking-main-card">
                    @if ($errors->has('payment'))
                        <div class="mb-25 p-20 rounded-12" style="background: var(--error-bg); border: 1px solid #fecaca; color: var(--error-text);">
                            <strong>{{ __('Payment') }}:</strong> {{ $errors->first('payment') }}
                        </div>
                    @endif
                    @if (session('status'))
                        <div class="mb-25 p-20 rounded-12" style="background: #eff6ff; border: 1px solid #bfdbfe; color: #1e40af;">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{-- Header Section --}}
                    <div class="text-center mb-40">
                        <div class="mb-20">
                            @if($booking->status === BookingStatus::Confirmed)
                                <div class="icon-circle bg-success-light text-success-2 mx-auto" style="width:80px; height:80px; font-size:40px; background:#dcfce7; color:#22c55e; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    <i class="fa fa-check"></i>
                                </div>
                            @else
                                <div class="icon-circle bg-blue-light text-blue-1 mx-auto" style="width:80px; height:80px; font-size:40px; background:#eff6ff; color:#3b82f6; border-radius:50%; display:flex; align-items:center; justify-content:center;">
                                    <i class="fa fa-hotel"></i>
                                </div>
                            @endif
                        </div>
                        <h1 class="text-30 fw-700">{{ __('Booking Confirmation') }}</h1>
                        <p class="text-16 text-light-1 mt-10">
                            {{ __('Booking Reference:') }} <span class="fw-700 text-dark-1">#{{ $booking->public_reference }}</span>
                        </p>
                    </div>

                    {{-- 1. PENDING PAYMENT STATE --}}
                    @if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
                        @php
                            $isPesapal = $booking->method && (
                                $booking->method->slug === 'pesapal'
                                || str_contains(strtolower($booking->method->name), 'pesapal')
                            );
                        @endphp
                        <div style="background: var(--pending-bg); border: 1px solid #fde68a; border-radius: 16px; padding: 30px; text-align: center; margin-bottom: 30px;">
                            <h3 class="text-18 fw-600 mb-10" style="color: var(--pending-text);">{{ __('Awaiting Payment') }}</h3>
                            <div id="pay-countdown" class="countdown-timer mb-15" data-deadline="{{ $booking->payment_deadline_at->toIso8601String() }}">00:00</div>

                            @if ($isPesapal)
                                <p class="text-14 mb-20" style="color: var(--pending-text); opacity: 0.9; max-width: 520px; margin-left: auto; margin-right: auto;">
                                    {{ __('Pay securely with PesaPal. Use the button below to open the payment page; you can pay by mobile money or card.') }}
                                </p>
                                <a href="{{ route('pay.now', $booking->public_reference) }}"
                                   class="action-button text-white"
                                   style="background: #2563eb; border: none; text-decoration: none; justify-content: center; max-width: 100%;">
                                    <i class="fa fa-external-link-alt"></i>
                                    {{ __('Open payment page (PesaPal)') }}
                                </a>
                                <p class="text-12 mt-15" style="color: #92400e; opacity: 0.85;">
                                    <i class="fa fa-lock mr-5"></i>{{ __('You will leave this site to complete payment, then you can return here to see your status.') }}
                                </p>
                            @else
                                <p class="text-14 fw-600 mb-10" style="color: var(--pending-text);">{{ __('Payment method: :name', ['name' => $booking->method->name]) }}</p>
                                <div class="text-left mx-auto" style="max-width: 480px; background: #fff; border-radius: 12px; padding: 18px 20px; border: 1px solid #fde68a;">
                                    @if ($booking->method->account_number)
                                        <p class="text-14 mb-8"><span class="text-light-1">{{ __('Account number') }}:</span> <strong class="text-dark-1">{{ $booking->method->account_number }}</strong></p>
                                    @endif
                                    @if ($booking->method->account_holder)
                                        <p class="text-14 mb-8"><span class="text-light-1">{{ __('Account name') }}:</span> <strong class="text-dark-1">{{ $booking->method->account_holder }}</strong></p>
                                    @endif
                                    @if ($booking->method->instructions)
                                        <p class="text-13 mt-10 mb-0" style="color: #78350f; line-height: 1.55;">{{ $booking->method->instructions }}</p>
                                    @endif
                                    @if (! $booking->method->account_number && ! $booking->method->account_holder && ! $booking->method->instructions)
                                        <p class="text-14 mb-0" style="color: var(--pending-text);">{{ __('Please complete payment using the details sent to your email and use booking reference :ref.', ['ref' => $booking->public_reference]) }}</p>
                                    @endif
                                </div>
                            @endif

                            <div class="mt-20 d-flex items-center justify-center text-13" style="color: #b45309;">
                                <i class="fa fa-spinner fa-spin mr-10"></i> {{ __('Monitoring payment status...') }}
                            </div>
                        </div>

                    {{-- 2. CONFIRMED STATE --}}
                    @elseif ($booking->status === BookingStatus::Confirmed)
                        <div style="background: var(--success-bg); border: 1px solid #bbf7d0; border-radius: 16px; padding: 30px; margin-bottom: 30px;">
                            <div class="d-flex items-start gap-15">
                                <i class="fa fa-envelope-open-text text-24 mt-5" style="color: var(--success-text);"></i>
                                <div>
                                    <h3 class="text-20 fw-700" style="color: var(--success-text);">{{ __('Login Details Sent!') }}</h3>
                                    <p class="text-15 mt-5" style="color: var(--success-text); line-height: 1.6;">
                                        {{ __('Your payment is confirmed. Check your email') }} (<strong>{{ $booking->email }}</strong>) {{ __('for your username and password to access the Member Dashboard.') }}
                                    </p>
                                    <a href="{{ route('login') }}" class="button -md bg-accent-1 text-white mt-15 action-button">
                                        <i class="fa fa-sign-in-alt"></i> {{ __('Login to Dashboard') }}
                                    </a>
                                </div>
                            </div>
                        </div>

                    {{-- 3. EXPIRED STATE --}}
                    @elseif ($booking->status === BookingStatus::Expired)
                        <div style="background: var(--error-bg); border: 1px solid #fee2e2; border-radius: 16px; padding: 30px; text-align: center; margin-bottom: 30px;">
                            <i class="fa fa-exclamation-circle text-30 mb-10" style="color: var(--error-text);"></i>
                            <h3 class="text-18 fw-600" style="color: var(--error-text);">{{ __('Booking Expired') }}</h3>
                            <p class="text-14">{{ __('The payment time limit has been exceeded.') }}</p>
                        </div>
                    @endif

                    {{-- Invoice Section --}}
                    @if ($booking->invoice)
                        <div class="d-flex items-center justify-between p-20 bg-light-1 rounded-12 mb-30" style="border: 1px dashed #cbd5e1;">
                            <div>
                                <p class="text-14 text-light-1">{{ __('Invoice Number') }}</p>
                                <p class="text-16 fw-600">{{ $booking->invoice->number }}</p>
                            </div>
                            <a href="{{ $booking->invoice->publicUrl() }}" target="_blank" class="button -sm bg-white text-dark-1 border-light" style="padding: 8px 20px; border: 1px solid #ddd;">
                                <i class="fa fa-print mr-10"></i> {{ __('View Invoice') }}
                            </a>
                        </div>
                    @endif

                    {{-- Reservation Summary --}}
                    <div class="mt-40">
                        <h3 class="text-20 fw-700 mb-20">{{ __('Reservation Summary') }}</h3>
                        <div class="info-grid">

    <div class="info-item">
        <span class="info-label">{{ __('Accommodation') }}</span>
        <span class="info-value">
            <i class="fa fa-bed mr-5 text-blue-1"></i>
            {{ $booking->room->name }}
        </span>
    </div>

    <div class="info-item">
        <span class="info-label">{{ __('Room Details') }}</span>
        <span class="info-value">
            #{{ $booking->room->room_number ?: 'N/A' }} ·
            Floor {{ $booking->room->floor_number }}
        </span>
    </div>

    <div class="info-item">
        <span class="info-label">{{ __('Check-In') }}</span>
        <span class="info-value">
            <i class="fa fa-calendar-alt mr-5 text-blue-1"></i>
            {{ $booking->check_in?->format('D, d M Y') }}
        </span>
    </div>

    <div class="info-item">
        <span class="info-label">{{ __('Check-Out') }}</span>
        <span class="info-value">
            <i class="fa fa-calendar-check mr-5 text-blue-1"></i>
            {{ $booking->check_out?->format('D, d M Y') }}
        </span>
    </div>

                <div class="info-item">
                    <span class="info-label">{{ __('Total Amount') }}</span>
                    <span class="info-value text-20 text-blue-1">
                        TZS {{ number_format((float) $booking->total_amount, 0) }}
                    </span>
                </div>

            </div>
                    </div>

                    {{-- Footer Actions --}}
                    <div class="mt-40 pt-30 border-top-light d-flex justify-between items-center flex-wrap gap-20">
                        <a href="{{ route('site.home') }}" class="text-15 fw-600 text-blue-1">
                            <i class="fa fa-arrow-left mr-10"></i> {{ __('Return to Homepage') }}
                        </a>
                        <div class="text-14 text-light-1">
                            {{ __('Need help?') }} <a href="/contact" class="text-dark-1 fw-600 underline">{{ __('Contact Support') }}</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- SCRIPTS --}}
@if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
<script>
(function () {
    var el = document.getElementById('pay-countdown');
    if (!el) return;
    var deadline = new Date(el.getAttribute('data-deadline')).getTime();

    function tick() {
        var now = new Date().getTime();
        var t = deadline - now;
        if (t <= 0) {
            el.textContent = "00:00";
            window.location.reload();
            return;
        }
        var m = Math.floor((t % (1000 * 60 * 60)) / (1000 * 60));
        var s = Math.floor((t % (1000 * 60)) / 1000);
        el.textContent = String(m).padStart(2, '0') + ":" + String(s).padStart(2, '0');
        setTimeout(tick, 1000);
    }
    tick();

    // Auto refresh status every 20 seconds
    setTimeout(function() {
        window.location.reload();
    }, 20000);
})();
</script>
@endif

@endsection
