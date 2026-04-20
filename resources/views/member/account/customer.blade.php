@extends('layouts.member')

@section('title', __('Guest profile & history'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('Guest profile & bookings') }}</h1>
@endsection

@section('content')
    <p class="text-15" style="line-height:1.6;color:#333;margin-bottom:1.5rem;">
        {{ __('Your saved profile from reservations and every stay linked to your account email.') }}
    </p>

    @if ($customer)
        <div class="p-20 rounded-8 mb-30" style="border:1px solid #e5e5e5;background:#fafafa;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Customer record') }}</h2>
            <p class="text-15" style="margin:.25rem 0;">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            <p class="text-14" style="margin:.25rem 0;opacity:.85;">{{ $customer->email }} · {{ $customer->phone }}</p>
            <p class="text-13" style="margin:.5rem 0 0;opacity:.7;">{{ __('Last booking activity') }}: {{ $customer->last_booking_at?->format('Y-m-d H:i') ?? '—' }}</p>
        </div>
    @else
        <p class="text-15 mb-30" style="opacity:.85;">{{ __('No guest profile stored yet — it is created when you complete a booking.') }}</p>
    @endif

    <h2 class="text-20 mb-15">{{ __('All bookings & rooms') }}</h2>
    <div class="table-responsive" style="overflow-x:auto;">
        <table class="table" style="width:100%;border-collapse:collapse;font-size:0.9rem;min-width:800px;">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #ddd;">
                    <th style="padding:0.65rem;">{{ __('Booked') }}</th>
                    <th style="padding:0.65rem;">{{ __('Room') }}</th>
                    <th style="padding:0.65rem;">{{ __('Stay') }}</th>
                    <th style="padding:0.65rem;">{{ __('Booking status') }}</th>
                    <th style="padding:0.65rem;">{{ __('In use') }}</th>
                    <th style="padding:0.65rem;">{{ __('Paid') }}</th>
                    <th style="padding:0.65rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $b)
                    <tr style="border-bottom:1px solid #eee;">
                        <td style="padding:0.65rem;">{{ $b->created_at?->format('Y-m-d H:i') }}</td>
                        <td style="padding:0.65rem;">{{ $b->room?->name }} <span class="text-13" style="opacity:.75;">#{{ $b->room?->room_number ?? '—' }}</span></td>
                        <td style="padding:0.65rem;">
                            @if ($b->check_in && $b->check_out)
                                {{ $b->check_in->format('Y-m-d') }} → {{ $b->check_out->format('Y-m-d') }}
                            @else
                                —
                            @endif
                        </td>
                        <td style="padding:0.65rem;">{{ $b->status->label() }}</td>
                        <td style="padding:0.65rem;">{{ $b->room?->isEffectivelyInUse() ? __('Yes') : __('No') }}</td>
                        <td style="padding:0.65rem;">{{ $b->status === \App\Enums\BookingStatus::Confirmed ? __('Yes') : __('No') }}</td>
                        <td style="padding:0.65rem;"><a href="{{ route('bookings.show', $b) }}">{{ __('Details') }}</a></td>
                    </tr>
                @empty
                    <tr><td colspan="7" style="padding:0.65rem;">{{ __('No bookings yet.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-30">{{ $bookings->links() }}</div>
@endsection
