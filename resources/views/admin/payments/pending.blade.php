@extends('layouts.admin')

@section('title', __('Pending payments'))

@section('content')
    <style>
        .pending-pay-table {
            table-layout: fixed;
            width: 100%;
        }
        .pending-pay-row td {
            padding-top: .8rem;
            padding-bottom: .8rem;
            vertical-align: top;
        }
        .pending-pay-cell-stack {
            display: grid;
            gap: .18rem;
        }
        .pending-pay-primary {
            font-weight: 700;
            color: #e5eef8;
            line-height: 1.35;
        }
        .pending-pay-meta {
            font-size: .78rem;
            opacity: .76;
            line-height: 1.4;
            word-break: break-word;
        }
        .pending-pay-amount {
            font-size: .98rem;
            font-weight: 700;
            white-space: nowrap;
        }
        .pending-pay-method-badge {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            max-width: 100%;
            padding: .42rem .72rem;
            border: 1px solid rgba(96, 165, 250, .28);
            background: linear-gradient(135deg, rgba(15, 23, 42, .96) 0%, rgba(30, 64, 175, .92) 100%);
            color: #eff6ff;
            font-size: .82rem;
            font-weight: 700;
            line-height: 1.35;
            white-space: normal;
            word-break: break-word;
        }
        .pending-pay-method-badge i {
            color: #93c5fd;
            flex-shrink: 0;
        }
        .pending-pay-dropdown {
            position: relative;
            width: 100%;
        }
        .pending-pay-dropdown summary {
            list-style: none;
            cursor: pointer;
        }
        .pending-pay-dropdown summary::-webkit-details-marker {
            display: none;
        }
        .pending-pay-dropdown__trigger {
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            gap: .55rem;
            min-width: 9.8rem;
            padding: .58rem .78rem;
            border: 1px solid rgba(148, 163, 184, .24);
            background: rgba(15, 23, 42, .88);
            color: #f8fafc;
            font-size: .8rem;
            font-weight: 700;
            line-height: 1.2;
        }
        .pending-pay-dropdown[open] .pending-pay-dropdown__trigger {
            border-color: rgba(96, 165, 250, .42);
            box-shadow: 0 10px 24px rgba(15, 23, 42, .18);
        }
        .pending-pay-dropdown__menu {
            position: absolute;
            right: 0;
            top: calc(100% + .45rem);
            z-index: 20;
            min-width: 14rem;
            padding: .7rem;
            border: 1px solid rgba(148, 163, 184, .2);
            background: #fff;
            box-shadow: 0 18px 40px rgba(15, 23, 42, .16);
            display: grid;
            gap: .45rem;
        }
        .pending-pay-dropdown__menu .dash-btn,
        .pending-pay-dropdown__menu a.dash-btn {
            width: 100%;
            justify-content: flex-start;
            text-decoration: none;
            white-space: nowrap;
        }
        @media (max-width: 991px) {
            .pending-pay-table {
                min-width: 980px;
            }
        }
    </style>
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
            <table class="admin-table pending-pay-table">
                <thead>
                    <tr>
                        <th>{{ __('Reference') }}</th>
                        <th>{{ __('Guest') }}</th>
                        <th>{{ __('Room / branch') }}</th>
                        <th>{{ __('Amount') }}</th>
                        <th>{{ __('Payment Method') }}</th>
                        <th>{{ __('Invoice') }}</th>
                        <th>{{ __('Deadline') }}</th>
                        <th style="min-width:200px;">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $b)
                        <tr class="pending-pay-row">
                            <td>
                                <div class="pending-pay-cell-stack">
                                    <span class="pending-pay-primary">{{ $b->public_reference }}</span>
                                    <span class="pending-pay-meta">{{ __('Created') }} {{ $b->created_at?->format('Y-m-d H:i') }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="pending-pay-cell-stack">
                                    <div class="pending-pay-primary">{{ $b->first_name }} {{ $b->last_name }}</div>
                                    <div class="pending-pay-meta">{{ $b->email }}</div>
                                    @if ($b->phone)
                                        <div class="pending-pay-meta">{{ $b->phone }}</div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="pending-pay-cell-stack">
                                    <div class="pending-pay-primary">{{ $b->room?->name ?: __('No room') }}</div>
                                    <div class="pending-pay-meta">{{ $b->room?->branch?->name ?: __('No branch') }}</div>
                                </div>
                            </td>
                            <td>
                                <div class="pending-pay-cell-stack">
                                    <span class="pending-pay-amount text-blue-1">{{ number_format((float) $b->total_amount, 0) }}</span>
                                    <span class="pending-pay-meta">{{ __('TZS') }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="pending-pay-method-badge">
                                    <i class="fa fa-credit-card"></i>
                                    <span>{{ $b->method?->name ?? __('Unknown') }}</span>
                                </span>
                            </td>
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
                                <div class="pending-pay-cell-stack">
                                    <div class="pending-pay-primary {{ $b->payment_deadline_at?->isPast() ? 'text-red-1' : '' }}">
                                        {{ $b->payment_deadline_at?->format('Y-m-d') ?? '—' }}
                                    </div>
                                    <div class="pending-pay-meta {{ $b->payment_deadline_at?->isPast() ? 'text-red-1 fw-600' : '' }}">
                                        {{ $b->payment_deadline_at?->format('H:i') ?? __('No deadline') }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <details class="pending-pay-dropdown">
                                    <summary class="pending-pay-dropdown__trigger">
                                        <span>{{ __('Booking actions') }}</span>
                                        <i class="fa fa-chevron-down" aria-hidden="true"></i>
                                    </summary>
                                    <div class="pending-pay-dropdown__menu">
                                        <a href="{{ route('admin.bookings.show', $b) }}" class="dash-btn dash-btn--ghost" style="font-size:.8rem;">{{ __('View booking') }}</a>
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
                                                <button type="submit" class="dash-btn dash-btn--primary" style="font-size:.85rem;">
                                                    <i class="fa fa-check mr-5"></i> {{ __('Confirm payment') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-20">{{ $bookings->links() }}</div>
    @endif
@endsection
