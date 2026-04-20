@extends('layouts.admin')

@section('title', __('Create branch'))

@section('content')
    <h1 class="text-30">{{ __('Create hotel branch') }}</h1>
    <p class="text-15 mt-15" style="opacity:.85;max-width:44rem;line-height:1.5;">{{ __('Add a branch with location, contacts, floors, logo and up to four preview images.') }}</p>

    <form method="POST" action="{{ route('admin.branches.store') }}" enctype="multipart/form-data" class="mt-30" data-autosave-key="admin-branch-create">
        @csrf

        <h2 class="text-20 mt-30 mb-15">{{ __('General') }}</h2>
        <div class="form-row">
            <label for="name">{{ __('Branch name') }} *</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="location_address">{{ __('Location / address') }}</label>
            <textarea id="location_address" name="location_address" rows="3">{{ old('location_address') }}</textarea>
            @error('location_address')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="city">{{ __('City') }}</label>
            <input id="city" type="text" name="city" value="{{ old('city') }}">
        </div>
        <div class="form-row">
            <label for="country">{{ __('Country') }}</label>
            <input id="country" type="text" name="country" value="{{ old('country') }}">
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Floors') }}</h2>
        <div class="form-row">
            <label class="text-13" style="font-weight:600;">
                <input type="hidden" name="is_ground_floor_only" value="0">
                <input type="checkbox" name="is_ground_floor_only" id="is_ground_floor_only" value="1" @checked(old('is_ground_floor_only'))>
                {{ __('Ground floor only (single level)') }}
            </label>
        </div>
        <div class="form-row" id="floors_count_wrap">
            <label for="floors_count">{{ __('Number of floors') }} <span class="text-13" style="opacity:.7;">({{ __('ground counts as floor 1') }})</span></label>
            <input id="floors_count" type="number" name="floors_count" min="1" max="200" value="{{ old('floors_count', 1) }}">
            @error('floors_count')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Contact') }}</h2>
        <div class="form-row">
            <label for="contact_phone">{{ __('Phone') }}</label>
            <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone') }}">
        </div>
        <div class="form-row">
            <label for="contact_email">{{ __('Email') }}</label>
            <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email') }}">
        </div>
        <div class="form-row">
            <label for="contact_whatsapp">{{ __('WhatsApp') }}</label>
            <input id="contact_whatsapp" type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp') }}">
        </div>
        <div class="form-row">
            <label for="extra_notes">{{ __('Other notes') }}</label>
            <textarea id="extra_notes" name="extra_notes" rows="3">{{ old('extra_notes') }}</textarea>
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Status & media') }}</h2>
        <div class="form-row">
            <label class="text-13" style="font-weight:600;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                {{ __('Branch is active') }}
            </label>
        </div>
        <div class="form-row">
            <label for="logo">{{ __('Branch logo') }}</label>
            <input id="logo" type="file" name="logo" accept="image/*">
            @error('logo')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'logo_media_asset_id',
            'label' => __('Or select logo from system gallery'),
            'selected' => old('logo_media_asset_id'),
        ])
        <div class="form-row">
            <label>{{ __('Preview images (max 4)') }}</label>
            @for ($i = 0; $i < 4; $i++)
                <div class="mb-10">
                    <input type="file" name="preview_images[]" accept="image/*">
                </div>
            @endfor
            @error('preview_images')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
            @error('preview_images.*')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'preview_media_asset_ids',
            'label' => __('Or select preview images from gallery'),
            'multiple' => true,
            'selected' => old('preview_media_asset_ids', []),
        ])

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Save branch') }}</button>
            <a href="{{ route('admin.branches.index') }}" style="margin-left:1rem;">{{ __('Cancel') }}</a>
        </div>
    </form>

    <script>
        (function () {
            var cb = document.getElementById('is_ground_floor_only');
            var wrap = document.getElementById('floors_count_wrap');
            var input = document.getElementById('floors_count');
            function sync() {
                if (!cb || !wrap) return;
                wrap.style.display = cb.checked ? 'none' : 'block';
                if (cb.checked && input) input.value = '1';
            }
            if (cb) cb.addEventListener('change', sync);
            sync();
        })();
    </script>
@endsection
