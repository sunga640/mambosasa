@extends('layouts.reception')

@section('title', __('Room types'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Room types') }}</h1>
        <a href="{{ route('reception.rooms.create', ['branch_id' => $filterBranchId]) }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create room') }}</a>
    </div>

    <form method="GET" action="{{ route('reception.rooms.index') }}" class="form-row mt-20" style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div>
            <label for="branch_id">{{ __('Filter by branch') }}</label>
            <select name="branch_id" id="branch_id" onchange="this.form.submit()">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" @selected($filterBranchId === $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="room_type_id">{{ __('Room type') }}</label>
            <select name="room_type_id" id="room_type_id">
                <option value="">{{ __('All room types') }}</option>
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}" @selected($filterRoomTypeId === $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="availability_range">{{ __('Availability dates') }}</label>
            <input id="availability_range" type="text" placeholder="{{ __('Select date range') }}" style="min-width:260px;">
            <input type="hidden" name="check_in" id="check_in" value="{{ $filterCheckIn }}">
            <input type="hidden" name="check_out" id="check_out" value="{{ $filterCheckOut }}">
        </div>
        <div style="display:flex;align-items:center;gap:.5rem;min-height:42px;">
            <label for="available_only" style="margin:0;display:flex;align-items:center;gap:.45rem;">
                <input type="checkbox" name="available_only" id="available_only" value="1" @checked($availableOnly)>
                {{ __('Available only') }}
            </label>
        </div>
        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Filter') }}</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Room #') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Floor') }}</th>
                <th>{{ __('Stay (from → to)') }}</th>
                <th>{{ __('Booking / payment') }}</th>
                <th>{{ __('In use') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Price') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rooms as $room)
                @php
                    $rb = $room->bookings->first(function ($bk) {
                        return $bk->status === \App\Enums\BookingStatus::PendingPayment
                            && $bk->payment_deadline_at
                            && $bk->payment_deadline_at->isFuture();
                    }) ?? $room->bookings->firstWhere('status', \App\Enums\BookingStatus::Confirmed);
                @endphp
                <tr>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->type?->name ?? '—' }}</td>
                    <td>{{ $room->room_number ?? '—' }}</td>
                    <td>{{ $room->branch?->name ?? '—' }}</td>
                    <td>{{ $room->floor_number === 0 ? __('Ground') : $room->floor_number }}</td>
                    <td>
                        @if ($rb && $rb->check_in && $rb->check_out)
                            {{ $rb->check_in->format('Y-m-d') }} → {{ $rb->check_out->format('Y-m-d') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($rb)
                            <div>{{ $rb->status->label() }}</div>
                            @if ($rb->invoice)
                                <a class="text-13" href="{{ $rb->invoice->publicUrl() }}" target="_blank" rel="noopener">{{ __('Invoice') }}</a>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($room->isEffectivelyInUse())
                            <span style="color:#b45309;font-weight:600;">{{ __('Yes') }}</span>
                        @else
                            <span style="opacity:.65;">{{ __('No') }}</span>
                        @endif
                        <form action="{{ route('reception.rooms.toggle-in-use', $room) }}" method="POST" style="display:inline;margin-left:.5rem;">
                            @csrf
                            <button type="submit" class="text-13" style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:6px;padding:.2rem .45rem;cursor:pointer;" title="{{ __('Toggle manual in-use flag') }}">
                                {{ $room->force_in_use ? __('Unforce') : __('Force') }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $room->status->label() }}</td>
                    <td>{{ number_format((float) $room->price, 0) }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('reception.rooms.edit', $room) }}">{{ __('Edit') }}</a>
                        <form action="{{ route('reception.rooms.destroy', $room) }}" method="POST" data-swal-delete
                              data-swal-title="{{ __('Delete room?') }}"
                              data-swal-text="{{ __('Room media will be removed from storage.') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $rooms->links() }}</div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        (function () {
            var range = document.getElementById('availability_range');
            var inputIn = document.getElementById('check_in');
            var inputOut = document.getElementById('check_out');
            if (!range || !inputIn || !inputOut || typeof flatpickr !== 'function') return;
            var initial = '';
            if (inputIn.value && inputOut.value) initial = inputIn.value + ' to ' + inputOut.value;
            flatpickr(range, {
                mode: 'range',
                minDate: 'today',
                dateFormat: 'Y-m-d',
                defaultDate: initial || null,
                onChange: function (selectedDates) {
                    if (selectedDates.length === 2) {
                        inputIn.value = flatpickr.formatDate(selectedDates[0], 'Y-m-d');
                        inputOut.value = flatpickr.formatDate(selectedDates[1], 'Y-m-d');
                    } else {
                        inputIn.value = '';
                        inputOut.value = '';
                    }
                }
            });
        })();
    </script>
@endpush
