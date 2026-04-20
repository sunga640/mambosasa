@extends('layouts.admin')

@section('title', __('Pending payments'))

@section('content')
    <div style="margin:0 0 1rem;">
        <h1 class="text-24" style="margin:0;line-height:1.2;">{{ __('Pending payments') }}</h1>
        <p class="text-13" style="opacity:.82;margin:.25rem 0 0;max-width:42rem;line-height:1.45;">
            {{ __('Confirm bookings once payment has been received (cash, bank transfer, mobile money, etc.).') }}
        </p>
    </div>

    @if (session('status'))
        <p class="text-15 mb-20" style="color:#0a0; background:#e8f5e9; padding:10px; border-radius:8px;">
            <i class="fa fa-check-circle mr-5"></i> {{ session('status') }}
        </p>
    @endif

    <form method="GET" action="{{ route('admin.payments.pending') }}" class="form-row mt-20" style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:230px;max-width:360px;">
            <label for="q">{{ __('Advanced search') }}</label>
            <input type="text" id="q" name="q" value="{{ $search ?? '' }}" placeholder="{{ __('Reference, guest, email, phone') }}">
        </div>
        <div style="min-width:220px;">
            <label for="method_id">{{ __('Payment method') }}</label>
            <select name="method_id" id="method_id">
                <option value="0">{{ __('All methods') }}</option>
                @foreach ($methods as $m)
                    <option value="{{ $m->id }}" @selected((int) ($methodId ?? 0) === (int) $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="dash-btn dash-btn--primary">{{ __('Filter') }}</button>
        </div>
    </form>

    @if ($bookings->isEmpty())
        <p class="text-15" style="opacity:.8;">{{ __('No bookings waiting for payment in the current branch filter.') }}</p>
    @else
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Guest') }}</th>
                        <th>{{ __('Room / branch') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Payment Method') }}</th> {{-- COLUMN MPYA --}}
                        <th>{{ __('Invoice') }}</th> {{-- COLUMN MPYA --}}
                        <th>{{ __('Deadline') }}</th>
                        <th style="min-width:200px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $b)
                        <tr>
                            <td><strong>{{ $b->public_reference }}</strong></td>
                            <td>
                                <div class="fw-600">{{ $b->first_name }} {{ $b->last_name }}</div>
                                <div class="text-12 opacity-70">{{ $b->email }}</div>
                            </td>
                            <td>
                                <div class="text-14">{{ $b->room?->name }}</div>
                                <div class="text-12 opacity-70">{{ $b->room?->branch?->name }}</div>
                            </td>
                            <td class="fw-600 text-blue-1">{{ number_format((float) $b->total_amount, 0) }}</td>

                            {{-- 1. Payment Method --}}
                            <td>
                                <span class="text-13 px-10 py-5 rounded-8" style="background:#f1f5f9; border:1px solid #e2e8f0;">
                                    <i class="fa fa-credit-card mr-5 text-light-1"></i> {{ $b->method?->name ?? __('Unknown') }}
                                </span>
                            </td>

                            {{-- 2. Invoice View --}}
                            <td>
                                @if ($b->invoice)
                                    <a href="{{ $b->invoice->publicUrl() }}" target="_blank" class="text-blue-1 fw-600 underline" style="font-size:.85rem;">
                                        <i class="fa fa-file-invoice mr-5"></i> {{ __('View Invoice') }}
                                    </a>
                                @else
                                    <span class="text-12 opacity-40 italic">{{ __('No invoice') }}</span>
                                @endif
                            </td>

                            <td>
                                <div class="text-13 {{ $b->payment_deadline_at?->isPast() ? 'text-red-1 fw-600' : '' }}">
                                    {{ $b->payment_deadline_at?->format('Y-m-d H:i') ?? '—' }}
                                </div>
                            </td>

                            <td>
                                <div style="display:flex;flex-direction:column;align-items:flex-start;gap:.45rem;">
                                    <a href="{{ route('admin.bookings.show', $b) }}" class="dash-btn dash-btn--ghost" style="font-size:.8rem;text-decoration:none;">{{ __('View booking') }}</a>
                                    <form method="POST" action="{{ route('admin.payments.resend-reminder', $b) }}" class="m-0">
                                        @csrf
                                        <button type="submit" class="dash-btn dash-btn--ghost" style="font-size:.8rem;">{{ __('Resend reminder') }}</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.payments.cancel', $b) }}" class="m-0" onsubmit="return confirm(@json(__('Cancel this pending booking? The guest will need to book again.')));">
                                        @csrf
                                        <button type="submit" class="dash-btn" style="font-size:.8rem;color:#b91c1c;border-color:#fecaca;background:#fff5f5;">{{ __('Cancel booking') }}</button>
                                    </form>
                                    @if (auth()->user()?->isSuperAdmin())
                                        <form method="POST" action="{{ route('admin.payments.confirm', $b) }}" onsubmit="return confirm(@json(__('Mark this booking as paid and confirmed?')));" class="m-0">
                                            @csrf
                                            <button type="submit" class="dash-btn dash-btn--primary" style="font-size:.85rem; white-space: nowrap;">
                                                <i class="fa fa-check mr-5"></i> {{ __('Confirm payment') }}
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-20">{{ $bookings->links() }}</div>
    @endif
@endsection
