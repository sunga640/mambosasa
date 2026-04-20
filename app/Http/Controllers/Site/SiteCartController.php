<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Support\HomeHeroSlides;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteCartController extends Controller
{
    private const SESSION_KEY = 'site_cart';

    public function show(Request $request): View
    {
        $items = $this->items($request);
        $roomIds = array_keys($items);
        $rooms = $roomIds === []
            ? collect()
            : Room::query()->listedOnPublicSite()->whereIn('id', $roomIds)->with('branch')->get()->keyBy('id');

        $lines = [];
        $subtotal = 0.0;
        foreach ($items as $roomId => $qty) {
            $room = $rooms->get((int) $roomId);
            if (! $room) {
                continue;
            }
            $q = max(1, (int) $qty);
            $price = (float) $room->price;
            $lineTotal = $price * $q;
            $subtotal += $lineTotal;
            $lines[] = [
                'room' => $room,
                'qty' => $q,
                'line_total' => $lineTotal,
            ];
        }

        return view('site.cart', [
            'cartLines' => $lines,
            'subtotal' => $subtotal,
            'heroUrl' => HomeHeroSlides::urlForSlideNumber(3),
        ]);
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
            'qty' => ['sometimes', 'integer', 'min:1', 'max:99'],
        ]);
        $qty = (int) ($data['qty'] ?? 1);
        $listed = Room::query()->listedOnPublicSite()->whereKey($data['room_id'])->exists();
        if (! $listed) {
            return back()->withErrors(['room_id' => __('This room is not available.')]);
        }
        $items = $this->items($request);
        $id = (string) $data['room_id'];
        $items[$id] = ($items[$id] ?? 0) + $qty;
        $request->session()->put(self::SESSION_KEY, $items);

        return redirect()->route('site.cart')->with('status', __('Added to cart.'));
    }

    public function remove(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'room_id' => ['required', 'integer', 'exists:rooms,id'],
        ]);
        $items = $this->items($request);
        unset($items[(string) $data['room_id']]);
        $request->session()->put(self::SESSION_KEY, $items);

        return redirect()->route('site.cart')->with('status', __('Removed from cart.'));
    }

    /**
     * @return array<string, int>
     */
    private function items(Request $request): array
    {
        $raw = $request->session()->get(self::SESSION_KEY, []);
        if (! is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $k => $v) {
            $out[(string) $k] = max(1, (int) $v);
        }

        return $out;
    }
}
