@extends('layouts.admin')

@section('title', __('Bookings'))

@section('content')
    <style>
        .bookings-table td,
        .bookings-table th {
            vertical-align: middle;
        }
        .bookings-table .booking-days-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 2.5rem;
            padding: .42rem .7rem !important;
            line-height: 1.45;
            text-align: center;
            white-space: normal;
        }
        .bookings-table .admin-actions {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            flex-wrap: nowrap;
            white-space: nowrap;
        }
        .bookings-table .admin-actions form {
            display: inline-flex !important;
            margin: 0;
        }
        .bookings-table .admin-actions a,
        .bookings-table .admin-actions span,
        .bookings-table .admin-actions button {
            margin-left: 0 !important;
        }
    </style>
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Bookings') }}</h1>
    </div>

    <form method="GET" action="{{ route('admin.bookings.index') }}" class="form-row mt-20" style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <label for="status">{{ __('Status') }}</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">{{ __('All') }}</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}" @selected($statusFilter === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div style="flex:1;min-width:200px;max-width:320px;">
            <label for="q">{{ __('Search') }}</label>
            <input type="text" name="q" id="q" value="{{ $q }}" placeholder="{{ __('Reference, email, name…') }}">
        </div>
        <div>
            <label for="from">{{ __('From date') }}</label>
            <input type="date" name="from" id="from" value="{{ $from ?? '' }}">
        </div>
        <div>
            <label for="to">{{ __('To date') }}</label>
            <input type="date" name="to" id="to" value="{{ $to ?? '' }}">
        </div>
        <div>
            <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;border:none;cursor:pointer;">{{ __('Filter') }}</button>
        </div>
    </form>

    <table class="admin-table bookings-table">
        <thead>
            <tr>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Guest') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Booked days') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Total') }}</th>
                <th>{{ __('Book date') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bookings as $b)
                <tr>
                    <td><span class="fw-600">{{ $b->public_reference }}</span></td>
                    <td>
                        <div>{{ $b->first_name }} {{ $b->last_name }}</div>
                        <div class="text-13" style="opacity:.75;">{{ $b->email }}</div>
                    </td>
                    <td>{{ $b->phone }}</td>
                    <td>{{ $b->room?->name ?? '—' }}</td>
                    <td>
                        @if ($b->check_in && $b->check_out)
                            <button type="button" class="js-booking-days text-13 booking-days-chip" data-check-in="{{ $b->check_in->format('Y-m-d') }}" data-check-out="{{ $b->check_out->format('Y-m-d') }}" style="border:1px solid #d1d5db;background:#fff;border-radius:6px;cursor:pointer;">
                                {{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}
                            </button>
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $b->status->label() }}</td>
                    <td>{{ number_format((float) $b->total_amount, 0) }}</td>
                    <td>{{ $b->created_at?->format('Y-m-d H:i') }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.bookings.show', $b) }}" style="font-weight:600;">{{ __('View') }}</a>
                        @if ($b->status !== \App\Enums\BookingStatus::Confirmed)
                            <form action="{{ route('admin.bookings.destroy', $b) }}" method="POST" style="display:inline-flex;" onsubmit="return confirm(@json(__('Permanently delete this booking?')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:#fee2e2;border:1px solid #fecaca;color:#b91c1c;cursor:pointer;padding:.35rem .7rem;font-weight:600;">{{ __('Delete') }}</button>
                            </form>
                        @else
                            <span style="color:#94a3b8;font-size:.82rem;font-weight:600;">{{ __('Locked') }}</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">{{ __('No bookings found.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-20">{{ $bookings->links() }}</div>

    <div id="booking-days-modal" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:9999;align-items:center;justify-content:center;padding:1rem;">
        <div class="booking-days-modal-panel" style="background:#fff;border-radius:12px;padding:1rem;max-width:420px;width:100%;">
            <div class="d-flex items-center justify-between mb-10">
                <strong>{{ __('Booked days calendar') }}</strong>
                <button type="button" id="booking-days-close" style="border:none;background:none;font-size:20px;cursor:pointer;">&times;</button>
            </div>
            <p id="booking-days-detail" class="text-13 mb-10" style="opacity:.8;"></p>
            <div class="text-12 mb-10" style="display:flex;gap:.75rem;flex-wrap:wrap;">
                <span><span style="display:inline-block;width:10px;height:10px;background:#dbeafe;border-radius:3px;margin-right:4px;"></span>{{ __('Selected booking days') }}</span>
            </div>
            <input type="text" id="booking-days-picker" style="display:none;">
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            var modal = document.getElementById('booking-days-modal');
            var closeBtn = document.getElementById('booking-days-close');
            var pickerEl = document.getElementById('booking-days-picker');
            var detailEl = document.getElementById('booking-days-detail');
            if (!modal || !closeBtn || !pickerEl || !detailEl || !window.flatpickr) return;
            var picker = flatpickr(pickerEl, { inline: true, mode: 'range', dateFormat: 'Y-m-d' });
            function close() { modal.style.display = 'none'; }
            closeBtn.addEventListener('click', close);
            modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
            document.querySelectorAll('.js-booking-days').forEach(function (btn) {
                btn.addEventListener('click', function () {
                    var checkIn = btn.dataset.checkIn;
                    var checkOut = btn.dataset.checkOut;
                    detailEl.textContent = 'Check-in: ' + checkIn + ' | Check-out: ' + checkOut;
                    picker.setDate([checkIn, checkOut], true);
                    picker.jumpToDate(checkIn, true);
                    modal.style.display = 'flex';
                });
            });
        })();
    </script>
@endpush
