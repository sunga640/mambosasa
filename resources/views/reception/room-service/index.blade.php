@extends('layouts.reception')

@section('title', __('Room service orders'))

@section('content')
    <h1 class="text-30">{{ __('Room service orders') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;">{{ __('Update status as kitchen and delivery progress. ETA is shown to the guest.') }}</p>

    @if (session('status'))
        <p class="text-15 mt-15" style="color:#0a6b0a;">{{ session('status') }}</p>
    @endif

    <table class="admin-table mt-25">
        <thead>
            <tr>
                <th>{{ __('When') }}</th>
                <th>{{ __('Guest') }}</th>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Items') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('ETA') }}</th>
                <th>{{ __('Total') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($orders as $o)
                <tr>
                    <td>{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        <div>{{ $o->user?->name }}</div>
                        <div class="text-13" style="opacity:.75;">{{ $o->user?->email }}</div>
                    </td>
                    <td>{{ $o->room?->name }} (#{{ $o->room?->room_number ?? '—' }})</td>
                    <td>{{ $o->branch?->name }}</td>
                    <td>
                        @foreach ($o->items as $line)
                            <div class="text-13">{{ $line->item_name }} × {{ $line->quantity }}</div>
                        @endforeach
                    </td>
                    <td>{{ $o->statusEnum()->label() }}</td>
                    <td>{{ $o->estimated_ready_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td>{{ number_format((float) $o->total_amount, 0) }}</td>
                    <td>
                        <form method="POST" action="{{ route('reception.room-service.update', $o) }}" style="display:flex;flex-direction:column;gap:.35rem;">
                            @csrf
                            <select name="status" class="text-13" onchange="this.form.submit()">
                                @foreach (\App\Enums\RoomServiceOrderStatus::cases() as $st)
                                    <option value="{{ $st->value }}" @selected($o->status === $st->value)>{{ $st->label() }}</option>
                                @endforeach
                            </select>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="9">{{ __('No orders yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-20">{{ $orders->links() }}</div>
@endsection
