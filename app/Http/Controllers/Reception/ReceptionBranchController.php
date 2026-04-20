<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReceptionBranchController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()?->isDirector() || $request->user()?->isSuperAdmin(), 403);

        $request->validate([
            'branch_id' => ['nullable', 'integer', 'exists:hotel_branches,id'],
        ]);

        if ($request->filled('branch_id')) {
            session(['director_branch_id' => $request->integer('branch_id')]);
        } else {
            $request->session()->forget('director_branch_id');
        }

        return back();
    }
}
