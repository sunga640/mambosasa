@extends('layouts.member')

@php
    use App\Enums\BookingStatus;
    use App\Enums\RoomServiceOrderStatus;
    $user = auth()->user();

    // 1. Kusafisha Role (Kuzuia JSON Display)
    $roleRaw = $user->role;
    $roleName = strtolower(is_string($roleRaw) ? $roleRaw : ($roleRaw->name ?? 'guest'));

    $canManageBookings = $user->hasPermission('manage-bookings');
    $canManageCustomers = $user->hasPermission('manage-customers');
    $canManageMaintenance = $user->hasPermission('manage-maintenance');
    $canViewReports = $user->hasAnyPermission(['view-reception-reports', 'view-reports', 'view-dashboard-analytics']);
    $canManageUsers = $user->hasAnyPermission(['manage-users', 'manage-staff-users']);
    $canManageSettings = $user->hasPermission('manage-system-settings');
    $canOpenReception = $user->hasStaffPanelAccess();
    $canOpenAdmin = $user->hasAdminPanelAccess();
    $isGuest = ! ($canManageBookings || $canManageCustomers || $canManageMaintenance || $canViewReports || $canManageUsers || $canManageSettings || $canOpenReception || $canOpenAdmin);
@endphp

@section('title', __('Dashboard'))

