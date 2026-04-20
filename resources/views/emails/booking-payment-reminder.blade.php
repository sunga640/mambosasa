<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,sans-serif;line-height:1.5;color:#111;max-width:560px;margin:0;padding:24px;">
    <p>{{ $body }}</p>
    <p style="margin:1.25rem 0;">
        <a href="{{ $portalUrl ?? '#' }}" style="display:inline-block;padding:12px 20px;background:#122223;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">{{ __('Open my booking') }}</a>
    </p>
    <p style="font-size:0.9rem;opacity:.8;word-break:break-all;">{{ $portalUrl ?? '' }}</p>
    <p style="margin-top:1.5rem;">
        <strong>{{ __('Booking reference') }}:</strong> {{ $booking->public_reference }}<br>
        <strong>{{ __('Amount') }}:</strong> {{ number_format((float) $booking->total_amount, 0) }}
    </p>
</body>
</html>
