<?php

namespace App\Notifications;

use App\Models\Task;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class TaskAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Task $task, protected ?User $assigner)
    {
        $this->onQueue('notifications');
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'task_id' => $this->task->id,
            'title' => $this->task->title,
            'description' => $this->task->description,
            'note' => $this->task->note,
            'is_priority' => (bool) $this->task->is_priority,
            'start_date' => optional($this->task->start_date)->toDateTimeString(),
            'delivery_date' => optional($this->task->delivery_date)->toDateTimeString(),
            'status' => $this->task->status,
            'assigned_by' => $this->assigner ? [
                'id' => $this->assigner->id,
                'name' => $this->assigner->name,
            ] : null,
            'assigned_at' => now()->toDateTimeString(),
        ];
    }
}