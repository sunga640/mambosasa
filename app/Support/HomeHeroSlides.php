<?php

namespace App\Support;

use App\Models\SystemSetting;

final class HomeHeroSlides
{
    private static function resolveSlide(string $value): string
    {
        $v = trim($value);
        if ($v === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $v) === 1) {
            return $v;
        }

        return asset(ltrim($v, '/'));
    }

    /**
     * Same URLs as the home hero after the first slide is dropped (matches {@see resources/views/site/home.blade.php}).
     *
     * @return list<string>
     */
    public static function displayedUrls(): array
    {
        $settingSlides = SystemSetting::current()->resolvedHomeHeroSlides();
        if ($settingSlides !== []) {
            return array_values(array_unique(array_map(fn (string $slide) => self::resolveSlide($slide), $settingSlides)));
        }

        $extraSlides = config('site.default_home_hero_slides', []);

        return array_values(array_unique(array_filter(array_map(
            fn ($slide) => self::resolveSlide((string) $slide),
            $extraSlides
        ))));
    }

    /**
     * 1-based slide index into {@see displayedUrls()} (wraps if there are fewer slides).
     */
    public static function urlForSlideNumber(int $number): string
    {
        $slides = self::displayedUrls();
        if ($slides === []) {
            return asset('img/pageHero/4.png');
        }

        $idx = max(0, $number - 1);
        $n = count($slides);

        return $slides[$idx % $n];
    }
}
