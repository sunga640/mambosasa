@extends('layouts.kitchen')

@section('title', __('Kitchen Orders'))

@section('content')
    <style>
        .ko-shell {
            display: grid;
            gap: 1rem;
        }
        .ko-summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: .85rem;
        }
        .ko-summary__card {
            border: 1px solid var(--brand-theme-border);
            padding: .9rem;
            min-width: 0;
        }
        .ko-summary__card strong {
            display: block;
            margin-top: .35rem;
            font-size: 1.6rem;
            color: #fff;
        }
        .ko-head,
        .ko-row {
            display: grid;
            grid-template-columns: 1fr 1.05fr 1.25fr 1fr .95fr 1.45fr;
            gap: .65rem;
            align-items: center;
            min-width: 0;
        }
        .ko-head {
            padding: .7rem .8rem;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(17,24,39,.42);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: #cbd5e1;
            font-weight: 700;
        }
        .ko-row {
            padding: .75rem .8rem;
            border: 1px solid rgba(255,255,255,.08);
            background: rgba(255,255,255,.02);
            font-size: .79rem;
        }
        .ko-row--assigned {
            border-color: rgba(34,197,94,.34);
            box-shadow: inset 0 0 0 1px rgba(34,197,94,.14);
            background: linear-gradient(135deg, rgba(34,197,94,.08), rgba(255,255,255,.02));
        }
        .ko-row--unpaid {
            border-color: rgba(245,158,11,.34);
            box-shadow: inset 0 0 0 1px rgba(245,158,11,.14);
        }
        .ko-row--assigned.ko-row--unpaid {
            border-color: rgba(34,197,94,.34);
            box-shadow: inset 0 0 0 1px rgba(34,197,94,.18);
        }
        .ko-cell {
            min-width: 0;
            overflow: hidden;
        }
        .ko-primary,
        .ko-items {
            font-weight: 700;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ko-meta {
            color: var(--brand-theme-muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .ko-pill-row {
            display: flex;
            gap: .35rem;
            flex-wrap: wrap;
        }
        .ko-pill {
            display: inline-flex;
            align-items: center;
            padding: .2rem .45rem;
            border: 1px solid rgba(213,172,66,.18);
            background: rgba(213,172,66,.08);
            color: #f8ecd0;
            font-size: .67rem;
            font-weight: 700;
            letter-spacing: .05em;
            text-transform: uppercase;
        }
        .ko-pill--success { border-color: rgba(34,197,94,.24); background: rgba(34,197,94,.14); color: #bbf7d0; }
        .ko-pill--warn { border-color: rgba(245,158,11,.24); background: rgba(245,158,11,.14); color: #fde68a; }
        .ko-pill--info { border-color: rgba(59,130,246,.24); background: rgba(59,130,246,.14); color: #bfdbfe; }
        .ko-status-form,
        .ko-actions {
            display: flex;
            gap: .35rem;
            align-items: center;
            flex-wrap: wrap;
        }
        .ko-status-form select,
        .ko-status-form input[type="text"] {
            min-width: 0;
            height: 34px;
            font-size: .76rem;
        }
        .ko-status-form select {
            background: #0f172a;
            color: #f8fafc;
            border: 1px solid rgba(213,172,66,.3);
            padding: .25rem .55rem;
            min-width: 110px;
        }
        .ko-status-form input[type="text"] {
            flex: 1 1 150px;
            background: #111827;
            color: #e5e7eb;
            border: 1px solid rgba(213,172,66,.18);
            padding: .25rem .55rem;
        }
        .ko-status-form .dash-btn,
        .ko-actions .dash-btn {
            padding: .34rem .58rem;
            font-size: .74rem;
        }
        @media (max-width: 1280px) {
            .ko-head,
            .ko-row {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="ko-shell">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Orders') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Compact kitchen queue with visible status updates, bill actions, and payment state on one balanced line.') }}</p>
        </div>

        <section class="ko-summary">
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(213,172,66,.18), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Orders today') }}</div>
                <strong>{{ number_format($summary['today']) }}</strong>
            </article>
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(245,158,11,.18), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Pending') }}</div>
                <strong>{{ number_format($summary['pending']) }}</strong>
            </article>
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(59,130,246,.18), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Preparing') }}</div>
                <strong>{{ number_format($summary['preparing']) }}</strong>
            </article>
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(34,197,94,.18), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Completed') }}</div>
                <strong>{{ number_format($summary['completed']) }}</strong>
            </article>
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(16,185,129,.18), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Paid today') }}</div>
                <strong>{{ number_format($summary['paid_today'], 0) }}</strong>
            </article>
            <article class="ko-summary__card" style="background:linear-gradient(135deg, rgba(239,68,68,.14), rgba(48,53,61,.96));">
                <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Unpaid balance') }}</div>
                <strong>{{ number_format($summary['unpaid_total'], 0) }}</strong>
            </article>
        </section>

        <section class="k-card">
            <form method="GET" class="k-form-section">
                <div class="k-form-grid">
                    <div class="k-field">
                        <label>{{ __('Search') }}</label>
                        <input type="search" name="q" value="{{ $search }}" placeholder="{{ __('Guest, phone, room, dish, order ref') }}">
                    </div>
                    <div class="k-field">
                        <label>{{ __('Status') }}</label>
                        <select name="status">
                            <option value="">{{ __('All statuses') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($statusFilter === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($supportsPaymentTracking)
                        <div class="k-field">
                            <label>{{ __('Payment') }}</label>
                            <select name="payment">
                                <option value="">{{ __('All payments') }}</option>
                                <option value="paid" @selected($paymentFilter === 'paid')>{{ __('Paid') }}</option>
                                <option value="unpaid" @selected($paymentFilter === 'unpaid')>{{ __('Not paid') }}</option>
                                <option value="cash_pending" @selected($paymentFilter === 'cash_pending')>{{ __('Cash pending') }}</option>
                                <option value="processing" @selected($paymentFilter === 'processing')>{{ __('Online processing') }}</option>
                                <option value="bill_later" @selected($paymentFilter === 'bill_later')>{{ __('Bill at checkout') }}</option>
                            </select>
                        </div>
                    @endif
                    <div class="k-checkbox" style="min-height:42px;align-items:end;">
                        <label style="display:flex;align-items:center;gap:.45rem;margin:0;">
                            <input type="checkbox" name="billed" value="1" @checked($billedOnly)>
                            {{ __('Generated bills only') }}
                        </label>
                    </div>
                </div>
                <div class="k-actions">
                    <button class="dash-btn dash-btn--primary" type="submit">{{ __('Filter queue') }}</button>
                    @if ($canSeeAllOrders)
                        <a
                            href="{{ $myTasksOnly ? route('kitchen.orders.index', collect(request()->query())->except('mine')->all()) : route('kitchen.orders.index', array_merge(request()->query(), ['mine' => 1])) }}"
                            class="dash-btn {{ $myTasksOnly ? 'dash-btn--primary' : 'dash-btn--ghost' }}">
                            {{ __('Only my tasks') }}
                        </a>
                    @endif
                    <a href="{{ route('kitchen.orders.index') }}" class="dash-btn dash-btn--ghost">{{ __('Reset') }}</a>
                </div>
            </form>
        </section>

        <div class="ko-head">
            <div>{{ __('When / Ref') }}</div>
            <div>{{ __('Guest / Room') }}</div>
            <div>{{ __('Items / Notes') }}</div>
            <div>{{ __('Status / Owner') }}</div>
            <div>{{ __('Payment / ETA') }}</div>
            <div>{{ __('Kitchen action / Bill') }}</div>
        </div>

        @forelse ($orders as $order)
            <div class="ko-row {{ $order->assigned_to_user_id ? 'ko-row--assigned' : '' }} {{ $order->hasPendingBalance() ? 'ko-row--unpaid' : '' }}">
                <div class="ko-cell">
                    <div class="ko-primary">{{ $order->created_at?->format('Y-m-d H:i') }}</div>
                    <div class="ko-meta">{{ $order->public_reference ?: ('#'.$order->id) }}</div>
                </div>
                <div class="ko-cell">
                    <div class="ko-primary">{{ $order->guest_name ?: __('Portal guest') }}</div>
                    <div class="ko-meta">{{ $order->room?->name ?: __('Unknown room') }} · #{{ $order->room?->room_number ?: '-' }}</div>
                </div>
                <div class="ko-cell">
                    <div class="ko-items">{{ $order->items->map(fn ($item) => $item->item_name.' x '.$item->quantity)->implode(', ') }}</div>
                    <div class="ko-meta">{{ $order->notes ?: __('No kitchen note') }}</div>
                </div>
                <div class="ko-cell">
                    <div class="ko-pill-row">
                        <span class="ko-pill">{{ strtoupper((string) $order->request_source) }}</span>
                        <span class="ko-pill {{ $order->status === 'delivered' ? 'ko-pill--success' : ($order->status === 'preparing' ? 'ko-pill--warn' : 'ko-pill--info') }}">{{ $order->statusEnum()->label() }}</span>
                        @if ($order->assigned_to_user_id)
                            <span class="ko-pill ko-pill--success">{{ __('Assigned') }}</span>
                        @endif
                        @if ($order->hasGeneratedBill())
                            <span class="ko-pill ko-pill--success">{{ __('Bill ready') }}</span>
                        @endif
                    </div>
                    <div class="ko-meta" style="margin-top:.35rem;">{{ __('Owner') }}: {{ $order->assignedTo?->name ?: __('Unassigned') }}</div>
                </div>
                <div class="ko-cell">
                    <div class="ko-primary">{{ $order->paymentStatusLabel() }}</div>
                    <div class="ko-meta">{{ $order->bookingMethod?->name ?: __('No method') }} · {{ number_format((float) $order->total_amount, 0) }} TZS</div>
                    <div class="ko-meta">{{ __('ETA') }} {{ $order->estimated_ready_at?->format('H:i') ?: '-' }}</div>
                </div>
                <div class="ko-cell">
                    @if ($canAssignOrders)
                        <form method="POST" action="{{ route('kitchen.assignments.store', $order) }}" class="ko-status-form" style="margin-bottom:.45rem;">
                            @csrf
                            <select name="assigned_to_user_id">
                                <option value="">{{ __('Unassigned') }}</option>
                                @foreach ($staffOptions as $staff)
                                    <option value="{{ $staff->id }}" @selected((int) $order->assigned_to_user_id === (int) $staff->id)>{{ $staff->name }}</option>
                                @endforeach
                            </select>
                            <button class="dash-btn dash-btn--ghost" type="submit">{{ __('Assign') }}</button>
                        </form>
                    @endif
                    <form method="POST" action="{{ route('kitchen.orders.update', $order) }}" class="ko-status-form">
                        @csrf
                        <select name="status">
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($order->status === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                        <input type="text" name="notes" value="{{ $order->notes }}" placeholder="{{ __('Short note') }}">
                        <button class="dash-btn dash-btn--primary" type="submit">{{ __('Update') }}</button>
                    </form>
                    <div class="ko-actions" style="margin-top:.4rem;">
                        @if (! $order->isPaid())
                            <form method="POST" action="{{ route('kitchen.orders.confirm-paid', $order) }}">
                                @csrf
                                <button class="dash-btn dash-btn--ghost" type="submit">{{ __('Confirm paid') }}</button>
                            </form>
                        @endif
                        @if ($order->canGenerateBill())
                            <form method="POST" action="{{ route('kitchen.orders.bill.generate', $order) }}">
                                @csrf
                                <button class="dash-btn dash-btn--ghost" type="submit">{{ $order->hasGeneratedBill() ? __('Open bill') : __('Generate bill') }}</button>
                            </form>
                        @elseif ($order->hasGeneratedBill())
                            <a href="{{ route('kitchen.orders.bill.show', $order) }}" class="dash-btn dash-btn--ghost">{{ __('Open bill') }}</a>
                        @else
                            <span class="ko-meta">{{ __('Deliver order to unlock bill') }}</span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="ko-row">
                <div class="ko-cell">{{ __('No kitchen orders found for this filter.') }}</div>
            </div>
        @endforelse

        <div>{{ $orders->links() }}</div>
    </div>
@endsection
