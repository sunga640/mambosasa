@extends('layouts.member')

@php use App\Enums\BookingStatus; @endphp

@section('title', __('Bookings'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('My bookings') }}</h1>
@endsection

@section('content')
    @if (isset($memberBranchesForFilter) && $memberBranchesForFilter->count() > 1)
        <form method="POST" action="{{ route('member.branch-filter') }}" style="display:flex;flex-wrap:wrap;align-items:center;gap:.65rem;margin-bottom:1.25rem;padding:.85rem 1rem;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;">
            @csrf
            <label class="text-14 fw-600" for="mb-branch">{{ __('Filter by branch') }}</label>
            <select name="branch_id" id="mb-branch" onchange="this.form.submit()" style="padding:.45rem .65rem;border-radius:8px;border:1px solid #ccc;min-width:12rem;font:inherit;">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($memberBranchesForFilter as $br)
                    <option value="{{ $br->id }}" @selected((string) ($memberBranchFilterId ?? '') === (string) $br->id)>{{ $br->name }}</option>
                @endforeach
            </select>
        </form>
    @endif
    <p class="text-15" style="line-height:1.6;color:#333;margin-bottom:1.5rem;">
        {{ __('Each row shows your stay window, payment status, and whether the room is currently held or in use.') }}
    </p>

    @if ($bookings->isEmpty())
        <p class="text-15 text-light-1">{{ __('You have no bookings yet.') }} <a href="{{ route('site.booking') }}">{{ __('Book a room') }}</a></p>
    @else
        <div class="table-responsive" style="overflow-x:auto;">
            <table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:920px;">
                <thead>
                    <tr style="text-align:left;border-bottom:1px solid #ddd;">
                        <th style="padding:0.65rem;">{{ __('Reference') }}</th>
                        <th style="padding:0.65rem;">{{ __('Room') }}</th>
                        <th style="padding:0.65rem;">{{ __('Room #') }}</th>
                        <th style="padding:0.65rem;">{{ __('Floor') }}</th>
                        <th style="padding:0.65rem;">{{ __('Branch') }}</th>
                        <th style="padding:0.65rem;">{{ __('Stay (from → to)') }}</th>
                        <th style="padding:0.65rem;">{{ __('Room in use') }}</th>
                        <th style="padding:0.65rem;">{{ __('Booking status') }}</th>
                        <th style="padding:0.65rem;">{{ __('Payment') }}</th>
                        <th style="padding:0.65rem;">{{ __('Invoice') }}</th>
                        <th style="padding:0.65rem;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $b)
                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:0.65rem;">{{ $b->public_reference }}</td>
                            <td style="padding:0.65rem;">{{ $b->room->name }}</td>
                            <td style="padding:0.65rem;">{{ $b->room->room_number ?? '—' }}</td>
                            <td style="padding:0.65rem;">{{ $b->room->floor_number === 0 ? __('Ground') : $b->room->floor_number }}</td>
                            <td style="padding:0.65rem;">{{ $b->room->branch?->name ?? '—' }}</td>
                            <td style="padding:0.65rem;">
                                @if ($b->check_in && $b->check_out)
                                    {{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding:0.65rem;">{{ $b->room->isEffectivelyInUse() ? __('Yes') : __('No') }}</td>
                            <td style="padding:0.65rem;">{{ $b->status->label() }}</td>
                            <td style="padding:0.65rem;">
                                @if ($b->status === BookingStatus::PendingPayment && $b->payment_deadline_at)
                                    <span title="{{ $b->payment_deadline_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}">{{ __('Pay by') }}</span>
                                    <div class="text-13" style="opacity:.85;">{{ $b->payment_deadline_at->timezone(config('app.timezone'))->format('Y-m-d H:i') }}</div>
                                @elseif ($b->status === BookingStatus::Confirmed)
                                    {{ __('Paid / confirmed') }}
                                @else
                                    {{ $b->status->label() }}
                                @endif
                            </td>
                            <td style="padding:0.65rem;">
                                @if ($b->invoice)
                                    <a href="{{ $b->invoice->publicUrl() }}" target="_blank" rel="noopener">{{ $b->invoice->number }}</a>
                                @else
                                    —
                                @endif
                            </td>
                            <td style="padding:0.65rem;">
                                <a href="{{ route('bookings.show', $b) }}">{{ __('Details') }}</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-30">{{ $bookings->links() }}</div>
    @endif
@endsection
