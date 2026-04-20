<?php

namespace App\Support;

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
        $extraSlides = config('site.default_home_hero_slides', []);
        $heroSlideUrls = array_values(array_unique(array_filter([
            self::resolveSlide((string) ($extraSlides[0] ?? 'img/mambosasa/hr1.jpg')),
            self::resolveSlide((string) ($extraSlides[1] ?? 'img/mambosasa/hr1.jpg')),
            self::resolveSlide((string) ($extraSlides[2] ?? 'img/mambosasa/hr2.webp')),
            self::resolveSlide((string) ($extraSlides[3] ?? 'img/mambosasa/hr2.webp')),
        ])));
        // if (count($heroSlideUrls) > 1) {
        //     array_shift($heroSlideUrls);
        // }

        return $heroSlideUrls;
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
