<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} PDF</title>
    <style>
        body { margin: 0; background: #0f172a; color: #e2e8f0; font-family: Manrope, Arial, sans-serif; }
        .print-shell { max-width: 1120px; margin: 0 auto; padding: 24px; }
        .print-card { background: #1e293b; border: 1px solid rgba(125, 211, 252, .18); border-radius: 16px; padding: 20px; margin-bottom: 18px; }
        .print-title { display:flex; justify-content:space-between; gap:16px; align-items:flex-end; margin-bottom:18px; }
        .print-title h1 { margin:0; color:#7dd3fc; font-size:30px; }
        .print-title p { margin:6px 0 0; color:#94a3b8; }
        .print-kpis { display:grid; grid-template-columns:repeat(auto-fit, minmax(180px, 1fr)); gap:14px; }
        .print-kpi { background:#243042; border:1px solid rgba(125, 211, 252, .14); border-radius:14px; padding:14px; }
        .print-kpi strong { display:block; font-size:24px; color:#f8fafc; }
        .print-kpi span { display:block; margin-top:6px; color:#94a3b8; font-size:13px; text-transform:uppercase; letter-spacing:.06em; }
        .print-grid { display:grid; grid-template-columns:2fr 1fr; gap:18px; }
        .print-chart-wrap { height: 340px; }
        .print-chart-wrap--small { height: 280px; }
        .print-chart-wrap canvas { width:100% !important; height:100% !important; }
        table { width:100%; border-collapse:collapse; margin-top:8px; }
        th, td { padding:10px 12px; border-bottom:1px solid rgba(148, 163, 184, .18); text-align:left; font-size:13px; }
        th { color:#7dd3fc; }
        td { color:#e2e8f0; }
        .print-note { color:#94a3b8; font-size:13px; }
        @media print {
            body { background:#fff; color:#111827; }
            .print-card, .print-kpi { background:#fff; border-color:#cbd5e1; box-shadow:none; }
            .print-title h1, th { color:#0f172a; }
            .print-title p, .print-note, .print-kpi span { color:#475569; }
            td, .print-kpi strong { color:#111827; }
        }
    </style>
</head>
<body>
    <div class="print-shell">
        <section class="print-card">
            <div class="print-title">
                <div>
                    <h1>{{ $title }}</h1>
                    <p>{{ $from->toDateString() }} - {{ $to->toDateString() }}</p>
                </div>
                <div class="print-note">{{ __('Print or Save as PDF after charts finish loading.') }}</div>
            </div>

            <div class="print-kpis">
                @foreach ($kpis as $kpi)
                    <div class="print-kpi">
                        <strong>{{ $kpi['value'] }}</strong>
                        <span>{{ $kpi['label'] }}</span>
                    </div>
                @endforeach
            </div>
        </section>

        @if (!empty($chartLabels) && count($chartLabels))
            <section class="print-card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ $chartLabel }}</h2>
                <div class="print-chart-wrap">
                    <canvas id="reportPrimaryChart"></canvas>
                </div>
            </section>
        @endif

        @isset($secondaryChart)
            @if (!empty($secondaryChart['labels']))
                <section class="print-card">
                    <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ $secondaryChart['label'] }}</h2>
                    <div class="print-chart-wrap print-chart-wrap--small">
                        <canvas id="reportSecondaryChart"></canvas>
                    </div>
                </section>
            @endif
        @endisset

        @if ($tableRows->isNotEmpty())
            <section class="print-card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Preview') }}</h2>
                <table>
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
            </section>
        @endif
    </div>

    @php
        $needsChart = $reportType !== 'full' && !empty($chartLabels) && count($chartLabels);
        $needsSecondary = isset($secondaryChart) && !empty($secondaryChart['labels']);
    @endphp
    @if ($needsChart || $needsSecondary)
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
        <script>
            window.addEventListener('load', function () {
                if (typeof Chart === 'undefined') return;
                Chart.defaults.color = '#cbd5e1';
                Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.18)';
                @if ($needsChart)
                (function () {
                    var el = document.getElementById('reportPrimaryChart');
                    if (!el) return;
                    var isBar = @json($reportType === 'rooms' || $reportType === 'maintenance');
                    new Chart(el, {
                        type: isBar ? 'bar' : 'line',
                        data: {
                            labels: @json($chartLabels),
                            datasets: [{
                                label: @json($chartLabel),
                                data: @json($chartData),
                                borderColor: '#38bdf8',
                                backgroundColor: isBar ? 'rgba(56, 189, 248, 0.65)' : 'rgba(56, 189, 248, 0.14)',
                                fill: !isBar,
                                tension: isBar ? 0 : 0.28,
                                borderRadius: isBar ? 6 : 0
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: { y: { beginAtZero: true }, x: { ticks: { maxRotation: 45, minRotation: 0, autoSkip: true, maxTicksLimit: 12 } } },
                            animation: {
                                onComplete: function () { setTimeout(function () { window.print(); }, 300); }
                            }
                        }
                    });
                })();
                @endif
                @if ($needsSecondary)
                (function () {
                    var el = document.getElementById('reportSecondaryChart');
                    if (!el) return;
                    new Chart(el, {
                        type: 'doughnut',
                        data: {
                            labels: @json($secondaryChart['labels']),
                            datasets: [{
                                data: @json($secondaryChart['values']),
                                backgroundColor: ['#38bdf8', '#f59e0b', '#22c55e', '#ef4444', '#8b5cf6', '#94a3b8']
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'bottom' } }
                        }
                    });
                })();
                @elseif (!$needsChart)
                setTimeout(function () { window.print(); }, 250);
                @endif
            });
        </script>
    @else
        <script>
            window.addEventListener('load', function () {
                setTimeout(function () { window.print(); }, 250);
            });
        </script>
    @endif
</body>
</html>
