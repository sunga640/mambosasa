<nav class="app-nav">
    <div>
        <a href="{{ route('site.home') }}">{{ config('app.name') }}</a>
        <a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a>
        @if (auth()->user()?->isSuperAdmin())
            <a href="{{ route('admin.dashboard') }}">{{ __('Admin') }}</a>
        @endif
    </div>
    <div>
        <span style="opacity:.9;margin-right:1rem;">{{ Auth::user()->name }}</span>
        <a href="{{ route('profile.edit') }}">{{ __('Profile') }}</a>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
            @csrf
            <button type="submit" style="background:transparent;border:1px solid #F9DABA;color:#F9DABA;cursor:pointer;padding:.35rem .75rem;border-radius:6px;margin-left:.5rem;">{{ __('Log out') }}</button>
        </form>
    </div>
</nav>
