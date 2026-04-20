@extends('layouts.admin')

@section('title', __('Room types'))

@section('content')
    <div class="d-flex items-center justify-between">
        <h1 class="text-30">{{ __('Room types') }}</h1>
        <a href="{{ route('admin.room-types.create') }}" class="dash-btn dash-btn--primary">{{ __('Add type') }}</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Image') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Rooms count') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($roomTypes as $type)
                <tr>
                    <td>
                        @if ($type->heroImageUrl())
                            <img src="{{ $type->heroImageUrl() }}" alt="" style="width:56px;height:56px;object-fit:cover;border-radius:8px;">
                        @endif
                    </td>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->branch?->name ?? __('All') }}</td>
                    <td>{{ number_format((float) $type->price, 0) }} TZS</td>
                    <td>
                        {{ $type->rooms->count() }}
                        @if (($type->max_rooms ?? 0) > 0)
                            / {{ $type->max_rooms }}
                            @if ($type->rooms->count() >= $type->max_rooms)
                                <span style="color:#b42318;font-weight:600;">({{ __('FULL') }})</span>
                            @endif
                        @endif
                    </td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.room-types.edit', $type) }}">{{ __('Edit') }}</a>
                        <a href="{{ route('admin.rooms.index', ['room_type_id' => $type->id]) }}">{{ __('Rooms') }}</a>
                        <a href="{{ route('admin.rooms.create', ['room_type_id' => $type->id, 'branch_id' => $type->hotel_branch_id]) }}">{{ __('Add room') }}</a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $roomTypes->links() }}</div>
@endsection
