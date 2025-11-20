<?php

namespace App\Repositories\Podcast;

use App\Models\Podcast;

class PodcastRepository
{
    public function findById(int $id): Podcast
    {
        return Podcast::findOrFail($id);
    }

    public function create(array $data): Podcast
    {
        $podcast = Podcast::create([
            'title' => $data['title'],
            'host_name' => $data['host_name'],
            'description' => $data['description'] ?? '',
            'audio_path' => $data['audio_path'],
            'is_active' => $data['is_active'] ?? true,
            'publish_date' => $data['publish_date'] ?? now(),
            'user_id' => $data['user_id'],
        ]);
        return $podcast;
    }

    public function update(Podcast $podcast, array $data): Podcast
    {
        $podcast->update([
            'title' => $data['title'] ?? $podcast->getTranslations('title'),
            'host_name' => $data['host_name'] ?? $podcast->getTranslations('host_name'),
            'description' => $data['description'] ?? $podcast->getTranslations('description'),
            'audio_path' => $data['audio_path'] ?? $podcast->audio_path,
            'is_active' => $data['is_active'] ?? $podcast->is_active,
            'publish_date' => $data['publish_date'] ?? $podcast->publish_date,
            'user_id' => $data['user_id'] ?? $podcast->user_id,
        ]);

        return $podcast;
    }

    public function delete(Podcast $podcast): bool
    {
        return (bool) $podcast->delete();
    }
}
