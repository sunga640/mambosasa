@extends('layouts.admin')

@section('title', __('Edit room rank'))

@section('content')
    <h1 class="text-30">{{ __('Edit room rank') }}</h1>
    <form method="POST" action="{{ route('admin.room-ranks.update', $rank) }}" class="mt-25">
        @csrf
        @method('PUT')
        <div class="form-row">
            <label for="name">{{ __('Name') }} *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $rank->name) }}" required maxlength="120">
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="slug">{{ __('Slug') }} *</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $rank->slug) }}" required maxlength="80" pattern="[a-z0-9\-]+">
            @error('slug')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="sort_order">{{ __('Sort order') }}</label>
            <input type="number" name="sort_order" id="sort_order" value="{{ old('sort_order', $rank->sort_order) }}" min="0" max="9999">
        </div>
        <div class="form-row">
            <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $rank->is_active))> {{ __('Active') }}</label>
        </div>
        <button type="submit" class="dash-btn dash-btn--primary">{{ __('Save') }}</button>
        <a href="{{ route('admin.room-ranks.index') }}" class="dash-btn dash-btn--ghost ml-10">{{ __('Cancel') }}</a>
    </form>
@endsection
