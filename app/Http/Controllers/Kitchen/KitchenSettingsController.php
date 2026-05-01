<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class KitchenSettingsController extends Controller
{
    public function edit(): View
    {
        $this->authorizeKitchenRoleAccess();

        $setting = SystemSetting::current();

        return view('kitchen.settings.edit', [
            'setting' => $setting,
            'availability' => $setting->kitchenServiceAvailability(now()),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorizeKitchenRoleAccess();

        $validated = $request->validate([
            'kitchen_weekday_service_start_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3])$/'],
            'kitchen_weekday_service_start_minute' => ['nullable', 'regex:/^[0-5]\d$/'],
            'kitchen_weekday_service_end_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3])$/'],
            'kitchen_weekday_service_end_minute' => ['nullable', 'regex:/^[0-5]\d$/'],
            'kitchen_weekend_service_start_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3])$/'],
            'kitchen_weekend_service_start_minute' => ['nullable', 'regex:/^[0-5]\d$/'],
            'kitchen_weekend_service_end_hour' => ['nullable', 'regex:/^(?:[01]\d|2[0-3])$/'],
            'kitchen_weekend_service_end_minute' => ['nullable', 'regex:/^[0-5]\d$/'],
            'kitchen_alert_email_list' => ['nullable', 'string', 'max:5000'],
            'kitchen_alert_phone_list' => ['nullable', 'string', 'max:5000'],
        ]);

        $data = [
            'kitchen_weekday_service_start_time' => $this->composeTime(
                $validated,
                'kitchen_weekday_service_start'
            ),
            'kitchen_weekday_service_end_time' => $this->composeTime(
                $validated,
                'kitchen_weekday_service_end'
            ),
            'kitchen_weekend_service_start_time' => $this->composeTime(
                $validated,
                'kitchen_weekend_service_start'
            ),
            'kitchen_weekend_service_end_time' => $this->composeTime(
                $validated,
                'kitchen_weekend_service_end'
            ),
            'kitchen_alert_email_list' => SystemSetting::normalizeRecipientList(
                $validated['kitchen_alert_email_list'] ?? null,
                'email'
            ),
            'kitchen_alert_phone_list' => SystemSetting::normalizeRecipientList(
                $validated['kitchen_alert_phone_list'] ?? null,
                'phone'
            ),
        ];

        $this->validateWindowPair(
            $data['kitchen_weekday_service_start_time'] ?? null,
            $data['kitchen_weekday_service_end_time'] ?? null,
            'kitchen_weekday_service_start_time',
            'kitchen_weekday_service_end_time',
            __('weekday'),
        );

        $this->validateWindowPair(
            $data['kitchen_weekend_service_start_time'] ?? null,
            $data['kitchen_weekend_service_end_time'] ?? null,
            'kitchen_weekend_service_start_time',
            'kitchen_weekend_service_end_time',
            __('weekend'),
        );

        SystemSetting::query()->updateOrCreate(
            ['id' => SystemSetting::current()->id],
            $data
        );

        SystemSetting::forgetCache();

        return redirect()
            ->route('kitchen.settings.edit')
            ->with('status', __('Kitchen service time settings saved.'));
    }

    private function validateWindowPair(
        ?string $start,
        ?string $end,
        string $startField,
        string $endField,
        string $label
    ): void
    {
        $hasStart = filled($start);
        $hasEnd = filled($end);

        if ($hasStart xor $hasEnd) {
            throw ValidationException::withMessages([
                $hasStart ? $endField : $startField => __('Please enter both start and end time for the :label schedule.', ['label' => $label]),
            ]);
        }

        if ($hasStart && $hasEnd && strcmp($start, $end) >= 0) {
            throw ValidationException::withMessages([
                $endField => __('The :label end time must be later than the start time.', ['label' => $label]),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function composeTime(array $validated, string $prefix): ?string
    {
        $hour = $validated[$prefix.'_hour'] ?? null;
        $minute = $validated[$prefix.'_minute'] ?? null;

        $hasAny = $hour !== null || $minute !== null;
        $hasAll = $hour !== null && $minute !== null;

        if (! $hasAny) {
            return null;
        }

        if (! $hasAll) {
            $label = str_contains($prefix, 'weekday') ? __('weekday') : __('weekend');
            $bound = str_ends_with($prefix, 'start') ? __('start') : __('end');

            throw ValidationException::withMessages([
                $prefix.'_hour' => __('Please choose both hour and minute for the :label :bound time.', [
                    'label' => $label,
                    'bound' => $bound,
                ]),
            ]);
        }

        $hour = (int) $hour;
        $minute = (int) $minute;

        return sprintf('%02d:%02d', $hour, $minute);
    }

    private function authorizeKitchenRoleAccess(): void
    {
        $user = auth()->user();

        abort_unless(
            $user
            && (
                $user->role?->slug === Role::KITCHEN_SLUG
                || $user->isSuperAdmin()
                || $user->isManager()
            ),
            403
        );
    }
}
