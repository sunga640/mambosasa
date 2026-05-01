@extends('layouts.kitchen')

@section('title', __('Service Settings'))

@push('styles')
    <style>
        .kitchen-settings-shell {
            display: grid;
            gap: 1.2rem;
        }

        .k-settings-hero {
            border: 1px solid rgba(125, 211, 252, .18);
            background:
                radial-gradient(circle at top right, rgba(56, 189, 248, .12), transparent 28%),
                linear-gradient(135deg, rgba(15, 23, 42, .96), rgba(30, 41, 59, .94));
            border-radius: 22px;
            padding: 1.35rem 1.4rem;
            display: grid;
            gap: 1rem;
        }

        .k-settings-hero__top {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: start;
            flex-wrap: wrap;
        }

        .k-settings-kicker {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            width: fit-content;
            padding: .42rem .78rem;
            border-radius: 999px;
            border: 1px solid rgba(125, 211, 252, .22);
            background: rgba(56, 189, 248, .12);
            color: #dbeafe;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .k-settings-kicker::before {
            content: "";
            width: .55rem;
            height: .55rem;
            border-radius: 999px;
            background: #38bdf8;
            box-shadow: 0 0 0 5px rgba(56, 189, 248, .12);
        }

        .k-settings-hero h2 {
            margin: .8rem 0 .35rem;
            font-size: clamp(1.45rem, 2vw, 2rem);
            line-height: 1;
        }

        .k-settings-hero p {
            margin: 0;
            color: #bfd3ea;
            max-width: 52rem;
            line-height: 1.75;
        }

        .k-settings-status {
            min-width: 220px;
            padding: 1rem 1.05rem;
            border-radius: 18px;
            border: 1px solid rgba(125, 211, 252, .18);
            background: rgba(15, 23, 42, .54);
        }

        .k-settings-status small {
            display: block;
            color: #93c5fd;
            font-size: .72rem;
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: .4rem;
        }

        .k-settings-status strong {
            display: block;
            font-size: 1.5rem;
            color: #f8fafc;
        }

        .k-settings-status span {
            display: block;
            margin-top: .45rem;
            color: #cbd5e1;
            line-height: 1.6;
        }

        .k-settings-form {
            display: grid;
            gap: 1.2rem;
        }

        .k-settings-panel {
            border: 1px solid var(--brand-theme-border);
            background: linear-gradient(180deg, rgba(255,255,255,.02), rgba(255,255,255,.01));
            border-radius: 22px;
            padding: 1.15rem;
            display: grid;
            gap: 1rem;
        }

        .k-settings-panel__head {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: end;
            flex-wrap: wrap;
        }

        .k-settings-panel__head h3 {
            margin: 0;
            font-size: 1.18rem;
        }

        .k-settings-panel__head p {
            margin: .28rem 0 0;
            color: var(--brand-theme-muted);
            line-height: 1.7;
            max-width: 42rem;
        }

        .k-settings-hours {
            display: grid;
            gap: .85rem;
        }

        .k-hours-row {
            display: grid;
            grid-template-columns: minmax(180px, 220px) minmax(0, 1fr) minmax(0, 1fr);
            gap: .85rem;
            align-items: center;
            padding: .95rem 1rem;
            border-radius: 18px;
            border: 1px solid rgba(125, 211, 252, .12);
            background: rgba(15, 23, 42, .34);
        }

        .k-hours-row__label strong {
            display: block;
            font-size: 1rem;
            color: #e2e8f0;
        }

        .k-hours-row__label span {
            display: block;
            margin-top: .18rem;
            color: var(--brand-theme-muted);
            font-size: .86rem;
        }

        .k-time-block {
            display: grid;
            gap: .42rem;
        }

        .k-time-block label {
            color: #cfe7fb;
            font-size: .75rem !important;
            letter-spacing: .12em;
            text-transform: uppercase;
        }

        .k-time-selects {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(0, 1fr);
            gap: .6rem;
        }

        .k-time-selects select,
        .k-field textarea {
            background: #111827 !important;
            color: #f8fafc !important;
        }

        .k-time-selects select option {
            background: #111827;
            color: #f8fafc;
        }

        .k-settings-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: .9rem;
        }

        .k-settings-note {
            color: var(--brand-theme-muted);
            font-size: .9rem;
            line-height: 1.7;
        }

        .k-settings-chip {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            width: fit-content;
            padding: .45rem .7rem;
            border-radius: 999px;
            background: rgba(34, 197, 94, .1);
            color: #bbf7d0;
            border: 1px solid rgba(34, 197, 94, .16);
            font-size: .76rem;
            font-weight: 700;
        }

        .k-settings-chip--muted {
            background: rgba(148, 163, 184, .1);
            color: #cbd5e1;
            border-color: rgba(148, 163, 184, .16);
        }

        .k-settings-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            align-items: center;
            flex-wrap: wrap;
        }

        @media (max-width: 991px) {
            .k-hours-row {
                grid-template-columns: 1fr;
            }

            .k-settings-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 767px) {
            .k-time-selects {
                grid-template-columns: 1fr;
            }
        }
    </style>
