<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Http\Controllers\Site\BookingPortalController;
use App\Mail\GuestLoginCredentialsMail;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\DailyRevenue;
use App\Models\Invoice;
use App\Models\SystemSetting;
use App\Support\ActivityLogger;
use App\Support\GuestEmailTemplateManager;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

final class BookingLifecycleService
{
    public function __construct(
        private SmsService $sms,
        private PesapalService $pesapal,
        private GuestEmailTemplateManager $guestEmailTemplates,
    ) {}

    public function handleNewBooking(Booking $booking): void
    {
        $booking->loadMissing(['room.branch', 'method']);

        $booking->ensureGuestAccessToken();
        $booking->refresh();

        Customer::syncFromBooking($booking);

        $invoice = $booking->invoice;
        if (! $invoice) {
            $invoice = Invoice::createForBooking($booking);
        }

        $pesapalUrl = null;
        if ($booking->method?->slug === 'pesapal') {
            try {
                $response = $this->pesapal->createPaymentLink($booking);
                if (isset($response['redirect_url'])) {
                    $pesapalUrl = $response['redirect_url'];
                }
            } catch (\Throwable $e) {
                Log::error('Pesapal payment link generation failed: '.$e->getMessage());
            }
        }

        ActivityLogger::log(
            'booking.created',
            auth()->user(),
            Booking::class,
            $booking->id,
            ['reference' => $booking->public_reference]
        );

        $deadline = $booking->payment_deadline_at;
        $portalUrl = $booking->guestPortalUrl();

        $deadlineShort = $deadline ? $deadline->timezone(config('app.timezone'))->format('d/m H:i') : '';
        if ($pesapalUrl) {
            $smsContent = __(':ref - Pay before :time: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '-',
                'url' => $pesapalUrl,
            ]);
        } else {
            $smsContent = __(':ref - Complete payment by :time. Open: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '-',
                'url' => $portalUrl,
            ]);
        }

        $pendingTemplate = $this->guestEmailTemplates->render('pending_payment', $booking, $invoice, [
            'payment_url' => $pesapalUrl ?: $portalUrl,
        ]);
        $invoiceTemplate = $this->guestEmailTemplates->render('invoice_ready', $booking, $invoice);

        $this->sendTemplateMail($booking, $pendingTemplate, 'Booking reminder mail failed');

        try {
            $this->sms->send($booking->phone, $smsContent);
        } catch (\Throwable $e) {
            Log::error('Booking reminder SMS failed: '.$e->getMessage());
        }

        $this->sendTemplateMail($booking, $invoiceTemplate, 'Invoice mail failed');
    }

    public function confirmPayment(Booking $booking, bool $sendCredentials = true): Booking
    {
        $booking->loadMissing(['room.branch', 'method', 'user']);

        if ($booking->status === BookingStatus::Confirmed) {
            return $booking;
        }

        $plainPassword = BookingPortalController::provisionUserForBooking($booking);
        $booking->refresh();

        $booking->forceFill([
            'status' => BookingStatus::Confirmed,
            'confirmed_at' => $booking->confirmed_at ?? now(),
            'payment_deadline_at' => null,
        ])->save();

        $booking->refresh();

        Customer::syncFromBooking($booking);
        if (! $booking->invoice) {
            Invoice::createForBooking($booking);
            $booking->refresh();
        }

        $this->handlePaymentConfirmed($booking);

        if ($sendCredentials && filled($plainPassword)) {
            $credentialsTemplate = $this->guestEmailTemplates->render('guest_credentials', $booking->fresh(), null, [
                'password' => $plainPassword,
            ]);

            if ($credentialsTemplate['enabled']) {
                try {
                    Mail::to($booking->email)->send(
                        new GuestLoginCredentialsMail($booking->fresh(), $plainPassword, $credentialsTemplate)
                    );
                } catch (\Throwable $e) {
                    Log::error('Guest credentials mail failed: '.$e->getMessage());
                }
            }
        }

        return $booking->fresh();
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

        $siteName = SystemSetting::current()->company_name ?? config('app.name');
        $portalUrl = $booking->guestPortalUrl();
        $body = __('Your payment for booking :ref is confirmed. Thank you - we look forward to hosting you at :site.', [
            'ref' => $booking->public_reference,
            'site' => $siteName,
        ]);
        $smsBody = $body.' '.__('Manage: :url', ['url' => $portalUrl]);

        $emailTemplate = $this->guestEmailTemplates->render('payment_confirmed', $booking);
        $this->sendTemplateMail($booking, $emailTemplate, 'Payment confirmed mail failed');

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
        $siteName = SystemSetting::current()->company_name ?? config('app.name');
        $body = __('You have checked out from :site. Booking :ref is complete.', [
            'site' => $siteName,
            'ref' => $booking->public_reference,
        ]);

        $emailTemplate = $this->guestEmailTemplates->render('guest_signout', $booking);
        $this->sendTemplateMail($booking, $emailTemplate, 'Guest sign-out mail failed');

        try {
            $this->sms->send($booking->phone, $body);
        } catch (\Throwable $e) {
            Log::error('Guest sign-out SMS failed: '.$e->getMessage());
        }
    }

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

        $deadline = $booking->payment_deadline_at;
        $portalUrl = $booking->guestPortalUrl();

        $deadlineShort = $deadline ? $deadline->timezone(config('app.timezone'))->format('d/m H:i') : '';
        if ($pesapalUrl) {
            $smsContent = __(':ref - Pay before :time: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '-',
                'url' => $pesapalUrl,
            ]);
        } else {
            $smsContent = __(':ref - Complete payment by :time. Open: :url', [
                'ref' => $booking->public_reference,
                'time' => $deadlineShort ?: '-',
                'url' => $portalUrl,
            ]);
        }

        $pendingTemplate = $this->guestEmailTemplates->render('pending_payment', $booking, $invoice, [
            'payment_url' => $pesapalUrl ?: $portalUrl,
        ]);
        $invoiceTemplate = $this->guestEmailTemplates->render('invoice_ready', $booking, $invoice);

        $this->sendTemplateMail($booking, $pendingTemplate, 'Booking reminder resend mail failed');

        try {
            $this->sms->send($booking->phone, $smsContent);
        } catch (\Throwable $e) {
            Log::error('Booking reminder resend SMS failed: '.$e->getMessage());
        }

        $this->sendTemplateMail($booking, $invoiceTemplate, 'Invoice mail (resend) failed');

        ActivityLogger::log(
            'booking.reminder_resent',
            auth()->user(),
            Booking::class,
            $booking->id,
            ['reference' => $booking->public_reference]
        );
    }

    /**
     * @param  array<string, mixed>  $emailTemplate
     */
    private function sendTemplateMail(Booking $booking, array $emailTemplate, string $logMessage): void
    {
        if (! ($emailTemplate['enabled'] ?? false)) {
            return;
        }

        try {
            Mail::send('emails.guest-template', [
                'emailTemplate' => $emailTemplate,
            ], function ($mail) use ($booking, $emailTemplate): void {
                $mail->to($booking->email)
                    ->subject((string) $emailTemplate['subject']);
            });
        } catch (\Throwable $e) {
            Log::error($logMessage.': '.$e->getMessage());
        }
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
