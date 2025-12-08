<?php

namespace App\Traits;

use App\Models\User;
use App\Notifications\UniversalNotification;
use App\Services\Notification\NotificationService;
use Illuminate\Database\Eloquent\Model;

trait NotifiesAdminsForApproval
{
    public static function bootNotifiesAdminsForApproval(): void
    {
        static::created(function (Model $model) {
            $value = $model->getAttribute('is_admin_approved');
            $u = $model->user()->first();
            if ($value === false) {
                $ids = User::role('super_admin', 'api')->pluck('id');
                if ($ids->isNotEmpty()) {
                    $data = [
                        'model' => class_basename($model),
                        'id' => $model->getKey(),
                        'title' => $model->getAttribute('title') ?? $model->getAttribute('description') ?? null,
                        'description' => 'طلب موافقة من الموظف '.($u->name ?? ''),
                    ];

                    $creator = null;
                    if (method_exists($model, 'user') && $model->getAttribute('user_id')) {
                        if ($u) {
                            $creator = [
                                'id' => $u->id,
                                'name' => $u->name,
                            ];
                        }
                    }

                    $notification = new UniversalNotification(
                        type: 'approval_required',
                        data: $data,
                        creator: $creator
                    );

                    (new NotificationService)->sendNotification($notification, $ids);
                }
            }
        });
    }
}
