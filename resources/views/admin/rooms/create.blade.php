@extends('layouts.admin')

@section('title', __('Create room'))

@section('content')
    <h1 class="text-30">{{ __('Create room') }}</h1>
    <p class="text-15 mt-15" style="opacity:.85;max-width:44rem;line-height:1.5;">{{ __('Set branch, floor, price, then choose what appears on cards (hero image or video). Gallery thumbnails are optional.') }}</p>

    <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" class="mt-30" data-autosave-key="admin-room-create">
        @csrf

        <div class="form-row">
            <label for="hotel_branch_id">{{ __('Branch') }} *</label>
            <select name="hotel_branch_id" id="hotel_branch_id" required @disabled($fixedBranchId)>
                <option value="">{{ __('Select branch') }}</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" data-max-floor="{{ $b->maxFloorIndex() }}" @selected(old('hotel_branch_id', $selectedBranchId) == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
            @if ($fixedBranchId)
                <input type="hidden" name="hotel_branch_id" value="{{ $fixedBranchId }}">
            @endif
            @error('hotel_branch_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="room_number">{{ __('Room number') }} *</label>
            <input type="text" name="room_number" id="room_number" value="{{ old('room_number') }}" required maxlength="32" placeholder="{{ __('e.g. 101, A-12') }}">
            @error('room_number')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="floor_number">{{ __('Floor number') }} * <span class="text-13" style="opacity:.7;">({{ __('0 = ground') }})</span></label>
            <input type="number" name="floor_number" id="floor_number" min="0" max="254" value="{{ old('floor_number', 0) }}" required>
            @error('floor_number')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="room_type_id">{{ __('Room type') }} *</label>
            <select id="room_type_id" name="room_type_id" required>
                <option value="">{{ __('Select room type') }}</option>
                @foreach ($roomTypes as $type)
                    <option value="{{ $type->id }}" @selected((string) old('room_type_id', $selectedRoomTypeId) === (string) $type->id)>{{ $type->name }}</option>
                @endforeach
            </select>
            @error('room_type_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="name">{{ __('Room name') }} *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="status">{{ __('Status') }} *</label>
            <select name="status" id="status" required>
                @foreach (\App\Enums\RoomStatus::cases() as $st)
                    <option value="{{ $st->value }}" @selected(old('status') === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label for="price">{{ __('Price') }} *</label>
            <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" required>
            @error('price')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Save room') }}</button>
            <a href="{{ route('admin.rooms.index') }}" style="margin-left:1rem;">{{ __('Cancel') }}</a>
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
                if (parseInt(floor.value, 10) > max) floor.value = max;
            }
            if (sel) sel.addEventListener('change', syncMax);
            syncMax();

        })();
    </script>
@endsection
