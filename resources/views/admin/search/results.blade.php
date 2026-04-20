@extends('layouts.admin')

@section('title', __('Search'))

@section('content')
    <h1 class="text-30">{{ __('Search') }}</h1>
    <form method="GET" action="{{ route('admin.search') }}" class="form-row mt-20" style="max-width:520px;">
        <label for="q">{{ __('Query') }}</label>
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <input type="search" name="q" id="q" value="{{ $q }}" placeholder="{{ __('Bookings, customers, rooms…') }}" autofocus style="flex:1;min-width:200px;">
            <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;">{{ __('Search') }}</button>
        </div>
    </form>

    @if (strlen($q) < 2)
        <p class="text-15 mt-30" style="opacity:.8;">{{ __('Enter at least 2 characters.') }}</p>
    @else
        <h2 class="text-20 mt-30">{{ __('Bookings') }}</h2>
        @if ($bookings->isEmpty())
            <p class="text-14" style="opacity:.75;">{{ __('No matches.') }}</p>
        @else
            <ul class="text-15 mt-10" style="line-height:1.8;">
                @foreach ($bookings as $b)
                    <li><a href="{{ route('admin.bookings.show', $b) }}">{{ $b->public_reference }}</a> — {{ $b->email }} · {{ $b->room?->name }}</li>
                @endforeach
            </ul>
        @endif

        <h2 class="text-20 mt-30">{{ __('Customers') }}</h2>
        @if ($customers->isEmpty())
            <p class="text-14" style="opacity:.75;">{{ __('No matches.') }}</p>
        @else
            <ul class="text-15 mt-10" style="line-height:1.8;">
                @foreach ($customers as $c)
                    <li>{{ $c->first_name }} {{ $c->last_name }} — <a href="mailto:{{ $c->email }}">{{ $c->email }}</a></li>
                @endforeach
            </ul>
        @endif

        <h2 class="text-20 mt-30">{{ __('Rooms') }}</h2>
        @if ($rooms->isEmpty())
            <p class="text-14" style="opacity:.75;">{{ __('No matches.') }}</p>
        @else
            <ul class="text-15 mt-10" style="line-height:1.8;">
                @foreach ($rooms as $r)
                    <li><a href="{{ route('admin.rooms.edit', $r) }}">{{ $r->name }}</a> @if($r->room_number)(#{{ $r->room_number }}) @endif— {{ $r->branch?->name }}</li>
                @endforeach
            </ul>
        @endif
    @endif
@endsection
