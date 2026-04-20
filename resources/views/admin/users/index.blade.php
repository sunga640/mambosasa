@extends('layouts.admin')

@section('title', __('Users'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('System users') }}</h1>
        <a href="{{ route('admin.users.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create user') }}</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Role') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($users as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>{{ $user->role?->name ?? '—' }}</td>
                    <td>{{ $user->hotelBranch?->name ?? '—' }}</td>
                    <td>
                        @if ($user->is_active ?? true)
                            <span style="color:#15803d;font-weight:600;">{{ __('Active') }}</span>
                        @else
                            <span style="color:#b45309;font-weight:600;">{{ __('Inactive') }}</span>
                        @endif
                    </td>
                    <td class="admin-actions">
                        @if ($user->id !== auth()->id())
                            <form method="POST" action="{{ route('admin.users.toggle-active', $user) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:none;border:none;color:#1565c0;cursor:pointer;padding:0;">
                                    {{ ($user->is_active ?? true) ? __('Deactivate') : __('Activate') }}
                                </button>
                            </form>
                        @endif
                        <a href="{{ route('admin.users.edit', $user) }}">{{ __('Edit') }}</a>
                        @if ($user->id !== auth()->id())
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" data-swal-delete
                                  data-swal-title="{{ __('Delete this user?') }}"
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
    <div class="mt-20">{{ $users->links() }}</div>
@endsection
