<?php

namespace App\Http\Controllers\Concerns;

use App\Models\HotelBranch;
use Illuminate\Support\Collection;

trait ResolvesActiveBranches
{
    /**
     * @return Collection<int, HotelBranch>
     */
    protected function activeBranchesForDirectory(): Collection
    {
        return HotelBranch::query()
            ->where('is_active', true)
            ->withCount(['rooms'])
            ->orderBy('name')
            ->get();
    }
}
