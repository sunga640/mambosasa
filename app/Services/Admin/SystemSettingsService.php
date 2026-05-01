<?php

namespace App\Services\Admin;

use App\Models\SystemSetting;
use App\Models\MediaAsset;
use App\Support\PublicDisk;
use App\Support\UploadStorage;
use Illuminate\Http\UploadedFile;

class SystemSettingsService
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function save(
        array $attributes,
        ?UploadedFile $logoHeader,
        ?UploadedFile $logoFooter,
        ?UploadedFile $heroBg,
        ?UploadedFile $heroSlideTwo,
        ?UploadedFile $heroSlideThree,
        ?UploadedFile $authGuestImage,
        bool $removeHeader,
        bool $removeFooter,
        bool $removeHero,
        bool $removeHeroSlideTwo,
        bool $removeHeroSlideThree,
        ?int $headerMediaAssetId = null,
        ?int $footerMediaAssetId = null,
        ?int $heroMediaAssetId = null,
        ?int $heroSlideTwoMediaAssetId = null,
        ?int $heroSlideThreeMediaAssetId = null,
        ?int $authGuestImageMediaAssetId = null,
        ?array $homeHeroGalleryMediaAssetIds = null,
        ?array $homeViewsGalleryMediaAssetIds = null,
        ?array $aboutGalleryMediaAssetIds = null,
    ): SystemSetting {
        $setting = SystemSetting::query()->first();

        if (! $setting) {
            $setting = new SystemSetting;
            $setting->save();
        }

        if ($removeHeader && $setting->logo_header) {
            PublicDisk::delete($setting->logo_header);
            $attributes['logo_header'] = null;
        }

        if ($removeFooter && $setting->logo_footer) {
            PublicDisk::delete($setting->logo_footer);
            $attributes['logo_footer'] = null;
        }

        if ($removeHero && $setting->hero_home_background) {
            PublicDisk::delete($setting->hero_home_background);
            $attributes['hero_home_background'] = null;
        }

        if ($removeHeroSlideTwo && $setting->hero_home_slide_two) {
            PublicDisk::delete($setting->hero_home_slide_two);
            $attributes['hero_home_slide_two'] = null;
        }

        if ($removeHeroSlideThree && $setting->hero_home_slide_three) {
            PublicDisk::delete($setting->hero_home_slide_three);
            $attributes['hero_home_slide_three'] = null;
        }

        if ($logoHeader) {
            $this->deleteIfExists($setting->logo_header);
            $attributes['logo_header'] = UploadStorage::storePublic($logoHeader, 'site');
        } elseif ($headerMediaAssetId) {
            $attributes['logo_header'] = MediaAsset::query()->whereKey($headerMediaAssetId)->value('path');
        }

        if ($logoFooter) {
            $this->deleteIfExists($setting->logo_footer);
            $attributes['logo_footer'] = UploadStorage::storePublic($logoFooter, 'site');
        } elseif ($footerMediaAssetId) {
            $attributes['logo_footer'] = MediaAsset::query()->whereKey($footerMediaAssetId)->value('path');
        }

        if ($heroBg) {
            $this->deleteIfExists($setting->hero_home_background);
            $attributes['hero_home_background'] = UploadStorage::storePublic($heroBg, 'site/hero');
        } elseif ($heroMediaAssetId) {
            $attributes['hero_home_background'] = MediaAsset::query()->whereKey($heroMediaAssetId)->value('path');
        }

        if ($heroSlideTwo) {
            $this->deleteIfExists($setting->hero_home_slide_two);
            $attributes['hero_home_slide_two'] = UploadStorage::storePublic($heroSlideTwo, 'site/hero');
        } elseif ($heroSlideTwoMediaAssetId) {
            $attributes['hero_home_slide_two'] = MediaAsset::query()->whereKey($heroSlideTwoMediaAssetId)->value('path');
        }

        if ($heroSlideThree) {
            $this->deleteIfExists($setting->hero_home_slide_three);
            $attributes['hero_home_slide_three'] = UploadStorage::storePublic($heroSlideThree, 'site/hero');
        } elseif ($heroSlideThreeMediaAssetId) {
            $attributes['hero_home_slide_three'] = MediaAsset::query()->whereKey($heroSlideThreeMediaAssetId)->value('path');
        }

        if ($authGuestImage) {
            $this->deleteIfExists($setting->auth_guest_image_path);
            $attributes['auth_guest_image_path'] = UploadStorage::storePublic($authGuestImage, 'site/auth');
        } elseif ($authGuestImageMediaAssetId) {
            $attributes['auth_guest_image_path'] = MediaAsset::query()->whereKey($authGuestImageMediaAssetId)->value('path');
        }

        if ($homeHeroGalleryMediaAssetIds !== null) {
            $attributes['home_hero_gallery_paths'] = $this->resolveMediaAssetPaths($homeHeroGalleryMediaAssetIds);
        }

        if ($homeViewsGalleryMediaAssetIds !== null) {
            $attributes['home_views_gallery_paths'] = $this->resolveMediaAssetPaths($homeViewsGalleryMediaAssetIds);
        }

        if ($aboutGalleryMediaAssetIds !== null) {
            $attributes['about_gallery_paths'] = $this->resolveMediaAssetPaths($aboutGalleryMediaAssetIds);
        }

        $setting->update($attributes);
        SystemSetting::forgetCache();

        return SystemSetting::current();
    }

    public function get(): SystemSetting
    {
        return SystemSetting::current();
    }

    private function deleteIfExists(?string $path): void
    {
        PublicDisk::delete($path);
    }

    /**
     * @param  list<int>  $mediaAssetIds
     * @return list<string>
     */
    private function resolveMediaAssetPaths(array $mediaAssetIds): array
    {
        $pathsById = MediaAsset::query()
            ->whereIn('id', $mediaAssetIds)
            ->pluck('path', 'id');

        $paths = [];
        foreach ($mediaAssetIds as $mediaAssetId) {
            $path = $pathsById[(int) $mediaAssetId] ?? null;
            if (is_string($path) && trim($path) !== '') {
                $paths[] = $path;
            }
        }

        return array_values(array_unique($paths));
    }
}
