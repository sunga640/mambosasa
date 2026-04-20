<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600&family=Jost:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <title>@yield('title', config('app.name'))</title>
    <style>
        .flatpickr-calendar { border-radius: 12px; border: 1px solid #e5e7eb; box-shadow: 0 12px 30px rgba(0,0,0,.14); font-family: inherit; }
        .flatpickr-months .flatpickr-month { height: 46px; }
        .flatpickr-current-month { font-size: 1rem; padding-top: 8px; }
        .flatpickr-prev-month svg, .flatpickr-next-month svg { width: 14px !important; height: 14px !important; }
        .flatpickr-day { border-radius: 8px; }
        .flatpickr-day.today { border-color: #0f172a; }
        .flatpickr-day.selected, .flatpickr-day.startRange, .flatpickr-day.endRange { background: #1d4ed8; border-color: #1d4ed8; }

    </style>
    @stack('head')
</head>
<body style="margin:0;min-height:100vh;background:#f1f5f9;font-family:Jost,system-ui,sans-serif;color:#0f172a;">
    @yield('content')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            if (!window.flatpickr) return;
            document.querySelectorAll('input[type="date"]:not([data-native-date="1"])').forEach(function (input) {
                if (input.dataset.flatpickrInitialized === '1') return;
                var minDate = input.getAttribute('min') || null;
                var maxDate = input.getAttribute('max') || null;
                var defaultDate = input.value || null;
                input.dataset.flatpickrInitialized = '1';
                input.type = 'text';
                flatpickr(input, {
                    dateFormat: 'Y-m-d',
                    defaultDate: defaultDate,
                    minDate: minDate,
                    maxDate: maxDate,
                    allowInput: true,
                    disableMobile: true
                });
            });
        })();
    </script>
    @stack('scripts')
</body>
</html>
