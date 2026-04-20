@extends('layouts.member')

@section('title', __('Room service'))

@section('header')
    <h1 class="text-30" style="margin:0;">{{ __('Restaurant & room service') }}</h1>
@endsection

@section('content')
    <p class="text-15" style="opacity:.85;">{{ __('Order food to your room during your stay. ETA is estimated from menu prep times.') }}</p>

    @if (session('status'))
        <p class="text-15 mt-15" style="color:#0a6b0a;">{{ session('status') }}</p>
    @endif

    @if ($stayBookings->isEmpty())
        <p class="text-15 mt-25">{{ __('Room service is available when you have an active confirmed stay.') }}
            <a href="{{ route('site.booking') }}">{{ __('Book a room') }}</a>
        </p>
    @else
        <form method="POST" action="{{ route('member.room-service.store') }}" class="mt-25">
            @csrf
            <div class="form-row">
                <label for="room_id">{{ __('Deliver to room') }} *</label>
                <select name="room_id" id="room_id" required>
                    @foreach ($stayBookings as $b)
                        <option value="{{ $b->room_id }}" @selected(old('room_id') == $b->room_id)>
                            {{ $b->room?->name }} ({{ $b->room?->branch?->name }}) — {{ $b->public_reference }}
                        </option>
                    @endforeach
                </select>
                @error('room_id')<p class="text-13 text-accent-1 mt-5">{{ $message }}</p>@enderror
            </div>

            <h2 class="text-20 mt-30 mb-10">{{ __('Menu') }}</h2>
            @if ($menu->isEmpty())
                <p class="text-14">{{ __('No menu items yet — ask reception to add dishes in the database.') }}</p>
            @else
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>{{ __('Item') }}</th>
                            <th>{{ __('Prep (min)') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Qty') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($menu as $i => $item)
                            <tr>
                                <td>
                                    <strong>{{ $item->name }}</strong>
                                    @if ($item->description)
                                        <div class="text-13" style="opacity:.8;">{{ $item->description }}</div>
                                    @endif
                                </td>
                                <td>{{ $item->preparation_minutes }}</td>
                                <td>{{ number_format((float) $item->price, 0) }}</td>
                                <td style="max-width:100px;">
                                    <input type="hidden" name="items[{{ $i }}][menu_item_id]" value="{{ $item->id }}">
                                    <input type="number" name="items[{{ $i }}][quantity]" value="{{ old('items.'.$i.'.quantity', 0) }}" min="0" max="20" style="width:100%;">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

            <div class="form-row mt-25">
                <label for="notes">{{ __('Notes (allergies, time)') }}</label>
                <textarea name="notes" id="notes" rows="2">{{ old('notes') }}</textarea>
            </div>

            @error('items')<p class="text-13 text-accent-1">{{ $message }}</p>@enderror

            <button type="submit" class="button -md -accent-1 bg-accent-1 text-white mt-20" style="border:none;cursor:pointer;padding:.55rem 1.2rem;border-radius:8px;">{{ __('Place order') }}</button>
        </form>
    @endif

    @if ($recentOrders->isNotEmpty())
        <h2 class="text-22 mt-40 mb-15">{{ __('Your recent orders') }}</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>{{ __('When') }}</th>
                    <th>{{ __('Room') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('ETA') }}</th>
                    <th>{{ __('Total') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($recentOrders as $o)
                    <tr>
                        <td>{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                        <td>{{ $o->room?->name }}</td>
                        <td>{{ $o->statusEnum()->label() }}</td>
                        <td>{{ $o->estimated_ready_at?->format('H:i') ?? '—' }}</td>
                        <td>{{ number_format((float) $o->total_amount, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
