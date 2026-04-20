<?php

namespace App\Models;

use App\Support\PublicDisk;
use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{
    protected $casts = [
        'booking_payment_timeout_minutes' => 'integer',
        'stat_pools_count' => 'integer',
        'stat_restaurants_count' => 'integer',
        'ui_page_heroes' => 'array',
    ];

    protected $fillable = [
        'company_name',
        'address_line',
        'phone',
        'email',
        'copyright_text',
        'logo_header',
        'logo_footer',
        'facebook_url',
        'twitter_url',
        'instagram_url',
        'linkedin_url',
        'hero_home_background',
        'hero_home_image_url',
        'inner_page_hero_image_url',
        'ui_page_heroes',
        'booking_payment_timeout_minutes',
        'booking_checkout_time',
        'booking_checkout_weekend_time',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'mail_from_address',
        'mail_from_name',
        'stat_pools_count',
        'stat_restaurants_count',
        'home_stat_customers_label',
        'home_hero_eyebrow',
        'home_hero_headline_suffix',
        'home_section1_heading',
        'home_section1_body',
        'home_stat_caption_guests',
        'home_stat_caption_rooms',
        'home_stat_caption_pools',
        'home_stat_caption_dining',
        'header_brand_lines',
    ];

    public function bookingCheckoutTime(): string
    {
        $value = trim((string) ($this->booking_checkout_time ?? ''));
        if (preg_match('/^\d{2}:\d{2}$/', $value) !== 1) {
            return '04:00';
        }

        return $value;
    }

    /**
     * Checkout clock time when the checkout calendar day falls on Saturday or Sunday (ISO day 6–7).
     */
    public function bookingCheckoutWeekendTime(): string
    {
        $value = trim((string) ($this->booking_checkout_weekend_time ?? ''));
        if (preg_match('/^\d{2}:\d{2}$/', $value) !== 1) {
            return '04:30';
        }

        return $value;
    }

    /**
     * Effective checkout time for a given checkout date (weekday vs weekend morning).
     */
    public function checkoutTimeForDate(\Carbon\CarbonInterface $checkoutDate): string
    {
        $dow = (int) $checkoutDate->dayOfWeekIso;

        return ($dow >= 6)
            ? $this->bookingCheckoutWeekendTime()
            : $this->bookingCheckoutTime();
    }

    public function headerLogoUrl(): string
    {
        if ($this->logo_header && PublicDisk::exists($this->logo_header)) {
            return PublicDisk::url($this->logo_header);
        }

        return asset('img/general/logo-header-8.svg');
    }

    /**
     * MIME type for the favicon link tag, from the uploaded header logo extension (or SVG default).
     */
    public function faviconMimeType(): string
    {
        if ($this->logo_header && PublicDisk::exists($this->logo_header)) {
            $ext = strtolower(pathinfo($this->logo_header, PATHINFO_EXTENSION));

            return match ($ext) {
                'svg' => 'image/svg+xml',
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'avif' => 'image/avif',
                default => 'image/png',
            };
        }

        return 'image/svg+xml';
    }

    public function hotelDisplayName(): string
    {
        $name = trim((string) ($this->company_name ?? ''));

        return $name !== '' ? $name : (string) config('app.name');
    }

    public function footerLogoUrl(): string
    {
        if ($this->logo_footer && PublicDisk::exists($this->logo_footer)) {
            return PublicDisk::url($this->logo_footer);
        }

        return asset('img/general/logo-white.svg');
    }

    public function heroHomeBackgroundUrl(): ?string
    {
        if ($this->hero_home_background && PublicDisk::exists($this->hero_home_background)) {
            return PublicDisk::url($this->hero_home_background);
        }

        return null;
    }

    public static function safeHttpsImageUrl(?string $url): ?string
    {
        if ($url === null) {
            return null;
        }
        $url = trim($url);
        if ($url === '' || strlen($url) > 2000) {
            return null;
        }
        if (! str_starts_with(strtolower($url), 'https://')) {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }

    /**
     * First home slider image: HTTPS URL (settings) → uploaded hero → config default (no public/img/hotel.*).
     */
    public function resolvedHomeHeroFirstSlide(): string
    {
        if ($u = self::safeHttpsImageUrl($this->hero_home_image_url)) {
            return $u;
        }
        if ($u = $this->heroHomeBackgroundUrl()) {
            return $u;
        }

        return config('site.default_home_hero_first', asset('img/hero/8/1.png'));
    }

    /**
     * Inner pages (about, cart, …): optional global HTTPS hero, else local asset path under public/.
     */
    public function resolvedInnerPageHero(string $fallbackRelativeToPublic = 'img/pageHero/4.png'): string
    {
        if ($u = self::safeHttpsImageUrl($this->inner_page_hero_image_url)) {
            return $u;
        }

        return asset($fallbackRelativeToPublic);
    }

    /**
     * Per-page hero for UI customization (JSON map slug → HTTPS URL or storage path).
     */
    public function resolvedPageHero(string $pageSlug, string $fallbackRelativeToPublic = 'img/pageHero/4.png'): string
    {
        $heroes = $this->ui_page_heroes;
        if (is_array($heroes) && ! empty($heroes[$pageSlug]) && is_string($heroes[$pageSlug])) {
            $raw = trim($heroes[$pageSlug]);
            if ($raw !== '') {
                if (str_starts_with(strtolower($raw), 'https://') && ($u = self::safeHttpsImageUrl($raw))) {
                    return $u;
                }
                if (PublicDisk::exists($raw)) {
                    return PublicDisk::url($raw);
                }
            }
        }

        return $this->resolvedInnerPageHero($fallbackRelativeToPublic);
    }

    public static function current(): self
    {
        return \Illuminate\Support\Facades\Cache::rememberForever('system_settings_singleton', function () {
            $row = self::query()->first();
            if ($row) {
                return $row;
            }

            return self::query()->create([
                'company_name' => 'Hotel & Spa Swiss Resort',
                'address_line' => 'PO Box 16122 Collins Street West, Victoria 8007 Australia',
                'phone' => '+41-96567-7854',
                'email' => 'info@swiss-resort.com',
                'copyright_text' => 'Copyright © '.date('Y').' '.config('app.name'),
                'booking_checkout_time' => '04:00',
                'booking_checkout_weekend_time' => '04:30',
            ]);
        });
    }

    public static function forgetCache(): void
    {
        \Illuminate\Support\Facades\Cache::forget('system_settings_singleton');
    }
}
