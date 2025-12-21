<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use Illuminate\Database\Eloquent\Builder;

class ArticleQuery
{
    public function articlesForPublicBuilder($_, array $args): Builder
    {
        $query = Article::forPublic();

        $name = $args['name'] ?? null;

        if (! empty($name)) {
            $query->where(function ($q) use ($name) {
                $q->where('author_name->ar', 'LIKE', "%{$name}%")
                    ->orWhere('author_name->en', 'LIKE', "%{$name}%");
            });
        }

        return $query->orderByDesc('created_at');
    }
}
