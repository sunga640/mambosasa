@extends('layouts.admin')

@section('title', __('System Reports & Analytics'))

@section('content')
    <style>
        .reports-card {
            background: var(--brand-theme-surface, #2e333b);
            border: 1px solid rgba(213, 172, 66, 0.18);
            color: var(--brand-theme-text, #f5efe2);
        }
        .reports-kpi-accent {
            border-color: rgba(213, 172, 66, 0.55);
            box-shadow: inset 0 0 0 1px rgba(213, 172, 66, 0.08);
        }
        .reports-filter-shell {
            display:flex;
            gap:10px;
            background: var(--brand-theme-panel, #23262b);
            padding:8px;
            border-radius:12px;
            border:1px solid rgba(213, 172, 66, 0.2);
        }
    </style>
     <div style="display:flex; justify-content:space-between; align-items:flex-end; flex-wrap: wrap; gap: 1.5rem; margin-bottom:2rem;">
        <div>
            <h1 class="text-30" style="margin:0;">{{ __('System Reports & Analytics') }}</h1>
            <p class="text-15 mt-5" style="opacity:.75;">{{ __('Financial overview and revenue performance.') }}</p>
        </div>

        <div style="display:flex; flex-direction: column; align-items: flex-end; gap: 10px;">
            <!-- EXPORT OPTIONS -->
            <div style="display:flex; gap:8px;">
                <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'csv'])) }}"
                   class="dash-btn" style="background:rgba(245,239,226,0.05); border:1px solid rgba(213, 172, 66, 0.2); color:var(--brand-theme-text, #f5efe2); padding:6px 12px; font-size:13px; border-radius:8px; display:flex; align-items:center; gap:6px;">
                    <i class="fa fa-file-csv text-16"></i> {{ __('Export CSV') }}
                </a>

                {{-- Kumbuka: Export PDF inahitaji library kama DomPDF au Snappy kwenye server --}}
                <a href="{{ route('admin.reports.export', array_merge(request()->all(), ['format' => 'pdf', 'dashboard' => 'overview'])) }}" target="_blank"
                   class="dash-btn" style="background:rgba(245,239,226,0.05); border:1px solid rgba(213, 172, 66, 0.2); color:#ff9898; padding:6px 12px; font-size:13px; border-radius:8px; display:flex; align-items:center; gap:6px;">
                    <i class="fa fa-file-pdf text-16"></i> {{ __('Export PDF') }}
                </a>
            </div>

            <!-- FILTER SECTION -->
            <form method="GET" action="{{ route('admin.reports.index') }}" class="reports-filter-shell">
                <input type="date" name="from" value="{{ $from->format('Y-m-d') }}" style="border:1px solid rgba(213, 172, 66, 0.2); padding:5px 10px; border-radius:8px; font-size:13px;">
                <input type="date" name="to" value="{{ $to->format('Y-m-d') }}" style="border:1px solid rgba(213, 172, 66, 0.2); padding:5px 10px; border-radius:8px; font-size:13px;">
                <button type="submit" class="dash-btn dash-btn--primary" style="padding:7px 15px; font-size:13px;">{{ __('Filter') }}</button>
            </form>
        </div>
    </div>

    <!-- 1. REVENUE COMPARISON & KPI CARDS -->
    <div style="display:grid; grid-template-columns:repeat(auto-fit, minmax(280px, 1fr)); gap:1.25rem; margin-bottom:2.5rem;">

        <!-- Monthly Comparison Card -->
        <div class="reports-card reports-kpi-accent" style="padding:1.5rem; border-radius:16px; position:relative; overflow:hidden;">
            <div class="text-12" style="text-transform:uppercase; color:var(--brand-theme-muted, #b9bdc7); font-weight:600;">{{ __('Revenue Growth (MoM)') }}</div>
            <div style="display:flex; align-items:baseline; gap:10px; margin-top:10px;">
                <div class="text-24 fw-700">TZS {{ number_format($revenueThisMonth, 0) }}</div>
                <div class="text-14 fw-600" style="color: {{ $percentageChange >= 0 ? '#16a34a' : '#dc2626' }};">
                    <i class="fa {{ $percentageChange >= 0 ? 'fa-arrow-up' : 'fa-arrow-down' }}"></i>
                    {{ number_format(abs($percentageChange), 1) }}%
                </div>
            </div>
            <p class="text-13 mt-5" style="color:var(--brand-theme-muted, #b9bdc7);">{{ __('Vs Last Month:') }} TZS {{ number_format($revenueLastMonth, 0) }}</p>
        </div>

        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <div class="text-12" style="text-transform:uppercase; color:var(--brand-theme-muted, #b9bdc7); font-weight:600;">{{ __('Revenue Today') }}</div>
            <div class="text-24 fw-700" style="margin-top:10px;">TZS {{ number_format($revenueToday ?? 0, 0) }}</div>
            <div class="text-13 mt-5" style="color:#16a34a;">{{ __('Confirmed today') }}</div>
        </div>

        <div class="reports-card" style="padding:1.5rem; border-radius:16px; background:#1d2430; color:#fff;">
            <div class="text-12" style="text-transform:uppercase; opacity:.7; font-weight:600;">{{ __('Total Year Revenue') }}</div>
            <div class="text-24 fw-700" style="margin-top:10px;">TZS {{ number_format($revenueYear ?? 0, 0) }}</div>
            <div class="text-13 mt-5" style="opacity:.7;">{{ __('Confirmed performance') }}</div>
        </div>
    </div>

    <h2 class="text-20 mb-20">{{ __('Main Reports') }}</h2>
    <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; margin-bottom: 2rem;">
        <!-- REPORT 1: FINANCIAL -->
        <a href="{{ route('admin.reports.show', ['type' => 'summary']) }}" class="report-hub-card" style="text-align: center; padding: 2rem;">
            <i class="fa fa-money-bill-wave text-30 mb-15" style="color:#2563eb;"></i>
            <h3 class="text-18 fw-700">{{ __('Revenue Summary') }}</h3>
            <p class="text-13 opacity-70">{{ __('Financial trends & payment methods') }}</p>
        </a>

        <!-- REPORT 2: BOOKINGS -->
        <a href="{{ route('admin.reports.show', ['type' => 'bookings']) }}" class="report-hub-card" style="text-align: center; padding: 2rem;">
            <i class="fa fa-calendar-check text-30 mb-15" style="color:#16a34a;"></i>
            <h3 class="text-18 fw-700">{{ __('Booking Analytics') }}</h3>
            <p class="text-13 opacity-70">{{ __('Status, volume & cancellations') }}</p>
        </a>

        <!-- REPORT 3: EXPORT -->
        <a href="{{ route('admin.reports.show', ['type' => 'full']) }}" class="report-hub-card reports-card" style="text-align: center; padding: 2rem;">
            <i class="fa fa-file-csv text-30 mb-15" style="color:#475569;"></i>
            <h3 class="text-18 fw-700">{{ __('Master Export') }}</h3>
            <p class="text-13 opacity-70">{{ __('Combined CSV data snapshot') }}</p>
        </a>
    </div>

    <!-- UPPER ANALYTICS CHARTS -->
    <div style="display:grid; grid-template-columns: 2fr 1fr; gap:1.5rem; margin-bottom: 1.5rem;">
        <!-- Revenue Trend Line Chart -->
        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <h2 class="text-17 fw-600 mb-20">{{ __('Revenue Trend (Current Filter)') }}</h2>
            <div style="position:relative; height:300px;">
                <canvas id="chartRevenueTrend"></canvas>
            </div>
        </div>

        <!-- Cash vs Digital -->
        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <h2 class="text-17 fw-600 mb-20">{{ __('Payment Distribution') }}</h2>
            <div style="height:200px; margin-bottom: 20px;">
                <canvas id="chartCashPie"></canvas>
            </div>
            <div class="text-13">
                <div style="display:flex; justify-content:space-between; margin-bottom:5px;">
                    <span>{{ __('Cash') }}</span>
                    <strong>TZS {{ number_format($cashRevenue, 0) }}</strong>
                </div>
                <div style="display:flex; justify-content:space-between;">
                    <span>{{ __('Digital') }}</span>
                    <strong>TZS {{ number_format($nonCashRevenue, 0) }}</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- LOWER ANALYTICS CHARTS (NEW) -->
    <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap:1.5rem;">

        <!-- Chart 3: Room Occupancy Today -->
        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <h2 class="text-17 fw-600 mb-20">{{ __('Occupancy (Booked vs Available)') }}</h2>
            <div style="height:220px;">
                <canvas id="chartBookingAvailability"></canvas>
            </div>
            <div class="mt-15 text-13 text-center">
                <span style="color:#16a34a">● {{ __('Available') }}: {{ $availableRoomsCount }}</span>
                <span class="ml-15" style="color:#ef4444">● {{ __('Booked') }}: {{ $bookedRoomsCount }}</span>
            </div>
        </div>

        <!-- Chart 4: Maintenance Status -->
        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <h2 class="text-17 fw-600 mb-20">{{ __('Maintenance Status Tasks') }}</h2>
            <div style="height:220px;">
                <canvas id="chartMaintenanceStatus"></canvas>
            </div>
        </div>

        <!-- Chart 5: Room Distribution by Branch -->
        <div class="reports-card" style="padding:1.5rem; border-radius:16px;">
            <h2 class="text-17 fw-600 mb-20">{{ __('Rooms Distribution per Branch') }}</h2>
            <div style="height:220px;">
                <canvas id="chartRoomDist"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js" crossorigin="anonymous"></script>
