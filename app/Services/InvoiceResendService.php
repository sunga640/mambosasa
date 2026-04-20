<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class InvoiceResendService
{
    public function __construct(
        private SmsService $sms,
    ) {}

    public function resend(Booking $booking): void
    {
        $booking->loadMissing(['invoice', 'room.branch', 'method']);
        $invoice = $booking->invoice;
        if (! $invoice) {
            $invoice = Invoice::createForBooking($booking);
        }

        $invoiceUrl = $invoice->publicUrl();

        try {
            Mail::send('emails.booking-invoice', [
                'booking' => $booking,
                'invoice' => $invoice,
                'invoiceUrl' => $invoiceUrl,
            ], function ($mail) use ($booking, $invoice): void {
                $mail->to($booking->email)
                    ->subject(__('Invoice :num — :ref', ['num' => $invoice->number, 'ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Invoice resend mail failed: '.$e->getMessage());
        }

        $smsBody = __('Your invoice :num: :url', ['num' => $invoice->number, 'url' => $invoiceUrl]);
        try {
            $this->sms->send($booking->phone, $smsBody);
        } catch (\Throwable $e) {
            Log::error('Invoice resend SMS failed: '.$e->getMessage());
        }
    }
}
