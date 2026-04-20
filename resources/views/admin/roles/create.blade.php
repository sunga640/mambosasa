@extends('layouts.admin')

@section('title', __('Create role'))

@section('content')
    <h1 class="text-30">{{ __('Create role') }}</h1>
    <form method="POST" action="{{ route('admin.roles.store') }}" class="mt-30">
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
            <label>{{ __('Permissions') }}</label>
            <p class="text-13 mb-10" style="opacity:.75;">{{ __('Grouped by permission slug prefix (matrix).') }}</p>
            @php
                $grouped = $permissions->groupBy(function ($p) {
                    $s = (string) $p->slug;
                    $seg = preg_split('/[.\-]/', $s);

                    return $seg[0] !== '' ? $seg[0] : 'general';
                })->sortKeys();
            @endphp
            <div style="display:flex;flex-direction:column;gap:1rem;">
                @foreach ($grouped as $group => $perms)
                    <div style="border:1px solid #e5e5e5;border-radius:10px;padding:1rem;background:#fafafa;">
                        <div class="text-12" style="text-transform:uppercase;letter-spacing:.08em;font-weight:700;color:#64748b;margin-bottom:.65rem;">{{ ucfirst($group) }}</div>
                        <div class="perm-grid" style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:.5rem;">
                            @foreach ($perms as $permission)
                                <label style="display:flex;align-items:flex-start;gap:.45rem;font-weight:400;">
                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked(in_array($permission->id, old('permission_ids', []), true))>
                                    <span><span class="fw-600">{{ $permission->name }}</span><span class="text-12" style="display:block;opacity:.65;">{{ $permission->slug }}</span></span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
            @error('permission_ids')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="border:none;cursor:pointer;padding:.6rem 1.2rem;border-radius:8px;">{{ __('Save') }}</button>
    </form>
@endsection
