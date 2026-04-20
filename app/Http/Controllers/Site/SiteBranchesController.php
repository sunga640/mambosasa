<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\HotelBranch;
use App\Models\SystemSetting;
use Illuminate\Contracts\View\View;

class SiteBranchesController extends Controller
{
    public function __invoke(): View
    {
        $branches = HotelBranch::query()
            ->where('is_active', true)
            ->withCount(['rooms'])
            ->orderBy('name')
            ->get();

        $setting = SystemSetting::current();

        return view('site.branches', [
            'branches' => $branches,
            'heroUrl' => $setting->resolvedPageHero('branches', 'img/pageHero/4.png'),
        ]);
    }
}
