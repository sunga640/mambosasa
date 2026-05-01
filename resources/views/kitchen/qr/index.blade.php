@extends('layouts.kitchen')

@section('title', __('Kitchen QR Codes'))

@section('content')
    <div class="k-grid">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Room QR Menu Access') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Generate a room-specific kitchen QR code, print it, and let guests scan directly into the in-room dining menu.') }}</p>
        </div>

        <section class="k-card">
            <div class="k-grid" style="grid-template-columns:repeat(auto-fit,minmax(280px,1fr));">
                @foreach ($rooms as $room)
                    @php $code = $codes->get($room->id); @endphp
                    <article class="k-card" style="background:var(--brand-theme-surface-card);">
                        <div class="k-actions" style="justify-content:space-between;">
                            <div>
                                <h3 style="margin-bottom:.25rem;">{{ $room->name }}</h3>
                                <div class="text-13 k-muted">{{ $room->branch?->name }} · #{{ $room->room_number ?: '—' }}</div>
                            </div>
                            <form method="POST" action="{{ route('kitchen.qr.store', $room) }}">
                                @csrf
                                <button class="dash-btn dash-btn--primary" type="submit">{{ $code ? __('Regenerate') : __('Generate') }}</button>
                            </form>
                        </div>

                        @if ($code)
                            @php $url = route('site.kitchen-menu.show', $code->token); @endphp
                            <div id="qr-room-{{ $room->id }}" class="mt-15" data-qr-url="{{ $url }}" style="display:flex;justify-content:center;padding:1rem;background:#fff;"></div>
                            <div class="mt-10 text-12" style="word-break:break-all;">{{ $url }}</div>
                            <div class="k-actions mt-10">
                                <a href="{{ $url }}" target="_blank" rel="noopener" class="dash-btn dash-btn--ghost">{{ __('Preview menu') }}</a>
                                <button type="button" class="dash-btn dash-btn--ghost" data-print-qr="qr-room-{{ $room->id }}" data-print-room="{{ $room->name }}">{{ __('Print') }}</button>
                                <button type="button" class="dash-btn dash-btn--ghost" data-download-qr="qr-room-{{ $room->id }}" data-download-room="{{ $room->name }}">{{ __('Download') }}</button>
                                <span class="text-12 k-muted">{{ __('Last scan') }}: {{ $code->last_scanned_at?->format('Y-m-d H:i') ?: __('Not yet') }}</span>
                            </div>
                        @else
                            <p class="text-13 k-muted mt-15">{{ __('No QR generated yet for this room.') }}</p>
                        @endif
                    </article>
                @endforeach
            </div>
            <div class="mt-20">{{ $rooms->links() }}</div>
        </section>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js" crossorigin="anonymous"></script>
    <script>
        document.querySelectorAll('[data-qr-url]').forEach(function (el) {
            if (typeof QRCode === 'undefined') return;
            new QRCode(el, {
                text: el.getAttribute('data-qr-url'),
                width: 160,
                height: 160,
                colorDark: '#122223',
                colorLight: '#ffffff',
            });
        });

        document.querySelectorAll('[data-download-qr]').forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-download-qr');
                var roomName = (button.getAttribute('data-download-room') || 'room-qr').replace(/\s+/g, '-').toLowerCase();
                var target = document.getElementById(targetId);
                var canvas = target ? target.querySelector('canvas') : null;
                if (!canvas) return;

                var link = document.createElement('a');
                link.href = canvas.toDataURL('image/png');
                link.download = roomName + '-kitchen-qr.png';
                link.click();
            });
        });

        document.querySelectorAll('[data-print-qr]').forEach(function (button) {
            button.addEventListener('click', function () {
                var targetId = button.getAttribute('data-print-qr');
                var roomName = button.getAttribute('data-print-room') || 'Room';
                var target = document.getElementById(targetId);
                var canvas = target ? target.querySelector('canvas') : null;
                var qrUrl = target ? target.getAttribute('data-qr-url') : '';
                if (!canvas) return;

                var imageData = canvas.toDataURL('image/png');
                var popup = window.open('', '_blank', 'width=720,height=840');
                if (!popup) return;

                popup.document.write('<html><head><title>' + roomName + ' QR</title><style>body{font-family:Arial,sans-serif;padding:32px;text-align:center}img{max-width:320px;margin:24px auto}small{display:block;word-break:break-all;margin-top:16px}</style></head><body><h1>' + roomName + '</h1><p>Kitchen menu QR code</p><img src="' + imageData + '" alt="QR Code"><small>' + qrUrl + '</small></body></html>');
                popup.document.close();
                popup.focus();
                popup.print();
            });
        });
    </script>
@endpush
