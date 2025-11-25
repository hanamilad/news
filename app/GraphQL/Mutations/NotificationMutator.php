<?php

namespace App\GraphQL\Mutations;

class NotificationMutator
{
    public function markRead($_, array $args): bool
    {
        /** @var User $user */
        $user = auth('api')->user();
        $ids = $args['ids'] ?? null;

        if ($ids && is_array($ids) && count($ids) > 0) {
            $affected = $user->notifications()
                ->whereIn('id', $ids)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return $affected > 0;
        }

        $affected = $user->unreadNotifications()->update(['read_at' => now()]);

        return $affected > 0;
    }

    public function delete($_, array $args): bool
    {
        /** @var User $user */
        $user = auth('api')->user();
        $id = $args['id'];

        $row = $user->notifications()->whereKey($id)->first();
        if (! $row) {
            return false;
        }

        return (bool) $row->delete();
    }
}
