<?php

namespace App\Repositories\Reel;

use App\Models\Reel;

class ReelRepository
{
    public function findById(int $id): Reel
    {
        return Reel::findOrFail($id);
    }

    public function create(array $data): Reel
    {
        $reel = Reel::create([
            'reel_group_id' => $data['reel_group_id'] ?? null,
            'description' => $data['description'],
            'path' => $data['path'],
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? true,
            'user_id' => $data['user_id'],
        ]);
        return $reel;
    }

    public function update(Reel $reel, array $data): Reel
    {
        $reel->update([
            'reel_group_id' => $data['reel_group_id'] ?? $reel->reel_group_id,
            'description' => $data['description'] ?? $reel->getTranslations('description'),
            'path' => $data['path'] ?? $reel->path,
            'type' => $data['type'] ?? $reel->type,
            'is_active' => $data['is_active'] ?? $reel->is_active,
            'user_id' => $data['user_id'] ?? $reel->user_id,
        ]);

        return $reel;
    }

    public function delete(Reel $reel): bool
    {
        return (bool) $reel->delete();
    }
}
