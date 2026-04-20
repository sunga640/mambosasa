<?php

namespace App\Services\Admin;

use App\Enums\RoomStatus;
use App\Models\HotelBranch;
use App\Models\MediaAsset;
use App\Models\Room;
use App\Models\RoomType;
use App\Support\PublicDisk;
use App\Support\UploadStorage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class RoomService
{
    /**
     * @param  array<string, mixed>  $attributes  status as string value; includes card_primary
     * @param  list<UploadedFile>  $imageUploads
     * @param  list<string|null>  $imageCaptions  same order as $imageUploads
     */
    public function create(
        HotelBranch $branch,
        array $attributes,
        ?UploadedFile $video,
        ?UploadedFile $heroImage,
        array $imageUploads,
        array $imageCaptions = [],
        ?int $heroMediaAssetId = null,
        array $galleryMediaAssetIds = [],
    ): Room {
        return DB::transaction(function () use ($branch, $attributes, $video, $heroImage, $imageUploads, $imageCaptions, $heroMediaAssetId, $galleryMediaAssetIds) {
            $attributes['hotel_branch_id'] = $branch->id;
            $roomType = RoomType::query()->find($attributes['room_type_id'] ?? null);
            if ($roomType && empty($attributes['name'])) {
                $attributes['name'] = $roomType->name.' '.$attributes['room_number'];
            }
            if ($roomType && ((float) ($attributes['price'] ?? 0)) <= 0) {
                $attributes['price'] = $roomType->price;
            }
            $attributes['slug'] = $this->uniqueRoomSlug($branch, $attributes['name']);
            $attributes['status'] = RoomStatus::from($attributes['status'])->value;
            $attributes['card_primary'] = $attributes['card_primary'] ?? 'none';

            unset($attributes['video_path'], $attributes['hero_image_path']);

            $room = Room::query()->create($attributes);

            if ($video) {
                $room->video_path = UploadStorage::storePublic($video, "rooms/{$room->id}/video");
            }
            if ($heroImage) {
                $room->hero_image_path = MediaAsset::createFromUpload($heroImage)->path;
            } elseif ($heroMediaAssetId) {
                $room->hero_image_path = MediaAsset::query()->whereKey($heroMediaAssetId)->value('path');
            }
            $room->save();

            $order = 0;
            foreach ($imageUploads as $idx => $file) {
                if ($file instanceof UploadedFile) {
                    $path = MediaAsset::createFromUpload($file)->path;
                    $cap = $imageCaptions[$idx] ?? null;
                    $cap = is_string($cap) && $cap !== '' ? $cap : null;
                    $room->images()->create([
                        'path' => $path,
                        'caption' => $cap,
                        'sort_order' => $order++,
                    ]);
                }
            }
            foreach ($galleryMediaAssetIds as $assetId) {
                $path = MediaAsset::query()->whereKey((int) $assetId)->value('path');
                if ($path) {
                    $room->images()->create([
                        'path' => $path,
                        'caption' => null,
                        'sort_order' => $order++,
                    ]);
                }
            }

            return $room->fresh(['images']);
        });
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  list<UploadedFile>  $imageUploads
     * @param  list<int>  $removeImageIds
     * @param  list<string|null>  $newImageCaptions  parallel to $imageUploads
     * @param  array<int, string|null>  $captionUpdates  room_image id => caption
     */
    public function update(
        Room $room,
        array $attributes,
        ?UploadedFile $video,
        bool $removeVideo,
        ?UploadedFile $heroImage,
        bool $removeHero,
        array $imageUploads,
        array $removeImageIds,
        array $newImageCaptions = [],
        array $captionUpdates = [],
        ?int $heroMediaAssetId = null,
        array $galleryMediaAssetIds = [],
    ): Room {
        return DB::transaction(function () use ($room, $attributes, $video, $removeVideo, $heroImage, $removeHero, $imageUploads, $removeImageIds, $newImageCaptions, $captionUpdates, $heroMediaAssetId, $galleryMediaAssetIds) {
            $newBranchId = (int) ($attributes['hotel_branch_id'] ?? $room->hotel_branch_id);
            $branch = HotelBranch::query()->findOrFail($newBranchId);
            $roomType = RoomType::query()->find($attributes['room_type_id'] ?? $room->room_type_id);
            if ($roomType && empty($attributes['name'])) {
                $attributes['name'] = $roomType->name.' '.($attributes['room_number'] ?? $room->room_number);
            }

            if ((int) $room->hotel_branch_id !== $newBranchId
                || ($attributes['name'] ?? $room->name) !== $room->name) {
                $attributes['slug'] = $this->uniqueRoomSlug($branch, $attributes['name'], $room->id);
            }

            if (isset($attributes['status'])) {
                $attributes['status'] = RoomStatus::from($attributes['status'])->value;
            }

            if (isset($attributes['card_primary'])) {
                $attributes['card_primary'] = $attributes['card_primary'];
            }

            unset($attributes['video_path'], $attributes['hero_image_path']);

            if ($removeVideo) {
                $this->deletePublicIfExists($room->video_path);
                $attributes['video_path'] = null;
            }

            if ($video) {
                $this->deletePublicIfExists($room->video_path);
                $attributes['video_path'] = UploadStorage::storePublic($video, "rooms/{$room->id}/video");
            }

            if ($removeHero) {
                $this->deletePublicIfExists($room->hero_image_path);
                $attributes['hero_image_path'] = null;
            }

            if ($heroImage) {
                $this->deletePublicIfExists($room->hero_image_path);
                $attributes['hero_image_path'] = MediaAsset::createFromUpload($heroImage)->path;
            } elseif ($heroMediaAssetId) {
                $attributes['hero_image_path'] = MediaAsset::query()->whereKey($heroMediaAssetId)->value('path');
            }

            $room->update($attributes);

            foreach ($captionUpdates as $imageId => $text) {
                $imageId = (int) $imageId;
                $img = $room->images()->whereKey($imageId)->first();
                if ($img) {
                    $cap = is_string($text) && $text !== '' ? $text : null;
                    $img->update(['caption' => $cap]);
                }
            }

            foreach ($removeImageIds as $id) {
                $img = $room->images()->whereKey($id)->first();
                if ($img) {
                    $this->deletePublicIfExists($img->path);
                    $img->delete();
                }
            }

            $maxOrder = (int) $room->images()->max('sort_order');
            $addIdx = 0;
            foreach ($imageUploads as $file) {
                if ($file instanceof UploadedFile) {
                    $path = MediaAsset::createFromUpload($file)->path;
                    $cap = $newImageCaptions[$addIdx] ?? null;
                    $cap = is_string($cap) && $cap !== '' ? $cap : null;
                    $addIdx++;
                    $room->images()->create([
                        'path' => $path,
                        'caption' => $cap,
                        'sort_order' => ++$maxOrder,
                    ]);
                }
            }
            foreach ($galleryMediaAssetIds as $assetId) {
                $path = MediaAsset::query()->whereKey((int) $assetId)->value('path');
                if ($path) {
                    $room->images()->create([
                        'path' => $path,
                        'caption' => null,
                        'sort_order' => ++$maxOrder,
                    ]);
                }
            }

            return $room->fresh(['images', 'branch']);
        });
    }

    public function delete(Room $room): void
    {
        DB::transaction(function () use ($room) {
            $this->deleteFilesOnly($room);
            $room->images()->delete();
            $room->delete();
        });
    }

    public function deleteFilesOnly(Room $room): void
    {
        $this->deletePublicIfExists($room->video_path);
        $this->deletePublicIfExists($room->hero_image_path);
        foreach ($room->images()->get() as $img) {
            $this->deletePublicIfExists($img->path);
        }
    }

    private function uniqueRoomSlug(HotelBranch $branch, string $name, ?int $ignoreRoomId = null): string
    {
        $slug = Str::slug($name) ?: 'room';
        $base = $slug;
        $i = 1;
        while (Room::query()
            ->where('hotel_branch_id', $branch->id)
            ->where('slug', $slug)
            ->when($ignoreRoomId, fn ($q) => $q->where('id', '!=', $ignoreRoomId))
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
