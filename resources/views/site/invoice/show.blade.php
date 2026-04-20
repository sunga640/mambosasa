@extends('layouts.plain')

@section('title', __('Invoice').' '.$invoice->number)

@push('head')
<meta name="robots" content="noindex,nofollow">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  :root {
    --primary-gradient: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    --primary-color: #4f46e5;
    --accent-color: #c41e3a;
    --text-main: #1e293b;
    --text-muted: #64748b;
    --border-color: #e2e8f0;
    --bg-light: #f8fafc;
    --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
  }

  body {
    background-color: #f1f5f9;
    font-family: 'Inter', sans-serif;
    color: var(--text-main);
    -webkit-print-color-adjust: exact;
  }

  .inv-wrap { max-width: 850px; margin: 2rem auto; padding: 0 1rem; }

  .inv-card {
    background: #fff;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border-color);
  }

  /* Header Section */
  .inv-header {
    padding: 3rem 2.5rem;
    background: var(--bg-light);
    border-bottom: 1px solid var(--border-color);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
  }

  .company-info h1 { margin: 0; font-size: 1.5rem; font-weight: 700; color: #0f172a; }
  .company-info p { margin: 5px 0 0; color: var(--text-muted); font-size: 0.9rem; line-height: 1.5; }

  .invoice-label { text-align: right; }
  .invoice-label h2 { margin: 0; font-size: 2rem; font-weight: 800; color: var(--accent-color); text-transform: uppercase; letter-spacing: 1px; }
  .invoice-label p { margin: 5px 0 0; font-weight: 600; color: #0f172a; }

  /* Badge */
  .status-badge {
    display: inline-block;
    padding: 5px 14px;
    border-radius: 50px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    background: #dcfce7;
    color: #166534;
    margin-top: 10px;
    border: 1px solid #bbf7d0;
  }

  /* Details Grid */
  .inv-details {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 2rem;
    padding: 2.5rem;
  }

  .detail-box h4 {
    margin: 0 0 12px;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    border-bottom: 1px solid var(--border-color);
    padding-bottom: 5px;
  }

  .detail-box p { margin: 0; font-size: 0.95rem; line-height: 1.6; }

  /* Buttons Style */
  .inv-actions {
    display: flex;
    gap: 12px;
    align-items: center;
  }

  .inv-btn {
    padding: 0.75rem 1.6rem;
    border-radius: 50px; /* Pill Shape */
    font-size: 0.85rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    border: none;
    display: inline-flex;
    align-items: center;
    box-shadow: var(--shadow-sm);
  }

  .inv-btn svg { width: 18px; height: 18px; margin-right: 8px; }

  /* Primary Button (Print) */
  .inv-btn--primary {
    background: var(--primary-gradient);
    color: #fff;
  }

  .inv-btn--primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.4);
    filter: brightness(1.1);
  }

  /* Outline Button (Export) */
  .inv-btn--outline {
    background: #ffffff;
    border: 1px solid var(--border-color);
    color: #475569;
  }

  .inv-btn--outline:hover {
    background: #f8fafc;
    border-color: #cbd5e1;
    color: #1e293b;
    transform: translateY(-2px);
  }

  .back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    color: var(--text-muted);
    font-size: 0.9rem;
    font-weight: 500;
    transition: color 0.2s;
  }
  .back-link:hover { color: var(--text-main); }

  /* Table Style */
  .inv-body { padding: 0 2.5rem 2.5rem; }
  .inv-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
  .inv-table th {
    background: var(--bg-light);
    padding: 14px 15px;
    text-align: left;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: var(--text-muted);
    font-weight: 600;
  }
  .inv-table td { padding: 18px 15px; border-bottom: 1px solid var(--border-color); font-size: 0.95rem; }

  /* Totals Section */
  .inv-footer {
    margin-top: 2.5rem;
    display: flex;
    justify-content: flex-end;
  }

  .total-table { width: 280px; }
  .total-row { display: flex; justify-content: space-between; padding: 10px 0; }
  .total-row.grand-total {
    border-top: 2px solid #0f172a;
    margin-top: 10px;
    padding-top: 18px;
    font-weight: 800;
    font-size: 1.3rem;
    color: #0f172a;
  }

  @media print {
    body { background: white; padding: 0; }
    .inv-wrap { max-width: 100%; margin: 0; padding: 0; }
    .inv-card { box-shadow: none; border: none; }
    .no-print { display: none !important; }
    .inv-header { padding: 20px 0; border-top: 5px solid #0f172a; }
    .inv-details, .inv-body { padding: 20px 0; }
  }
</style>
@endpush

@section('content')
@php
  $settings = \App\Models\SystemSetting::current();
  $isPaid = str_contains(strtolower($invoice->number), 'paid');
@endphp

<div class="inv-wrap">
  <!-- Top Bar: Back & Actions -->
  <div class="no-print" style="margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center;">
      <a href="{{ url()->previous() }}" class="back-link">
          <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
          {{ __('Back') }}
      </a>
      <div class="inv-actions">
        <button type="button" class="inv-btn inv-btn--primary" onclick="window.print()">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
            {{ __('Print Invoice') }}
        </button>
        <a href="{{ route('site.invoice.export', ['token' => $invoice->token]) }}" class="inv-btn inv-btn--outline">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            {{ __('Export Data') }}
        </a>
      </div>
  </div>

  <div class="inv-card">
    <!-- Invoice Header -->
    <div class="inv-header">
      <div class="company-info">
        <h1>{{ $settings->company_name ?? config('app.name') }}</h1>
        @if ($settings->address_line)
          <p>{!! nl2br(e($settings->address_line)) !!}</p>
        @endif
      </div>
      <div class="invoice-label">
        <h2>{{ __('Invoice') }}</h2>
        <p>#{{ $invoice->number }}</p>
        <span class="status-badge">{{ $invoice->status ?? 'Official' }}</span>
      </div>
    </div>

    <!-- Bill Details -->
    <div class="inv-details">
      <div class="detail-box">
        <h4>{{ __('Billed To') }}</h4>
        <p>
          <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong><br>
          {{ $booking->email }}<br>
          {{ $booking->phone }}
        </p>
      </div>
      <div class="detail-box">
        <h4>{{ __('Invoice Details') }}</h4>
        <p>
          <strong>{{ __('Issue Date') }}:</strong> {{ $invoice->issued_at?->format('d M, Y') }}<br>
          <strong>{{ __('Booking Ref') }}:</strong> {{ $booking->public_reference }}<br>
          <strong>{{ __('Due Date') }}:</strong> {{ $invoice->issued_at?->addDays(7)->format('d M, Y') }}
        </p>
      </div>
    </div>

    <!-- Table Body -->
    <div class="inv-body">
      <table class="inv-table">
        <thead>
          <tr>
            <th>{{ __('Description') }}</th>
            <th style="text-align:center">{{ __('Qty') }}</th>
            <th style="text-align:right">{{ __('Unit Price') }}</th>
            <th style="text-align:right">{{ __('Amount') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($invoice->line_items ?? [] as $line)
            <tr>
              <td style="font-weight:500; color:#0f172a">{{ $line['description'] ?? '—' }}</td>
              <td style="text-align:center">{{ $line['quantity'] ?? 1 }}</td>
              <td style="text-align:right">{{ number_format((float) ($line['unit_price'] ?? 0), 0) }}</td>
              <td style="text-align:right; font-weight:600; color:#0f172a">{{ number_format((float) ($line['total'] ?? 0), 0) }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>

      <!-- Grand Totals -->
      <div class="inv-footer">
        <div class="total-table">
          <div class="total-row">
            <span style="color:var(--text-muted)">{{ __('Subtotal') }}</span>
            <span style="font-weight: 500;">{{ number_format((float) $invoice->total_amount, 0) }}</span>
          </div>
          <div class="total-row grand-total">
            <span>{{ __('Total Amount') }}</span>
            <span>{{ $invoice->currency }} {{ number_format((float) $invoice->total_amount, 0) }}</span>
          </div>
        </div>
      </div>

      <!-- Footer Note -->
      <div style="margin-top: 4rem; padding-top: 1.5rem; border-top: 1px dashed var(--border-color);">
          <p style="font-size: 0.85rem; color: var(--text-muted); text-align: center; line-height: 1.6;">
            {{ __('Thank you for choosing') }} <strong>{{ $settings->company_name ?? config('app.name') }}</strong>. <br>
            {{ __('Please pay the total amount within 7 days from the issued date.') }}
          </p>
      </div>
    </div>
  </div>
</div>
@endsection
