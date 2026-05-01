@extends('layouts.admin')

@section('title', $title)

@section('content')
    <div style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;justify-content:space-between;margin-bottom:1.25rem;">
        <div>
            <h1 class="text-30" style="margin:0;">{{ $title }}</h1>
            <p class="text-14 mt-10" style="opacity:.8;margin:0;">{{ __('Adjust the period and filters, then export CSV.') }}</p>
        </div>
        <div style="display:flex;flex-wrap:wrap;gap:.5rem;">
            <a href="{{ route('admin.reports.index') }}" class="dash-btn dash-btn--ghost">{{ __('All reports') }}</a>
            <a href="{{ $exportUrl }}" class="dash-btn dash-btn--primary">{{ __('Export CSV') }}</a>
            <a href="{{ route('admin.reports.export', array_filter(array_merge(request()->query(), ['type' => $reportType, 'format' => 'pdf', 'status' => $statusFilter]), fn ($value) => $value !== null && $value !== '')) }}" target="_blank" class="dash-btn dash-btn--ghost">{{ __('Export PDF') }}</a>
        </div>
    </div>

    @if ($reportType === 'full')
        <div class="report-hub-grid">
            @foreach ([
                'summary' => __('Summary dashboard'),
                'bookings' => __('Bookings'),
                'customers' => __('Customers'),
                'rooms' => __('Rooms'),
                'maintenance' => __('Maintenance'),
            ] as $slug => $label)
                <a href="{{ route('admin.reports.show', ['type' => $slug, 'from' => $from->toDateString(), 'to' => $to->toDateString()]) }}" class="report-hub-card">
                    <span class="report-hub-card__title">{{ $label }}</span>
                    <span class="report-hub-card__meta">{{ __('Open dashboard') }}</span>
                </a>
            @endforeach
        </div>
        <p class="text-15 mt-25">{{ __('Full CSV merges bookings, customers, rooms, and maintenance. Date filters apply to bookings, customers, and maintenance sections.') }}</p>
    @else
        <form method="GET" action="{{ route('admin.reports.show', ['type' => $reportType]) }}" class="report-filters">
            <div class="report-filters__row">
                <label>
                    <span class="report-filters__lab">{{ __('From') }}</span>
                    <input type="date" name="from" value="{{ $from->toDateString() }}">
                </label>
                <label>
                    <span class="report-filters__lab">{{ __('To') }}</span>
                    <input type="date" name="to" value="{{ $to->toDateString() }}">
                </label>
                @if ($reportType === 'bookings')
                    <label>
                        <span class="report-filters__lab">{{ __('Status') }}</span>
                        <select name="status">
                            <option value="">{{ __('All statuses') }}</option>
                            @foreach ($bookingStatuses as $st)
                                <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>{{ $st->value }}</option>
                            @endforeach
                        </select>
                    </label>
                @endif
                <button type="submit" class="dash-btn dash-btn--primary">{{ __('Apply') }}</button>
            </div>
        </form>

        <div class="report-kpi-grid">
            @foreach ($kpis as $kpi)
                <div class="report-kpi">
                    <div class="report-kpi__val">{{ $kpi['value'] }}</div>
                    <div class="report-kpi__lab">{{ $kpi['label'] }}</div>
                </div>
            @endforeach
        </div>

        @if (!empty($chartLabels) && count($chartLabels))
            <div class="report-chart-wrap">
                <h2 class="text-18" style="margin:0 0 .5rem;">{{ $chartLabel }}</h2>
                <div class="report-chart-canvas report-chart-canvas--primary">
                    <canvas id="reportPrimaryChart" aria-hidden="true"></canvas>
                </div>
            </div>
        @endif

        @isset($secondaryChart)
            @if (!empty($secondaryChart['labels']))
                <div class="report-chart-wrap">
                    <h2 class="text-18" style="margin:0 0 .5rem;">{{ $secondaryChart['label'] }}</h2>
                    <div class="report-chart-canvas report-chart-canvas--doughnut">
                        <canvas id="reportSecondaryChart" aria-hidden="true"></canvas>
                    </div>
                </div>
            @endif
        @endisset

        @if ($tableRows->isNotEmpty())
            <h2 class="text-18 mt-25">{{ __('Preview') }}</h2>
            <div style="overflow-x:auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            @foreach ($tableHeaders as $h)
                                <th>{{ $h }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tableRows as $row)
                            <tr>
                                @foreach ($row as $cell)
                                    <td>{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    @endif
@endsection

@push('scripts')
    @php
        $needsChart = $reportType !== 'full' && !empty($chartLabels) && count($chartLabels);
        $needsSecondary = isset($secondaryChart) && !empty($secondaryChart['labels']);
    @endphp
    @if ($needsChart || $needsSecondary)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                if (typeof Chart === 'undefined') return;
                @if ($needsChart)
                (function () {
                    var labels = @json($chartLabels);
                    var data = @json($chartData);
                    var el = document.getElementById('reportPrimaryChart');
                    if (!el) return;
                    var isBar = @json($reportType === 'rooms' || $reportType === 'maintenance');
                    new Chart(el, {
                        type: isBar ? 'bar' : 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: @json($chartLabel),
                                data: data,
                                borderColor: '#c41e3a',
                                backgroundColor: isBar ? 'rgba(196, 30, 58, 0.55)' : 'rgba(196, 30, 58, 0.15)',
                                fill: !isBar,
                                tension: isBar ? 0 : 0.25
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true }, x: { ticks: { maxRotation: 45, minRotation: 0, autoSkip: true, maxTicksLimit: 12 } } }
                        }
                    });
                })();
                @endif
                @if ($needsSecondary)
                (function () {
                    var labels = @json($secondaryChart['labels']);
                    var data = @json($secondaryChart['values']);
                    var el = document.getElementById('reportSecondaryChart');
                    if (!el) return;
                    new Chart(el, {
                        type: 'doughnut',
                        data: {
                            labels: labels,
                            datasets: [{
                                data: data,
                                backgroundColor: ['#c41e3a', '#111827', '#6b7280', '#9ca3af', '#d1d5db', '#e5e7eb']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: { boxWidth: 10, padding: 8, font: { size: 11 } }
                                }
                            }
                        }
                    });
                })();
                @endif
            });
        </script>
    @endif
@endpush
