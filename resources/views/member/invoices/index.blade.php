@extends('layouts.member')

@section('title', __('My invoice'))
@section('breadcrumb', __('My invoice'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('My invoice') }}</h1>
@endsection

@section('content')
    @if ($invoices->isEmpty())
        <p class="text-15">{{ __('No invoices found yet.') }}</p>
    @else
        <div class="table-responsive" style="overflow-x:auto;">
            <table class="table" style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb;text-align:left;">
                        <th style="padding:.65rem;">{{ __('Invoice #') }}</th>
                        <th style="padding:.65rem;">{{ __('Booking') }}</th>
                        <th style="padding:.65rem;">{{ __('Room') }}</th>
                        <th style="padding:.65rem;">{{ __('Issued') }}</th>
                        <th style="padding:.65rem;">{{ __('Amount') }}</th>
                        <th style="padding:.65rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr style="border-bottom:1px solid #f0f0f0;">
                            <td style="padding:.65rem;">{{ $invoice->number }}</td>
                            <td style="padding:.65rem;">{{ $invoice->booking?->public_reference ?? '—' }}</td>
                            <td style="padding:.65rem;">{{ $invoice->booking?->room?->name ?? '—' }}</td>
                            <td style="padding:.65rem;">{{ $invoice->issued_at?->format('Y-m-d H:i') ?? '—' }}</td>
                            <td style="padding:.65rem;">{{ number_format((float) $invoice->total_amount, 0) }} {{ $invoice->currency }}</td>
                            <td style="padding:.65rem;"><a href="{{ $invoice->publicUrl() }}">{{ __('Open') }}</a></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-15">
            {{ $invoices->links() }}
        </div>
    @endif
@endsection
