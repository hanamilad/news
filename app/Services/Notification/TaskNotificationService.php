<?php

namespace App\Services\Notification;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssignedNotification;

class TaskNotificationService
{
    public function notifyTaskAssigned(Task $task, array $userIds, ?User $assigner = null): void
    {
        $chunkSize = 200;
        $ids = array_values(array_unique(array_filter($userIds, fn($id) => (int) $id > 0)));
        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            $users = User::whereIn('id', $chunk)->get();
            foreach ($users as $user) {
                $user->notify(new TaskAssignedNotification($task, $assigner));
            }
        }
    }
}