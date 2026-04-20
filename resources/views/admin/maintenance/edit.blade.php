@extends('layouts.admin')

@section('title', __('Edit maintenance'))

@section('content')
    <h1 class="text-30">{{ __('Edit maintenance') }}</h1>
    <p class="text-14 mt-10">{{ __('Room') }}: <strong>{{ $maintenance->room?->name }}</strong> · {{ $maintenance->branch?->name }}</p>

    <form method="POST" action="{{ route('admin.maintenance.update', $maintenance) }}" class="mt-30">
        @csrf
        @method('PUT')
        <div class="form-row">
            <label for="kind">{{ __('Kind') }} *</label>
            <select name="kind" id="kind" required>
                @foreach ($kinds as $k)
                    <option value="{{ $k->value }}" @selected(old('kind', $maintenance->kind->value) === $k->value)>{{ $k->label() }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-row">
            <label for="description">{{ __('Description') }}</label>
            <textarea name="description" id="description" rows="3">{{ old('description', $maintenance->description) }}</textarea>
        </div>
        <div class="form-row">
            <label for="expenses">{{ __('Expenses') }} *</label>
            <input type="number" step="0.01" min="0" name="expenses" id="expenses" value="{{ old('expenses', $maintenance->expenses) }}" required>
        </div>
        <div class="form-row">
            <label for="started_at">{{ __('Started at') }}</label>
            <input type="datetime-local" name="started_at" id="started_at" value="{{ old('started_at', $maintenance->started_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="form-row">
            <label for="due_at">{{ __('Due at') }}</label>
            <input type="datetime-local" name="due_at" id="due_at" value="{{ old('due_at', $maintenance->due_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="form-row">
            <label for="completed_at">{{ __('Completed at') }}</label>
            <input type="datetime-local" name="completed_at" id="completed_at" value="{{ old('completed_at', $maintenance->completed_at?->format('Y-m-d\TH:i')) }}">
        </div>
        <div class="form-row">
            <label for="status">{{ __('Status') }} *</label>
            <select name="status" id="status" required>
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}" @selected(old('status', $maintenance->status->value) === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1.2rem;border-radius:8px;cursor:pointer;">{{ __('Update') }}</button>
    </form>
@endsection
