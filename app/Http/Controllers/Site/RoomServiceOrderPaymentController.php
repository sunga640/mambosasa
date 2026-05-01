<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\BookingMethod;
use App\Models\RoomServiceOrder;
use App\Services\PesapalService;
use App\Services\RoomServiceOrderPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RoomServiceOrderPaymentController extends Controller
{
    public function __construct(
        private readonly RoomServiceOrderPaymentService $payments,
        private readonly PesapalService $pesapal,
    ) {}

    public function store(Request $request, string $reference): RedirectResponse
    {
        $order = RoomServiceOrder::query()
            ->with(['booking', 'bookingMethod'])
            ->where('public_reference', $reference)
            ->firstOrFail();

        $data = $request->validate([
            'booking_method_id' => ['nullable', 'integer', 'exists:booking_methods,id'],
            'payment_choice' => ['nullable', 'in:cash,online,bill_later'],
        ]);

        abort_unless(RoomServiceOrder::supportsPaymentTracking(), 422, 'Payment tracking columns are missing.');

        if ($order->isPaid()) {
            return redirect()->away($order->paymentReturnUrl())
                ->with('status', __('This order is already marked as paid.'));
        }

        $choice = (string) ($data['payment_choice'] ?? 'online');
        $method = null;

        if ($choice === 'cash' || $choice === 'bill_later') {
            $method = $this->payments->cashMethod() ?: $order->bookingMethod;
            abort_unless($method, 422, 'Cash payment method missing.');
        } else {
            abort_if(blank($data['booking_method_id']), 422, __('Choose an online payment method for this order.'));

            $method = BookingMethod::query()
                ->whereKey($data['booking_method_id'])
                ->where('is_active', true)
                ->where('method_type', 'online')
                ->firstOrFail();
        }

        if ($choice === 'bill_later') {
            $this->payments->markBillLater($order, $method);

            return redirect()->away($order->paymentReturnUrl())
                ->with('status', __('This order will stay on the room bill until checkout at reception.'));
        }

        if ($choice === 'cash') {
            $this->payments->markCashPending($order, $method);

            return redirect()->away($order->paymentReturnUrl())
                ->with('status', __('Cash option saved. Kitchen can now confirm payment after receiving the money.'));
        }

        if ($this->payments->supportsAutomaticGateway($method)) {
            $this->payments->markProcessing($order, $method);
            try {
                $response = $this->pesapal->createRoomServicePaymentLink($order, $method);
                $url = $response['redirect_url'] ?? null;

                if ($url) {
                    return redirect()->away($url);
                }
            } catch (\Throwable) {
            }

            return redirect()->away($order->paymentReturnUrl())
                ->withErrors(['payment' => __('Could not start the online payment for this order right now.')]);
        }

        return redirect()->away($order->paymentReturnUrl())
            ->withErrors(['payment' => __('This payment method is saved in the system, but automatic confirmation will start only after its live gateway keys are configured correctly.')]);
    }
}
