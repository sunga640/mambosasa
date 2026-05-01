@extends('layouts.admin')

@section('title', __('Payment methods'))

@section('content')
    <style>
        .pay-grid { display:grid; gap:1rem; }
        .pay-hero { display:grid; gap:1rem; grid-template-columns:1.2fr .8fr; align-items:start; }
        .pay-card { border:1px solid rgba(213, 172, 66, 0.22); background:var(--brand-theme-surface, #2e333b); padding:1rem; color:var(--brand-theme-text, #f5efe2); }
        .pay-form-grid { display:grid; gap:.75rem; grid-template-columns:repeat(auto-fit, minmax(200px, 1fr)); }
        .pay-inline-grid { display:grid; gap:.75rem; grid-template-columns:repeat(auto-fit, minmax(240px, 1fr)); }
        .pay-method-form-grid { display:grid; gap:.9rem 1rem; grid-template-columns:repeat(2, minmax(0, 1fr)); align-items:start; }
        .pay-method-form-span { grid-column:1 / -1; }
        .pay-pill { display:inline-flex; align-items:center; gap:.4rem; padding:.28rem .65rem; font-size:.78rem; font-weight:700; letter-spacing:.04em; text-transform:uppercase; border:1px solid rgba(213, 172, 66, 0.24); }
        .pay-pill--ok { background:rgba(34, 197, 94, 0.16); color:#9ff0b8; border-color:rgba(34, 197, 94, 0.34); }
        .pay-pill--muted { background:rgba(245, 239, 226, 0.06); color:var(--brand-theme-muted, #b9bdc7); }
        .pay-status-list { display:grid; gap:.7rem; }
        .pay-status-row { display:flex; justify-content:space-between; gap:1rem; align-items:center; padding:.75rem 0; border-bottom:1px solid rgba(213, 172, 66, 0.14); }
        .pay-status-row:last-child { border-bottom:none; padding-bottom:0; }
        .pay-method-table .admin-table td { vertical-align:middle; }
        .pay-method-actions { display:flex; gap:.45rem; flex-wrap:wrap; align-items:center; }
        .pay-secret { font-family:ui-monospace, SFMono-Regular, Menlo, monospace; font-size:.82rem; }
        .pay-card code { color:#ff69b4; }
        .pay-modal {
            position: fixed;
            inset: 0;
            z-index: 30050;
            display: none;
            align-items: center;
            justify-content: center;
            padding: max(5.75rem, 1.2rem) 1.2rem 1.2rem;
            background: rgba(6, 11, 20, 0.72);
            backdrop-filter: blur(6px);
        }
        .pay-modal.is-open { display:flex; }
        .pay-modal__dialog {
            width: min(980px, 100%);
            max-height: calc(100vh - 2.4rem);
            overflow: auto;
            border: 1px solid rgba(96, 165, 250, 0.22);
            background: linear-gradient(180deg, rgba(19, 32, 46, 0.98) 0%, rgba(33, 67, 92, 0.98) 100%);
            color: #eff6ff;
            box-shadow: 0 28px 70px rgba(0, 0, 0, 0.34);
        }
        .pay-modal__head {
            display:flex;
            justify-content:space-between;
            gap:1rem;
            align-items:flex-start;
            padding:1.1rem 1.2rem .9rem;
            border-bottom:1px solid rgba(96, 165, 250, 0.14);
        }
        .pay-modal__body {
            padding:1.15rem 1.2rem 1.2rem;
            display:grid;
            gap:1rem;
        }
        .pay-modal__close {
            border:1px solid rgba(148, 163, 184, 0.34);
            background: rgba(15, 23, 42, 0.45);
            color:#e2e8f0;
            min-width: 2.35rem;
            min-height: 2.35rem;
            cursor:pointer;
            font-size:1.15rem;
            line-height:1;
        }
        .pay-modal__meta {
            display:flex;
            gap:.55rem;
            flex-wrap:wrap;
            align-items:center;
        }
        .pay-modal .form-row {
            margin-bottom: 0;
        }
        .pay-modal label {
            display:block;
            margin-bottom:.35rem;
            font-size:.8rem;
            font-weight:700;
            letter-spacing:.03em;
            text-transform:uppercase;
            color:#dbeafe;
        }
        .pay-modal input[type="text"],
        .pay-modal input[type="url"],
        .pay-modal input[type="number"],
        .pay-modal select,
        .pay-modal textarea {
            width:100%;
            max-width:none;
            min-height:42px;
            padding:.58rem .75rem;
            border:1px solid rgba(148, 163, 184, 0.22);
            background: rgba(15, 23, 42, 0.58);
            color:#f8fafc;
        }
        .pay-modal textarea {
            min-height: 112px;
            resize: vertical;
        }
        .pay-modal__footer {
            display:flex;
            justify-content:space-between;
            gap:.75rem;
            flex-wrap:wrap;
            align-items:center;
            padding-top:.2rem;
        }
        .pay-check-row {
            display:flex;
            gap:1rem;
            flex-wrap:wrap;
            align-items:center;
        }
        .pay-check-row label {
            display:inline-flex;
            align-items:center;
            gap:.55rem;
            margin-bottom:0;
            text-transform:none;
            color:#e2e8f0;
        }
        @media (max-width: 900px) {
            .pay-hero { grid-template-columns:1fr; }
            .pay-method-form-grid { grid-template-columns:1fr; }
        }
    </style>

    <div class="pay-grid">
        <div>
            <h1 class="text-30">{{ __('Payment methods') }}</h1>
            <p class="text-14 mt-10" style="opacity:.8;max-width:58rem;">
                {{ __('Configure booking payments from admin. Add provider type, account details, gateway keys, base URLs, and Pesapal IPN values here so you do not need to depend on .env for daily payment setup.') }}
            </p>
        </div>

        @if (session('status'))
            <p style="padding:.75rem .9rem;background:rgba(34, 197, 94, 0.16);border:1px solid rgba(34, 197, 94, 0.34);color:#9ff0b8;">
                {{ session('status') }}
            </p>
        @endif

        @if ($errors->has('payment_method_delete'))
            <p style="padding:.75rem .9rem;background:rgba(248, 113, 113, 0.12);border:1px solid rgba(248, 113, 113, 0.3);color:#fecaca;">
                {{ $errors->first('payment_method_delete') }}
            </p>
        @endif

        <div class="pay-hero">
            <section class="pay-card">
                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                    <div>
                        <h2 class="text-20" style="margin:0;">{{ __('Add or prepare a payment provider') }}</h2>
                        <p class="text-13 mt-5" style="opacity:.72;max-width:42rem;">
                            {{ __('Use one of the common presets below, then add provider credentials and booking instructions. Pesapal uses API keys and base URL, while M-Pesa and Tigo Pesa can also store paybill or wallet instructions here.') }}
                        </p>
                    </div>
                    <div class="pay-pill pay-pill--muted">{{ count($providerPresets) }} {{ __('presets') }}</div>
                </div>

                <form method="POST" action="{{ route('admin.payment-methods.store') }}" class="mt-20">
                    @csrf
                    <div class="pay-form-grid">
                        <div>
                            <label for="provider_preset">{{ __('Quick provider preset') }}</label>
                            <select id="provider_preset" data-provider-preset>
                                <option value="">{{ __('Select preset') }}</option>
                                @foreach ($providerPresets as $key => $preset)
                                    <option value="{{ $key }}">{{ $preset['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="name">{{ __('Method name') }}</label>
                            <input id="name" type="text" name="name" value="{{ old('name') }}" placeholder="{{ __('Method name') }}" required>
                        </div>
                        <div>
                            <label for="code">{{ __('Provider code') }}</label>
                            <input id="code" type="text" name="code" value="{{ old('code') }}" placeholder="{{ __('e.g. pesapal, mpesa, tigopesa') }}">
                        </div>
                        <div>
                            <label for="method_type">{{ __('Type') }}</label>
                            <select id="method_type" name="method_type">
                                <option value="offline" @selected(old('method_type') === 'offline')>{{ __('Offline') }}</option>
                                <option value="online" @selected(old('method_type', 'online') === 'online')>{{ __('Online') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="visibility">{{ __('Visibility') }}</label>
                            <select id="visibility" name="visibility">
                                <option value="public" @selected(old('visibility', 'public') === 'public')>{{ __('Public') }}</option>
                                <option value="internal" @selected(old('visibility') === 'internal')>{{ __('Internal') }}</option>
                            </select>
                        </div>
                        <div>
                            <label for="sort_order">{{ __('Sort order') }}</label>
                            <input id="sort_order" type="number" name="sort_order" min="0" value="{{ old('sort_order', 0) }}">
                        </div>
                    </div>

                    <div class="pay-inline-grid mt-15">
                        <div>
                            <label for="account_number">{{ __('Account number / paybill / wallet') }}</label>
                            <input id="account_number" type="text" name="account_number" value="{{ old('account_number') }}" placeholder="{{ __('Paybill, till number, or account number') }}">
                        </div>
                        <div>
                            <label for="account_holder">{{ __('Account holder / business name') }}</label>
                            <input id="account_holder" type="text" name="account_holder" value="{{ old('account_holder') }}" placeholder="{{ __('Business name or merchant label') }}">
                        </div>
                    </div>

                    <div class="pay-inline-grid mt-15">
                        <div>
                            <label for="gateway_public_key">{{ __('Gateway public key / consumer key') }}</label>
                            <input id="gateway_public_key" type="text" name="gateway_public_key" value="{{ old('gateway_public_key') }}" placeholder="{{ __('Provider public key') }}">
                        </div>
                        <div>
                            <label for="gateway_secret_key">{{ __('Gateway secret key') }}</label>
                            <input id="gateway_secret_key" type="text" name="gateway_secret_key" value="{{ old('gateway_secret_key') }}" placeholder="{{ __('Provider secret key') }}">
                        </div>
                        <div>
                            <label for="gateway_base_url">{{ __('Gateway base URL') }}</label>
                            <input id="gateway_base_url" type="url" name="gateway_base_url" value="{{ old('gateway_base_url') }}" placeholder="https://">
                        </div>
                        <div>
                            <label for="gateway_ipn_id">{{ __('IPN / callback ID') }}</label>
                            <input id="gateway_ipn_id" type="text" name="gateway_ipn_id" value="{{ old('gateway_ipn_id') }}" placeholder="{{ __('Optional callback or IPN identifier') }}">
                        </div>
                    </div>

                    <div class="mt-15">
                        <label for="instructions">{{ __('Booking instructions shown to guest') }}</label>
                        <textarea id="instructions" name="instructions" rows="3" placeholder="{{ __('Example: Complete payment then send confirmation to reception...') }}">{{ old('instructions') }}</textarea>
                    </div>

                    <div class="mt-15" style="display:flex;gap:1rem;flex-wrap:wrap;">
                        <label><input type="checkbox" name="show_on_booking_page" value="1" @checked(old('show_on_booking_page', true))> {{ __('Show on booking page') }}</label>
                        <label><input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))> {{ __('Enabled now') }}</label>
                    </div>

                    <div class="mt-15" style="display:flex;gap:.7rem;flex-wrap:wrap;">
                        <button class="dash-btn dash-btn--primary" type="submit">{{ __('Save payment method') }}</button>
                        <a href="{{ route('admin.settings.edit') }}" class="dash-btn dash-btn--ghost">{{ __('Open system settings') }}</a>
                    </div>
                </form>
            </section>

            <aside class="pay-card">
                <h2 class="text-18" style="margin:0 0 .65rem;">{{ __('Setup checklist') }}</h2>
                <div class="pay-status-list">
                    <div class="pay-status-row">
                        <div>
                            <div class="fw-600">{{ __('Pesapal') }}</div>
                            <div class="text-13" style="opacity:.7;">{{ __('Add consumer key, secret, base URL, and optional IPN id.') }}</div>
                        </div>
                        <span class="pay-pill {{ $methods->contains(fn ($method) => $method->slug === 'pesapal' && $method->gateway_public_key && $method->gateway_secret_key && $method->gateway_base_url) ? 'pay-pill--ok' : 'pay-pill--muted' }}">
                            {{ $methods->contains(fn ($method) => $method->slug === 'pesapal' && $method->gateway_public_key && $method->gateway_secret_key && $method->gateway_base_url) ? __('Ready') : __('Pending') }}
                        </span>
                    </div>
                    <div class="pay-status-row">
                        <div>
                            <div class="fw-600">{{ __('M-Pesa') }}</div>
                            <div class="text-13" style="opacity:.7;">{{ __('Keep account/paybill details, instructions, and any API keys if your integration needs them.') }}</div>
                        </div>
                        <span class="pay-pill {{ $methods->contains(fn ($method) => $method->slug === 'mpesa' && ($method->account_number || $method->gateway_public_key)) ? 'pay-pill--ok' : 'pay-pill--muted' }}">
                            {{ $methods->contains(fn ($method) => $method->slug === 'mpesa' && ($method->account_number || $method->gateway_public_key)) ? __('Ready') : __('Pending') }}
                        </span>
                    </div>
                    <div class="pay-status-row">
                        <div>
                            <div class="fw-600">{{ __('Tigo Pesa') }}</div>
                            <div class="text-13" style="opacity:.7;">{{ __('Add business wallet details and credentials once your integration is ready.') }}</div>
                        </div>
                        <span class="pay-pill {{ $methods->contains(fn ($method) => $method->slug === 'tigopesa' && ($method->account_number || $method->gateway_public_key)) ? 'pay-pill--ok' : 'pay-pill--muted' }}">
                            {{ $methods->contains(fn ($method) => $method->slug === 'tigopesa' && ($method->account_number || $method->gateway_public_key)) ? __('Ready') : __('Pending') }}
                        </span>
                    </div>
                    <div class="pay-status-row">
                        <div>
                            <div class="fw-600">{{ __('Email settings') }}</div>
                            <div class="text-13" style="opacity:.7;">{{ __('Booking emails use the guest email templates plus SMTP and from-address values saved in admin system settings. When admin SMTP host and port are filled, that runtime setup overrides daily dependence on .env values.') }}</div>
                        </div>
                        <a href="{{ route('admin.settings.edit') }}" class="dash-btn dash-btn--ghost">{{ __('Open email setup') }}</a>
                    </div>
                </div>
            </aside>
        </div>

        <section class="pay-card pay-method-table">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div>
                    <h2 class="text-20" style="margin:0;">{{ __('Configured methods') }}</h2>
                    <p class="text-13 mt-5" style="opacity:.72;">{{ __('Edit credentials, turn providers on or off, and control what appears on the guest booking page.') }}</p>
                </div>
                <div class="pay-pill pay-pill--muted">{{ $methods->count() }} {{ __('methods') }}</div>
            </div>

            <div class="mt-20" style="overflow:auto;">
                <table class="admin-table">
                    <thead>
                    <tr>
                        <th>{{ __('Method') }}</th>
                        <th>{{ __('Provider') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Booking page') }}</th>
                        <th>{{ __('Account / base URL') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($methods as $m)
                        <tr>
                            <td>
                                <div class="fw-600">{{ $m->name }}</div>
                                <div class="text-12" style="opacity:.65;">{{ __('Sort') }}: {{ $m->sort_order }}</div>
                            </td>
                            <td>
                                <div><code>{{ $m->code ?: $m->slug }}</code></div>
                                <div class="text-12" style="opacity:.65;">{{ ucfirst($m->method_type) }} · {{ ucfirst($m->visibility) }}</div>
                            </td>
                            <td>
                                <span class="pay-pill {{ $m->is_active ? 'pay-pill--ok' : 'pay-pill--muted' }}">{{ $m->is_active ? __('Enabled') : __('Disabled') }}</span>
                            </td>
                            <td>
                                <span class="pay-pill {{ $m->show_on_booking_page ? 'pay-pill--ok' : 'pay-pill--muted' }}">{{ $m->show_on_booking_page ? __('Visible') : __('Hidden') }}</span>
                            </td>
                            <td>
                                <div>{{ $m->account_number ?: '—' }}</div>
                                <div class="text-12" style="opacity:.65;">{{ $m->gateway_base_url ?: '—' }}</div>
                            </td>
                            <td>
                                <div class="pay-method-actions">
                                    <button type="button" class="dash-btn dash-btn--primary" data-pay-modal-open="pay-method-modal-{{ $m->id }}">{{ __('Edit') }}</button>
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
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6">{{ __('No methods yet.') }}</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @foreach($methods as $m)
            @php
                $configured = filled($m->account_number) || filled($m->gateway_public_key) || filled($m->gateway_base_url);
            @endphp
            <div class="pay-modal" id="pay-method-modal-{{ $m->id }}" aria-hidden="true">
                <div class="pay-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="pay-method-modal-title-{{ $m->id }}">
                    <div class="pay-modal__head">
                        <div>
                            <h2 id="pay-method-modal-title-{{ $m->id }}" class="text-22" style="margin:0;">{{ __('Edit payment method') }}</h2>
                            <p class="text-13 mt-5" style="opacity:.78;margin-bottom:0;">{{ __('Update provider settings inside a cleaner modal layout with grouped fields for public booking visibility, account details, and gateway credentials.') }}</p>
                        </div>
                        <button type="button" class="pay-modal__close" data-pay-modal-close aria-label="{{ __('Close edit modal') }}">&times;</button>
                    </div>
                    <div class="pay-modal__body">
                        <div class="pay-modal__meta">
                            <span class="pay-pill pay-pill--muted">{{ $m->name }}</span>
                            <span class="pay-pill {{ $m->is_active ? 'pay-pill--ok' : 'pay-pill--muted' }}">{{ $m->is_active ? __('Enabled') : __('Disabled') }}</span>
                            <span class="pay-pill {{ $m->show_on_booking_page ? 'pay-pill--ok' : 'pay-pill--muted' }}">{{ $m->show_on_booking_page ? __('Visible on booking page') : __('Hidden from booking page') }}</span>
                        </div>

                        <form method="POST" action="{{ route('admin.payment-methods.update', $m) }}">
                            @csrf
                            @method('PUT')

                            <div class="pay-method-form-grid">
                                <div class="form-row">
                                    <label>{{ __('Method name') }}</label>
                                    <input type="text" name="name" value="{{ $m->name }}" required>
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Provider code') }}</label>
                                    <input type="text" name="code" value="{{ $m->code }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Type') }}</label>
                                    <select name="method_type">
                                        <option value="offline" @selected($m->method_type === 'offline')>{{ __('Offline') }}</option>
                                        <option value="online" @selected($m->method_type === 'online')>{{ __('Online') }}</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Visibility') }}</label>
                                    <select name="visibility">
                                        <option value="public" @selected($m->visibility === 'public')>{{ __('Public') }}</option>
                                        <option value="internal" @selected($m->visibility === 'internal')>{{ __('Internal') }}</option>
                                    </select>
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Sort order') }}</label>
                                    <input type="number" name="sort_order" min="0" value="{{ $m->sort_order }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Account holder / business name') }}</label>
                                    <input type="text" name="account_holder" value="{{ $m->account_holder }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Account number / paybill / wallet') }}</label>
                                    <input type="text" name="account_number" value="{{ $m->account_number }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Gateway base URL') }}</label>
                                    <input type="url" name="gateway_base_url" value="{{ $m->gateway_base_url }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Gateway public key / consumer key') }}</label>
                                    <input type="text" name="gateway_public_key" value="{{ $m->gateway_public_key }}">
                                </div>
                                <div class="form-row">
                                    <label>{{ __('Gateway secret key') }}</label>
                                    <input type="text" name="gateway_secret_key" value="{{ $m->gateway_secret_key }}">
                                </div>
                                <div class="form-row pay-method-form-span">
                                    <label>{{ __('IPN / callback ID') }}</label>
                                    <input type="text" name="gateway_ipn_id" value="{{ $m->gateway_ipn_id }}">
                                </div>
                                <div class="form-row pay-method-form-span">
                                    <label>{{ __('Booking instructions shown to guest') }}</label>
                                    <textarea name="instructions" rows="4">{{ $m->instructions }}</textarea>
                                </div>
                            </div>

                            <div class="mt-15 pay-check-row">
                                <label><input type="checkbox" name="is_active" value="1" @checked($m->is_active)> {{ __('Enabled now') }}</label>
                                <label><input type="checkbox" name="show_on_booking_page" value="1" @checked($m->show_on_booking_page)> {{ __('Show on booking page') }}</label>
                            </div>

                            <div class="mt-15 text-12" style="opacity:.74;">
                                {{ __('Saved public key') }}:
                                <span class="pay-secret">{{ $m->gateway_public_key ? str($m->gateway_public_key)->limit(18, '...') : '—' }}</span>
                                · {{ __('Saved secret key') }}:
                                <span class="pay-secret">{{ $m->gateway_secret_key ? '********' : '—' }}</span>
                                · {{ __('Configured') }}:
                                <span>{{ $configured ? __('Yes') : __('No') }}</span>
                            </div>

                            <div class="mt-20 pay-modal__footer">
                                <div class="text-13" style="opacity:.72;">{{ __('This modal only changes the selected payment method. Booking emails still come from the email templates and SMTP settings saved under admin system settings.') }}</div>
                                <div style="display:flex;gap:.55rem;flex-wrap:wrap;">
                                    <button type="button" class="dash-btn dash-btn--ghost" data-pay-modal-close>{{ __('Close') }}</button>
                                    <button class="dash-btn dash-btn--primary" type="submit">{{ __('Save changes') }}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        var presetSelect = document.querySelector('[data-provider-preset]');
        var presets = @json($providerPresets);
        var fieldMap = {
            name: document.getElementById('name'),
            code: document.getElementById('code'),
            method_type: document.getElementById('method_type'),
            visibility: document.getElementById('visibility')
        };
        var bookingCheckbox = document.querySelector('input[name="show_on_booking_page"]');

        if (presetSelect) {
            presetSelect.addEventListener('change', function () {
                var preset = presets[presetSelect.value];
                if (!preset) return;

                Object.keys(fieldMap).forEach(function (key) {
                    if (!fieldMap[key]) return;
                    fieldMap[key].value = preset[key] ?? '';
                });

                if (bookingCheckbox) {
                    bookingCheckbox.checked = !!preset.show_on_booking_page;
                }
            });
        }

        var activeModal = null;
        function closeModal(modal) {
            if (!modal) return;
            modal.classList.remove('is-open');
            modal.setAttribute('aria-hidden', 'true');
            document.documentElement.classList.remove('html-overflow-hidden');
            activeModal = null;
        }

        document.querySelectorAll('[data-pay-modal-open]').forEach(function (button) {
            button.addEventListener('click', function () {
                var modalId = button.getAttribute('data-pay-modal-open');
                var modal = modalId ? document.getElementById(modalId) : null;
                if (!modal) return;
                modal.classList.add('is-open');
                modal.setAttribute('aria-hidden', 'false');
                document.documentElement.classList.add('html-overflow-hidden');
                activeModal = modal;
            });
        });

        document.querySelectorAll('[data-pay-modal-close]').forEach(function (button) {
            button.addEventListener('click', function () {
                closeModal(button.closest('.pay-modal'));
            });
        });

        document.querySelectorAll('.pay-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    closeModal(modal);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && activeModal) {
                closeModal(activeModal);
            }
        });
    })();
</script>
@endpush
