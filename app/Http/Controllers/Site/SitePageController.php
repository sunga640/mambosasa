<?php

namespace App\Http\Controllers\Site;

use App\Enums\BookingStatus;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Models\RoomRank;
use App\Models\HotelService;
use App\Models\RoomType;
use App\Models\SystemSetting;
use App\Models\User;
use App\Support\HomeHeroSlides;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class SitePageController extends Controller
{
    public function home(Request $request): View
    {
        Artisan::call('bookings:expire-pending');

        $homeRoomTypes = RoomType::query()
            ->where('is_active', true)
            ->with(['branch', 'rooms' => fn ($q) => $q->listedOnPublicSite()->with('images')->orderBy('room_number')])
            ->orderByDesc('updated_at')
            ->paginate(7)
            ->withQueryString();

        return view('site.home', [
            'stats' => $this->buildStats(),
            'homeRoomTypes' => $homeRoomTypes,
            'homeHotelServices' => HotelService::query()
                ->listedForGuests()
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(6)
                ->get(),
        ]);
    }

    public function show(Request $request, string $slug): View
    {
        $view = 'site.'.$slug;

        if (! view()->exists($view)) {
            abort(404);
        }

        $setting = SystemSetting::current();
        $slideHero = match ($slug) {
            'about' => HomeHeroSlides::urlForSlideNumber(2),
            'contact' => HomeHeroSlides::urlForSlideNumber(1),
            'terms' => HomeHeroSlides::urlForSlideNumber(4),
            'pricing' => HomeHeroSlides::urlForSlideNumber(5),
            'faq' => HomeHeroSlides::urlForSlideNumber(6),
            default => null,
        };
        $data = [
            'heroUrl' => $slideHero ?? $setting->resolvedPageHero($slug, $this->defaultHeroAssetForSlug($slug)),
        ];
        if (in_array($slug, ['about', 'contact'], true)) {
            Artisan::call('bookings:expire-pending');
            $data['siteSettings'] = $setting;
        }
        if ($slug === 'about') {
            $data['stats'] = $this->buildStats();
            $data['aboutHotelServices'] = HotelService::query()
                ->listedForGuests()
                ->with('branch')
                ->orderBy('sort_order')
                ->orderBy('name')
                ->limit(12)
                ->get();
        }

        if ($slug === 'pricing') {
            $ranks = RoomRank::query()->where('is_active', true)->orderBy('sort_order')->get();
            $roomsByRankId = [];
            foreach ($ranks as $rank) {
                $roomsByRankId[$rank->id] = Room::query()
                    ->where('room_rank_id', $rank->id)
                    ->listedOnPublicSite()
                    ->with(['branch', 'rank'])
                    ->orderBy('name')
                    ->get();
            }
            $data['roomRanks'] = $ranks;
            $data['roomsByRankId'] = $roomsByRankId;
            $data['unrankedRooms'] = Room::query()
                ->whereNull('room_rank_id')
                ->listedOnPublicSite()
                ->with(['branch', 'rank'])
                ->orderBy('name')
                ->get();
        }

        return view($view, $data);
    }

    private function defaultHeroAssetForSlug(string $slug): string
    {
        return match ($slug) {
            'about' => 'img/pageHero/4.png',
            'contact' => 'img/pageHero/5.png',
            'terms' => 'img/pageHero/6.png',
            'pricing' => 'img/pageHero/7.png',
            'faq' => 'img/pageHero/8/1.png',
            'checkout', 'booking', 'cart' => 'img/pageHero/1.png',
            default => 'img/pageHero/4.png',
        };
    }

    /**
     * @return array{customers_display: string, rooms_count: int, pools_count: string, restaurants_count: string, caption_guests: ?string, caption_rooms: ?string, caption_pools: ?string, caption_dining: ?string}
     */
    private function buildStats(): array
    {
        $setting = SystemSetting::current();

        $confirmedGuests = (int) Booking::query()
            ->where('status', BookingStatus::Confirmed)
            ->distinct()
            ->count('email');

        $happyCount = $confirmedGuests > 0 ? $confirmedGuests : User::query()->count();

        return [
            'customers_display' => $setting->home_stat_customers_label ?: (string) $happyCount,
            'rooms_count' => Room::query()->listedOnPublicSite()->count(),
            'pools_count' => (string) ($setting->stat_pools_count ?? 14),
            'restaurants_count' => (string) ($setting->stat_restaurants_count ?? 17),
            'caption_guests' => $setting->home_stat_caption_guests,
            'caption_rooms' => $setting->home_stat_caption_rooms,
            'caption_pools' => $setting->home_stat_caption_pools,
            'caption_dining' => $setting->home_stat_caption_dining,
        ];
    }
}
