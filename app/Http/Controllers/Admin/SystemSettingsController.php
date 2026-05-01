<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSystemSettingsRequest;
use App\Models\MediaAsset;
use App\Services\Admin\SystemSettingsService;
use App\Support\GuestEmailTemplateManager;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SystemSettingsController extends Controller
{
    public function __construct(
        private SystemSettingsService $settings,
        private GuestEmailTemplateManager $guestEmailTemplates,
    ) {}

    public function edit(): View
    {
        $setting = $this->settings->get();

        return view('admin.settings.edit', [
            'setting' => $setting,
            'emailTemplateDefinitions' => $this->guestEmailTemplates->definitions(),
            'emailTemplates' => $this->guestEmailTemplates->all($setting),
            // Keep this page responsive because the same media picker renders multiple times.
            'mediaAssets' => MediaAsset::query()->latest()->get(),
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
        $removeHeroSlideTwo = $request->boolean('remove_hero_slide_two');
        $removeHeroSlideThree = $request->boolean('remove_hero_slide_three');

        $attributes = collect($validated)
            ->except([
                'logo_header',
                'logo_footer',
                'hero_home_background',
                'hero_home_slide_two',
                'hero_home_slide_three',
            ])
            ->map(function ($value) {
                return $value === '' ? null : $value;
            })
            ->all();

        $attributes['email_templates'] = $this->guestEmailTemplates->normalizeForSave(
            (array) $request->input('email_templates', [])
        );

        foreach ([
            'smtp_password',
            'restaurant_api_key',
            'restaurant_api_secret',
            'restaurant_sso_shared_secret',
        ] as $secretField) {
            if (($attributes[$secretField] ?? null) === null) {
                unset($attributes[$secretField]);
            }
        }

        $attributes['restaurant_integration_enabled'] = $request->boolean('restaurant_integration_enabled');

        $this->settings->save(
            $attributes,
            $request->file('logo_header'),
            $request->file('logo_footer'),
            $request->file('hero_home_background'),
            $request->file('hero_home_slide_two'),
            $request->file('hero_home_slide_three'),
            $request->file('auth_guest_image'),
            $removeHeader,
            $removeFooter,
            $removeHero,
            $removeHeroSlideTwo,
            $removeHeroSlideThree,
            $request->integer('logo_header_media_asset_id') ?: null,
            $request->integer('logo_footer_media_asset_id') ?: null,
            $request->integer('hero_home_media_asset_id') ?: null,
            $request->integer('hero_home_slide_two_media_asset_id') ?: null,
            $request->integer('hero_home_slide_three_media_asset_id') ?: null,
            $request->integer('auth_guest_image_media_asset_id') ?: null,
            $request->has('home_hero_gallery_media_asset_ids')
                ? array_values(array_filter(array_map('intval', $request->input('home_hero_gallery_media_asset_ids', []))))
                : null,
            $request->has('home_views_gallery_media_asset_ids')
                ? array_values(array_filter(array_map('intval', $request->input('home_views_gallery_media_asset_ids', []))))
                : null,
            $request->has('about_gallery_media_asset_ids')
                ? array_values(array_filter(array_map('intval', $request->input('about_gallery_media_asset_ids', []))))
                : null,
        );

        return redirect()->route('admin.settings.edit')->with('status', __('Settings saved.'));
    }
}
