<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HotelBranch;
use Illuminate\Contracts\View\View;

class PropertiesDirectoryController extends Controller
{
    public function index(): View
    {
        return view('admin.properties.index', [
            'branches' => HotelBranch::query()->withCount('rooms')->orderByDesc('id')->get(),
        ]);
    }
}
