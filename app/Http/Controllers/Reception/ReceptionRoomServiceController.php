<?php

namespace App\Http\Controllers\Reception;

use App\Enums\RoomServiceOrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\BookingMethod;
use App\Models\RoomServiceOrder;
use App\Services\RoomServiceOrderPaymentService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReceptionRoomServiceController extends Controller
{
    use InteractsWithStaffScope;

    public function __construct(
        private readonly RoomServiceOrderPaymentService $payments,
    ) {}

    public function index(Request $request): View
    {
        $q = RoomServiceOrder::query()
            ->with(['user', 'room.branch', 'items', 'booking', 'bookingMethod', 'billGeneratedBy'])
            ->latest();
        $supportsPaymentTracking = RoomServiceOrder::supportsPaymentTracking();

        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $q->whereRaw('0=1');
            } else {
                $q->whereIn('hotel_branch_id', $ids);
            }
        }

        $statusFilter = $request->string('status')->toString();
        if ($statusFilter !== '' && in_array($statusFilter, RoomServiceOrderStatus::values(), true)) {
            $q->where('status', $statusFilter);
        }

        $paymentFilter = $request->string('payment')->toString();
        if ($supportsPaymentTracking && $paymentFilter !== '') {
            if ($paymentFilter === 'paid') {
                $q->where('payment_status', 'paid');
            } elseif ($paymentFilter === 'unpaid') {
                $q->where('payment_status', '!=', 'paid');
            } elseif (in_array($paymentFilter, ['cash_pending', 'processing', 'bill_later'], true)) {
                $q->where('payment_status', $paymentFilter);
            }
        }

        $billedOnly = $request->boolean('billed');
        if ($billedOnly) {
            $q->whereNotNull('bill_generated_at');
        }

        $search = trim($request->string('q')->toString());
        if ($search !== '') {
            $q->where(function ($query) use ($search): void {
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

        $orders = $q->paginate(7)->withQueryString();

        return view('reception.room-service.index', [
            'orders' => $orders,
            'supportsPaymentTracking' => $supportsPaymentTracking,
            'statusFilter' => $statusFilter,
            'paymentFilter' => $paymentFilter,
            'search' => $search,
            'billedOnly' => $billedOnly,
            'paymentMethods' => BookingMethod::query()->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, RoomServiceOrder $roomServiceOrder): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($roomServiceOrder);

        $data = $request->validate([
            'booking_method_id' => ['required', 'integer', 'exists:booking_methods,id'],
        ]);

        $method = BookingMethod::query()
            ->whereKey($data['booking_method_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $roomServiceOrder->update([
            'booking_method_id' => $method->id,
        ]);

        return back()->with('status', __('Order payment method updated.'));
    }

    public function confirmPaid(RoomServiceOrder $roomServiceOrder): RedirectResponse
    {
        $this->ensureRoomServiceOrderInScope($roomServiceOrder);

        abort_if(! RoomServiceOrder::supportsPaymentTracking(), 422, 'Payment tracking unavailable.');

        $method = $roomServiceOrder->bookingMethod
            ?: BookingMethod::query()->where('slug', 'cash')->where('is_active', true)->first();

        abort_unless($method, 422, 'Payment method missing.');

        $this->payments->markPaid($roomServiceOrder, $method, $roomServiceOrder->payment_reference);

        return back()->with('status', __('Kitchen bill marked as paid.'));
    }
}
