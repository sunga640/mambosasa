<?php

namespace App\Http\Controllers\Reception;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\Booking;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class ReceptionReportController extends Controller
{
    use InteractsWithStaffScope;

    public function index(Request $request): View
    {
        $fromDate = $request->date('from') ?? now()->subDays(30);
        $toDate = $request->date('to') ?? now();
        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }
        $from = $fromDate->copy()->startOfDay();
        $to = $toDate->copy()->endOfDay();

        $base = Booking::query()
            ->whereBetween('bookings.created_at', [$from, $to]);

        $this->scope()->filterBookingsByBranch($base);

        $paymentByMethod = (clone $base)
            ->where('status', BookingStatus::Confirmed)
            ->join('booking_methods', 'bookings.booking_method_id', '=', 'booking_methods.id')
            ->selectRaw('booking_methods.name as method_name, booking_methods.slug, SUM(bookings.total_amount) as total, COUNT(*) as c')
            ->groupBy('booking_methods.id', 'booking_methods.name', 'booking_methods.slug')
            ->orderByDesc('total')
            ->get();

        $cashTotal = (float) (clone $base)
            ->where('status', BookingStatus::Confirmed)
            ->whereHas('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        $nonCashTotal = (float) (clone $base)
            ->where('status', BookingStatus::Confirmed)
            ->whereDoesntHave('method', fn ($m) => $m->where('slug', 'cash'))
            ->sum('total_amount');

        return view('reception.reports.index', [
            'from' => $from,
            'to' => $to,
            'paymentByMethod' => $paymentByMethod,
            'cashTotal' => $cashTotal,
            'nonCashTotal' => $nonCashTotal,
        ]);
    }
}
