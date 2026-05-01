@extends('layouts.reception')

@section('title', __('Manual booking'))

@section('content')
    <h1 class="text-30" style="margin:0 0 .25rem;">{{ __('Reception Dashboard') }}</h1>
    <p class="text-15" style="opacity:.85; max-width:48rem; line-height:1.55; margin:0 0 1.5rem;">
        {{ __('Create a fast manual check-in using only available rooms, then confirm how the guest will pay.') }}
    </p>

    @if ($errors->any())
        <div class="mt-20 p-15" style="border:1px solid #fecaca;background:#fef2f2;border-radius:8px;">
            <ul class="text-14" style="margin:0;padding-left:1.2rem;">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('reception.bookings.store') }}" class="mt-30">
        @csrf

        <div style="display:grid;gap:1rem;margin-bottom:1rem;">
            <div style="display:grid;grid-template-columns:minmax(220px,1.2fr) minmax(220px,1fr);gap:1rem;">
                <div class="form-row" style="margin:0;">
                    <label for="room_search">{{ __('Search available room') }}</label>
                    <input type="search" id="room_search" placeholder="{{ __('Type room name, number, branch, or price') }}">
                </div>
                <label style="display:flex;align-items:center;justify-content:flex-start;gap:.55rem;cursor:pointer;border:1px solid #cbd5e1;background:#eff6ff;padding:.85rem 1rem;border-radius:8px;margin:0;">
                    <input type="checkbox" id="only_available_rooms" checked>
                    <span class="fw-600">{{ __('Show only available rooms right now') }}</span>
                </label>
            </div>

            <div class="form-row" style="margin:0;">
                <label for="room_id">{{ __('Room') }} *</label>
                <select name="room_id" id="room_id" required onchange="fetchBookedDates(this.value)">
                    <option value="">{{ __('Select room') }}</option>
                    @foreach ($rooms as $r)
                        <option
                            value="{{ $r->id }}"
                            data-room-search="{{ \Illuminate\Support\Str::lower(trim($r->name.' '.$r->room_number.' '.($r->branch?->name ?? '').' '.number_format((float) $r->price, 0))) }}"
                            data-available="{{ $r->getAttribute('is_currently_available') ? '1' : '0' }}"
                            @selected(old('room_id') == $r->id)
                        >
                            {{ $r->name }} - {{ $r->branch?->name }} - {{ number_format((float) $r->price, 0) }} TZS/{{ __('night') }}{{ $r->getAttribute('is_currently_available') ? '' : ' - '.__('NOT AVAILABLE NOW') }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="form-row">
            <label for="booking_dates">{{ __('Booking Dates (Check-in to Check-out)') }} *</label>
            <div id="calendar-loading" class="text-12 mb-5" style="display:none; color: #2563eb;">
                <i class="fa fa-spinner fa-spin mr-5"></i> {{ __('Loading room availability...') }}
            </div>
            <input type="text" id="booking_dates" name="booking_dates_raw" placeholder="{{ __('Select dates after choosing a room') }}" readonly required style="cursor: pointer; background: #fff;">
            <input type="hidden" name="check_in" id="check_in_val" value="{{ old('check_in') }}">
            <input type="hidden" name="check_out" id="check_out_val" value="{{ old('check_out') }}">
            @error('check_in')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:15px;">
            <div class="form-row">
                <label for="booking_method_id">{{ __('Payment method') }} *</label>
                <select name="booking_method_id" id="booking_method_id" required>
                    @foreach ($methods as $m)
                        <option value="{{ $m->id }}" @selected(old('booking_method_id') == $m->id)>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <label for="rooms_count">{{ __('Number of rooms') }} *</label>
                <input type="number" name="rooms_count" id="rooms_count" min="1" max="20" value="{{ old('rooms_count', 1) }}" required>
            </div>
        </div>

        <h2 class="text-20 mt-30 mb-10">{{ __('Guest details') }}</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
            <div class="form-row">
                <label for="first_name">{{ __('First name') }} *</label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required>
            </div>
            <div class="form-row">
                <label for="last_name">{{ __('Last name') }} *</label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required>
            </div>
        </div>

        <div class="form-row">
            <label for="email">{{ __('Email') }} *</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required>
        </div>

        <div class="form-row">
            <label for="phone">{{ __('Phone') }} *</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone') }}" required>
        </div>

        <div class="form-row">
            <label style="display:flex;align-items:center;justify-content:flex-start;gap:.65rem;cursor:pointer; background: #eff6ff; padding: 12px 14px; border-radius: 8px; width:100%;">
                <input type="hidden" name="confirm_paid" value="0">
                <input type="checkbox" name="confirm_paid" value="1" @checked(old('confirm_paid')) style="margin:0;">
                <span class="fw-600">{{ __('Guest paid now (cash at desk confirms booking immediately)') }}</span>
            </label>
        </div>

        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="width: 100%; border:none; padding: 15px; border-radius: 10px;">{{ __('Create manual booking') }}</button>
    </form>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let datePicker;
    const dateInput = document.getElementById('booking_dates');
    const loadingEl = document.getElementById('calendar-loading');
    const roomSelect = document.getElementById('room_id');
    const roomSearch = document.getElementById('room_search');
    const onlyAvailableRooms = document.getElementById('only_available_rooms');

    datePicker = flatpickr("#booking_dates", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: [],
        onClose: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('check_in_val').value = this.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('check_out_val').value = this.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    function filterRoomOptions() {
        if (!roomSelect) return;
        const term = (roomSearch ? roomSearch.value : '').toLowerCase().trim();
        const availabilityOnly = onlyAvailableRooms ? onlyAvailableRooms.checked : false;
        Array.from(roomSelect.options).forEach(function (option, index) {
            if (index === 0) {
                option.hidden = false;
                return;
            }
            const hay = (option.dataset.roomSearch || '').toLowerCase();
            const matchesSearch = term === '' || hay.indexOf(term) !== -1;
            const matchesAvailability = !availabilityOnly || option.dataset.available === '1';
            option.hidden = !(matchesSearch && matchesAvailability);
        });

        if (roomSelect.selectedOptions.length && roomSelect.selectedOptions[0].hidden) {
            roomSelect.value = '';
            datePicker.clear();
        }
    }

    function fetchBookedDates(roomId) {
        if (!roomId) {
            datePicker.set('disable', []);
            datePicker.clear();
            return;
        }

        if (loadingEl) loadingEl.style.display = 'block';
        dateInput.placeholder = "Checking availability...";

        fetch(`/reception/rooms/${roomId}/booked-dates`)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                const disabledRanges = Array.isArray(data) ? data : (data.disabled_ranges || []);
                datePicker.set('disable', disabledRanges);
                dateInput.placeholder = data && data.under_maintenance
                    ? "Room is under maintenance (not bookable)"
                    : "Select check-in to check-out";
                if (loadingEl) loadingEl.style.display = 'none';
            })
            .catch(() => {
                if (loadingEl) loadingEl.style.display = 'none';
                dateInput.placeholder = "Error loading dates. Try again.";
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        if (roomSearch) roomSearch.addEventListener('input', filterRoomOptions);
        if (onlyAvailableRooms) onlyAvailableRooms.addEventListener('change', filterRoomOptions);
        filterRoomOptions();
        const initialRoomId = roomSelect.value;
        if (initialRoomId) fetchBookedDates(initialRoomId);
    });
</script>

<style>
    .flatpickr-day.disabled {
        color: #ef4444 !important;
        text-decoration: line-through;
        background: #fee2e2 !important;
    }
</style>
@endpush
