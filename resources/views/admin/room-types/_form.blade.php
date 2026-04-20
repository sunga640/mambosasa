@php($t = $roomType ?? null)
<div class="form-row">
    <label for="name">{{ __('Type name') }}</label>
    <input id="name" type="text" name="name" value="{{ old('name', $t?->name) }}" required>
</div>
<div class="form-row">
    <label for="hotel_branch_id">{{ __('Branch') }}</label>
    <select id="hotel_branch_id" name="hotel_branch_id">
        <option value="">{{ __('All branches') }}</option>
        @foreach ($branches as $branch)
            <option value="{{ $branch->id }}" @selected((string) old('hotel_branch_id', $t?->hotel_branch_id) === (string) $branch->id)>{{ $branch->name }}</option>
        @endforeach
    </select>
</div>
<div class="form-row">
    <label for="price">{{ __('Default price (TZS)') }}</label>
    <input id="price" type="number" step="0.01" min="0" name="price" value="{{ old('price', $t?->price ?? 0) }}" required>
</div>
<div class="form-row">
    <label for="max_rooms">{{ __('Rooms count limit for this type') }}</label>
    <input id="max_rooms" type="number" min="0" max="5000" name="max_rooms" value="{{ old('max_rooms', $t?->max_rooms ?? 0) }}">
</div>
@if (! $t)
<div class="form-row">
    <label for="rooms_to_generate">{{ __('Create how many rooms under this type now?') }}</label>
    <input id="rooms_to_generate" type="number" min="0" max="500" name="rooms_to_generate" value="{{ old('rooms_to_generate', 0) }}">
</div>
@endif
<div class="form-row">
    <label for="description">{{ __('Description') }}</label>
    <textarea id="description" name="description" rows="4">{{ old('description', $t?->description) }}</textarea>
</div>
<div class="form-row">
    <label for="hero_image">{{ __('Hero image') }}</label>
    <input id="hero_image" type="file" name="hero_image" accept=".jpg,.jpeg,.png,.gif,.webp,.avif">
</div>
@include('partials.media-picker', [
    'assets' => $mediaAssets,
    'name' => 'hero_media_asset_id',
    'label' => __('Or select from system gallery'),
    'selected' => old('hero_media_asset_id'),
])
@include('partials.media-picker', [
    'assets' => $mediaAssets,
    'name' => 'thumbnail_media_asset_ids',
    'label' => __('Thumbnail images for frontend card/gallery'),
    'multiple' => true,
    'selected' => old('thumbnail_media_asset_ids', []),
])
<div class="form-row">
    <label style="display:flex;align-items:center;gap:.5rem;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $t?->is_active ?? true))>
        {{ __('Active') }}
    </label>
</div>
