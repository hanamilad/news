<?php

namespace App\GraphQL\Queries;

use App\Models\News;
use App\Models\Category;
use App\Models\Video;
use App\Models\Podcast;
use App\Models\Article;

class HomeQuery
{
    public function home($_, array $args)
    {
        $latestLimit   = $args['latest_limit'] ?? 10;
        $urgentLimit   = $args['urgent_limit'] ?? 5;
        $categoryLimit = $args['category_limit'] ?? 5;
        $videoLimit    = $args['video_limit'] ?? 5;
        $podcastLimit  = $args['podcast_limit'] ?? 5;
        $articleLimit  = $args['article_limit'] ?? 5;

        $latestNews = News::where('is_active', true)
            ->latest()
            ->take($latestLimit)
            ->get();

        $urgentCategories = Category::whereHas('news', function ($q) {
            $q->where('is_urgent', true)->where('is_active', true);
        })->get();

        $urgentNewsByCategory = $urgentCategories->map(function ($cat) use ($urgentLimit) {
            return [
                'category' => $cat,
                'news' => $cat->news()
                    ->where('is_urgent', true)
                    ->where('is_active', true)
                    ->latest()
                    ->take($urgentLimit)
                    ->get(),
            ];
        });

        $normalCategories = Category::showInHomepage()->whereHas('news', function ($q) {
            $q->where('is_active', true);
        })->get();

        $categoryNews = $normalCategories->map(function ($cat) use ($categoryLimit) {
            return [
                'category' => $cat,
                'news' => $cat->news()
                    ->where('is_active', true)
                    ->latest()
                    ->take($categoryLimit)
                    ->get(),
            ];
        });

        // 4️ الفيديوهات
        $videos = Video::where('is_active', true)
            ->latest()
            ->take($videoLimit)
            ->get();

        // 5️ البودكاست
        $podcasts = Podcast::where('is_active', true)
            ->latest()
            ->take($podcastLimit)
            ->get();

        // 6️ المقالات
        $articles = Article::where('is_active', true)
            ->latest()
            ->take($articleLimit)
            ->get();

        return [
            'latest_news' => $latestNews,
            'urgent_news_by_category' => $urgentNewsByCategory,
            'category_news' => $categoryNews,
            'videos' => $videos,
            'podcasts' => $podcasts,
            'articles' => $articles,
        ];
    }
}
