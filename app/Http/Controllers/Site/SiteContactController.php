<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Requests\Site\StoreContactMessageRequest;
use App\Models\ContactMessage;
use App\Models\HotelBranch;
use Illuminate\Http\RedirectResponse;

class SiteContactController extends Controller
{
    public function store(StoreContactMessageRequest $request): RedirectResponse
    {
        $data = $request->validated();

        ContactMessage::create([
            'hotel_branch_id' => $this->resolveBranchId($data['branch_id'] ?? null),
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'body' => $data['body'],
        ]);

        return redirect()
            ->route('site.page', ['slug' => 'contact'])
            ->with('status', __('Thank you - we will get back to you soon.'));
    }

    private function resolveBranchId(?int $requestedBranchId): ?int
    {
        if ($requestedBranchId && HotelBranch::query()->whereKey($requestedBranchId)->exists()) {
            return $requestedBranchId;
        }

        $activeBranchId = HotelBranch::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->value('id');

        if ($activeBranchId) {
            return (int) $activeBranchId;
        }

        $fallbackBranchId = HotelBranch::query()
            ->orderBy('name')
            ->value('id');

        return $fallbackBranchId ? (int) $fallbackBranchId : null;
    }
}
