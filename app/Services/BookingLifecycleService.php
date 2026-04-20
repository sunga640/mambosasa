<?php

namespace App\Services;

use App\Http\Controllers\Site\BookingPortalController;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\DailyRevenue;
use App\Models\Invoice;
use App\Models\SystemSetting;
use App\Support\ActivityLogger;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Carbon;


final class BookingLifecycleService
{
    /**
     * @param SmsService $sms
     * @param PesapalService $pesapal (Huduma mpya ya Pesapal)
     */
    public function __construct(
        private SmsService $sms,
        private PesapalService $pesapal,
    ) {}

    public function handleNewBooking(Booking $booking): void
    {
        $booking->loadMissing(['room.branch', 'method']);

        $booking->ensureGuestAccessToken();
        $booking->refresh();

        $customer = Customer::syncFromBooking($booking);

        $invoice = $booking->invoice;
        if (! $invoice) {
            $invoice = Invoice::createForBooking($booking);
        }

        // --- TAYARISHA MALIPO YA PESAPAL KAMA IMECHAGULIWA ---
        $pesapalUrl = null;
        if ($booking->method?->slug === 'pesapal') {
            try {
                // Tunatengeneza Order Pesapal na kupata Redirect URL
                $response = $this->pesapal->createPaymentLink($booking);
                if (isset($response['redirect_url'])) {
                    $pesapalUrl = $response['redirect_url'];
                }
            } catch (\Throwable $e) {
                Log::error('Pesapal payment link generation failed: ' . $e->getMessage());
            }
        }

        ActivityLogger::log(
            'booking.created',
            auth()->user(),
            Booking::class,
            $booking->id,
            ['reference' => $booking->public_reference]
        );

        $settings = SystemSetting::current();
        $siteName = $settings->company_name ?? config('app.name');
        $deadline = $booking->payment_deadline_at;

        $portalUrl = $booking->guestPortalUrl();
        $invoiceUrl = $invoice->publicUrl();

        $reminderBody = __('Please complete payment for booking :ref at :site before :time.', [
            'ref' => $booking->public_reference,
            'site' => $siteName,
            'time' => $deadline ? $deadline->timezone(config('app.timezone'))->format('Y-m-d H:i') : __('the deadline'),
        ]);

        // 1. TAYARISHA MESEJI ZA SMS (mfupi: SMS nyingi hazistawi ujumbe mrefu + link mbili)
        $deadlineShort = $deadline ? $deadline->timezone(config('app.timezone'))->format('d/m H:i') : '';
        if ($pesapalUrl) {
            $smsContent = __(':ref — Pay before :time: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '—',
                'url' => $pesapalUrl,
            ]);
        } else {
            $smsContent = __(':ref — Complete payment by :time. Open: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '—',
                'url' => $portalUrl,
            ]);
        }

        // 2. TUMA EMAIL YA REMINDER NA INVOICE
        try {
            Mail::send('emails.booking-payment-reminder', [
                'booking' => $booking,
                'body' => $reminderBody,
                'portalUrl' => $portalUrl,
                'pesapalUrl' => $pesapalUrl, // Tunatuma link kwenye email template
            ], function ($mail) use ($booking): void {
                $mail->to($booking->email)
                    ->subject(__('Complete your payment — :ref', ['ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Booking reminder mail failed: '.$e->getMessage());
        }

        // 3. TUMA SMS
        try {
            $this->sms->send($booking->phone, $smsContent);
        } catch (\Throwable $e) {
            Log::error('Booking reminder SMS failed: '.$e->getMessage());
        }

        // Tuma Mail ya pili (Invoice)
        try {
            Mail::send('emails.booking-invoice', [
                'booking' => $booking,
                'invoice' => $invoice,
                'invoiceUrl' => $invoiceUrl,
                'portalUrl' => $portalUrl,
                'pesapalUrl' => $pesapalUrl,
            ], function ($mail) use ($booking, $invoice): void {
                $mail->to($booking->email)
                    ->subject(__('Invoice :num — :ref', ['num' => $invoice->number, 'ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Invoice mail failed: '.$e->getMessage());
        }
    }

    public function handlePaymentConfirmed(Booking $booking): void
    {
        $booking->loadMissing(['room.branch', 'method']);

        if (! $booking->user_id) {
            $plain = BookingPortalController::provisionUserForBooking($booking);
            if ($plain !== null && $plain !== '') {
                Cache::put(
                    BookingPortalController::cacheKey($booking),
                    $plain,
                    now()->addHours(48)
                );
            }
        }

        $booking->ensureGuestAccessToken();
        $booking->extendGuestTokenAfterPaymentConfirmed();
        $booking->refresh();
        $this->recordRevenue($booking);

        $settings = SystemSetting::current();
        $siteName = $settings->company_name ?? config('app.name');

        $portalUrl = $booking->guestPortalUrl();
        $body = __('Your payment for booking :ref is confirmed. Thank you — we look forward to hosting you at :site.', [
            'ref' => $booking->public_reference,
            'site' => $siteName,
        ]);
        $smsBody = $body.' '.__('Manage: :url', ['url' => $portalUrl]);

        try {
            Mail::send('emails.booking-payment-confirmed', [
                'booking' => $booking,
                'body' => $body,
                'portalUrl' => $portalUrl,
            ], function ($mail) use ($booking): void {
                $mail->to($booking->email)
                    ->subject(__('Payment confirmed — :ref', ['ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Payment confirmed mail failed: '.$e->getMessage());
        }

        try {
            $this->sms->send($booking->phone, $smsBody);
        } catch (\Throwable $e) {
            Log::error('Payment confirmed SMS failed: '.$e->getMessage());
        }

        ActivityLogger::log(
            'booking.payment_confirmed',
            auth()->user(),
            Booking::class,
            $booking->id,
            ['reference' => $booking->public_reference]
        );
    }

    public function notifyGuestSignedOut(Booking $booking): void
    {
        $booking->loadMissing(['room.branch']);
        $settings = SystemSetting::current();
        $siteName = $settings->company_name ?? config('app.name');

        $body = __('You have checked out from :site. Booking :ref is complete.', ['site' => $siteName, 'ref' => $booking->public_reference]);

        try {
            Mail::send('emails.booking-guest-signout', [
                'booking' => $booking,
                'body' => $body,
            ], function ($mail) use ($booking): void {
                $mail->to($booking->email)
                    ->subject(__('Checkout notice — :ref', ['ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Guest sign-out mail failed: '.$e->getMessage());
        }

        try {
            $this->sms->send($booking->phone, $body);
        } catch (\Throwable $e) {
            Log::error('Guest sign-out SMS failed: '.$e->getMessage());
        }
    }

    /**
     * Tuma tena ujumbe wa malipo (email + SMS) kwa booking bado inayosubiri malipo.
     */
    public function resendPendingPaymentNotifications(Booking $booking): void
    {
        $booking->loadMissing(['room.branch', 'method']);

        $invoice = $booking->invoice;
        if (! $invoice) {
            $invoice = Invoice::createForBooking($booking);
        }

        $pesapalUrl = null;
        if ($booking->method?->slug === 'pesapal') {
            try {
                $response = $this->pesapal->createPaymentLink($booking);
                $pesapalUrl = $response['redirect_url'] ?? null;
            } catch (\Throwable $e) {
                Log::error('Pesapal resend link failed: '.$e->getMessage());
            }
        }

        $settings = SystemSetting::current();
        $siteName = $settings->company_name ?? config('app.name');
        $deadline = $booking->payment_deadline_at;
        $portalUrl = $booking->guestPortalUrl();
        $invoiceUrl = $invoice->publicUrl();

        $reminderBody = __('Please complete payment for booking :ref at :site before :time.', [
            'ref' => $booking->public_reference,
            'site' => $siteName,
            'time' => $deadline ? $deadline->timezone(config('app.timezone'))->format('Y-m-d H:i') : __('the deadline'),
        ]);

        $deadlineShort = $deadline ? $deadline->timezone(config('app.timezone'))->format('d/m H:i') : '';
        if ($pesapalUrl) {
            $smsContent = __(':ref — Pay before :time: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '—',
                'url' => $pesapalUrl,
            ]);
        } else {
            $smsContent = __(':ref — Complete payment by :time. Open: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '—',
                'url' => $portalUrl,
            ]);
        }

        try {
            Mail::send('emails.booking-payment-reminder', [
                'booking' => $booking,
                'body' => $reminderBody,
                'portalUrl' => $portalUrl,
                'pesapalUrl' => $pesapalUrl,
            ], function ($mail) use ($booking): void {
                $mail->to($booking->email)
                    ->subject(__('Complete your payment — :ref', ['ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Booking reminder resend mail failed: '.$e->getMessage());
        }

        try {
            $this->sms->send($booking->phone, $smsContent);
        } catch (\Throwable $e) {
            Log::error('Booking reminder resend SMS failed: '.$e->getMessage());
        }

        try {
            Mail::send('emails.booking-invoice', [
                'booking' => $booking,
                'invoice' => $invoice,
                'invoiceUrl' => $invoiceUrl,
                'portalUrl' => $portalUrl,
                'pesapalUrl' => $pesapalUrl,
            ], function ($mail) use ($booking, $invoice): void {
                $mail->to($booking->email)
                    ->subject(__('Invoice :num — :ref', ['num' => $invoice->number, 'ref' => $booking->public_reference]));
            });
        } catch (\Throwable $e) {
            Log::error('Invoice mail (resend) failed: '.$e->getMessage());
        }

        ActivityLogger::log(
            'booking.reminder_resent',
            auth()->user(),
            Booking::class,
            $booking->id,
            ['reference' => $booking->public_reference]
        );
    }

    private function recordRevenue(Booking $booking): void
    {
        $day = Carbon::parse($booking->confirmed_at ?? now())->format('Y-m-d');

        try {
            $row = DailyRevenue::updateOrCreate(
                ['revenue_date' => $day],
                ['updated_at' => now()]
            );

            $row->increment('amount_total', (float) $booking->total_amount);
            $row->increment('bookings_count');

        } catch (\Illuminate\Database\UniqueConstraintViolationException $e) {
            $row = DailyRevenue::where('revenue_date', $day)->first();
            if ($row) {
                $row->increment('amount_total', (float) $booking->total_amount);
                $row->increment('bookings_count');
            }
        }
    }
}
