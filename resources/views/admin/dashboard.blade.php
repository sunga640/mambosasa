@extends('layouts.admin')

@section('title', __('Dashboard'))

@section('content')
    <style>
        .dash-content-card .dash-btn,
        .dash-content-card .button,
        .dash-content-card button {
            padding: .35rem .7rem !important;
            font-size: .84rem !important;
            font-weight: 500 !important;
            border-width: 1px !important;
        }
        .dash-content-card select,
        .dash-content-card input[type="month"] {
            min-height: 34px;
            font-size: .86rem;
        }
        .dashboard-compact-controls .dash-btn,
        .dashboard-compact-controls button,
        .dashboard-compact-controls a.dash-btn {
            padding: .35rem .7rem !important;
            font-size: .84rem !important;
            font-weight: 500 !important;
            border-width: 1px !important;
        }
        .dashboard-compact-controls select,
        .dashboard-compact-controls input[type="month"] {
            height: 34px;
        }
    </style>
    <div style="display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:.75rem;margin:0 0 .85rem;">
        <div>
            <h1 class="text-24" style="margin:0;line-height:1.2;">{{ __('Admin dashboard') }}</h1>
            <p class="text-13" style="opacity:.8;margin:.2rem 0 0;max-width:40rem;line-height:1.45;">
                {{ __('Overview of bookings, revenue, and key system metrics.') }}
            </p>
        </div>
    </div>
    @include('partials.dashboard-month-calendar')
    @if ($notificationUnreadCount > 0 || $recentDashboardNotifications->isNotEmpty())
    <div class="mb-25" style="padding:1.25rem;border:1px solid #fde68a;border-radius:12px;background:#fffbeb;">
        <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:.75rem;">
            <h2 class="text-18" style="margin:0;">{{ __('Notifications') }}</h2>
            <a href="{{ route('admin.notifications.index') }}" class="dash-btn dash-btn--ghost">{{ __('Manage all') }}</a>
        </div>
        @if ($notificationUnreadCount > 0)
            <p class="text-14 mt-10" style="margin:0;color:#92400e;">{{ __('You have :n unread notification(s).', ['n' => $notificationUnreadCount]) }}</p>
        @endif
        <ul class="text-14 mt-15" style="list-style:none;padding:0;margin:0;">
            @foreach ($recentDashboardNotifications as $n)
                <li style="padding:.5rem 0;border-bottom:1px solid #fcd34d;">
                    <span style="opacity:.7;">{{ $n->created_at?->format('Y-m-d H:i') }}</span>
                    · <strong>{{ $n->title }}</strong>
                    @if ($n->booking)
                        · <a href="{{ route('admin.bookings.show', $n->booking) }}">{{ $n->booking->public_reference }}</a>
                    @endif
                </li>
            @endforeach
        </ul>
    </div>
    @endif


<!-- BRANCH SUMMARY (TOP BAR) -->
@isset($branchSummary)
    <div class="mb-25" style="padding:1.25rem 1.5rem; border:1px solid #e2e8f0; border-radius:12px; background:linear-gradient(135deg,#f8fafc 0%,#fff 100%); display:flex; flex-wrap:wrap; gap:1.5rem; justify-content:space-between; align-items:center; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <div>
            <div style="font-size:12px; text-transform:uppercase; letter-spacing:.08em; color:#64748b; font-weight:700;">{{ __('Hotel Branches') }}</div>
            <p style="margin:.5rem 0 0; font-size:16px;">
                <span style="color:#15803d; font-weight:700;">● {{ __('Active') }}: {{ number_format($branchSummary['active']) }}</span>
                <span style="margin:0 .75rem; opacity:.3;">|</span>
                <span style="color:#b45309; font-weight:700;">○ {{ __('Inactive') }}: {{ number_format($branchSummary['inactive']) }}</span>
            </p>
        </div>
        <a href="{{ route('admin.payments.pending') }}" class="dash-btn dash-btn--primary" style="text-decoration:none; padding: 10px 20px; font-size: 14px; border-radius: 8px;">{{ __('Review Pending Payments') }}</a>
    </div>
@endisset

<!-- MAIN KPI GRID (ONGEZEKO LA UKUBWA) -->
<div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(200px, 1fr)); gap:1.25rem; margin-bottom: 2.5rem;">

    <!-- REVENUE TODAY -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #bfdbfe; background:#eff6ff; box-shadow: 0 2px 4px rgba(37,99,235,0.05);">
        <div style="font-size:11px; text-transform:uppercase; color:#1e40af; font-weight:700; letter-spacing:0.05em;">{{ __('Revenue (Today)') }}</div>
        <div style="font-size:24px; font-weight:800; color:#1e3a8a; margin-top:8px;">{{ number_format($kpis['revenue_today'] ?? 0, 0) }}</div>
    </div>
