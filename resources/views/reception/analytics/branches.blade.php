@extends('layouts.reception')

@section('title', __('Branch comparison'))

@section('content')
    <h1 class="text-30">{{ __('Branch analytics & comparison') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;">{{ __('Director view: all branches. Use the header branch filter to limit reception screens to one property.') }}</p>

    <table class="admin-table mt-25">
        <thead>
            <tr>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Rooms') }}</th>
                <th>{{ __('Bookings (all)') }}</th>
                <th>{{ __('Confirmed') }}</th>
                <th>{{ __('Revenue (confirmed)') }}</th>
                <th>{{ __('Active maintenance') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $row)
                <tr>
                    <td class="fw-600">{{ $row['branch']->name }}</td>
                    <td>{{ number_format($row['rooms']) }}</td>
                    <td>{{ number_format($row['bookings_total']) }}</td>
                    <td>{{ number_format($row['bookings_confirmed']) }}</td>
                    <td>{{ number_format($row['revenue'], 0) }}</td>
                    <td>{{ number_format($row['maintenance_active']) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
