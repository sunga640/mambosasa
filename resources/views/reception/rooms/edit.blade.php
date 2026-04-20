@extends('layouts.reception')

@section('title', __('Edit room'))

@section('content')
    <h1 class="text-30">{{ __('Edit room') }}: {{ $room->name }}</h1>

    <form method="POST" action="{{ route('reception.rooms.update', $room) }}" enctype="multipart/form-data" class="mt-30">
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
        </div>
        <div class="form-row">
            <label for="name">{{ __('Room name') }} *</label>
            <input id="name" type="text" name="name" value="{{ old('name', $room->name) }}" required>
        </div>
        <div class="form-row">
            <label for="status">{{ __('Status') }} *</label>
            <select name="status" id="status" required>
                @foreach (\App\Enums\RoomStatus::cases() as $st)
                    <option value="{{ $st->value }}" @selected(old('status', $room->status->value) === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label for="price">{{ __('Price') }} *</label>
            <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $room->price) }}" required>
        </div>
        <div class="form-row">
            <label for="description">{{ __('Description') }}</label>
            <textarea id="description" name="description" rows="4">{{ old('description', $room->description) }}</textarea>
        </div>

        <h2 class="text-20 mt-30 mb-10">{{ __('Listing card media') }}</h2>
        <div class="form-row">
            <label for="card_primary">{{ __('Card shows') }} *</label>
            <select name="card_primary" id="card_primary" required>
                <option value="none" @selected(old('card_primary', $room->card_primary ?? 'none') === 'none')>{{ __('No main media') }}</option>
                <option value="image" @selected(old('card_primary', $room->card_primary ?? 'none') === 'image')>{{ __('Hero image') }}</option>
                <option value="video" @selected(old('card_primary', $room->card_primary ?? 'none') === 'video')>{{ __('Video') }}</option>
            </select>
            @error('card_primary')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        @if ($room->hero_image_path)
            <div class="form-row">
                <p class="text-13">{{ __('Current hero image') }}:</p>
                <img src="{{ \App\Support\PublicDisk::url($room->hero_image_path) }}" alt="" style="max-height:140px;border-radius:8px;">
                <label class="text-13 mt-10" style="font-weight:400;display:block;">
                    <input type="checkbox" name="remove_hero_image" value="1"> {{ __('Remove hero image') }}
                </label>
            </div>
        @endif
        <div class="form-row">
            <label for="hero_image">{{ $room->hero_image_path ? __('Replace hero image') : __('Hero image (main card photo)') }}</label>
            <input id="hero_image" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.gif,.webp,.avif">
            @error('hero_image')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        @if ($room->video_path)
            <div class="form-row">
                <p class="text-13">{{ __('Current video') }}:</p>
                <video controls style="max-width:100%;max-height:240px;border-radius:8px;" src="{{ \App\Support\PublicDisk::url($room->video_path) }}"></video>
                <label class="text-13 mt-10" style="font-weight:400;display:block;">
                    <input type="checkbox" name="remove_video" value="1"> {{ __('Remove video') }}
                </label>
            </div>
        @endif
        <div class="form-row">
            <label for="video">{{ $room->video_path ? __('Replace video') : __('Video file') }}</label>
            <input id="video" type="file" name="video" accept=".mp4,.webm,.mov">
            @error('video')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h3 class="text-18 mt-25 mb-10">{{ __('Gallery images & thumbnail labels (optional)') }}</h3>
        <p class="text-13 mb-15" style="opacity:.8;">{{ __('Labels appear with each thumbnail on the public room page. Check “Remove” to delete an image.') }}</p>

        @if ($room->images->isNotEmpty())
            <div class="form-row">
                @foreach ($room->images as $img)
                    <div style="border:1px solid #ddd;border-radius:10px;padding:12px;margin-bottom:12px;background:#fafafa;">
                        <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-start;">
                            <img src="{{ \App\Support\PublicDisk::url($img->path) }}" alt="{{ $img->caption ?? '' }}" style="max-height:100px;border-radius:8px;">
                            <div style="flex:1;min-width:200px;">
                                <label class="text-13" style="font-weight:600;">{{ __('View / thumbnail label') }}</label>
                                <input type="text" name="captions[{{ $img->id }}]" value="{{ old('captions.'.$img->id, $img->caption) }}" maxlength="160" placeholder="{{ __('e.g. Sea view') }}" style="width:100%;max-width:100%;">
                                <label class="text-13" style="font-weight:400;margin-top:8px;display:block;">
                                    <input type="checkbox" name="remove_image_ids[]" value="{{ $img->id }}"> {{ __('Remove image') }}
                                </label>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        <p class="text-14 font-weight-600 mb-10">{{ __('Add more gallery images') }}</p>
        <div id="room-gallery-rows-edit">
            <div class="room-gallery-row" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;margin-bottom:14px;padding:12px;background:#f9f9fa;border-radius:10px;border:1px solid #e8e8e8;">
                <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                    <label>{{ __('Image') }}</label>
                    <input type="file" name="images[]" accept="image/*">
                </div>
                <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                    <label>{{ __('View / thumbnail label') }}</label>
                    <input type="text" name="image_captions[]" placeholder="{{ __('e.g. Living area') }}" maxlength="160">
                </div>
            </div>
        </div>
        <button type="button" id="add-gallery-row-edit" class="btn-account-secondary" style="margin-bottom:1rem;">{{ __('Add gallery row') }}</button>

        <template id="room-gallery-row-template-edit">
            <div class="room-gallery-row" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;margin-bottom:14px;padding:12px;background:#f9f9fa;border-radius:10px;border:1px solid #e8e8e8;">
                <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                    <label>{{ __('Image') }}</label>
                    <input type="file" name="images[]" accept="image/*">
                </div>
                <div class="form-row" style="margin:0;flex:1;min-width:200px;">
                    <label>{{ __('View / thumbnail label') }}</label>
                    <input type="text" name="image_captions[]" placeholder="{{ __('e.g. Living area') }}" maxlength="160">
                </div>
            </div>
        </template>

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Update room') }}</button>
            <a href="{{ route('reception.rooms.index') }}" style="margin-left:1rem;">{{ __('Back') }}</a>
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

            var addBtn = document.getElementById('add-gallery-row-edit');
            var wrap = document.getElementById('room-gallery-rows-edit');
            var tpl = document.getElementById('room-gallery-row-template-edit');
            if (addBtn && wrap && tpl) {
                addBtn.addEventListener('click', function () {
                    wrap.appendChild(tpl.content.cloneNode(true));
                });
            }
        })();
    </script>
@endsection
