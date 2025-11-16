<?php
namespace App\Repositories\Reel;

use App\Models\Reel;
use Illuminate\Support\Facades\DB;

class ReelRepository
{
    public function findById(int $id): Reel
    {
        return Reel::findOrFail($id);
    }

    public function create(array $data): Reel
    {
        return DB::transaction(function () use ($data) {
            $data['sort_order'] = $this->resolveSortOrder($data['reel_group_id'] ?? null, $data['sort_order'] ?? null);
            $reel = Reel::create([
                'reel_group_id' => $data['reel_group_id'] ?? null,
                'news_id' => $data['news_id'] ?? null,
                'description' => $data['description'] ?? null,
                'path' => $data['path'],
                'type' => $data['type'] ?? null,
                'is_active' => $data['is_active'] ?? true,
                'user_id' => $data['user_id'],
                'sort_order' => $data['sort_order'],
            ]);
            return $reel;
        });
    }

    public function update(Reel $reel, array $data): Reel
    {
        return DB::transaction(function () use ($reel, $data) {
            $newGroupId = $data['reel_group_id'] ?? $reel->reel_group_id;
            $newSortOrder = $data['sort_order'] ?? $reel->sort_order;
            if ($newGroupId !== $reel->reel_group_id || $newSortOrder !== $reel->sort_order) {
                $this->reorderOnUpdate($reel, $newGroupId, $newSortOrder);
            }
            $reel->update([
                'reel_group_id' => $newGroupId,
                'description' => $data['description'] ?? $reel->getTranslations('description'),
                'path' => $data['path'] ?? $reel->path,
                'type' => $data['type'] ?? $reel->type,
                'is_active' => $data['is_active'] ?? $reel->is_active,
                'user_id' => $data['user_id'] ?? $reel->user_id,
                'news_id' => $data['news_id'] ?? $reel->news_id,
                'sort_order' => $newSortOrder,
            ]);
            return $reel->fresh();
        });
    }

    public function delete(Reel $reel): bool
    {
        return DB::transaction(function () use ($reel) {
            $groupId = $reel->reel_group_id;
            $sortOrder = $reel->sort_order;
            $deleted = (bool) $reel->delete();
            if ($deleted) {
                $this->shiftSortOrders($groupId, $sortOrder + 1, null, -1);
            }
            return $deleted;
        });
    }


    protected function resolveSortOrder(?int $groupId, ?int $sortOrder): int
    {
        if ($sortOrder === null) {
            $maxOrder = Reel::where('reel_group_id', $groupId)->max('sort_order');
            return ($maxOrder ?? 0) + 1;
        }

        $this->shiftSortOrders($groupId, $sortOrder, null, 1);
        return $sortOrder;
    }


    protected function reorderOnUpdate(Reel $reel, int $newGroupId, int $newSortOrder): void
    {
        $oldGroupId = $reel->reel_group_id;
        $oldSortOrder = $reel->sort_order;

        if ($newGroupId !== $oldGroupId) {
            $this->shiftSortOrders($oldGroupId, $oldSortOrder + 1, null, -1);
            $this->shiftSortOrders($newGroupId, $newSortOrder, null, 1);
        } else {
            if ($newSortOrder > $oldSortOrder) {
                $this->shiftSortOrders($newGroupId, $oldSortOrder + 1, $newSortOrder, -1);
            } elseif ($newSortOrder < $oldSortOrder) {
                $this->shiftSortOrders($newGroupId, $newSortOrder, $oldSortOrder - 1, 1);
            }
        }
    }


    protected function shiftSortOrders(?int $groupId, int $from, ?int $to = null, int $step = 1): void
    {
        $query = Reel::where('reel_group_id', $groupId)->where('sort_order', '>=', $from);
        if ($to !== null) {
            if ($step > 0) {
                $query->where('sort_order', '<=', $to);
            } else {
                $query->where('sort_order', '<=', $to);
            }
        }
        $query->increment('sort_order', $step * ($step > 0 ? 1 : -1));
    }
}
