@extends('layouts.member')

@section('title', __('My notification'))
@section('breadcrumb', __('My notification'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('My notification') }}</h1>
@endsection

@section('content')
    @if ($alerts->isEmpty())
        <p class="text-15">{{ __('You have no notifications right now.') }}</p>
    @else
        <div style="display:flex;flex-direction:column;gap:1rem;">
            @foreach ($alerts as $alert)
                <article style="border:1px solid #f1d2a9;background:#fff7ed;border-radius:10px;padding:1rem;">
                    <div class="text-15 fw-600">{{ __('Payment reminder for :ref', ['ref' => $alert->public_reference]) }}</div>
                    <div class="text-13 mt-5">{{ $alert->room?->name ?? '—' }}</div>
                    <div class="text-13 mt-5">
                        {{ __('Deadline') }}:
                        {{ $alert->payment_deadline_at?->format('Y-m-d H:i') ?? '—' }}
                    </div>
                    <div class="mt-10">
                        <a href="{{ route('bookings.show', $alert) }}">{{ __('Open booking') }}</a>
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
