<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Services\InvoiceResendService;
use Illuminate\Http\RedirectResponse;

class InvoiceController extends Controller
{
    public function resend(Invoice $invoice, InvoiceResendService $resend): RedirectResponse
    {
        $booking = $invoice->booking;
        if (! $booking) {
            return back()->withErrors(['invoice' => __('Booking missing.')]);
        }

        $resend->resend($booking);

        return back()->with('status', __('Invoice email and SMS resent.'));
    }
}
