@extends('layouts.kitchen')

@section('title', __('Kitchen Staff'))

@section('content')
    <style>
        .ks-shell { display:grid; gap:1rem; }
        .ks-grid { display:grid; grid-template-columns:minmax(0, 380px) minmax(0, 1fr); gap:1rem; }
        .ks-card { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface); padding:1rem; border-radius:16px; }
        .ks-staff-list { display:grid; gap:.9rem; }
        .ks-staff-item { border:1px solid rgba(125,211,252,.14); background:rgba(255,255,255,.02); padding:1rem; border-radius:16px; display:grid; gap:.8rem; }
        .ks-staff-top { display:flex; justify-content:space-between; gap:.8rem; align-items:flex-start; }
        .ks-staff-meta { display:flex; gap:.6rem; flex-wrap:wrap; color:var(--brand-theme-muted); font-size:.82rem; }
        .ks-pill { padding:.28rem .58rem; border-radius:999px; border:1px solid rgba(125,211,252,.2); background:rgba(56,189,248,.1); color:#e8f6ff; font-size:.76rem; }
        .ks-actions { display:flex; gap:.65rem; flex-wrap:wrap; }
        .ks-modal { position:fixed; inset:0; background:rgba(7, 12, 20, .68); display:none; align-items:center; justify-content:center; padding:1rem; z-index:3000; }
        .ks-modal.is-open { display:flex; }
        .ks-modal-card { width:min(760px, 100%); max-height:92vh; overflow:auto; border:1px solid rgba(125,211,252,.2); background:#222833; border-radius:20px; padding:1rem; box-shadow:0 24px 60px rgba(0,0,0,.35); }
        .ks-modal-head { display:flex; justify-content:space-between; gap:1rem; align-items:flex-start; margin-bottom:1rem; }
        .ks-modal-close { border:none; background:rgba(255,255,255,.06); color:#eaf6ff; width:40px; height:40px; border-radius:999px; font-size:1.15rem; cursor:pointer; }
        .ks-card input[type="text"],
        .ks-card input[type="email"],
        .ks-card input[type="password"],
        .ks-card select,
        .ks-modal-card input[type="text"],
        .ks-modal-card input[type="email"],
        .ks-modal-card input[type="password"],
        .ks-modal-card select {
            width:100%;
            min-height:44px;
            border:1px solid rgba(125,211,252,.26);
            background:#121a23;
            color:#e6f0ff;
            padding:.72rem .85rem;
            font:inherit;
            border-radius:12px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.28);
        }
        .ks-card select,
        .ks-modal-card select {
            appearance:auto;
            background:#121a23;
            color:#f8fafc;
        }
        .ks-card input::placeholder,
        .ks-modal-card input::placeholder { color:#7f93ad; }
        .ks-card input:focus,
        .ks-card select:focus,
        .ks-modal-card input:focus,
        .ks-modal-card select:focus {
            outline:none;
            border-color:rgba(56,189,248,.72);
            box-shadow:0 0 0 1px rgba(56,189,248,.28), inset 0 0 0 1px rgba(15, 23, 42, 0.28);
        }
        @media (max-width: 1100px) { .ks-grid { grid-template-columns:1fr; } }
    </style>

    <div class="ks-shell">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Staff') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Create kitchen staff accounts, choose their role, and track which live tasks they are handling right now.') }}</p>
        </div>

        <div class="ks-grid">
            <section class="ks-card">
                <h2 class="text-20" style="margin-top:0;">{{ __('Create staff user') }}</h2>
                <form method="POST" action="{{ route('kitchen.staff.store') }}" class="k-form-section">
                    @csrf
                    <div class="k-form-grid">
                        <div class="k-field">
                            <label>{{ __('Full name') }}</label>
                            <input type="text" name="name" value="{{ old('name') }}" required>
                        </div>
                        <div class="k-field">
                            <label>{{ __('Email') }}</label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                        </div>
                        <div class="k-field">
                            <label>{{ __('Password') }}</label>
                            <input type="text" name="password" required>
                        </div>
                        <div class="k-field">
                            <label>{{ __('Role') }}</label>
                            <select name="role_id" required>
                                @foreach ($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="k-checkbox">
                        <label style="display:flex;align-items:center;gap:.5rem;">
                            <input type="checkbox" name="is_active" value="1" checked>
                            {{ __('Active account') }}
                        </label>
                    </div>
                    <div class="k-actions">
                        <button class="dash-btn dash-btn--primary" type="submit">{{ __('Create kitchen user') }}</button>
                    </div>
                </form>
            </section>

            <section class="ks-card">
                <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                    <div>
                        <h2 class="text-20" style="margin:0;">{{ __('Current kitchen staff') }}</h2>
                        <p class="text-14 k-muted" style="margin:.45rem 0 0;">{{ __('Click edit to open a modal, update the account, or remove staff you no longer need.') }}</p>
                    </div>
                    <div class="ks-pill">{{ $staff->count() }} {{ __('staff accounts') }}</div>
                </div>
                <div class="ks-staff-list mt-20">
                    @forelse ($staff as $member)
                        <article class="ks-staff-item">
                            <div class="ks-staff-top">
                                <div>
                                    <div class="fw-700" style="font-size:1.05rem;">{{ $member->name }}</div>
                                    <div class="ks-staff-meta mt-5">
                                        <span>{{ $member->email }}</span>
                                        <span>{{ $member->role?->name ?: __('Kitchen role') }}</span>
                                        <span>{{ $member->is_active ? __('Active') : __('Inactive') }}</span>
                                        <span>{{ __('Tasks') }} {{ number_format($member->assignedRoomServiceOrders->whereIn('status', ['pending', 'preparing'])->count()) }}</span>
                                    </div>
                                </div>
                                <div class="ks-pill">{{ (int) $member->created_by_user_id === (int) auth()->id() ? __('Managed by you') : __('Primary account') }}</div>
                            </div>

                            <div class="ks-actions">
                                @if ((int) $member->created_by_user_id === (int) auth()->id())
                                    <button type="button" class="dash-btn dash-btn--ghost js-open-staff-modal" data-modal="staff-modal-{{ $member->id }}">{{ __('Edit staff') }}</button>
                                    <form method="POST" action="{{ route('kitchen.staff.destroy', $member) }}" onsubmit="return confirm('{{ __('Delete this kitchen staff account?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="dash-btn" type="submit">{{ __('Delete staff') }}</button>
                                    </form>
                                @else
                                    <span class="text-13 k-muted">{{ __('This is your main kitchen account.') }}</span>
                                @endif
                            </div>
                        </article>
                    @empty
                        <div class="k-muted">{{ __('No kitchen staff created yet.') }}</div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>

    @foreach ($staff as $member)
        @if ((int) $member->created_by_user_id === (int) auth()->id())
            <div class="ks-modal" id="staff-modal-{{ $member->id }}">
                <div class="ks-modal-card">
                    <div class="ks-modal-head">
                        <div>
                            <h3 class="text-20" style="margin:0;">{{ __('Edit kitchen staff') }}</h3>
                            <p class="text-14 k-muted" style="margin:.45rem 0 0;">{{ $member->name }} · {{ $member->email }}</p>
                        </div>
                        <button type="button" class="ks-modal-close js-close-staff-modal" data-modal="staff-modal-{{ $member->id }}">×</button>
                    </div>

                    <form method="POST" action="{{ route('kitchen.staff.update', $member) }}" class="k-form-section">
                        @csrf
                        @method('PUT')
                        <div class="k-form-grid">
                            <div class="k-field">
                                <label>{{ __('Name') }}</label>
                                <input type="text" name="name" value="{{ $member->name }}" required>
                            </div>
                            <div class="k-field">
                                <label>{{ __('Email') }}</label>
                                <input type="email" name="email" value="{{ $member->email }}" required>
                            </div>
                            <div class="k-field">
                                <label>{{ __('New password') }}</label>
                                <input type="text" name="password" placeholder="{{ __('Leave blank to keep current') }}">
                            </div>
                            <div class="k-field">
                                <label>{{ __('Role') }}</label>
                                <select name="role_id" required>
                                    @foreach ($roles as $role)
                                        <option value="{{ $role->id }}" @selected((int) $member->role_id === (int) $role->id)>{{ $role->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="k-checkbox">
                            <label style="display:flex;align-items:center;gap:.5rem;">
                                <input type="checkbox" name="is_active" value="1" @checked($member->is_active)>
                                {{ __('Active account') }}
                            </label>
                        </div>
                        <div class="k-actions">
                            <button class="dash-btn dash-btn--primary" type="submit">{{ __('Save staff changes') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endif
    @endforeach

    <script>
        document.querySelectorAll('.js-open-staff-modal').forEach(function (button) {
            button.addEventListener('click', function () {
                const modal = document.getElementById(button.dataset.modal);
                if (modal) modal.classList.add('is-open');
            });
        });

        document.querySelectorAll('.js-close-staff-modal').forEach(function (button) {
            button.addEventListener('click', function () {
                const modal = document.getElementById(button.dataset.modal);
                if (modal) modal.classList.remove('is-open');
            });
        });

        document.querySelectorAll('.ks-modal').forEach(function (modal) {
            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.remove('is-open');
                }
            });
        });
    </script>
@endsection
