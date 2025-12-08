<?php

namespace App\GraphQL\Queries;

use App\Models\Article;
use App\Models\Category;
use App\Models\News;
use App\Models\Podcast;
use App\Models\Video;
use App\Support\HomeCache;
use Illuminate\Support\Facades\Log;

class HomeQuery
{
    private const CACHE_TTL = 300;

    public function home($_, array $args)
    {
        try {
            $limits = [
                'main_news' => min($args['main_news_limit'] ?? 6, 20),
                'category' => min($args['category_limit'] ?? 5, 15),
                'video' => min($args['video_limit'] ?? 5, 10),
                'podcast' => min($args['podcast_limit'] ?? 5, 10),
                'article' => min($args['article_limit'] ?? 5, 10),
            ];

            $cacheKey = 'home_page:'.md5(json_encode($limits));

            if (function_exists('tenant') && tenant('id')) {
                $cacheKey = 'tenant_'.tenant('id').':'.$cacheKey;
            }

            HomeCache::registerKey($cacheKey);

            return cache()->store('file')->remember($cacheKey, self::CACHE_TTL, function () use ($limits) {
                return [
                    'mainNews' => News::forPublic(null, false, true)->take($limits['main_news'])->get() ?? collect([]),
                    'categoryNews' => $this->formatGridCategories(
                        Category::showInHomepage()
                            ->with([
                                'template',
                                'news' => fn ($q) => $q->forPublic()->take($limits['category']),
                                'subCategories.news' => fn ($q) => $q->forPublic()->take($limits['category']),
                            ])
                            ->get()
                            ->map(function ($cat) use ($limits) {
                                $cat->merged_news = $cat->mergedNews($limits['category']);

                                return $cat;
                            })
                    ),
                    'videos' => Video::forPublic()->take($limits['video'])->get(),
                    'podcasts' => Podcast::forPublic()->take($limits['podcast'])->get(),
                    'articles' => Article::forPublic()->take($limits['article'])->get(),
                ];
            });
        } catch (\Exception $e) {
            Log::error('Home query failed: '.$e->getMessage());

            return $this->getDefaultResponse();
        }
    }

    private function formatGridCategories($categories): array
    {
        $rows = [];
        $currentRow = [];

        foreach ($categories as $cat) {
            if (! isset($cat->merged_news) || $cat->merged_news->isEmpty()) {
                continue;
            }

            $categoryData = ['category' => $cat, 'news' => $cat->merged_news];

            if ($cat->show_in_grid) {
                $currentRow[] = $categoryData;

                if (count($currentRow) === 2) {
                    $rows[] = $currentRow;
                    $currentRow = [];
                }
            } else {
                if (! empty($currentRow)) {
                    $rows[] = $currentRow;
                    $currentRow = [];
                }
                $rows[] = [$categoryData];
            }
        }

        if (! empty($currentRow)) {
            $rows[] = $currentRow;
        }

        return $rows;
    }

    private function getDefaultResponse(): array
    {
        return [
            'mainNews' => collect([]),
            'categoryNews' => [],
            'videos' => collect([]),
            'podcasts' => collect([]),
            'articles' => collect([]),
        ];
    }
}
