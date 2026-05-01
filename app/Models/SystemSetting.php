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
        'restaurant_integration_enabled' => 'boolean',
        'restaurant_api_key' => 'encrypted',
        'restaurant_api_secret' => 'encrypted',
        'restaurant_sso_shared_secret' => 'encrypted',
        'restaurant_api_timeout_seconds' => 'integer',
        'restaurant_token_ttl_minutes' => 'integer',
        'ui_page_heroes' => 'array',
        'home_hero_gallery_paths' => 'array',
        'home_views_gallery_paths' => 'array',
        'about_gallery_paths' => 'array',
        'email_templates' => 'array',
        'kitchen_alert_email_list' => 'array',
        'kitchen_alert_phone_list' => 'array',
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
        'hero_home_slide_two',
        'hero_home_slide_three',
        'home_hero_gallery_paths',
        'home_views_gallery_paths',
        'about_gallery_paths',
        'auth_guest_image_path',
        'hero_home_image_url',
        'inner_page_hero_image_url',
        'ui_page_heroes',
        'booking_payment_timeout_minutes',
        'booking_checkout_time',
        'booking_checkout_weekend_time',
        'kitchen_weekday_service_start_time',
        'kitchen_weekday_service_end_time',
        'kitchen_weekend_service_start_time',
        'kitchen_weekend_service_end_time',
        'kitchen_alert_email_list',
        'kitchen_alert_phone_list',
        'smtp_host',
        'smtp_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryption',
        'mail_from_address',
        'mail_from_name',
        'email_templates',
        'dashboard_theme_mode',
        'restaurant_integration_enabled',
        'restaurant_api_base_url',
        'restaurant_api_key',
        'restaurant_api_secret',
        'restaurant_sso_shared_secret',
        'restaurant_sso_entry_path',
        'restaurant_api_timeout_seconds',
        'restaurant_token_ttl_minutes',
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

    public function restaurantApiTimeoutSeconds(): int
    {
        return max(5, (int) ($this->restaurant_api_timeout_seconds ?? 15));
    }

    public function restaurantTokenTtlMinutes(): int
    {
        return max(1, (int) ($this->restaurant_token_ttl_minutes ?? 5));
    }

    public function restaurantSsoEntryPath(): string
    {
        $value = trim((string) ($this->restaurant_sso_entry_path ?? 'restaurant/sso/hotel'));

        return $value !== '' ? ltrim($value, '/') : 'restaurant/sso/hotel';
    }

    public function restaurantIntegrationConfigured(): bool
    {
        return (bool) $this->restaurant_integration_enabled
            && filled($this->restaurant_api_base_url)
            && filled($this->restaurant_sso_shared_secret);
    }

    public function restaurantEntryUrl(): ?string
    {
        $base = trim((string) ($this->restaurant_api_base_url ?? ''));
        if ($base === '' || ! filter_var($base, FILTER_VALIDATE_URL)) {
            return null;
        }

        return rtrim($base, '/').'/'.$this->restaurantSsoEntryPath();
    }

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

    /**
     * @return list<string>
     */
    public function headerBrandLines(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) ($this->header_brand_lines ?? '')) ?: [];
        $lines = array_values(array_filter(array_map(
            static fn (string $line): string => trim($line),
            $lines
        )));

        if ($lines === []) {
            return [$this->hotelDisplayName()];
        }

        return array_slice($lines, 0, 3);
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

    public function heroHomeSlideTwoUrl(): ?string
    {
        if ($this->hero_home_slide_two && PublicDisk::exists($this->hero_home_slide_two)) {
            return PublicDisk::url($this->hero_home_slide_two);
        }

        return null;
    }

    public function heroHomeSlideThreeUrl(): ?string
    {
        if ($this->hero_home_slide_three && PublicDisk::exists($this->hero_home_slide_three)) {
            return PublicDisk::url($this->hero_home_slide_three);
        }

        return null;
    }

    /**
     * @return list<string>
     */
    public function homeHeroGalleryUrls(): array
    {
        return $this->resolveStoredGallery($this->home_hero_gallery_paths);
    }

    /**
     * @return list<string>
     */
    public function homeViewsGalleryUrls(): array
    {
        return $this->resolveStoredGallery($this->home_views_gallery_paths);
    }

    /**
     * @return list<string>
     */
    public function aboutGalleryUrls(): array
    {
        return $this->resolveStoredGallery($this->about_gallery_paths);
    }

    public function authGuestImageUrl(): string
    {
        $resolved = $this->resolveStoredGallery([$this->auth_guest_image_path]);

        return $resolved[0] ?? asset('img/mambosasa/herologin.png');
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
     * @return list<string>
     */
    public function resolvedHomeHeroSlides(): array
    {
        $gallerySlides = $this->homeHeroGalleryUrls();
        if ($gallerySlides !== []) {
            return $gallerySlides;
        }

        $slides = array_values(array_filter([
            $this->resolvedHomeHeroFirstSlide(),
            $this->heroHomeSlideTwoUrl(),
            $this->heroHomeSlideThreeUrl(),
        ]));

        if ($slides !== []) {
            return array_values(array_unique($slides));
        }

        $fallbacks = (array) config('site.default_home_hero_slides', []);
        $resolved = [];
        foreach ($fallbacks as $fallback) {
            $fallback = trim((string) $fallback);
            if ($fallback === '') {
                continue;
            }
            $resolved[] = str_starts_with($fallback, 'http') ? $fallback : asset(ltrim($fallback, '/'));
        }

        return $resolved !== [] ? array_values(array_unique($resolved)) : [asset('img/hero/8/2.png')];
    }

    /**
     * @param  mixed  $paths
     * @return list<string>
     */
    private function resolveStoredGallery(mixed $paths): array
    {
        $resolved = [];

        foreach (array_values(array_filter((array) $paths)) as $path) {
            $path = trim((string) $path);
            if ($path === '') {
                continue;
            }

            if (str_starts_with(strtolower($path), 'http')) {
                $resolved[] = $path;
                continue;
            }

            if (PublicDisk::exists($path)) {
                $resolved[] = PublicDisk::url($path);
                continue;
            }

            if (is_file(public_path('storage/'.$path))) {
                $resolved[] = asset('storage/'.$path);
                continue;
            }

            $resolved[] = asset(ltrim($path, '/'));
        }

        return array_values(array_unique($resolved));
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

    public function kitchenWeekdayServiceStartTime(): ?string
    {
        return $this->normalizeClockValue($this->kitchen_weekday_service_start_time);
    }

    public function kitchenWeekdayServiceEndTime(): ?string
    {
        return $this->normalizeClockValue($this->kitchen_weekday_service_end_time);
    }

    public function kitchenWeekendServiceStartTime(): ?string
    {
        return $this->normalizeClockValue($this->kitchen_weekend_service_start_time);
    }

    public function kitchenWeekendServiceEndTime(): ?string
    {
        return $this->normalizeClockValue($this->kitchen_weekend_service_end_time);
    }

    public function kitchenServiceWindowForDate(\Carbon\CarbonInterface $date): ?array
    {
        $isWeekend = (int) $date->dayOfWeekIso >= 6;
        $start = $isWeekend ? $this->kitchenWeekendServiceStartTime() : $this->kitchenWeekdayServiceStartTime();
        $end = $isWeekend ? $this->kitchenWeekendServiceEndTime() : $this->kitchenWeekdayServiceEndTime();

        if ($start === null || $end === null) {
            return null;
        }

        return [
            'schedule_key' => $isWeekend ? 'weekend' : 'weekday',
            'schedule_label' => $isWeekend ? __('Weekend (Sat-Sun)') : __('Weekday (Mon-Fri)'),
            'start_time' => $start,
            'end_time' => $end,
        ];
    }

    public function kitchenServiceAvailability(\Carbon\CarbonInterface $moment): array
    {
        $localMoment = $moment->copy()->setTimezone($this->kitchenServiceTimezone());
        $window = $this->kitchenServiceWindowForDate($localMoment);

        if ($window === null) {
            return [
                'is_configured' => false,
                'is_available' => true,
                'start_at' => null,
                'end_at' => null,
                'next_start_at' => null,
                'message' => null,
                'schedule_label' => null,
            ];
        }

        $startAt = $localMoment->copy()->setTimeFromTimeString($window['start_time']);
        $endAt = $localMoment->copy()->setTimeFromTimeString($window['end_time']);
        $isAvailable = $startAt->lte($localMoment) && $localMoment->lt($endAt);
        $nextStartAt = $isAvailable ? null : $this->nextKitchenServiceStartAt($localMoment, $startAt, $endAt);

        return [
            'is_configured' => true,
            'is_available' => $isAvailable,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'next_start_at' => $nextStartAt,
            'message' => $isAvailable
                ? null
                : __('Service is not available now. Wait up to :time.', [
                    'time' => $nextStartAt?->translatedFormat('l H:i') ?? $startAt->translatedFormat('l H:i'),
                ]),
            'schedule_label' => $window['schedule_label'],
        ];
    }

    private function nextKitchenServiceStartAt(
        \Carbon\CarbonInterface $moment,
        \Carbon\CarbonInterface $todayStartAt,
        \Carbon\CarbonInterface $todayEndAt,
    ): ?\Carbon\CarbonInterface {
        if ($moment->lt($todayStartAt)) {
            return $todayStartAt;
        }

        if ($moment->gte($todayEndAt)) {
            for ($offset = 1; $offset <= 7; $offset++) {
                $candidateDate = $moment->copy()->addDays($offset);
                $candidateWindow = $this->kitchenServiceWindowForDate($candidateDate);
                if ($candidateWindow === null) {
                    continue;
                }

                return $candidateDate->copy()->setTimeFromTimeString($candidateWindow['start_time']);
            }
        }

        return null;
    }

    private function kitchenServiceTimezone(): string
    {
        return config('hotel.operating_timezone', env('HOTEL_OPERATING_TIMEZONE', 'Africa/Nairobi'));
    }

    /**
     * @return list<string>
     */
    public function kitchenAlertEmails(): array
    {
        return $this->normalizeRecipientList($this->kitchen_alert_email_list, 'email');
    }

    /**
     * @return list<string>
     */
    public function kitchenAlertPhones(): array
    {
        return $this->normalizeRecipientList($this->kitchen_alert_phone_list, 'phone');
    }

    /**
     * @param  array<int, string>|string|null  $value
     * @return list<string>
     */
    public static function normalizeRecipientList(array|string|null $value, string $type): array
    {
        $items = is_array($value)
            ? $value
            : (preg_split('/[\r\n,;]+/', (string) ($value ?? '')) ?: []);

        $normalized = [];

        foreach ($items as $item) {
            $item = trim((string) $item);
            if ($item === '') {
                continue;
            }

            if ($type === 'email') {
                $email = mb_strtolower($item);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $normalized[] = $email;
                }
                continue;
            }

            $digits = preg_replace('/[^0-9+]/', '', $item);
            if ($digits !== '') {
                $normalized[] = $digits;
            }
        }

        return array_values(array_unique($normalized));
    }

    private function normalizeClockValue(mixed $value): ?string
    {
        $value = trim((string) ($value ?? ''));

        return preg_match('/^\d{2}:\d{2}$/', $value) === 1 ? $value : null;
    }
}
