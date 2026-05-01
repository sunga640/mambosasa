@extends('layouts.kitchen')

@section('title', __('Kitchen Reports'))

@section('content')
    @php
        $maxOrders = max(1, (int) $trend->max('orders'));
        $maxSales = max(1, (float) $trend->max('sales'));
        $maxItemQty = max(1, (int) $topItems->max('qty'));
        $maxRoomOrders = max(1, (int) $roomPerformance->max('total_orders'));
        $statusTotal = max(1, (int) $statusRows->sum('total'));
    @endphp

    <style>
        .k-report-shell {
            display: grid;
            gap: 1rem;
        }
        .k-report-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .k-report-kpi {
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface-card);
            padding: 1rem;
        }
        .k-report-kpi small {
            display: block;
            color: var(--brand-theme-muted);
            text-transform: uppercase;
            letter-spacing: .12em;
            margin-bottom: .4rem;
        }
        .k-report-kpi strong {
            font-size: 1.85rem;
            color: #fff;
        }
        .k-report-panels {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 1rem;
        }
        .k-report-chart {
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface);
            padding: 1rem;
        }
        .k-report-chart h2,
        .k-report-chart h3 {
            margin-top: 0;
            color: #f8ecd0;
        }
        .k-report-chart span,
        .k-report-chart strong,
        .k-report-chart text {
            color: #f8fafc;
        }
        .k-svg-chart {
            width: 100%;
            height: auto;
            display: block;
        }
        .k-status-list {
            display: grid;
            gap: .75rem;
        }
        .k-status-row {
            display: grid;
            gap: .35rem;
        }
        .k-status-bar {
            height: 12px;
            background: rgba(255,255,255,.06);
            border: 1px solid var(--brand-theme-border);
            overflow: hidden;
        }
        .k-status-bar > span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, rgba(213,172,66,.95), rgba(213,172,66,.28));
        }
        .k-rank-list {
            display: grid;
            gap: .75rem;
        }
        .k-rank-item {
            display: grid;
            gap: .35rem;
        }
        .k-rank-item__bar {
            height: 10px;
            background: rgba(255,255,255,.05);
            border: 1px solid rgba(213,172,66,.14);
        }
        .k-rank-item__bar > span {
            display: block;
            height: 100%;
            background: linear-gradient(90deg, rgba(59,130,246,.9), rgba(59,130,246,.3));
        }
        .k-print-note {
            color: var(--brand-theme-muted);
            font-size: .84rem;
        }
        .k-report-filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .9rem;
            align-items: end;
        }
        @media (max-width: 1023px) {
            .k-report-panels {
                grid-template-columns: 1fr;
            }
        }
        @media print {
            body.kitchen-body {
                overflow: visible !important;
                background: #fff !important;
                color: #111827 !important;
            }
            .k-sidebar,
            .k-top,
            .k-actions,
            .dash-btn,
            form {
                display: none !important;
            }
            .k-main,
            .k-card,
            .k-report-chart,
            .k-report-kpi {
                background: #fff !important;
                color: #111827 !important;
                border-color: #d1d5db !important;
            }
            .k-card h1,
            .k-card h2,
            .k-card h3,
            .k-report-chart h2,
            .k-report-chart h3 {
                color: #111827 !important;
            }
            .k-muted,
            .k-print-note,
            .k-report-kpi small {
                color: #4b5563 !important;
            }
        }
    </style>

    <div class="k-report-shell">
        <div class="k-actions" style="justify-content:space-between;align-items:flex-start;">
            <div>
                <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Reports') }}</h1>
                <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Kitchen-only analytics for room-service demand, kitchen revenue, production flow, and top-performing dishes for the selected period.') }}</p>
            </div>
            <div class="k-actions">
                <button type="button" class="dash-btn dash-btn--primary" onclick="window.print()">{{ __('Download PDF') }}</button>
            </div>
        </div>

        <section class="k-card">
            <form method="GET" action="{{ route('kitchen.reports.index') }}" class="k-form-section">
                <div class="k-report-filter-grid">
                    <div class="k-field">
                        <label for="kitchen-report-from">{{ __('From') }}</label>
                        <input id="kitchen-report-from" type="date" name="from" value="{{ $from->format('Y-m-d') }}">
                    </div>
                    <div class="k-field">
                        <label for="kitchen-report-to">{{ __('To') }}</label>
                        <input id="kitchen-report-to" type="date" name="to" value="{{ $to->format('Y-m-d') }}">
                    </div>
                    <div class="k-field">
                        <label for="kitchen-report-status">{{ __('Order status') }}</label>
                        <select id="kitchen-report-status" name="status">
                            <option value="">{{ __('All statuses') }}</option>
                            @foreach ($statuses as $status)
                                <option value="{{ $status->value }}" @selected($statusFilter === $status->value)>{{ $status->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    @if ($supportsPaymentTracking)
                        <div class="k-field">
                            <label for="kitchen-report-payment">{{ __('Payment state') }}</label>
                            <select id="kitchen-report-payment" name="payment">
                                <option value="">{{ __('All payments') }}</option>
                                <option value="paid" @selected($paymentFilter === 'paid')>{{ __('Paid') }}</option>
                                <option value="unpaid" @selected($paymentFilter === 'unpaid')>{{ __('Not paid') }}</option>
                                <option value="cash_pending" @selected($paymentFilter === 'cash_pending')>{{ __('Cash pending') }}</option>
                                <option value="processing" @selected($paymentFilter === 'processing')>{{ __('Online processing') }}</option>
                                <option value="bill_later" @selected($paymentFilter === 'bill_later')>{{ __('Bill at checkout') }}</option>
                            </select>
                        </div>
                    @endif
                    <div class="k-field">
                        <label for="kitchen-report-search">{{ __('Search') }}</label>
                        <input id="kitchen-report-search" type="search" name="q" value="{{ $search }}" placeholder="{{ __('Room, guest, phone, order ref, dish') }}">
                    </div>
                </div>
                <div class="k-actions">
                    <button type="submit" class="dash-btn dash-btn--primary">{{ __('Apply filters') }}</button>
                    <a href="{{ route('kitchen.reports.index') }}" class="dash-btn dash-btn--ghost">{{ __('Reset') }}</a>
                    <div class="k-print-note">{{ __('Tip: use Download PDF to save this exact report with its charts.') }}</div>
                </div>
            </form>
        </section>

        <section class="k-report-grid">
            <article class="k-report-kpi"><small>{{ __('Orders total') }}</small><strong>{{ number_format($summary['orders_total']) }}</strong></article>
            <article class="k-report-kpi"><small>{{ __('Sales total') }}</small><strong>{{ number_format($summary['sales_total'], 0) }}</strong></article>
            <article class="k-report-kpi"><small>{{ __('Average order value') }}</small><strong>{{ number_format($summary['avg_ticket'], 0) }}</strong></article>
            <article class="k-report-kpi"><small>{{ __('Average prep minutes') }}</small><strong>{{ number_format($summary['avg_prep_minutes'], 1) }}</strong></article>
        </section>

        <section class="k-report-panels">
            <article class="k-report-chart">
                <h2>{{ __('Daily order trend') }}</h2>
                <p class="text-13 k-muted">{{ __('Orders and sales movement across the selected dates.') }}</p>
                <svg class="k-svg-chart" viewBox="0 0 720 280" preserveAspectRatio="none" role="img" aria-label="{{ __('Daily order trend chart') }}">
                    <line x1="40" y1="220" x2="690" y2="220" stroke="rgba(213,172,66,.28)" stroke-width="1" />
                    <line x1="40" y1="30" x2="40" y2="220" stroke="rgba(213,172,66,.28)" stroke-width="1" />
                    @foreach ($trend as $index => $point)
                        @php
                            $count = max(1, $trend->count());
                            $x = 40 + (($index / max(1, $count - 1)) * 640);
                            $barHeight = ($point['sales'] / $maxSales) * 120;
                            $barY = 220 - $barHeight;
                            $circleY = 220 - (($point['orders'] / $maxOrders) * 150);
                        @endphp
                        <rect x="{{ $x - 10 }}" y="{{ number_format($barY, 2, '.', '') }}" width="20" height="{{ number_format($barHeight, 2, '.', '') }}" fill="rgba(59,130,246,.35)" stroke="rgba(59,130,246,.9)" />
                        <circle cx="{{ number_format($x, 2, '.', '') }}" cy="{{ number_format($circleY, 2, '.', '') }}" r="4.5" fill="#38bdf8" />
                        @if ($index > 0)
                            @php
                                $prev = $trend[$index - 1];
                                $prevX = 40 + ((($index - 1) / max(1, $count - 1)) * 640);
                                $prevY = 220 - (($prev['orders'] / $maxOrders) * 150);
                            @endphp
                            <line x1="{{ number_format($prevX, 2, '.', '') }}" y1="{{ number_format($prevY, 2, '.', '') }}" x2="{{ number_format($x, 2, '.', '') }}" y2="{{ number_format($circleY, 2, '.', '') }}" stroke="#38bdf8" stroke-width="3" />
                        @endif
                        @if ($index < 10 || $index === $trend->count() - 1)
                            <text x="{{ number_format($x, 2, '.', '') }}" y="248" text-anchor="middle" fill="currentColor" font-size="11">{{ $point['label'] }}</text>
                        @endif
                    @endforeach
                </svg>
            </article>

            <article class="k-report-chart">
                <h2>{{ __('Status distribution') }}</h2>
                <p class="text-13 k-muted">{{ __('How kitchen orders moved through the workflow.') }}</p>
                <div class="k-status-list mt-20">
                    @foreach ($statusRows as $row)
                        <div class="k-status-row">
                            <div class="k-actions" style="justify-content:space-between;">
                                <span>{{ $row['label'] }}</span>
                                <strong>{{ number_format($row['total']) }}</strong>
                            </div>
                            <div class="k-status-bar">
                                <span style="width:{{ ($row['total'] / $statusTotal) * 100 }}%;"></span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>
        </section>

        <section class="k-report-panels">
            <article class="k-report-chart">
                <h3>{{ __('Top ordered dishes') }}</h3>
                @if ($topItems->isEmpty())
                    <p class="text-13 k-muted">{{ __('No menu sales in this period.') }}</p>
                @else
                    <div class="k-rank-list">
                        @foreach ($topItems as $item)
                            <div class="k-rank-item">
                                <div class="k-actions" style="justify-content:space-between;">
                                    <span>{{ $item->item_name }}</span>
                                    <span class="k-muted">{{ number_format((int) $item->qty) }} {{ __('qty') }} · {{ number_format((float) $item->sales, 0) }}</span>
                                </div>
                                <div class="k-rank-item__bar"><span style="width:{{ (((int) $item->qty) / $maxItemQty) * 100 }}%;"></span></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="k-report-chart">
                <h3>{{ __('Top rooms by order volume') }}</h3>
                @if ($roomPerformance->isEmpty())
                    <p class="text-13 k-muted">{{ __('No room demand in this period.') }}</p>
                @else
                    <div class="k-rank-list">
                        @foreach ($roomPerformance as $room)
                            <div class="k-rank-item">
                                <div class="k-actions" style="justify-content:space-between;">
                                    <span>{{ $room->room_name }} <span class="k-muted">#{{ $room->room_number }}</span></span>
                                    <span class="k-muted">{{ number_format((int) $room->total_orders) }} {{ __('orders') }} · {{ number_format((float) $room->sales, 0) }}</span>
                                </div>
                                <div class="k-rank-item__bar"><span style="width:{{ (((int) $room->total_orders) / $maxRoomOrders) * 100 }}%;"></span></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </article>
        </section>
    </div>
@endsection
