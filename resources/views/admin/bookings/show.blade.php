@extends('layouts.admin')

@section('title', __('Booking').' '.$booking->public_reference)

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="text-30" style="margin:0;">{{ $booking->public_reference }}</h1>
            <p class="text-14 mt-10" style="opacity:.8;">{{ __('Created') }} {{ $booking->created_at?->format('Y-m-d H:i') }}</p>
        </div>
        <a href="{{ route('admin.bookings.index') }}">{{ __('← Back to list') }}</a>
    </div>

    @if (session('status'))
        <p class="text-15 mt-20" style="color:#0a6b0a;">{{ session('status') }}</p>
    @endif

    <div class="mt-30" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(260px,1fr));gap:1.25rem;">
        <div style="padding:1rem;border:1px solid #e8e8e8;border-radius:10px;background:#fafafa;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Guest') }}</h2>
            <p class="text-15" style="margin:.35rem 0;">{{ $booking->first_name }} {{ $booking->last_name }}</p>
            <p class="text-14" style="margin:.35rem 0;"><a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a></p>
            <p class="text-14" style="margin:.35rem 0;">{{ $booking->phone }}</p>
            @if ($booking->user)
                <p class="text-13 mt-10" style="opacity:.8;">{{ __('Account') }}: {{ $booking->user->email }}</p>
            @endif
        </div>
        <div style="padding:1rem;border:1px solid #e8e8e8;border-radius:10px;background:#fafafa;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Stay') }}</h2>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Room') }}: <strong>{{ $booking->room?->name ?? '—' }}</strong></p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Room #') }}: {{ $booking->room?->room_number ?? '—' }} · {{ __('Floor') }}: {{ $booking->room?->floor_number ?? '—' }}</p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Branch') }}: {{ $booking->room?->branch?->name ?? '—' }}</p>
            @if ($booking->room?->branch)
            <p class="text-14" style="margin:.35rem 0;">{{ __('Branch status') }}:
                @if ($booking->room->branch->is_active)
                    <span style="color:#15803d;">{{ __('Active') }}</span>
                @else
                    <span style="color:#b45309;">{{ __('Inactive') }}</span>
                @endif
            </p>
            @endif
            <p class="text-14" style="margin:.35rem 0;">{{ __('Check-in') }}: {{ $booking->check_in?->format('Y-m-d') ?? '—' }}</p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Check-out') }}: {{ $booking->check_out?->format('Y-m-d') ?? '—' }}</p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Adults') }} / {{ __('Children') }}: {{ $booking->adults }} / {{ $booking->children }}</p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Rooms') }}: {{ $booking->rooms_count }} · {{ __('Nights') }}: {{ $booking->nights }}</p>
        </div>
        <div style="padding:1rem;border:1px solid #e8e8e8;border-radius:10px;background:#fafafa;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Payment') }}</h2>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Method') }}: {{ $booking->method?->name ?? '—' }}</p>
            <p class="text-14" style="margin:.35rem 0;">{{ __('Total') }}: <strong>{{ number_format((float) $booking->total_amount, 0) }}</strong></p>
            @if ($booking->confirmed_at)
                <p class="text-14" style="margin:.35rem 0;">{{ __('Payment confirmed at') }}: {{ $booking->confirmed_at->format('Y-m-d H:i') }}</p>
            @endif
            @if ($booking->payment_deadline_at)
                <p class="text-14" style="margin:.35rem 0;">{{ __('Payment deadline') }}: {{ $booking->payment_deadline_at->format('Y-m-d H:i') }}</p>
            @endif
        </div>
    </div>

    @if ($booking->invoice)
        <div class="mt-20 p-20" style="border:1px solid #e2e8f0;border-radius:10px;background:#f8fafc;">
            <h2 class="text-18" style="margin:0 0 .75rem;">{{ __('Invoice') }}</h2>
            <p class="text-14">{{ __('Number') }}: <strong>{{ $booking->invoice->number }}</strong></p>
            <div class="admin-actions mt-15">
                <a href="{{ $booking->invoice->publicUrl() }}" target="_blank" rel="noopener">{{ __('Open invoice') }}</a>
                <form method="POST" action="{{ route('admin.invoices.resend', $booking->invoice) }}" style="display:inline;">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:#c41e3a;cursor:pointer;padding:0;">{{ __('Resend email & SMS') }}</button>
                </form>
            </div>
            <div class="mt-20" style="border:1px solid #e2e8f0;border-radius:8px;overflow:hidden;background:#fff;">
                <iframe src="{{ $booking->invoice->publicUrl() }}" title="{{ __('Invoice preview') }}" style="width:100%;min-height:480px;border:0;"></iframe>
            </div>
        </div>
    @endif

    @if ($booking->special_requests)
        <div class="mt-20" style="padding:1rem;border:1px solid #e8e8e8;border-radius:10px;">
            <h2 class="text-18" style="margin:0 0 .5rem;">{{ __('Special requests') }}</h2>
            <p class="text-15" style="margin:0;white-space:pre-wrap;">{{ $booking->special_requests }}</p>
        </div>
    @endif

    <div class="mt-30" style="padding:1.25rem;border:1px solid #e0e0e0;border-radius:10px;max-width:480px;">
        <h2 class="text-18" style="margin:0 0 1rem;">{{ __('Update status') }}</h2>
        <form method="POST" action="{{ route('admin.bookings.update', $booking) }}">
            @csrf
            @method('PUT')
            <div class="form-row">
                <label for="status">{{ __('Status') }}</label>
                <select name="status" id="status" required>
                    @foreach ($statuses as $st)
                        <option value="{{ $st->value }}" @selected($booking->status === $st)>{{ $st->label() }}</option>
                    @endforeach
                </select>
            </div>
            @error('status')
                <p class="text-14" style="color:#c62828;margin-top:.5rem;">{{ $message }}</p>
            @enderror
            <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-15" style="padding:.5rem 1.2rem;border-radius:8px;border:none;cursor:pointer;">{{ __('Save') }}</button>
        </form>
    </div>
@endsection
