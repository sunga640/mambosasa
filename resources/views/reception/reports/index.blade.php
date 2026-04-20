@extends('layouts.reception')

@section('title', __('Reports'))

@section('content')
    <h1 class="text-30">{{ __('Reception reports') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;">{{ __('Filter by booking creation date. Metrics use confirmed bookings only.') }}</p>

    <form method="GET" action="{{ route('reception.reports.index') }}" class="mt-25" style="display:flex;flex-wrap:wrap;gap:1rem;align-items:flex-end;">
        <div class="form-row" style="margin:0;">
            <label for="from">{{ __('From') }}</label>
            <input type="date" name="from" id="from" value="{{ $from->format('Y-m-d') }}">
        </div>
        <div class="form-row" style="margin:0;">
            <label for="to">{{ __('To') }}</label>
            <input type="date" name="to" id="to" value="{{ $to->format('Y-m-d') }}">
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="padding:.5rem 1rem;border-radius:8px;border:none;cursor:pointer;">{{ __('Apply') }}</button>
    </form>

    <div class="mt-30" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.25rem;">
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:#fffbeb;">
            <h2 class="text-16" style="margin:0 0 .5rem;">{{ __('Cash (confirmed)') }}</h2>
            <div class="text-26 fw-600">{{ number_format($cashTotal, 0) }}</div>
        </div>
        <div style="padding:1.25rem;border:1px solid #e5e5e5;border-radius:12px;background:#f0fdf4;">
            <h2 class="text-16" style="margin:0 0 .5rem;">{{ __('Non-cash (confirmed)') }}</h2>
            <div class="text-26 fw-600">{{ number_format($nonCashTotal, 0) }}</div>
        </div>
    </div>

    <div class="mt-30">
        <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('By payment method') }}</h2>
        @if ($paymentByMethod->isEmpty())
            <p class="text-14" style="opacity:.75;">{{ __('No confirmed bookings in this period.') }}</p>
        @else
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Bookings') }}</th>
                        <th>{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($paymentByMethod as $row)
                        <tr>
                            <td>{{ $row->method_name }}</td>
                            <td>{{ number_format((int) $row->c) }}</td>
                            <td>{{ number_format((float) $row->total, 0) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
