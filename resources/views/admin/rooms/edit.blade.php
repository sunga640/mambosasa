@extends('layouts.admin')

@section('title', __('Edit room'))

@section('content')
    <h1 class="text-30">{{ __('Edit room') }}: {{ $room->name }}</h1>

    <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="mt-30" data-autosave-key="admin-room-edit-{{ $room->id }}">
        @csrf
        @method('PUT')

        <div class="form-row">
            <label for="hotel_branch_id">{{ __('Branch') }} *</label>
            <select name="hotel_branch_id" id="hotel_branch_id" required>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" data-max-floor="{{ $b->maxFloorIndex() }}" @selected(old('hotel_branch_id', $room->hotel_branch_id) == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label for="name">{{ __('Room name') }} *</label>
            <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="room_number">{{ __('Room number') }} *</label>
            <input type="text" name="room_number" id="room_number" value="{{ old('room_number', $room->room_number) }}" required maxlength="32">
            @error('room_number')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="floor_number">{{ __('Floor number') }} *</label>
            <input type="number" name="floor_number" id="floor_number" min="0" max="254" value="{{ old('floor_number', $room->floor_number) }}" required>
            @error('floor_number')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="room_type_id">{{ __('Room type') }} *</label>
            <select id="room_type_id" name="room_type_id" required>
                <option value="">{{ __('Select room type') }}</option>
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}" @selected((string) old('room_type_id', $room->room_type_id) === (string) $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('room_type_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="status">{{ __('Status') }} *</label>
            <select name="status" id="status" required>
                @foreach (\App\Enums\RoomStatus::cases() as $st)
                    <option value="{{ $st->value }}" @selected(old('status', $room->status->value) === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
            @error('status')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="price">{{ __('Price') }} *</label>
            <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $room->price) }}" required>
            @error('price')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Update room') }}</button>
            <a href="{{ route('admin.rooms.index') }}" style="margin-left:1rem;">{{ __('Back') }}</a>
        </div>
    </form>

    <script>
        (function () {
            var sel = document.getElementById('hotel_branch_id');
            var floor = document.getElementById('floor_number');
            function syncMax() {
                if (!sel || !floor) return;
                var opt = sel.options[sel.selectedIndex];
                var max = opt ? parseInt(opt.getAttribute('data-max-floor'), 10) : 254;
                if (isNaN(max)) max = 254;
                floor.max = max;
            }
            if (sel) sel.addEventListener('change', syncMax);
            syncMax();

        })();
    </script>
@endsection
