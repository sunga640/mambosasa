<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="font-family:system-ui,sans-serif;line-height:1.5;color:#111;max-width:560px;margin:0;padding:24px;">
    <h1 style="font-size:1.25rem;margin:0 0 1rem;">{{ __('Your invoice is ready') }}</h1>
    <p>{{ __('Thank you for choosing us. Your invoice :num is available online.', ['num' => $invoice->number]) }}</p>
    <p style="margin:1.5rem 0;">
        <a href="{{ $invoiceUrl }}" style="display:inline-block;padding:12px 20px;background:#c41e3a;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">{{ __('View & print invoice') }}</a>
    </p>
    <p class="text-13" style="opacity:.75;word-break:break-all;">{{ $invoiceUrl }}</p>
    <p style="margin-top:1.5rem;">
        <a href="{{ $portalUrl ?? '#' }}" style="display:inline-block;padding:10px 18px;background:#122223;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;">{{ __('My booking details') }}</a>
    </p>
    <p style="font-size:0.85rem;opacity:.75;word-break:break-all;">{{ $portalUrl ?? '' }}</p>
    <p style="margin-top:1.5rem;"><strong>{{ __('Reference') }}:</strong> {{ $booking->public_reference }}</p>
    @if(isset($pesapalUrl))
    <a href="{{ $pesapalUrl }}" style="background:#2563eb; color:#fff; padding:10px 20px; text-decoration:none; border-radius:5px;">
        PAY VIA MOBILE MONEY / CARD
    </a>
@endif
</body>
</html>
