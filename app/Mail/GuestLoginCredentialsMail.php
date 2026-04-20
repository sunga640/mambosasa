<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuestLoginCredentialsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;
    public $password;

    public function __construct(Booking $booking, $password)
    {
        $this->booking = $booking;
        $this->password = $password;
    }

    public function build()
    {
        return $this->subject(__('Your Booking Confirmation & Login Details'))
                    ->view('emails.guest_credentials');
    }
}
