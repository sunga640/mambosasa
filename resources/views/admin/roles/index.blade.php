@extends('layouts.admin')

@section('title', __('Roles'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Roles') }}</h1>
        <a href="{{ route('admin.roles.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create role') }}</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Permissions') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roles as $role)
                <tr>
                    <td>{{ $role->name }} @if($role->is_system)<span class="text-13" style="opacity:.6;">({{ __('system') }})</span>@endif</td>
                    <td><code>{{ $role->slug }}</code></td>
                    <td>{{ $role->permissions->pluck('name')->join(', ') ?: '—' }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.roles.edit', $role) }}">{{ __('Edit') }}</a>
                        @if (! $role->is_system)
                            <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" data-swal-delete
                                  data-swal-title="{{ __('Delete this role?') }}"
                                  data-swal-text="{{ __('This action cannot be undone.') }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($roles->hasPages())
        <div class="mt-30 d-flex justify-center">{{ $roles->links() }}</div>
    @endif
@endsection
