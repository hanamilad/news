<?php

namespace App\GraphQL\Queries;

use App\Models\News;
use App\Models\Category;
use App\Models\Video;
use App\Models\Podcast;
use App\Models\Article;

class HomeQuery
{
    private const CACHE_TTL = 300;

    public function home($_, array $args)
    {
        $limits = [
            'main_news' => min($args['main_news_limit'] ?? 6, 20),
            'urgent_news' => min($args['urgent_limit'] ?? 5, 10),
            'category' => min($args['category_limit'] ?? 5, 15),
            'video' => min($args['video_limit'] ?? 5, 10),
            'podcast' => min($args['podcast_limit'] ?? 5, 10),
            'article' => min($args['article_limit'] ?? 5, 10),
        ];

        $cacheKey = 'home_page:' . md5(json_encode($limits));
        
        if (function_exists('tenant')) {
            $cacheKey = 'tenant_' . tenant('id') . ':' . $cacheKey;
        }

        \App\Support\HomeCache::registerKey($cacheKey);
        return cache()->store('file')->remember($cacheKey, self::CACHE_TTL, function () use ($limits) {
            return [
                'mainNews' => News::forPublic(null, false, true)->take($limits['main_news'])->get(),
                
                'urgentNewsByCategory' => Category::whereHas('news', fn($q) => $q->forPublic(null, true))
                    ->with(['news' => fn($q) => $q->forPublic(null, true)->take($limits['urgent_news'])])
                    ->get()
                    ->map(fn($cat) => ['category' => $cat, 'news' => $cat->news]),
                
                'categoryNews' => $this->formatGridCategories(
                    Category::showInHomepage()
                        ->whereHas('news', fn($q) => $q->forPublic())
                        ->with(['news' => fn($q) => $q->forPublic()->take($limits['category']), 'template'])
                        ->get()
                ),
                
                'videos' => Video::forPublic()->take($limits['video'])->get(),
                'podcasts' => Podcast::forPublic()->take($limits['podcast'])->get(),
                'articles' => Article::forPublic()->take($limits['article'])->get(),
            ];
        });
    }

    private function formatGridCategories($categories): array
    {
        $rows = [];
        $currentRow = [];

        foreach ($categories as $cat) {
            if (!$cat->news || $cat->news->isEmpty()) {
                continue;
            }

            $categoryData = ['category' => $cat, 'news' => $cat->news];

            if ($cat->show_in_grid) {
                $currentRow[] = $categoryData;
                
                if (count($currentRow) === 2) {
                    $rows[] = $currentRow;
                    $currentRow = [];
                }
            } else {
                if (!empty($currentRow)) {
                    $rows[] = $currentRow;
                    $currentRow = [];
                }
                $rows[] = [$categoryData];
            }
        }

        if (!empty($currentRow)) {
            $rows[] = $currentRow;
        }

        return $rows;
    }
}