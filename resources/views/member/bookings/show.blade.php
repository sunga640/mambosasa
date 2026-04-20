@extends('layouts.member')

@php use App\Enums\BookingStatus; @endphp

@section('title', $booking->public_reference)

@section('header')
    <h1 class="text-30" style="margin:0;">{{ $booking->public_reference }}</h1>
@endsection

@section('content')
    @if (session('status'))
        <p class="text-15 mb-20" style="color:#0a0;">{{ session('status') }}</p>
    @endif

    <div class="text-15" style="line-height:1.7;color:#333;">
        <p><span class="fw-600">{{ __('Status') }}:</span> {{ $booking->status->label() }}</p>
        <p><span class="fw-600">{{ __('Room') }}:</span> {{ $booking->room->name }}</p>
        <p><span class="fw-600">{{ __('Room # / floor') }}:</span> {{ $booking->room->room_number ?? '—' }} · {{ __('Floor') }} {{ $booking->room->floor_number }}</p>
        <p><span class="fw-600">{{ __('Branch') }}:</span> {{ $booking->room->branch?->name ?? '—' }}</p>
        @if ($booking->room->branch)
        <p><span class="fw-600">{{ __('Branch status') }}:</span>
            @if ($booking->room->branch->is_active)
                <span style="color:#15803d;">{{ __('Active') }}</span>
            @else
                <span style="color:#b45309;">{{ __('Inactive') }}</span>
            @endif
        </p>
        @endif
        <p><span class="fw-600">{{ __('Room listing status') }}:</span> {{ $booking->room->status->label() }}</p>
        <p><span class="fw-600">{{ __('Payment method') }}:</span> {{ $booking->method->name }}</p>

        @if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
            <div class="mt-30 p-20 bg-light-1 rounded-8" id="payment-countdown">
                <p class="fw-600 mb-10">{{ __('Time left to pay') }}</p>
                <p class="text-24" id="member-pay-countdown" data-deadline="{{ $booking->payment_deadline_at->toIso8601String() }}">—</p>
            </div>
        @endif

        <p class="mt-30"><span class="fw-600">{{ __('Stay dates') }}:</span>
            @if ($booking->check_in && $booking->check_out)
                {{ $booking->check_in->format('Y-m-d') }} → {{ $booking->check_out->format('Y-m-d') }} ({{ $booking->nights }} {{ __('nights') }})
            @else
                {{ __('Not set') }}
            @endif
        </p>
        <p><span class="fw-600">{{ __('Total') }}:</span> {{ number_format((float) $booking->total_amount, 0) }}</p>

        @if ($booking->invoice)
            <div class="mt-30 p-20 rounded-8" style="border:1px solid #e2e8f0;background:#f8fafc;">
                <p class="fw-600 mb-10">{{ __('Invoice') }} {{ $booking->invoice->number }}</p>
                <div style="display:flex;flex-wrap:wrap;gap:.65rem;align-items:center;">
                    <a href="{{ $booking->invoice->publicUrl() }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;padding:.45rem 1rem;border-radius:8px;" target="_blank" rel="noopener">{{ __('View / print') }}</a>
                    <a href="{{ route('site.invoice.export', ['token' => $booking->invoice->token]) }}" class="text-15" style="color:#333;">{{ __('Export CSV') }}</a>
                    <form method="POST" action="{{ route('bookings.invoice.resend', $booking) }}" style="display:inline;">
                        @csrf
                        <button type="submit" style="background:#fff;border:1px solid #ccc;border-radius:8px;padding:.45rem .85rem;cursor:pointer;font-family:inherit;">{{ __('Resend invoice') }}</button>
                    </form>
                </div>
            </div>
        @endif

        @if (isset($catalogServices) && $catalogServices->isNotEmpty() && ($booking->user_id === auth()->id() || $booking->email === auth()->user()->email) && ($booking->status === BookingStatus::Confirmed || $booking->status === BookingStatus::PendingPayment))
            <h2 class="text-20 mt-40 mb-15">{{ __('Request a hotel service') }}</h2>
            <p class="text-14 mb-15" style="opacity:.85;">{{ __('Choose an add-on for this stay. The hotel will confirm availability and pricing.') }}</p>
            <form method="POST" action="{{ route('member.booking-service-requests.store', $booking) }}" class="mt-10" style="max-width:520px;">
                @csrf
                <div class="form-row" style="margin-bottom:1rem;">
                    <label for="hotel_service_id">{{ __('Service') }}</label>
                    <select name="hotel_service_id" id="hotel_service_id" required style="width:100%;max-width:400px;padding:0.5rem;border:1px solid #ccc;border-radius:8px;">
                        <option value="">{{ __('— Select —') }}</option>
                        @foreach ($catalogServices as $svc)
                            <option value="{{ $svc->id }}" @selected(old('hotel_service_id') == $svc->id)>{{ $svc->name }} — {{ number_format((float) $svc->price, 0) }} {{ __('TZS') }} @if($svc->branch) ({{ $svc->branch->name }}) @endif</option>
                        @endforeach
                    </select>
                    @error('hotel_service_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                </div>
                <div class="form-row" style="margin-bottom:1rem;">
                    <label for="qty">{{ __('Quantity') }}</label>
                    <input type="number" name="quantity" id="qty" value="{{ old('quantity', 1) }}" min="1" max="99" style="width:6rem;padding:0.5rem;border:1px solid #ccc;border-radius:8px;">
                    @error('quantity')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                </div>
                <div class="form-row" style="margin-bottom:1rem;">
                    <label for="svc_notes">{{ __('Notes (optional)') }}</label>
                    <textarea name="notes" id="svc_notes" rows="3" style="width:100%;padding:0.5rem;border:1px solid #ccc;border-radius:8px;font-family:inherit;">{{ old('notes') }}</textarea>
                    @error('notes')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;">{{ __('Submit request') }}</button>
            </form>
        @endif

        @if ($booking->hotelServiceRequests->isNotEmpty())
            <h2 class="text-20 mt-40 mb-15">{{ __('Your service requests') }}</h2>
            <ul class="text-15" style="line-height:1.8;padding-left:1.2rem;">
                @foreach ($booking->hotelServiceRequests as $req)
                    <li>
                        <strong>{{ $req->service?->name }}</strong>
                        × {{ $req->quantity }} — <em>{{ $req->status }}</em>
                        @if ($req->notes)
                            <br><span class="text-13" style="opacity:.8;">{{ $req->notes }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif

        @if ($booking->user_id === auth()->id() && ($booking->status === BookingStatus::Confirmed || $booking->status === BookingStatus::PendingPayment))
            <h2 class="text-20 mt-40 mb-15">{{ __('Request stay extension') }}</h2>
            <p class="text-14 mb-15" style="opacity:.85;">{{ __('Send a message to the hotel (admin will see it in dashboard notifications).') }}</p>
            <form method="POST" action="{{ route('bookings.extend', $booking) }}" class="mt-10">
                @csrf
                <textarea name="message" required rows="4" style="width:100%;max-width:520px;padding:0.5rem;border:1px solid #ccc;border-radius:8px;font-family:inherit;" placeholder="{{ __('Preferred new check-out date or number of extra nights…') }}">{{ old('message') }}</textarea>
                @error('message')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-15" style="border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;">{{ __('Send extension request') }}</button>
            </form>
        @endif

        @if ($booking->user_id === auth()->id() && ($booking->status === BookingStatus::PendingPayment || $booking->status === BookingStatus::Confirmed))
            <h2 class="text-20 mt-40 mb-15">{{ __('Set check-in & check-out') }}</h2>
            <p class="text-14 mb-15" style="opacity:.85;">{{ __('Saving dates updates nights and total from the room nightly rate.') }}</p>
            <form method="POST" action="{{ route('bookings.update-dates', $booking) }}" class="mt-15">
                @csrf
                @method('PATCH')
                <div class="form-row" style="margin-bottom:1rem;">
                    <label for="check_in">{{ __('Check-in') }}</label>
                    <input type="date" id="check_in" name="check_in" value="{{ old('check_in', $booking->check_in?->format('Y-m-d')) }}" required>
                    @error('check_in')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                </div>
                <div class="form-row" style="margin-bottom:1rem;">
                    <label for="check_out">{{ __('Check-out') }}</label>
                    <input type="date" id="check_out" name="check_out" value="{{ old('check_out', $booking->check_out?->format('Y-m-d')) }}" required>
                    @error('check_out')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
                </div>
                <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;">{{ __('Save dates') }}</button>
            </form>
        @endif
    </div>

    <p class="mt-30"><a href="{{ route('bookings.index') }}">{{ __('← All bookings') }}</a></p>

    @if ($booking->status === BookingStatus::PendingPayment && $booking->payment_deadline_at)
    <script>
    (function () {
      var el = document.getElementById('member-pay-countdown');
      if (!el) return;
      var deadline = new Date(el.getAttribute('data-deadline')).getTime();
      function fmt(left) {
        var h = Math.floor(left / 3600);
        var m = Math.floor((left % 3600) / 60);
        var s = left % 60;
        if (h > 0) return h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
        return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
      }
      function tick() {
        var left = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
        el.textContent = left <= 0 ? @json(__('Expired')) : fmt(left);
        if (left > 0) setTimeout(tick, 1000);
      }
      tick();
    })();
    </script>
    @endif
@endsection
