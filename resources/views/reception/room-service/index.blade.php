@extends('layouts.reception')

@section('title', __('Room service orders'))

@section('content')
    <style>
        .rs-shell {
            display: grid;
            gap: 1rem;
        }
        .rs-line-head,
        .rs-line-row {
            display: grid;
            grid-template-columns: .95fr 1.05fr 1.2fr .9fr 1.05fr;
            gap: .65rem;
            align-items: start;
            min-width: 0;
        }
        .rs-line-head {
            padding: .62rem .8rem;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(17,24,39,.42);
            font-size: .7rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #cbd5e1;
            font-weight: 700;
        }
        .rs-line-row {
            padding: .68rem .8rem;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.02);
            font-size: .78rem;
        }
        .rs-line-cell {
            min-width: 0;
            overflow: hidden;
        }
        .rs-line-primary {
            font-weight: 700;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }
        .rs-line-meta {
            opacity: .72;
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.35;
        }
        .rs-items {
            white-space: normal;
            overflow: hidden;
            text-overflow: ellipsis;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .rs-pay-form {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: .4rem;
            align-items: center;
            min-width: 0;
            width: 100%;
        }
        .rs-pay-form select {
            min-width: 140px;
            font-size: .74rem;
            height: 32px;
        }
        .rs-pay-form .dash-btn {
            padding: .3rem .58rem;
            font-size: .72rem;
            min-height: 32px;
        }
        .rs-row-actions {
            display: grid;
            grid-template-columns: 1fr;
            gap: .4rem;
            justify-items: stretch;
        }
        .rs-row-actions .dash-btn {
            padding: .32rem .58rem;
            font-size: .72rem;
            min-height: 32px;
            width: 100%;
        }
        @media (max-width: 1180px) {
            .rs-line-head,
            .rs-line-row {
                grid-template-columns: 1fr;
            }
            .rs-row-actions {
                justify-items: start;
            }
            .rs-row-actions .dash-btn,
            .rs-pay-form {
                width: 100%;
            }
        }
    </style>

    <div class="rs-shell">
        <div>
            <h1 class="text-30">{{ __('Room service orders') }}</h1>
                    <p class="text-15 mt-10" style="opacity:.85;max-width:58rem;">{{ __('Reception follows payment only here. Kitchen remains the place for preparing and delivery status updates.') }}</p>
        </div>

        @if (session('status'))
            <p class="text-15" style="color:#0a6b0a;">{{ session('status') }}</p>
        @endif

        <form method="GET" action="{{ route('reception.room-service.index') }}" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:.85rem;align-items:end;">
            <div>
                <label for="reception-room-service-search">{{ __('Search') }}</label>
                <input id="reception-room-service-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Guest, room, dish, phone, ref') }}">
            </div>
            <div>
                <label for="reception-room-service-status">{{ __('Kitchen status') }}</label>
                <select id="reception-room-service-status" name="status">
                    <option value="">{{ __('All statuses') }}</option>
                    @foreach (\App\Enums\RoomServiceOrderStatus::cases() as $st)
                        <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
            @if ($supportsPaymentTracking)
                <div>
                    <label for="reception-room-service-payment">{{ __('Payment') }}</label>
                    <select id="reception-room-service-payment" name="payment">
                        <option value="">{{ __('All payments') }}</option>
                        <option value="paid" @selected($paymentFilter === 'paid')>{{ __('Paid') }}</option>
                        <option value="unpaid" @selected($paymentFilter === 'unpaid')>{{ __('Not paid') }}</option>
                        <option value="cash_pending" @selected($paymentFilter === 'cash_pending')>{{ __('Cash pending') }}</option>
                        <option value="processing" @selected($paymentFilter === 'processing')>{{ __('Online processing') }}</option>
                        <option value="bill_later" @selected($paymentFilter === 'bill_later')>{{ __('Bill at checkout') }}</option>
                    </select>
                </div>
            @endif
            <div style="display:flex;align-items:center;gap:.55rem;min-height:42px;">
                <label style="display:flex;align-items:center;gap:.45rem;margin:0;">
                    <input type="checkbox" name="billed" value="1" @checked($billedOnly)>
                    {{ __('Generated bills only') }}
                </label>
            </div>
            <div style="display:flex;gap:.6rem;align-items:end;">
                <button type="submit" class="dash-btn dash-btn--primary">{{ __('Apply') }}</button>
                <a href="{{ route('reception.room-service.index') }}" class="dash-btn dash-btn--ghost">{{ __('Reset') }}</a>
            </div>
        </form>

        <div class="rs-line-head">
            <div>{{ __('When / Ref') }}</div>
            <div>{{ __('Guest / Room') }}</div>
            <div>{{ __('Items / Kitchen') }}</div>
            <div>{{ __('Payment') }}</div>
            <div style="text-align:right;">{{ __('Reception action') }}</div>
        </div>

        @forelse ($orders as $o)
            <div class="rs-line-row">
                <div class="rs-line-cell">
                    <div class="rs-line-primary">{{ $o->created_at?->format('Y-m-d H:i') }}</div>
                    <div class="rs-line-meta">{{ $o->public_reference ?: ('#'.$o->id) }}</div>
                </div>
                <div class="rs-line-cell">
                    <div class="rs-line-primary">{{ $o->guest_name ?: $o->user?->name ?: __('Guest') }}</div>
                    <div class="rs-line-meta">{{ $o->room?->name }} (#{{ $o->room?->room_number ?? '-' }})</div>
                </div>
                <div class="rs-line-cell">
                    <div class="rs-items">{{ $o->items->map(fn ($line) => $line->item_name.' x '.$line->quantity)->implode(', ') }}</div>
                    <div class="rs-line-meta">{{ $o->statusEnum()->label() }} · {{ $o->estimated_ready_at?->format('Y-m-d H:i') ?? '-' }}</div>
                </div>
                <div class="rs-line-cell">
                    <div class="rs-line-primary">{{ $o->paymentStatusLabel() }}</div>
                    <div class="rs-line-meta">{{ $o->bookingMethod?->name ?: __('No method') }} · {{ number_format((float) $o->total_amount, 0) }} TZS</div>
                    @if ($o->hasGeneratedBill())
                        <div class="rs-line-meta">{{ __('Bill ready') }} · {{ $o->billReference() }}</div>
                    @endif
                </div>
                <div class="rs-line-cell">
                    <div class="rs-row-actions">
                        <form method="POST" action="{{ route('reception.room-service.update', $o) }}" class="rs-pay-form">
                            @csrf
                            <select name="booking_method_id">
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}" @selected($o->booking_method_id === $method->id)>{{ $method->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="dash-btn dash-btn--ghost">{{ __('Save method') }}</button>
                        </form>
                        @if ($o->hasGeneratedBill())
                            <a href="{{ route('reception.room-service.bill.show', $o) }}" class="dash-btn dash-btn--ghost">{{ __('Open bill') }}</a>
                        @endif
                        @if ($supportsPaymentTracking && ! $o->isPaid())
                            <form method="POST" action="{{ route('reception.room-service.confirm-paid', $o) }}">
                                @csrf
                                <button type="submit" class="dash-btn dash-btn--primary">{{ __('Confirm paid') }}</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="rs-line-row">
                <div class="rs-line-cell">{{ __('No orders yet.') }}</div>
            </div>
        @endforelse

        <div>{{ $orders->links() }}</div>
    </div>
@endsection
