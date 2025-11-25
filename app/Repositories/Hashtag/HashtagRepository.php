<?php

namespace App\Repositories\Hashtag;

use App\Models\Hashtag;

class HashtagRepository
{
    public function findById(int $id): Hashtag
    {
        return Hashtag::findOrFail($id);
    }

    public function create(array $data): Hashtag
    {
        $hashtag = Hashtag::create([
            'name' => $data['name'],
        ]);

        return $hashtag;
    }

    public function update(Hashtag $hashtag, array $data): Hashtag
    {
        $hashtag->update([
            'name' => $data['name'] ?? $hashtag->getTranslations('name'),
        ]);

        return $hashtag;
    }

    public function delete(Hashtag $hashtag): bool
    {
        return (bool) $hashtag->delete();
    }
}
