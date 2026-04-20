@extends('layouts.admin')

@section('title', __('Guests'))

@section('content')
    <h1 class="text-30">{{ __('Customers') }}</h1>
    <p class="text-14 mt-10" style="opacity:.8;">{{ __('Created or updated automatically when guests book.') }}</p>

    <form method="GET" action="{{ route('admin.customers.index') }}" class="form-row mt-20" style="display:flex;gap:1rem;flex-wrap:wrap;align-items:flex-end;">
        <div>
            <label for="q">{{ __('Search') }}</label>
            <input type="text" name="q" id="q" value="{{ $q }}" placeholder="{{ __('Email, phone, name…') }}">
        </div>
        <button type="submit" class="button -md -accent-1 bg-accent-1 text-white" style="border:none;padding:.5rem 1rem;border-radius:8px;cursor:pointer;">{{ __('Search') }}</button>
    </form>

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Active') }}</th>
                <th>{{ __('Last booking') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($customers as $c)
                <tr>
                    <td>{{ $c->first_name }} {{ $c->last_name }}</td>
                    <td>{{ $c->email }}</td>
                    <td>{{ $c->phone ?? '—' }}</td>
                    <td>{{ $c->is_active ? __('Yes') : __('No') }}</td>
                    <td>{{ $c->last_booking_at?->format('Y-m-d H:i') ?? '—' }}</td>
                    <td class="admin-actions">
                        <form method="POST" action="{{ route('admin.customers.toggle', $c) }}" style="display:inline;">
                            @csrf
                            <button type="submit" style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:6px;padding:.2rem .5rem;cursor:pointer;">
                                {{ $c->is_active ? __('Deactivate') : __('Activate') }}
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.customers.destroy', $c) }}" style="display:inline;" data-swal-delete data-swal-title="{{ __('Delete customer?') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" style="background:none;border:none;color:#c62828;cursor:pointer;padding:0;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-20">{{ $customers->links() }}</div>
@endsection
