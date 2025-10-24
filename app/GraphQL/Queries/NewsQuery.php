<?php

namespace App\GraphQL\Queries;

use App\Models\News;

class NewsQuery
{
    public function searchNews($_, array $args)
    {
        $query = News::query();

        // البحث في العنوان والوصف
        if (!empty($args['search'])) {
            $search = $args['search'];
            $query->where(function ($q) use ($search) {
                $q->where('title->ar', 'LIKE', "%{$search}%")
                  ->orWhere('title->en', 'LIKE', "%{$search}%")
                  ->orWhere('styled_description->ar', 'LIKE', "%{$search}%")
                  ->orWhere('styled_description->en', 'LIKE', "%{$search}%");
            });
        }

        // استبعاد كلمات معينة
        if (!empty($args['exclude'])) {
            $exclude = $args['exclude'];
            $query->where(function ($q) use ($exclude) {
                $q->where('title->ar', 'NOT LIKE', "%{$exclude}%")
                  ->where('title->en', 'NOT LIKE', "%{$exclude}%")
                  ->where('styled_description->ar', 'NOT LIKE', "%{$exclude}%")
                  ->where('styled_description->en', 'NOT LIKE', "%{$exclude}%");
            });
        }

        // التحقق من وجود كلمة معينة
        if (!empty($args['include'])) {
            $include = $args['include'];
            $query->where(function ($q) use ($include) {
                $q->where('title->ar', 'LIKE', "%{$include}%")
                  ->orWhere('title->en', 'LIKE', "%{$include}%")
                  ->orWhere('styled_description->ar', 'LIKE', "%{$include}%")
                  ->orWhere('styled_description->en', 'LIKE', "%{$include}%");
            });
        }

        // نرجع النتائج
        return $query->orderByDesc('created_at')->paginate($args['first'] ?? 10, ['*'], 'page', $args['page'] ?? 1);
    }
}
