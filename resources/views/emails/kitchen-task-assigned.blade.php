<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Kitchen Task Assigned') }}</title>
</head>
<body style="margin:0;padding:24px;background:#0f1720;font-family:Arial,Helvetica,sans-serif;color:#e8f1fb;">
    <div style="max-width:680px;margin:0 auto;background:#1e2630;border:1px solid #2d4254;border-radius:18px;overflow:hidden;">
        <div style="padding:24px 28px;background:linear-gradient(135deg,#123b4c,#1e2630);border-bottom:1px solid #2d4254;">
            <div style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;color:#8fd3ff;font-weight:700;">{{ config('app.name', 'Kitchen System') }}</div>
            <h1 style="margin:12px 0 0;font-size:28px;line-height:1.2;color:#f8fbff;">{{ __('You have a new assigned kitchen task') }}</h1>
        </div>

        <div style="padding:28px;">
            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;">
                {{ __('Hello :name,', ['name' => $staffUser->name]) }}
            </p>
            <p style="margin:0 0 18px;font-size:15px;line-height:1.7;color:#c8d8e8;">
                {{ __('A new kitchen order has been assigned to your account. Please follow it up until completion and keep the status updated inside the kitchen dashboard.') }}
            </p>

            <div style="border:1px solid #2d4254;border-radius:16px;padding:20px;background:#18202a;">
                <div style="display:grid;gap:12px;">
                    <div>
                        <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Order reference') }}</div>
                        <div style="font-size:18px;font-weight:700;color:#f8fbff;margin-top:4px;">{{ $order->public_reference ?: '#'.$order->id }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Guest and room') }}</div>
                        <div style="font-size:15px;color:#e8f1fb;margin-top:4px;">
                            {{ $order->guest_name ?: __('Portal guest') }}
                            @if ($order->room?->name)
                                · {{ $order->room->name }}@if($order->room?->room_number) / #{{ $order->room->room_number }}@endif
                            @endif
                        </div>
                    </div>
                    <div>
                        <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Assigned items') }}</div>
                        <div style="font-size:15px;color:#e8f1fb;margin-top:4px;">{{ $itemsText ?: __('No items captured') }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Current status') }}</div>
                        <div style="font-size:15px;color:#9ff2c0;margin-top:4px;font-weight:700;">{{ $order->statusEnum()->label() }}</div>
                    </div>
                    <div>
                        <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Payment state') }}</div>
                        <div style="font-size:15px;color:#e8f1fb;margin-top:4px;">{{ $order->paymentStatusLabel() }}</div>
                    </div>
                    @if ($assignedBy)
                        <div>
                            <div style="font-size:12px;letter-spacing:.1em;text-transform:uppercase;color:#7aa6c7;font-weight:700;">{{ __('Assigned by') }}</div>
                            <div style="font-size:15px;color:#e8f1fb;margin-top:4px;">{{ $assignedBy->name }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <p style="margin:20px 0 0;font-size:14px;line-height:1.7;color:#c8d8e8;">
                {{ __('Open your kitchen dashboard to update the task when it moves from pending to preparing, delivered, or paid.') }}
            </p>
        </div>
    </div>
</body>
</html>
