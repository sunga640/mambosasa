<?php

namespace App\Http\Controllers;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\HotelBranch;
use App\Models\RoomServiceOrder;
use App\Models\SystemSetting;
use App\Support\DashboardMonthCalendar;
use App\Models\RoomMaintenance;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;

class MemberDashboardController extends Controller
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $allowStaffGuestView = $user
            && $request->boolean('guest_view')
            && ($user->hasAdminPanelAccess() || $user->hasStaffPanelAccess());

        if ($user && ! $allowStaffGuestView && $user->accountHomeUrl() !== route('dashboard')) {
            return redirect()->to($user->accountHomeUrl());
        }

        Artisan::call('bookings:expire-pending');

        $userId = $user->id;
        $email = $user->email;

        $bookingScope = function ($q) use ($userId, $email): void {
            $q->where('user_id', $userId)->orWhere('email', $email);
        };

        $branchFilter = session('member_booking_branch_id');
        $scopeBranch = function ($q) use ($bookingScope, $branchFilter): void {
            $bookingScope($q);
            if ($branchFilter) {
                $q->whereHas('room', fn ($r) => $r->where('hotel_branch_id', (int) $branchFilter));
            }
        };

        $pendingBookings = Booking::query()
            ->where($scopeBranch)
            ->where('status', BookingStatus::PendingPayment)
            ->with('room')
            ->latest()
            ->get();

        $recentBookings = Booking::query()
            ->where($scopeBranch)
            ->with(['room.branch', 'method', 'invoice'])
            ->latest()
            ->limit(20)
            ->get();

        $memberStats = [
            'total_spend_confirmed' => (float) Booking::query()
                ->where($scopeBranch)
                ->where('status', BookingStatus::Confirmed)
                ->sum('total_amount'),
            'bookings_confirmed' => (int) Booking::query()
                ->where($scopeBranch)
                ->where('status', BookingStatus::Confirmed)
                ->count(),
            'bookings_pending' => (int) Booking::query()
                ->where($scopeBranch)
                ->where('status', BookingStatus::PendingPayment)
                ->count(),
            'bookings_total' => (int) Booking::query()->where($scopeBranch)->count(),
            'expenses_month_total' => (float) RoomMaintenance::query()
                ->whereMonth('started_at', now()->month)
                ->whereYear('started_at', now()->year)
                ->sum('expenses'),
        ];

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

        $calendarBookings = Booking::query()
            ->where($scopeBranch)
            ->whereIn('status', [BookingStatus::Confirmed, BookingStatus::PendingPayment])
            ->whereNotNull('check_in')
            ->whereNotNull('check_out')
            ->get(['public_reference', 'check_in', 'check_out', 'status']);

        $recentRoomServiceOrders = RoomServiceOrder::query()
            ->where(function ($q) use ($userId, $email): void {
                $q->where('user_id', $userId)
                    ->orWhereHas('booking', fn ($bookingQuery) => $bookingQuery->where('email', $email));
            })
            ->with(['room', 'items'])
            ->latest()
            ->limit(6)
            ->get();

        $sys = SystemSetting::current();

        return view('dashboard', [
            'dashCalendar' => DashboardMonthCalendar::forMemberStays(
                $calendarBookings,
                null,
                $sys->bookingCheckoutTime(),
                $sys->bookingCheckoutWeekendTime(),
            ),
            'pendingBookings' => $pendingBookings,
            'recentBookings' => $recentBookings,
            'memberStats' => $memberStats,
            'branchSummary' => [
                'active' => HotelBranch::query()->where('is_active', true)->count(),
                'inactive' => HotelBranch::query()->where('is_active', false)->count(),
            ],
            'memberBranchFilterId' => $branchFilter ? (int) $branchFilter : null,
            'memberBranchesForFilter' => HotelBranch::query()->orderBy('name')->get(),
            'expenseLabels' => $expenseLabels,
            'expenseSeries' => $expenseSeries,
            'recentRoomServiceOrders' => $recentRoomServiceOrders,
        ]);
    }
}