<!-- REVENUE MONTH -->
<div style="padding:1.25rem; border-radius:12px; border:1px solid #bbf7d0; background:#f0fdf4;">
    <div style="font-size:11px; text-transform:uppercase; color:#15803d; font-weight:700; letter-spacing:0.05em;">{{ __('Revenue (Month)') }}</div>
    <!-- Namba 0 hapa chini inaondoa decimal zote -->
    <div style="font-size:24px; font-weight:800; color:#14532d; margin-top:8px;">{{ number_format($kpis['revenue_month'] ?? 0, 0) }}</div>
</div>

    <!-- EXPENSES -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #fecaca; background:#fff1f2;">
        <div style="font-size:11px; text-transform:uppercase; color:#b91c1c; font-weight:700; letter-spacing:0.05em;">{{ __('Expenses (Month)') }}</div>
        <div style="font-size:24px; font-weight:800; color:#9f1239; margin-top:8px;">{{ number_format($kpis['expenses_month_total'] ?? 0, 0) }}</div>
    </div>

    <!-- NET PROFIT/LOSS (DYNAMIC) -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid {{ $kpis['profit_month'] >= 0 ? '#86efac' : '#fda4af' }}; background: {{ $kpis['profit_month'] >= 0 ? '#f0fdf4' : '#fff1f2' }}; border-width: 2px;">
        <div style="font-size:11px; text-transform:uppercase; color:{{ $kpis['profit_month'] >= 0 ? '#166534' : '#9f1239' }}; font-weight:800; letter-spacing:0.05em;">{{ __('Net Profit / Loss') }}</div>
        <div style="display:flex; align-items:baseline; gap:10px; margin-top:8px;">
            <div style="font-size:24px; font-weight:800; color:{{ $kpis['profit_month'] >= 0 ? '#14532d' : '#881337' }};">{{ number_format($kpis['profit_month'], 0) }}</div>
            <div style="font-size:14px; font-weight:700; color:{{ $kpis['profit_month'] >= 0 ? '#16a34a' : '#dc2626' }};">
                {{ $kpis['profit_month'] >= 0 ? '▲' : '▼' }} {{ number_format(abs($kpis['profit_percent']), 1) }}%
            </div>
        </div>
    </div>

    <!-- BOOKINGS -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #e2e8f0; background:#ffffff; box-shadow: 0 1px 2px rgba(0,0,0,0.05);">
        <div style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700;">{{ __('Total Bookings') }}</div>
        <div style="font-size:26px; font-weight:800; color:#0f172a; margin-top:5px;">{{ number_format($kpis['bookings_total']) }}</div>
    </div>

    <!-- CONFIRMED -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #bbf7d0; background:#f0fdf4;">
        <div style="font-size:11px; text-transform:uppercase; color:#166534; font-weight:700;">{{ __('Confirmed') }}</div>
        <div style="font-size:26px; font-weight:800; color:#14532d; margin-top:5px;">{{ number_format($kpis['bookings_confirmed']) }}</div>
    </div>

    <!-- PENDING -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #fde68a; background:#fffbeb;">
        <div style="font-size:11px; text-transform:uppercase; color:#92400e; font-weight:700;">{{ __('Pending Pay') }}</div>
        <div style="font-size:26px; font-weight:800; color:#78350f; margin-top:5px;">{{ number_format($kpis['bookings_pending']) }}</div>
    </div>

    <!-- ROOMS -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #e2e8f0; background:#f8fafc;">
        <div style="font-size:11px; text-transform:uppercase; color:#64748b; font-weight:700;">{{ __('Total Rooms') }}</div>
        <div style="font-size:26px; font-weight:800; color:#334155; margin-top:5px;">{{ number_format($kpis['rooms']) }}</div>
    </div>

    <!-- CONTACT MESSAGES -->
    <div style="padding:1.25rem; border-radius:12px; border:1px solid #ddd6fe; background:#f5f3ff;">
        <div style="font-size:11px; text-transform:uppercase; color:#5b21b6; font-weight:700;">{{ __('Contact Messages') }}</div>
        <div style="font-size:26px; font-weight:800; color:#4c1d95; margin-top:5px;">{{ number_format($kpis['contact_messages']) }}</div>
    </div>

</div>

