@extends('layouts.admin')

@section('title', __('Rooms'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">
            @if (!empty($currentRoomTypeName))
                {{ __('Rooms in :name', ['name' => $currentRoomTypeName]) }}
            @else
                {{ __('Rooms in :branch', ['branch' => $currentBranchName]) }}
            @endif
        </h1>
        <a href="{{ route('admin.rooms.create', ['branch_id' => $filterBranchId, 'room_type_id' => $filterRoomTypeId]) }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;display:inline-block;padding:.5rem 1rem;border-radius:8px;">{{ __('Create room') }}</a>
    </div>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Number') }}</th>
                <th>{{ __('Floor') }}</th>
                <th>{{ __('Booking / payment') }}</th>
                <th>{{ __('In use') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Price') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rooms as $room)
                @php
                    $rb = $room->bookings->first(function ($bk) {
                        return $bk->status === \App\Enums\BookingStatus::PendingPayment
                            && $bk->payment_deadline_at
                            && $bk->payment_deadline_at->isFuture();
                    }) ?? $room->bookings->firstWhere('status', \App\Enums\BookingStatus::Confirmed);
                @endphp
                <tr>
                    <td>{{ $room->name }}</td>
                    <td>{{ $room->room_number ?? '—' }}</td>
                    <td>{{ $room->floor_number === 0 ? __('Ground') : $room->floor_number }}</td>
                    <td>
                        @if ($rb)
                            <div>{{ $rb->status->label() }}</div>
                            @if ($rb->invoice)
                                <a class="text-13" href="{{ $rb->invoice->publicUrl() }}" target="_blank" rel="noopener">{{ __('Invoice') }}</a>
                            @endif
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($room->isEffectivelyInUse())
                            <span style="color:#b45309;font-weight:600;">{{ __('Yes') }}</span>
                        @else
                            <span style="opacity:.65;">{{ __('No') }}</span>
                        @endif
                        <form action="{{ route('admin.rooms.toggle-in-use', $room) }}" method="POST" style="display:inline;margin-left:.5rem;">
                            @csrf
                            <button type="submit" class="text-13" style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:6px;padding:.2rem .45rem;cursor:pointer;" title="{{ __('Toggle manual in-use flag') }}">
                                {{ $room->force_in_use ? __('Unforce') : __('Force') }}
                            </button>
                        </form>
                    </td>
                    <td>{{ $room->status->label() }}</td>
                    <td>{{ number_format((float) $room->price, 0) }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.rooms.edit', $room) }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.rooms.destroy', $room) }}" method="POST" data-swal-delete
                              data-swal-title="{{ __('Delete room?') }}"
                              data-swal-text="{{ __('Room media will be removed from storage.') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $rooms->links() }}</div>
@endsection
