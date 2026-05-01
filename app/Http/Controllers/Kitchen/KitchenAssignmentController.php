<?php

namespace App\Http\Controllers\Kitchen;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\RoomServiceOrder;
use App\Models\Role;
use App\Models\User;
use App\Services\KitchenNotificationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;
use Illuminate\Validation\Rule;

class KitchenAssignmentController extends Controller
{
    use InteractsWithStaffScope;

    public function __construct(
        private readonly KitchenNotificationService $notifications,
    ) {}

    public function index(): View
    {
        $staff = $this->staffPool();
        $ordersQuery = RoomServiceOrder::query()
            ->with(['room', 'items', 'assignedTo'])
            ->latest()
            ->limit(100);

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $ordersQuery->whereRaw('0=1');
            } else {
                $ordersQuery->whereIn('hotel_branch_id', $ids);
            }
        }

        $orders = $ordersQuery->get();

        $unassigned = $orders->whereNull('assigned_to_user_id')->values();
        $staffRows = $staff->map(function (User $staffUser) use ($orders): array {
            $assigned = $orders->where('assigned_to_user_id', $staffUser->id)->values();
            $totalAssigned = $assigned->count();
            $completed = $assigned->filter(fn (RoomServiceOrder $order) => $order->status === RoomServiceOrderStatus::Delivered->value)->count();
            $active = $assigned->filter(fn (RoomServiceOrder $order) => in_array($order->status, [RoomServiceOrderStatus::Pending->value, RoomServiceOrderStatus::Preparing->value], true))->count();

            return [
                'user' => $staffUser,
                'orders' => $assigned,
                'total_assigned' => $totalAssigned,
                'completed' => $completed,
                'active' => $active,
                'performance' => $totalAssigned > 0 ? round(($completed / $totalAssigned) * 100) : 0,
            ];
        });

        return view('kitchen.assignments.index', [
            'unassignedOrders' => $unassigned,
            'staffRows' => $staffRows,
        ]);
    }

    public function assign(Request $request, RoomServiceOrder $order): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($order);
        $order->loadMissing(['room.branch', 'items', 'assignedTo']);

        $staffIds = $this->staffPool()->pluck('id')->all();
        $data = $request->validate([
            'assigned_to_user_id' => ['nullable', Rule::in($staffIds)],
        ]);

        $assignedUserId = isset($data['assigned_to_user_id']) && $data['assigned_to_user_id'] !== null && $data['assigned_to_user_id'] !== ''
            ? (int) $data['assigned_to_user_id']
            : null;
        $previousAssignedUserId = (int) ($order->assigned_to_user_id ?? 0);

        $order->update([
            'assigned_to_user_id' => $assignedUserId,
            'assigned_by_user_id' => $assignedUserId ? auth()->id() : null,
            'assigned_at' => $assignedUserId ? now() : null,
        ]);

        if ($assignedUserId) {
            $assignedUser = $this->staffPool()->firstWhere('id', $assignedUserId);
            if ($assignedUser && ($previousAssignedUserId !== $assignedUserId || $order->wasChanged('assigned_to_user_id'))) {
                $freshOrder = $order->fresh(['room.branch', 'items', 'assignedTo']);
                $this->notifications->notifyAssignedTask($freshOrder, $assignedUser);
                $this->sendAssignmentEmail($freshOrder, $assignedUser);
            }
        }

        return back()->with('status', $assignedUserId ? __('Order assigned to kitchen staff.') : __('Order returned to unassigned queue.'));
    }

    private function staffPool()
    {
        $currentUser = auth()->user();

        return User::query()
            ->with('role')
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
            ->get();
    }

    private function sendAssignmentEmail(RoomServiceOrder $order, User $staffUser): void
    {
        if (! $staffUser->email) {
            return;
        }

        try {
            Mail::send('emails.kitchen-task-assigned', [
                'order' => $order,
                'staffUser' => $staffUser,
                'assignedBy' => auth()->user(),
                'taskTitle' => $order->guest_name ?: __('Kitchen order task'),
                'itemsText' => $order->items->map(fn ($item) => $item->item_name.' x '.$item->quantity)->implode(', '),
            ], function ($mail) use ($staffUser, $order): void {
                $mail->to($staffUser->email, $staffUser->name)
                    ->subject(__('New kitchen task assigned: :reference', [
                        'reference' => $order->public_reference ?: '#'.$order->id,
                    ]));
            });
        } catch (Throwable $exception) {
            report($exception);
        }
    }
}
