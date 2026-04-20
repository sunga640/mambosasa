@extends('layouts.reception')

@section('title', __('Log maintenance'))

@section('content')
    <h1 class="text-30">{{ __('Log room maintenance') }}</h1>

    <form method="GET" action="{{ route('reception.maintenance.create') }}" class="form-row mt-20" style="display:flex;gap:1rem;align-items:flex-end;">
        <div>
            <label for="branch_id">{{ __('Filter rooms by branch') }}</label>
            <select name="branch_id" id="branch_id" onchange="this.form.submit()">
                <option value="">{{ __('All branches') }}</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" @selected($selectedBranchId === $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <form method="POST" action="{{ route('reception.maintenance.store') }}" class="mt-30">
        @csrf
        <div class="form-row">
            <label for="room_id">{{ __('Room') }} *</label>
            <select name="room_id" id="room_id" required>
                <option value="">{{ __('Select room') }}</option>
                @foreach ($rooms as $r)
                    <option value="{{ $r->id }}" @selected(old('room_id') == $r->id)>{{ $r->name }} @if($r->room_number)(#{{ $r->room_number }}) @endif— {{ $r->branch?->name }}</option>
                @endforeach
            </select>
            @error('room_id')<div class="text-accent-1 text-13 mt-5">{{ $message }}</div>@enderror
        </div>
        <div class="form-row">
            <label for="kind">{{ __('Kind') }} *</label>
            <select name="kind" id="kind" required>
                @foreach ($kinds as $k)
                    <option value="{{ $k->value }}" @selected(old('kind') === $k->value)>{{ $k->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label for="description">{{ __('Description') }}</label>
            <textarea name="description" id="description" rows="3">{{ old('description') }}</textarea>
        </div>
        <div class="form-row">
            <label for="expenses">{{ __('Expenses') }} *</label>
            <input type="number" step="0.01" min="0" name="expenses" id="expenses" value="{{ old('expenses', '0') }}" required>
        </div>
        <div class="form-row">
            <label for="started_at">{{ __('Started at') }}</label>
            <input type="datetime-local" name="started_at" id="started_at" value="{{ old('started_at') }}">
        </div>
        <div class="form-row">
            <label for="due_at">{{ __('Due at') }}</label>
            <input type="datetime-local" name="due_at" id="due_at" value="{{ old('due_at') }}">
        </div>
        <div class="form-row">
            <label for="status">{{ __('Status') }} *</label>
            <select name="status" id="status" required>
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}" @selected(old('status', 'active') === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1.2rem;border-radius:8px;cursor:pointer;">{{ __('Save') }}</button>
    </form>
@endsection
