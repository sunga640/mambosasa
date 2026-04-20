<?php

namespace App\Http\Controllers\Site;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingLifecycleService;
use App\Services\PesapalService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PesapalController extends Controller
{
    public function __construct(private BookingLifecycleService $lifecycle) {}

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

        return redirect()->route('dashboard')
            ->with('status', "Malipo ya booking #$reference yanashughulikiwa.");
    }

    // 2. IPN - Inaitwa na Pesapal Server kwa siri (Muhimu kwa kuji-confirm)
    public function ipn(Request $request)
    {
        Log::info('Pesapal IPN Received', $request->all());

        $reference = $request->input('OrderMerchantReference');
        $status = $request->input('Status'); // Pesapal v3 inatuma Status hapa

        $booking = Booking::where('public_reference', $reference)->first();

        if ($booking && $status === 'COMPLETED') {
            // Hapa ndipo system inaji-confirm yenyewe na kurekodi Revenue
            $this->lifecycle->handlePaymentConfirmed($booking);
        }

        return response()->json(['status' => 'OK']);
    }
}
