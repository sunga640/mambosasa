<?php

namespace App\Http\Controllers\Kitchen;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\RoomServiceOrder;
use App\Models\RoomServiceOrderItem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class KitchenReportController extends Controller
{
    use InteractsWithStaffScope;

    public function index(Request $request): View
    {
        $fromDate = $request->date('from') ?? now()->subDays(29);
        $toDate = $request->date('to') ?? now();
        $supportsPaymentTracking = RoomServiceOrder::supportsPaymentTracking();

        if ($fromDate->gt($toDate)) {
            [$fromDate, $toDate] = [$toDate, $fromDate];
        }

        $from = $fromDate->copy()->startOfDay();
        $to = $toDate->copy()->endOfDay();

        $orders = RoomServiceOrder::query()
            ->with(['room.branch', 'items'])
            ->whereBetween('created_at', [$from, $to]);

        $scopeIds = $this->scope()->branchIds();
        if ($scopeIds !== null) {
            if ($scopeIds === []) {
                $orders->whereRaw('0=1');
            } else {
                $orders->whereIn('hotel_branch_id', $scopeIds);
            }
        }

        $statusFilter = $request->string('status')->toString();
        if ($statusFilter !== '' && in_array($statusFilter, RoomServiceOrderStatus::values(), true)) {
            $orders->where('status', $statusFilter);
        }

        $paymentFilter = $request->string('payment')->toString();
        if ($supportsPaymentTracking && $paymentFilter !== '') {
            if ($paymentFilter === 'paid') {
                $orders->where('payment_status', 'paid');
            } elseif ($paymentFilter === 'unpaid') {
                $orders->where('payment_status', '!=', 'paid');
            } elseif (in_array($paymentFilter, ['cash_pending', 'processing', 'bill_later'], true)) {
                $orders->where('payment_status', $paymentFilter);
            }
        }

        $search = trim($request->string('q')->toString());
        if ($search !== '') {
            $orders->where(function ($query) use ($search): void {
                $query->where('guest_name', 'like', '%'.$search.'%')
                    ->orWhere('guest_phone', 'like', '%'.$search.'%')
                    ->orWhere('public_reference', 'like', '%'.$search.'%')
                    ->orWhereHas('room', function ($roomQuery) use ($search): void {
                        $roomQuery->where('name', 'like', '%'.$search.'%')
                            ->orWhere('room_number', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('items', function ($itemQuery) use ($search): void {
                        $itemQuery->where('item_name', 'like', '%'.$search.'%');
                    });
            });
        }

        $summary = [
            'orders_total' => (clone $orders)->count(),
            'sales_total' => (float) ($supportsPaymentTracking
                ? (clone $orders)->where('payment_status', 'paid')->whereBetween('paid_at', [$from, $to])->sum('total_amount')
                : (clone $orders)->sum('total_amount')),
            'pending' => (clone $orders)->where('status', RoomServiceOrderStatus::Pending->value)->count(),
            'preparing' => (clone $orders)->where('status', RoomServiceOrderStatus::Preparing->value)->count(),
            'completed' => (clone $orders)->where('status', RoomServiceOrderStatus::Delivered->value)->count(),
            'cancelled' => (clone $orders)->where('status', RoomServiceOrderStatus::Cancelled->value)->count(),
            'avg_ticket' => (float) (($supportsPaymentTracking
                ? (clone $orders)->where('payment_status', 'paid')
                : (clone $orders))->avg('total_amount') ?? 0),
            'avg_prep_minutes' => (float) ((clone $orders)->avg('preparation_minutes') ?? 0),
        ];

        $statusRows = (clone $orders)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $trendSelect = $supportsPaymentTracking
            ? "DATE(created_at) as report_day, COUNT(*) as total, COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN total_amount ELSE 0 END), 0) as sales"
            : 'DATE(created_at) as report_day, COUNT(*) as total, COALESCE(SUM(total_amount), 0) as sales';

        $trendRows = (clone $orders)
            ->selectRaw($trendSelect)
            ->groupBy('report_day')
            ->orderBy('report_day')
            ->get()
            ->keyBy('report_day');

        $trend = [];
        for ($day = $fromDate->copy(); $day->lte($toDate); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $row = $trendRows->get($key);
            $trend[] = [
                'label' => $day->format('M j'),
                'orders' => (int) ($row->total ?? 0),
                'sales' => (float) ($row->sales ?? 0),
            ];
        }

        $topItemSalesSelect = $supportsPaymentTracking
            ? "room_service_order_items.item_name, SUM(room_service_order_items.quantity) as qty, SUM(CASE WHEN room_service_orders.payment_status = 'paid' THEN room_service_order_items.line_total ELSE 0 END) as sales"
            : 'room_service_order_items.item_name, SUM(room_service_order_items.quantity) as qty, SUM(room_service_order_items.line_total) as sales';

        $topItems = RoomServiceOrderItem::query()
            ->join('room_service_orders', 'room_service_order_items.room_service_order_id', '=', 'room_service_orders.id')
            ->when($scopeIds !== null, function ($query) use ($scopeIds): void {
                if ($scopeIds === []) {
                    $query->whereRaw('0=1');
                } else {
                    $query->whereIn('room_service_orders.hotel_branch_id', $scopeIds);
                }
            })
            ->whereBetween('room_service_orders.created_at', [$from, $to])
            ->selectRaw($topItemSalesSelect)
            ->groupBy('room_service_order_items.item_name')
            ->orderByDesc('qty')
            ->limit(8)
            ->get();

        $roomSalesSelect = $supportsPaymentTracking
            ? "COALESCE(rooms.name, 'Unknown room') as room_name, COALESCE(rooms.room_number, '-') as room_number, COUNT(room_service_orders.id) as total_orders, COALESCE(SUM(CASE WHEN room_service_orders.payment_status = 'paid' THEN room_service_orders.total_amount ELSE 0 END), 0) as sales"
            : "COALESCE(rooms.name, 'Unknown room') as room_name, COALESCE(rooms.room_number, '-') as room_number, COUNT(room_service_orders.id) as total_orders, COALESCE(SUM(room_service_orders.total_amount), 0) as sales";

        $roomPerformance = RoomServiceOrder::query()
            ->leftJoin('rooms', 'room_service_orders.room_id', '=', 'rooms.id')
            ->whereBetween('room_service_orders.created_at', [$from, $to])
            ->when($scopeIds !== null, function ($query) use ($scopeIds): void {
                if ($scopeIds === []) {
                    $query->whereRaw('0=1');
                } else {
                    $query->whereIn('room_service_orders.hotel_branch_id', $scopeIds);
                }
            })
            ->selectRaw($roomSalesSelect)
            ->groupBy('rooms.id', 'rooms.name', 'rooms.room_number')
            ->orderByDesc('total_orders')
            ->limit(8)
            ->get();

        return view('kitchen.reports.index', [
            'from' => $from,
            'to' => $to,
            'summary' => $summary,
            'statusRows' => $this->statusCollection($statusRows),
            'trend' => collect($trend),
            'topItems' => $topItems,
            'roomPerformance' => $roomPerformance,
            'printMode' => $request->boolean('print'),
            'supportsPaymentTracking' => $supportsPaymentTracking,
            'statusFilter' => $statusFilter,
            'paymentFilter' => $paymentFilter,
            'search' => $search,
            'statuses' => RoomServiceOrderStatus::cases(),
        ]);
    }

    private function statusCollection(Collection $statusRows): Collection
    {
        return collect(RoomServiceOrderStatus::cases())->map(function (RoomServiceOrderStatus $status) use ($statusRows): array {
            return [
                'label' => $status->label(),
                'slug' => $status->value,
                'total' => (int) ($statusRows->get($status->value) ?? 0),
            ];
        });
    }
}
