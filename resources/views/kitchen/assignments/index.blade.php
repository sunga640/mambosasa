@extends('layouts.kitchen')

@section('title', __('Assign Orders'))

@section('content')
    <style>
        .ka-shell { display:grid; gap:1rem; }
        .ka-stats { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:.85rem; }
        .ka-stat,
        .ka-board { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface); border-radius:16px; }
        .ka-stat { padding:.9rem; display:grid; gap:.45rem; }
        .ka-board { padding:1rem; display:grid; gap:1rem; }
        .ka-layout { display:grid; grid-template-columns:minmax(0, 1fr) minmax(0, .96fr); gap:1rem; align-items:start; }
        .ka-section,
        .ka-guide,
        .ka-lane { border:1px solid rgba(125,211,252,.14); background:rgba(255,255,255,.02); border-radius:14px; padding:1rem; }
        .ka-section.is-drop,
        .ka-lane.is-drop { outline:2px dashed rgba(56,189,248,.55); outline-offset:4px; }
        .ka-col-head,
        .ka-lane-top { display:flex; justify-content:space-between; gap:.8rem; align-items:flex-start; margin-bottom:.85rem; }
        .ka-chip { display:inline-flex; align-items:center; padding:.25rem .58rem; border-radius:999px; background:rgba(56,189,248,.12); color:var(--brand-theme-heading); font-size:.72rem; font-weight:700; }
        .ka-mini-metrics { display:flex; gap:.75rem; flex-wrap:wrap; color:var(--brand-theme-muted); font-size:.8rem; }
        .ka-order-list,
        .ka-staff-lanes { display:grid; gap:.85rem; }
        .ka-order { border:1px solid rgba(125,211,252,.18); background:rgba(255,255,255,.03); padding:.85rem .9rem; border-radius:12px; display:grid; gap:.4rem; cursor:grab; }
        .ka-order-title { font-size:1rem; line-height:1.35; color:#eff9ff; }
        .ka-order small { color:var(--brand-theme-muted); line-height:1.5; }
        .ka-guide { padding:.9rem 1rem; min-height:auto; }
        .ka-lane-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:.85rem; }
        .ka-lane { min-height:170px; display:grid; gap:.75rem; align-content:start; }
        .ka-empty { color:var(--brand-theme-muted); font-size:.84rem; }
        @media (max-width: 1100px) {
            .ka-layout { grid-template-columns:1fr; }
        }
        @media (max-width: 768px) {
            .ka-board { padding:.85rem; }
            .ka-section,
            .ka-guide,
            .ka-lane { padding:.85rem; }
            .ka-col-head,
            .ka-lane-top { flex-direction:column; align-items:flex-start; }
            .ka-lane-grid { grid-template-columns:1fr; }
            .ka-mini-metrics { gap:.55rem; }
        }
    </style>

    <div class="ka-shell">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Assign Orders') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Drag orders into staff lanes to assign ownership, then track performance by completed versus active kitchen tasks.') }}</p>
        </div>

        <section class="ka-stats">
            @forelse ($staffRows as $row)
                <article class="ka-stat">
                    <div class="fw-700">{{ $row['user']->name }}</div>
                    <div class="text-13 k-muted">{{ $row['user']->role?->name ?: __('Kitchen staff') }}</div>
                    <div class="ka-mini-metrics">
                        <span>{{ __('Active') }} <strong>{{ $row['active'] }}</strong></span>
                        <span>{{ __('Completed') }} <strong>{{ $row['completed'] }}</strong></span>
                        <span>{{ __('Performance') }} <strong>{{ $row['performance'] }}%</strong></span>
                    </div>
                </article>
            @empty
                <article class="ka-stat">
                    <div class="fw-700">{{ __('No kitchen staff lanes yet') }}</div>
                    <div class="text-13 k-muted">{{ __('Create staff users from the kitchen staff page to start assigning orders.') }}</div>
                </article>
            @endforelse
        </section>

        <section class="ka-board">
            <div class="ka-layout">
                <div class="ka-order-list">
                    <div class="ka-section" data-staff="">
                        <div class="ka-col-head">
                            <div>
                                <div class="fw-700">{{ __('Unassigned queue') }}</div>
                                <small>{{ __('Orders waiting for owner allocation') }}</small>
                            </div>
                            <span class="ka-chip">{{ $unassignedOrders->count() }}</span>
                        </div>

                        <div class="ka-order-list">
                            @forelse ($unassignedOrders as $order)
                                <article class="ka-order" draggable="true" data-order-id="{{ $order->id }}">
                                    <div class="fw-700 ka-order-title">{{ $order->guest_name ?: __('Guest') }} · {{ $order->room?->name ?: __('Room') }}</div>
                                    <small>{{ $order->public_reference ?: ('#'.$order->id) }} · {{ $order->statusEnum()->label() }}</small>
                                    <small>{{ $order->items->map(fn ($item) => $item->item_name.' x '.$item->quantity)->implode(', ') }}</small>
                                </article>
                            @empty
                                <div class="ka-empty">{{ __('No unassigned orders right now.') }}</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="ka-staff-lanes">
                    <div class="ka-guide">
                        <div class="fw-700">{{ __('Staff drop lanes') }}</div>
                        <div class="text-13 k-muted mt-5">{{ __('Drop any queue card into one of the staff cards below to assign responsibility.') }}</div>
                    </div>

                    <div class="ka-lane-grid">
                        @forelse ($staffRows as $row)
                            <div class="ka-lane" data-staff="{{ $row['user']->id }}">
                                <div class="ka-lane-top">
                                    <div>
                                        <div class="fw-700">{{ $row['user']->name }}</div>
                                        <small>{{ $row['user']->role?->name ?: __('Kitchen staff') }}</small>
                                    </div>
                                    <span class="ka-chip">{{ $row['active'] }} {{ __('active') }}</span>
                                </div>

                                <div class="ka-mini-metrics">
                                    <span>{{ __('Completed') }} <strong>{{ $row['completed'] }}</strong></span>
                                    <span>{{ __('Performance') }} <strong>{{ $row['performance'] }}%</strong></span>
                                </div>

                                @forelse ($row['orders'] as $order)
                                    <article class="ka-order" draggable="true" data-order-id="{{ $order->id }}">
                                        <div class="fw-700 ka-order-title">{{ $order->guest_name ?: __('Guest') }} · {{ $order->room?->name ?: __('Room') }}</div>
                                        <small>{{ $order->public_reference ?: ('#'.$order->id) }} · {{ $order->statusEnum()->label() }}</small>
                                        <small>{{ $order->items->map(fn ($item) => $item->item_name.' x '.$item->quantity)->implode(', ') }}</small>
                                    </article>
                                @empty
                                    <div class="ka-empty">{{ __('Drop orders here for this staff member.') }}</div>
                                @endforelse
                            </div>
                        @empty
                            <div class="ka-guide">
                                <div class="fw-700">{{ __('No staff lanes available') }}</div>
                                <div class="text-13 k-muted mt-5">{{ __('Create kitchen staff first, then their assignment lanes will appear here automatically.') }}</div>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>

    <form id="assignOrderForm" method="POST" style="display:none;">
        @csrf
        <input type="hidden" name="assigned_to_user_id" id="assignOrderTarget">
    </form>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let draggedOrderId = null;
            const form = document.getElementById('assignOrderForm');
            const target = document.getElementById('assignOrderTarget');

            document.querySelectorAll('.ka-order').forEach(function (card) {
                card.addEventListener('dragstart', function () {
                    draggedOrderId = card.dataset.orderId;
                });
            });

            document.querySelectorAll('.ka-section[data-staff], .ka-lane[data-staff]').forEach(function (column) {
                column.addEventListener('dragover', function (event) {
                    event.preventDefault();
                    column.classList.add('is-drop');
                });
                column.addEventListener('dragleave', function () {
                    column.classList.remove('is-drop');
                });
                column.addEventListener('drop', function (event) {
                    event.preventDefault();
                    column.classList.remove('is-drop');
                    if (!draggedOrderId || !form || !target) return;
                    target.value = column.dataset.staff || '';
                    form.action = '{{ url('/kitchen/assignments/orders') }}/' + draggedOrderId;
                    form.submit();
                });
            });
        });
    </script>
@endsection
