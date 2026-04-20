@extends('layouts.admin')

@section('title', __('Edit branch'))

@section('content')
    <h1 class="text-30">{{ __('Edit branch') }}: {{ $branch->name }}</h1>

    <form method="POST" action="{{ route('admin.branches.update', $branch) }}" enctype="multipart/form-data" class="mt-30" data-autosave-key="admin-branch-edit-{{ $branch->id }}">
        @csrf
        @method('PUT')

        <h2 class="text-20 mt-30 mb-15">{{ __('General') }}</h2>
        <div class="form-row">
            <label for="name">{{ __('Branch name') }} *</label>
            <input id="name" type="text" name="name" value="{{ old('name', $branch->name) }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="location_address">{{ __('Location / address') }}</label>
            <textarea id="location_address" name="location_address" rows="3">{{ old('location_address', $branch->location_address) }}</textarea>
        </div>
        <div class="form-row">
            <label for="city">{{ __('City') }}</label>
            <input id="city" type="text" name="city" value="{{ old('city', $branch->city) }}">
        </div>
        <div class="form-row">
            <label for="country">{{ __('Country') }}</label>
            <input id="country" type="text" name="country" value="{{ old('country', $branch->country) }}">
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Floors') }}</h2>
        <div class="form-row">
            <label class="text-13" style="font-weight:600;">
                <input type="hidden" name="is_ground_floor_only" value="0">
                <input type="checkbox" name="is_ground_floor_only" id="is_ground_floor_only" value="1" @checked(old('is_ground_floor_only', $branch->is_ground_floor_only))>
                {{ __('Ground floor only') }}
            </label>
        </div>
        <div class="form-row" id="floors_count_wrap">
            <label for="floors_count">{{ __('Number of floors') }}</label>
            <input id="floors_count" type="number" name="floors_count" min="1" max="200" value="{{ old('floors_count', $branch->floors_count) }}">
            @error('floors_count')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Contact') }}</h2>
        <div class="form-row">
            <label for="contact_phone">{{ __('Phone') }}</label>
            <input id="contact_phone" type="text" name="contact_phone" value="{{ old('contact_phone', $branch->contact_phone) }}">
        </div>
        <div class="form-row">
            <label for="contact_email">{{ __('Email') }}</label>
            <input id="contact_email" type="email" name="contact_email" value="{{ old('contact_email', $branch->contact_email) }}">
        </div>
        <div class="form-row">
            <label for="contact_whatsapp">{{ __('WhatsApp') }}</label>
            <input id="contact_whatsapp" type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $branch->contact_whatsapp) }}">
        </div>
        <div class="form-row">
            <label for="extra_notes">{{ __('Other notes') }}</label>
            <textarea id="extra_notes" name="extra_notes" rows="3">{{ old('extra_notes', $branch->extra_notes) }}</textarea>
        </div>

        <h2 class="text-20 mt-30 mb-15">{{ __('Status & media') }}</h2>
        <div class="form-row">
            <label class="text-13" style="font-weight:600;">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $branch->is_active))>
                {{ __('Branch is active') }}
            </label>
        </div>
        <div class="form-row">
            <label for="logo">{{ __('Branch logo') }}</label>
            @if ($branch->logo_path)
                <div class="mb-10"><img src="{{ \App\Support\PublicDisk::url($branch->logo_path) }}" alt="" style="max-height:64px;border-radius:8px;"></div>
            @endif
            <input id="logo" type="file" name="logo" accept="image/*">
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'logo_media_asset_id',
            'label' => __('Or select logo from system gallery'),
            'selected' => old('logo_media_asset_id'),
        ])
        <div class="form-row">
            <label>{{ __('Current previews') }}</label>
            @php $previews = $branch->preview_images ?? []; @endphp
            @forelse ($previews as $idx => $path)
                <div class="mb-15" style="display:flex;align-items:flex-start;gap:12px;flex-wrap:wrap;">
                    <img src="{{ \App\Support\PublicDisk::url($path) }}" alt="" style="max-height:80px;border-radius:8px;border:1px solid #ddd;">
                    <label class="text-13" style="font-weight:400;">
                        <input type="checkbox" name="remove_preview_indexes[]" value="{{ $idx }}"> {{ __('Remove') }}
                    </label>
                </div>
            @empty
                <p class="text-13" style="opacity:.7;">{{ __('No preview images yet.') }}</p>
            @endforelse
        </div>
        <div class="form-row">
            <label>{{ __('Add preview images (max 4 total)') }}</label>
            @for ($i = 0; $i < 4; $i++)
                <div class="mb-10">
                    <input type="file" name="preview_images[]" accept="image/*">
                </div>
            @endfor
        </div>
        @include('partials.media-picker', [
            'assets' => $mediaAssets,
            'name' => 'preview_media_asset_ids',
            'label' => __('Or select preview images from gallery'),
            'multiple' => true,
            'selected' => old('preview_media_asset_ids', []),
        ])

        <div class="mt-30">
            <button type="submit" class="btn-account-primary" style="border:none;">{{ __('Update branch') }}</button>
            <a href="{{ route('admin.branches.index') }}" style="margin-left:1rem;">{{ __('Back') }}</a>
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
