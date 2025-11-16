<?php

namespace App\GraphQL\Queries;

use App\Models\ReelGroup;
use Carbon\Carbon;

class ReelQuery
{
    public function reels_for_public($_, array $args)
    {
        $first = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;
        $twentyFourHoursAgo = Carbon::now()->subHours(24);
        $paginator = ReelGroup::with(['reels' => function ($query) use ($twentyFourHoursAgo) {
            $query->where('is_active', true)
                ->where('created_at', '>=', $twentyFourHoursAgo)
                ->orderBy('sort_order', 'asc');
        }])
            ->where('is_active', true)
            ->whereHas('reels', function ($query) use ($twentyFourHoursAgo) {
                $query->where('is_active', true)
                    ->where('created_at', '>=', $twentyFourHoursAgo);
            })
            ->orderBy('sort_order', 'asc')
            ->paginate($first, ['*'], 'page', $page);

        return [
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'data' => $paginator->items(),
        ];
    }

    public function reels_for_admin($_, array $args)
    {
        $first = $args['first'] ?? 10;
        $page = $args['page'] ?? 1;

        $paginator = ReelGroup::with(['reels' => function ($query) {
            $query->orderBy('sort_order', 'asc');
        }])
            ->orderBy('sort_order', 'asc')
            ->paginate($first, ['*'], 'page', $page);

        return [
            'paginatorInfo' => [
                'count' => $paginator->count(),
                'currentPage' => $paginator->currentPage(),
                'firstItem' => $paginator->firstItem(),
                'hasMorePages' => $paginator->hasMorePages(),
                'lastItem' => $paginator->lastItem(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'data' => $paginator->items(),
        ];
    }
}
