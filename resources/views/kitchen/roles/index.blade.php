@extends('layouts.kitchen')

@section('title', __('Kitchen Roles'))

@section('content')
    <style>
        .kr-shell { display:grid; gap:1.15rem; }
        .kr-card { border:1px solid var(--brand-theme-border); background:var(--brand-theme-surface); padding:1.15rem; border-radius:18px; }
        .kr-create-grid { display:grid; gap:1rem; }
        .kr-role-list { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:1rem; }
        .kr-role-card { border:1px solid rgba(125,211,252,.14); background:rgba(255,255,255,.02); border-radius:16px; padding:1rem; display:grid; gap:.7rem; }
        .kr-role-top { display:flex; justify-content:space-between; gap:.75rem; align-items:flex-start; }
        .kr-role-badges { display:flex; flex-wrap:wrap; gap:.45rem; }
        .kr-badge { padding:.28rem .6rem; border-radius:999px; border:1px solid rgba(125,211,252,.2); background:rgba(56,189,248,.1); font-size:.74rem; color:#d9efff; }
        .kr-permission-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(220px, 1fr)); gap:.85rem; }
        .kr-group { border:1px solid rgba(125,211,252,.16); background:rgba(255,255,255,.02); border-radius:14px; padding:.9rem; }
        .kr-checklist { display:grid; gap:.55rem; margin-top:.75rem; }
        .kr-check { display:flex; gap:.65rem; align-items:flex-start; }
        .kr-disclosure { border:1px solid rgba(125,211,252,.16); background:rgba(255,255,255,.02); border-radius:16px; margin-top:1rem; overflow:hidden; }
        .kr-disclosure summary { list-style:none; cursor:pointer; padding:1rem; display:flex; align-items:center; justify-content:space-between; gap:1rem; font-weight:700; color:#eef8ff; }
        .kr-disclosure summary::-webkit-details-marker { display:none; }
        .kr-disclosure-body { padding:0 1rem 1rem; }
        .kr-option-grid { display:grid; grid-template-columns:repeat(auto-fit, minmax(250px, 1fr)); gap:.8rem; }
        .kr-option { border:1px solid rgba(125,211,252,.14); background:#202732; border-radius:14px; padding:.85rem; min-height:100%; }
        .kr-option label { display:flex; gap:.7rem; align-items:flex-start; margin:0; }
        .kr-option input[type="checkbox"] { width:18px; height:18px; margin-top:.15rem; accent-color:#38bdf8; }
        .kr-matrix-wrap { overflow:auto; border:1px solid rgba(125,211,252,.14); border-radius:16px; }
        .kr-matrix { width:100%; min-width:900px; border-collapse:collapse; }
        .kr-matrix th,
        .kr-matrix td { border-bottom:1px solid rgba(125,211,252,.1); vertical-align:top; padding:.9rem .8rem; }
        .kr-matrix th { background:rgba(255,255,255,.02); text-align:left; color:#ecf7ff; font-size:.85rem; letter-spacing:.08em; text-transform:uppercase; }
        .kr-matrix th:first-child,
        .kr-matrix td:first-child { min-width:280px; position:sticky; left:0; background:#262c36; z-index:1; }
        .kr-matrix thead th:first-child { z-index:2; }
        .kr-matrix-role { min-width:220px; }
        .kr-matrix-role input[type="text"] { margin-top:.55rem; }
        .kr-matrix-role small { display:block; margin-top:.35rem; color:#93adc9; }
        .kr-toggle { display:flex; justify-content:center; align-items:center; min-height:48px; }
        .kr-toggle input[type="checkbox"] { width:18px; height:18px; accent-color:#38bdf8; }
        .kr-permission-name { font-weight:700; color:#eef8ff; }
        .kr-permission-desc { color:#9bb0c9; font-size:.84rem; margin-top:.28rem; line-height:1.55; }
        .kr-pagination .pagination { margin:0; }
        .kr-empty { padding:1rem; border:1px dashed rgba(125,211,252,.16); border-radius:14px; color:#a7bbd4; }

        .kr-card input[type="text"],
        .kr-card select {
            width:100%;
            min-height:46px;
            border:1px solid rgba(125,211,252,.26);
            background:#121a23;
            color:#e6f0ff;
            padding:.78rem .9rem;
            font:inherit;
            border-radius:12px;
            box-shadow: inset 0 0 0 1px rgba(15, 23, 42, 0.28);
        }
        .kr-card input::placeholder { color:#7f93ad; }
        .kr-card input:focus,
        .kr-card select:focus {
            outline:none;
            border-color:rgba(56,189,248,.72);
            box-shadow:0 0 0 1px rgba(56,189,248,.28), inset 0 0 0 1px rgba(15, 23, 42, 0.28);
        }
        @media (max-width: 768px) {
            .kr-card { padding:1rem; }
            .kr-role-top { flex-direction:column; }
            .kr-matrix th:first-child,
            .kr-matrix td:first-child { position:static; }
        }
    </style>

    <div class="kr-shell">
        <div>
            <h1 class="text-30" style="margin:0;color:var(--brand-theme-heading);">{{ __('Kitchen Roles') }}</h1>
            <p class="text-14 k-muted" style="margin-top:.45rem;">{{ __('Create the role first, review the paginated list of available roles, then manage permissions through one clean matrix view.') }}</p>
        </div>

        <section class="kr-card">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div>
                    <h2 class="text-20" style="margin:0;">{{ __('Create role') }}</h2>
                    <p class="text-14 k-muted" style="margin:.45rem 0 0;">{{ __('Start with the role profile, then choose the permissions it should unlock for kitchen staff.') }}</p>
                </div>
                <div class="kr-badge">{{ $permissions->count() }} {{ __('permissions available') }}</div>
            </div>

            <form method="POST" action="{{ route('kitchen.roles.store') }}" class="k-form-section mt-20">
                @csrf
                <div class="k-form-grid">
                    <div class="k-field">
                        <label>{{ __('Role name') }}</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="k-field">
                        <label>{{ __('Role slug') }}</label>
                        <input type="text" name="slug" placeholder="{{ __('Optional') }}">
                    </div>
                </div>

                <details class="kr-disclosure">
                    <summary>
                        <span>{{ __('Select permissions') }}</span>
                        <span class="kr-badge">{{ __('Click to open') }}</span>
                    </summary>
                    <div class="kr-disclosure-body">
                        <div class="kr-permission-grid">
                            @foreach ($permissionGroups as $group)
                                <div class="kr-group">
                                    <div class="fw-700">{{ $group['label'] }}</div>
                                    <div class="text-13 k-muted mt-5">{{ $group['description'] }}</div>
                                    <div class="kr-option-grid mt-15">
                                        @foreach ($group['permissions'] as $permission)
                                            <div class="kr-option">
                                                <label>
                                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" @checked($permission->slug === 'access-kitchen-panel')>
                                                    <span>
                                                        <strong>{{ $permission->name }}</strong><br>
                                                        <span class="text-13 k-muted">{{ $permission->description }}</span>
                                                    </span>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </details>

                <div class="k-actions">
                    <button class="dash-btn dash-btn--primary" type="submit">{{ __('Create role') }}</button>
                </div>
            </form>
        </section>

        <section class="kr-card">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div>
                    <h2 class="text-20" style="margin:0;">{{ __('Available kitchen roles') }}</h2>
                    <p class="text-14 k-muted" style="margin:.45rem 0 0;">{{ __('These are the roles on the current page. The matrix below edits only these visible roles, and the list is paginated to keep the page short.') }}</p>
                </div>
                <div class="kr-badge">{{ __('Page :page of :pages', ['page' => $roles->currentPage(), 'pages' => $roles->lastPage()]) }}</div>
            </div>

            @if ($roles->count())
                <div class="kr-role-list mt-20">
                    @foreach ($roles as $role)
                        <article class="kr-role-card">
                            <div class="kr-role-top">
                                <div>
                                    <div class="fw-700" style="font-size:1.05rem;">{{ $role->name }}</div>
                                    <div class="text-13 k-muted mt-5">{{ $role->slug }} @if($role->is_system) · {{ __('System role') }} @else · {{ __('Custom role') }} @endif</div>
                                </div>
                                <div class="kr-badge">{{ $role->permissions->count() }} {{ __('permissions') }}</div>
                            </div>
                            <div class="kr-role-badges">
                                @foreach ($role->permissions->take(4) as $permission)
                                    <span class="kr-badge">{{ $permission->name }}</span>
                                @endforeach
                                @if ($role->permissions->count() > 4)
                                    <span class="kr-badge">+{{ $role->permissions->count() - 4 }}</span>
                                @endif
                            </div>
                            <div class="text-13 k-muted">{{ __('Edit this role from the matrix section below.') }}</div>
                        </article>
                    @endforeach
                </div>

                <div class="kr-pagination mt-20">
                    {{ $roles->links() }}
                </div>
            @else
                <div class="kr-empty mt-20">{{ __('No kitchen roles found yet.') }}</div>
            @endif
        </section>

        <section class="kr-card">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start;flex-wrap:wrap;">
                <div>
                    <h2 class="text-20" style="margin:0;">{{ __('Permission matrix') }}</h2>
                    <p class="text-14 k-muted" style="margin:.45rem 0 0;">{{ __('One row per permission, one column per role. This keeps the view short and makes permission comparison much easier.') }}</p>
                </div>
                <div class="kr-badge">{{ __('Visible roles: :count', ['count' => $roles->count()]) }}</div>
            </div>

            @if ($roles->count())
                <form method="POST" action="{{ route('kitchen.roles.matrix.update') }}" class="mt-20">
                    @csrf
                    @method('PUT')
                    <div class="kr-matrix-wrap">
                        <table class="kr-matrix">
                            <thead>
                                <tr>
                                    <th>{{ __('Permission / Role') }}</th>
                                    @foreach ($roles as $role)
                                        <th class="kr-matrix-role">
                                            <input type="hidden" name="roles[{{ $role->id }}][id]" value="{{ $role->id }}">
                                            <div class="fw-700">{{ $role->is_system ? __('System') : __('Custom') }}</div>
                                            <input type="text" name="roles[{{ $role->id }}][name]" value="{{ $role->name }}" required>
                                            <small>{{ __('Role name') }}</small>
                                            <input type="text" name="roles[{{ $role->id }}][slug]" value="{{ $role->slug }}" @disabled($role->is_system)>
                                            <small>{{ $role->is_system ? __('System slug is locked') : __('Role slug') }}</small>
                                        </th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($permissions as $permission)
                                    <tr>
                                        <td>
                                            <div class="kr-permission-name">{{ $permission->name }}</div>
                                            <div class="kr-permission-desc">{{ $permission->description }}</div>
                                        </td>
                                        @foreach ($roles as $role)
                                            <td>
                                                <div class="kr-toggle">
                                                    <input
                                                        type="checkbox"
                                                        name="roles[{{ $role->id }}][permission_ids][]"
                                                        value="{{ $permission->id }}"
                                                        @checked($role->permissions->contains('id', $permission->id))
                                                        @disabled($permission->slug === 'access-kitchen-panel')>
                                                    @if ($permission->slug === 'access-kitchen-panel')
                                                        <input type="hidden" name="roles[{{ $role->id }}][permission_ids][]" value="{{ $permission->id }}">
                                                    @endif
                                                </div>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="k-actions mt-20">
                        <button class="dash-btn dash-btn--primary" type="submit">{{ __('Save role matrix') }}</button>
                    </div>
                </form>
            @else
                <div class="kr-empty mt-20">{{ __('Create the first role to unlock the matrix view.') }}</div>
            @endif
        </section>
    </div>
@endsection
