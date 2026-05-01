@extends('layouts.site')

@section('title', __('My booking'))

@php use App\Enums\BookingStatus; @endphp

@section('content')
<section class="layout-pt-lg layout-pb-lg" style="padding-top:2rem;">
    <div class="container" style="max-width:720px;">
        @if (session('status'))
            <p class="text-15 mb-25" style="color:#15803d;">{{ session('status') }}</p>
        @endif

        <h1 class="text-36 md:text-28 mb-10">{{ __('Your reservation') }}</h1>
        <p class="text-15 mb-30" style="opacity:.85;">{{ __('Reference :ref — save this page link to return anytime.', ['ref' => $booking->public_reference]) }}</p>

        <div class="rounded-16 p-25 mb-30" style="border:1px solid rgba(18,34,35,.12);background:#fafafa;">
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Status') }}:</span> {{ $booking->status->label() }}</p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Guest') }}:</span> {{ $booking->first_name }} {{ $booking->last_name }}</p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Email') }}:</span> {{ $booking->email }}</p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Phone') }}:</span> {{ $booking->phone }}</p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Room') }}:</span> {{ $booking->room->name }}</p>
            @if ($booking->room->rank)
                <p class="text-15 mb-8"><span class="fw-600">{{ __('Category') }}:</span> {{ $booking->room->rank->name }}</p>
            @endif
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Branch') }}:</span> {{ $booking->room->branch?->name ?? '—' }}</p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Stay') }}:</span>
                @if ($booking->check_in && $booking->check_out)
                    {{ $booking->check_in->format('M j, Y') }} → {{ $booking->check_out->format('M j, Y') }}
                    ({{ $booking->nights }} {{ __('nights') }})
                @else
                    —
                @endif
            </p>
            <p class="text-15 mb-8"><span class="fw-600">{{ __('Total') }}:</span> {{ number_format((float) $booking->total_amount, 0) }} {{ __('TZS') }}</p>
            <p class="text-15"><span class="fw-600">{{ __('Payment method') }}:</span> {{ $booking->method->name }}</p>
        </div>

        @if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
            <div class="rounded-16 p-25 mb-30" style="border:1px solid #f59e0b;background:#fffbeb;">
                <p class="fw-600 mb-8">{{ __('Time remaining to pay') }}</p>
                <p class="text-28" id="guest-pay-countdown" data-deadline="{{ $booking->payment_deadline_at->toIso8601String() }}">—</p>
                <p class="text-13 mt-10" style="opacity:.85;">{{ __('After payment is confirmed, restaurant and room-service options will unlock below for your stay dates.') }}</p>
            </div>
        @endif

        @if ($booking->invoice)
            <div class="rounded-16 p-25 mb-30" style="border:1px solid #e2e8f0;">
                <p class="fw-600 mb-10">{{ __('Invoice') }} {{ $booking->invoice->number }}</p>
                <a href="{{ $booking->invoice->publicUrl() }}" class="button -md -dark-1 bg-dark-1 text-white" style="text-decoration:none;padding:.5rem 1rem;border-radius:8px;display:inline-block;" target="_blank" rel="noopener">{{ __('Open invoice & pay') }}</a>
                <a href="{{ route('site.invoice.export', ['token' => $booking->invoice->token]) }}" class="text-15 ml-20">{{ __('CSV') }}</a>
            </div>
        @endif

        @if ($booking->status === BookingStatus::Confirmed && $canOrderService)
            @if ($dashboardSettings->restaurantIntegrationConfigured())
                <div class="p-25 mb-30" style="border:1px solid #d1fae5;background:#ecfdf5;">
                    <p class="fw-600 mb-8" style="color:#0f766e;">{{ __('Restaurant ordering') }}</p>
                    <p class="text-14 mb-15" style="opacity:.85;">{{ __('Open our connected restaurant system and continue with your active stay details already signed by the hotel system.') }}</p>
                    <a href="{{ route('site.guest-stay.restaurant', ['token' => $token]) }}" class="button -md -dark-1 bg-dark-1 text-white" style="text-decoration:none;padding:.5rem 1rem;border-radius:8px;display:inline-block;">{{ __('Open restaurant ordering') }}</a>
                </div>
            @endif

            <h2 class="text-24 mb-15">{{ __('Restaurant & room service') }}</h2>
            <p class="text-14 mb-20" style="opacity:.85;">{{ __('Order from our in-room menu during your stay. Charges may be added to your folio as arranged with reception.') }}</p>

            @if ($menu->isEmpty())
                <p class="text-14">{{ __('Menu is being updated — please contact reception to order.') }}</p>
            @else
                <form method="POST" action="{{ route('site.guest-stay.room-service', ['token' => $token]) }}" class="mt-15">
                    @csrf

                    <table class="w-full text-14" style="border-collapse:collapse;width:100%;">
                        <thead>
                            <tr style="border-bottom:1px solid #e5e5e5;">
                                <th style="text-align:left;padding:.5rem 0;">{{ __('Item') }}</th>
                                <th>{{ __('TZS') }}</th>
                                <th>{{ __('Qty') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($menu as $i => $item)
                                <tr style="border-bottom:1px solid #f0f0f0;">
                                    <td style="padding:.5rem 0;">
                                        <strong>{{ $item->name }}</strong>
                                        @if ($item->description)
                                            <div class="text-12" style="opacity:.75;">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ number_format((float) $item->price, 0) }}</td>
                                    <td class="text-center" style="max-width:80px;">
                                        <input type="hidden" name="items[{{ $i }}][menu_item_id]" value="{{ $item->id }}">
                                        <input type="number" name="items[{{ $i }}][quantity]" value="{{ old('items.'.$i.'.quantity', 0) }}" min="0" max="20" style="width:100%;padding:.25rem;">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <div class="mt-20">
                        <label for="notes" class="text-14 fw-500">{{ __('Notes') }}</label>
                        <textarea name="notes" id="notes" rows="2" class="w-full mt-5" style="width:100%;padding:.5rem;border:1px solid #ccc;border-radius:8px;">{{ old('notes') }}</textarea>
                    </div>

                    @error('items')<p class="text-13 text-accent-1 mt-10">{{ $message }}</p>@enderror

                    <button type="submit" class="button -md -dark-1 bg-dark-1 text-white mt-20" style="border:none;padding:.55rem 1.2rem;border-radius:8px;cursor:pointer;">{{ __('Place order') }}</button>
                </form>
            @endif
        @elseif ($booking->status === BookingStatus::Confirmed && ! $canOrderService)
            <div class="rounded-16 p-20 mb-25" style="border:1px dashed #ccc;background:#fafafa;">
                <p class="text-15 mb-5">{{ __('Room service') }}</p>
                <p class="text-14" style="opacity:.8;">{{ __('Restaurant and add-on requests unlock on your check-in date and stay available until check-out.') }}</p>
            </div>
        @endif

        @if ($recentOrders->isNotEmpty())
            <h2 class="text-22 mt-40 mb-15">{{ __('Your orders') }}</h2>
            <table class="w-full text-14" style="border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #ddd;">
                        <th style="text-align:left;padding:.4rem 0;">{{ __('When') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Payment') }}</th>
                        <th>{{ __('Total (TZS)') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($recentOrders as $o)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:.4rem 0;">{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                            <td>{{ $o->statusEnum()->label() }}</td>
                            <td>
                                <div>{{ $o->paymentStatusLabel() }}</div>
                                <div class="text-12" style="opacity:.7;">{{ $o->bookingMethod?->name ?: __('Select payment method') }}</div>
                                @if (\App\Models\RoomServiceOrder::supportsPaymentTracking() && ! $o->isPaid() && $paymentMethods->isNotEmpty())
                                    <form method="POST" action="{{ route('site.room-service-orders.payment', $o->public_reference) }}" style="display:grid;gap:.5rem;margin-top:.5rem;">
                                        @csrf
                                        <select name="booking_method_id" style="padding:.4rem .55rem;border:1px solid #d1d5db;border-radius:8px;">
                                            <option value="">{{ __('Choose online payment method') }}</option>
                                            @foreach ($paymentMethods as $method)
                                                <option value="{{ $method->id }}" @selected($o->booking_method_id === $method->id)>{{ $method->name }}</option>
                                            @endforeach
                                        </select>
                                        <div style="display:flex;gap:.85rem;flex-wrap:wrap;font-size:.85rem;justify-content:flex-start;align-items:flex-start;">
                                            <label style="display:flex;align-items:center;gap:.35rem;"><input type="radio" name="payment_choice" value="online" checked> {{ __('Use selected method') }}</label>
                                            <label style="display:flex;align-items:center;gap:.35rem;"><input type="radio" name="payment_choice" value="cash"> {{ __('Pay cash') }}</label>
                                            <label style="display:flex;align-items:center;gap:.35rem;"><input type="radio" name="payment_choice" value="bill_later"> {{ __('Keep bill for checkout') }}</label>
                                        </div>
                                        <button type="submit" class="button -sm -dark-1 bg-dark-1 text-white" style="border:none;padding:.45rem .85rem;border-radius:8px;cursor:pointer;width:max-content;">
                                            {{ __('Save payment choice') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                            <td>{{ number_format((float) $o->total_amount, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <p class="text-14 mt-40" style="opacity:.75;">
            {{ __('Questions?') }} <a href="{{ route('site.page', ['slug' => 'contact']) }}">{{ __('Contact the hotel') }}</a>
        </p>
    </div>
</section>

@if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
<script>
(function () {
    var el = document.getElementById('guest-pay-countdown');
    if (!el) return;
    var deadline = new Date(el.getAttribute('data-deadline')).getTime();
    function tick() {
        var s = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        if (s <= 0) { el.textContent = @json(__('Expired')); return; }
        var d = Math.floor(s / 86400);
        var h = Math.floor((s % 86400) / 3600);
        var m = Math.floor((s % 3600) / 60);
        var sec = s % 60;
        el.textContent = (d ? d + 'd ' : '') + String(h).padStart(2,'0') + ':' + String(m).padStart(2,'0') + ':' + String(sec).padStart(2,'0');
    }
    tick();
    setInterval(tick, 1000);
})();
</script>
@endif
@endsection
