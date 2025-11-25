<?php

namespace App\GraphQL\Queries;

use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

class TaskQuery
{
    public function myTasksBuilder($_, array $args): Builder
    {
        $user = auth('api')->user();
        $query = Task::query()
            ->whereHas('users', function ($q) use ($user) {
                $q->whereKey($user->id);
            })
            ->orderBy('delivery_date')
            ->orderBy('start_date');

        return $query;
    }
}