<!-- CHARTS SECTION (Ipande juu zaidi sasa) -->
<div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(300px, 1fr)); gap:1rem;">
    <div style="padding:1rem; border:1px solid #e5e5e5; border-radius:10px; background:#fff; height:280px;">
        <h2 style="font-size:15px; margin:0 0 0.75rem; color:#334155;">{{ __('Bookings trend (14 days)') }}</h2>
        <div style="position:relative; height:200px;">
            <canvas id="chartBookingsTrend"></canvas>
        </div>
    </div>
    <div style="padding:1rem; border:1px solid #e5e5e5; border-radius:10px; background:#fff; height:280px;">
        <h2 style="font-size:15px; margin:0 0 0.75rem; color:#334155;">{{ __('Bookings status') }}</h2>
        <div style="position:relative; height:200px;">
            <canvas id="chartBookingsStatus"></canvas>
        </div>
    </div>
    <div style="padding:1rem; border:1px solid #e5e5e5; border-radius:10px; background:#fff; height:280px;">
        <h2 style="font-size:15px; margin:0 0 0.75rem; color:#334155;">{{ __('Revenue trend (14 days)') }}</h2>
        <div style="position:relative; height:200px;">
            <canvas id="chartRevenueTrend"></canvas>
        </div>
    </div>
