<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\HotelService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class MemberHotelServiceCatalogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $branchId = session('member_booking_branch_id');
        $q = HotelService::query()->listedForGuests($branchId)->with('branch')->orderBy('category')->orderBy('sort_order')->orderBy('name');
        $grouped = $q->get()->groupBy('category');

        return view('member.hotel-services.catalog', [
            'groupedServices' => $grouped,
            'filterBranchId' => $branchId,
        ]);
    }
}