@section('content')
    <style>
        .dash-content-card .dash-btn, .dash-content-card .button, .dash-content-card button {
            padding: .35rem .7rem !important; font-size: .84rem !important; font-weight: 500 !important; border-width: 1px !important;
        }
        .stat-card-work { padding:1.1rem; border-radius:0; border:1px solid rgba(213,172,66,.16); background:linear-gradient(135deg, rgba(17,24,39,.88), rgba(51,65,85,.92)); transition: 0.3s; }
        .work-label { font-size: 11px; text-transform: uppercase; font-weight: 700; color: #64748b; letter-spacing: 0.5px; }
        .work-value { font-size: 22px; font-weight: 700; margin-top: 5px; color: #ffffff; }
        .guest-surface-card { background:linear-gradient(135deg, rgba(15,23,42,.92), rgba(51,65,85,.95)) !important; border:1px solid rgba(213,172,66,.16) !important; color:#e5e7eb; }
        .guest-surface-card .dash-table-styled { color:#e5e7eb; }
        .guest-surface-card .dash-table-styled__head { background:rgba(30,41,59,.78); color:#f8fafc; }
        .guest-surface-card .dash-table-styled__row { background:transparent; }
        .guest-surface-card .dash-table-styled td,
        .guest-surface-card .dash-table-styled th { background: transparent !important; color: inherit; }
        .guest-surface-card .badge { background:rgba(34,197,94,.14); color:#dcfce7; border:1px solid rgba(34,197,94,.24); }
    </style>

    {{-- 1. HERO SECTION (Dynamic based on Role) --}}
    @php
        $memberHeroFallback = is_file(public_path('img/reception.jpg')) ? 'img/reception.jpg' : 'img/hero/8/1.png';
        $memberHeroBg = $isGuest
            ? $dashboardSettings->resolvedHomeHeroFirstSlide()
            : $dashboardSettings->resolvedInnerPageHero($memberHeroFallback);
    @endphp
    <div class="member-dash-hero" style="margin:-1.5rem -1.75rem 1.25rem; border-radius:0; min-height:148px; background:#1a1a1a url('{{ $memberHeroBg }}') center/cover no-repeat; position:relative; overflow:hidden;">
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
                    @if($canOpenAdmin)
                        <a href="{{ route('admin.dashboard') }}" class="dash-btn" style="background:#2563eb; color:#fff; border:none; font-weight:600;">{{ __('Admin Panel') }}</a>
                    @elseif($canOpenReception)
                        <a href="{{ route('reception.dashboard') }}" class="dash-btn" style="background:#2563eb; color:#fff; border:none; font-weight:600;">{{ __('Staff Dashboard') }}</a>
                    @endif
                    @if($canManageMaintenance)
                        <a href="{{ $canOpenAdmin ? route('admin.maintenance.index') : route('reception.maintenance.index') }}" class="dash-btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid #fff;">{{ __('Tasks List') }}</a>
                    @endif
                @endif

                <a href="{{ route('profile.edit') }}" class="dash-btn" style="background:rgba(255,255,255,0.1); color:#fff; border:1px solid rgba(255,255,255,0.3);">{{ __('My Profile') }}</a>
            </div>
        </div>
    </div>

    {{-- 2. KPI / STATS SECTION (Role Filtering) --}}
    <div style="display:grid; grid-template-columns:repeat(auto-fill, minmax(220px, 1fr)); gap:1.25rem; margin-bottom:2rem;">

        {{-- KADI ZA MANAGEMENT (Admin/Manager Tu) --}}
        @if($canViewReports || $canOpenAdmin)
            <div class="stat-card-work" style="border-left:4px solid #2563eb;">
                <div class="work-label">{{ __('Monthly Revenue') }}</div>
                <div class="work-value">TZS {{ number_format($memberStats['total_spend_confirmed'] ?? 0) }}</div>
            </div>
            <div class="stat-card-work" style="border-left:4px solid #16a34a;">
                <div class="work-label">{{ __('Confirmed Bookings') }}</div>
                <div class="work-value">{{ number_format($memberStats['bookings_total'] ?? 0) }}</div>
            </div>
        @endif

        {{-- KADI ZA MAINTENANCE (Maintenance Staff Tu) --}}
        @if($canManageMaintenance)
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
            <div class="stat-card-work" style="background:linear-gradient(135deg, rgba(15,23,42,.92), rgba(6,95,70,.92));">
                <div class="work-label">{{ __('My Confirmed Stays') }}</div>
                <div class="work-value">{{ number_format($memberStats['bookings_confirmed'] ?? 0) }}</div>
            </div>
            <div class="stat-card-work" style="background:linear-gradient(135deg, rgba(15,23,42,.92), rgba(120,53,15,.92));">
                <div class="work-label">{{ __('Awaiting Payment') }}</div>
                <div class="work-value text-orange-1">{{ number_format($memberStats['bookings_pending'] ?? 0) }}</div>
            </div>
            @if($dashboardSettings->restaurantIntegrationConfigured())
                <div class="stat-card-work" style="border-left:4px solid #0f766e;">
                    <div class="work-label">{{ __('Restaurant Orders') }}</div>
                    <div class="work-value">{{ __('Live') }}</div>
                </div>
            @endif
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
            <div style="padding:1.5rem; background:#fffbeb; border:1px solid #fde68a; border-radius:0; margin-bottom:2rem;">
                <h2 class="text-18 fw-700 mb-10" style="color:#92400e;">⚠️ {{ __('Action Required: Pending Payment') }}</h2>
                @foreach ($pendingBookings as $b)
                    <div class="d-flex justify-between items-center bg-white p-15 mb-10 border-light" style="border-radius:0;">
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

        <div class="content-box p-25 shadow-sm border-light guest-surface-card" style="border-radius:0;">
            <h2 class="text-20 fw-700 mb-20">{{ __('Your Stay History') }}</h2>
            @if($dashboardSettings->restaurantIntegrationConfigured())
                <div class="mb-20" style="display:flex;justify-content:space-between;align-items:center;gap:1rem;flex-wrap:wrap;padding:1rem;border:1px solid #d1fae5;background:#ecfdf5;">
                    <div>
                        <div class="text-12" style="font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:#0f766e;">{{ __('Restaurant Integration') }}</div>
                        <p class="text-14 mt-5 mb-0" style="opacity:.8;">{{ __('Open the connected restaurant system with your active stay so guests can place food orders securely.') }}</p>
                    </div>
                    <a href="{{ route('member.restaurant.launch') }}" class="dash-btn dash-btn--primary">{{ __('Open Restaurant Ordering') }}</a>
                </div>
            @endif
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

        @if (!empty($recentRoomServiceOrders) && count($recentRoomServiceOrders))
            <div class="content-box p-25 shadow-sm border-light mt-25 guest-surface-card" style="border-radius:0;">
                <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:center;">
                    <div>
                        <h2 class="text-20 fw-700 mb-5">{{ __('Your food orders') }}</h2>
                        <p class="text-14 opacity-60" style="margin:0;">{{ __('See which room the order belongs to, the kitchen stage, and the estimated delivery time.') }}</p>
                    </div>
                    <a href="{{ route('member.room-service.index') }}" class="dash-btn dash-btn--primary">{{ __('Open full food orders') }}</a>
                </div>
                <div class="mt-20" style="display:grid;gap:1rem;">
                    @foreach ($recentRoomServiceOrders as $order)
                        <article style="padding:1rem;border:1px solid rgba(213,172,66,.18);background:rgba(255,255,255,.04);">
                            <div style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:flex-start;">
                                <div>
                                    <div class="fw-700">{{ $order->room?->name ?? __('Room order') }}</div>
                                    <div class="text-13 opacity-60">{{ __('Order source') }}: {{ strtoupper((string) $order->request_source) }}</div>
                                </div>
                                <div style="text-align:right;">
                                    <div class="text-12 opacity-60">{{ __('Estimated delivery') }}</div>
                                    <div class="fw-700">{{ $order->estimated_ready_at?->format('H:i') ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="mt-12" style="display:flex;justify-content:space-between;gap:1rem;flex-wrap:wrap;align-items:center;">
                                <span class="badge">
                                    {{ match((string) $order->status) {
                                        RoomServiceOrderStatus::Pending->value => __('Pending'),
                                        RoomServiceOrderStatus::Preparing->value => __('Preparing'),
                                        RoomServiceOrderStatus::Delivered->value => __('Delivered'),
                                        RoomServiceOrderStatus::Cancelled->value => __('Cancelled'),
                                        default => ucfirst((string) $order->status),
                                    } }}
                                </span>
                                <span class="text-13 opacity-60">{{ __('Placed') }}: {{ $order->created_at?->format('Y-m-d H:i') }}</span>
                            </div>
                            <div class="mt-12">
                                @foreach ($order->items as $item)
                                    <div class="text-14">{{ $item->item_name }} x {{ $item->quantity }}</div>
                                @endforeach
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    @endif

    {{-- B. SEHEMU YA STAFF (Work Tasks / Operations) --}}
    @if(!$isGuest)
        <div class="content-box p-25 bg-white shadow-sm border-light" style="border-radius:0;">
            <h2 class="text-20 fw-700 mb-15">
                @if($canManageMaintenance) {{ __('Current Maintenance Tasks') }} @else {{ __('Role-based Operations') }} @endif
            </h2>
            <p class="text-14 opacity-60">
                {{ __('This dashboard now exposes tools according to the permissions assigned to your role.') }}
            </p>

            {{-- Quick Shortcuts --}}
            <div style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; margin-top:20px;">
                @if($canManageMaintenance)
                    <a href="{{ $canOpenAdmin ? route('admin.maintenance.index') : route('reception.maintenance.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Go to Job List') }}</a>
                    <a href="{{ $canOpenAdmin ? route('admin.rooms.index') : route('reception.rooms.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Update Room Status') }}</a>
                @endif
                @if($canManageBookings)
                    <a href="{{ $canOpenAdmin ? route('admin.bookings.index') : route('reception.bookings.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Check-ins List') }}</a>
                    <a href="{{ route('reception.dashboard') }}" class="dash-btn dash-btn--outline w-full">{{ __('Reception Desk') }}</a>
                @endif
                @if($canManageCustomers)
                    <a href="{{ $canOpenAdmin ? route('admin.customers.index') : route('reception.customers.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Customers') }}</a>
                @endif
                @if($canViewReports)
                    <a href="{{ $canOpenAdmin ? route('admin.reports.index') : route('reception.reports.index') }}" class="dash-btn dash-btn--outline w-full">{{ __('Reports') }}</a>
                @endif
                @if($canManageSettings && $canOpenAdmin)
                    <a href="{{ route('admin.settings.edit') }}" class="dash-btn dash-btn--outline w-full">{{ __('System Settings') }}</a>
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
