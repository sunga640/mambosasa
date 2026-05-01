@extends('layouts.admin')

@section('title', __('Permissions'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="text-30">{{ __('Permissions') }}</h1>
            <p class="text-14 mt-5" style="opacity:.7;">{{ __('Permissions are organized by business module so role setup stays complete and easy to audit.') }}</p>
        </div>
        <a href="{{ route('admin.permissions.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create permission') }}</a>
    </div>

    <div style="display:grid;gap:1rem;margin-top:1.25rem;">
        @foreach ($permissionGroups as $group)
            <section style="border:1px solid #e5e7eb;background:#fff;padding:1rem;">
                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;margin-bottom:.85rem;">
                    <div>
                        <h2 class="text-18 fw-700" style="margin:0;">{{ $group['label'] }}</h2>
                        <p class="text-13 mt-5" style="opacity:.7;margin:0;">{{ $group['description'] }}</p>
                    </div>
                    <span class="text-12" style="font-weight:700;letter-spacing:.08em;text-transform:uppercase;opacity:.6;">{{ $group['permissions']->count() }} {{ __('items') }}</span>
                </div>

                <table class="admin-table" style="margin-top:0;">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Slug') }}</th>
                            <th>{{ __('Description') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($group['permissions'] as $permission)
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
            </section>
        @endforeach
    </div>
@endsection
