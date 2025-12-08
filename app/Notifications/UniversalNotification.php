<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class UniversalNotification extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public function __construct(
        protected string $type,
        protected array $data,
        protected ?array $creator = null
    ) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        $unreadCount = $notifiable->unreadNotifications()->count() + 1;

        return [
            'type' => $this->type,
            'data' => $this->data,
            'creator' => $this->creator,
            'created_at' => now()->toDateTimeString(),
            'unread_count' => $unreadCount,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $unreadCount = $notifiable->unreadNotifications()->count() + 1;

        return new BroadcastMessage([
            'type' => $this->type,
            'data' => $this->data,
            'creator' => $this->creator,
            'created_at' => now()->toDateTimeString(),
            'unread_count' => $unreadCount,
        ]);
    }
}
