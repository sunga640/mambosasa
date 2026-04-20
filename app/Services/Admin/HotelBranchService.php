<?php

namespace App\Services\Admin;

use App\Models\HotelBranch;
use App\Models\MediaAsset;
use App\Support\PublicDisk;
use App\Support\UploadStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HotelBranchService
{
    public function __construct(
        private RoomService $rooms,
    ) {}

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<UploadedFile>  $previewUploads
     */
    public function create(array $attributes, ?UploadedFile $logo, array $previewUploads, ?int $logoMediaAssetId = null, array $previewMediaAssetIds = []): HotelBranch
    {
        return DB::transaction(function () use ($attributes, $logo, $previewUploads, $logoMediaAssetId, $previewMediaAssetIds) {
            $attributes['slug'] = $this->uniqueSlug($attributes['name']);
            $attributes['preview_images'] = [];

            $branch = HotelBranch::query()->create($attributes);

            if ($logo) {
                $branch->logo_path = UploadStorage::storePublic($logo, "branches/{$branch->id}");
            } elseif ($logoMediaAssetId) {
                $branch->logo_path = MediaAsset::query()->whereKey($logoMediaAssetId)->value('path');
            }

            $paths = [];
            foreach ($previewUploads as $file) {
                if ($file instanceof UploadedFile && count($paths) < 4) {
                    $paths[] = UploadStorage::storePublic($file, "branches/{$branch->id}/previews");
                }
            }
            foreach ($previewMediaAssetIds as $assetId) {
                if (count($paths) >= 4) {
                    break;
                }
                $path = MediaAsset::query()->whereKey((int) $assetId)->value('path');
                if ($path) {
                    $paths[] = $path;
                }
            }
            $branch->preview_images = $paths;
            $branch->save();

            return $branch->fresh();
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<UploadedFile>  $previewUploads
     * @param  list<int>  $removePreviewIndexes 0-based indexes to clear
     */
    public function update(
        HotelBranch $branch,
        array $attributes,
        ?UploadedFile $logo,
        array $previewUploads,
        array $removePreviewIndexes,
        ?int $logoMediaAssetId = null,
        array $previewMediaAssetIds = [],
    ): HotelBranch {
        return DB::transaction(function () use ($branch, $attributes, $logo, $previewUploads, $removePreviewIndexes, $logoMediaAssetId, $previewMediaAssetIds) {
            if (($attributes['name'] ?? $branch->name) !== $branch->name) {
                $attributes['slug'] = $this->uniqueSlug($attributes['name'], $branch->id);
            }

            $previews = array_values($branch->preview_images ?? []);

            foreach ($removePreviewIndexes as $idx) {
                $idx = (int) $idx;
                if (isset($previews[$idx])) {
                    $this->deletePublicIfExists($previews[$idx]);
                    unset($previews[$idx]);
                }
            }
            $previews = array_values($previews);

            foreach ($previewUploads as $file) {
                if (! ($file instanceof UploadedFile)) {
                    continue;
                }
                if (count($previews) >= 4) {
                    break;
                }
                $previews[] = UploadStorage::storePublic($file, "branches/{$branch->id}/previews");
            }
            foreach ($previewMediaAssetIds as $assetId) {
                if (count($previews) >= 4) {
                    break;
                }
                $path = MediaAsset::query()->whereKey((int) $assetId)->value('path');
                if ($path) {
                    $previews[] = $path;
                }
            }

            $attributes['preview_images'] = $previews;

            if ($logo) {
                $this->deletePublicIfExists($branch->logo_path);
                $attributes['logo_path'] = UploadStorage::storePublic($logo, "branches/{$branch->id}");
            } elseif ($logoMediaAssetId) {
                $attributes['logo_path'] = MediaAsset::query()->whereKey($logoMediaAssetId)->value('path');
            }

            $branch->update($attributes);

            return $branch->fresh();
        });
    }

    public function delete(HotelBranch $branch): void
    {
        DB::transaction(function () use ($branch) {
            foreach ($branch->rooms()->with('images')->get() as $room) {
                $this->rooms->deleteFilesOnly($room);
                $room->images()->delete();
                $room->delete();
            }

            $this->deletePublicIfExists($branch->logo_path);
            foreach ($branch->preview_images ?? [] as $path) {
                $this->deletePublicIfExists($path);
            }

            $branch->delete();
        });
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $slug = Str::slug($name) ?: 'branch';
        $base = $slug;
        $i = 1;
        while (HotelBranch::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $base.'-'.$i++;
        }

        return $slug;
    }

    private function deletePublicIfExists(?string $path): void
    {
        PublicDisk::delete($path);
    }
}
