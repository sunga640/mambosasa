<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\HotelBranch;
use Illuminate\Contracts\View\View;

class ReceptionPropertiesController extends Controller
{
    use InteractsWithStaffScope;

    public function index(): View
    {
        $q = HotelBranch::query()
            ->where('is_active', true)
            ->withCount(['rooms'])
            ->orderBy('name');
        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $q->whereRaw('0 = 1');
            } else {
                $q->whereIn('id', $ids);
            }
        }

        return view('reception.properties.index', [
            'branches' => $q->get(),
        ]);
    }
}
