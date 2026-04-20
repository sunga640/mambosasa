@extends('layouts.member')

@section('title', __('Search'))

@section('content')
    <h1 class="text-30">{{ __('Search bookings') }}</h1>
    <form method="GET" action="{{ route('member.search') }}" class="form-row mt-20" style="max-width:520px;">
        <label for="mq">{{ __('Query') }}</label>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <input type="search" name="q" id="mq" value="{{ $q }}" placeholder="{{ __('Reference, email, name…') }}" style="flex:1;min-width:200px;">
            <button type="submit" class="dash-btn dash-btn--primary">{{ __('Search') }}</button>
        </div>
    </form>

    @if (strlen($q) < 2)
        <p class="text-15 mt-30" style="opacity:.8;">{{ __('Enter at least 2 characters.') }}</p>
    @elseif ($bookings->isEmpty())
        <p class="text-15 mt-30">{{ __('No bookings found.') }}</p>
    @else
        <ul class="text-15 mt-25" style="line-height:1.9;">
            @foreach ($bookings as $b)
                <li>
                    <a href="{{ route('bookings.show', $b) }}">{{ $b->public_reference }}</a>
                    — {{ $b->status->value }} · {{ $b->room?->name }}
                </li>
            @endforeach
        </ul>
    @endif
@endsection
