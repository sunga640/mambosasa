@extends('layouts.admin')

@section('title', __('Maintenance'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <h1 class="text-30">{{ __('Room maintenance') }}</h1>
        <a href="{{ route('admin.maintenance.create', ['branch_id' => $filterBranchId]) }}" class="button -md -accent-1 bg-accent-1 text-white" style="text-decoration:none;padding:.5rem 1rem;border-radius:8px;">{{ __('Log maintenance') }}</a>
    </div>

    <form method="GET" class="form-row mt-20" style="display:flex;gap:1rem;flex-wrap:wrap;">
        <div>
            <label for="branch_id">{{ __('Branch') }}</label>
            <select name="branch_id" id="branch_id" onchange="this.form.submit()">
                <option value="">{{ __('All') }}</option>
                @foreach ($branches as $b)
                    <option value="{{ $b->id }}" @selected($filterBranchId === $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label for="status">{{ __('Status') }}</label>
            <select name="status" id="status" onchange="this.form.submit()">
                <option value="">{{ __('All') }}</option>
                @foreach ($statuses as $st)
                    <option value="{{ $st->value }}" @selected($filterStatus === $st->value)>{{ $st->label() }}</option>
                @endforeach
            </select>
        </div>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Room') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Kind') }}</th>
                <th>{{ __('Status') }}</th>
                <th>{{ __('Expenses') }}</th>
                <th>{{ __('Due') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($records as $m)
                <tr>
                    <td>{{ $m->room?->name ?? '—' }} @if($m->room?->room_number)<span class="text-13" style="opacity:.7;">#{{ $m->room->room_number }}</span>@endif</td>
                    <td>{{ $m->branch?->name ?? '—' }}</td>
                    <td>{{ $m->kind->label() }}</td>
                    <td>{{ $m->status->label() }}</td>
                    <td>{{ number_format((float) $m->expenses, 0) }}</td>
                    <td>{{ $m->due_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('admin.maintenance.edit', $m) }}">{{ __('Edit') }}</a>
                        <form action="{{ route('admin.maintenance.destroy', $m) }}" method="POST" data-swal-delete data-swal-title="{{ __('Delete record?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $records->links() }}</div>
@endsection
