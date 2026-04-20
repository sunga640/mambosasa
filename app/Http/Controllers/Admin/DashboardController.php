<?php

namespace App\Http\Controllers\Admin;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\DashboardNotification;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\User;
use App\Support\DashboardMonthCalendar;
use App\Support\StaffScope;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __invoke(StaffScope $scope): View
    {
        $bookingBase = Booking::query();
        $scope->filterBookingsByBranch($bookingBase);

        $statusRows = (clone $bookingBase)
            ->selectRaw('status, COUNT(*) as c')
            ->groupBy('status')
            ->pluck('c', 'status')
            ->all();

        $byStatus = [];
        foreach (BookingStatus::cases() as $case) {
            $byStatus[$case->value] = (int) ($statusRows[$case->value] ?? 0);
        }

        $totalBookings = array_sum($byStatus);

        // --- REVENUE CALCULATIONS ---

        // 1. Total Confirmed Revenue (Ilikuwepo)
        $confirmedRevenue = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->sum('total_amount');

        // 2. Revenue Today (MPYA)
        $revenueToday = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // 3. Revenue This Month (MPYA)
        $revenueMonth = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // 4. Revenue This Year (MPYA)
        $revenueYear = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        $pendingRevenue = (float) (clone $bookingBase)
            ->where('status', BookingStatus::PendingPayment)
            ->sum('total_amount');

        // --- CHARTS & TRENDS ---
        $days = 14;
        $start = now()->subDays($days - 1)->startOfDay();
        $trendLabels = [];
        $trendCounts = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $trendLabels[] = $d->format('M j');
            $ds = $d->toDateString();
            $trendCounts[] = (int) (clone $bookingBase)
                ->whereDate('created_at', $ds)
                ->count();
        }

        $statusChartLabels = [];
        $statusChartData = [];
        $statusChartColors = [
            'pending_payment' => 'rgba(234, 179, 8, 0.85)',
            'expired' => 'rgba(148, 163, 184, 0.85)',
            'confirmed' => 'rgba(34, 197, 94, 0.85)',
            'cancelled' => 'rgba(239, 68, 68, 0.85)',
        ];
        $chartColors = [];
        foreach (BookingStatus::cases() as $case) {
            $c = $byStatus[$case->value] ?? 0;
            if ($c > 0) {
                $statusChartLabels[] = $case->label();
                $statusChartData[] = $c;
                $chartColors[] = $statusChartColors[$case->value] ?? 'rgba(100,116,139,0.8)';
            }
        }
        if ($totalBookings === 0) {
            $statusChartLabels = [__('No bookings yet')];
            $statusChartData = [1];
            $chartColors = ['rgba(226,232,240,0.9)'];
        }

        $revenueByDayLabels = [];
        $revenueByDayData = [];
        for ($i = 0; $i < $days; $i++) {
            $d = $start->copy()->addDays($i);
            $revenueByDayLabels[] = $d->format('M j');
            $ds = $d->toDateString();
            $revenueByDayData[] = round((float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->where(function ($q) use ($ds): void {
                    $q->whereDate('confirmed_at', $ds)
                        ->orWhere(function ($q2) use ($ds): void {
                            $q2->whereNull('confirmed_at')->whereDate('created_at', $ds);
                        });
                })
                ->sum('total_amount'), 2);
        }

        $contactCount = ContactMessage::query()->count();
        $recentContacts = ContactMessage::query()->latest()->limit(5)->get();

        $paymentByMethod = (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->join('booking_methods', 'bookings.booking_method_id', '=', 'booking_methods.id')
            ->selectRaw('booking_methods.name as method_name, booking_methods.slug, SUM(bookings.total_amount) as total, COUNT(*) as c')
            ->groupBy('booking_methods.id', 'booking_methods.name', 'booking_methods.slug')
            ->orderByDesc('total')
            ->get();

        $paymentSeriesRows = (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->join('booking_methods', 'bookings.booking_method_id', '=', 'booking_methods.id')
            ->whereDate('bookings.created_at', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('DATE(bookings.created_at) as d, booking_methods.name as method_name, SUM(bookings.total_amount) as total')
            ->groupBy(DB::raw('DATE(bookings.created_at)'), 'booking_methods.name')
            ->orderBy('d')
            ->get();
        $paymentSeriesLabels = collect(range(0, 30))
            ->map(fn (int $i) => now()->subDays(30 - $i)->format('Y-m-d'))
            ->values();
        $paymentSeriesDataset = [];
        foreach ($paymentSeriesRows->groupBy('method_name') as $methodName => $rows) {
            $map = $rows->pluck('total', 'd');
            $paymentSeriesDataset[] = [
                'label' => $methodName,
                'data' => $paymentSeriesLabels->map(fn ($d) => round((float) ($map[$d] ?? 0), 2))->values()->all(),
            ];
        }

        $cashRevenue = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        $nonCashRevenue = (float) (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->whereDoesntHave('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        $recentActivity = ActivityLog::query()
            ->with('user')
            ->latest()
            ->limit(18)
            ->get();

        $expenseRows = RoomMaintenance::query()
            ->whereDate('started_at', '>=', now()->subDays(30)->toDateString())
            ->selectRaw('DATE(started_at) as d, SUM(COALESCE(expenses,0)) as total')
            ->groupBy(DB::raw('DATE(started_at)'))
            ->orderBy('d')
            ->get();
        $expenseLabels = collect(range(0, 30))
            ->map(fn (int $i) => now()->subDays(30 - $i)->format('Y-m-d'))
            ->values();
        $expenseMap = $expenseRows->pluck('total', 'd');
        $expenseSeries = $expenseLabels->map(fn ($d) => round((float) ($expenseMap[$d] ?? 0), 2))->values();
        $expensesMonthTotal = (float) RoomMaintenance::query()
            ->whereMonth('started_at', now()->month)
            ->whereYear('started_at', now()->year)
            ->sum('expenses');

        $notificationUnreadCount = DashboardNotification::query();
        $scope->filterNotificationsByBranch($notificationUnreadCount);
        $notificationUnreadCount = $notificationUnreadCount
            ->whereNull('read_at')
            ->whereNull('resolved_at')
            ->count();

        $recentDashboardNotifications = DashboardNotification::query()
            ->with(['room.branch', 'booking']);
        $scope->filterNotificationsByBranch($recentDashboardNotifications);
        $recentDashboardNotifications = $recentDashboardNotifications->latest()->limit(8)->get();

        $roomQ = Room::query();
        $scope->filterRoomsByBranch($roomQ);

            // --- PROFIT & LOSS CALCULATION ---
            $profitMonth = $revenueMonth - $expensesMonthTotal;

            // Kokotoa asilimia ya faida (Profit Margin)
            $profitPercent = 0;
            if ($revenueMonth > 0) {
                $profitPercent = ($profitMonth / $revenueMonth) * 100;
            } elseif ($expensesMonthTotal > 0) {
                // Kama hakuna mapato lakini kuna matumizi, basi ni hasara ya 100%
                $profitPercent = -100;
            }

        return view('admin.dashboard', [
            'notificationUnreadCount' => $notificationUnreadCount,
            'recentDashboardNotifications' => $recentDashboardNotifications,
            'branchSummary' => [
                'active' => HotelBranch::query()->where('is_active', true)->count(),
                'inactive' => HotelBranch::query()->where('is_active', false)->count(),
            ],
            'kpis' => [
                'bookings_total' => $totalBookings,
                'bookings_pending' => $byStatus[BookingStatus::PendingPayment->value] ?? 0,
                'bookings_confirmed' => $byStatus[BookingStatus::Confirmed->value] ?? 0,
                'rooms' => $roomQ->count(),
                'branches' => HotelBranch::query()->count(),
                'users' => User::query()->count(),
                'contact_messages' => $contactCount,
                'revenue_confirmed' => $confirmedRevenue,
                'revenue_pending' => $pendingRevenue,
                // Ongeza hizi hapa chini:
                'revenue_today' => $revenueToday,
                'revenue_month' => $revenueMonth,
                'revenue_year' => $revenueYear,
                'expenses_month_total' => $expensesMonthTotal,
                'profit_month' => $profitMonth,
                'profit_percent' => $profitPercent,
            ],
            'dashCalendar' => DashboardMonthCalendar::forStaffBookings(clone $bookingBase),
            'byStatus' => $byStatus,
            'chartTrendLabels' => $trendLabels,
            'chartTrendData' => $trendCounts,
            'chartStatusLabels' => $statusChartLabels,
            'chartStatusData' => $statusChartData,
            'chartStatusColors' => $chartColors,
            'chartRevenueLabels' => $revenueByDayLabels,
            'chartRevenueData' => $revenueByDayData,
            'recentContacts' => $recentContacts,
            'recentActivity' => $recentActivity,
            'paymentByMethod' => $paymentByMethod,
            'cashRevenue' => $cashRevenue,
            'nonCashRevenue' => $nonCashRevenue,
            'paymentSeriesLabels' => $paymentSeriesLabels,
            'paymentSeriesDataset' => $paymentSeriesDataset,
            'expenseLabels' => $expenseLabels,
            'expenseSeries' => $expenseSeries,
        ]);
    }
}
