@extends('layouts.reception')

@section('title', $customer->first_name.' '.$customer->last_name)

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="text-30" style="margin:0;">{{ $customer->first_name }} {{ $customer->last_name }}</h1>
            <p class="text-14 mt-10" style="opacity:.8;">{{ $customer->email }} · {{ $customer->phone }}</p>
        </div>
        <a href="{{ route('reception.customers.index') }}">{{ __('← Customers') }}</a>
    </div>

    <h2 class="text-20 mt-30 mb-15">{{ __('Bookings & rooms (your branch scope)') }}</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Stay') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Total') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($bookings as $b)
                <tr>
                    <td>{{ $b->public_reference }}</td>
                    <td>{{ $b->room?->name }} (#{{ $b->room?->room_number ?? '—' }})</td>
                    <td>
                        @if ($b->check_in && $b->check_out)
                            {{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $b->status->label() }}</td>
                    <td>{{ $b->method?->name ?? '—' }}</td>
                    <td>{{ number_format((float) $b->total_amount, 0) }}</td>
                    <td><a href="{{ route('reception.bookings.show', $b) }}">{{ __('View') }}</a></td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
