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
        $mainNewsLimit = $args['main_news_limit'] ?? 6;
        $urgentLimit   = $args['urgent_limit'] ?? 5;
        $categoryLimit = $args['category_limit'] ?? 5;
        $videoLimit    = $args['video_limit'] ?? 5;
        $podcastLimit  = $args['podcast_limit'] ?? 5;
        $articleLimit  = $args['article_limit'] ?? 5;

        $mainNews = News::forPublic(null, false, true)->take($mainNewsLimit)->get();

        $urgentCategories = Category::whereHas('news', function ($q) {
            $q->forPublic(null, true);
        })->with(['news' => function ($q) use ($urgentLimit) {
            $q->forPublic(null, true)->take($urgentLimit);
        }])->get();

        $urgentNewsByCategory = $urgentCategories->map(function ($cat) {
            return [
                'category' => $cat,
                'news' => $cat->news,
            ];
        });


        $normalCategories = Category::showInHomepage()
            ->whereHas('news', function ($q) {
                $q->forPublic();
            })
            ->with(['news' => function ($q) use ($categoryLimit) {
                $q->forPublic()->take($categoryLimit);
            }])
            ->get();
        $categoryNews = $normalCategories->map(function ($cat) {
            return [
                'category' => $cat,
                'news' => $cat->news,
            ];
        });


        $videos = Video::forPublic()->take($videoLimit)->get();
        $podcasts = Podcast::forPublic()->take($podcastLimit)->get();
        $articles = Article::forPublic()->take($articleLimit)->get();

        return [
            'mainNews' => $mainNews,
            'urgent_news_by_category' => $urgentNewsByCategory,
            'category_news' => $categoryNews,
            'videos' => $videos,
            'podcasts' => $podcasts,
            'articles' => $articles,
        ];
    }
}
