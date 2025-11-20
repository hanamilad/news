<?php

namespace App\Repositories\Video;

use App\Models\Video;

class VideoRepository
{
    public function findById(int $id): Video
    {
        return Video::findOrFail($id);
    }

    public function create(array $data): Video
    {
        $video = Video::create([
            'description' => $data['description'],
            'video_path' => $data['video_path'],
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? true,
            'publish_date' => $data['publish_date'] ?? now(),
            'user_id' => $data['user_id'],
        ]);
        return $video;
    }

    public function update(Video $video, array $data): Video
    {
        $video->update([
            'description' => $data['description'] ?? $video->getTranslations('description'),
            'video_path' => $data['video_path'] ?? $video->video_path,
            'type' => $data['type'] ?? $video->type,
            'is_active' => $data['is_active'] ?? $video->is_active,
            'publish_date' => $data['publish_date'] ?? $video->publish_date,
            'user_id' => $data['user_id'] ?? $video->user_id,
        ]);

        return $video;
    }

    public function delete(Video $video): bool
    {
        return (bool) $video->delete();
    }
}
