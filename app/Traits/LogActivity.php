<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogActivity
{
    const MODEL_NAME = [
        'App\Models\Ad' => 'إعلان',
        'App\Models\Article' => 'مقالة',
        'App\Models\Category' => 'فئة',
        'App\Models\Hashtag' => 'هاش تاج',
        'App\Models\News' => 'خبر',
        'App\Models\Podcast' => 'بودكاست',
        'App\Models\ReelGroup' => 'مجموعة حالات',
        'App\Models\Reel' => 'حالة',
        'App\Models\Task' => ' مهمة',
        'App\Models\Video' => 'فيديو',
        'App\Models\Template' => 'قالب',
        'App\Models\User' => 'مستخدم',
        'App\Models\Client' => 'زائر',

    ];

    protected function log($userId, string $action, string $modelType, $modelId, $old = null, $new = null): void
    {
        ActivityLog::create([
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'description' => ucfirst($action).' '.self::MODEL_NAME[$modelType],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'old_values' => $old,
            'new_values' => $new,
        ]);
    }
}
