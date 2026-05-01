<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body style="margin:0;padding:24px;background:#eef2f6;font-family:Arial,sans-serif;color:#1f2933;">
    <div style="max-width:640px;margin:0 auto;background:#ffffff;border-radius:18px;overflow:hidden;border:1px solid #d7e0ea;">
        <div style="padding:28px 30px;background:linear-gradient(135deg, {{ $emailTemplate['accent_color'] ?? '#1f7ae0' }}, #1f2933);color:#ffffff;">
            <div style="font-size:12px;letter-spacing:.18em;text-transform:uppercase;opacity:.78;">{{ $emailTemplate['hotel_name'] ?? config('app.name') }}</div>
            @if (!empty($emailTemplate['title']))
                <h1 style="margin:12px 0 0;font-size:28px;line-height:1.2;color:#ffffff;">{{ $emailTemplate['title'] }}</h1>
            @endif
        </div>

        <div style="padding:28px 30px 32px;">
            @if (!empty($emailTemplate['intro']))
                <p style="margin:0 0 18px;font-size:16px;line-height:1.7;color:#1f2933;">{!! nl2br(e($emailTemplate['intro'])) !!}</p>
            @endif

            @if (!empty($emailTemplate['body']))
                <p style="margin:0 0 18px;font-size:15px;line-height:1.8;color:#45556c;">{!! nl2br(e($emailTemplate['body'])) !!}</p>
            @endif

            @if (!empty($emailTemplate['highlight']))
                <div style="margin:0 0 22px;padding:16px 18px;border-radius:14px;background:#f5f8fb;border-left:4px solid {{ $emailTemplate['accent_color'] ?? '#1f7ae0' }};font-size:15px;line-height:1.7;color:#1f2933;">
                    {!! nl2br(e($emailTemplate['highlight'])) !!}
                </div>
            @endif

            @if (!empty($emailTemplate['primary_button_label']) && !empty($emailTemplate['primary_button_url']))
                <div style="margin:0 0 14px;">
                    <a href="{{ $emailTemplate['primary_button_url'] }}" style="display:inline-block;padding:13px 20px;border-radius:10px;background:{{ $emailTemplate['accent_color'] ?? '#1f7ae0' }};color:#ffffff;text-decoration:none;font-weight:700;">
                        {{ $emailTemplate['primary_button_label'] }}
                    </a>
                </div>
            @endif

            @if (!empty($emailTemplate['secondary_button_label']) && !empty($emailTemplate['secondary_button_url']))
                <div style="margin:0 0 20px;">
                    <a href="{{ $emailTemplate['secondary_button_url'] }}" style="display:inline-block;padding:12px 18px;border-radius:10px;border:1px solid #cfd8e3;background:#ffffff;color:#1f2933;text-decoration:none;font-weight:600;">
                        {{ $emailTemplate['secondary_button_label'] }}
                    </a>
                </div>
            @endif

            @if (!empty($emailTemplate['details_enabled']) && !empty($emailTemplate['details']))
                <div style="margin:24px 0 20px;padding:18px;border-radius:14px;background:#f8fafc;border:1px solid #dde5ee;">
                    <div style="font-size:13px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#66758a;margin-bottom:12px;">{{ __('Booking details') }}</div>
                    @foreach ($emailTemplate['details'] as $detail)
                        <div style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-top:{{ $loop->first ? '0' : '1px solid #e4ebf2' }};">
                            <span style="font-size:13px;color:#66758a;">{{ $detail['label'] }}</span>
                            <span style="font-size:13px;font-weight:700;color:#1f2933;text-align:right;">{{ $detail['value'] }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            @if (!empty($emailTemplate['footer_note']))
                <p style="margin:0;font-size:13px;line-height:1.7;color:#66758a;">{!! nl2br(e($emailTemplate['footer_note'])) !!}</p>
            @endif
        </div>
    </div>
</body>
</html>
