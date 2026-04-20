@extends('layouts.admin')

@section('title', __('Create user'))

@section('content')
    <h1 class="text-30">{{ __('Create user') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;">{{ __('Assign a role; the user inherits that role’s permissions.') }}</p>
    <form method="POST" action="{{ route('admin.users.store') }}" class="mt-30">
        @csrf
        <div class="form-row">
            <label for="name">{{ __('Name') }}</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required>
            @error('name')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
            @error('email')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" name="password" required autocomplete="new-password">
            @error('password')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="password_confirmation">{{ __('Confirm password') }}</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
        </div>
        <div class="form-row">
            <label for="role_id">{{ __('Role') }}</label>
            <select id="role_id" name="role_id">
                <option value="">{{ __('— None —') }}</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>{{ $role->name }}</option>
                @endforeach
            </select>
            @error('role_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="hotel_branch_id">{{ __('Hotel branch') }} <span class="text-13" style="opacity:.7;">({{ __('reception & staff') }})</span></label>
            <select id="hotel_branch_id" name="hotel_branch_id">
                <option value="">{{ __('— All / not assigned —') }}</option>
                @foreach ($branches as $branch)
                    <option value="{{ $branch->id }}" @selected(old('hotel_branch_id') == $branch->id)>{{ $branch->name }}</option>
                @endforeach
            </select>
            @error('hotel_branch_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="border:none;cursor:pointer;padding:.6rem 1.2rem;border-radius:8px;">{{ __('Save') }}</button>
    </form>
@endsection
