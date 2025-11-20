<?php

namespace App\GraphQL\Resolvers;

use App\Models\News;

class NewsResolver
{
    public function suggestedNews(News $news)
    {
        return News::where('category_id', $news->category_id)
            ->where('id', '!=', $news->id)
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }
}
