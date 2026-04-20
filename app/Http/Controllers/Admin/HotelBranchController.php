<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreHotelBranchRequest;
use App\Http\Requests\Admin\UpdateHotelBranchRequest;
use App\Models\HotelBranch;
use App\Models\MediaAsset;
use App\Services\Admin\HotelBranchService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HotelBranchController extends Controller
{
    public function __construct(
        private HotelBranchService $branches,
    ) {}

    public function index(): View
    {
        return view('admin.branches.index', [
            'branches' => HotelBranch::query()->orderBy('name')->paginate(7),
        ]);
    }

    public function create(): View
    {
        return view('admin.branches.create', [
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function store(StoreHotelBranchRequest $request): RedirectResponse
    {
        $attributes = collect($request->safe()->all())
            ->except(['logo', 'preview_images'])
            ->all();
        $previews = array_values(array_filter(
            $request->file('preview_images', []) ?? [],
            fn ($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()
        ));

        $this->branches->create(
            $attributes,
            $request->file('logo'),
            $previews,
            $request->integer('logo_media_asset_id') ?: null,
            array_values(array_filter(array_map('intval', $request->input('preview_media_asset_ids', [])))),
        );

        return redirect()->route('admin.branches.index')->with('status', __('Branch created.'));
    }

    public function edit(HotelBranch $branch): View
    {
        return view('admin.branches.edit', [
            'branch' => $branch,
            'mediaAssets' => MediaAsset::query()->latest()->limit(100)->get(),
        ]);
    }

    public function update(UpdateHotelBranchRequest $request, HotelBranch $branch): RedirectResponse
    {
        $attributes = collect($request->safe()->all())
            ->except(['logo', 'preview_images', 'remove_preview_indexes'])
            ->all();
        $previews = array_values(array_filter(
            $request->file('preview_images', []) ?? [],
            fn ($f) => $f instanceof \Illuminate\Http\UploadedFile && $f->isValid()
        ));
        $removeIdx = array_values(array_unique(array_map('intval', (array) ($request->input('remove_preview_indexes', [])))));

        $this->branches->update(
            $branch,
            $attributes,
            $request->file('logo'),
            $previews,
            $removeIdx,
            $request->integer('logo_media_asset_id') ?: null,
            array_values(array_filter(array_map('intval', $request->input('preview_media_asset_ids', [])))),
        );

        return redirect()->route('admin.branches.index')->with('status', __('Branch updated.'));
    }

    public function destroy(HotelBranch $branch): RedirectResponse
    {
        $this->branches->delete($branch);

        return redirect()->route('admin.branches.index')->with('status', __('Branch deleted.'));
    }
}
