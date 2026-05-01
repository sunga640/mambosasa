<?php

namespace App\Mail;

use App\Models\Booking;
use App\Support\GuestEmailTemplateManager;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestLoginCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $password;
    public $emailTemplate;

    public function __construct(Booking $booking, $password, ?array $emailTemplate = null)
    {
        $this->booking = $booking;
        $this->password = $password;
        $this->emailTemplate = $emailTemplate
            ?? app(GuestEmailTemplateManager::class)->render('guest_credentials', $booking, null, [
                'password' => (string) $password,
            ]);
    }

    public function build()
    {
        return $this->subject($this->emailTemplate['subject'] ?? __('Your Booking Confirmation & Login Details'))
                    ->view('emails.guest-template', [
                        'emailTemplate' => $this->emailTemplate,
                    ]);
    }
}
