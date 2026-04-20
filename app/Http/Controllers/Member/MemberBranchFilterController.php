<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Optional filter for member dashboard/bookings views (by room branch).
 */
class MemberBranchFilterController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:hotel_branches,id'],
        ]);

        if ($request->filled('branch_id')) {
            session(['member_booking_branch_id' => $request->integer('branch_id')]);
        } else {
            $request->session()->forget('member_booking_branch_id');
        }

        return back();
    }
}
