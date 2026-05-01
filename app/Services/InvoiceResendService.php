<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Invoice;
use App\Support\GuestEmailTemplateManager;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class InvoiceResendService
{
    public function __construct(
        private SmsService $sms,
        private GuestEmailTemplateManager $guestEmailTemplates,
    ) {}

    public function resend(Booking $booking): void
    {
        $booking->loadMissing(['invoice', 'room.branch', 'method']);
        $invoice = $booking->invoice;
        if (! $invoice) {
            $invoice = Invoice::createForBooking($booking);
        }

        $invoiceUrl = $invoice->publicUrl();
        $emailTemplate = $this->guestEmailTemplates->render('invoice_ready', $booking, $invoice);

        if ($emailTemplate['enabled']) {
            try {
                Mail::send('emails.guest-template', [
                    'emailTemplate' => $emailTemplate,
                ], function ($mail) use ($booking, $emailTemplate): void {
                    $mail->to($booking->email)
                        ->subject($emailTemplate['subject']);
                });
            } catch (\Throwable $e) {
                Log::error('Invoice resend mail failed: '.$e->getMessage());
            }
        }

        $smsBody = __('Your invoice :num: :url', ['num' => $invoice->number, 'url' => $invoiceUrl]);
        try {
            $this->sms->send($booking->phone, $smsBody);
        } catch (\Throwable $e) {
            Log::error('Invoice resend SMS failed: '.$e->getMessage());
        }
    }
}
