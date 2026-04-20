@extends('layouts.member')

@php
    use App\Enums\BookingStatus;
    $user = auth()->user();

    // 1. Kusafisha Role (Kuzuia JSON Display)
    $roleRaw = $user->role;
    $roleName = strtolower(is_string($roleRaw) ? $roleRaw : ($roleRaw->name ?? 'guest'));

    // 2. Logic za Mamlaka
    $isAdmin = in_array($roleName, ['admin', 'director']);
    $isManagement = in_array($roleName, ['admin', 'manager', 'director']);
    $isMaintenance = in_array($roleName, ['maintenance', 'maintainance', 'housekeeping']);
    $isReception = in_array($roleName, ['receptionist', 'manager', 'admin']);

    // Ni Guest tu kama hana role ya ki-staff
    $isGuest = in_array($roleName, ['guest', 'customer', 'member']) || (!$isAdmin && !$isMaintenance && !$isReception);
@endphp

@section('title', __('Dashboard'))

@section('content')
    <style>
        .dash-content-card .dash-btn, .dash-content-card .button, .dash-content-card button {
            padding: .35rem .7rem !important; font-size: .84rem !important; font-weight: 500 !important; border-width: 1px !important;
        }
        .stat-card-work { padding:1.25rem; border-radius:12px; border:1px solid #e2e8f0; background:#fff; transition: 0.3s; }
        .work-label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
        .work-value { font-size: 24px; font-weight: 700; margin-top: 5px; color: #0f172a; }
    </style>

    {{-- 1. HERO SECTION (Dynamic based on Role) --}}
    @php
        $memberHeroFallback = is_file(public_path('img/reception.jpg')) ? 'img/reception.jpg' : 'img/hero/8/1.png';
        $memberHeroBg = $isGuest
            ? $dashboardSettings->resolvedHomeHeroFirstSlide()
            : $dashboardSettings->resolvedInnerPageHero($memberHeroFallback);
    @endphp
    <div class="member-dash-hero" style="margin:-1.5rem -1.75rem 1.25rem; border-radius:12px 12px 0 0; min-height:148px; background:#1a1a1a url('{{ $memberHeroBg }}') center/cover no-repeat; position:relative; overflow:hidden;">
        <div style="position:absolute; inset:0; background:linear-gradient(90deg, rgba(15,23,42,0.85) 0%, rgba(15,23,42,0.4) 100%);"></div>
        <div style="position:relative; padding:1.5rem 1.75rem 1.35rem; color:#fff;">
            {{-- Badge ya Role --}}
            <span style="background:rgba(255,255,255,0.2); padding:4px 10px; border-radius:4px; font-size:10px; text-transform:uppercase; font-weight:700; letter-spacing:1px; border:1px solid rgba(255,255,255,0.3);">
                {{ $roleName }}
            </span>

            <h2 class="text-22 fw-700 mt-8">{{ __('Welcome, :name', ['name' => $user->name]) }}</h2>

            @if($isGuest)
                <p class="text-15 opacity-80 mt-5">{{ __('Manage your personal bookings and guest profile.') }}</p>
            @else
                <p class="text-15 opacity-80 mt-5">{{ __('System Operations Hub — Monitoring branch performance.') }}</p>
            @endif

            <div class="d-flex gap-10 mt-14">
                {{-- Vitendo vya Staff --}}
                @if(!$isGuest)
                    <a href="{{ route('admin.dashboard') }}" class="dash-btn" style="background:#2563eb; color:#fff; border:none; font-weight:600;">{{ __('Admin Panel') }}</a>
                    @if($isMaintenance)
                        <a href="{{ route('admin.maintenance.index') }}" class="dash-btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid #fff;">{{ __('Tasks List') }}</a>
                    @endif
                @endif

                <a href="{{ route('profile.edit') }}" class="dash-btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.3);">{{ __('My Profile') }}</a>
            </div>
        </div>
    </div>

    {{-- 2. KPI / STATS SECTION (Role Filtering) --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1.25rem; margin-bottom:2rem;">

        {{-- KADI ZA MANAGEMENT (Admin/Manager Tu) --}}
        @if($isManagement)
            <div class="stat-card-work" style="border-left:4px solid #2563eb;">
                <div class="work-label">{{ __('Monthly Revenue') }}</div>
                <div class="work-value">TZS {{ number_format($memberStats['total_revenue_month'] ?? 0) }}</div>
            </div>
            <div class="stat-card-work" style="border-left:4px solid #16a34a;">
                <div class="work-label">{{ __('Confirmed Bookings') }}</div>
                <div class="work-value">{{ number_format($memberStats['bookings_total'] ?? 0) }}</div>
            </div>
        @endif

        {{-- KADI ZA MAINTENANCE (Maintenance Staff Tu) --}}
        @if($isMaintenance)
            <div class="stat-card-work" style="border-left:4px solid #ea580c;">
                <div class="work-label">{{ __('Pending Repairs') }}</div>
                <div class="work-value">--</div> {{-- Hapa unaweza kuweka count ya maintenance --}}
            </div>
            <div class="stat-card-work" style="border-left:4px solid #6366f1;">
                <div class="work-label">{{ __('Rooms Under Maintenance') }}</div>
                <div class="work-value">{{ number_format($kpis['rooms_maintenance'] ?? 0) }}</div>
            </div>
        @endif

        {{-- KADI ZA GUEST (Mteja Pekee) --}}
        @if($isGuest)
            <div class="stat-card-work">
                <div class="work-label">{{ __('My Total Spend') }}</div>
                <div class="work-value">{{ number_format($memberStats['total_spend_confirmed'] ?? 0, 0) }}</div>
            </div>
            <div class="stat-card-work" style="background:#f0fdf4;">
                <div class="work-label">{{ __('My Confirmed Stays') }}</div>
                <div class="work-value">{{ number_format($memberStats['bookings_confirmed'] ?? 0) }}</div>
            </div>
            <div class="stat-card-work" style="background:#fffbeb;">
                <div class="work-label">{{ __('Awaiting Payment') }}</div>
                <div class="work-value text-orange-1">{{ number_format($memberStats['bookings_pending'] ?? 0) }}</div>
            </div>
        @endif

    </div>

    @if($isGuest)
        @include('partials.dashboard-month-calendar')
    @endif

    {{-- 3. CONTENT AREA (Strict Roles) --}}

    {{-- A. SEHEMU YA MTEJA (Bookings zake) --}}
    @if($isGuest)
        {{-- Payment Action Needed --}}
        @if ($pendingBookings->isNotEmpty())
            <div style="padding:1.5rem; background:#fffbeb; border:1px solid #fde68a; border-radius:12px; margin-bottom:2rem;">
                <h2 class="text-18 fw-700 mb-10" style="color:#92400e;">⚠️ {{ __('Action Required: Pending Payment') }}</h2>
                @foreach ($pendingBookings as $b)
                    <div class="d-flex justify-between items-center bg-white p-15 rounded-8 mb-10 border-light">
                        <div>
                            <div class="fw-700">{{ $b->public_reference }}</div>
                            <div class="text-12 opacity-60">{{ $b->room->name }}</div>
                        </div>
                        <div class="text-22 fw-800 dash-pay-cd" data-deadline="{{ $b->payment_deadline_at->toIso8601String() }}" style="color:#c41e3a;">--:--</div>
                        <a href="{{ route('bookings.show', $b) }}" class="dash-btn dash-btn--primary">{{ __('Pay Now') }}</a>
                    </div>
                @endforeach
            </div>
        @endif

        <div class="content-box p-25 bg-white rounded-12 shadow-sm border-light">
            <h2 class="text-20 fw-700 mb-20">{{ __('Your Stay History') }}</h2>
            @if ($recentBookings->isEmpty())
                <p class="text-14 opacity-50">{{ __('No bookings found.') }}</p>
            @else
                <table class="table w-full dash-table-styled">
                    <thead><tr class="text-left text-12 uppercase dash-table-styled__head"><th class="pb-10">{{ __('Date') }}</th><th class="pb-10">{{ __('Room') }}</th><th class="pb-10">{{ __('Status') }}</th></tr></thead>
                    <tbody>
                        @foreach ($recentBookings as $b)
                            <tr class="border-top-light dash-table-styled__row">
                                <td class="py-12">{{ $b->created_at?->format('Y-m-d') }}</td>
                                <td class="py-12">{{ $b->room?->name }}</td>
                                <td class="py-12"><span class="badge">{{ $b->status->label() }}</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    @endif

    {{-- B. SEHEMU YA STAFF (Work Tasks / Operations) --}}
    @if(!$isGuest)
        <div class="content-box p-25 bg-white rounded-12 shadow-sm border-light">
            <h2 class="text-20 fw-700 mb-15">
                @if($isMaintenance) {{ __('Current Maintenance Tasks') }} @else {{ __('Recent System Activity') }} @endif
            </h2>
            <p class="text-14 opacity-60">
                {{ __('Use the sidebar to access full management tools for your specific role.') }}
            </p>

            {{-- Quick Shortcuts --}}
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:20px;">
                @if($isMaintenance)
                    <a href="{{ route('admin.maintenance.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Go to Job List') }}</a>
                    <a href="{{ route('admin.rooms.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Update Room Status') }}</a>
                @endif
                @if($isReception)
                    <a href="{{ route('admin.bookings.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Check-ins List') }}</a>
                    <a href="{{ route('reception.dashboard') }}" class="dash-btn dash-btn--outline w-full">{{ __('Reception Desk') }}</a>
                @endif
            </div>
        </div>
    @endif

@endsection

@push('scripts')
<script>
(function () {
  // Timer ya malipo (Guest Only)
  function fmt(left) {
    var m = Math.floor(left / 60), s = left % 60;
    return String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
  }
  document.querySelectorAll('.dash-pay-cd').forEach(function (el) {
    var deadline = new Date(el.getAttribute('data-deadline')).getTime();
    function tick() {
      var left = Math.max(0, Math.floor((deadline - Date.now()) / 1000));
      el.textContent = left <= 0 ? "00:00" : fmt(left);
      if (left > 0) setTimeout(tick, 1000);
    }
    tick();
  });
})();
</script>
@endpush
