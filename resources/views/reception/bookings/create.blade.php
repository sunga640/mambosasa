@extends('layouts.reception')

@section('title', __('Manual booking'))

@section('content')
    <h1 class="text-30" style="margin:0 0 .25rem;">{{ __('Reception Dashboard') }}</h1>
<p class="text-15" style="opacity:.85; max-width:48rem; line-height:1.55; margin:0 0 1.5rem;">
    {{ __('Real-time branch metrics: manage direct check-ins, room availability, and maintenance tasks.') }}
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

        {{-- ROOM SELECT --}}
        <div class="form-row">
            <label for="room_id">{{ __('Room') }} *</label>
            <select name="room_id" id="room_id" required onchange="fetchBookedDates(this.value)">
                <option value="">{{ __('Select room') }}</option>
                @foreach ($rooms as $r)
                    <option value="{{ $r->id }}" @selected(old('room_id') == $r->id)>
                        {{ $r->name }} — {{ $r->branch?->name }} — {{ number_format((float) $r->price, 0) }} TZS/{{ __('night') }}{{ $r->status === \App\Enums\RoomStatus::UnderMaintenance ? ' — '.__('UNDER MAINTENANCE') : '' }}
                    </option>
                @endforeach
            </select>
            @error('room_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        {{-- BOOKING DATES (CALENDAR) --}}
        <div class="form-row">
            <label for="booking_dates">{{ __('Booking Dates (Check-in to Check-out)') }} *</label>
            <div id="calendar-loading" class="text-12 mb-5" style="display:none; color: #2563eb;">
                <i class="fa fa-spinner fa-spin mr-5"></i> {{ __('Loading room availability...') }}
            </div>
            <input type="text" id="booking_dates" name="booking_dates_raw" placeholder="{{ __('Select dates after choosing a room') }}" readonly required
                style="cursor: pointer; background: #fff;">

            {{-- Hidden inputs ili controller isivunjike (inasubiri check_in na check_out) --}}
            <input type="hidden" name="check_in" id="check_in_val" value="{{ old('check_in') }}">
            <input type="hidden" name="check_out" id="check_out_val" value="{{ old('check_out') }}">

            @error('check_in')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

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
            <label style="display:flex;align-items:center;gap:.5rem;cursor:pointer; background: #eff6ff; padding: 10px; border-radius: 8px;">
                <input type="hidden" name="confirm_paid" value="0">
                <input type="checkbox" name="confirm_paid" value="1" @checked(old('confirm_paid'))>
                <span class="fw-600">{{ __('Guest paid now (cash at desk — confirms booking immediately)') }}</span>
            </label>
        </div>

        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="width: 100%; border:none; padding: 15px; border-radius: 10px;">{{ __('Create manual booking') }}</button>
    </form>
@endsection

{{-- JAVASCRIPT --}}
@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    let datePicker;
    const dateInput = document.getElementById('booking_dates');
    const loadingEl = document.getElementById('calendar-loading');

    // 1. Initialize Flatpickr
    datePicker = flatpickr("#booking_dates", {
        mode: "range",
        minDate: "today",
        dateFormat: "Y-m-d",
        disable: [], // Anza ikiwa tupu
        onClose: function(selectedDates) {
            if (selectedDates.length === 2) {
                document.getElementById('check_in_val').value = this.formatDate(selectedDates[0], "Y-m-d");
                document.getElementById('check_out_val').value = this.formatDate(selectedDates[1], "Y-m-d");
            }
        }
    });

    function fetchBookedDates(roomId) {
        if (!roomId) {
            datePicker.set('disable', []);
            datePicker.clear();
            return;
        }

        // Onyesha hali ya upakiaji
        if(loadingEl) loadingEl.style.display = 'block';
        dateInput.placeholder = "Checking availability...";

        // Tumia URL kamili
        const url = `/reception/rooms/${roomId}/booked-dates`;

        fetch(url)
            .then(response => {
                if (!response.ok) throw new Error('Network response was not ok');
                return response.json();
            })
            .then(data => {
                console.log("Booked dates received:", data); // Angalia kwenye Console (F12) kama data inafika

                // UPDATE FLATPICKR
                var disabledRanges = Array.isArray(data) ? data : (data.disabled_ranges || []);
                datePicker.set('disable', disabledRanges);
                if (data && data.under_maintenance) {
                    dateInput.placeholder = "Room is under maintenance (not bookable)";
                } else {
                    dateInput.placeholder = "Select check-in to check-out";
                }

                // Rudisha hali ya kawaida
                if(loadingEl) loadingEl.style.display = 'none';
            })
            .catch(error => {
                console.error('Error:', error);
                if(loadingEl) loadingEl.style.display = 'none';
                dateInput.placeholder = "Error loading dates. Try again.";
            });
    }

    // Kama kuna chumba kimeshachaguliwa tayari (mfano baada ya validation error)
    document.addEventListener('DOMContentLoaded', function() {
        const initialRoomId = document.getElementById('room_id').value;
        if (initialRoomId) fetchBookedDates(initialRoomId);
    });
</script>

<style>
    /* Style ili siku zilizochaguliwa zionekane nyekundu au kupigwa mstari */
    .flatpickr-day.disabled {
        color: #ef4444 !important;
        text-decoration: line-through;
        background: #fee2e2 !important;
    }
</style>
@endpush
