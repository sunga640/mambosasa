<?php

namespace App\Http\Controllers\Reception;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\Booking;
use App\Models\ContactMessage;
use App\Models\Customer;
use App\Models\HotelBranch;
use App\Models\Room;
use App\Models\RoomMaintenance;
use App\Models\RoomServiceOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon; // Hakikisha hii ipo
use App\Support\DashboardMonthCalendar;
use Illuminate\Support\Facades\DB;

class ReceptionDashboardController extends Controller
{
    use InteractsWithStaffScope;

    public function __invoke(): View
    {
        $scope = $this->scope();

        $bookingBase = Booking::query();
        $scope->filterBookingsByBranch($bookingBase);

        // Kuanzia hapa ndio kumeongezeka kwa ajili ya Revenue za Leo, Mwezi na Mwaka
        $kpis = [
            'bookings_total' => (clone $bookingBase)->count(),
            'bookings_pending' => (clone $bookingBase)->where('status', BookingStatus::PendingPayment)->count(),
            'bookings_confirmed' => (clone $bookingBase)->where('status', BookingStatus::Confirmed)->count(),

            // Mapato ya Jumla (Confirmed)
            'revenue_confirmed' => (float) (clone $bookingBase)->where('status', BookingStatus::Confirmed)->sum('total_amount'),

            // MAPATO MAPYA (Leo, Mwezi, Mwaka)
            'revenue_today' => (float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->whereDate('created_at', Carbon::today())
                ->sum('total_amount'),

            'revenue_month' => (float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount'),

            'revenue_year' => (float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('total_amount'),

            'revenue_cash' => (float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->whereHas('method', fn ($q) => $q->where('slug', 'cash'))
                ->sum('total_amount'),

            'customers' => Customer::query()
                ->whereHas('bookings', function ($q) use ($scope) {
                    $scope->filterBookingsByBranch($q);
                })
                ->distinct()
                ->count('email'),
        ];

        $roomBase = Room::query();
        $scope->filterRoomsByBranch($roomBase);
        $kpis['rooms'] = (clone $roomBase)->count();

        $branchIds = $scope->branchIds();
        $maintQ = RoomMaintenance::query();
        if ($branchIds !== null) {
            if ($branchIds === []) {
                $maintQ->whereRaw('0=1');
            } else {
                $maintQ->whereIn('hotel_branch_id', $branchIds);
            }
        }
        $kpis['maintenance_open'] = (clone $maintQ)->where('status', \App\Enums\MaintenanceStatus::Active)->count();
        $kpis['expenses_month_total'] = (float) (clone $maintQ)
            ->whereMonth('started_at', now()->month)
            ->whereYear('started_at', now()->year)
            ->sum('expenses');

        $roomServiceBills = RoomServiceOrder::query();
        if ($branchIds !== null) {
            if ($branchIds === []) {
                $roomServiceBills->whereRaw('0=1');
            } else {
                $roomServiceBills->whereIn('hotel_branch_id', $branchIds);
            }
        }
        if (RoomServiceOrder::supportsPaymentTracking()) {
            $roomServiceBills->whereNotNull('bill_generated_at')
                ->where('payment_status', '!=', 'paid');
        } else {
            $roomServiceBills->whereRaw('0=1');
        }
        $kpis['kitchen_unpaid_bills'] = (clone $roomServiceBills)->count();
        $kpis['kitchen_unpaid_amount'] = (float) (clone $roomServiceBills)->sum('total_amount');

        // ... code zingine zinaendelea vilevile (paymentByMethod, branchTotals, etc.) ...

        // PAYMENT BY METHOD
        $paymentByMethod = (clone $bookingBase)
            ->where('status', BookingStatus::Confirmed)
            ->join('booking_methods', 'bookings.booking_method_id', '=', 'booking_methods.id')
            ->selectRaw('booking_methods.name as method_name, booking_methods.slug, SUM(bookings.total_amount) as total')
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

        $expenseRows = (clone $maintQ)
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

        $branches = HotelBranch::query()->orderBy('name')->get();
        $branchTotals = [];
        foreach ($branches as $br) {
            if ($branchIds !== null && ($branchIds === [] || ! in_array($br->id, $branchIds, true))) {
                continue;
            }
            $b = Booking::query()
                ->where('status', BookingStatus::Confirmed)
                ->whereHas('room', fn ($r) => $r->where('hotel_branch_id', $br->id));
            $branchTotals[] = [
                'branch' => $br->name,
                'total' => (float) (clone $b)->sum('total_amount'),
                'count' => (clone $b)->count(),
            ];
        }

        $recentContacts = ContactMessage::query()->with('branch')->latest();
        $scope->filterContactMessagesByBranch($recentContacts);
        $recentContacts = $recentContacts->limit(5)->get();

        $chartDays = 14;
        $chartStart = now()->subDays($chartDays - 1)->startOfDay();
        $chartTrendLabels = [];
        $chartTrendData = [];
        $chartRevenueLabels = [];
        $chartRevenueData = [];
        for ($i = 0; $i < $chartDays; $i++) {
            $d = $chartStart->copy()->addDays($i);
            $ds = $d->toDateString();
            $chartTrendLabels[] = $d->format('M j');
            $chartTrendData[] = (int) (clone $bookingBase)->whereDate('created_at', $ds)->count();
            $chartRevenueLabels[] = $d->format('M j');
            $chartRevenueData[] = round((float) (clone $bookingBase)
                ->where('status', BookingStatus::Confirmed)
                ->where(function ($q) use ($ds): void {
                    $q->whereDate('confirmed_at', $ds)
                        ->orWhere(function ($q2) use ($ds): void {
                            $q2->whereNull('confirmed_at')->whereDate('created_at', $ds);
                        });
                })
                ->sum('total_amount'), 2);
        }

        return view('reception.dashboard', [
            'dashCalendar' => DashboardMonthCalendar::forStaffBookings(clone $bookingBase),
            'kpis' => $kpis,
            'paymentByMethod' => $paymentByMethod,
            'branchTotals' => $branchTotals,
            'recentContacts' => $recentContacts,
            'chartTrendLabels' => $chartTrendLabels,
            'chartTrendData' => $chartTrendData,
            'chartRevenueLabels' => $chartRevenueLabels,
            'chartRevenueData' => $chartRevenueData,
            'paymentSeriesLabels' => $paymentSeriesLabels,
            'paymentSeriesDataset' => $paymentSeriesDataset,
            'cashRevenue' => $cashRevenue,
            'nonCashRevenue' => $nonCashRevenue,
            'expenseLabels' => $expenseLabels,
            'expenseSeries' => $expenseSeries,
            'branchSummary' => [
                'active' => HotelBranch::query()->where('is_active', true)->count(),
                'inactive' => HotelBranch::query()->where('is_active', false)->count(),
            ],
        ]);
    }
}
