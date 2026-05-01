{{--
  @var array $dashCalendar from DashboardMonthCalendar::forStaffBookings / forMemberStays
--}}
@php
    $dc = $dashCalendar ?? null;
    $weekdayLabels = [__('Mo'), __('Tu'), __('We'), __('Th'), __('Fr'), __('Sa'), __('Su')];
@endphp
@if ($dc && !empty($dc['weeks']))
    <div class="dash-open-calendar dash-open-calendar--gradient" data-dash-calendar>
        <button
            type="button"
            class="dash-open-calendar__toggle"
            data-dash-calendar-toggle
            aria-expanded="false"
            aria-controls="dashCalendarBody"
        >
            <span>
                <span class="dash-open-calendar__eyebrow">{{ __('This month') }}</span>
                <span class="dash-open-calendar__title">{{ $dc['title'] }}</span>
            </span>
            <span class="dash-open-calendar__toggle-side">
                @if (($dc['mode'] ?? '') === 'staff')
                    <span class="dash-open-calendar__legend">
                        <span><span class="dash-cal-dot dash-cal-dot--new"></span>{{ __('New bookings') }}</span>
                        <span><span class="dash-cal-dot dash-cal-dot--ok"></span>{{ __('Confirmed') }}</span>
                    </span>
                @else
                    <span class="dash-open-calendar__hint">{{ __('Stay details') }}</span>
                @endif
                <span class="dash-open-calendar__caret" data-dash-calendar-caret>+</span>
            </span>
        </button>

        <div class="dash-open-calendar__body" id="dashCalendarBody" hidden>
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
                            <div
                                class="{{ $cellClass }}"
                                title="{{ $cell['date'] ?? '' }}"
                                data-cal-cell
                                data-cal-mode="{{ $cell['mode'] ?? ($dc['mode'] ?? 'staff') }}"
                                data-cal-date="{{ $cell['date'] ?? '' }}"
                                data-cal-new="{{ $n }}"
                                data-cal-confirmed="{{ $c }}"
                                data-cal-stay="{{ $stay ? '1' : '0' }}"
                                data-cal-checkout="{{ $isCheckout ? '1' : '0' }}"
                                data-cal-time="{{ $checkoutClock ?? '' }}"
                            >
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
    </div>

    <div class="dash-cal-modal" id="dashCalendarModal" aria-hidden="true">
        <div class="dash-cal-modal__backdrop" data-cal-modal-close></div>
        <div class="dash-cal-modal__panel">
            <button type="button" class="dash-cal-modal__close" data-cal-modal-close aria-label="{{ __('Close') }}">&times;</button>
            <div class="dash-cal-modal__eyebrow">{{ __('Calendar details') }}</div>
            <h3 class="dash-cal-modal__title" id="dashCalendarModalTitle">{{ __('Selected day') }}</h3>
            <div class="dash-cal-modal__stats">
                <article>
                    <span id="dashCalendarModalLabelOne">{{ __('New bookings') }}</span>
                    <strong id="dashCalendarModalNew">0</strong>
                </article>
                <article>
                    <span id="dashCalendarModalLabelTwo">{{ __('Confirmed') }}</span>
                    <strong id="dashCalendarModalConfirmed">0</strong>
                </article>
            </div>
        </div>
    </div>

    <style>
        .dash-open-calendar--gradient {
            margin-bottom: 1rem;
            padding: .95rem 1rem;
            border-radius: 0;
            border: 1px solid var(--brand-theme-border, #e2e8f0);
            background: var(--brand-theme-surface-card, #ffffff);
            box-shadow: 0 12px 28px rgba(0, 0, 0, 0.14);
        }
        .dash-open-calendar__toggle {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            text-align: left;
        }
        .dash-open-calendar__eyebrow {
            display: block;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--brand-theme-muted, #64748b);
            font-weight: 700;
        }
        .dash-open-calendar__title {
            display: block;
            margin-top: .18rem;
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--brand-theme-heading, #0f172a);
        }
        .dash-open-calendar__toggle-side {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: .9rem;
            flex-wrap: wrap;
        }
        .dash-open-calendar__legend,
        .dash-open-calendar__hint {
            display: flex;
            gap: .9rem;
            font-size: 11px;
            color: var(--brand-theme-muted, #64748b);
            flex-wrap: wrap;
            align-items: center;
        }
        .dash-open-calendar__hint {
            max-width: 20rem;
            line-height: 1.35;
        }
        .dash-open-calendar__caret {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 2rem;
            height: 2rem;
            border: 1px solid var(--brand-theme-border, #e2e8f0);
            color: var(--brand-theme-heading, #0f172a);
            font-size: 1.25rem;
            font-weight: 700;
            flex-shrink: 0;
        }
        .dash-open-calendar.is-open .dash-open-calendar__caret {
            background: var(--brand-theme-surface-soft, #f8fafc);
        }
        .dash-open-calendar__body {
            margin-top: .9rem;
        }
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
            gap: 8px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: var(--brand-theme-muted, #94a3b8);
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .dash-open-calendar__week {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 8px;
            margin-bottom: 8px;
        }
        .dash-open-calendar__cell {
            min-height: 90px;
            border-radius: 0;
            padding: 10px 6px 11px;
            border: 1px solid var(--brand-theme-border, rgba(226, 232, 240, 0.95));
            background: var(--brand-theme-surface, #ffffff);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            gap: 2px;
            cursor: pointer;
        }
        .dash-open-calendar__cell--today {
            border-color: var(--brand-theme-heading, #2563eb);
            background: var(--brand-theme-surface-soft, #ffffff);
        }
        .dash-open-calendar__cell--stay {
            border-color: rgba(125, 211, 252, 0.42);
            background: var(--brand-theme-surface-soft, #ffffff);
        }
        .dash-open-calendar__cell--checkout {
            border-color: rgba(196, 30, 58, 0.45);
            background: var(--brand-theme-surface-soft, #ffffff);
        }
        .dash-open-calendar__cell--empty {
            min-height: 90px;
            border: none;
            background: transparent;
        }
        .dash-open-calendar__daynum {
            font-size: 15px;
            font-weight: 700;
            color: var(--brand-theme-text, #0f172a);
        }
        .dash-cal-pill {
            font-size: 10px;
            font-weight: 800;
            border-radius: 0;
            padding: 3px 8px;
            letter-spacing: .02em;
        }
        .dash-cal-pill--new { color: #dbeafe; background: linear-gradient(90deg, rgba(37, 99, 235, 0.55), rgba(59, 130, 246, 0.22)); }
        .dash-cal-pill--ok { color: #dcfce7; background: linear-gradient(90deg, rgba(22, 163, 74, 0.5), rgba(34, 197, 94, 0.22)); }
        .dash-cal-pill--stay { color: #dbeafe; background: linear-gradient(90deg, rgba(14, 116, 144, 0.55), rgba(56, 189, 248, 0.2)); }
        .dash-cal-pill--out { color: #fecdd3; background: linear-gradient(90deg, rgba(190, 24, 93, 0.5), rgba(244, 63, 94, 0.2)); }
        .dash-cal-time { font-size: 10px; font-weight: 700; color: #fecdd3; line-height: 1.1; }
        .dash-cal-modal {
            position: fixed;
            inset: 0;
            display: none;
            z-index: 12050;
        }
        .dash-cal-modal.is-open { display: block; }
        .dash-cal-modal__backdrop {
            position: absolute;
            inset: 0;
            background: rgba(15, 23, 42, 0.45);
        }
        .dash-cal-modal__panel {
            position: relative;
            z-index: 1;
            width: min(92vw, 24rem);
            margin: 10vh auto 0;
            padding: 1.25rem;
            background: var(--brand-theme-surface, #fff);
            border: 1px solid var(--brand-theme-border, #e2e8f0);
            box-shadow: 0 22px 50px rgba(15, 23, 42, 0.18);
        }
        .dash-cal-modal__close {
            position: absolute;
            top: .7rem;
            right: .7rem;
            border: none;
            background: transparent;
            font-size: 1.4rem;
            cursor: pointer;
        }
        .dash-cal-modal__eyebrow {
            font-size: 11px;
            letter-spacing: .12em;
            text-transform: uppercase;
            color: var(--brand-theme-muted, #64748b);
            font-weight: 700;
        }
        .dash-cal-modal__title {
            margin: .55rem 0 1rem;
            color: var(--brand-theme-heading, #0f172a);
            font-size: 1.2rem;
        }
        .dash-cal-modal__stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .8rem;
        }
        .dash-cal-modal__stats article {
            border: 1px solid var(--brand-theme-border, #e2e8f0);
            padding: .9rem;
            background: var(--brand-theme-surface-card, #f8fafc);
        }
        .dash-cal-modal__stats span {
            display: block;
            font-size: .72rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: var(--brand-theme-muted, #64748b);
            font-weight: 700;
        }
        .dash-cal-modal__stats strong {
            display: block;
            margin-top: .5rem;
            font-size: 1.4rem;
            color: var(--brand-theme-text, #0f172a);
        }
        @media (max-width: 767px) {
            .dash-open-calendar__toggle {
                align-items: flex-start;
                flex-direction: column;
            }
            .dash-open-calendar__toggle-side {
                width: 100%;
                justify-content: space-between;
            }
            .dash-open-calendar__dow,
            .dash-open-calendar__week {
                gap: 6px;
            }
            .dash-open-calendar__cell,
            .dash-open-calendar__cell--empty {
                min-height: 74px;
            }
        }
    </style>

    <script>
        (function () {
            document.querySelectorAll('[data-dash-calendar]').forEach(function (wrap) {
                var toggle = wrap.querySelector('[data-dash-calendar-toggle]');
                var body = wrap.querySelector('.dash-open-calendar__body');
                var caret = wrap.querySelector('[data-dash-calendar-caret]');
                if (toggle && body && caret) {
                    toggle.addEventListener('click', function () {
                        var isOpen = !body.hasAttribute('hidden');
                        if (isOpen) {
                            body.setAttribute('hidden', 'hidden');
                            wrap.classList.remove('is-open');
                            toggle.setAttribute('aria-expanded', 'false');
                            caret.textContent = '+';
                        } else {
                            body.removeAttribute('hidden');
                            wrap.classList.add('is-open');
                            toggle.setAttribute('aria-expanded', 'true');
                            caret.textContent = '−';
                        }
                    });
                }
            });

            var modal = document.getElementById('dashCalendarModal');
            var titleEl = document.getElementById('dashCalendarModalTitle');
            var newEl = document.getElementById('dashCalendarModalNew');
            var confirmedEl = document.getElementById('dashCalendarModalConfirmed');
            var labelOneEl = document.getElementById('dashCalendarModalLabelOne');
            var labelTwoEl = document.getElementById('dashCalendarModalLabelTwo');
            if (!modal || !titleEl || !newEl || !confirmedEl || !labelOneEl || !labelTwoEl) return;

            function closeModal() {
                modal.classList.remove('is-open');
            }

            document.querySelectorAll('[data-cal-modal-close]').forEach(function (el) {
                el.addEventListener('click', closeModal);
            });

            document.querySelectorAll('[data-cal-cell]').forEach(function (cell) {
                cell.addEventListener('click', function () {
                    titleEl.textContent = cell.dataset.calDate || '{{ __('Selected day') }}';
                    if ((cell.dataset.calMode || 'staff') === 'member') {
                        var isCheckout = cell.dataset.calCheckout === '1';
                        var isStay = cell.dataset.calStay === '1';
                        labelOneEl.textContent = '{{ __('Status') }}';
                        labelTwoEl.textContent = '{{ __('Time') }}';
                        newEl.textContent = isCheckout ? '{{ __('Checkout') }}' : (isStay ? '{{ __('Stay') }}' : '{{ __('No stay') }}');
                        confirmedEl.textContent = isCheckout ? (cell.dataset.calTime || '--') : (isStay ? '{{ __('All day') }}' : '--');
                    } else {
                        labelOneEl.textContent = '{{ __('New bookings') }}';
                        labelTwoEl.textContent = '{{ __('Confirmed') }}';
                        newEl.textContent = cell.dataset.calNew || '0';
                        confirmedEl.textContent = cell.dataset.calConfirmed || '0';
                    }
                    modal.classList.add('is-open');
                });
            });
        })();
    </script>
@endif
