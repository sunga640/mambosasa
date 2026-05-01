<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('System Reports & Analytics') }} PDF</title>
    <style>
        body { margin:0; background:#0f172a; color:#e2e8f0; font-family:Manrope, Arial, sans-serif; }
        .shell { max-width:1200px; margin:0 auto; padding:24px; }
        .card { background:#1e293b; border:1px solid rgba(125, 211, 252, .18); border-radius:16px; padding:20px; margin-bottom:18px; }
        .hero { display:flex; justify-content:space-between; gap:18px; align-items:flex-end; }
        .hero h1 { margin:0; color:#7dd3fc; font-size:30px; }
        .hero p { margin:6px 0 0; color:#94a3b8; }
        .kpis { display:grid; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); gap:14px; margin-top:18px; }
        .kpi { background:#243042; border:1px solid rgba(125, 211, 252, .14); border-radius:14px; padding:14px; }
        .kpi strong { display:block; font-size:24px; color:#f8fafc; }
        .kpi span { display:block; margin-top:6px; color:#94a3b8; font-size:13px; text-transform:uppercase; letter-spacing:.06em; }
        .grid-two { display:grid; grid-template-columns:2fr 1fr; gap:18px; }
        .grid-three { display:grid; grid-template-columns:repeat(3, 1fr); gap:18px; }
        .chart { height:320px; }
        .chart-sm { height:250px; }
        canvas { width:100% !important; height:100% !important; }
        @media print {
            body { background:#fff; color:#111827; }
            .card, .kpi { background:#fff; border-color:#cbd5e1; box-shadow:none; }
            .hero h1 { color:#0f172a; }
            .hero p, .kpi span { color:#475569; }
            .kpi strong { color:#111827; }
        }
    </style>
</head>
<body>
    <div class="shell">
        <section class="card">
            <div class="hero">
                <div>
                    <h1>{{ __('System Reports & Analytics') }}</h1>
                    <p>{{ $from->toDateString() }} - {{ $to->toDateString() }}</p>
                </div>
                <div style="color:#94a3b8;font-size:13px;">{{ __('Print or Save as PDF after charts finish loading.') }}</div>
            </div>
            <div class="kpis">
                <div class="kpi"><strong>TZS {{ number_format($revenueThisMonth, 0) }}</strong><span>{{ __('Revenue Growth (MoM)') }}</span></div>
                <div class="kpi"><strong>TZS {{ number_format($revenueToday ?? 0, 0) }}</strong><span>{{ __('Revenue Today') }}</span></div>
                <div class="kpi"><strong>TZS {{ number_format($revenueYear ?? 0, 0) }}</strong><span>{{ __('Total Year Revenue') }}</span></div>
            </div>
        </section>

        <section class="grid-two">
            <div class="card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Revenue Trend (Current Filter)') }}</h2>
                <div class="chart"><canvas id="chartRevenueTrend"></canvas></div>
            </div>
            <div class="card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Payment Distribution') }}</h2>
                <div class="chart-sm"><canvas id="chartCashPie"></canvas></div>
            </div>
        </section>

        <section class="grid-three">
            <div class="card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Occupancy (Booked vs Available)') }}</h2>
                <div class="chart-sm"><canvas id="chartBookingAvailability"></canvas></div>
            </div>
            <div class="card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Maintenance Status Tasks') }}</h2>
                <div class="chart-sm"><canvas id="chartMaintenanceStatus"></canvas></div>
            </div>
            <div class="card">
                <h2 style="margin:0 0 14px; color:#7dd3fc;">{{ __('Rooms Distribution per Branch') }}</h2>
                <div class="chart-sm"><canvas id="chartRoomDist"></canvas></div>
            </div>
        </section>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" crossorigin="anonymous"></script>
    <script>
        window.addEventListener('load', function () {
            if (typeof Chart === 'undefined') return;
            Chart.defaults.color = '#cbd5e1';
            Chart.defaults.borderColor = 'rgba(148, 163, 184, 0.18)';

            new Chart(document.getElementById('chartRevenueTrend'), {
                type: 'line',
                data: {
                    labels: @json($revenueTrend->pluck('date')),
                    datasets: [{
                        label: 'Daily Revenue',
                        data: @json($revenueTrend->pluck('total')),
                        borderColor: '#38bdf8',
                        backgroundColor: 'rgba(56, 189, 248, 0.14)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            new Chart(document.getElementById('chartCashPie'), {
                type: 'doughnut',
                data: {
                    labels: ['Cash', 'Digital'],
                    datasets: [{ data: [@json($cashRevenue), @json($nonCashRevenue)], backgroundColor: ['#f59e0b', '#38bdf8'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(document.getElementById('chartBookingAvailability'), {
                type: 'doughnut',
                data: {
                    labels: ['Available', 'Booked'],
                    datasets: [{ data: [@json($availableRoomsCount), @json($bookedRoomsCount)], backgroundColor: ['#22c55e', '#ef4444'] }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
            });

            new Chart(document.getElementById('chartMaintenanceStatus'), {
                type: 'bar',
                data: {
                    labels: @json($maintenanceStats->pluck('status')),
                    datasets: [{ label: 'Total Tasks', data: @json($maintenanceStats->pluck('total')), backgroundColor: '#38bdf8', borderRadius: 6 }]
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
            });

            new Chart(document.getElementById('chartRoomDist'), {
                type: 'pie',
                data: {
                    labels: @json($roomDistribution->pluck('branch_name')),
                    datasets: [{ data: @json($roomDistribution->pluck('total')), backgroundColor: ['#38bdf8', '#8b5cf6', '#ec4899', '#f97316', '#22c55e', '#f59e0b'] }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } },
                    animation: { onComplete: function () { setTimeout(function () { window.print(); }, 400); } }
                }
            });
        });
    </script>
</body>
</html>
