<?php

namespace App\Repositories\Ad;

use App\Models\Ad;

class AdRepository
{
    public function findById(int $id): Ad
    {
        return Ad::findOrFail($id);
    }

    public function create(array $data): Ad
    {
        return Ad::create($data);
    }

    public function update(Ad $ad, array $data): Ad
    {
        $ad->update($data);
        return $ad;
    }

    public function delete(Ad $ad): bool
    {
        return (bool) $ad->delete();
    }
}