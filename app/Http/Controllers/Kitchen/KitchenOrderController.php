<?php

namespace App\Http\Controllers\Kitchen;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\BookingMethod;
use App\Models\RoomServiceOrder;
use App\Models\Role;
use App\Models\User;
use App\Services\RoomServiceOrderPaymentService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class KitchenOrderController extends Controller
{
    use InteractsWithStaffScope;

    public function __construct(
        private readonly RoomServiceOrderPaymentService $payments,
    ) {}

    public function index(Request $request): View
    {
        $viewer = $request->user();
        $query = RoomServiceOrder::query()
            ->with(['room.branch', 'items', 'bookingMethod', 'assignedTo'])
            ->latest();
        $today = Carbon::today();
        $supportsPaymentTracking = RoomServiceOrder::supportsPaymentTracking();
        $canSeeAllOrders = $viewer?->canAssignKitchenOrders() || $viewer?->canManageKitchenStaff();
        $myTasksOnly = $request->boolean('mine');

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $query->whereRaw('0=1');
            } else {
                $query->whereIn('hotel_branch_id', $ids);
            }
        }

        if (! $canSeeAllOrders) {
            $query->where('assigned_to_user_id', $viewer?->id ?? 0);
            $myTasksOnly = true;
        } elseif ($myTasksOnly) {
            $query->where('assigned_to_user_id', $viewer?->id ?? 0);
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        $paymentFilter = $request->string('payment')->toString();
        if ($supportsPaymentTracking && $paymentFilter !== '') {
            if ($paymentFilter === 'paid') {
                $query->where('payment_status', 'paid');
            } elseif ($paymentFilter === 'unpaid') {
                $query->where('payment_status', '!=', 'paid');
            } elseif (in_array($paymentFilter, ['cash_pending', 'processing', 'bill_later'], true)) {
                $query->where('payment_status', $paymentFilter);
            }
        }

        $billedOnly = $request->boolean('billed');
        if ($billedOnly) {
            $query->whereNotNull('bill_generated_at');
        }

        $search = trim($request->string('q')->toString());
        if ($search !== '') {
            $query->where(function ($inner) use ($search): void {
                $inner->where('guest_name', 'like', '%'.$search.'%')
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

        $summaryQuery = RoomServiceOrder::query();
        if ($ids !== null) {
            if ($ids === []) {
                $summaryQuery->whereRaw('0=1');
            } else {
                $summaryQuery->whereIn('hotel_branch_id', $ids);
            }
        }

        if ($myTasksOnly) {
            $summaryQuery->where('assigned_to_user_id', $viewer?->id ?? 0);
        }

        return view('kitchen.orders.index', [
            'orders' => $query->paginate(12)->withQueryString(),
            'statusFilter' => $request->string('status')->toString(),
            'statuses' => RoomServiceOrderStatus::cases(),
            'summary' => [
                'today' => (clone $summaryQuery)->whereDate('created_at', $today)->count(),
                'pending' => (clone $summaryQuery)->where('status', RoomServiceOrderStatus::Pending->value)->count(),
                'preparing' => (clone $summaryQuery)->where('status', RoomServiceOrderStatus::Preparing->value)->count(),
                'completed' => (clone $summaryQuery)->where('status', RoomServiceOrderStatus::Delivered->value)->count(),
                'paid_today' => (float) ($supportsPaymentTracking
                    ? (clone $summaryQuery)->where('payment_status', 'paid')->whereDate('paid_at', $today)->sum('total_amount')
                    : 0),
                'unpaid_total' => (float) ($supportsPaymentTracking
                    ? (clone $summaryQuery)->where('payment_status', '!=', 'paid')->sum('total_amount')
                    : 0),
            ],
            'supportsPaymentTracking' => $supportsPaymentTracking,
            'paymentFilter' => $paymentFilter,
            'search' => $search,
            'billedOnly' => $billedOnly,
            'staffOptions' => $this->staffOptions(),
            'canAssignOrders' => $viewer?->canAssignKitchenOrders() ?? false,
            'canSeeAllOrders' => $canSeeAllOrders,
            'myTasksOnly' => $myTasksOnly,
        ]);
    }

    public function update(Request $request, RoomServiceOrder $order): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($order);
        $this->ensureKitchenOrderAccess($request->user(), $order);

        $data = $request->validate([
            'status' => ['required', 'in:pending,preparing,delivered,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $status = RoomServiceOrderStatus::from($data['status']);
        $updates = [
            'status' => $status->value,
            'notes' => $data['notes'] ?? null,
        ];

        if ($status === RoomServiceOrderStatus::Preparing) {
            $updates['estimated_ready_at'] = now()->addMinutes(max(10, (int) $order->preparation_minutes));
        }

        if ($status === RoomServiceOrderStatus::Delivered) {
            $updates['estimated_ready_at'] = now();
            $updates['completed_at'] = now();
        }

        if ($status === RoomServiceOrderStatus::Cancelled) {
            $updates['completed_at'] = now();
        }

        $updates['last_status_updated_by_user_id'] = $request->user()?->id;

        $order->update($updates);

        return back()->with('status', __('Kitchen order updated.'));
    }

    public function confirmPaid(RoomServiceOrder $order): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($order);
        $this->ensureKitchenOrderAccess(request()->user(), $order);

        $method = $order->bookingMethod ?: BookingMethod::query()->where('slug', 'cash')->first();
        abort_unless($method, 422, 'Payment method missing.');

        $this->payments->markPaid($order, $method, $order->payment_reference);

        return back()->with('status', __('Order payment confirmed.'));
    }

    private function ensureKitchenOrderAccess(?User $user, RoomServiceOrder $order): void
    {
        if ($user?->canAssignKitchenOrders() || $user?->canManageKitchenStaff()) {
            return;
        }

        abort_unless($order->isAssignedTo($user), 403);
    }

    private function staffOptions()
    {
        $currentUser = auth()->user();

        return User::query()
            ->where('is_active', true)
            ->where(function ($query) use ($currentUser): void {
                $query->where('id', $currentUser?->id)
                    ->orWhere(function ($staffQuery) use ($currentUser): void {
                        $staffQuery->whereHas('role', function ($roleQuery): void {
                            $roleQuery->where('slug', Role::KITCHEN_SLUG)
                                ->orWhere('context', 'kitchen');
                        })->where('created_by_user_id', $currentUser?->id);
                    });
            })
            ->orderBy('name')
            ->get(['id', 'name']);
    }
}
