<?php

namespace App\Services\Notification;

use App\Models\User;
use App\Notifications\UniversalNotification;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Collection;

class NotificationService
{
    protected int $chunkSize = 200;

    /**
     * @param  array|Collection  $userIds
     */
    public function sendNotification(Notification $notification, $userIds): void
    {
        $ids = collect($userIds)->filter(fn ($id) => (int) $id > 0)->unique()->values();
        foreach ($ids->chunk($this->chunkSize) as $chunk) {
            $users = User::whereIn('id', $chunk)->get();
            foreach ($users as $user) {
                $user->notify($notification);
            }
        }
    }

    public function notifyUsersAboutEvent(string $type, array $data, array $userIds, ?array $creator = null): void
    {
        $notification = new UniversalNotification(
            type: $type,
            data: $data,
            creator: $creator
        );

        $this->sendNotification($notification, $userIds);
    }
}
