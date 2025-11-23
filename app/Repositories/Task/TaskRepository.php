<?php

namespace App\Repositories\Task;

use App\Models\Task;

class TaskRepository
{
    public function findById(int $id): Task
    {
        return Task::findOrFail($id);
    }

    public function create(array $data): Task
    {
        $task = Task::create([
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'note' => $data['note'] ?? null,
            'start_date' => $data['start_date'] ?? null,
            'delivery_date' => $data['delivery_date'] ?? null,
            'is_priority' => $data['is_priority'] ?? false,
            'status' => 'pending',
        ]);

        if (!empty($data['assign_to'])) {
            $task->users()->sync($data['assign_to']);
        }

        return $task->fresh(['users']);
    }

    public function update(Task $task, array $data): Task
    {
        $task->update([
            'title' => $data['title'] ?? $task->title,
            'description' => $data['description'] ?? $task->description,
            'note' => $data['note'] ?? $task->note,
            'start_date' => $data['start_date'] ?? $task->start_date,
            'delivery_date' => $data['delivery_date'] ?? $task->delivery_date,
            'is_priority' => $data['is_priority'] ?? $task->is_priority,
        ]);

        if (array_key_exists('assign_to', $data)) {
            $task->users()->sync($data['assign_to'] ?: []);
        }

        return $task->fresh(['users']);
    }

    public function setStatus(Task $task, string $status): Task
    {
        $task->update(['status' => $status]);
        return $task;
    }

    public function assignUsers(Task $task, array $userIds): Task
    {
        $task->users()->syncWithoutDetaching($userIds);
        return $task->fresh(['users']);
    }

    public function unassignUsers(Task $task, array $userIds): Task
    {
        $task->users()->detach($userIds);
        return $task->fresh(['users']);
    }

    public function delete(Task $task): bool
    {
        return (bool) $task->delete();
    }
}