@php
    $s = $service ?? null;
@endphp
<div class="form-row">
    <label for="name">{{ __('Name') }} *</label>
    <input type="text" id="name" name="name" value="{{ old('name', $s?->name) }}" required maxlength="200">
    @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label for="image">{{ __('Image upload') }}</label>
    <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png,.gif,.webp,.avif">
    @error('image')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
@include('partials.media-picker', [
    'assets' => ($mediaAssets ?? collect()),
    'name' => 'media_asset_id',
    'label' => __('Or select from system gallery'),
    'selected' => old('media_asset_id'),
])
@error('media_asset_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
<div class="form-row">
    <label for="category">{{ __('Category') }} *</label>
    <input type="text" id="category" name="category" value="{{ old('category', $s?->category ?? 'general') }}" required maxlength="64" placeholder="{{ __('e.g. laundry, food, transport, meeting') }}">
    @error('category')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label for="description">{{ __('Description') }}</label>
    <textarea id="description" name="description" rows="4" maxlength="5000" style="width:100%;max-width:520px;padding:0.5rem;border:1px solid #ccc;border-radius:8px;font-family:inherit;">{{ old('description', $s?->description) }}</textarea>
    @error('description')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label for="price">{{ __('Price (TZS)') }} *</label>
    <input type="number" step="0.01" min="0" id="price" name="price" value="{{ old('price', $s?->price ?? 0) }}" required>
    @error('price')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label for="hotel_branch_id">{{ __('Branch') }}</label>
    <select id="hotel_branch_id" name="hotel_branch_id">
        <option value="">{{ __('All branches') }}</option>
        @foreach ($branches as $br)
            <option value="{{ $br->id }}" @selected((string) old('hotel_branch_id', $s?->hotel_branch_id) === (string) $br->id)>{{ $br->name }}</option>
        @endforeach
    </select>
    <p class="text-13 mt-5" style="opacity:.75;">{{ __('Leave empty if the service applies to every property.') }}</p>
    @error('hotel_branch_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label for="sort_order">{{ __('Sort order') }}</label>
    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $s?->sort_order ?? 0) }}" min="0" max="99999">
    @error('sort_order')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
</div>
<div class="form-row">
    <label style="display:flex;align-items:center;gap:.5rem;font-weight:500;">
        <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $s?->is_active ?? true))>
        {{ __('Published (visible to guests)') }}
    </label>
</div>
