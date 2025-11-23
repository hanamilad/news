<?php

namespace App\Services\Task;

use App\Repositories\Task\TaskRepository;
use App\Models\Task;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class TaskService
{
    use LogActivity;

    public function __construct(
        protected TaskRepository $repo,
        protected \App\Services\Notification\TaskNotificationService $notifier
    ) {}

    public function findById(int $id): Task
    {
        return $this->repo->findById($id);
    }

    public function create(array $input): Task
    {
        return DB::transaction(function () use ($input) {
            $user = auth('api')->user();
            $task = $this->repo->create($input);
            $this->log($user?->id, 'create', Task::class, $task->id, null, $task->toArray());
            if (!empty($input['assign_to'])) {
                $this->notifier->notifyTaskAssigned($task, $input['assign_to'], $user);
            }
            return $task;
        });
    }

    public function update(int $id, array $input): Task
    {
        return DB::transaction(function () use ($id, $input) {
            $user = auth('api')->user();
            $task = $this->repo->findById($id);
            $old = $task->toArray();
            $updated = $this->repo->update($task, $input);
            $this->log($user?->id, 'update', Task::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function setStatus(int $id, string $status): Task
    {
        return DB::transaction(function () use ($id, $status) {
            $user = auth('api')->user();
            $task = $this->repo->findById($id);
            $old = $task->toArray();
            $updated = $this->repo->setStatus($task, $status);
            $this->log($user?->id, 'update_status', Task::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function assignUsers(int $id, array $userIds): Task
    {
        return DB::transaction(function () use ($id, $userIds) {
            $user = auth('api')->user();
            $task = $this->repo->findById($id);
            $oldAssigned = $task->users()->pluck('users.id')->all();
            $updated = $this->repo->assignUsers($task, $userIds);
            $newAssigned = array_values(array_diff($updated->users->pluck('id')->all(), $oldAssigned));
            if (!empty($newAssigned)) {
                $this->notifier->notifyTaskAssigned($updated, $newAssigned, $user);
            }
            $this->log($user?->id, 'assign_users', Task::class, $updated->id, $oldAssigned, $updated->toArray());
            return $updated;
        });
    }

    public function unassignUsers(int $id, array $userIds): Task
    {
        return DB::transaction(function () use ($id, $userIds) {
            $user = auth('api')->user();
            $task = $this->repo->findById($id);
            $old = $task->toArray();
            $updated = $this->repo->unassignUsers($task, $userIds);
            $this->log($user?->id, 'unassign_users', Task::class, $updated->id, $old, $updated->toArray());
            return $updated;
        });
    }

    public function delete(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $user = auth('api')->user();
            $task = $this->repo->findById($id);
            $old = $task->toArray();
            $deleted = $this->repo->delete($task);
            $this->log($user?->id, 'delete', Task::class, $task->id, $old, null);
            return $deleted;
        });
    }

    public function finishTasks(array $items): array
    {
        return DB::transaction(function () use ($items) {
            $user = auth('api')->user();
            $updatedTasks = [];
            foreach ($items as $item) {
                $id = (int) ($item['id'] ?? 0);
                if (!$id) { continue; }
                $task = $this->repo->findById($id);
                $old = $task->toArray();
                $task = $this->repo->update($task, [
                    'note' => $item['note'] ?? $task->note,
                ]);
                $task = $this->repo->setStatus($task, 'done');
                $this->log($user?->id, 'finish', Task::class, $task->id, $old, $task->toArray());
                $updatedTasks[] = $task;
            }
            return $updatedTasks;
        });
    }
}