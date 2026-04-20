@extends('layouts.admin')

@section('title', __('Create permission'))

@section('content')
    <h1 class="text-30">{{ __('Create permission') }}</h1>
    <form method="POST" action="{{ route('admin.permissions.store') }}" class="mt-30">
        @csrf
        <div class="form-row">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="slug">{{ __('Slug') }} <span class="text-13" style="opacity:.7;">({{ __('optional') }})</span></label>
            <input id="slug" type="text" name="slug" value="{{ old('slug') }}">
            @error('slug')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="description">{{ __('Description') }}</label>
            <textarea id="description" name="description" rows="3">{{ old('description') }}</textarea>
            @error('description')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="border:none;cursor:pointer;padding:.6rem 1.2rem;border-radius:8px;">{{ __('Save') }}</button>
    </form>
@endsection
