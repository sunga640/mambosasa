<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,sans-serif;line-height:1.5;color:#111;max-width:560px;margin:0;padding:24px;">
    <p>{{ $body }}</p>
    <p style="margin-top:1.5rem;">
        <strong>{{ __('Booking reference') }}:</strong> {{ $booking->public_reference }}<br>
        @if ($booking->room)
        <strong>{{ __('Room') }}:</strong> {{ $booking->room->room_number ?: $booking->room->name }}<br>
        @endif
    </p>
</body>
</html>
