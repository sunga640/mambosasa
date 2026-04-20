@extends('layouts.admin')

@section('title', __('Room ranks'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Room ranks') }}</h1>
        <a href="{{ route('admin.room-ranks.create') }}" class="dash-btn dash-btn--primary" style="text-decoration:none;">{{ __('Create rank') }}</a>
    </div>
    <p class="text-14 mt-10" style="opacity:.8;max-width:40rem;">{{ __('Standard, VIP, VVIP, MVP (or your own labels). Assign each room to a rank for pricing pages and the public site.') }}</p>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Slug') }}</th>
                <th>{{ __('Sort') }}</th>
                <th>{{ __('Active') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ranks as $rank)
                <tr>
                    <td>{{ $rank->name }}</td>
                    <td><code>{{ $rank->slug }}</code></td>
                    <td>{{ $rank->sort_order }}</td>
                    <td>{{ $rank->is_active ? __('Yes') : __('No') }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.room-ranks.edit', $rank) }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.room-ranks.destroy', $rank) }}" method="POST" data-swal-delete
                              data-swal-title="{{ __('Delete this rank?') }}"
                              data-swal-text="{{ __('Only ranks with no rooms can be removed.') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @if ($ranks->hasPages())
        <div class="mt-30 d-flex justify-center">{{ $ranks->links() }}</div>
    @endif
@endsection
