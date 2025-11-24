<?php

namespace App\Services\Task;

use App\Repositories\Task\TaskRepository;
use App\Models\Task;
use App\Services\Notification\NotificationService;
use App\Traits\LogActivity;
use Illuminate\Support\Facades\DB;

class TaskService
{
    use LogActivity;

    public function __construct(
        protected TaskRepository $repo,
        protected NotificationService $notifier
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
            $this->log($user?->id, 'اضافة', Task::class, $task->id, null, $task->toArray());
            if (!empty($input['assign_to'])) {
                $this->notifier->notifyUsersAboutEvent(
                    type: 'task',
                    data: [
                        'task_id' => $task->id,
                        'title' => $task->title,
                        'description' => $task->description,
                        'is_priority' => $task->is_priority,
                        'start_date' => $task->start_date,
                        'delivery_date' => $task->delivery_date,
                    ],
                    userIds: $input['assign_to'],
                    creator: [
                        'id' => $user->id,
                        'name' => $user->name,
                    ]
                );
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
            $this->log($user?->id, 'تعديل', Task::class, $updated->id, $old, $updated->toArray());
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
            $this->log($user?->id, 'تغيير حالة', Task::class, $updated->id, $old, $updated->toArray());
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
                $this->notifier->notifyUsersAboutEvent(
                    type: 'task_assigned',
                    data: [
                        'task_id' => $updated->id,
                        'title' => $updated->title,
                    ],
                    userIds: $newAssigned,
                    creator: [
                        'id' => $user->id,
                        'name' => $user->name,
                    ]
                );
            }

            $this->log($user?->id, 'تعيين مهمة لموظفين', Task::class, $updated->id, $oldAssigned, $updated->toArray());
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
            $this->log($user?->id, 'إلغاء تعيين مهمة لموظفين', Task::class, $updated->id, $old, $updated->toArray());
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
            $this->log($user?->id, 'حذف', Task::class, $task->id, $old, null);
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
                if (!$id) {
                    continue;
                }
                $task = $this->repo->findById($id);
                $old = $task->toArray();
                $task = $this->repo->update($task, [
                    'note' => $item['note'] ?? $task->note,
                ]);
                $task = $this->repo->setStatus($task, 'done');
                $this->log($user?->id, 'انهاء مهمة', Task::class, $task->id, $old, $task->toArray());
                $updatedTasks[] = $task;
            }
            return $updatedTasks;
        });
    }
}
