<?php

namespace App\Repositories\ReelGroup;

use App\Models\ReelGroup;
use Illuminate\Support\Facades\DB;

class ReelGroupRepository
{
    public function findById(int $id): ReelGroup
    {
        return ReelGroup::findOrFail($id);
    }

    public function create(array $data): ReelGroup
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = $this->resolveSortOrder($data['sort_order'] ?? null);

            return ReelGroup::create([
                'title' => $data['title'],
                'is_active' => $data['is_active'] ?? true,
                'is_admin_approved' => $data['is_admin_approved'] ?? false,
                'user_id' => $data['user_id'],
                'sort_order' => $data['sort_order'],
            ]);
        });
    }

    public function update(ReelGroup $group, array $data): ReelGroup
    {
        return DB::transaction(function () use ($group, $data) {
            $newSortOrder = $data['sort_order'] ?? $group->sort_order;
            if ($newSortOrder != $group->sort_order) {
                $this->reorderOnUpdate($group, $newSortOrder);
            }
            $group->update([
                'title' => $data['title'] ?? $group->getTranslations('title'),
                'is_active' => $data['is_active'] ?? $group->is_active,
                'is_admin_approved' => $data['is_admin_approved'] ?? $group->is_admin_approved,
                'sort_order' => $newSortOrder,
            ]);

            return $group;
        });
    }

    public function delete(ReelGroup $group): bool
    {
        return DB::transaction(function () use ($group) {
            $deleted = (bool) $group->delete();

            if ($deleted) {
                $this->shiftSortOrders($group->sort_order + 1, null, -1);
            }

            return $deleted;
        });
    }

    protected function resolveSortOrder(?int $sortOrder): int
    {
        if ($sortOrder === null) {
            $maxOrder = ReelGroup::max('sort_order');

            return ($maxOrder ?? 0) + 1;
        }
        $this->shiftSortOrders($sortOrder, null, 1);

        return $sortOrder;
    }

    protected function reorderOnUpdate(ReelGroup $group, int $newSortOrder): void
    {
        $oldSortOrder = $group->sort_order;

        if ($newSortOrder > $oldSortOrder) {
            $this->shiftSortOrders($oldSortOrder + 1, $newSortOrder, -1);
        } elseif ($newSortOrder < $oldSortOrder) {
            $this->shiftSortOrders($newSortOrder, $oldSortOrder - 1, 1);
        }
    }

    protected function shiftSortOrders(int $from, ?int $to = null, int $step = 1): void
    {
        $query = ReelGroup::where('sort_order', '>=', $from);

        if ($to !== null) {
            $query->where('sort_order', '<=', $to);
        }

        $query->increment('sort_order', $step);
    }
}
