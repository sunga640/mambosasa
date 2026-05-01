@extends('layouts.reception')

@section('title', __('Reception dashboard'))

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
        .reception-kpi-value {
            color: #ffffff !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, .22);
        }
    </style>
    <div style="display:flex;flex-wrap:wrap;align-items:baseline;justify-content:space-between;gap:.75rem;margin:0 0 .85rem;">
        <div>
            <h1 class="text-24" style="margin:0;line-height:1.2;">{{ __('Reception dashboard') }}</h1>
            <p class="text-13" style="opacity:.8;margin:.2rem 0 0;max-width:40rem;line-height:1.45;">
                {{ __('Overview for your branch: bookings, revenue, rooms, and maintenance.') }}
            </p>
        </div>
    </div>
    @include('partials.dashboard-month-calendar')
    @isset($branchSummary)
    <div class="mb-20" style="display:inline-flex;align-items:center;gap:.9rem;padding:.8rem .95rem;border:1px solid #1e3a8a;border-radius:0;background:linear-gradient(135deg,#0f172a 0%,#1d4ed8 100%);box-shadow:0 14px 28px rgba(15,23,42,.2);">
        <div>
            <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#bfdbfe;font-weight:700;">{{ __('Branches') }}</div>
            <div class="text-14" style="margin:.3rem 0 0;display:flex;gap:.75rem;flex-wrap:wrap;">
                <span style="color:#86efac;font-weight:700;">{{ __('Active') }}: {{ number_format($branchSummary['active']) }}</span>
                <span style="color:#fcd34d;font-weight:700;">{{ __('Inactive') }}: {{ number_format($branchSummary['inactive']) }}</span>
            </div>
        </div>
    </div>
    @endisset

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:1rem;">
    <!-- REVENUE TODAY -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #bfdbfe;background:#eff6ff;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#1e40af;font-weight:600;">{{ __('Revenue (Today)') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['revenue_today'], 0) }}</div>
    </div>

    <!-- REVENUE THIS MONTH -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #bbf7d0;background:#f0fdf4;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#15803d;font-weight:600;">{{ __('Revenue (Month)') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['revenue_month'], 0) }}</div>
    </div>

    <!-- REVENUE THIS YEAR -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #e5e5e5;background:#f8fafc;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#475569;font-weight:600;">{{ __('Revenue (Year)') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['revenue_year'], 0) }}</div>
    </div>

    <!-- BOOKINGS -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #e5e5e5;background:#fff;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:600;">{{ __('Bookings') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['bookings_total']) }}</div>
    </div>

    <!-- PENDING PAY -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #fde68a;background:#fffbeb;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#92400e;font-weight:600;">{{ __('Pending pay') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['bookings_pending']) }}</div>
    </div>

    <!-- CONFIRMED -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #bbf7d0;background:#f0fdf4;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#166534;font-weight:600;">{{ __('Confirmed') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['bookings_confirmed']) }}</div>
    </div>

    <!-- REVENUE CONFIRMED (TOTAL) -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #e5e5e5;background:#fff;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:600;">{{ __('Total Revenue') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['revenue_confirmed'], 0) }}</div>
    </div>

    <!-- CASH REVENUE -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #fef3c7;background:#fffbeb;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#92400e;font-weight:600;">{{ __('Cash revenue') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['revenue_cash'], 0) }}</div>
    </div>

    <!-- CUSTOMERS -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #e5e5e5;background:#fff;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:600;">{{ __('Customers') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['customers']) }}</div>
    </div>

    <!-- ROOMS -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #e5e5e5;background:#fff;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#64748b;font-weight:600;">{{ __('Rooms') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['rooms']) }}</div>
    </div>

    <!-- MAINTENANCE -->
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #fee2e2;background:#fef2f2;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#991b1b;font-weight:600;">{{ __('Maintenance (active)') }}</div>
        <div class="text-28 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['maintenance_open']) }}</div>
    </div>
    <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #fecdd3;background:#fff1f2;">
        <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#9f1239;font-weight:600;">{{ __('Expenses (Month)') }}</div>
        <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($kpis['expenses_month_total'] ?? 0, 0) }}</div>
    </div>
