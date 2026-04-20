@extends('layouts.admin')

@section('title', __('Newsletter emails'))

@section('content')
    <h1 class="text-24" style="margin:0 0 .25rem;">{{ __('Newsletter emails') }}</h1>
    <p class="text-13" style="opacity:.85;max-width:48rem;line-height:1.5;margin:0 0 1rem;">
        {{ __('Email addresses collected from the home page “hotel news & offers” form.') }}
    </p>

    @if (session('status'))
        <p class="text-14 mb-15" style="color:#0a0;">{{ session('status') }}</p>
    @endif

    <table class="admin-table">
        <thead>
            <tr>
                <th>{{ __('When') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($subscriptions as $row)
                <tr>
                    <td>{{ $row->created_at?->format('Y-m-d H:i') }}</td>
                    <td><a href="mailto:{{ $row->email }}">{{ $row->email }}</a></td>
                    <td>
                        <form method="POST" action="{{ route('admin.emails.destroy', $row) }}" onsubmit="return confirm(@json(__('Remove this email from the list?')));" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dash-btn" style="font-size:.8rem;color:#b91c1c;border-color:#fecaca;background:#fff5f5;">{{ __('Delete') }}</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3">{{ __('No sign-ups yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-20">{{ $subscriptions->links() }}</div>
@endsection
