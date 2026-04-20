@php
    $profileLayout = auth()->user()?->isSuperAdmin() ? 'layouts.admin' : 'layouts.member';
    $u = auth()->user();
    $parts = preg_split('/\s+/', trim((string) $u->name), -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $initials = mb_strtoupper(mb_substr($parts[0] ?? '?', 0, 1));
    if (count($parts) > 1) {
        $initials .= mb_strtoupper(mb_substr($parts[count($parts) - 1], 0, 1));
    }
@endphp
@extends($profileLayout)

@section('title', __('Profile'))

@section('content')
    <div class="profile-page-grid" style="display:grid;grid-template-columns:minmax(0,1fr) minmax(260px,320px);gap:2rem;align-items:start;">
        <div>
            <h1 class="text-30" style="margin-top:0;">{{ __('Profile') }}</h1>

            @include('profile.partials.update-profile-information-form')
            @include('profile.partials.update-password-form')
            @include('profile.partials.delete-user-form')
        </div>
        <aside class="profile-summary-card" style="border:1px solid #e5e5e5;border-radius:12px;padding:1.75rem 1.5rem;text-align:center;background:#fff;">
            <div style="width:72px;height:72px;border-radius:50%;background:#111;color:#fff;display:flex;align-items:center;justify-content:center;margin:0 auto 1rem;font-weight:700;font-size:1.35rem;border:2px solid #e0e0e0;">{{ $initials }}</div>
            <strong style="font-size:1.05rem;color:#111;display:block;">{{ $u->name }}</strong>
            <div class="text-13" style="opacity:.65;margin-top:.4rem;word-break:break-word;">{{ $u->email }}</div>
            @if ($u->role)
                <div class="text-12 mt-15" style="opacity:.55;">{{ $u->role->name }}</div>
            @endif
        </aside>
    </div>
    <style>
        @media (max-width: 900px) {
            .profile-page-grid { grid-template-columns: 1fr !important; }
        }
    </style>
@endsection