@endpush

@section('content')
    @php
        $timeParts = function (?string $time): array {
            if (! $time || ! preg_match('/^\d{2}:\d{2}$/', $time)) {
                return ['hour' => null, 'minute' => null];
            }

            [$hour, $minute] = array_map('intval', explode(':', $time));

            return [
                'hour' => sprintf('%02d', $hour),
                'minute' => sprintf('%02d', $minute),
            ];
        };

        $hours = array_map(fn ($hour) => sprintf('%02d', $hour), range(0, 23));
        $minutes = array_map(fn ($minute) => sprintf('%02d', $minute), range(0, 59));

        $weekdayStart = $timeParts($setting->kitchenWeekdayServiceStartTime());
        $weekdayEnd = $timeParts($setting->kitchenWeekdayServiceEndTime());
        $weekendStart = $timeParts($setting->kitchenWeekendServiceStartTime());
        $weekendEnd = $timeParts($setting->kitchenWeekendServiceEndTime());

        $renderPrettyTime = function (?string $time): string {
            return $time ?: __('Not set');
        };

        $recipientEmailsText = old('kitchen_alert_email_list', implode(PHP_EOL, $setting->kitchenAlertEmails()));
        $recipientPhonesText = old('kitchen_alert_phone_list', implode(PHP_EOL, $setting->kitchenAlertPhones()));
    @endphp

    <div class="kitchen-settings-shell">
        <section class="k-settings-hero">
            <div class="k-settings-hero__top">
                <div>
                    <span class="k-settings-kicker">{{ __('Kitchen control') }}</span>
                    <h2>{{ __('Service availability') }}</h2>
                    <p>{{ __('Keep the kitchen schedule easy to manage. Set weekday and weekend hours in 24-hour format, then choose who should receive new-order alerts by email and phone.') }}</p>
                </div>
                <div class="k-settings-status">
                    <small>{{ __('Status now') }}</small>
                    <strong>{{ ($availability['is_available'] ?? true) ? __('Open') : __('Closed') }}</strong>
                    <span>
                        @if (! empty($availability['message']))
                            {{ $availability['message'] }}
                        @elseif (! ($availability['is_configured'] ?? false))
                            {{ __('Service hours are not limited yet.') }}
                        @else
                            {{ __('Guests can place kitchen orders right now.') }}
                        @endif
                    </span>
                </div>
            </div>
        </section>

        <form method="POST" action="{{ route('kitchen.settings.update') }}" class="k-settings-form">
            @csrf
            @method('PUT')

            <section class="k-settings-panel">
                <div class="k-settings-panel__head">
                    <div>
                        <h3>{{ __('Working hours') }}</h3>
                        <p>{{ __('Use one compact row for weekdays and one for weekends. Time uses 24-hour format, and guests scanning the room QR outside these hours will see the menu as unavailable.') }}</p>
                    </div>
                    <div class="k-settings-chip {{ ($availability['is_configured'] ?? false) ? '' : 'k-settings-chip--muted' }}">
                        {{ ($availability['is_configured'] ?? false) ? __('Schedule active') : __('No restriction yet') }}
                    </div>
                </div>

                <div class="k-settings-hours">
                    <div class="k-hours-row">
                        <div class="k-hours-row__label">
                            <strong>{{ __('Weekdays') }}</strong>
                            <span>{{ __('Monday to Friday') }} · {{ $renderPrettyTime($setting->kitchenWeekdayServiceStartTime()) }} - {{ $renderPrettyTime($setting->kitchenWeekdayServiceEndTime()) }}</span>
                        </div>
                        <div class="k-time-block">
                            <label>{{ __('Start') }}</label>
                            <div class="k-time-selects">
                                <select name="kitchen_weekday_service_start_hour">
                                    <option value="">{{ __('Hour') }}</option>
                                    @foreach ($hours as $hour)
                                        <option value="{{ $hour }}" @selected(old('kitchen_weekday_service_start_hour', $weekdayStart['hour']) === $hour)>{{ $hour }}</option>
                                    @endforeach
                                </select>
                                <select name="kitchen_weekday_service_start_minute">
                                    <option value="">{{ __('Minute') }}</option>
                                    @foreach ($minutes as $minute)
                                        <option value="{{ $minute }}" @selected(old('kitchen_weekday_service_start_minute', $weekdayStart['minute']) === $minute)>{{ $minute }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kitchen_weekday_service_start_hour')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                            @error('kitchen_weekday_service_start_time')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>
                        <div class="k-time-block">
                            <label>{{ __('End') }}</label>
                            <div class="k-time-selects">
                                <select name="kitchen_weekday_service_end_hour">
                                    <option value="">{{ __('Hour') }}</option>
                                    @foreach ($hours as $hour)
                                        <option value="{{ $hour }}" @selected(old('kitchen_weekday_service_end_hour', $weekdayEnd['hour']) === $hour)>{{ $hour }}</option>
                                    @endforeach
                                </select>
                                <select name="kitchen_weekday_service_end_minute">
                                    <option value="">{{ __('Minute') }}</option>
                                    @foreach ($minutes as $minute)
                                        <option value="{{ $minute }}" @selected(old('kitchen_weekday_service_end_minute', $weekdayEnd['minute']) === $minute)>{{ $minute }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kitchen_weekday_service_end_hour')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                            @error('kitchen_weekday_service_end_time')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <div class="k-hours-row">
                        <div class="k-hours-row__label">
                            <strong>{{ __('Weekends') }}</strong>
                            <span>{{ __('Saturday to Sunday') }} · {{ $renderPrettyTime($setting->kitchenWeekendServiceStartTime()) }} - {{ $renderPrettyTime($setting->kitchenWeekendServiceEndTime()) }}</span>
                        </div>
                        <div class="k-time-block">
                            <label>{{ __('Start') }}</label>
                            <div class="k-time-selects">
                                <select name="kitchen_weekend_service_start_hour">
                                    <option value="">{{ __('Hour') }}</option>
                                    @foreach ($hours as $hour)
                                        <option value="{{ $hour }}" @selected(old('kitchen_weekend_service_start_hour', $weekendStart['hour']) === $hour)>{{ $hour }}</option>
                                    @endforeach
                                </select>
                                <select name="kitchen_weekend_service_start_minute">
                                    <option value="">{{ __('Minute') }}</option>
                                    @foreach ($minutes as $minute)
                                        <option value="{{ $minute }}" @selected(old('kitchen_weekend_service_start_minute', $weekendStart['minute']) === $minute)>{{ $minute }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kitchen_weekend_service_start_hour')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                            @error('kitchen_weekend_service_start_time')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>
                        <div class="k-time-block">
                            <label>{{ __('End') }}</label>
                            <div class="k-time-selects">
                                <select name="kitchen_weekend_service_end_hour">
                                    <option value="">{{ __('Hour') }}</option>
                                    @foreach ($hours as $hour)
                                        <option value="{{ $hour }}" @selected(old('kitchen_weekend_service_end_hour', $weekendEnd['hour']) === $hour)>{{ $hour }}</option>
                                    @endforeach
                                </select>
                                <select name="kitchen_weekend_service_end_minute">
                                    <option value="">{{ __('Minute') }}</option>
                                    @foreach ($minutes as $minute)
                                        <option value="{{ $minute }}" @selected(old('kitchen_weekend_service_end_minute', $weekendEnd['minute']) === $minute)>{{ $minute }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('kitchen_weekend_service_end_hour')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                            @error('kitchen_weekend_service_end_time')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                        </div>
                    </div>
                </div>

                <div class="k-settings-note">{{ __('Leave a schedule row blank only if you want that day group to stay unrestricted for now.') }}</div>
            </section>

            <section class="k-settings-panel">
                <div class="k-settings-panel__head">
                    <div>
                        <h3>{{ __('Order alerts') }}</h3>
                        <p>{{ __('When a new kitchen order is placed, the system will keep dashboard alerts for kitchen users and also send notifications to the contacts listed here.') }}</p>
                    </div>
                    <div class="k-settings-chip {{ ($setting->kitchenAlertEmails() !== [] || $setting->kitchenAlertPhones() !== []) ? '' : 'k-settings-chip--muted' }}">
                        {{ __('Advanced recipients') }}
                    </div>
                </div>

                <div class="k-settings-grid">
                    <div class="k-field">
                        <label for="kitchen_alert_email_list">{{ __('Alert emails') }}</label>
                        <textarea id="kitchen_alert_email_list" name="kitchen_alert_email_list" rows="8" placeholder="manager@example.com&#10;kitchen@example.com">{{ $recipientEmailsText }}</textarea>
                        <div class="k-settings-note">{{ __('Add one email per line. Each new order will send an email alert to every valid address listed here using the configured Laravel mailer.') }}</div>
                        @error('kitchen_alert_email_list')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                    </div>

                    <div class="k-field">
                        <label for="kitchen_alert_phone_list">{{ __('Alert phone numbers') }}</label>
                        <textarea id="kitchen_alert_phone_list" name="kitchen_alert_phone_list" rows="8" placeholder="255712345678&#10;0712345678">{{ $recipientPhonesText }}</textarea>
                        <div class="k-settings-note">{{ __('Add one phone number per line. The system sends SMS alerts through the configured SMS driver. If the driver is only log mode, the message is recorded in logs instead of reaching the phone.') }}</div>
                        @error('kitchen_alert_phone_list')<div class="k-muted" style="color:#fca5a5;">{{ $message }}</div>@enderror
                    </div>
                </div>
            </section>

            <div class="k-settings-actions">
                <div class="k-settings-note">{{ __('Saved contacts will receive new-order alerts in addition to the normal kitchen dashboard notifications.') }}</div>
                <button type="submit" class="dash-btn dash-btn--primary">{{ __('Save kitchen settings') }}</button>
            </div>
        </form>
    </div>
@endsection
