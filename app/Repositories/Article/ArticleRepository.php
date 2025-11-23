<?php

namespace App\Repositories\Article;

use App\Models\Article;

class ArticleRepository
{
    public function findById(int $id): Article
    {
        return Article::findOrFail($id);
    }

    public function create(array $data): Article
    {
        $article = Article::create([
            'title' => $data['title'],
            'content' => $data['content'],
            'author_name' => $data['author_name'],
            'author_image' => $data['author_image'],
            'is_active' => $data['is_active'] ?? true,
            'is_admin_approved' => $data['is_admin_approved'] ?? false,
            'publish_date' => $data['publish_date'] ?? now(),
            'user_id' => $data['user_id'],
        ]);
        return $article;
    }

    public function update(Article $article, array $data): Article
    {
        $article->update([
            'title' => $data['title'] ?? $article->getTranslations('title'),
            'content' => $data['content'] ?? $article->getTranslations('content'),
            'author_name' => $data['author_name'] ?? $article->getTranslations('author_name'),
            'author_image' => $data['author_image'] ?? $article->author_image,
            'is_active' => $data['is_active'] ?? $article->is_active,
            'is_admin_approved' => $data['is_admin_approved'] ?? $article->is_admin_approved,
            'publish_date' => $data['publish_date'] ?? $article->publish_date,
            'user_id' => $data['user_id'] ?? $article->user_id,
        ]);

        return $article;
    }

    public function delete(Article $article): bool
    {
        return (bool) $article->delete();
    }
}
