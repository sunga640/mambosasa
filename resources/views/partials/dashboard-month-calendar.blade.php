{{--
  @var array $dashCalendar from DashboardMonthCalendar::forStaffBookings / forMemberStays
--}}
@php
    $dc = $dashCalendar ?? null;
    $weekdayLabels = [__('Mo'), __('Tu'), __('We'), __('Th'), __('Fr'), __('Sa'), __('Su')];
@endphp
@if ($dc && !empty($dc['weeks']))
    <div class="dash-open-calendar dash-open-calendar--gradient">
        <div class="dash-open-calendar__head">
            <div>
                <div class="dash-open-calendar__eyebrow">{{ __('This month') }}</div>
                <div class="dash-open-calendar__title">{{ $dc['title'] }}</div>
            </div>
            @if (($dc['mode'] ?? '') === 'staff')
                <div class="dash-open-calendar__legend">
                    <span><span class="dash-cal-dot dash-cal-dot--new"></span>{{ __('New bookings') }}</span>
                    <span><span class="dash-cal-dot dash-cal-dot--ok"></span>{{ __('Confirmed') }}</span>
                </div>
            @else
                <div class="dash-open-calendar__hint">{{ __('Stay nights, checkout day, and checkout time follow your booking settings.') }}</div>
            @endif
        </div>
        <div class="dash-open-calendar__dow">
            @foreach ($weekdayLabels as $wd)
                <div>{{ $wd }}</div>
            @endforeach
        </div>
        @foreach ($dc['weeks'] as $week)
            <div class="dash-open-calendar__week">
                @foreach ($week as $cell)
                    @if ($cell === null)
                        <div class="dash-open-calendar__cell dash-open-calendar__cell--empty"></div>
                    @else
                        @php
                            $isStaff = ($cell['mode'] ?? '') === 'staff';
                            $n = (int) ($cell['new_bookings'] ?? 0);
                            $c = (int) ($cell['confirmed'] ?? 0);
                            $stay = !empty($cell['stay']);
                            $isCheckout = !empty($cell['is_checkout']);
                            $checkoutClock = $cell['checkout_time'] ?? null;
                            $today = !empty($cell['is_today']);
                            $cellMods = [];
                            if ($today) $cellMods[] = 'today';
                            if ($stay) $cellMods[] = 'stay';
                            if ($isCheckout) $cellMods[] = 'checkout';
                            $cellClass = trim('dash-open-calendar__cell '.implode(' ', array_map(fn ($m) => 'dash-open-calendar__cell--'.$m, $cellMods)));
                        @endphp
                        <div class="{{ $cellClass }}" title="{{ $cell['date'] ?? '' }}">
                            <span class="dash-open-calendar__daynum">{{ $cell['day'] ?? '' }}</span>
                            @if ($isStaff)
                                @if ($n > 0)
                                    <span class="dash-cal-pill dash-cal-pill--new">+{{ $n }}</span>
                                @endif
                                @if ($c > 0)
                                    <span class="dash-cal-pill dash-cal-pill--ok">✓{{ $c }}</span>
                                @endif
                            @elseif ($isCheckout && $checkoutClock)
                                <span class="dash-cal-pill dash-cal-pill--out">{{ __('Out') }}</span>
                                <span class="dash-cal-time">{{ \Carbon\Carbon::createFromFormat('H:i', $checkoutClock)->format('g:i A') }}</span>
                            @elseif ($stay)
                                <span class="dash-cal-pill dash-cal-pill--stay">{{ __('Stay') }}</span>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>
        @endforeach
    </div>
    <style>
        .dash-open-calendar--gradient {
            margin-bottom: 1.5rem;
            padding: 1.1rem 1.2rem 1.15rem;
            border-radius: 14px;
            border: 1px solid rgba(255, 255, 255, 0.35);
            background: linear-gradient(145deg, rgba(30, 77, 107, 0.12) 0%, rgba(18, 34, 35, 0.08) 38%, rgba(0, 153, 204, 0.1) 100%);
            box-shadow: 0 8px 28px rgba(18, 34, 35, 0.08);
        }
        .dash-open-calendar__head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: .5rem;
            margin-bottom: .75rem;
        }
        .dash-open-calendar__eyebrow {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #64748b;
            font-weight: 700;
        }
        .dash-open-calendar__title {
            font-size: 1.05rem;
            font-weight: 700;
            color: #0f172a;
        }
        .dash-open-calendar__legend, .dash-open-calendar__hint {
            display: flex;
            gap: 1rem;
            font-size: 11px;
            color: #64748b;
            flex-wrap: wrap;
            align-items: center;
        }
        .dash-open-calendar__hint { max-width: 22rem; line-height: 1.35; }
        .dash-cal-dot {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 2px;
            vertical-align: middle;
            margin-right: 4px;
        }
        .dash-cal-dot--new { background: linear-gradient(135deg, #60a5fa, #2563eb); }
        .dash-cal-dot--ok { background: linear-gradient(135deg, #4ade80, #16a34a); }
        .dash-open-calendar__dow {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            text-align: center;
            font-size: 10px;
            font-weight: 700;
            color: #94a3b8;
            text-transform: uppercase;
            margin-bottom: 6px;
        }
        .dash-open-calendar__week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 4px;
            margin-bottom: 4px;
        }
        .dash-open-calendar__cell {
            min-height: 54px;
            border-radius: 10px;
            padding: 4px 2px 5px;
            border: 1px solid rgba(226, 232, 240, 0.95);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 2px;
        }
        .dash-open-calendar__cell--today {
            border-color: #2563eb;
            background: linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%);
        }
        .dash-open-calendar__cell--stay {
            border-color: rgba(234, 88, 12, 0.35);
            background: linear-gradient(160deg, #fff7ed 0%, #ffedd5 100%);
        }
        .dash-open-calendar__cell--checkout {
            border-color: rgba(196, 30, 58, 0.45);
            background: linear-gradient(155deg, #fff1f2 0%, #ffe4e6 100%);
        }
        .dash-open-calendar__cell--empty { min-height: 52px; border: none; background: transparent; }
        .dash-open-calendar__daynum {
            font-size: 12px;
            font-weight: 700;
            color: #0f172a;
        }
        .dash-cal-pill {
            font-size: 8px;
            font-weight: 800;
            border-radius: 4px;
            padding: 2px 4px;
            letter-spacing: .02em;
        }
        .dash-cal-pill--new { color: #1e40af; background: linear-gradient(90deg, #dbeafe, #bfdbfe); }
        .dash-cal-pill--ok { color: #166534; background: linear-gradient(90deg, #dcfce7, #bbf7d0); }
        .dash-cal-pill--stay { color: #9a3412; background: linear-gradient(90deg, #ffedd5, #fed7aa); }
        .dash-cal-pill--out { color: #9f1239; background: linear-gradient(90deg, #ffe4e6, #fecdd3); }
        .dash-cal-time { font-size: 8px; font-weight: 700; color: #881337; line-height: 1.1; }
    </style>
@endif
