<?php

namespace App\GraphQL\Queries;

use App\Models\News;
use Illuminate\Database\Eloquent\Builder;

class NewsQuery
{
    private function addSearchCondition($query, array $fields, string $value, string $operator = 'LIKE', string $boolean = 'or')
    {
        $query->where(function ($q) use ($fields, $value, $operator, $boolean) {
            foreach ($fields as $index => $field) {
                if ($index === 0) {
                    $q->where($field, $operator, "%{$value}%");
                } else {
                    if ($boolean === 'or') {
                        $q->orWhere($field, $operator, "%{$value}%");
                    } else {
                        $q->where($field, $operator, "%{$value}%");
                    }
                }
            }
        });
    }

    public function searchNews($_, array $args)
    {
        $query = News::forPublic();

        $fields = [
            'title->ar',
            'title->en',
            'styled_description->ar',
            'styled_description->en',
        ];

        if (! empty($args['search'])) {
            $this->addSearchCondition($query, $fields, $args['search'], 'LIKE', 'or');
        }

        if (! empty($args['exclude'])) {
            $this->addSearchCondition($query, $fields, $args['exclude'], 'NOT LIKE', 'and');
        }

        if (! empty($args['include'])) {
            $this->addSearchCondition($query, $fields, $args['include'], 'LIKE', 'or');
        }
        if (! empty($args['time_range'])) {
            $range = $args['time_range'];
            $now = now();

            switch ($range) {
                case 'today':
                    $query->whereDate('created_at', $now->toDateString());
                    break;

                case 'week':
                    $query->whereBetween('created_at', [
                        $now->startOfWeek(),
                        $now->endOfWeek(),
                    ]);
                    break;

                case 'month':
                    $query->whereBetween('created_at', [
                        $now->startOfMonth(),
                        $now->endOfMonth(),
                    ]);
                    break;

                case 'year':
                    $query->whereBetween('created_at', [
                        $now->startOfYear(),
                        $now->endOfYear(),
                    ]);
                    break;
            }
        }
        $perPage = isset($args['first']) && is_numeric($args['first']) ? (int) $args['first'] : 10;
        $page = isset($args['page']) && is_numeric($args['page']) ? (int) $args['page'] : 1;

        return $query->orderByDesc('created_at')
            ->paginate($perPage, ['*'], 'page', $page);
    }

    public function newsForPublicBuilder($_, array $args): Builder
    {
        return News::forPublic($args['category_id'] ?? null);
    }
    public function newsForAdminBuilder($_, array $args): Builder
    {
        return News::filterByCategory($args['category_id'] ?? null);
    }
}
