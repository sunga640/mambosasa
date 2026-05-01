<?php

namespace App\Support;

use App\Models\HotelBranch;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

final class StaffScope
{
    public function __construct(
        private Request $request,
    ) {}

    /**
     * Branch IDs to filter (single-element for reception, or one selected for director).
     * Null means no branch filter (director viewing all branches).
     *
     * @return list<int>|null
     */
    public function branchIds(?User $user = null): ?array
    {
        $user = $user ?? $this->request->user();
        if (! $user) {
            return [];
        }

        if ($user->role?->slug === Role::RECEPTION_SLUG) {
            return $user->hotel_branch_id ? [(int) $user->hotel_branch_id] : [];
        }

        if ($user->role?->slug === Role::DIRECTOR_SLUG || $user->isSuperAdmin()) {
            $sel = session('director_branch_id');
            if ($sel) {
                return [(int) $sel];
            }

            return null;
        }

        return null;
    }

    public function filterBookingsByBranch(Builder $query, ?User $user = null): Builder
    {
        $ids = $this->branchIds($user);
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereHas('room', fn (Builder $q) => $q->whereIn('hotel_branch_id', $ids));
    }

    public function filterRoomsByBranch(Builder $query, ?User $user = null): Builder
    {
        $ids = $this->branchIds($user);
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('hotel_branch_id', $ids);
    }

    public function filterNotificationsByBranch(Builder $query, ?User $user = null): Builder
    {
        $user = $user ?? $this->request->user();
        $ids = $this->branchIds($user);
        if ($ids === null) {
            return $query->where(function (Builder $notificationQuery) use ($user): void {
                $notificationQuery->whereNull('recipient_user_id');
                if ($user) {
                    $notificationQuery->orWhere('recipient_user_id', $user->id);
                }
            });
        }
        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query
            ->where(function (Builder $notificationQuery) use ($user): void {
                $notificationQuery->whereNull('recipient_user_id');
                if ($user) {
                    $notificationQuery->orWhere('recipient_user_id', $user->id);
                }
            })
            ->whereHas('room', fn (Builder $q) => $q->whereIn('hotel_branch_id', $ids));
    }

    public function filterUsersByBranch(Builder $query, ?User $user = null): Builder
    {
        $ids = $this->branchIds($user);
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('hotel_branch_id', $ids);
    }

    public function filterContactMessagesByBranch(Builder $query, ?User $user = null): Builder
    {
        $ids = $this->branchIds($user);
        if ($ids === null) {
            return $query;
        }
        if ($ids === []) {
            return $query->whereRaw('0 = 1');
        }

        return $query->whereIn('hotel_branch_id', $ids);
    }

    public function branchIdsOrAll(?User $user = null): array
    {
        $ids = $this->branchIds($user);

        if ($ids === null) {
            return HotelBranch::query()->pluck('id')->map(fn ($id) => (int) $id)->all();
        }

        return $ids;
    }

    public function branchesForSwitcher(?User $user = null): array
    {
        $u = $user ?? $this->request->user();
        if (! $u?->isDirector() && ! $u?->isSuperAdmin()) {
            return [];
        }

        return HotelBranch::query()->orderBy('name')->get()->all();
    }
}