</div>

    <div class="mt-25" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:1.25rem;">
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:#fff;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Revenue by payment method (confirmed)') }}</h2>
            @if ($paymentByMethod->isEmpty())
                <p class="text-14" style="opacity:.75;">{{ __('No confirmed bookings yet.') }}</p>
            @else
                <table class="admin-table" style="font-size:.9rem;">
                    <thead>
                        <tr>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Bookings') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentByMethod as $row)
                            <tr>
                                <td>{{ $row->method_name }}</td>
                                <td>{{ number_format((int) $row->c) }}</td>
                                <td>{{ number_format((float) $row->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:#fff;">
            <div style="display:flex;justify-content:space-between;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:.75rem;">
                <h2 class="text-18" style="margin:0;">{{ __('Payment method analysis') }}</h2>
                <input id="adminPaymentMonthFilter" type="month" value="{{ now()->format('Y-m') }}">
            </div>
            <div style="position:relative;height:280px;">
                <canvas id="adminPaymentMethodChart"></canvas>
            </div>
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:linear-gradient(135deg,#fffbeb 0%,#fff 100%);">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Cash vs other payments') }}</h2>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Cash (reception)') }}: <strong>{{ number_format($cashRevenue, 0) }}</strong></p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Non-cash') }}: <strong>{{ number_format($nonCashRevenue, 0) }}</strong></p>
            @php($sum = $cashRevenue + $nonCashRevenue)
            @if ($sum > 0)
                <p class="text-13 mt-15" style="opacity:.75;">{{ __('Cash share') }}: {{ number_format($cashRevenue / $sum * 100, 1) }}%</p>
            @endif
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:#fff;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Expense analytics (30 days)') }}</h2>
            <div style="position:relative;height:280px;">
                <canvas id="adminExpenseChart"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-25">
        <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Recent system activity') }}</h2>
        @if ($recentActivity->isEmpty())
            <p class="text-14" style="opacity:.75;">{{ __('No activity logged yet.') }}</p>
        @else
            <ul class="text-14" style="list-style:none;padding:0;margin:0;max-height:280px;overflow-y:auto;">
                @foreach ($recentActivity as $log)
                    <li style="padding:.5rem 0;border-bottom:1px solid #eee;">
                        <span style="opacity:.65;">{{ $log->created_at?->format('Y-m-d H:i') }}</span>
                        · <code style="font-size:.85em;">{{ $log->action }}</code>
                        @if ($log->user)
                            · {{ $log->user->name }}
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    <div class="mt-25" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(300px,1fr));gap:1.25rem;">
        <div>
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Quick links') }}</h2>
            <ul class="text-15" style="line-height:2;margin:0;padding-left:1.2rem;">
                <li><a href="{{ route('admin.bookings.index') }}">{{ __('Manage bookings') }}</a></li>
                <li><a href="{{ route('admin.customers.index') }}">{{ __('Customers') }}</a></li>
                <li><a href="{{ route('admin.maintenance.index') }}">{{ __('Maintenance') }}</a></li>
                <li><a href="{{ route('admin.reports.index') }}">{{ __('Reports') }}</a></li>
                <li><a href="{{ route('admin.settings.edit') }}">{{ __('System settings') }}</a></li>
                <li><a href="{{ route('admin.branches.index') }}">{{ __('Hotel branches') }}</a></li>
                <li><a href="{{ route('admin.rooms.index') }}">{{ __('Rooms') }}</a></li>
                <li><a href="{{ route('admin.users.index') }}">{{ __('Users') }}</a></li>
                <li><a href="{{ route('admin.contacts.index') }}">{{ __('Contact messages') }}</a></li>
            </ul>
        </div>
        <div>
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Recent contact messages') }}</h2>
            @if ($recentContacts->isEmpty())
                <p class="text-14" style="opacity:.75;">{{ __('No messages yet.') }}</p>
            @else
                <ul class="text-14" style="list-style:none;padding:0;margin:0;">
                    @foreach ($recentContacts as $msg)
                        <li style="padding:.65rem 0;border-bottom:1px solid #eee;">
                            <strong>{{ $msg->first_name }} {{ $msg->last_name }}</strong>
                            <span style="opacity:.7;"> — {{ $msg->email }}</span>
                            <div class="text-13 mt-5" style="opacity:.65;">{{ $msg->created_at?->diffForHumans() }}</div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function () {
  if (typeof Chart === 'undefined') return;
  var tickColor = '#64748b';
  var gridColor = 'rgba(148,163,184,0.2)';

  var chartBookingsTrendEl = document.getElementById('chartBookingsTrend');
  if (chartBookingsTrendEl) new Chart(chartBookingsTrendEl, {
    type: 'line',
    data: {
      labels: @json($chartTrendLabels),
      datasets: [{
        label: @json(__('Bookings')),
        data: @json($chartTrendData),
        borderColor: 'rgb(196, 30, 58)',
        backgroundColor: 'rgba(196, 30, 58, 0.12)',
        fill: true,
        tension: 0.35,
        pointRadius: 3,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: tickColor, maxRotation: 45 }, grid: { color: gridColor } },
        y: { beginAtZero: true, ticks: { color: tickColor, precision: 0 }, grid: { color: gridColor } },
      },
    },
  });

  var chartBookingsStatusEl = document.getElementById('chartBookingsStatus');
  if (chartBookingsStatusEl) new Chart(chartBookingsStatusEl, {
    type: 'doughnut',
    data: {
      labels: @json($chartStatusLabels),
      datasets: [{
        data: @json($chartStatusData),
        backgroundColor: @json($chartStatusColors),
        borderWidth: 2,
        borderColor: '#fff',
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { color: tickColor, boxWidth: 12 } } },
    },
  });

  var chartRevenueTrendEl = document.getElementById('chartRevenueTrend');
  if (chartRevenueTrendEl) new Chart(chartRevenueTrendEl, {
    type: 'bar',
    data: {
      labels: @json($chartRevenueLabels),
      datasets: [{
        label: @json(__('Amount')),
        data: @json($chartRevenueData),
        backgroundColor: 'rgba(34, 197, 94, 0.55)',
        borderColor: 'rgb(22, 163, 74)',
        borderWidth: 1,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: tickColor, maxRotation: 45 }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } },
      },
    },
  });

  var adminPaymentLabels = @json($paymentSeriesLabels);
  var adminPaymentSeries = @json($paymentSeriesDataset);
  var paymentColors = ['#2563eb', '#16a34a', '#ea580c', '#7c3aed', '#db2777', '#0f766e'];
  var adminPaymentCanvas = document.getElementById('adminPaymentMethodChart');
  if (!adminPaymentCanvas) return;
  var adminPaymentChart = new Chart(adminPaymentCanvas, {
    type: 'line',
    data: { labels: [], datasets: [] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom' } },
      scales: {
        x: { ticks: { color: tickColor, maxRotation: 45 }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }
      }
    }
  });
  var renderPaymentChart = function (monthValue) {
    var rows = [];
    adminPaymentLabels.forEach(function (label, idx) {
      if (!monthValue || label.slice(0, 7) === monthValue) rows.push({ label: label, idx: idx });
    });
    adminPaymentChart.data.labels = rows.map(function (r) { return r.label; });
    adminPaymentChart.data.datasets = adminPaymentSeries.map(function (s, i) {
      return {
        label: s.label,
        data: rows.map(function (r) { return s.data[r.idx] || 0; }),
        borderColor: paymentColors[i % paymentColors.length],
        backgroundColor: paymentColors[i % paymentColors.length] + '33',
        tension: 0.28,
        fill: false,
        pointRadius: 2,
      };
    });
    adminPaymentChart.update();
  };
  var monthFilter = document.getElementById('adminPaymentMonthFilter');
  if (monthFilter) {
    renderPaymentChart(monthFilter.value);
    monthFilter.addEventListener('change', function () { renderPaymentChart(monthFilter.value); });
  } else {
    renderPaymentChart('');
  }

  var expenseCanvas = document.getElementById('adminExpenseChart');
  if (expenseCanvas) new Chart(expenseCanvas, {
    type: 'bar',
    data: {
      labels: @json($expenseLabels),
      datasets: [{
        label: @json(__('Expenses')),
        data: @json($expenseSeries),
        backgroundColor: 'rgba(225,29,72,0.35)',
        borderColor: 'rgb(190,24,93)',
        borderWidth: 1,
      }],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: tickColor, maxRotation: 45 }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } },
      },
    }
  });
})();
</script>
@endpush
