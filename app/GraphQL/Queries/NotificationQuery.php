<?php

namespace App\GraphQL\Queries;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;

class NotificationQuery
{
    public function myNotificationsBuilder($_, array $args): Builder
    {
        $user = auth('api')->user();

        return DatabaseNotification::query()
            ->where('notifiable_id', $user->id)
            ->where('notifiable_type', get_class($user))
            ->orderByDesc('created_at');
    }
}
