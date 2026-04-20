<?php

return [
    /*
    | Internal user used for room-service orders placed via the public guest booking link
    | when the booking has no registered member account.
    */
    'guest_portal_user_email' => env('HOTEL_GUEST_PORTAL_USER_EMAIL', 'guest-portal@hotel.internal'),
];
