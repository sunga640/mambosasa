@extends('layouts.admin')

@section('title', __('Payment methods'))

@section('content')
    <h1 class="text-30">{{ __('Payment methods') }}</h1>
    <p class="text-14 mt-10" style="opacity:.8;max-width:50rem;">
        {{ __('Configure payment methods used on the booking page. Toggle on/off and set account details. Gateway secret/public keys stay in .env and are not shown here.') }}
    </p>

    @if (session('status'))
        <p class="mt-15" style="padding:.6rem .8rem;border-radius:8px;background:#ecfdf5;border:1px solid #86efac;color:#166534;">
            {{ session('status') }}
        </p>
    @endif

    <div class="mt-25" style="padding:1rem;border:1px solid #e5e7eb;border-radius:12px;background:#fff;">
        <h2 class="text-18 mb-10">{{ __('New payment method') }}</h2>
        <form method="POST" action="{{ route('admin.payment-methods.store') }}">
            @csrf
            <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:.75rem;">
                <div><label>{{ __('Method name') }}</label><input type="text" name="name" placeholder="{{ __('Method name') }}" required></div>
                <div><label>{{ __('Code') }}</label><input type="text" name="code" placeholder="{{ __('Code e.g. mpesa') }}"></div>
                <div><label>{{ __('Type') }}</label><select name="method_type">
                    <option value="offline">{{ __('Offline') }}</option>
                    <option value="online">{{ __('Online') }}</option>
                </select></div>
                <div><label>{{ __('Visibility') }}</label><select name="visibility">
                    <option value="public">{{ __('Public') }}</option>
                    <option value="internal">{{ __('Internal') }}</option>
                </select></div>
                <div><label>{{ __('Sort order') }}</label><input type="number" name="sort_order" min="0" value="0" placeholder="{{ __('Sort') }}"></div>
            </div>
            <div class="mt-10" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:.75rem;">
                <div><label>{{ __('Account number') }}</label><input type="text" name="account_number" placeholder="{{ __('Account number') }}"></div>
                <div><label>{{ __('Account holder') }}</label><input type="text" name="account_holder" placeholder="{{ __('Account holder') }}"></div>
            </div>
            <label class="mt-10" style="display:block;">{{ __('Instructions') }}</label>
            <textarea name="instructions" rows="2" placeholder="{{ __('Guest payment instruction shown on booking page...') }}"></textarea>
            <div class="mt-10" style="display:flex;gap:1rem;flex-wrap:wrap;">
                <label><input type="checkbox" name="show_on_booking_page" value="1" checked> {{ __('Show on booking page') }}</label>
                <label><input type="checkbox" name="is_active" value="1" checked> {{ __('Enabled') }}</label>
            </div>
            <button class="dash-btn dash-btn--primary mt-15" type="submit">{{ __('Add method') }}</button>
        </form>
    </div>

    <div class="mt-25" style="overflow:auto;">
        <table class="admin-table">
            <thead>
            <tr>
                <th>{{ __('Method') }}</th>
                <th>{{ __('Code') }}</th>
                <th>{{ __('Type') }}</th>
                <th>{{ __('Enabled') }}</th>
                <th>{{ __('Account Number') }}</th>
                <th>{{ __('Account Holder') }}</th>
                <th>{{ __('Sort') }}</th>
                <th>{{ __('Actions') }}</th>
            </tr>
            </thead>
            <tbody>
            @forelse($methods as $m)
                <tr>
                    <td>{{ $m->name }}</td>
                    <td>{{ $m->code }}</td>
                    <td>{{ ucfirst($m->method_type) }}</td>
                    <td>
                        <span style="padding:.2rem .5rem;border-radius:999px;background:{{ $m->is_active ? '#dcfce7' : '#f3f4f6' }};font-size:.82rem;">
                            {{ $m->is_active ? __('Enabled') : __('Disabled') }}
                        </span>
                    </td>
                    <td>{{ $m->account_number ?: '—' }}</td>
                    <td>{{ $m->account_holder ?: '—' }}</td>
                    <td>{{ $m->sort_order }}</td>
                    <td>
                        <details>
                            <summary style="cursor:pointer;">{{ __('Edit') }}</summary>
                            <form method="POST" action="{{ route('admin.payment-methods.update', $m) }}" style="margin-top:.6rem;min-width:320px;">
                                @csrf
                                @method('PUT')
                                <div style="display:grid;grid-template-columns:1fr 1fr;gap:.45rem;">
                                    <input type="text" name="name" value="{{ $m->name }}" required>
                                    <input type="text" name="code" value="{{ $m->code }}">
                                    <select name="method_type">
                                        <option value="offline" @selected($m->method_type === 'offline')>{{ __('Offline') }}</option>
                                        <option value="online" @selected($m->method_type === 'online')>{{ __('Online') }}</option>
                                    </select>
                                    <input type="number" name="sort_order" min="0" value="{{ $m->sort_order }}">
                                    <input type="text" name="account_number" value="{{ $m->account_number }}" placeholder="{{ __('Account number') }}">
                                    <input type="text" name="account_holder" value="{{ $m->account_holder }}" placeholder="{{ __('Account holder') }}">
                                    <select name="visibility">
                                        <option value="public" @selected($m->visibility === 'public')>{{ __('Public') }}</option>
                                        <option value="internal" @selected($m->visibility === 'internal')>{{ __('Internal') }}</option>
                                    </select>
                                    <div>
                                        <label><input type="checkbox" name="is_active" value="1" @checked($m->is_active)> {{ __('Enabled') }}</label><br>
                                        <label><input type="checkbox" name="show_on_booking_page" value="1" @checked($m->show_on_booking_page)> {{ __('Public') }}</label>
                                    </div>
                                </div>
                                <textarea name="instructions" rows="2" class="mt-8" placeholder="{{ __('Payment instructions') }}">{{ $m->instructions }}</textarea>
                                <div class="mt-8" style="display:flex;gap:.45rem;flex-wrap:wrap;">
                                    <button class="dash-btn dash-btn--primary" type="submit">{{ __('Save') }}</button>
                                </div>
                            </form>
                            <div class="mt-8" style="display:flex;gap:.45rem;flex-wrap:wrap;">
                                <form method="POST" action="{{ route('admin.payment-methods.toggle', $m) }}">
                                    @csrf
                                    <button class="dash-btn dash-btn--ghost" type="submit">{{ $m->is_active ? __('Disable') : __('Enable') }}</button>
                                </form>
                                <form method="POST" action="{{ route('admin.payment-methods.destroy', $m) }}" onsubmit="return confirm(@json(__('Delete this payment method?')));">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dash-btn dash-btn--ghost" type="submit">{{ __('Delete') }}</button>
                                </form>
                            </div>
                        </details>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8">{{ __('No methods yet.') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
