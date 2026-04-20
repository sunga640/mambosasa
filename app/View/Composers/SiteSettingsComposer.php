<?php

namespace App\View\Composers;

use App\Models\RoomType;
use App\Models\SystemSetting;
use App\Support\HomeHeroSlides;
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

        $headerNavRooms = RoomType::query()
            ->where('is_active', true)
            ->with(['branch', 'rooms' => fn ($q) => $q->availableForBooking()->limit(1)])
            ->orderBy('name')
            ->limit(200)
            ->get();

        $headerRoomsNavPayload = $headerNavRooms->map(fn (RoomType $r) => [
            'id' => $r->id,
            'name' => $r->name,
            'branch' => $r->branch?->name,
            'thumb' => $r->heroImageUrl() ?? $r->rooms->first()?->cardImageUrl(),
            'url' => route('site.booking', ['type' => $r->id]),
        ])->values()->all();

        $view->with('siteSettings', SystemSetting::current());
        $view->with('siteCartCount', $siteCartCount);
        $view->with('headerNavRooms', $headerNavRooms);
        $view->with('headerRoomsNavPayload', $headerRoomsNavPayload);
        $heroSlides = HomeHeroSlides::displayedUrls();
        if ($heroSlides === []) {
            $heroSlides = [asset('img/hero/8/2.png')];
        }
        $view->with('homeHeroSlideUrls', $heroSlides);
    }
}
