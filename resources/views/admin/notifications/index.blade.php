@extends('layouts.admin')

@section('title', __('Notifications'))

@section('content')
    <div style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:1rem;">
        <div>
            <h1 class="text-30" style="margin:0;">{{ __('Dashboard notifications') }}</h1>
            <p class="text-14 mt-10" style="opacity:.8;margin:0;">{{ __('Stay-end alerts, extension requests, and actions.') }}</p>
        </div>
        @if ($unreadCount > 0)
            <span class="text-14" style="background:#fef3c7;color:#92400e;padding:.35rem .75rem;border-radius:999px;font-weight:600;">{{ __('Unread: :n', ['n' => $unreadCount]) }}</span>
        @endif
    </div>

    @if (session('status'))
        <p class="text-15 mt-20" style="color:#0a6b0a;">{{ session('status') }}</p>
    @endif

    <table class="admin-table mt-25">
        <thead>
            <tr>
                <th>{{ __('When') }}</th>
                <th>{{ __('Kind') }}</th>
                <th>{{ __('Title') }}</th>
                <th>{{ __('Room / booking') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($notifications as $n)
                <tr style="{{ $n->read_at ? '' : 'background:#fffbeb;' }}">
                    <td>{{ $n->created_at?->format('Y-m-d H:i') }}</td>
                    <td><code class="text-13">{{ $n->kind }}</code></td>
                    <td>
                        <div class="fw-600">{{ $n->title }}</div>
                        @if ($n->body)
                            <div class="text-13 mt-5" style="opacity:.85;max-width:420px;">{{ \Illuminate\Support\Str::limit($n->body, 180) }}</div>
                        @endif
                    </td>
                    <td>
                        @if ($n->room)
                            <div>{{ __('Room #') }} {{ $n->room->room_number ?: '—' }} · {{ __('Floor') }} {{ $n->room->floor_number }}</div>
                            <div class="text-13" style="opacity:.75;">{{ $n->room->branch?->name }}</div>
                        @endif
                        @if ($n->booking)
                            <div class="text-13 mt-5"><a href="{{ route('admin.bookings.show', $n->booking) }}">{{ $n->booking->public_reference }}</a></div>
                        @endif
                    </td>
                    <td class="admin-actions">
                        @if (! $n->read_at)
                            <form method="POST" action="{{ route('admin.notifications.read', $n) }}" style="display:inline;">
                                @csrf
                                <button type="submit" style="background:#f1f5f9;border:1px solid #cbd5e1;border-radius:8px;padding:.35rem .65rem;cursor:pointer;">{{ __('Mark read') }}</button>
                            </form>
                        @endif
                        @if ($n->kind === 'stay_ended_room_available' && $n->booking && ! $n->resolved_at)
                            <form method="POST" action="{{ route('admin.notifications.signout', $n) }}" style="display:inline;margin-left:.35rem;">
                                @csrf
                                <button type="submit" style="background:#ecfdf5;border:1px solid #6ee7b7;border-radius:8px;padding:.35rem .65rem;cursor:pointer;">{{ __('Notify guest: signed out') }}</button>
                            </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">{{ __('No notifications yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-20">{{ $notifications->links() }}</div>
@endsection
