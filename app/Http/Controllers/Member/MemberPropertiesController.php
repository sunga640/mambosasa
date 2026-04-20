<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Concerns\ResolvesActiveBranches;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;

class MemberPropertiesController extends Controller
{
    use ResolvesActiveBranches;

    public function index(): View
    {
        return view('member.properties.index', [
            'branches' => $this->activeBranchesForDirectory(),
        ]);
    }
}
