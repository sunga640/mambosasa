<?php

namespace App\Http\Controllers\Kitchen;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use App\Models\RestaurantMenuItem;
use App\Support\PublicDisk;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KitchenMenuController extends Controller
{
    public function index(): View
    {
        return view('kitchen.menu.index', [
            'items' => RestaurantMenuItem::query()->orderBy('sort_order')->orderBy('name')->paginate(12),
            'mediaAssets' => MediaAsset::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['is_active'] = $request->boolean('is_active');
        $data['image_path'] = $this->resolveImagePath($request, $data['media_asset_id'] ?? null);
        unset($data['media_asset_id']);

        RestaurantMenuItem::query()->create($data);

        return back()->with('status', __('Menu item added.'));
    }

    public function update(Request $request, RestaurantMenuItem $menuItem): RedirectResponse
    {
        $data = $this->validated($request, true);
        $data['is_active'] = $request->boolean('is_active');
        $data['image_path'] = $this->resolveImagePath($request, $data['media_asset_id'] ?? null, $menuItem->image_path);
        unset($data['media_asset_id']);

        $menuItem->update($data);

        return back()->with('status', __('Menu item updated.'));
    }

    public function destroy(RestaurantMenuItem $menuItem): RedirectResponse
    {
        if ($menuItem->image_path && ! str_starts_with((string) $menuItem->image_path, 'media-library/')) {
            Storage::disk('public')->delete($menuItem->image_path);
        }

        $menuItem->delete();

        return back()->with('status', __('Menu item deleted.'));
    }

    private function validated(Request $request, bool $updating = false): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'preparation_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => [$updating ? 'nullable' : 'nullable', 'image', 'max:4096'],
            'media_asset_id' => ['nullable', 'integer', 'exists:media_assets,id'],
        ]);
    }

    private function resolveImagePath(Request $request, ?int $mediaAssetId, ?string $current = null): ?string
    {
        if ($request->hasFile('image')) {
            if ($current) {
                Storage::disk('public')->delete($current);
            }

            return $request->file('image')->store('kitchen-menu', 'public');
        }

        if ($mediaAssetId) {
            return MediaAsset::query()->whereKey($mediaAssetId)->value('path') ?: $current;
        }

        return $current;
    }
}
