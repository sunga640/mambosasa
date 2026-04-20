<?php

namespace App\Http\Controllers\Reception;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Reception\Concerns\InteractsWithStaffScope;
use App\Models\HotelBranch;
use App\Models\HotelService;
use App\Models\MediaAsset;
use App\Support\UploadStorage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReceptionHotelServiceController extends Controller
{
    use InteractsWithStaffScope;

    private function isAdminPanel(): bool
    {
        return request()->routeIs('admin.*');
    }

    private function panelView(string $suffix): string
    {
        return ($this->isAdminPanel() ? 'admin' : 'reception').'.hotel-services.'.$suffix;
    }

    private function panelRoute(string $suffix): string
    {
        return ($this->isAdminPanel() ? 'admin' : 'reception').'.hotel-services.'.$suffix;
    }

    public function index(): View
    {
        $q = HotelService::query()->with('branch')->orderBy('sort_order')->orderBy('name');
        $ids = $this->scope()->branchIds();
        if ($ids !== null) {
            if ($ids === []) {
                $q->whereRaw('0 = 1');
            } else {
                $q->where(function ($q2) use ($ids): void {
                    $q2->whereNull('hotel_branch_id')->orWhereIn('hotel_branch_id', $ids);
                });
            }
        }

        return view($this->panelView('index'), [
            'services' => $q->paginate(15),
            'branches' => HotelBranch::query()->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view($this->panelView('create'), [
            'branches' => $this->branchesForForm(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $this->assertBranchAllowed($data['hotel_branch_id'] ?? null);

        $data['slug'] = $this->uniqueSlug(Str::slug($data['name']));

        HotelService::query()->create(array_merge($data, [
            'sort_order' => $data['sort_order'] ?? 0,
        ]));

        return redirect()->route($this->panelRoute('index'))->with('status', __('Service created.'));
    }

    public function edit(HotelService $hotelService): View
    {
        $this->assertServiceInScope($hotelService);

        return view($this->panelView('edit'), [
            'service' => $hotelService,
            'branches' => $this->branchesForForm(),
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function update(Request $request, HotelService $hotelService): RedirectResponse
    {
        $this->assertServiceInScope($hotelService);
        $data = $this->validated($request);
        $this->assertBranchAllowed($data['hotel_branch_id'] ?? null);

        if (($data['name'] ?? '') !== $hotelService->name) {
            $data['slug'] = $this->uniqueSlug(Str::slug($data['name']), $hotelService->id);
        }

        $hotelService->update($data);

        return redirect()->route($this->panelRoute('index'))->with('status', __('Service updated.'));
    }

    public function destroy(HotelService $hotelService): RedirectResponse
    {
        $this->assertServiceInScope($hotelService);
        $hotelService->delete();

        return redirect()->route($this->panelRoute('index'))->with('status', __('Service removed.'));
    }

    private function validated(Request $request): array
    {
        $v = $request->validate([
            'name' => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0'],
            'category' => ['required', 'string', 'max:64'],
            'hotel_branch_id' => ['nullable', 'integer', 'exists:hotel_branches,id'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:99999'],
            'image' => ['nullable', 'file', 'max:8192', 'extensions:jpg,jpeg,png,gif,webp,avif'],
            'media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
        ]);
        $v['hotel_branch_id'] = $request->filled('hotel_branch_id') ? $request->integer('hotel_branch_id') : null;
        $v['is_active'] = $request->boolean('is_active');
        $v['sort_order'] = $v['sort_order'] ?? 0;
        if ($request->file('image')) {
            $v['image_path'] = UploadStorage::storePublic($request->file('image'), 'hotel-services');
        } elseif ($request->filled('media_asset_id')) {
            $v['image_path'] = MediaAsset::query()->whereKey($request->integer('media_asset_id'))->value('path');
        }
        unset($v['image'], $v['media_asset_id']);

        return $v;
    }

    private function assertBranchAllowed(?int $branchId): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($branchId === null) {
            return;
        }
        if ($ids === [] || ! in_array($branchId, $ids, true)) {
            abort(403);
        }
    }

    private function assertServiceInScope(HotelService $service): void
    {
        $ids = $this->scope()->branchIds();
        if ($ids === null) {
            return;
        }
        if ($service->hotel_branch_id === null) {
            return;
        }
        if ($ids === [] || ! in_array((int) $service->hotel_branch_id, $ids, true)) {
            abort(404);
        }
    }

    private function branchesForForm()
    {
        $ids = $this->scope()->branchIds();
        $q = HotelBranch::query()->orderBy('name');
        if ($ids !== null && $ids !== []) {
            $q->whereIn('id', $ids);
        }

        return $q->get();
    }

    private function uniqueSlug(string $base, ?int $exceptId = null): string
    {
        $slug = $base !== '' ? $base : 'service';
        $i = 0;
        do {
            $try = $i === 0 ? $slug : $slug.'-'.$i;
            $q = HotelService::query()->where('slug', $try);
            if ($exceptId) {
                $q->where('id', '!=', $exceptId);
            }
            $exists = $q->exists();
            $i++;
        } while ($exists);

        return $try;
    }
}
