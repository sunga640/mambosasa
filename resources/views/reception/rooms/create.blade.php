@extends('layouts.reception')

@section('title', __('Create room'))

@section('content')
    <h1 class="text-30">{{ __('Create room') }}</h1>
    <p class="text-15 mt-15" style="opacity:.85;max-width:44rem;line-height:1.5;">{{ __('Set branch, floor, price, then choose what appears on cards (hero image or video). Gallery thumbnails are optional.') }}</p>

    <form method="POST" action="{{ route('reception.rooms.store') }}" enctype="multipart/form-data" class="mt-30">
        @csrf

        <div class="form-row">
            <label for="hotel_branch_id">{{ __('Branch') }} *</label>
            <select name="hotel_branch_id" id="hotel_branch_id" required>
                <option value="">{{ __('Select branch') }}</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" data-max-floor="{{ $b->maxFloorIndex() }}" @selected(old('hotel_branch_id', $selectedBranchId) == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
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
                    <option value="{{ $type->id }}" @selected((string) old('room_type_id') === (string) $type->id)>{{ $type->name }}</option>
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
        <div class="form-row">
            <label for="description">{{ __('Description') }}</label>
            <textarea id="description" name="description" rows="4">{{ old('description') }}</textarea>
        </div>

        <h2 class="text-20 mt-30 mb-10">{{ __('Listing card media') }}</h2>
        <p class="text-13 mb-15" style="opacity:.8;max-width:42rem;">{{ __('Pick whether the room card shows a video, a single hero image, or no main media. Optional gallery (below) is for extra thumbnail views.') }}</p>

        <div class="form-row">
            <label for="card_primary">{{ __('Card shows') }} *</label>
            <select name="card_primary" id="card_primary" required>
                <option value="none" @selected(old('card_primary', 'none') === 'none')>{{ __('No main media') }}</option>
                <option value="image" @selected(old('card_primary') === 'image')>{{ __('Hero image') }}</option>
                <option value="video" @selected(old('card_primary') === 'video')>{{ __('Video') }}</option>
            </select>
            @error('card_primary')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="hero_image">{{ __('Hero image (main card photo)') }}</label>
            <input id="hero_image" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.gif,.webp,.avif">
            @error('hero_image')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="video">{{ __('Video file') }}</label>
            <input id="video" type="file" name="video" accept=".mp4,.webm,.mov">
            @error('video')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-10">{{ __('Gallery (optional thumbnails)') }}</h2>
        <p class="text-13 mb-15" style="opacity:.8;max-width:40rem;">{{ __('Add as many rows as you need, or leave empty. Labels are optional.') }}</p>

        <div id="room-gallery-rows"></div>
        <button type="button" id="add-gallery-row" class="btn-account-secondary" style="margin-bottom:1rem;">{{ __('Add gallery row') }}</button>
        @error('images')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        @error('images.*')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        @error('image_captions.*')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Save room') }}</button>
            <a href="{{ route('reception.rooms.index') }}" style="margin-left:1rem;">{{ __('Cancel') }}</a>
        </div>
    </form>

    <template id="room-gallery-row-template">
        <div class="room-gallery-row" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;margin-bottom:14px;padding:12px;background:#f9f9fa;border-radius:10px;border:1px solid #e8e8e8;">
            <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                <label>{{ __('Image') }}</label>
                <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.gif,.webp,.avif">
            </div>
            <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                <label>{{ __('View / thumbnail label') }}</label>
                <input type="text" name="image_captions[]" placeholder="{{ __('e.g. Living area, Balcony') }}" maxlength="160">
            </div>
        </div>
    </template>

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

            var addBtn = document.getElementById('add-gallery-row');
            var wrap = document.getElementById('room-gallery-rows');
            var tpl = document.getElementById('room-gallery-row-template');
            if (addBtn && wrap && tpl) {
                addBtn.addEventListener('click', function () {
                    wrap.appendChild(tpl.content.cloneNode(true));
                });
            }
        })();
    </script>
@endsection
