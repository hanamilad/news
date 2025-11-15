<?php

namespace App\Repositories\ReelGroup;

use App\Models\ReelGroup;

class ReelGroupRepository
{
    public function findById(int $id): ReelGroup
    {
        return ReelGroup::findOrFail($id);
    }

    public function create(array $data): ReelGroup
    {
        return ReelGroup::create([
            'title' => $data['title'],
            'is_active' => $data['is_active'] ?? true,
            'user_id' => $data['user_id'],
        ]);
    }

    public function update(ReelGroup $group, array $data): ReelGroup
    {
        $group->update([
            'title' => $data['title'] ?? $group->getTranslations('title'),
            'is_active' => $data['is_active'] ?? $group->is_active,
        ]);

        return $group;
    }

    public function delete(ReelGroup $group): bool
    {
        return (bool) $group->delete();
    }
}