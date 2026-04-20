<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\Invoice;
use App\Services\InvoiceResendService;
use Illuminate\Http\RedirectResponse;

class ReceptionInvoiceController extends Controller
{
    use InteractsWithStaffScope;

    public function resend(Invoice $invoice, InvoiceResendService $resend): RedirectResponse
    {
        $booking = $invoice->booking;
        if (! $booking) {
            return back()->withErrors(['invoice' => __('Booking missing.')]);
        }

        $this->ensureBookingInScope($booking);

        $resend->resend($booking);

        return back()->with('status', __('Invoice email and SMS resent.'));
    }
}
