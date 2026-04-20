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
        bool $removeHeader,
        bool $removeFooter,
        bool $removeHero,
        ?int $headerMediaAssetId = null,
        ?int $footerMediaAssetId = null,
        ?int $heroMediaAssetId = null,
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
}
