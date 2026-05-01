@extends('layouts.kitchen')

@section('title', __('Kitchen Dashboard'))

@section('content')
    <style>
        .k-dashboard-page {
            display: grid;
            gap: 1rem;
            width: 100%;
            min-width: 0;
        }
        .k-tabs {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            border-bottom: 1px solid var(--brand-theme-border);
            padding: .35rem 0 .85rem;
            position: sticky;
            top: 0;
            z-index: 25;
            background: linear-gradient(180deg, rgba(26, 28, 31, 0.98), rgba(26, 28, 31, 0.92));
        }
        .k-tab {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            text-decoration: none;
            color: var(--brand-theme-text);
            padding: .7rem 1rem;
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface-soft);
            font-size: .9rem;
            font-weight: 600;
            border-radius: 12px;
        }
        .k-tab:hover {
            border-color: rgba(125, 211, 252, 0.3);
            background: rgba(56, 189, 248, 0.12);
        }
        .k-tab.is-active {
            color: var(--brand-theme-heading);
            background: var(--brand-theme-highlight);
        }
        .k-dashboard-stack,
        .k-dashboard-gallery,
        .k-dashboard-band {
            display: grid;
            grid-template-columns: minmax(0, 1fr);
            gap: 1rem;
            min-width: 0;
        }
        .k-highlight {
            border: 1px solid rgba(125, 211, 252, 0.22);
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.12), rgba(56, 189, 248, 0.03));
            padding: 1rem 1.1rem;
            border-radius: 14px;
        }
        .k-chip {
            display: inline-flex;
            align-items: center;
            padding: .28rem .65rem;
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface-soft);
            color: var(--brand-theme-muted);
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            font-weight: 700;
        }
        .k-mini-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
            min-width: 0;
        }
        .k-team-card {
            border: 1px solid rgba(125, 211, 252, 0.16);
            background: rgba(255,255,255,.02);
            padding: 1rem;
            border-radius: 14px;
        }
        .k-panel-actions {
            display:flex;
            gap:.7rem;
            flex-wrap:wrap;
            align-items:center;
            justify-content:flex-end;
        }
        .k-panel-btn {
            display:inline-flex;
            align-items:center;
            justify-content:center;
            min-height:42px;
            padding:.72rem 1rem;
            border-radius:12px;
            border:1px solid rgba(125, 211, 252, 0.24);
            background:linear-gradient(135deg, rgba(56, 189, 248, 0.16), rgba(56, 189, 248, 0.06));
            color:#e0f2fe;
            text-decoration:none;
            font-size:.84rem;
            font-weight:700;
            line-height:1;
            white-space:nowrap;
            transition:transform .18s ease, border-color .18s ease, background .18s ease;
        }
        .k-panel-btn:hover {
            transform:translateY(-1px);
            border-color:rgba(125, 211, 252, 0.42);
            background:linear-gradient(135deg, rgba(56, 189, 248, 0.24), rgba(56, 189, 248, 0.1));
            color:#f0f9ff;
        }
        .k-panel-btn--soft {
            background:rgba(255,255,255,.03);
            color:var(--brand-theme-text);
        }
        .k-panel-btn--soft:hover {
            background:rgba(56, 189, 248, 0.12);
            color:#f0f9ff;
        }
        .k-board {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 1rem;
        }
        .k-board__col,
        .k-room-qr {
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface-card);
            padding: 1rem;
            border-radius: 14px;
            min-width: 0;
        }
        .k-board__item {
            padding: .75rem .85rem;
            border: 1px solid rgba(125, 211, 252, 0.14);
            background: rgba(255, 255, 255, 0.03);
            margin-bottom: .65rem;
            border-radius: 12px;
        }
        .k-room-qr__canvas {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 210px;
            padding: 1rem;
            background: #ffffff;
            border-radius: 12px;
        }
        .k-actions-between {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .k-order-stack {
            display: grid;
            gap: .7rem;
        }
        .k-order-card {
            display: grid;
            gap: .72rem;
            padding: .82rem .9rem;
            border: 1px solid rgba(125, 211, 252, 0.16);
            background: rgba(255, 255, 255, 0.02);
            border-radius: 14px;
        }
        .k-order-card__top,
        .k-guest-card__meta {
            display: flex;
            justify-content: space-between;
            gap: .8rem;
            flex-wrap: wrap;
            align-items: flex-start;
        }
        .k-order-card__status {
            display: inline-flex;
            align-items: center;
            padding: .35rem .7rem;
            border-radius: 999px;
            background: rgba(52, 195, 143, 0.12);
            color: #86efac;
            border: 1px solid rgba(52, 195, 143, 0.22);
            font-size: .78rem;
            font-weight: 700;
            text-transform: capitalize;
        }
        .k-order-card__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(135px, 1fr));
            gap: .72rem;
        }
        .k-order-card__label {
            display: block;
            font-size: .72rem;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--brand-theme-muted);
            margin-bottom: .35rem;
            font-weight: 700;
        }
        .k-order-card__value {
            color: var(--brand-theme-text);
            font-size: .84rem;
            line-height: 1.4;
        }
        .k-order-card__items {
            display: grid;
            gap: .22rem;
        }
        .k-order-card__items div {
            font-size: .78rem;
            color: var(--brand-theme-text);
        }
        .k-order-card__items span {
            color: var(--brand-theme-heading);
            font-weight: 700;
        }
        .k-task-grid {
            display:grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: .85rem;
        }
        .k-task-mini {
            border:1px solid rgba(52, 195, 143, 0.18);
            background:rgba(52, 195, 143, 0.08);
            padding:.85rem .95rem;
            border-radius:14px;
        }
        .k-task-list {
            display:grid;
            gap:.8rem;
        }
        .k-task-item {
            border:1px solid rgba(52, 195, 143, 0.18);
            background:rgba(255,255,255,.02);
            border-radius:14px;
            padding:.9rem 1rem;
            display:grid;
            gap:.55rem;
        }
        .k-task-top {
            display:flex;
            justify-content:space-between;
            gap:.75rem;
            flex-wrap:wrap;
            align-items:flex-start;
        }
        .k-task-badge {
            display:inline-flex;
            align-items:center;
            padding:.32rem .7rem;
            border-radius:999px;
            border:1px solid rgba(52, 195, 143, 0.22);
            background:rgba(52, 195, 143, 0.12);
            color:#9ff2c0;
            font-size:.74rem;
            font-weight:700;
        }
        .k-preview-toolbar,
        .k-preview-search,
        .k-filter-pills {
            display: flex;
            gap: .75rem;
            flex-wrap: wrap;
            align-items: end;
        }
        .k-filter-pill {
            border: 1px solid var(--brand-theme-border);
            background: var(--brand-theme-surface-soft);
            color: var(--brand-theme-text);
            padding: .65rem .95rem;
            font: inherit;
            font-size: .82rem;
            letter-spacing: .06em;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 12px;
        }
        .k-filter-pill.is-active {
            color: var(--brand-theme-heading);
            background: var(--brand-theme-highlight);
        }
        .k-guest-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }
        .k-guest-card {
            overflow: hidden;
            padding: 0;
            display: grid;
            grid-template-rows: 190px auto;
            min-height: 100%;
            background: var(--brand-theme-surface-card);
        }
        .k-guest-card__media {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.22), rgba(35, 38, 43, 0.3));
        }
        .k-guest-card__media img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .k-guest-card__body {
            padding: 1.15rem;
            display: grid;
            gap: .8rem;
        }
        .k-guest-card__cta {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: .8rem 1.15rem;
            border: 1px solid rgba(125, 211, 252, 0.26);
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.2), rgba(56, 189, 248, 0.08));
            color: #e0f2fe;
            text-decoration: none;
            font-size: .84rem;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 12px;
        }
        .k-guest-card__cta:hover {
            background: linear-gradient(135deg, rgba(56, 189, 248, 0.32), rgba(56, 189, 248, 0.12));
            color: #f0f9ff;
        }
        @media (max-width: 720px) {
            .k-tabs {
                top: 72px;
            }
        }
    </style>

    <div class="k-dashboard-page">
        <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:1rem;flex-wrap:wrap;">
            <div>
                <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Dashboard') }}</h1>
                <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Monitor room dining demand, dispatch progress, menu readiness, and QR ordering activity in one place.') }}</p>
            </div>
            <a href="{{ route('profile.edit') }}" class="k-panel-btn k-panel-btn--soft">{{ __('My Profile') }}</a>
        </div>

        <nav class="k-tabs" aria-label="{{ __('Kitchen workspace tabs') }}">
            <a href="#menu-schedule" class="k-tab is-active">{{ __('Menu Schedule') }}</a>
            <a href="#kitchen-team" class="k-tab">{{ __('Kitchen Team') }}</a>
            <a href="#menu-builder" class="k-tab">{{ __('Menu Builder') }}</a>
            <a href="{{ route('kitchen.menu.index') }}" class="k-tab">{{ __('Menu Items') }}</a>
            <a href="#station-templates" class="k-tab">{{ __('Dining Station Templates') }}</a>
            <a href="{{ route('kitchen.orders.index') }}" class="k-tab">{{ __('KDS') }}</a>
            <a href="#qr-studio" class="k-tab">{{ __('Kiosk Configuration') }}</a>
        </nav>

        <div class="k-stats">
            <div class="k-stat"><small>{{ __('Orders today') }}</small><strong>{{ $summary['total_today'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Pending') }}</small><strong>{{ $summary['pending'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Preparing') }}</small><strong>{{ $summary['preparing'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Completed') }}</small><strong>{{ $summary['completed'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Active menu') }}</small><strong>{{ $summary['active_menu'] }}</strong></div>
            <div class="k-stat"><small>{{ __('QR rooms') }}</small><strong>{{ $summary['qr_rooms'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Rooms covered') }}</small><strong>{{ $summary['rooms_total'] }}</strong></div>
            <div class="k-stat"><small>{{ __('Scanned today') }}</small><strong>{{ $summary['scanned_today'] }}</strong></div>
        </div>

        <div class="k-dashboard-stack" id="menu-schedule">
            <section class="k-card">
                <h2>{{ __('7-day order trend') }}</h2>
                @php $maxTrend = max(1, collect($trend)->max('total')); @endphp
                <div class="k-chart-bars">
                    @foreach ($trend as $point)
                        <div class="k-bar-col">
                            <div class="text-12">{{ $point['total'] }}</div>
                            <div class="k-bar" style="height:{{ max(14, (int) (($point['total'] / $maxTrend) * 180)) }}px;"></div>
                            <div class="text-12 k-muted">{{ $point['label'] }}</div>
                        </div>
                    @endforeach
                </div>
            </section>

            <section class="k-card">
                <h2>{{ __('Status mix') }}</h2>
                <div class="k-mini-grid">
                    @foreach ($statusBreakdown as $status => $total)
                        <div class="k-stat">
                            <small>{{ ucfirst((string) $status) }}</small>
                            <strong>{{ $total }}</strong>
                        </div>
                    @endforeach
                    <div class="k-stat">
                        <small>{{ __('Sales today') }}</small>
                        <strong>{{ number_format($summary['sales_today'], 0) }}</strong>
                    </div>
                    <div class="k-stat">
                        <small>{{ __('Last month sales') }}</small>
                        <strong>{{ number_format($summary['sales_last_month'], 0) }}</strong>
                    </div>
                </div>
            </section>
        </div>

        <section class="k-card" id="kitchen-team">
            <div class="k-actions-between">
                <div>
                    <div class="k-chip">{{ __('Kitchen Team') }}</div>
                    <h2 class="mt-10">{{ __('Roles, staff, and assigned task ownership') }}</h2>
                    <p class="text-13 k-muted">{{ __('Create kitchen staff users, choose role permissions, assign orders to owners, and review who is completing the work.') }}</p>
                </div>
                <div class="k-panel-actions">
                    @if (auth()->user()?->canManageKitchenRoles())
                        <a href="{{ route('kitchen.roles.index') }}" class="k-panel-btn k-panel-btn--soft">{{ __('Open roles') }}</a>
                    @endif
                    @if (auth()->user()?->canManageKitchenStaff())
                        <a href="{{ route('kitchen.staff.index') }}" class="k-panel-btn k-panel-btn--soft">{{ __('Open staff') }}</a>
                    @endif
                    @if (auth()->user()?->canAssignKitchenOrders())
                        <a href="{{ route('kitchen.assignments.index') }}" class="k-panel-btn">{{ __('Open assign board') }}</a>
                    @endif
                </div>
            </div>
            <div class="k-mini-grid mt-20">
                <div class="k-team-card">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Staff accounts') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($staffSummary['staff_total'] ?? 0) }}</div>
                </div>
                <div class="k-team-card">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Kitchen roles') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($staffSummary['roles_total'] ?? 0) }}</div>
                </div>
                <div class="k-team-card">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('My active tasks') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($staffSummary['my_active_tasks'] ?? 0) }}</div>
                </div>
                <div class="k-team-card">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Unassigned queue') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($staffSummary['unassigned_orders'] ?? 0) }}</div>
                </div>
            </div>
            @if(($staffCards ?? collect())->isNotEmpty())
                <div class="k-mini-grid mt-20">
                    @foreach ($staffCards as $staffCard)
                        <article class="k-team-card">
                            <div class="fw-700">{{ $staffCard->name }}</div>
                            <div class="text-13 k-muted mt-5">{{ $staffCard->role?->name ?: __('Kitchen staff') }}</div>
                            <div class="mt-15 text-13">{{ __('Active tasks') }}: <strong>{{ number_format($staffCard->active_tasks_count ?? 0) }}</strong></div>
                            <div class="mt-5 text-13">{{ __('Completed') }}: <strong>{{ number_format($staffCard->completed_tasks_count ?? 0) }}</strong></div>
                        </article>
                    @endforeach
                </div>
            @endif
        </section>

        <section class="k-card">
            <div class="k-actions-between">
                <div>
                    <div class="k-chip">{{ __('My Task Board') }}</div>
                    <h2 class="mt-10">{{ __('Assigned work that needs your follow-up') }}</h2>
                    <p class="text-13 k-muted">{{ __('Every kitchen staff account can see the orders assigned to them here, along with the current stage and payment condition that still needs attention.') }}</p>
                </div>
                <a href="{{ route('kitchen.orders.index') }}" class="k-panel-btn k-panel-btn--soft">{{ __('Open my queue') }}</a>
            </div>

            <div class="k-task-grid mt-20">
                <div class="k-task-mini">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Assigned total') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($myTaskSummary['total_assigned'] ?? 0) }}</div>
                </div>
                <div class="k-task-mini">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Pending') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($myTaskSummary['pending_assigned'] ?? 0) }}</div>
                </div>
                <div class="k-task-mini">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Preparing') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($myTaskSummary['preparing_assigned'] ?? 0) }}</div>
                </div>
                <div class="k-task-mini">
                    <div class="text-12 k-muted" style="text-transform:uppercase;letter-spacing:.12em;">{{ __('Delivered') }}</div>
                    <div class="text-30 fw-700 mt-10">{{ number_format($myTaskSummary['delivered_assigned'] ?? 0) }}</div>
                </div>
            </div>

            <div class="k-task-list mt-20">
                @forelse ($myAssignedOrders as $taskOrder)
                    <article class="k-task-item">
                        <div class="k-task-top">
                            <div>
                                <strong>{{ $taskOrder->guest_name ?: __('Guest portal order') }}</strong>
                                <div class="text-12 k-muted mt-5">{{ $taskOrder->room?->name ?: __('Room not set') }}{{ $taskOrder->room?->room_number ? ' · #'.$taskOrder->room->room_number : '' }} · {{ $taskOrder->public_reference ?: ('#'.$taskOrder->id) }}</div>
                            </div>
                            <span class="k-task-badge">{{ $taskOrder->statusEnum()->label() }}</span>
                        </div>
                        <div class="text-13">{{ $taskOrder->items->map(fn ($item) => $item->item_name.' x '.$item->quantity)->implode(', ') }}</div>
                        <div class="k-mini-grid" style="grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:.75rem;">
                            <div>
                                <span class="k-order-card__label">{{ __('Payment') }}</span>
                                <div class="k-order-card__value">{{ $taskOrder->paymentStatusLabel() }}</div>
                            </div>
                            <div>
                                <span class="k-order-card__label">{{ __('ETA') }}</span>
                                <div class="k-order-card__value">{{ $taskOrder->estimated_ready_at?->format('H:i') ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="k-order-card__label">{{ __('Source') }}</span>
                                <div class="k-order-card__value">{{ strtoupper((string) $taskOrder->request_source) }}</div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="k-highlight">{{ __('No assigned tasks for your account right now.') }}</div>
                @endforelse
            </div>
        </section>

        <section class="k-card" id="menu-builder">
            <div class="k-actions-between">
                <div>
                    <div class="k-chip">{{ __('Menu Builder') }}</div>
                    <h2 class="mt-10">{{ __('Build weekly meal options the kitchen can publish') }}</h2>
                    <p class="text-13 k-muted">{{ __('This block organizes your active dishes into meal periods so the kitchen team can quickly review what guests will see after scanning each room QR code.') }}</p>
                </div>
                <a href="{{ route('kitchen.menu.index') }}" class="k-panel-btn">{{ __('Open menu builder page') }}</a>
            </div>

            <div class="k-board mt-20">
                @foreach ($builderColumns as $label => $items)
                    <section class="k-board__col">
                        <h3>{{ $label }}</h3>
                        @forelse ($items as $item)
                            <article class="k-board__item">
                                <strong>{{ $item->name }}</strong>
                                <div class="text-12 k-muted mt-5">{{ number_format((float) $item->price, 0) }} TZS · {{ $item->preparation_minutes }} {{ __('mins') }}</div>
                            </article>
                        @empty
                            <div class="text-13 k-muted">{{ __('No dishes assigned yet.') }}</div>
                        @endforelse
                    </section>
                @endforeach
            </div>
        </section>

        <section class="k-card" id="station-templates">
            <div class="k-actions-between">
                <div>
                    <div class="k-chip">{{ __('Dining Stations') }}</div>
                    <h2 class="mt-10">{{ __('Simple daily workflow') }}</h2>
                </div>
                <a href="{{ route('kitchen.orders.index') }}" class="k-panel-btn k-panel-btn--soft">{{ __('Open live KDS queue') }}</a>
            </div>
            <div class="k-dashboard-band mt-20" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr));">
                <article class="k-highlight">
                    <strong>{{ __('New orders') }}</strong>
                    <p class="text-13 k-muted mt-10">{{ __('Check fresh tickets, confirm items, and move active requests into preparation fast.') }}</p>
                </article>
                <article class="k-highlight">
                    <strong>{{ __('Preparing') }}</strong>
                    <p class="text-13 k-muted mt-10">{{ __('Cook, update ETA, and keep the handoff notes clean for the team.') }}</p>
                </article>
                <article class="k-highlight">
                    <strong>{{ __('Ready and paid') }}</strong>
                    <p class="text-13 k-muted mt-10">{{ __('Finish delivery, confirm cash payments, and let paid totals flow into reports.') }}</p>
                </article>
            </div>
        </section>

        <section class="k-card" id="qr-studio">
            <div class="k-actions-between">
                <div>
                    <div class="k-chip">{{ __('QR Studio') }}</div>
                    <h2 class="mt-10">{{ __('Generate room QR codes right from the kitchen dashboard') }}</h2>
                    <p class="text-13 k-muted">{{ __('Each QR opens the guest menu page where the customer can view dishes and use the order now button to send requests directly into the kitchen queue.') }}</p>
                </div>
                <a href="{{ route('kitchen.qr.index') }}" class="k-panel-btn">{{ __('Open full QR page') }}</a>
            </div>

            @if (! $hasQrTable)
                <div class="mt-20 k-highlight">
                    {{ __('Kitchen QR table is not ready yet. Run the kitchen migration and refresh this page.') }}
                </div>
            @endif

            <div class="k-dashboard-gallery mt-20" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr));">
                @foreach ($rooms as $room)
                    @php($code = $qrCodes->get($room->id))
                    <article class="k-room-qr">
                        <div class="k-actions-between">
                            <div>
                                <strong>{{ $room->name }}</strong>
                                <div class="text-12 k-muted mt-5">{{ $room->branch?->name }} · #{{ $room->room_number ?: '—' }}</div>
                            </div>
                            <form method="POST" action="{{ route('kitchen.qr.store', $room) }}">
                                @csrf
                                <button class="k-panel-btn" type="submit">{{ $code ? __('Regenerate') : __('Generate') }}</button>
                            </form>
                        </div>

                        @if ($code)
                            @php($qrUrl = route('site.kitchen-menu.show', $code->token))
                            <div id="dash-qr-room-{{ $room->id }}" class="k-room-qr__canvas mt-15" data-qr-url="{{ $qrUrl }}"></div>
                            <div class="text-12 mt-10" style="word-break:break-all;">{{ $qrUrl }}</div>
                            <div class="k-actions mt-15">
                                <a href="{{ $qrUrl }}" target="_blank" rel="noopener" class="k-panel-btn k-panel-btn--soft">{{ __('Preview menu') }}</a>
                                <span class="text-12 k-muted">{{ __('Last scan') }}: {{ $code->last_scanned_at?->format('Y-m-d H:i') ?: __('Not yet') }}</span>
                            </div>
                        @else
                            <p class="text-13 k-muted mt-15">{{ __('Generate a QR here, then print or download it from the full QR page.') }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
        </section>

        <section class="k-card" id="live-orders" style="background-color:#1a1d21;color:#ffffff;padding:20px;">
            <div class="k-actions-between" style="margin-bottom:20px;">
                <div>
                    <h2 style="color:var(--brand-theme-heading);margin:0;font-size:22px;">{{ __('Latest incoming orders') }}</h2>
                    <p class="text-13 k-muted" style="color:#a6b0cf;margin-top:5px;font-size:13px;max-width:800px;">
                        {{ __('Only the 5 most recent room-service and QR orders are shown here for a cleaner kitchen overview.') }}
                    </p>
                </div>
                <a href="{{ route('kitchen.orders.index') }}" class="k-panel-btn">
                    {{ __('Open full queue') }}
                </a>
            </div>

            <div class="k-order-stack">
                @forelse ($recentOrders as $order)
                    <article class="k-order-card">
                        <div class="k-order-card__top">
                            <div>
                                <strong>{{ $order->guest_name ?: __('Guest portal order') }}</strong>
                                <div class="text-12 k-muted mt-5">{{ strtoupper((string) $order->request_source) }}{{ $order->guest_phone ? ' · '.$order->guest_phone : '' }}</div>
                            </div>
                            <div class="k-order-card__status">{{ $order->status }}</div>
                        </div>

                        <div class="k-order-card__grid">
                            <div>
                                <span class="k-order-card__label">{{ __('Time') }}</span>
                                <div class="k-order-card__value">{{ $order->created_at?->format('Y-m-d H:i') ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="k-order-card__label">{{ __('Room') }}</span>
                                <div class="k-order-card__value">{{ $order->room?->name ?: __('Room not set') }}{{ $order->room?->room_number ? ' · #'.$order->room?->room_number : '' }}</div>
                            </div>
                            <div>
                                <span class="k-order-card__label">{{ __('ETA') }}</span>
                                <div class="k-order-card__value">{{ $order->estimated_ready_at?->format('H:i') ?: '—' }}</div>
                            </div>
                            <div>
                                <span class="k-order-card__label">{{ __('Total') }}</span>
                                <div class="k-order-card__value" style="color:var(--brand-theme-heading);font-weight:700;">{{ number_format((float) $order->total_amount, 0) }}</div>
                            </div>
                        </div>

                        <div>
                            <span class="k-order-card__label">{{ __('Items') }}</span>
                            <div class="k-order-card__items">
                                @foreach ($order->items as $item)
                                    <div>{{ $item->item_name }} <span>x {{ $item->quantity }}</span></div>
                                @endforeach
                            </div>
                        </div>

                        <div>
                            <span class="k-order-card__label">{{ __('Kitchen note') }}</span>
                            <div class="k-order-card__value">{{ $order->notes ?: __('No extra kitchen note') }}</div>
                        </div>
                    </article>
                @empty
                    <div class="k-highlight">{{ __('No kitchen orders yet.') }}</div>
                @endforelse
            </div>
        </section>

        <section class="k-card">
            <div class="k-preview-toolbar">
                <div>
                    <div class="k-chip">{{ __('Guest View') }}</div>
                    <h2 class="mt-10">{{ __('What the guest sees after scanning') }}</h2>
                    <p class="text-13 k-muted">{{ __('Your existing room dining menu stays public-facing. Guests open the menu, choose dishes, and tap Order now to send requests into the kitchen dashboard.') }}</p>
                </div>
                <div class="k-preview-search">
                    <div class="k-field">
                        <label>{{ __('Search guest dishes') }}</label>
                        <input type="search" id="kitchenGuestSearch" placeholder="{{ __('Search by dish, description, or price') }}">
                    </div>
                    @if ($qrCodes->first())
                        <a href="{{ route('site.kitchen-menu.show', $qrCodes->first()->token) }}" target="_blank" rel="noopener" class="k-panel-btn">{{ __('Preview customer menu') }}</a>
                    @endif
                </div>
            </div>
            <div class="k-filter-pills mt-20">
                <button type="button" class="k-filter-pill is-active" data-guest-filter="all">{{ __('All') }}</button>
                <button type="button" class="k-filter-pill" data-guest-filter="quick">{{ __('Quick prep') }}</button>
                <button type="button" class="k-filter-pill" data-guest-filter="premium">{{ __('Premium') }}</button>
                <button type="button" class="k-filter-pill" data-guest-filter="image">{{ __('With images') }}</button>
            </div>
            <div class="k-guest-grid mt-20">
                @foreach ($menuItems as $item)
                    <article class="k-card k-guest-card js-guest-view-card"
                        data-search="{{ \Illuminate\Support\Str::lower($item->name.' '.($item->description ?? '').' '.number_format((float) $item->price, 0)) }}"
                        data-prep="{{ (int) $item->preparation_minutes }}"
                        data-price="{{ (float) $item->price }}"
                        data-has-image="{{ $item->image_path ? 'yes' : 'no' }}">
                        <div class="k-guest-card__media">
                            @if ($item->image_path)
                                <img src="{{ \App\Support\PublicDisk::url($item->image_path) }}" alt="{{ $item->name }}" loading="lazy">
                            @endif
                        </div>
                        <div class="k-guest-card__body">
                            <div>
                                <strong>{{ $item->name }}</strong>
                                <p class="text-13 k-muted mt-10">{{ $item->description ?: __('Dish description will appear here for the guest.') }}</p>
                            </div>
                            <div class="k-guest-card__meta">
                                <span>{{ number_format((float) $item->price, 0) }} TZS</span>
                                <span class="text-12 k-muted">{{ $item->preparation_minutes }} {{ __('mins') }}</span>
                            </div>
                            <div class="k-guest-card__meta">
                                <span class="k-chip">{{ $item->preparation_minutes <= 20 ? __('Fast moving') : __('Chef prep') }}</span>
                                <span class="k-guest-card__cta">{{ __('Order now') }}</span>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('.k-tabs a[href^="#"]').forEach(function (link) {
            link.addEventListener('click', function (event) {
                event.preventDefault();
                var target = document.querySelector(link.getAttribute('href'));
                if (!target) return;
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        document.querySelectorAll('[data-qr-url]').forEach(function (el) {
            if (typeof QRCode === 'undefined' || el.childElementCount > 0) return;
            new QRCode(el, {
                text: el.getAttribute('data-qr-url'),
                width: 160,
                height: 160,
                colorDark: '#122223',
                colorLight: '#ffffff',
            });
        });

        (function () {
            var search = document.getElementById('kitchenGuestSearch');
            var filterButtons = Array.from(document.querySelectorAll('[data-guest-filter]'));
            var cards = Array.from(document.querySelectorAll('.js-guest-view-card'));
            if (!search || filterButtons.length === 0 || cards.length === 0) return;

            var activeFilter = 'all';

            function applyFilters() {
                var term = (search.value || '').toLowerCase().trim();
                cards.forEach(function (card) {
                    var matchesSearch = term === '' || (card.dataset.search || '').indexOf(term) !== -1;
                    var matchesFilter = true;

                    if (activeFilter === 'quick') matchesFilter = Number(card.dataset.prep || 0) <= 20;
                    if (activeFilter === 'premium') matchesFilter = Number(card.dataset.price || 0) >= 18000;
                    if (activeFilter === 'image') matchesFilter = (card.dataset.hasImage || '') === 'yes';

                    card.style.display = matchesSearch && matchesFilter ? '' : 'none';
                });
            }

            search.addEventListener('input', applyFilters);
            filterButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    activeFilter = button.dataset.guestFilter || 'all';
                    filterButtons.forEach(function (item) { item.classList.remove('is-active'); });
                    button.classList.add('is-active');
                    applyFilters();
                });
            });
        })();
    </script>
@endpush
