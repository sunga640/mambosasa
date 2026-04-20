<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Support\HomeHeroSlides;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

class SiteSearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        if (strlen($q) >= 2) {
            $like = '%'.$q.'%';
            $rooms = Room::query()
                ->listedOnPublicSite()
                ->with(['branch', 'images'])
                ->where(function ($b) use ($like): void {
                    $b->where('name', 'like', $like)
                        ->orWhere('slug', 'like', $like)
                        ->orWhere('room_number', 'like', $like)
                        ->orWhere('description', 'like', $like);
                })
                ->orderBy('name')
                ->paginate(7)
                ->withQueryString();
        } else {
            $rooms = new LengthAwarePaginator([], 0, 7, (int) $request->query('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);
        }

        return view('site.search', [
            'q' => $q,
            'rooms' => $rooms,
            'heroUrl' => HomeHeroSlides::urlForSlideNumber(1),
        ]);
    }
}
