<?php

namespace App\Repositories\News;

use App\Models\News;

class NewsRepository
{
    public function findById(int $id): News
    {
        return News::findOrFail($id);
    }

    public function create(array $data): News
    {
        $news = News::create([
            'title' => $data['title'],
            'styled_description' => $data['styled_description'] ?? null,
            'is_urgent' => $data['is_urgent'] ?? false,
            'is_active' => $data['is_active'] ?? true,
            'is_admin_approved' => $data['is_admin_approved'] ?? false,
            'is_main' => $data['is_main'] ?? false,
            'publish_date' => $data['publish_date'] ?? now(),
            'user_id' => $data['user_id'],
            'category_id' => $data['category_id'],
        ]);

        if (! empty($data['hashtag_ids'])) {
            $news->hashtags()->sync($data['hashtag_ids']);
        }

        if (! empty($data['images'])) {
            foreach ($data['images'] as $img) {
                $news->images()->create([
                    'image_path' => $img['image_path'],
                    'is_main' => $img['is_main'] ?? false,
                ]);
            }
        }

        if (! empty($data['links'])) {
            foreach ($data['links'] as $ln) {
                $news->links()->create([
                    'video_link' => $ln['video_link'] ?? null,
                ]);
            }
        }

        return $news->load(['hashtags', 'images', 'links']);
    }

    public function update(News $news, array $data): News
    {
        $news->update([
            'title' => $data['title'] ?? $news->getTranslations('title'),
            'styled_description' => $data['styled_description'] ?? $news->getTranslations('styled_description'),
            'is_urgent' => $data['is_urgent'] ?? $news->is_urgent,
            'is_active' => $data['is_active'] ?? $news->is_active,
            'is_admin_approved' => $data['is_admin_approved'] ?? $news->is_admin_approved,
            'is_main' => $data['is_main'] ?? $news->is_main,
            'publish_date' => $data['publish_date'] ?? $news->publish_date,
            'user_id' => $data['user_id'] ?? $news->user_id,
            'category_id' => $data['category_id'] ?? $news->category_id,
        ]);

        if (array_key_exists('hashtag_ids', $data)) {
            $news->hashtags()->sync($data['hashtag_ids'] ?: []);
        }

        if (array_key_exists('images', $data)) {
            $news->images()->delete();
            foreach ($data['images'] as $img) {
                $news->images()->create([
                    'image_path' => $img['image_path'],
                    'is_main' => $img['is_main'] ?? false,
                ]);
            }
        }

        if (array_key_exists('links', $data)) {
            $news->links()->delete();
            foreach ($data['links'] as $ln) {
                $news->links()->create([
                    'video_link' => $ln['video_link'] ?? null,
                ]);
            }
        }

        return $news->fresh(['hashtags', 'images', 'links']);
    }

    public function delete(News $news): bool
    {
        return (bool) $news->delete();
    }
}
