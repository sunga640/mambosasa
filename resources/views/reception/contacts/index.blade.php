@extends('layouts.reception')

@section('title', __('Contact messages'))

@section('content')
    <h1 class="text-30">{{ __('Contact messages') }}</h1>
    <p class="text-15 mt-10" style="opacity:.85;">{{ __('Public contact form submissions for your branch scope.') }}</p>
    <form method="GET" action="{{ route('reception.contacts.index') }}" class="form-row mt-20" style="display:flex;align-items:flex-end;gap:1rem;flex-wrap:wrap;">
        <div style="flex:1;min-width:240px;max-width:420px;">
            <label for="q">{{ __('Advanced search') }}</label>
            <input type="text" name="q" id="q" value="{{ $q ?? '' }}" placeholder="{{ __('Name, email, phone, or message text') }}">
        </div>
        <div>
            <button type="submit" class="dash-btn dash-btn--primary">{{ __('Filter') }}</button>
        </div>
    </form>

    <table class="admin-table mt-25">
        <thead>
            <tr>
                <th>{{ __('When') }}</th>
                <th>{{ __('Branch') }}</th>
                <th>{{ __('Name') }}</th>
                <th>{{ __('Email') }}</th>
                <th>{{ __('Phone') }}</th>
                <th>{{ __('Message') }}</th>
                <th>{{ __('Action') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($messages as $msg)
                @php
                    $phoneDigits = preg_replace('/\D+/', '', (string) ($msg->phone ?? ''));
                    $replyText = rawurlencode(__('Hello :name, regarding your message: :msg', ['name' => trim($msg->first_name.' '.$msg->last_name), 'msg' => mb_strimwidth((string) $msg->body, 0, 120, '...')]));
                @endphp
                <tr>
                    <td>{{ $msg->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $msg->branch?->name ?? __('Unassigned') }}</td>
                    <td>{{ $msg->first_name }} {{ $msg->last_name }}</td>
                    <td><a href="mailto:{{ $msg->email }}">{{ $msg->email }}</a></td>
                    <td>{{ $msg->phone ?: '-' }}</td>
                    <td style="max-width:420px;white-space:pre-wrap;">{{ $msg->body }}</td>
                    <td style="min-width:280px;">
                        <div style="display:flex;align-items:flex-start;gap:.75rem;flex-wrap:wrap;">
                            <details style="flex:1;min-width:200px;">
                                <summary style="cursor:pointer;">{{ __('Reply') }}</summary>
                                <form method="POST" action="{{ route('reception.contacts.reply', $msg) }}" class="mt-10">
                                    @csrf
                                    <input type="text" name="subject" value="{{ __('Re: Your contact message') }}" required>
                                    <textarea name="body" rows="3" required>{{ __('Hello :name,', ['name' => trim($msg->first_name.' '.$msg->last_name)]) }}</textarea>
                                    <button type="submit" class="dash-btn dash-btn--primary mt-8">{{ __('Send email') }}</button>
                                </form>
                                <div class="mt-8" style="display:flex;gap:.45rem;flex-wrap:wrap;">
                                    @if ($phoneDigits !== '')
                                        <a class="dash-btn dash-btn--ghost" href="https://wa.me/{{ $phoneDigits }}?text={{ $replyText }}" target="_blank">{{ __('WhatsApp') }}</a>
                                        <a class="dash-btn dash-btn--ghost" href="sms:{{ $phoneDigits }}?body={{ $replyText }}">{{ __('SMS') }}</a>
                                    @endif
                                </div>
                            </details>
                            <form method="POST" action="{{ route('reception.contacts.destroy', $msg) }}" onsubmit="return confirm(@json(__('Delete this message?')));">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dash-btn" style="font-size:.8rem;color:#b91c1c;border-color:#fecaca;background:#fff5f5;">{{ __('Delete') }}</button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">{{ __('No messages yet.') }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
    <div class="mt-20">{{ $messages->links() }}</div>
@endsection