<script>
(function () {
  if (typeof Chart === 'undefined') return;
  var styles = getComputedStyle(document.documentElement);
  var tickColor = styles.getPropertyValue('--brand-theme-text').trim() || '#d9e1ea';
  var gridColor = 'rgba(213, 172, 66, 0.16)';
  var surfaceBorder = styles.getPropertyValue('--brand-theme-surface').trim() || '#23262b';

  // Chart 1: Revenue Trend (Line Chart)
  new Chart(document.getElementById('chartRevenueTrend'), {
    type: 'line',
    data: {
      labels: @json($revenueTrend->pluck('date')),
      datasets: [{
        label: 'Daily Revenue',
        data: @json($revenueTrend->pluck('total')),
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.1)',
        fill: true,
        tension: 0.3
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: tickColor }, grid: { color: gridColor } },
        y: { ticks: { color: tickColor }, grid: { color: gridColor } }
      }
    }
  });

  // Chart 2: Cash vs Digital (Doughnut)
  new Chart(document.getElementById('chartCashPie'), {
    type: 'doughnut',
    data: {
      labels: ['Cash', 'Digital'],
      datasets: [{
        data: [@json($cashRevenue), @json($nonCashRevenue)],
        backgroundColor: ['#f59e0b', '#3b82f6'],
        borderWidth: 2,
        borderColor: surfaceBorder
      }]
    },
    options: { cutout: '70%', plugins: { legend: { position: 'bottom', labels: { color: tickColor } } } }
  });

  // Chart 3: Booking Availability (Doughnut)
  new Chart(document.getElementById('chartBookingAvailability'), {
    type: 'doughnut',
    data: {
      labels: ['Available', 'Booked'],
      datasets: [{
        data: [@json($availableRoomsCount), @json($bookedRoomsCount)],
        backgroundColor: ['#16a34a', '#ef4444'],
        borderWidth: 2,
        borderColor: surfaceBorder
      }]
    },
    options: { cutout: '65%', plugins: { legend: { display: false } } }
  });

  // Chart 4: Maintenance Status (Bar)
  new Chart(document.getElementById('chartMaintenanceStatus'), {
    type: 'bar',
    data: {
      labels: @json($maintenanceStats->pluck('status')),
      datasets: [{
        label: 'Total Tasks',
        data: @json($maintenanceStats->pluck('total')),
        backgroundColor: '#f59e0b',
        borderRadius: 5
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        x: { ticks: { color: tickColor }, grid: { color: gridColor } },
        y: { beginAtZero: true, ticks: { color: tickColor }, grid: { color: gridColor } }
      }
    }
  });

  // Chart 5: Room Distribution (Pie)
  new Chart(document.getElementById('chartRoomDist'), {
    type: 'pie',
    data: {
      labels: @json($roomDistribution->pluck('branch_name')),
      datasets: [{
        data: @json($roomDistribution->pluck('total')),
        backgroundColor: ['#3b82f6', '#8b5cf6', '#ec4899', '#f97316'],
        borderWidth: 2,
        borderColor: surfaceBorder
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, color: tickColor } } }
    }
  });

})();
</script>
@endpush
