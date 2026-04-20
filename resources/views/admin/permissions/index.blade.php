@extends('layouts.admin')

@section('title', __('Permissions'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Permissions') }}</h1>
        <a href="{{ route('admin.permissions.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create permission') }}</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Description') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($permissions as $permission)
                <tr>
                    <td>{{ $permission->name }}</td>
                    <td><code>{{ $permission->slug }}</code></td>
                    <td>{{ $permission->description ?? '—' }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.permissions.edit', $permission) }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" data-swal-delete
                              data-swal-title="{{ __('Delete this permission?') }}"
                              data-swal-text="{{ __('This action cannot be undone.') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($permissions->hasPages())
        <div class="mt-30 d-flex justify-center">{{ $permissions->links() }}</div>
    @endif
@endsection
