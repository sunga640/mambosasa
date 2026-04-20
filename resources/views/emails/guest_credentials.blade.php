<!DOCTYPE html>
<html>
<head>
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>Habari {{ $booking->first_name }},</h2>
    <p>Malipo yako ya booking <strong>#{{ $booking->public_reference }}</strong> yamethibitishwa na sasa booking yako ipo Active.</p>

    <div style="background: #f4f4f4; padding: 15px; border-radius: 8px; margin-top: 20px;">
        <h3 style="margin-top: 0;">Maelezo ya Login:</h3>
        <p>Tumekutengenezea account ili uweze kusimamia stay yako, kuomba huduma (Room Service), na kuona invoice zako.</p>
        <p><strong>Link:</strong> <a href="{{ url('/login') }}">{{ url('/login') }}</a></p>
        <p><strong>Username:</strong> {{ $booking->email }}</p>
        <p><strong>Password:</strong> <span style="color: #e63946; font-weight: bold;">{{ $password }}</span></p>
    </div>

    <p style="margin-top: 20px;">Karibu sana na ufurahie stay yako!</p>
    <p>Asante,<br>Management.</p>
</body>
</html>
