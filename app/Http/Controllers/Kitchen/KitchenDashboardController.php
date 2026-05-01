<?php

namespace App\Http\Controllers\Kitchen;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\KitchenRoomQr;
use App\Models\Room;
use App\Models\Role;
use App\Models\RestaurantMenuItem;
use App\Models\RoomServiceOrder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class KitchenDashboardController extends Controller
{
    use InteractsWithStaffScope;

    public function __invoke(): View
    {
        $viewer = auth()->user();
        $orders = RoomServiceOrder::query()->with(['room', 'items', 'bookingMethod'])->latest();
        $menu = RestaurantMenuItem::query();
        $rooms = Room::query()->with('branch')->orderBy('name');
        $hasQrTable = Schema::hasTable('kitchen_room_qrs');
        $qrs = $hasQrTable ? KitchenRoomQr::query() : null;

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $orders->whereRaw('0=1');
                $menu->whereRaw('0=1');
                $rooms->whereRaw('0=1');
                if ($qrs) {
                    $qrs->whereRaw('0=1');
                }
            } else {
                $orders->whereIn('hotel_branch_id', $ids);
                $rooms->whereIn('hotel_branch_id', $ids);
                if ($qrs) {
                    $qrs->whereIn('hotel_branch_id', $ids);
                }
            }
        }

        $today = Carbon::today();
        $weekAgo = Carbon::today()->subDays(6);
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        $supportsPaymentTracking = RoomServiceOrder::supportsPaymentTracking();

        $summary = [
            'total_today' => (clone $orders)->whereDate('created_at', $today)->count(),
            'pending' => (clone $orders)->where('status', RoomServiceOrderStatus::Pending->value)->count(),
            'preparing' => (clone $orders)->where('status', RoomServiceOrderStatus::Preparing->value)->count(),
            'completed' => (clone $orders)->where('status', RoomServiceOrderStatus::Delivered->value)->count(),
            'active_menu' => (clone $menu)->where('is_active', true)->count(),
            'qr_rooms' => $qrs ? (clone $qrs)->where('is_active', true)->count() : 0,
            'sales_today' => (float) ($supportsPaymentTracking
                ? (clone $orders)->where('payment_status', 'paid')->whereDate('paid_at', $today)->sum('total_amount')
                : (clone $orders)->whereDate('created_at', $today)->sum('total_amount')),
            'sales_last_month' => (float) ($supportsPaymentTracking
                ? (clone $orders)->where('payment_status', 'paid')->whereBetween('paid_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount')
                : (clone $orders)->whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total_amount')),
            'rooms_total' => (clone $rooms)->count(),
            'scanned_today' => $qrs ? (clone $qrs)->whereDate('last_scanned_at', $today)->count() : 0,
        ];

        $staffQuery = User::query()
            ->with('role')
            ->where('is_active', true)
            ->whereHas('role', function ($query): void {
                $query->where('slug', Role::KITCHEN_SLUG)
                    ->orWhere('context', 'kitchen');
            })
            ->where(function ($query) use ($viewer): void {
                $query->where('id', $viewer?->id)
                    ->orWhere('created_by_user_id', $viewer?->id);
            });

        $staffSummary = [
            'staff_total' => (clone $staffQuery)->count(),
            'roles_total' => Role::query()->where('context', 'kitchen')->where('created_by_user_id', $viewer?->id)->count(),
            'my_active_tasks' => (clone $orders)->where('assigned_to_user_id', $viewer?->id)->whereIn('status', [RoomServiceOrderStatus::Pending->value, RoomServiceOrderStatus::Preparing->value])->count(),
            'unassigned_orders' => (clone $orders)->whereNull('assigned_to_user_id')->whereIn('status', [RoomServiceOrderStatus::Pending->value, RoomServiceOrderStatus::Preparing->value])->count(),
        ];

        $staffCards = (clone $staffQuery)
            ->withCount([
                'assignedRoomServiceOrders as active_tasks_count' => fn ($query) => $query->whereIn('status', [RoomServiceOrderStatus::Pending->value, RoomServiceOrderStatus::Preparing->value]),
                'assignedRoomServiceOrders as completed_tasks_count' => fn ($query) => $query->where('status', RoomServiceOrderStatus::Delivered->value),
            ])
            ->limit(6)
            ->get();

        $myAssignedOrders = (clone $orders)
            ->where('assigned_to_user_id', $viewer?->id)
            ->whereIn('status', [
                RoomServiceOrderStatus::Pending->value,
                RoomServiceOrderStatus::Preparing->value,
                RoomServiceOrderStatus::Delivered->value,
            ])
            ->limit(6)
            ->get();

        $myTaskSummary = [
            'total_assigned' => (clone $orders)->where('assigned_to_user_id', $viewer?->id)->count(),
            'pending_assigned' => (clone $orders)->where('assigned_to_user_id', $viewer?->id)->where('status', RoomServiceOrderStatus::Pending->value)->count(),
            'preparing_assigned' => (clone $orders)->where('assigned_to_user_id', $viewer?->id)->where('status', RoomServiceOrderStatus::Preparing->value)->count(),
            'delivered_assigned' => (clone $orders)->where('assigned_to_user_id', $viewer?->id)->where('status', RoomServiceOrderStatus::Delivered->value)->count(),
        ];

        $statusBreakdown = (clone $orders)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $trendRows = (clone $orders)
            ->whereBetween('created_at', [$weekAgo->copy()->startOfDay(), $today->copy()->endOfDay()])
            ->selectRaw('DATE(created_at) as order_day, COUNT(*) as total')
            ->groupBy('order_day')
            ->orderBy('order_day')
            ->get()
            ->pluck('total', 'order_day');

        $trend = [];
        for ($day = $weekAgo->copy(); $day->lte($today); $day->addDay()) {
            $key = $day->format('Y-m-d');
            $trend[] = [
                'label' => $day->format('M j'),
                'total' => (int) ($trendRows[$key] ?? 0),
            ];
        }

        $qrCodes = $qrs
            ? (clone $qrs)->with(['room.branch'])->latest()->get()->keyBy('room_id')
            : collect();
        $roomCollection = (clone $rooms)->limit(8)->get();
        $menuItems = (clone $menu)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return view('kitchen.dashboard', [
            'summary' => $summary,
            'statusBreakdown' => $statusBreakdown,
            'trend' => $trend,
            'recentOrders' => (clone $orders)->limit(2)->get(),
            'rooms' => $roomCollection,
            'qrCodes' => $qrCodes,
            'menuItems' => $menuItems,
            'builderColumns' => $this->menuBuilderColumns($menuItems),
            'hasQrTable' => $hasQrTable,
            'staffSummary' => $staffSummary,
            'staffCards' => $staffCards,
            'myAssignedOrders' => $myAssignedOrders,
            'myTaskSummary' => $myTaskSummary,
        ]);
    }

    private function menuBuilderColumns(Collection $items): array
    {
        $buckets = [
            __('Breakfast') => [],
            __('Lunch') => [],
            __('Dinner') => [],
            __('Late Night') => [],
        ];

        foreach ($items->values() as $index => $item) {
            $keys = array_keys($buckets);
            $bucket = $keys[$index % count($keys)];
            $buckets[$bucket][] = $item;
        }

        return $buckets;
    }
}
