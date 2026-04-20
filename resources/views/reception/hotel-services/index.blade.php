@extends('layouts.reception')

@section('title', __('Hotel services'))

@section('content')
    <h1 class="text-30" style="margin:0 0 .25rem;">{{ __('Hotel services catalog') }}</h1>
    <p class="text-15" style="opacity:.85;margin:0 0 1.25rem;">{{ __('Guests can request these add-ons after booking (laundry, meals, transport, meeting rooms, etc.).') }}</p>

    @if (session('status'))
        <p class="text-15 mb-20" style="color:#0a0;">{{ session('status') }}</p>
    @endif

    <p><a href="{{ route('reception.hotel-services.create') }}" class="dash-btn dash-btn--primary" style="text-decoration:none;display:inline-flex;">{{ __('Add service') }}</a></p>

    <table class="admin-table mt-20">
        <thead>
            <tr>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Category') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Active') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse ($services as $row)
                <tr>
                    <td><strong>{{ $row->name }}</strong></td>
                    <td>{{ $row->category }}</td>
                    <td>{{ $row->branch?->name ?? __('All') }}</td>
                    <td>{{ number_format((float) $row->price, 0) }} {{ __('TZS') }}</td>
                    <td>{{ $row->is_active ? __('Yes') : __('No') }}</td>
                    <td class="admin-actions">
                        <a href="{{ route('reception.hotel-services.edit', $row) }}" class="dash-btn dash-btn--ghost" style="text-decoration:none;">{{ __('Edit') }}</a>
                        <form method="POST" action="{{ route('reception.hotel-services.destroy', $row) }}" style="display:inline;" onsubmit="return confirm(@json(__('Delete this service?')));">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dash-btn dash-btn--ghost">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6">{{ __('No services yet.') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="mt-20">{{ $services->links() }}</div>
@endsection
