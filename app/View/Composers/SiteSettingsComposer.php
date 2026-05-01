<?php

namespace App\View\Composers;

use App\Models\SystemSetting;
use Illuminate\View\View;

class SiteSettingsComposer
{
    public function compose(View $view): void
    {
        $cart = request()->session()->get('site_cart', []);
        $siteCartCount = 0;
        if (is_array($cart)) {
            foreach ($cart as $qty) {
                $siteCartCount += max(1, (int) $qty);
            }
        }

        $settings = SystemSetting::current();
        $view->with('siteSettings', $settings);
        $view->with('siteCartCount', $siteCartCount);
        $heroSlides = $settings->resolvedHomeHeroSlides();
        if ($heroSlides === []) {
            $heroSlides = [asset('img/hero/8/2.png')];
        }
        $view->with('homeHeroSlideUrls', $heroSlides);
    }
}