</div>

    @php($receptionPaymentTotal = $cashRevenue + $nonCashRevenue)

    <div class="mt-20" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:1rem;">
        <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #fef3c7;background:#fffbeb;">
            <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#92400e;font-weight:600;">{{ __('Cash payments') }}</div>
            <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($cashRevenue, 0) }}</div>
        </div>
        <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #dbeafe;background:#eff6ff;">
            <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#1d4ed8;font-weight:600;">{{ __('Other payments') }}</div>
            <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">{{ number_format($nonCashRevenue, 0) }}</div>
        </div>
        <div style="padding:1rem 1.1rem;border-radius:0;border:1px solid #dcfce7;background:#f0fdf4;">
            <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#15803d;font-weight:600;">{{ __('Cash share') }}</div>
            <div class="text-22 fw-600 reception-kpi-value" style="margin-top:.35rem;">
                {{ $receptionPaymentTotal > 0 ? number_format($cashRevenue / $receptionPaymentTotal * 100, 1) : '0.0' }}%
            </div>
        </div>
    </div>

    <div class="mt-20" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;padding:1rem 1.1rem;border:1px solid #1d4ed8;background:linear-gradient(135deg,#0f172a 0%,#1e3a8a 100%);">
        <div>
            <div class="text-12" style="text-transform:uppercase;letter-spacing:.06em;color:#bfdbfe;font-weight:700;">{{ __('Kitchen bills pending') }}</div>
            <div class="text-18 fw-600" style="margin-top:.25rem;color:#ffffff;">
                {{ number_format($kpis['kitchen_unpaid_bills'] ?? 0) }} {{ __('bills') }} · {{ number_format($kpis['kitchen_unpaid_amount'] ?? 0, 0) }} TZS
            </div>
        </div>
        <a href="{{ route('reception.room-service.index', ['payment' => 'unpaid', 'billed' => 1]) }}" class="dash-btn dash-btn--primary">
            {{ __('Open kitchen bills list') }}
        </a>
    </div>

    <div class="mt-25" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(380px,1fr));gap:1.25rem;align-items:stretch;">
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;min-height:420px;">
            <h2 class="text-18" style="margin:0 0 1rem;">{{ __('New bookings (14 days)') }}</h2>
            <div style="position:relative;height:330px;">
                <canvas id="receptionChartBookingsTrend" aria-label="{{ __('Bookings per day chart') }}"></canvas>
            </div>
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;min-height:420px;">
            <h2 class="text-18" style="margin:0 0 1rem;">{{ __('Confirmed revenue (14 days)') }}</h2>
            <div style="position:relative;height:330px;">
                <canvas id="receptionChartRevenueTrend" aria-label="{{ __('Revenue chart') }}"></canvas>
            </div>
        </div>
    </div>

    <div class="mt-25" style="display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:1.25rem;">
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Confirmed revenue by payment method') }}</h2>
            @if ($paymentByMethod->isEmpty())
                <p class="text-14" style="opacity:.75;">{{ __('No confirmed bookings in scope.') }}</p>
            @else
                <table class="admin-table" style="font-size:.9rem;">
                    <thead>
                        <tr>
                            <th>{{ __('Method') }}</th>
                            <th>{{ __('Amount') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($paymentByMethod as $row)
                            <tr>
                                <td>{{ $row->method_name }}</td>
                                <td>{{ number_format((float) $row->total, 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;grid-column:span 2;">
            <div style="display:flex;justify-content:space-between;gap:.75rem;flex-wrap:wrap;align-items:center;margin-bottom:.75rem;">
                <h2 class="text-18" style="margin:0;">{{ __('Payment analysis') }}</h2>
                <input id="receptionPaymentMonthFilter" type="month" value="{{ now()->format('Y-m') }}">
            </div>
            <div style="position:relative;height:320px;">
                <canvas id="receptionPaymentMethodChart"></canvas>
            </div>
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Revenue by branch') }}</h2>
            @if (empty($branchTotals))
                <p class="text-14" style="opacity:.75;">{{ __('No branch data.') }}</p>
            @else
                <table class="admin-table" style="font-size:.9rem;">
                    <thead>
                        <tr>
                            <th>{{ __('Branch') }}</th>
                            <th>{{ __('Bookings') }}</th>
                            <th>{{ __('Revenue') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branchTotals as $bt)
                            <tr>
                                <td>{{ $bt['branch'] }}</td>
                                <td>{{ number_format($bt['count']) }}</td>
                                <td>{{ number_format($bt['total'], 0) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:0;background:#fff;grid-column:span 2;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Expense analytics (30 days)') }}</h2>
            <div style="position:relative;height:320px;">
                <canvas id="receptionExpenseChart"></canvas>
            </div>
        </div>
    </div>

    <style>
        @media (max-width: 991px) {
            .dash-content-card > div[style*="repeat(auto-fit,minmax(300px,1fr))"] > div[style*="grid-column:span 2"] {
                grid-column: auto !important;
            }
            .dash-content-card > div[style*="repeat(3,minmax(0,1fr))"] {
                grid-template-columns: 1fr !important;
            }
            .dash-content-card > div[style*="repeat(3,minmax(0,1fr))"] > div[style*="grid-column:span 2"] {
                grid-column: auto !important;
            }
        }
    </style>

    <div class="mt-25">
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
            <p class="mt-15"><a href="{{ route('reception.contacts.index') }}">{{ __('View all contact messages') }}</a></p>
        @endif
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function () {
  if (typeof Chart === 'undefined') return;
  var styles = getComputedStyle(document.documentElement);
  var tickColor = (styles.getPropertyValue('--brand-theme-text') || '#d9e1ea').trim();
  var gridColor = 'rgba(213,172,66,0.16)';

  var receptionBookingsTrendEl = document.getElementById('receptionChartBookingsTrend');
  if (receptionBookingsTrendEl) new Chart(receptionBookingsTrendEl, {
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

  var receptionRevenueTrendEl = document.getElementById('receptionChartRevenueTrend');
  if (receptionRevenueTrendEl) new Chart(receptionRevenueTrendEl, {
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

  var receptionPaymentLabels = @json($paymentSeriesLabels);
  var receptionPaymentSeries = @json($paymentSeriesDataset);
  var paymentColors = ['#2563eb', '#16a34a', '#ea580c', '#7c3aed', '#db2777', '#0f766e'];
  var receptionPaymentCanvas = document.getElementById('receptionPaymentMethodChart');
  if (!receptionPaymentCanvas) return;
  var receptionPaymentChart = new Chart(receptionPaymentCanvas, {
    type: 'line',
    data: { labels: [], datasets: [] },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      interaction: { mode: 'index', intersect: false },
      plugins: { legend: { position: 'bottom', labels: { color: tickColor } } },
      scales: {
        x: { ticks: { color: tickColor, maxRotation: 45 }, grid: { display: false } },
        y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }
      }
    }
  });
  var renderReceptionPaymentChart = function (monthValue) {
    var rows = [];
    receptionPaymentLabels.forEach(function (label, idx) {
      if (!monthValue || label.slice(0, 7) === monthValue) rows.push({ label: label, idx: idx });
    });
    receptionPaymentChart.data.labels = rows.map(function (r) { return r.label; });
    receptionPaymentChart.data.datasets = receptionPaymentSeries.map(function (s, i) {
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
    receptionPaymentChart.update();
  };
  var monthFilter = document.getElementById('receptionPaymentMonthFilter');
  if (monthFilter) {
    renderReceptionPaymentChart(monthFilter.value);
    monthFilter.addEventListener('change', function () { renderReceptionPaymentChart(monthFilter.value); });
  } else {
    renderReceptionPaymentChart('');
  }

  var receptionExpenseCanvas = document.getElementById('receptionExpenseChart');
  if (receptionExpenseCanvas) new Chart(receptionExpenseCanvas, {
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
    },
  });
})();
</script>
@endpush
