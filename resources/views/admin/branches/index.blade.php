@extends('layouts.admin')

@section('title', __('Hotel branches'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Hotel branches') }}</h1>
        <a href="{{ route('admin.branches.create') }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create branch') }}</a>
    </div>
    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Location') }}</th>
                <th>{{ __('Floors') }}</th>
                <th>{{ __('Status') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($branches as $branch)
                <tr>
                    <td>
                        @if ($branch->logo_path)
                            <img src="{{ \App\Support\PublicDisk::url($branch->logo_path) }}" alt="" style="max-height:36px;vertical-align:middle;margin-right:8px;border-radius:4px;">
                        @endif
                        {{ $branch->name }}
                    </td>
                    <td>{{ \Illuminate\Support\Str::limit($branch->location_address, 48) }}</td>
                    <td>
                        @if ($branch->is_ground_floor_only)
                            {{ __('Ground only') }}
                        @else
                            {{ $branch->floors_count }}
                        @endif
                    </td>
                    <td>{{ $branch->is_active ? __('Active') : __('Inactive') }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.branches.edit', $branch) }}">{{ __('Edit') }}</a>
                        <a href="{{ route('admin.rooms.index', ['branch_id' => $branch->id]) }}">{{ __('Rooms') }}</a>
                        <form action="{{ route('admin.branches.destroy', $branch) }}" method="POST" data-swal-delete
                              data-swal-title="{{ __('Delete branch?') }}"
                              data-swal-text="{{ __('All rooms and media in this branch will be removed. This cannot be undone.') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $branches->links() }}</div>
@endsection
