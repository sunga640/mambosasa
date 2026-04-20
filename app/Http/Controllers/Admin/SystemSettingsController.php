<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Models\MediaAsset;
use App\Services\Admin\SystemSettingsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SystemSettingsController extends Controller
{
    public function __construct(
        private SystemSettingsService $settings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'setting' => $this->settings->get(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function update(UpdateSystemSettingsRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->has('ui_page_heroes_json')) {
            $raw = $request->string('ui_page_heroes_json')->toString();
            $decoded = trim($raw) === '' ? null : json_decode($raw, true);
            $validated['ui_page_heroes'] = is_array($decoded) ? $decoded : null;
        }
        unset($validated['ui_page_heroes_json']);

        $removeHeader = $request->boolean('remove_logo_header');
        $removeFooter = $request->boolean('remove_logo_footer');
        $removeHero = $request->boolean('remove_hero_background');

        $attributes = collect($validated)
            ->except([
                'logo_header',
                'logo_footer',
                'hero_home_background',
            ])
            ->map(function ($value) {
                return $value === '' ? null : $value;
            })
            ->all();

        // Keep current SMTP password unless a new one is explicitly entered.
        if (($attributes['smtp_password'] ?? null) === null) {
            unset($attributes['smtp_password']);
        }

        $this->settings->save(
            $attributes,
            $request->file('logo_header'),
            $request->file('logo_footer'),
            $request->file('hero_home_background'),
            $removeHeader,
            $removeFooter,
            $removeHero,
            $request->integer('logo_header_media_asset_id') ?: null,
            $request->integer('logo_footer_media_asset_id') ?: null,
            $request->integer('hero_home_media_asset_id') ?: null,
        );

        return redirect()->route('admin.settings.edit')->with('status', __('Settings saved.'));
    }
}
