<?php

namespace App\Http\Controllers\Site;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\RoomServiceOrder;
use App\Services\BookingLifecycleService;
use App\Services\PesapalService;
use App\Services\RoomServiceOrderPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PesapalController extends Controller
{
    public function __construct(
        private BookingLifecycleService $lifecycle,
        private RoomServiceOrderPaymentService $roomServicePayments,
    ) {}

    /**
     * Redirect mteja kwenye ukurasa wa malipo ya Pesapal (link inapatikana kwa API).
     */
    public function payNow(string $reference)
    {
        $booking = Booking::query()
            ->where('public_reference', $reference)
            ->with('method')
            ->firstOrFail();

        if ($booking->status !== BookingStatus::PendingPayment) {
            return redirect()
                ->route('site.booking.confirmation', ['reference' => $reference])
                ->with('status', __('This booking is not awaiting payment.'));
        }

        if ($booking->method?->slug !== 'pesapal') {
            return redirect()
                ->route('site.booking.confirmation', ['reference' => $reference])
                ->with('status', __('Online payment is not used for this booking.'));
        }

        try {
            $response = app(PesapalService::class)->createPaymentLink($booking);
            $url = $response['redirect_url'] ?? null;

            if (! $url) {
                Log::error('Pesapal pay-now missing redirect', [
                    'reference' => $reference,
                    'response' => $response,
                ]);

                return redirect()
                    ->route('site.booking.confirmation', ['reference' => $reference])
                    ->withErrors(['payment' => __('Could not start payment. Please try again or contact support.')]);
            }

            return redirect()->away($url);
        } catch (\Throwable $e) {
            Log::error('Pesapal pay-now failed: '.$e->getMessage());

            return redirect()
                ->route('site.booking.confirmation', ['reference' => $reference])
                ->withErrors(['payment' => __('Payment service is temporarily unavailable.')]);
        }
    }

    // 1. Inaitwa mteja akimaliza kulipa (Mteja anarudishwa hapa)
    public function callback(Request $request)
    {
        $reference = $request->query('OrderMerchantReference');

        if (! $reference) {
            return redirect()
                ->route('site.booking')
                ->with('status', __('Payment update received. We are checking your booking status.'));
        }

        $order = RoomServiceOrder::query()
            ->with('booking')
            ->where('public_reference', $reference)
            ->first();
        if ($order) {
            return redirect()->away($order->paymentReturnUrl())
                ->with('status', __('Payment update received for order :ref. We are checking your payment status.', ['ref' => $reference]));
        }

        return redirect()
            ->route('site.booking.confirmation', ['reference' => $reference])
            ->with('status', __('Payment update received for booking :ref. We are checking your booking status.', ['ref' => $reference]));
    }

    // 2. IPN - Inaitwa na Pesapal Server kwa siri (Muhimu kwa kuji-confirm)
    public function ipn(Request $request)
    {
        Log::info('Pesapal IPN Received', $request->all());

        $reference = $request->input('OrderMerchantReference');
        $status = strtoupper((string) $request->input('Status')); // Pesapal v3 inatuma Status hapa

        $booking = Booking::where('public_reference', $reference)->first();
        $order = RoomServiceOrder::query()->with('bookingMethod')->where('public_reference', $reference)->first();

        if ($booking && $booking->status === BookingStatus::PendingPayment && $status === 'COMPLETED') {
            // Hapa ndipo system inaji-confirm yenyewe, ina-activate booking, na kutuma credentials.
            $this->lifecycle->confirmPayment($booking);
        }

        if ($order && $status === 'COMPLETED') {
            $method = $order->bookingMethod;
            if ($method) {
                $this->roomServicePayments->markPaid($order, $method, (string) $request->input('OrderTrackingId'));
            }
        }

        return response()->json(['status' => 'OK']);
    }
}
