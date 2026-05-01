@extends('layouts.plain')

@section('title', __('Kitchen Bill').' '.$order->billReference())

@push('head')
<meta name="robots" content="noindex,nofollow">
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
  :root {
    --bill-gold: #d5ac42;
    --bill-ink: #111827;
    --bill-muted: #6b7280;
    --bill-border: #e5e7eb;
    --bill-surface: #ffffff;
    --bill-bg: #f3f4f6;
  }
  body {
    margin: 0;
    background: var(--bill-bg);
    color: var(--bill-ink);
    font-family: 'Manrope', sans-serif;
    -webkit-print-color-adjust: exact;
  }
  .bill-wrap { max-width: 920px; margin: 2rem auto; padding: 0 1rem; }
  .bill-card { background: var(--bill-surface); border: 1px solid var(--bill-border); box-shadow: 0 16px 40px rgba(17, 24, 39, 0.08); }
  .bill-top { display:flex; justify-content:space-between; gap:1.5rem; padding:2.2rem 2.2rem 1.5rem; border-bottom:1px solid var(--bill-border); }
  .bill-brand h1 { margin:0; font-size:1.7rem; }
  .bill-brand p, .bill-meta p { margin:.3rem 0 0; color:var(--bill-muted); line-height:1.55; }
  .bill-badge { display:inline-flex; align-items:center; gap:.4rem; padding:.45rem .8rem; border:1px solid rgba(213,172,66,.32); background:rgba(213,172,66,.12); color:#8b6914; font-weight:700; text-transform:uppercase; letter-spacing:.08em; font-size:.75rem; }
  .bill-grid { display:grid; grid-template-columns:1fr 1fr; gap:1.4rem; padding:1.6rem 2.2rem 0; }
  .bill-box { border:1px solid var(--bill-border); padding:1rem 1rem .9rem; }
  .bill-box h3 { margin:0 0 .65rem; font-size:.85rem; text-transform:uppercase; letter-spacing:.08em; color:var(--bill-muted); }
  .bill-table-wrap { padding:1.6rem 2.2rem; }
  .bill-table { width:100%; border-collapse:collapse; }
  .bill-table th, .bill-table td { padding:.9rem .8rem; border-bottom:1px solid var(--bill-border); text-align:left; vertical-align:top; }
  .bill-table th { color:var(--bill-muted); text-transform:uppercase; letter-spacing:.06em; font-size:.76rem; }
  .bill-total { display:flex; justify-content:flex-end; padding:0 2.2rem 2rem; }
  .bill-total__card { width:min(100%, 320px); border:1px solid var(--bill-border); padding:1rem 1.1rem; }
  .bill-total__row { display:flex; justify-content:space-between; gap:1rem; padding:.4rem 0; }
  .bill-total__row--grand { border-top:2px solid var(--bill-ink); margin-top:.5rem; padding-top:.9rem; font-size:1.2rem; font-weight:800; }
  .bill-actions { display:flex; justify-content:space-between; align-items:center; gap:1rem; margin-bottom:1rem; }
  .bill-btn { display:inline-flex; align-items:center; justify-content:center; padding:.75rem 1.2rem; border:1px solid var(--bill-border); text-decoration:none; color:var(--bill-ink); background:#fff; cursor:pointer; font:inherit; }
  .bill-btn--primary { background:var(--bill-ink); color:#fff; border-color:var(--bill-ink); }
  @media (max-width: 720px) {
    .bill-top, .bill-grid { grid-template-columns:1fr; display:grid; }
    .bill-top, .bill-grid, .bill-table-wrap, .bill-total { padding-left:1rem; padding-right:1rem; }
    .bill-actions { flex-direction:column; align-items:stretch; }
  }
  @media print {
    body { background:#fff; }
    .bill-wrap { max-width:none; margin:0; padding:0; }
    .bill-card { box-shadow:none; border:none; }
    .no-print { display:none !important; }
  }
</style>
@endpush

@section('content')
@php
  $settings = \App\Models\SystemSetting::current();
@endphp

<div class="bill-wrap">
  <div class="bill-actions no-print">
    <a href="{{ url()->previous() }}" class="bill-btn">{{ __('Back') }}</a>
    <button type="button" class="bill-btn bill-btn--primary" onclick="window.print()">{{ __('Print bill') }}</button>
  </div>

  <div class="bill-card">
    <div class="bill-top">
      <div class="bill-brand">
        <h1>{{ $settings->company_name ?? config('app.name') }}</h1>
        <p>{{ $order->room?->branch?->name ?? __('Hotel branch') }}</p>
        <p>{{ __('Kitchen / room service bill') }}</p>
      </div>
      <div class="bill-meta">
        <div class="bill-badge">{{ $order->isPaid() ? __('Paid') : __('Payment pending') }}</div>
        <p><strong>{{ __('Bill Ref') }}:</strong> {{ $order->billReference() }}</p>
        <p><strong>{{ __('Order Ref') }}:</strong> {{ $order->public_reference ?: ('#'.$order->id) }}</p>
        <p><strong>{{ __('Generated') }}:</strong> {{ $order->bill_generated_at?->format('Y-m-d H:i') }}</p>
      </div>
    </div>

    <div class="bill-grid">
      <div class="bill-box">
        <h3>{{ __('Guest details') }}</h3>
        <div><strong>{{ $order->guest_name ?: __('Guest') }}</strong></div>
        <div>{{ $order->guest_phone ?: __('No phone') }}</div>
        <div>{{ __('Room') }}: {{ $order->room?->name }} (#{{ $order->room?->room_number ?: '-' }})</div>
      </div>
      <div class="bill-box">
        <h3>{{ __('Payment summary') }}</h3>
        <div>{{ __('Order status') }}: {{ $order->statusEnum()->label() }}</div>
        <div>{{ __('Payment state') }}: {{ $order->paymentStatusLabel() }}</div>
        <div>{{ __('Method') }}: {{ $order->bookingMethod?->name ?: __('Not selected') }}</div>
        <div>{{ __('Prepared / delivered at') }}: {{ $order->estimated_ready_at?->format('Y-m-d H:i') ?: '-' }}</div>
      </div>
    </div>

    <div class="bill-table-wrap">
      <table class="bill-table">
        <thead>
          <tr>
            <th>{{ __('Dish') }}</th>
            <th>{{ __('Qty') }}</th>
            <th>{{ __('Unit price') }}</th>
            <th>{{ __('Line total') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($order->items as $item)
            <tr>
              <td>{{ $item->item_name }}</td>
              <td>{{ number_format((int) $item->quantity) }}</td>
              <td>{{ number_format((float) $item->unit_price, 0) }} TZS</td>
              <td>{{ number_format((float) $item->line_total, 0) }} TZS</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="bill-total">
      <div class="bill-total__card">
        <div class="bill-total__row">
          <span>{{ __('Subtotal') }}</span>
          <strong>{{ number_format((float) $order->total_amount, 0) }} TZS</strong>
        </div>
        <div class="bill-total__row">
          <span>{{ __('Notes') }}</span>
          <span style="text-align:right;color:var(--bill-muted);">{{ $order->notes ?: __('None') }}</span>
        </div>
        <div class="bill-total__row bill-total__row--grand">
          <span>{{ __('Amount due') }}</span>
          <span>{{ number_format((float) $order->total_amount, 0) }} TZS</span>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
